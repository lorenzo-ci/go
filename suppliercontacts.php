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
// $Id: supplier-contacts.php,v 0.8 $
//
// Manage supplier-contacts records
//

require_once 'base.php';

require_once 'lib/credentials.php';
// Only operator and admin are allowed
OMP_checkCredentials(array(2, 3));
require_once 'schemas/db-schema.php'; // Database schema
// Name of current master-table
$_OMP_tbl = 'suppliercontacts';
// Suppliers schema
require_once 'schemas/suppliers-schema.php';
// SQL for drop-down lists
$_OMP_get['popup'] or require_once 'lib/dd-sql.php';
// Client-contacts schema
require_once 'schemas/suppliercontacts-schema.php';
// Table fields, length and keys
$_OMP_tbl_fld = $_OMP_suppliercontacts_fld;
$_OMP_fld_len = $_OMP_suppliercontacts_len;
$_OMP_tbl_key = $_OMP_suppliercontacts_key;
/**
* SQL code
*/
$_OMP_sql['select'] = 'SELECT supcon.'.$_OMP_tbl_fld['pkey'].
    ' AS pkey, supcon.'.$_OMP_tbl_fld['supplier_pkey'].
    ' AS supplier_pkey, sup.'.$_OMP_suppliers_fld['name'].
    ' AS supplier_label, supcon.'.$_OMP_tbl_fld['first_name'].
    ' AS first_name, supcon.'.$_OMP_tbl_fld['last_name'].
    ' AS last_name, '.$_OMP_tbl_fld['title'].
    ' AS title, supcon.'.$_OMP_tbl_fld['addr'].
    ' AS addr, supcon.'.$_OMP_tbl_fld['zip'].
    ' AS zip, supcon.'.$_OMP_tbl_fld['city'].
    ' AS city, supcon.'.$_OMP_tbl_fld['state'].
    ' AS state, supcon.'.$_OMP_tbl_fld['region'].
    ' AS region, supcon.'.$_OMP_tbl_fld['country'].
    ' AS country, supcon.'.$_OMP_tbl_fld['tel_home'].
    ' AS tel_home, supcon.'.$_OMP_tbl_fld['tel_office'].
    ' AS tel_office, supcon.'.$_OMP_tbl_fld['mobile'].
    ' AS mobile, supcon.'.$_OMP_tbl_fld['tel_other'].
    ' AS tel_other, supcon.'.$_OMP_tbl_fld['fax'].
    ' AS fax, supcon.'.$_OMP_tbl_fld['email'].
    ' AS email FROM '.$_OMP_tables[$_OMP_tbl].
    ' AS supcon LEFT JOIN '.$_OMP_tables['suppliers'].
    ' AS sup ON (supcon.'.$_OMP_tbl_fld['supplier_pkey'].' = sup.'.
    $_OMP_suppliers_fld['pkey'].')';
$_OMP_sql['row'] = $_OMP_sql['select'].' WHERE supcon.'.
    $_OMP_tbl_fld['pkey'].' = ? AND supcon.'.
    $_OMP_tbl_fld['supplier_pkey'].' = ?';;
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
        $_OMP_html_tel_home, $_OMP_html_tel_office,
        $_OMP_html_mobile, $_OMP_html_tel_other,
        $_OMP_LC, $_OMP_get, $_OMP_rec, $_OMP_tbl_fld, $_OMP_TPL;
    if ($_OMP_get['action'] == 'new') {
        $_OMP_rec['country'] = $_SESSION['country'];
        return;
    }
    $_OMP_rec = array_map('OMP_htmlentities', $_OMP_rec);
    if ($_OMP_get['action'] == 'del') {
        $_OMP_table_header = array($_OMP_LC[101], $_OMP_LC[504]);
        $_OMP_table_data = array($_OMP_rec['first_name'].' '.
            $_OMP_rec['last_name'], $_OMP_rec['supplier_pkey']);
        return;
    } elseif ($_OMP_get['action'] == 'read') {
        if (!$_OMP_get['popup']) {
            $tmp = OMP_PATH.'suppliers.php?'.OMP_link(
                'filter=1&pkey='.
                urlencode(
                    html_entity_decode(
                        $_OMP_rec['supplier_pkey']
                    )
                ).'&popup=1');
            $_OMP_rec['supplier_pkey'] =
                OMP_popLink($_OMP_TPL[9],
                    $tmp,
                    $_OMP_rec['supplier_pkey']
                );
        }
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
            $_OMP_html['rec0'] = $_OMP_rec['last_name'].' '.
                $_OMP_rec['first_name'];
            $_OMP_html['rec1'] = $_OMP_rec['supplier_pkey'];
            $_OMP_html['rec2'] = $_OMP_rec['full_addr'];
            $_OMP_html['label0'] = '';
            $_OMP_html['label1'] = $_OMP_rec['supplier_label'];
            $_OMP_html['label2'] = '';
            /* mdc-list */
            /* or mdc-data-table__cell--numeric */
            $_OMP_html['cell1_numeric'] = '';
            $_OMP_html['cell2_numeric'] = '';
            $_OMP_html['cell3_numeric'] = '';
        } else {
            $_OMP_html['tel_home'] = $_OMP_html['tel_office'] =
            $_OMP_html['mobile'] = $_OMP_html['tel_other'] =
            $_OMP_html['fax'] = $_OMP_html['email'] = '';
            if (!empty($_OMP_rec['tel_home'])) {
                $_OMP_html['tel_home'] =
                    OMP_html_tel_link($_OMP_rec['tel_home']);
            }
            if (!empty($_OMP_rec['tel_office'])) {
                $_OMP_html['tel_office'] =
                    OMP_html_tel_link($_OMP_rec['tel_office']);
            }
            if (!empty($_OMP_rec['mobile'])) {
                $_OMP_html['mobile'] =
                    OMP_html_tel_link($_OMP_rec['mobile']);
            }
            if (!empty($_OMP_rec['tel_other'])) {
                $_OMP_html['tel_other'] =
                    OMP_html_tel_link($_OMP_rec['tel_other']);
            }
            if (!empty($_OMP_rec['fax'])) {
                $_OMP_html['fax'] =
                    OMP_html_tel_link($_OMP_rec['fax']);
            }
            if (!empty($_OMP_rec['email'])) {
                $_OMP_html['email'] = $_OMP_rec['email'];
            }
        }
    }
}
/**
* End functions
*/
!isset($_OMP_get['first_name']) or 
    $_OMP_get['first_name'] = stripslashes($_OMP_get['first_name']);
!isset($_OMP_get['last_name']) or 
    $_OMP_get['last_name'] = stripslashes($_OMP_get['last_name']);
// See makeSql
$_OMP_table_alias = 'supcon.';
switch ($_OMP_get['action']) {

/**
* Read record
*/
case 'read': {
    $_OMP_tpl = OMP_TPL_READ.'9, 63, 64, 1200';
    $_OMP_lcl = OMP_LCL_READ.'6, 42, 54, 102, 104, 504, 1000, '.
        '1001, 1002, 1003, 1004, 1005, 1006, 1007';
    $_OMP_sql['sort_default'] = ' ORDER BY supcon.'.
        $_OMP_tbl_fld['last_name'].' ?, supcon.'.
        $_OMP_tbl_fld['first_name'];
    $_OMP_sql['sort_record'][0] = $_OMP_sql['sort_list'][0] = 
        $_OMP_sql['sort_default'];
    $_OMP_sql['sort_record'][1] = $_OMP_sql['sort_list'][1] = 
        ' ORDER BY supcon.'.$_OMP_tbl_fld['supplier_pkey'].
        ' ?, supcon.'.$_OMP_tbl_fld['last_name'].', supcon.'.
        $_OMP_tbl_fld['first_name'];
    $_OMP_sql['sort_record'][2] = ' ORDER BY supcon.'.
        $_OMP_tbl_fld['first_name'].' ?, supcon.'.
        $_OMP_tbl_fld['last_name'];
    if ($_OMP_get['list']) {
        $_OMP_list_tpl = 60;
        $_OMP_list_header_tpl = 63;
        $_OMP_list_rec_tpl = 64;
        $_OMP_sort_idx = array(1000, 504, 102);
        $_OMP_list_wrapper = 'wrapper3';
        /* mdc table */
        // or mdc-data-table__header-cell--numeric"
        $_OMP_html['header1_numeric'] = '';
        $_OMP_html['header2_numeric'] = '';
        $_OMP_html['header3_numeric'] = '';
    } else {
        $_OMP_rec_tpl = 1200;
        $_OMP_sort_idx = array(1001, 504, 1000);
    }
    $_OMP_html_tel_home =
        $_OMP_html_tel_office =
        $_OMP_html_mobile =
        $_OMP_html_tel_other = '';
    $_OMP_title = 54;
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
    $_OMP_tpl = OMP_TPL_EDIT.'1202';
    $_OMP_lcl = OMP_LCL_EDIT.'54, 102, 104, 111, 112, 113, 114, '.
        '115, 504, 1000, 1001, 1002, 1003, 1004, 1005, 1006, 1007';
    // Check if insert-button was pushed
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['update'] = 'UPDATE '.$_OMP_tables[$_OMP_tbl].' SET '.
            $_OMP_tbl_fld['supplier_pkey'].' = ?, '.
            $_OMP_tbl_fld['first_name'].' = ?, '.
            $_OMP_tbl_fld['last_name'].' = ?, '.
            $_OMP_tbl_fld['title'].' = ?, '.
            $_OMP_tbl_fld['addr'].' = ?, '.
            $_OMP_tbl_fld['zip'].' = ?, '.
            $_OMP_tbl_fld['city'].' = ?, '.
            $_OMP_tbl_fld['state'].' = ?, '.
            $_OMP_tbl_fld['region'].' = ?, '.
            $_OMP_tbl_fld['country'].' = ?, '.
            $_OMP_tbl_fld['tel_home'].' = ?, '.
            $_OMP_tbl_fld['tel_office'].' = ?, '.
            $_OMP_tbl_fld['mobile'].' = ?, '.
            $_OMP_tbl_fld['tel_other'].' = ?, '.
            $_OMP_tbl_fld['fax'].' = ?, '.
            $_OMP_tbl_fld['email'].' = ? WHERE '.
            $_OMP_tbl_fld['pkey'].' = ? AND '.
            $_OMP_tbl_fld['supplier_pkey'].' = ?';
        $_OMP_datatypes = array('text', 'text', 'text', 'text', 'text', 
            'text', 'text', 'text', 'text', 'text', 'text', 'text', 
            'text', 'text', 'text', 'text', 'text', 'text');
    } else {
        $_OMP_datatypes = array('integer', 'text');
        $_OMP_combo_required = true;
        $_OMP_drop_down = array('lib/mdc-select-suppliers.php');
        $_OMP_input_tpl = 1202;
        $_OMP_edit_tpl = 51;
        $_OMP_page_title_lcl = 54;
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
    $_OMP_tpl = OMP_TPL_FILTER.'1201';
    $_OMP_lcl = OMP_LCL_FILTER.'54, 102, 104, 111, 112, 113, '.
        '114, 115, 504, 1000, 1001, 1002, 1003, 1004, 1005, 1006, 1007';
    if ($_SESSION['cat'] == 1) { // Filter for suppliers
        $_OMP_supplier_combo = '<input disabled type="text" '.
            'name="supplier_pkey" id="supplier_pkey" '. 
            'size="10" value="'.htmlentities($_SESSION['id']).'" /> '.
            '<input type="hidden" name="form[enc_supplier_pkey]" '.
            'id="form[enc_supplier_pkey]" value="'.
            htmlentities($_SESSION['id']).'" />';
    } else {
        $_OMP_drop_down = array('lib/mdc-select-suppliers.php');
    }
    $_OMP_page_title_lcl = 54;
    $_OMP_include_tpl = 1201;
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
    $_OMP_tpl = OMP_TPL_NEW.'1202';
    $_OMP_lcl = OMP_LCL_NEW.'54, 102, 104, 111, 112, 113, '.
        '114, 115, 504, 1000, 1001, 1002, 1003, 1004, 1005, 1006, 1007';
    // Check if insert-button was pushed
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['insert'] = 'INSERT INTO '.
            $_OMP_tables[$_OMP_tbl].' ('.
            $_OMP_tbl_fld['supplier_pkey'].', '.
            $_OMP_tbl_fld['first_name'].', '.
            $_OMP_tbl_fld['last_name'].', '.
            $_OMP_tbl_fld['addr'].', '.
            $_OMP_tbl_fld['zip'].', '.
            $_OMP_tbl_fld['city'].', '.
            $_OMP_tbl_fld['state'].', '.
            $_OMP_tbl_fld['region'].', '.
            $_OMP_tbl_fld['country'].', '.
            $_OMP_tbl_fld['tel_home'].', '.
            $_OMP_tbl_fld['tel_office'].', '.
            $_OMP_tbl_fld['mobile'].', '.
            $_OMP_tbl_fld['tel_other'].', '.
            $_OMP_tbl_fld['fax'].', '.
            $_OMP_tbl_fld['email'].', '.
            $_OMP_tbl_fld['user'].
            ') VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $_OMP_datatypes = array('text', 'text', 'text', 'text', 'text', 
            'text', 'text', 'text', 'text', 'text', 'text', 
            'text', 'text', 'text', 'text', 'text');
        $_OMP_sql['get_last'] = 
            'SELECT pkey FROM supcon_log WHERE "user" = ';
    } else {
        $_OMP_combo_required = true;
        $_OMP_drop_down = array('lib/mdc-select-suppliers.php');
        $_OMP_input_tpl = 1202;
        $_OMP_include_tpl = 50;
        $_OMP_page_title_lcl = 54;
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
    $_OMP_lcl = OMP_LCL_DEL.'54, 101, 504';
    $_OMP_datatypes = array('integer', 'text');
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['delete'] = 'DELETE FROM '.$_OMP_tables[$_OMP_tbl].
            ' WHERE '.$_OMP_tbl_fld['pkey'].' = ? AND '.
            $_OMP_tbl_fld['supplier_pkey'].' = ?';
    } else {
        $_OMP_sql['delete_pre'] = 'SELECT '.
            $_OMP_tbl_fld['supplier_pkey'].' AS supplier_pkey, '.
            $_OMP_tbl_fld['first_name'].' AS first_name, '.
            $_OMP_tbl_fld['last_name'].' AS last_name FROM '.
            $_OMP_tables[$_OMP_tbl].
            ' WHERE '.$_OMP_tbl_fld['pkey'].' = ? AND '.
            $_OMP_tbl_fld['supplier_pkey'].' = ?';
        $_OMP_del_tpl = 52;
        $_OMP_page_title_lcl = 54;
    }
    require 'lib/del.php';
    break;
}
/**
* End delete record
*/
}
?>
