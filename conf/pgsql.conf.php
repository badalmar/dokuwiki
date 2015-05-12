<?php
/**
 * Example PgSQL Auth Plugin settings
 * See https://www.dokuwiki.org/plugin:authpgsql for details and explanation
 */
/**
 * Options
 */
$conf['authtype'] = "authpgsql";
$conf['plugin']['authpgsql']['debug'] = 2;
$conf['plugin']['authpgsql']['server'] = 'localhost';
$conf['plugin']['authpgsql']['user'] = 'postgres';
$conf['plugin']['authpgsql']['password'] = 'kotek';
$conf['plugin']['authpgsql']['database'] = 'doku';
$conf['plugin']['authpgsql']['forwardClearPass'] = 0;

$conf['plugin']['authpgsql']['dbType'] = 'pgsql';
/**
 * SQL User Authentication
 */
$conf['plugin']['authpgsql']['checkPass'] = "SELECT pass
                                             FROM usergroup AS ug
                                             JOIN users AS u ON u.uid=ug.uid
                                             JOIN groups AS g ON g.gid=ug.gid
                                             WHERE u.login='%{user}'
                                               AND g.name='%{dgroup}'";
$conf['plugin']['authpgsql']['getUserInfo'] = "SELECT pass, fullname AS name, email AS mail
                                               FROM users
                                               WHERE login='%{user}'";
$conf['plugin']['authpgsql']['getGroups'] = "SELECT g.name as group
                                             FROM groups g, users u, usergroup ug
                                             WHERE u.uid = ug.uid
                                               AND g.gid = ug.gid
                                               AND u.login='%{user}'";
$conf['plugin']['authpgsql']['getUsers'] = "SELECT DISTINCT u.login AS user
                                            FROM users AS u 
                                            LEFT JOIN usergroup AS ug ON u.uid=ug.uid
                                            LEFT JOIN groups AS g ON ug.gid=g.gid";
$conf['plugin']['authpgsql']['FilterLogin'] = "u.login LIKE '%{user}'";
$conf['plugin']['authpgsql']['FilterName']  = "u.fullname LIKE '%{name}'";
$conf['plugin']['authpgsql']['FilterEmail'] = "u.email LIKE '%{email}'";
$conf['plugin']['authpgsql']['FilterGroup'] = "g.name LIKE '%{group}'";
$conf['plugin']['authpgsql']['SortOrder']   = "ORDER BY u.login";

/**
 * SQL Support for Add User
 */
$conf['plugin']['authpgsql']['addUser']     = "INSERT INTO users
                                                 (login, pass, email, fullname)
                                               VALUES 
                                                 ('%{user}', '%{pass}', '%{email}', '%{name}')";
$conf['plugin']['authpgsql']['addGroup']    = "INSERT INTO groups (name)
                                               VALUES ('%{group}')";
$conf['plugin']['authpgsql']['addUserGroup']= "INSERT INTO usergroup (uid, gid)
                                               VALUES ('%{uid}', '%{gid}')";
$conf['plugin']['authpgsql']['delGroup']    = "DELETE FROM groups
                                               WHERE gid='%{gid}'";
$conf['plugin']['authpgsql']['getUserID']   = "SELECT uid AS id FROM users WHERE login='%{user}'";
$conf['plugin']['authpgsql']['getGroupID']  = "SELECT gid AS id FROM groups WHERE name='%{group}'";

/**
 * SQL Support for Delete User
 */
$conf['plugin']['authpgsql']['delUser']     = "DELETE FROM users
                                               WHERE uid='%{uid}'";
$conf['plugin']['authpgsql']['delUserRefs'] = "DELETE FROM usergroup
                                               WHERE uid='%{uid}'";

/**
 * SQL Support for Modify User
 */
$conf['plugin']['authpgsql']['updateUser']  = "UPDATE users SET";
$conf['plugin']['authpgsql']['UpdateLogin'] = "login='%{user}'";
$conf['plugin']['authpgsql']['UpdatePass']  = "pass='%{pass}'";
$conf['plugin']['authpgsql']['UpdateEmail'] = "email='%{email}'";
$conf['plugin']['authpgsql']['UpdateName']  = "fullname='%{name}'";
$conf['plugin']['authpgsql']['UpdateTarget']= "WHERE uid=%{uid}";

$conf['plugin']['authpgsql']['delUserGroup'] = "DELETE FROM usergroup
                                                WHERE uid='%{uid}'
                                                  AND gid='%{gid}'";