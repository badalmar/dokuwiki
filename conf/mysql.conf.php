<?php
/**
 * Configuration for MySQL Auth Plugin
 * See https://www.dokuwiki.org/plugin:authmysql for details and explanation
 */

//database access
$conf['plugin']['authmysql']['server']   = 'localhost';
$conf['plugin']['authmysql']['user']     = 'root';
$conf['plugin']['authmysql']['password'] = '';
$conf['plugin']['authmysql']['database'] = 'dokuwiki';

//Debug level: 0 = disable, 1 = only mysql errors, 2 = print every sent SQL query 
$conf['plugin']['authmysql']['debug']= 2;

//multiple table operation needs locks
$conf['plugin']['authmysql']['TablesToLock']= array("users", "users AS u", "groups", "groups AS g", "usergroup", "usergroup AS ug");

//Where password encryption is done, 0 by DokuWiki (recommended) or 1 by database
$conf['plugin']['authmysql']['forwardClearPass'] = 0;

/**
 * Basic SQL statements for user authentication (required) 
 */

//This statement is used to grant or deny access to the wiki. 
$conf['plugin']['authmysql']['checkPass']   = "SELECT pass
                                               FROM usergroup AS ug
                                               JOIN users AS u ON u.uid=ug.uid
                                               JOIN groups AS g ON g.gid=ug.gid
                                               WHERE login='%{user}'
                                               AND name='%{dgroup}'";
//Return a table with exact one row containing information about one user
$conf['plugin']['authmysql']['getUserInfo'] = "SELECT pass, fullname AS name, email AS mail
                                               FROM users
                                               WHERE login='%{user}'";
//get all groups a user is member of                                            
$conf['plugin']['authmysql']['getGroups']   = "SELECT name as `group`
                                               FROM groups g, users u, usergroup ug
                                               WHERE u.uid = ug.uid
                                                 AND g.gid = ug.gid
                                                 AND u.login='%{user}'";
/**
 * Additional minimum SQL statements to use the user manager
 */

//return a table containing all user login names that meet certain filter criteria
$conf['plugin']['authmysql']['getUsers']    = "SELECT DISTINCT login AS user
                                               FROM users AS u 
                                               LEFT JOIN usergroup AS ug ON u.uid=ug.uid
                                               LEFT JOIN groups AS g ON ug.gid=g.gid";
$conf['plugin']['authmysql']['FilterLogin'] = "login LIKE '%{user}'";
$conf['plugin']['authmysql']['FilterName']  = "fullname LIKE '%{name}'";
$conf['plugin']['authmysql']['FilterEmail'] = "email LIKE '%{email}'";
$conf['plugin']['authmysql']['FilterGroup'] = "name LIKE '%{group}'";
$conf['plugin']['authmysql']['SortOrder']   = "ORDER BY login";                                         

/**
 * Additional SQL statements to add new users with the user manager 
 */

//should add a user to the database
$conf['plugin']['authmysql']['addUser']     = "INSERT INTO users
                                               (login, pass, email, fullname)
                                               VALUES ('%{user}', '%{pass}', '%{email}', '%{name}')";
//should add a group to the database
$conf['plugin']['authmysql']['addGroup']    = "INSERT INTO groups (name)
                                               VALUES ('%{group}')";
//should connect a user to a group (a user become member of that group).
$conf['plugin']['authmysql']['addUserGroup']= "INSERT INTO usergroup (uid, gid)
                                               VALUES ('%{uid}', '%{gid}')";
//This statement should remove a group fom the database
$conf['plugin']['authmysql']['delGroup']    = "DELETE FROM groups
                                               WHERE gid='%{gid}'";
//This statement should return the database index of a given user name.
$conf['plugin']['authmysql']['getUserID']   = "SELECT uid AS id FROM users WHERE login='%{user}'";

/**
 * Additional SQL statements to delete users with the user manager
 */

//return the database index of a given group name
$conf['plugin']['authmysql']['getGroupID']  = "SELECT gid AS id FROM groups WHERE name='%{group}'";
//should remove a user fom the database.
$conf['plugin']['authmysql']['delUser']     = "DELETE FROM users
                                               WHERE uid='%{uid}'";
//This statement should remove all connections from a user to any group
$conf['plugin']['authmysql']['delUserRefs'] = "DELETE FROM usergroup
                                               WHERE uid='%{uid}'";
/**
 * Additional SQL statements to modify users with the user manager 
 */

//should modify a user entry in the database
$conf['plugin']['authmysql']['updateUser']  = "UPDATE users SET";
$conf['plugin']['authmysql']['UpdateLogin'] = "login='%{user}'";
$conf['plugin']['authmysql']['UpdatePass']  = "pass='%{pass}'";
$conf['plugin']['authmysql']['UpdateEmail'] = "email='%{email}'";
$conf['plugin']['authmysql']['UpdateName']  = "fullname='%{name}'";
$conf['plugin']['authmysql']['UpdateTarget']= "WHERE uid=%{uid}";

//should remove a single connection from a user to a group
$conf['plugin']['authmysql']['delUserGroup']= "DELETE FROM usergroup
                                               WHERE uid='%{uid}'
                                                 AND gid='%{gid}'";                                         