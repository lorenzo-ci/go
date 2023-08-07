<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | OMP Version 1                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2004 Lorenzo Ciani                                |
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
// $Id: currencies.php,v 0.8 $
//
// Manage currencies records
//

require_once 'base.php';
require_once 'schemas/db-schema.php'; // Database schema
require_once 'schemas/currencies-schema.php'; // Enduses schema
// Name of current master-table
$_OMP_tbl = 'currencies';
// Table fields, length and keys
$_OMP_tbl_fld = $_OMP_currencies_fld;
$_OMP_fld_len = $_OMP_currencies_len;
$_OMP_tbl_key = $_OMP_currencies_key;
/**
* SQL code
*/
$_OMP_sql['select'] = 'SELECT '.
    $_OMP_tbl_fld['pkey'].' AS pkey, '.
    $_OMP_tbl_fld['name'].' AS name, '.
    $_OMP_tbl_fld['rate'].' AS rate, '.
    $_OMP_tbl_fld['active'].' AS active FROM '.
    $_OMP_tables[$_OMP_tbl];
$_OMP_sql['row'] = $_OMP_sql['select'].' WHERE '.
    $_OMP_tbl_fld['pkey'].' = ?';
/**
* End SQL code
*/

/**
* Function definitions
*/
/**
* Process the array $GLOBALS['_OMP_rec']
*
*/
function OMP_makeVars()
{
    global $_OMP_html, $_OMP_table_data, $_OMP_table_header,
        $_OMP_LC, $_OMP_get, $_OMP_rec;
    if ($_OMP_get['action'] == 'new') { return; }
    $_OMP_rec = array_map('OMP_htmlentities', $_OMP_rec);
    if ($_OMP_get['action'] == 'del') {
        $_OMP_table_header = array($_OMP_LC[1401], $_OMP_LC[301]);
        $_OMP_table_data = array($_OMP_rec['pkey'], $_OMP_rec['name']);
        return;
    }
    if ($_OMP_get['action'] == 'read') {
        $_OMP_rec['rate'] = number_format($_OMP_rec['rate'],
            OMP_DB_DECIMALS, $_SESSION['dp'], $_SESSION['ts']);
        $_OMP_html['rec0'] = $_OMP_rec['pkey'];
        $_OMP_html['rec1'] = $_OMP_rec['name'];
        $_OMP_html['rec2'] = $_OMP_rec['rate'];
        $_OMP_rec['active'] = OMP_yesno($_OMP_rec['active']);
        if ($_OMP_get['list']) {
            $_OMP_html['label0'] = $_OMP_html['label1'] =
                $_OMP_html['label2'] = '';
            /* mdc-list */
            /* or mdc-data-table__cell--numeric */
            $_OMP_html['cell1_numeric'] = '';
            $_OMP_html['cell2_numeric'] = '';
            $_OMP_html['cell3_numeric'] = '';
        } else {
            $_OMP_html['label0'] = $_OMP_LC[1401];
            $_OMP_html['label1'] = $_OMP_LC[301];
            $_OMP_html['label2'] = $_OMP_LC[1402];
        }
    }
}
/**
* End functions
*/
$_OMP_table_alias = ''; // See makeSql
switch ($_OMP_get['action']) {
/**
* Read record
*/
case 'read': {
    $_OMP_tpl = OMP_TPL_READ.'63, 64, 1400';
    $_OMP_lcl = OMP_LCL_READ.'119, 301, 605,
        1400, 1401, 1402';
    $_OMP_sql['sort_default'] =
        ' ORDER BY '.$_OMP_tbl_fld['pkey'].' ?';
    $_OMP_sql['sort_record'][0] =
        $_OMP_sql['sort_list'][0] =
        $_OMP_sql['sort_default'];
    $_OMP_sql['sort_record'][1] =
        $_OMP_sql['sort_list'][1] = ' ORDER BY '.
        $_OMP_tbl_fld['name'].' ?';
    $_OMP_sql['sort_record'][2] =
        $_OMP_sql['sort_list'][2] = ' ORDER BY '.
        $_OMP_tbl_fld['rate'].' ?';
    $_OMP_sort_idx = array(1401, 301, 1402);
    if ($_OMP_get['list']) {
        $_OMP_list_tpl = 60;
        $_OMP_list_header_tpl = 63;
        $_OMP_list_rec_tpl = 64;
        /* CSS class for record list */
        $_OMP_list_wrapper = 'wrapper3';
        /* mdc table */
        // or mdc-data-table__header-cell--numeric"
        $_OMP_html['header1_numeric'] = '';
        $_OMP_html['header2_numeric'] = '';
        $_OMP_html['header3_numeric'] = '';
    } else {
        $_OMP_rec_tpl = 1400;
        $_OMP_headline = 605;
    }
    $_OMP_title = 1400;
    $_OMP_sort['default'] = '0'; // Default sort
    $_OMP_sort['type'] = 0; // Default sort order
    /* Load record-read script */
    require 'lib/read.php';
    break;
}

/**
* Edit record
*/
case 'edit': {
    $_OMP_tpl = OMP_TPL_EDIT.'1402';
    $_OMP_lcl = OMP_LCL_EDIT.'301, 1400,
        1401, 1402';
    // Check if insert-button was pushed
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['update'] = 'UPDATE '.$_OMP_tables[$_OMP_tbl].' SET '.
            $_OMP_tbl_fld['pkey'].' = ?, '.
            $_OMP_tbl_fld['name'].' = ?, '.
            $_OMP_tbl_fld['rate'].' = ?, '.
            $_OMP_tbl_fld['active'].' = ? WHERE '.
            $_OMP_tbl_fld['pkey'].' = ?';
        $_OMP_datatypes = array(
            'text', 'text', 'decimal', 'text', 'text'
        );
    } else {
        /* switches and scripts to set their value */
        $_OMP_active_switch = true;
        $_OMP_active_switch_locale = 119;
        $_OMP_datatypes = array('text');
        $_OMP_input_tpl = 1402;
        $_OMP_edit_tpl = 51;
        $_OMP_page_title_lcl = 1400;
    }
    require 'lib/edit.php';
    break;
}
/**
* End edit record
*/

/**
* Filter form
*/
case 'filter': {
    $_OMP_tpl = OMP_TPL_FILTER.'1401';
    $_OMP_lcl = OMP_LCL_FILTER.'300, 301, 1400, 1401, 1402';
    /* set variables for drop-down lists */
    $_OMP_html['active_combo'] = '';
    $_OMP_page_title_lcl = 1400;
    $_OMP_include_tpl = 1401;
    require 'lib/filter.php';
    break;
}
/**
* End filter form
*/

/**
* New record
*/
case 'new': {
    $_OMP_tpl = OMP_TPL_NEW.'1402';
    $_OMP_lcl = OMP_LCL_NEW.'119, 301, 1400, 1401, 1402';
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['insert'] = 'INSERT INTO '.
            $_OMP_tables[$_OMP_tbl].' ('.
            $_OMP_tbl_fld['pkey'].', '.
            $_OMP_tbl_fld['name'].', '.
            $_OMP_tbl_fld['rate'].') VALUES (?, ?, ?)';
        $_OMP_datatypes = array('text', 'text', 'decimal');
    } else {
        /* switches and scripts to set their value */
        $_OMP_active_switch = true;
        $_OMP_active_switch_locale = 119;
        $_OMP_input_tpl = 1402;
        $_OMP_include_tpl = 50;
        $_OMP_page_title_lcl = 1400;
    }
    require 'lib/new.php';
    break;
}
/**
* End new record
*/

/**
* Delete record
*/
case 'del': {
    $_OMP_tpl = OMP_TPL_DEL.'52';
    $_OMP_lcl = OMP_LCL_DEL.'301, 1400, 1401';
    $_OMP_datatypes = array('text');
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['delete'] = 'DELETE FROM '.
            $_OMP_tables[$_OMP_tbl].' WHERE '.
            $_OMP_tbl_fld['pkey'].' = ?';
    } else {
        $_OMP_sql['delete_pre'] = 'SELECT '.
            $_OMP_tbl_fld['pkey'].' AS pkey, '.
            $_OMP_tbl_fld['name'].' AS name FROM '.
            $_OMP_tables[$_OMP_tbl].' WHERE '.
            $_OMP_tbl_fld['pkey'].' = ?';
        $_OMP_del_tpl = 52;
        $_OMP_page_title_lcl = 1400;
    }
    require 'lib/del.php';
    break;
}
/**
* End delete record
*/
}
?>
