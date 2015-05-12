<?php
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * PostgreSQL authentication backend
 *
 * This class inherits much functionality from the MySQL class
 * and just reimplements the Postgres specific parts.
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 * @author     Chris Smith <chris@jalakai.co.uk>
 * @author     Matthias Grimm <matthias.grimmm@sourceforge.net>
 * @author     Jan Schumann <js@schumann-it.com>
 */
class auth_plugin_authpgsql extends auth_plugin_authpdo {

    /**
     * Constructor
     *
     * checks if the pgsql interface is available, otherwise it will
     * set the variable $success of the basis class to false
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    public function __construct() {
        $this->dbType = 'pgsql';
        $this->dbName = 'PgSQL';
        parent::__construct();
    }

    /**
     * Counts users which meet certain $filter criteria.
     *
     * @author  Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     *
     * @param  array  $filter  filter criteria in item/pattern pairs
     * @return int count of found users.
     */
    public function getUserCount($filter = array()) {
        $rc = 0;

        if($this->_openDB()) {
            $sql = $this->_createSQLFilter($this->getConf('getUsers'), $filter);

            // no equivalent of SQL_CALC_FOUND_ROWS in pgsql?
            if(($result = $this->_queryDB($sql))) {
                $rc = count($result);
            }
            $this->_closeDB();
        }
        return $rc;
    }

    /**
     * Executes an update or insert query. This differs from the
     * MySQL one because it does NOT return the last insertID
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     *
     * @param string $query
     * @return bool
     */
    protected function _modifyDB($query) {
        if($this->dbcon) {
            $result = $this->dbcon->query($query);
            if($result) {
                $result->closeCursor();
                return true;
            }
            $this->_debug('PgSQL err: '.pg_last_error($this->dbcon), -1, __LINE__, __FILE__);
        }
        return false;
    }

    /**
     * Start a transaction
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     *
     * @param string $mode  could be 'READ' or 'WRITE'
     * @return bool
     */
    protected function _lockTables($mode) {
        if($this->dbcon) {
            $this->_modifyDB('BEGIN');
            return true;
        }
        return false;
    }

    /**
     * Commit a transaction
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     *
     * @return bool
     */
    protected function _unlockTables() {
        if($this->dbcon) {
            $this->_modifyDB('COMMIT');
            return true;
        }
        return false;
    }

}