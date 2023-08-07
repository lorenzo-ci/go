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
// $Id: shippers.php,v 0.8 $
//
// Manage shippers records
//

require_once 'base.php';
// Name of current master-table
$_OMP_tbl = 'shippers';
require_once 'schemas/db-schema.php'; // Database schema
require_once 'schemas/shippers-schema.php'; // Shippers schema
// Table fields, length and keys
$_OMP_tbl_fld = $_OMP_shippers_fld;
$_OMP_fld_len = $_OMP_shippers_len;
$_OMP_tbl_key = $_OMP_shippers_key;
// Clients-shippers schema
require_once 'schemas/shippers-clients-schema.php';
// Suppliers-shippers schema
require_once 'schemas/shippers-suppliers-schema.php';
/**
* SQL code
*/
if ($_SESSION['cat'] >= 2) {
    $_OMP_sql['select'] = 'SELECT '.$_OMP_tbl_fld['pkey'].
        ' AS pkey, '.$_OMP_tbl_fld['name'].
        ' AS name, '.$_OMP_tbl_fld['addr'].
        ' AS addr, '.$_OMP_tbl_fld['zip'].
        ' AS zip, '.$_OMP_tbl_fld['city'].
        ' AS city, '.$_OMP_tbl_fld['state'].
        ' AS state, '.$_OMP_tbl_fld['region'].
        ' AS region,'.$_OMP_tbl_fld['country'].
        ' AS country, '.$_OMP_tbl_fld['vat'].
        ' AS vat, '.$_OMP_tbl_fld['tel'].
        ' AS tel, '.$_OMP_tbl_fld['fax'].
        ' AS fax, '.$_OMP_tbl_fld['note'].
        ' AS note FROM '.$_OMP_tables[$_OMP_tbl];
} else {
    $_OMP_sql['select'] = 'SELECT ship.'.$_OMP_tbl_fld['pkey'].
        ' AS pkey, ship.'.$_OMP_tbl_fld['name'].
        ' AS name, ship.'.$_OMP_tbl_fld['addr'].
        ' AS addr, ship.'.$_OMP_tbl_fld['zip'].
        ' AS zip, ship.'.$_OMP_tbl_fld['city'].
        ' AS city, ship.'.$_OMP_tbl_fld['state'].
        ' AS state, ship.'.$_OMP_tbl_fld['region'].
        ' AS region, ship.'.$_OMP_tbl_fld['country'].
        ' AS country, ship.'.$_OMP_tbl_fld['vat'].
        ' AS vat, ship.'.$_OMP_tbl_fld['tel'].
        ' AS tel, ship.'.$_OMP_tbl_fld['fax'].
        ' AS fax, ship.'.$_OMP_tbl_fld['note'].
        ' AS note FROM '.$_OMP_tables[$_OMP_tbl].' AS ship';
}
// Please note $_OMP_sql['select_join'] 
// is used in OMP_sqlCount() (see functions.php)
if ($_SESSION['cat'] == 0) { // Select for clients
    $_OMP_sql['select_join'] = ' INNER JOIN '.
        $_OMP_tables['shippers-cli'].' AS shipcli ON (ship.'.
        $_OMP_tbl_fld['pkey'].' = shipcli.'.
        $_OMP_shippers_cli_fld['shipper_pkey'].' AND lower(shipcli.'.
        $_OMP_shippers_cli_fld['pkey'].') = lower('.
        $_OMP_db->quote($_SESSION['id']).'))';
    $_OMP_sql['select'] .= $_OMP_sql['select_join'];
} elseif ($_SESSION['cat'] == 1) { // Select for suppliers
    $_OMP_sql['select_join'] = ' INNER JOIN '.
        $_OMP_tables['shippers-sup'].' AS shipsup ON (ship.'.
        $_OMP_tbl_fld['pkey'].' = shipsup.'.
        $_OMP_shippers_sup_fld['shipper_pkey'].' AND lower(shipsup.'.
        $_OMP_shippers_sup_fld['pkey'].
        ') = lower('.$_OMP_db->quote($_SESSION['id']).'))';
    $_OMP_sql['select'] .= $_OMP_sql['select_join'];
}
if ($_SESSION['cat'] >= 2) {
    $_OMP_sql['row'] = $_OMP_sql['select'].' WHERE '.
        $_OMP_tbl_fld['pkey'].' = ?';
} else {
    $_OMP_sql['row'] = $_OMP_sql['select'].' WHERE ship.'.
        $_OMP_tbl_fld['pkey'].' = ?';
}
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
    if ($_OMP_get['action'] == 'new') {
        $_OMP_rec['country'] = $_SESSION['country'];
        return;
    }
    $_OMP_rec = array_map('OMP_htmlentities', $_OMP_rec);
    if ($_OMP_get['action'] == 'del') {
        $_OMP_table_header = array($_OMP_LC[1301], $_OMP_LC[101]);
        $_OMP_table_data = array($_OMP_rec['pkey'], $_OMP_rec['name']);
        return;
    } elseif ($_OMP_get['action'] == 'read') {
        $_OMP_html['full_addr'] = $_OMP_rec['full_addr'] = '';
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
            /* or mdc-data-table__cell--numeric */
            $_OMP_html['cell1_numeric'] = '';
            $_OMP_html['cell2_numeric'] = '';
            $_OMP_html['cell3_numeric'] = '';
        } else {
            /* OMP_html_tel_link is defined in functions.php */
            if(!empty($_OMP_rec['tel'])) {
                $_OMP_html['tel'] = OMP_html_tel_link($_OMP_rec['tel']);
            }
            if(!empty($_OMP_rec['fax'])) {
                $_OMP_html['fax'] = OMP_html_tel_link($_OMP_rec['fax']);
            }
            $_OMP_rec['note'] = nl2br($_OMP_rec['note'], false);
        }
    }
}
/**
* End functions
*/
// See OMP_makeSql()
$_OMP_table_alias = ($_SESSION['cat'] >= 2) ? '' : 'ship.';
switch ($_OMP_get['action']) {

/**
* Read record
*/
case 'read': {
    $_OMP_tpl = OMP_TPL_READ.'63, 64, 1300';
    $_OMP_lcl = OMP_LCL_READ.'101, 102, 103, 104, 106, 108, 1300, 1301';
    if ($_SESSION['cat'] >= 2) {
        $_OMP_sql['sort_default'] = ' ORDER BY '.$_OMP_tbl_fld['pkey'].' ?';
        $_OMP_sql['sort_record'][0] = $_OMP_sql['sort_list'][0] = 
            $_OMP_sql['sort_default'];
        $_OMP_sql['sort_record'][1] = $_OMP_sql['sort_list'][1] = ' ORDER BY '.
            $_OMP_tbl_fld['name'].' ?';
    } else {
        $_OMP_sql['sort_default'] = ' ORDER BY ship.'.
            $_OMP_tbl_fld['pkey'].' ?';
        $_OMP_sql['sort_record'][0] = $_OMP_sql['sort_list'][0] = 
            $_OMP_sql['sort_default'];
        $_OMP_sql['sort_record'][1] = $_OMP_sql['sort_list'][1] = 
            ' ORDER BY ship.'.$_OMP_tbl_fld['name'].' ?';
    }
    if ($_OMP_get['list']) {
        $_OMP_list_tpl = 60;
        $_OMP_list_header_tpl = 63;
        $_OMP_list_rec_tpl = 64;
        $_OMP_sort_idx = array(1301, 101, 102);
        $_OMP_list_wrapper = 'wrapper3';
        /* mdc table */
        // or mdc-data-table__header-cell--numeric"
        $_OMP_html['header1_numeric'] = '';
        $_OMP_html['header2_numeric'] = '';
        $_OMP_html['header3_numeric'] = '';
    } else {
        $_OMP_rec_tpl = 1300;
        $_OMP_sort_idx = array(1301, 101);
    }
    $_OMP_html_tel = $_OMP_html_fax = '';
    $_OMP_title = 1300;
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
    $_OMP_tpl = OMP_TPL_EDIT.'1302';
    $_OMP_lcl = OMP_LCL_EDIT.'101, 102, 103, 104, 106, 108, 111, 
        112, 113, 114, 115, 1300, 1301';
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
            $_OMP_tbl_fld['vat'].' = ?, '.
            $_OMP_tbl_fld['tel'].' = ?, '.
            $_OMP_tbl_fld['fax'].' = ?, '.
            $_OMP_tbl_fld['note'].' = ? WHERE '.
            $_OMP_tbl_fld['pkey'].' = ?';
        $_OMP_datatypes = array('text', 'text', 'text', 'text', 'text', 
            'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text');
    } else {
        $_OMP_datatypes = array('text');
        $_OMP_input_tpl = 1302;
        $_OMP_edit_tpl = 51;
        $_OMP_page_title_lcl = 1300;
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
    $_OMP_tpl = OMP_TPL_FILTER.'1301';
    $_OMP_lcl = OMP_LCL_FILTER.'101, 102, 103, 104, 106, 
        108, 111, 112, 113, 114, 115, 1300, 1301';
    $_OMP_page_title_lcl = 1300;
    $_OMP_include_tpl = 1301;
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
    $_OMP_tpl = OMP_TPL_NEW.'1302';
    $_OMP_lcl = OMP_LCL_NEW.'100, 101, 102, 103, 104, 
        105, 106, 108, 111, 112, 113, 114, 115, 1300, 1301';
    // Check if insert-button was pushed
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['insert'] = 'INSERT INTO '.
            $_OMP_tables[$_OMP_tbl].' ('.
            $_OMP_tbl_fld['pkey'].', '.
            $_OMP_tbl_fld['name'].', '.
            $_OMP_tbl_fld['addr'].', '.
            $_OMP_tbl_fld['zip'].', '.
            $_OMP_tbl_fld['city'].', '.
            $_OMP_tbl_fld['state'].', '.
            $_OMP_tbl_fld['region'].', '.
            $_OMP_tbl_fld['country'].', '.
            $_OMP_tbl_fld['vat'].', '.
            $_OMP_tbl_fld['tel'].', '.
            $_OMP_tbl_fld['fax'].', '.
            $_OMP_tbl_fld['note'].
            ') VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $_OMP_datatypes = array('text', 'text', 'text', 'text', 'text', 
            'text', 'text', 'text', 'text', 'text', 'text', 'text');
    } else {
        $_OMP_input_tpl = 1302;
        $_OMP_include_tpl = 50;
        $_OMP_page_title_lcl = 1300;
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
    $_OMP_lcl = OMP_LCL_DEL.'101, 1300, 1301';
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
        $_OMP_page_title_lcl = 1300;
    }
    require 'lib/del.php';
    break;
}
/**
* End delete record
*/
}
?>
