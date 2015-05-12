<?php
// must be run within Dokuwiki
if (!defined('DOKU_INC'))
    die();

/**
 * MySQL authentication backend
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 * @author     Chris Smith <chris@jalakai.co.uk>
 * @author     Matthias Grimm <matthias.grimmm@sourceforge.net>
 * @author     Jan Schumann <js@schumann-it.com>
 */
class auth_plugin_authmysql extends auth_plugin_authpdo {

    /**
     * Constructor
     *
     * checks if the mysql interface is available, otherwise it will
     * set the variable $success of the basis class to false
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    public function __construct() {
        $this->dbType = 'mysql';
        $this->dbName = 'MySQL';
        parent::__construct();
    }

    /**
     * Counts users which meet certain $filter criteria.
     *
     * @author  Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     *
     * @param  array $filter  filter criteria in item/pattern pairs
     * @return int count of found users
     */
    public function getUserCount($filter = array()) {
        $rc = 0;

        if ($this->_openDB()) {
            $sql = $this->_createSQLFilter($this->getConf('getUsers'), $filter);

            if ($this->dbver >= 4) {
                $sql = substr($sql, 6); /* remove 'SELECT' or 'select' */
                $sql = "SELECT SQL_CALC_FOUND_ROWS" . $sql . " LIMIT 1";
                $this->_queryDB($sql);
                $result = $this->_queryDB("SELECT FOUND_ROWS()");
                $rc = $result[0]['FOUND_ROWS()'];
            } else if (($result = $this->_queryDB($sql)))
                $rc = count($result);

            $this->_closeDB();
        }
        return $rc;
    }

    /**
     * Sends a SQL query to the database
     *
     * This function is only able to handle queries that returns
     * either nothing or an id value such as INPUT, DELETE, UPDATE, etc.
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     *
     * @param string $query  SQL string that contains the query
     * @return int|bool insert id or 0, false on error
     */
    protected function _modifyDB($query) {
        if ($this->getConf('debug') >= 2) {
            msg('MySQL query: ' . hsc($query), 0, __LINE__, __FILE__);
        }

        if ($this->dbcon) {
            $result = $this->dbcon->query($query);
            if ($result) {
                $rc = $this->dbcon->lastInsertId(); //give back ID on insert
                if ($rc !== false)
                    return $rc;
            }
            $this->_debug('MySQL err: ' . $this->dbcon->errorInfo(), -1, __LINE__, __FILE__);
        }
        return false;
    }

    /**
     * Locked a list of tables for exclusive access so that modifications
     * to the database can't be disturbed by other threads. The list
     * could be set with $conf['plugin']['authmysql']['TablesToLock'] = array()
     *
     * If aliases for tables are used in SQL statements, also this aliases
     * must be locked. For eg. you use a table 'user' and the alias 'u' in
     * some sql queries, the array must looks like this (order is important):
     *   array("user", "user AS u");
     *
     * MySQL V3 is not able to handle transactions with COMMIT/ROLLBACK
     * so that this functionality is simulated by this function. Nevertheless
     * it is not as powerful as transactions, it is a good compromise in safty.
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     *
     * @param string $mode  could be 'READ' or 'WRITE'
     * @return bool
     */
    protected function _lockTables($mode) {
        if ($this->dbcon) {
            $ttl = $this->getConf('TablesToLock');
            if (is_array($ttl) && !empty($ttl)) {
                if ($mode == "READ" || $mode == "WRITE") {
                    $sql = "LOCK TABLES ";
                    $cnt = 0;
                    foreach ($ttl as $table) {
                        if ($cnt++ != 0)
                            $sql .= ", ";
                        $sql .= "$table $mode";
                    }
                    $this->_modifyDB($sql);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Unlock locked tables. All existing locks of this thread will be
     * abrogated.
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     *
     * @return bool
     */
    protected function _unlockTables() {
        if ($this->dbcon) {
            $this->_modifyDB("UNLOCK TABLES");
            return true;
        }
        return false;
    }

}
