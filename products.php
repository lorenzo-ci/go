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
// | Author: Lorenzo Ciani <lorenzo@ciani.it>                             |
// +----------------------------------------------------------------------+
//
// $Id: products.php,v 0.9 $
//
// Manage products records
//

require_once 'base.php';
// Name of current master-table
$_OMP_tbl = 'products';
require_once 'schemas/db-schema.php'; // Database schema
require_once 'schemas/ranges-schema.php'; // Ranges schema
require_once 'schemas/suppliers-schema.php'; // Suppliers schema
require_once 'schemas/products-schema.php'; // Products schema
// SQL for drop-down lists
$_OMP_get['popup'] or require_once 'lib/dd-sql.php';
// Table fields, length and keys
$_OMP_tbl_fld = $_OMP_products_fld;
$_OMP_fld_len = $_OMP_products_len;
$_OMP_tbl_key = $_OMP_products_key;
/**
* SQL code
*/
$_OMP_sql['select'] = 'SELECT prod.'.$_OMP_tbl_fld['pkey'].' AS pkey, prod.'.
    $_OMP_tbl_fld['range_pkey'].' AS range_pkey, prod.'.
    $_OMP_tbl_fld['description'].' AS description, prod.'.
    $_OMP_tbl_fld['supplier_pkey'].' AS supplier_pkey, supp.'.
    $_OMP_suppliers_fld['name'].' AS supplier_name, prod.'.
    $_OMP_tbl_fld['price'].' AS price, prod.'.
    $_OMP_tbl_fld['duty'].' AS duty, prod.'.
    $_OMP_tbl_fld['titer'].' AS titer, prod.'.
    $_OMP_tbl_fld['cut'].' AS cut, prod.'.
    $_OMP_tbl_fld['luster'].' As luster, prod.'.
    $_OMP_tbl_fld['note'].' AS note, prod.'.
    $_OMP_tbl_fld['monthly'].' AS monthly, prod.'.
    $_OMP_tbl_fld['active'].' AS active, prod.'.
    $_OMP_tbl_fld['grade'].' AS grade, range.'.
    $_OMP_ranges_fld['name'].' AS range_name FROM '.
    $_OMP_tables[$_OMP_tbl].' AS prod LEFT JOIN '.$_OMP_tables['ranges'].
    ' AS range ON (range.'.$_OMP_ranges_fld['pkey'].' = prod.'.
    $_OMP_tbl_fld['range_pkey'].') LEFT JOIN '.$_OMP_tables['suppliers'].
    ' AS supp ON (supp.'.$_OMP_suppliers_fld['pkey'].
    ' = prod.'.$_OMP_tbl_fld['supplier_pkey'].')';
$_OMP_sql['row'] = $_OMP_sql['select'].' WHERE '.$_OMP_tbl_fld['pkey'].' = ?';
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
    global $_OMP_get, $_OMP_html, $_OMP_LC, $_OMP_rec,
        $_OMP_table_data, $_OMP_table_header, $_OMP_TPL, $ts;
    $_OMP_rec = array_map('OMP_htmlentities', $_OMP_rec);
    $ts = ($ts) ? OMP_DB_THOUSANDS_SEP: $_SESSION['ts'];
    if ($_OMP_get['action'] == 'del') {
        $_OMP_table_header = array($_OMP_LC[600], $_OMP_LC[601]);
        $_OMP_table_data = array($_OMP_rec['pkey'], $_OMP_rec['description']);
        return;
    } elseif ($_OMP_get['action'] == 'new') {
        $_OMP_rec['price'] = '0';
        $_OMP_rec['grade'] = '0';
        $_OMP_rec['monthly'] = '1';
        $_OMP_rec['monthly_true'] = 'selected="selected"';
        $_OMP_rec['monthly_false'] = '';
        $_OMP_rec['active_true'] = 'selected="selected"';
        $_OMP_rec['active_false'] = '';
        return;
    }
    if ($_OMP_get['action'] == 'read') {
        if (!$_OMP_get['popup']) {
            /* OMP_popLink is defined in base.php */
            $_OMP_rec['supplier_pkey'] =
                OMP_popLink(
                    $_OMP_TPL[9], OMP_PATH.
                    'suppliers.php?q='.base64_encode('filter=1&pkey='.
                    urlencode(html_entity_decode($_OMP_rec['supplier_pkey'])).
                    '&popup=1'),
                    $_OMP_rec['supplier_pkey']
                );
        }
        $_OMP_rec['duty'] = (empty($_OMP_rec['duty'])) ? 0 :
            number_format(
                $_OMP_rec['duty'] * 100, 2, $_SESSION['dp'], $ts
            ).'%';
        $_OMP_rec['price'] = (empty($_OMP_rec['price'])) ? 0 :
            number_format(
                $_OMP_rec['price'], 2, $_SESSION['dp'], $ts
            );
        $_OMP_rec['monthly'] =
            ($_OMP_rec['monthly'] == 'f') ? $_OMP_LC[615] :
            $_OMP_LC[614];
        if ($_OMP_rec['active'] == 't') {
            $_OMP_rec['active'] = $_OMP_LC[98];
        } else {
            $_OMP_rec['active'] = $_OMP_LC[99];
        }
        if ($_OMP_get['list']) {
            $_OMP_html['rec0'] = $_OMP_rec['pkey'];
            $_OMP_html['rec1'] = $_OMP_rec['description'];
            $_OMP_html['rec2'] = $_OMP_rec['supplier_pkey'];
            $_OMP_html['label0'] = '';
            $_OMP_html['label1'] = '';
            $_OMP_html['label2'] = $_OMP_rec['supplier_name'];
            $_OMP_html['cell1_numeric'] = ''; // or mdc-data-table__cell--numeric
            $_OMP_html['cell2_numeric'] = ''; // or mdc-data-table__cell--numeric
            $_OMP_html['cell3_numeric'] = ''; // or mdc-data-table__cell--numeric
        }
    } elseif ($_OMP_get['action'] == 'edit') {
        if ($_OMP_rec['monthly'] == 'f') {
            $_OMP_rec['monthly_false'] = 'selected="selected"';
            $_OMP_rec['monthly_true'] = '';
        } else {
            $_OMP_rec['monthly_true'] = 'selected="selected"';
            $_OMP_rec['monthly_false'] = '';
        }
        /*$_OMP_rec['duty'] = 
            number_format($_OMP_rec['duty']*100, 2, $_SESSION['dp'], 
                $ts);
        $_OMP_rec_sf['price'] = 
            number_format($_OMP_rec['price'], 2, $_SESSION['dp'], $ts);*/
        if ($_OMP_rec['active'] == 't') {
            $_OMP_rec['active_true'] = 'selected="selected"';
            $_OMP_rec['active_false'] = '';
        } else {
            $_OMP_rec['active_false'] = 'selected="selected"';
            $_OMP_rec['active_true'] = '';
        }
    }
}
/**
* End functions
*/
$_OMP_table_alias = 'prod.'; // See makeSql()
switch ($_OMP_get['action']) {
/**
* Read record
*/
case 'read': {
    $_OMP_tpl = OMP_TPL_READ.'9, 63, 64, 600';
    /* Locales 23 and 39 are required by template 7 even if they are useless */
    $_OMP_lcl = OMP_LCL_READ.'7, 23, 39, 98, 99, 106, 119, 504, 600, 601, 602, 
        603, 604, 606, 607, 608, 609, 610, 614, 615, 616';
    $_OMP_sql['sort_default'] = ' ORDER BY prod.'.$_OMP_tbl_fld['pkey'].' ?';
    $_OMP_sql['sort_record'][0] = $_OMP_sql['sort_list'][0] = 
        $_OMP_sql['sort_default'];
    $_OMP_sql['sort_record'][1] = $_OMP_sql['sort_list'][1] = ' ORDER BY prod.'.
        $_OMP_tbl_fld['description'].' ?';
    $_OMP_sql['sort_record'][2] = $_OMP_sql['sort_list'][2] = ' ORDER BY prod.'.
        $_OMP_tbl_fld['supplier_pkey'].' ?';
    $_OMP_sort_idx = array(600, 601, 504);
    if ($_OMP_get['list']) {
        $_OMP_list_tpl = 60;
        $_OMP_list_header_tpl = 63;
        $_OMP_list_rec_tpl = 64;
        /* CSS class for record list */
        $_OMP_list_wrapper = 'wrapper3';
        /* mdc table */
        $_OMP_html['header1_numeric'] = ''; // or mdc-data-table__header-cell--numeric"
        $_OMP_html['header2_numeric'] = ''; // or mdc-data-table__header-cell--numeric"
        $_OMP_html['header3_numeric'] = ''; // or mdc-data-table__header-cell--numeric"
    } else {
        $_OMP_rec_tpl = 600;
        $_OMP_headline = 600;
    }
    $_OMP_title = 7;
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
    $_OMP_tpl = OMP_TPL_EDIT.'603';
    /* Need 85 for OMP_genErr() @see base.php */
    $_OMP_lcl = OMP_LCL_EDIT.'7, 106, 504,
        600, 601, 602, 603, 604, 607, 608, 609,
        610, 614, 615, 616';
    // Check if insert-button was pushed
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['update'] = 'UPDATE '.
            $_OMP_tables[$_OMP_tbl].' SET '.
            $_OMP_tbl_fld['pkey'].' = ?, '.
            $_OMP_tbl_fld['range_pkey'].' = ?, '.
            $_OMP_tbl_fld['description'].' = ?, '.
            $_OMP_tbl_fld['supplier_pkey'].' = ?, '.
            $_OMP_tbl_fld['price'].' = ?, '.
            $_OMP_tbl_fld['duty'].' = ?, '.
            $_OMP_tbl_fld['titer'].' = ?, '.
            $_OMP_tbl_fld['cut'].' = ?, '.
            $_OMP_tbl_fld['luster'].' = ?, '.
            $_OMP_tbl_fld['note'].' = ?, '.
            $_OMP_tbl_fld['active'].' = ?, '.
            $_OMP_tbl_fld['monthly'].' = ?, '.
            $_OMP_tbl_fld['grade'].' = ? WHERE '.
            $_OMP_tbl_fld['pkey'].' = ?';
        $_OMP_datatypes = array('text', 'integer',
            'text', 'text', 'decimal',
            'decimal', 'text', 'text', 'text',
            'text', 'boolean', 'boolean',
            'integer', 'text');
    } else {
        $_OMP_datatypes = array('text');
        $_OMP_drop_down = array(
            'lib/mdc-select-ranges.php',
            'lib/mdc-select-suppliers.php'
        );
        $_OMP_input_tpl = 603;
        $_OMP_edit_tpl = 51;
        $_OMP_page_title_lcl = 7;
        /* switches and scripts to set their value */
        $_OMP_monthly_switch = true;
        $_OMP_monthly_switch_locale = 614;
        $_OMP_active_switch = true;
        $_OMP_active_switch_locale = 119;
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
    $_OMP_tpl = OMP_TPL_FILTER.'601';
    $_OMP_lcl = OMP_LCL_FILTER.'7, 106,
        504, 600, 601, 602, 603, 604, 606,
        607, 608, 609, 610, 614, 615, 616';
    if ($_SESSION['cat'] == 1) { // Filter for suppliers
        $_OMP_supplier_combo_disabled = true;
        $_OMP_rec['supplier_pkey'] =
            htmlentities($_SESSION['id']);
        // $_OMP_html['supplier_combo'] =
        //     '<input disabled type="text" name="supplier_pkey" '.
        //     'id="supplier_pkey" size="10" value="'.
        //     htmlentities($_SESSION['id']).'" /> '.
        //     '<input type="hidden" name="form[enc_supplier_pkey]" '.
        //     'id="form[enc_supplier_pkey]" value="'.
        //     htmlentities($_SESSION['id']).'" />';
    }
    $_OMP_drop_down = array(
        'lib/mdc-select-ranges.php',
        'lib//mdc-select-suppliers.php'
    );
    /* set variables for drop-down lists */
    $_OMP_html['active_combo'] = $_OMP_html['monthly_combo'] = '';
    $_OMP_page_title_lcl = 7;
    $_OMP_include_tpl = 601;
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
    $_OMP_tpl = OMP_TPL_NEW.'602';
    $_OMP_lcl = OMP_LCL_NEW.'7, 98, 99, 106, 119, 504, 600, 601, 602, 
        603, 604, 607, 608, 609, 610, 613, 614, 615, 616';
    // Check if insert-button was pushed
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['insert'] = 'INSERT INTO '.$_OMP_tables[$_OMP_tbl].
            ' ('.$_OMP_tbl_fld['pkey'].
            ', '.$_OMP_tbl_fld['range_pkey'].
            ', '.$_OMP_tbl_fld['description'].
            ', '.$_OMP_tbl_fld['supplier_pkey'].
            ', '.$_OMP_tbl_fld['price'].
            ', '.$_OMP_tbl_fld['duty'].
            ', '.$_OMP_tbl_fld['titer'].
            ', '.$_OMP_tbl_fld['cut'].
            ', '.$_OMP_tbl_fld['luster'].
            ', '.$_OMP_tbl_fld['note'].
            ', '.$_OMP_tbl_fld['monthly'].
            ', '.$_OMP_tbl_fld['grade'].
            ') VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $_OMP_datatypes = array('text', 'integer', 'text', 
            'text', 'decimal', 'decimal', 'text', 'text', 
            'text', 'text', 'boolean', 'integer', 'integer');
    } else {
        $_OMP_combo_required = true;
        $_OMP_drop_down = array(
            'lib/mdc-select-ranges.php',
            'lib/mdc-select-suppliers.php',
        );
        $_OMP_input_tpl = 602;
        $_OMP_include_tpl = 50;
        $_OMP_page_title_lcl = 7;
        /* switches and scripts to set their value */
        $_OMP_monthly_switch = true;
        $_OMP_monthly_switch_locale = 614;
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
    // Please note: need 85 for genError() (see functions.php)
    $_OMP_lcl = OMP_LCL_DEL.'7, 600, 601';
    $_OMP_datatypes = array('text');
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['delete'] = 'DELETE FROM '.$_OMP_tables[$_OMP_tbl].
            ' WHERE '.$_OMP_tbl_fld['pkey'].' = ?';
    } else {
        $_OMP_sql['delete_pre'] = 'SELECT '.$_OMP_tbl_fld['pkey'].
            ' AS pkey, '.$_OMP_tbl_fld['description'].
            ' AS description FROM '.$_OMP_tables[$_OMP_tbl].
            ' WHERE '.$_OMP_tbl_fld['pkey'].' = ?';
        $_OMP_del_tpl = 52;
        $_OMP_page_title_lcl = 7;
    }
    require 'lib/del.php';
    break;
}
/**
* End delete record
*/
}
?>
