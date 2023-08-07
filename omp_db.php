<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | OMP Version 2                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 2009-2011 Lorenzo Ciani                                |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation; either version 2 of the License, or    |
// | (at your option) any later version.                                  |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
// | See the GNU General Public License for more details.                 |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the                        |
// | Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston,  |
// | MA 02111-1307 USA                                                    |
// +----------------------------------------------------------------------+
// | Author: Lorenzo Ciani <lciani@yahoo.it>                              |
// +----------------------------------------------------------------------+
//
// $Id: omp_db.php,v 0.7 $
//
// Database functions and definitions
//

// Database backend suppported by PEAR_MDB for DSN
define('OMP_DB_TYPE', 'pgsql');
// Database server IP address for DSN
define('OMP_DB_HOST', '');
// Database name for DSN
define('OMP_DB_NAME', 'orders');
// Permanent database connection?
define('OMP_DB_PCON', false);
// Database number format: decimal digits
define('OMP_DB_DECIMALS', 4);
// Database number format: decimal point
define('OMP_DB_DEC_POINT', '.');
// Database number format: thousands separator
define('OMP_DB_THOUSANDS_SEP', '');
// Maximum length of table column in database
define('OMP_DB_MAXSIZE', 1000);
// Date format in database, as in strftime()
// Set this to 0 to store dates in timestamp
define('OMP_DB_DATE', '%Y-%m-%d');
// Database account for clients
define('OMP_DB_CLINAME', 'ofb_client');
define('OMP_DB_CLIPASS', 'ofbclient');
// Database account for suppliers
define('OMP_DB_SUPNAME', 'ofb_supplier');
define('OMP_DB_SUPPASS', 'ofbsupplier');
// Database account for operators
define('OMP_DB_OPNAME', 'ofb_operator');
define('OMP_DB_OPPASS', 'ofboperator');
// Database account for admins - also default account
// define('OMP_DB_ADMNAME', 'ofb_admin');
// define('OMP_DB_ADMPASS', 'ofbadmin');
define('OMP_DB_ADMNAME', 'lore');
define('OMP_DB_ADMPASS', 'peka');
define('OMP_DB_TZ', 'Europe/Rome');

/**
* Connect to database
*
* @param string $my_user user name
* @param string $my_pass password
* @return object $_OMP_db PEAR MDB connection object
* @see OMP_genDbErr()
*/
function OMP_backendConnect($my_user, $my_pass)
{
/*    $db = DB::connect(OMP_DB_TYPE.'://'.$my_user.':'.$my_pass.'@'.
        OMP_DB_HOST.'/'.OMP_DB_NAME, array('persistent' => OMP_DB_PCON));
    if (DB::isError($db)) {
        OMP_genDbErr($db->getMessage(), 'OMP_backendConnect()');
    }
*/
// PEAR MDB2
    $db = MDB2::connect(array('phptype' => OMP_DB_TYPE,
        'hostspec' => OMP_DB_HOST, 'database' => OMP_DB_NAME,
        'username' => $my_user, 'password' => $my_pass),
        array('persistent' => OMP_DB_PCON,
        'portability' => MDB2_PORTABILITY_ALL));
    if (PEAR::isError($db)) {
        OMP_genDbErr($db->getMessage(), 'OMP_backendConnect()');
    }
    $db->setFetchMode(MDB2_FETCHMODE_ASSOC);
    return $db;
}

/**
* Quote strings using DB class
*
*/
function OMP_db_quote($value, $datatype = 'text')
{
    global $_OMP_db;
    $value = isset($_OMP_db) ? $_OMP_db->quote($value, $datatype) : $value;
    return $value;
}

//!isset($_OMP_db) or $_OMP_db->disconnect(); SI
//$_OMP_db->setFetchMode(MDB2_FETCHMODE_ORDERED); SI FUNZIONE FETCH_ARRAY
//$_OMP_db->setFetchMode(MDB2_FETCHMODE_ASSOC); SI FUNZIONE FETCH_ASSOC
//$_OMP_db->query SI pg_query
//$_OMP_db->queryRow FORSE pg_fetch_row
//$_OMP_db->queryOne FORSE pg_query
//$_OMP_db->queryAll('SELECT * FROM language'); SI pg_fetch_all
//$_OMP_db_result = $_OMP_db->query($_OMP_get['sql']); SI
//$_OMP_db_result->numRows() SI pg_num_rows
//$_OMP_db_result->fetchRow() SI pg_fetch_row Get a row as an enumerated array
//$_OMP_db_result->free(); SI
//$_OMP_db_result = $_OMP_db->query($_OMP_get['sql']); SI
/*
$prepared = $_OMP_db->prepare($_OMP_get['sql_sub']); SI pg_prepare
        $_OMP_db_result_sf = $prepared->execute($array_sub); SI pg_execute
        if ($_OMP_db_result_sf->numRows() > 0) { SI pg_num_rows
$_OMP_rec_sf = $_OMP_db_result_sf->fetchRow() SI pg_fetch_row
*/
?>
