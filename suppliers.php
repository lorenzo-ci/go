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
// $Id: suppliers.php,v 0.8 $
//
// Manage suppliers records
//

require_once 'base.php';
// Name of current master-table
$_OMP_tbl = 'suppliers';
require_once 'schemas/db-schema.php'; // Database schema
require_once 'schemas/um-schema.php';
require_once 'schemas/suppliers-schema.php'; // Suppliers schema
require_once 'schemas/currencies-schema.php'; // Currencies schema
// SQL for drop-down lists
$_OMP_get['popup'] or require_once 'lib/dd-sql.php';
// Table fields, length and keys
$_OMP_tbl_fld = $_OMP_suppliers_fld;
$_OMP_fld_len = $_OMP_suppliers_len;
$_OMP_tbl_key = $_OMP_suppliers_key;
/**
* SQL code
*/
$_OMP_sql['select'] = 'SELECT '.$_OMP_tbl_fld['pkey'].' AS pkey, '.
    $_OMP_tbl_fld['name'].' AS name, '.
    $_OMP_tbl_fld['addr'].' AS addr, '.
    $_OMP_tbl_fld['zip'].' AS zip, '.
    $_OMP_tbl_fld['city'].' AS city, '.
    $_OMP_tbl_fld['state'].' AS state, '.
    $_OMP_tbl_fld['region'].' AS region, '.
    $_OMP_tbl_fld['country'].' AS country, '.
    $_OMP_tbl_fld['vat'].' AS vat, '.
    $_OMP_tbl_fld['tel'].' AS tel, '.
    $_OMP_tbl_fld['fax'].' AS fax, '.
    $_OMP_tbl_fld['note'].' AS note, '.
    $_OMP_tbl_fld['um'].' AS um, '.
    $_OMP_tbl_fld['curr'].' AS curr, '.
    $_OMP_tbl_fld['active'].' FROM '.
    $_OMP_tables[$_OMP_tbl];
$_OMP_sql['row'] = $_OMP_sql['select'].' WHERE '.$_OMP_tbl_fld['pkey'].
    ' = ?';
/**
* End SQL code
*/

/**
* Function definitions
*/
/**
* Process the array $GLOBALS['_OMP_rec']
*
* @access public
*/
function OMP_makeVars()
{
    global $_OMP_conf, $_OMP_html, $_OMP_table_data, $_OMP_table_header,
        $_OMP_LC, $_OMP_get, $_OMP_rec;
    if ($_OMP_get['action'] == 'new') {
        $_OMP_rec['country'] = $_SESSION['country'];
        $_OMP_rec['um_kg'] = 'selected="selected"';
        $_OMP_rec['um_lbs'] = '';
        $_OMP_rec['active_true'] = 'selected="selected"';
        $_OMP_rec['active_false'] = '';
        $_OMP_rec['curr'] = $_OMP_conf['currency'];
        return;
    }
    $_OMP_rec = array_map('OMP_htmlentities', $_OMP_rec);
    if ($_OMP_get['action'] == 'del') {
        $_OMP_table_header = array($_OMP_LC[504], $_OMP_LC[101]);
        $_OMP_table_data = array($_OMP_rec['pkey'], $_OMP_rec['name']);
        return;
    }
    if ($_OMP_get['action'] == 'edit') {
            // if ($_OMP_rec['um'] == 'Kg') {
            //     $_OMP_rec['um_kg'] = 'selected="selected"';
            //     $_OMP_rec['um_lbs'] = '';
            // } else {
            //     $_OMP_rec['um_lbs'] = 'selected="selected"';
            //     $_OMP_rec['um_kg'] = '';
            // }
            if ($_OMP_rec['active'] == 't') {
                $_OMP_rec['active_true'] = 'selected="selected"';
                $_OMP_rec['active_false'] = '';
            } else {
                $_OMP_rec['active_false'] = 'selected="selected"';
                $_OMP_rec['active_true'] = '';
            }
    } elseif ($_OMP_get['action'] == 'read') {
        $_OMP_html['full_addr'] =
        $_OMP_rec['full_addr'] = '';
        if (!empty($_OMP_rec['addr'])) {
            $_OMP_rec['full_addr'] = $_OMP_rec['addr'];
        }
        if (!empty($_OMP_rec['zip'])) {
            $_OMP_rec['full_addr'] .= ' '.$_OMP_rec['zip'];
        }
        if (!empty($_OMP_rec['city'])) {
            $_OMP_rec['full_addr'] .= ' '.$_OMP_rec['city'];
        }
        if (!empty($_OMP_rec['state'])) {
            $_OMP_rec['full_addr'] .= ' ('.$_OMP_rec['state'].')';
        }
        if (!empty($_OMP_rec['region'])) {
            $_OMP_rec['full_addr'] .= ' '.$_OMP_rec['region'];
        }
        if ($_OMP_rec['country'] != $_SESSION['country']) {
            $_OMP_rec['full_addr'] .= ' '.$_OMP_rec['country'];
        }
        if (!empty(trim($_OMP_rec['full_addr']))) {
            $_OMP_html['full_addr'] = urlencode($_OMP_rec['full_addr']);
        }
        if ($_OMP_get['list']) {
            $_OMP_html['rec0'] = $_OMP_rec['pkey'];
            $_OMP_html['rec1'] = $_OMP_rec['name'];
            $_OMP_html['rec2'] = $_OMP_rec['full_addr'];
            $_OMP_html['label0'] = '';
            $_OMP_html['label1'] = '';
            $_OMP_html['label2'] = '';
            /* mdc-list */
            $_OMP_html['cell1_numeric'] = ''; // or mdc-data-table__cell--numeric
            $_OMP_html['cell2_numeric'] = ''; // or mdc-data-table__cell--numeric
            $_OMP_html['cell3_numeric'] = ''; // or mdc-data-table__cell--numeric
        } else {
            $_OMP_html['tel'] = $_OMP_html['fax'] = '';
            if(!empty($_OMP_rec['tel'])) {
                $_OMP_html['tel'] = OMP_html_tel_link($_OMP_rec['tel']);
            }
            if(!empty($_OMP_rec['fax'])) {
                $_OMP_html['fax'] = OMP_html_tel_link($_OMP_rec['fax']);
            }
            $_OMP_rec['note'] = nl2br($_OMP_rec['note'], false);
            $_OMP_rec['label0'] = '';
            $_OMP_rec['label1'] = '';
            $_OMP_rec['label2'] = '';
        }
        if ($_OMP_rec['active'] == 't') {
            $_OMP_rec['active'] = $_OMP_LC[98];
        } else {
            $_OMP_rec['active'] = $_OMP_LC[99];
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
    $_OMP_tpl = OMP_TPL_READ.'63, 64, 500';
    $_OMP_lcl = OMP_LCL_READ.'6, 98, 99, 101, 102, 103, 104, 
        106, 108, 119, 504, 710, 1401';
    $_OMP_sql['sort_default'] = ' ORDER BY '.$_OMP_tbl_fld['pkey'].' ?';
    $_OMP_sql['sort_record'][0] = $_OMP_sql['sort_list'][0] = 
        $_OMP_sql['sort_default'];
    $_OMP_sql['sort_record'][1] = $_OMP_sql['sort_list'][1] = 
        ' ORDER BY '.$_OMP_tbl_fld['name'].' ?';
    if ($_OMP_get['list']) {
        $_OMP_list_tpl = 60;
        $_OMP_list_header_tpl = 63;
        $_OMP_list_rec_tpl = 64;
        $_OMP_sort_idx = array(504, 101, 102);
        /* mdc table */
        $_OMP_html['header1_numeric'] = ''; // or mdc-data-table__header-cell--numeric"
        $_OMP_html['header2_numeric'] = ''; // or mdc-data-table__header-cell--numeric"
        $_OMP_html['header3_numeric'] = ''; // or mdc-data-table__header-cell--numeric"
    } else {
        $_OMP_headline = 504;
        $_OMP_rec_tpl = 500;
        $_OMP_sort_idx = array(504, 101);
    }
    $_OMP_title = 6;
    $_OMP_sort['default'] = '0'; // Default sort
    $_OMP_sort['type'] = 0; // Default sort order
    require 'lib/read.php'; // Load record-read script
    break;
}
/**
* End read record
*/

/**
* Edit record
*/
case 'edit': {
    $_OMP_tpl = OMP_TPL_EDIT.'502';
    $_OMP_lcl = OMP_LCL_EDIT.'6, 98, 99, 101, 102, 103, 104, 
        106, 108, 111, 112, 113, 114, 115, 119, 504, 710, 726, 1401';
    // Check if insert-button was pushed
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['update'] = 'UPDATE '.$_OMP_tables[$_OMP_tbl].' SET '.
            $_OMP_tbl_fld['pkey'].' = ?, '.
            $_OMP_tbl_fld['name'].' = ?, '.
            $_OMP_tbl_fld['addr'].' = ?, '.
            $_OMP_tbl_fld['zip'].' = ?, '.
            $_OMP_tbl_fld['city'].' = ?, '.
            $_OMP_tbl_fld['state'].' = ?, '.
            $_OMP_tbl_fld['region'].' = ?, '.
            $_OMP_tbl_fld['country'].' = ?, '.
            $_OMP_tbl_fld['tel'].' = ?, '.
            $_OMP_tbl_fld['fax'].' = ?, '.
            $_OMP_tbl_fld['vat'].' = ?, '.
            $_OMP_tbl_fld['um'].' = ?, '.
            $_OMP_tbl_fld['curr'].' = ?, '.
            $_OMP_tbl_fld['active'].' = ?, '.
            $_OMP_tbl_fld['note'].' = ? WHERE '.
            $_OMP_tbl_fld['pkey'].' = ?';
//         From postgresql-8.4.2 and php-5.3.1 and MDB2_Driver_pgsql-1.4.1
//         datatype boolean does not work with 'active'
        $_OMP_datatypes = array('text', 'text', 'text', 'text', 'text', 
            'text', 'text', 'text', 'text', 'text', 'text', 'text',
            'text', 'text', 'text', 'text');
    } else {
        $_OMP_datatypes = array('text');
        $_OMP_input_tpl = 502;
        $_OMP_edit_tpl = 51;
        $_OMP_page_title_lcl = 6;
        $_OMP_combo_required = true;
        $_OMP_prefilter = true;
        $_OMP_active_switch = true;
        $_OMP_active_switch_locale = 119;
        $_OMP_drop_down = array(
            'lib/mdc-select-currencies.php',
            'lib/mdc-select-um.php'
        );
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
    $_OMP_tpl = OMP_TPL_FILTER.'80, 81, 82, 501';
    $_OMP_lcl = OMP_LCL_FILTER.'6, 98, 99, 101, 102, 103, 104, 106, 108, '.
        '111, 112, 113, 114, 115, 119, 504, 710, 726, 1401';
    $_OMP_page_title_lcl = 6;
    $_OMP_include_tpl = 501;
    $_OMP_drop_down = array(
        'lib/mdc-select-currencies.php',
        'lib/mdc-select-um.php'
    );
    /* set variables for drop-down lists */
    $_OMP_html['active_combo'] = '';
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
    $_OMP_tpl = OMP_TPL_NEW.'502';
    $_OMP_lcl = OMP_LCL_NEW.'6, 98, 99, 100, 101, 102, 103, 104, 105, '.
        '106, 108, 111, 112, 113, 114, 115, 119, 504, 710, 726, 1401';
    // Check if insert-button was pushed
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['insert'] = 'INSERT INTO '.
            $_OMP_tables[$_OMP_tbl].' ('.
            $_OMP_tbl_fld['pkey'].', '.$_OMP_tbl_fld['name'].', '.
            $_OMP_tbl_fld['addr'].', '.$_OMP_tbl_fld['zip'].', '.
            $_OMP_tbl_fld['city'].', '.$_OMP_tbl_fld['state'].', '.
            $_OMP_tbl_fld['region'].', '.$_OMP_tbl_fld['country'].', '.
            $_OMP_tbl_fld['vat'].', '.$_OMP_tbl_fld['tel'].', '.
            $_OMP_tbl_fld['fax'].', '.$_OMP_tbl_fld['note'].', '.
            $_OMP_tbl_fld['um'].', '.$_OMP_tbl_fld['curr'].', '.
            $_OMP_tbl_fld['active'].
            ') VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $_OMP_datatypes = array('text', 'text', 'text', 'text', 'text', 'text', 
            'text', 'text', 'text', 'text', 'text', 'text', 
            'text', 'text', 'boolean');
    } else {
        $_OMP_input_tpl = 502;
        $_OMP_include_tpl = 50;
        $_OMP_page_title_lcl = 6;
        $_OMP_combo_required = true;
        $_OMP_prefilter = true;
        $_OMP_active_switch = true;
        $_OMP_active_switch_locale = 119;
        $_OMP_drop_down = array(
            'lib/mdc-select-currencies.php',
            'lib/mdc-select-um.php'
        );
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
    $_OMP_lcl = OMP_LCL_DEL.'6, 101, 504';
    $_OMP_datatypes = array('text');
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['delete'] = 'DELETE FROM '.$_OMP_tables[$_OMP_tbl].
            ' WHERE '.$_OMP_tbl_fld['pkey'].' = ?';
    } else {
        $_OMP_sql['delete_pre'] = 'SELECT '.$_OMP_tbl_fld['pkey'].
            ' AS pkey, '.$_OMP_tbl_fld['name'].
            ' AS name FROM '.$_OMP_tables[$_OMP_tbl].
            ' WHERE '.$_OMP_tbl_fld['pkey'].' = ?';
        $_OMP_del_tpl = 52;
        $_OMP_page_title_lcl = 6;
    }
    require 'lib/del.php';
    break;
}
/**
* End delete record
*/
}
?>
