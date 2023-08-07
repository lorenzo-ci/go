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
// $Id: tbcs.php,v 0.9 $
//
// Manage TBCs records
//
require_once 'base.php';
require_once 'schemas/db-schema.php'; // Database schema
// Name of current master-table
$_OMP_tbl = 'tbcs';
$_OMP_tbl_line = 'tbcs_lines';
require_once 'schemas/clients-schema.php'; // Clients schema
require_once 'schemas/enduses-schema.php'; // Enduses schema
require_once 'schemas/suppliers-schema.php'; // Suppliers schema
require_once 'schemas/products-schema.php'; // Products schema
// SQL for drop-down lists
$_OMP_get['popup'] or require_once 'lib/dd-sql.php';
require_once 'schemas/orders-schema.php'; // Orders schema
require_once 'schemas/tbcs-schema.php'; // TBCs schema
// Table fields, length and keys
$_OMP_tbl_fld = $_OMP_tbcs_fld;
$_OMP_fld_len = $_OMP_tbcs_len;
$_OMP_tbl_key = $_OMP_tbcs_key;
$_OMP_tbl_fld_line = $_OMP_tbcs_lines_fld;
$_OMP_fld_len_line = $_OMP_tbcs_lines_len;
$_OMP_tbl_line_key = $_OMP_tbcs_lines_key;
/**
 * SQL code
 */
$_OMP_sql['sort_default_sub'] = ' ORDER BY tl.'.
    $_OMP_tbl_fld_line['plan'].', tl.'.
    $_OMP_tbl_fld_line['prod_pkey'].', tl.'.
    $_OMP_tbl_fld_line['eta'];
$_OMP_sql['sort_record_sub'][0] = ' ORDER BY tl.'.
    $_OMP_tbl_fld_line['plan'].' ?, tl.'.
    $_OMP_tbl_fld_line['prod_pkey'].', tl.'.
    $_OMP_tbl_fld_line['eta'];
$_OMP_sql['sort_record_sub'][1] = ' ORDER BY tl.'.
    $_OMP_tbl_fld_line['plan'].' ?, tl.'.
    $_OMP_tbl_fld_line['prod_pkey'].', tl.'.
    $_OMP_tbl_fld_line['eta'];
$_OMP_sql['sort_record_sub'][2] = ' ORDER BY tl.'.
    $_OMP_tbl_fld_line['plan'].' ?, tl.'.
    $_OMP_tbl_fld_line['prod_pkey'].', tl.'.
    $_OMP_tbl_fld_line['eta'];
$_OMP_sql['select'] = 'SELECT ti.'.
    $_OMP_tbl_fld['pkey'].' AS pkey, ti.'.
    $_OMP_tbl_fld['order_pkey'].' AS order_pkey, ti.'.
    $_OMP_tbl_fld['supplier_pkey'].' AS supplier_pkey, ti.'.
    $_OMP_tbl_fld['supplier_ref'].' AS supplier_ref, ti.'.
    $_OMP_tbl_fld['note'].' AS note, cli.'.
    $_OMP_clients_fld['name'].' AS client_name, sup.'.
    $_OMP_suppliers_fld['name'].' AS supplier_name FROM '.
    $_OMP_tables[$_OMP_tbl].' AS ti LEFT JOIN '.
    $_OMP_tables['clients'].' AS cli ON (ti.'.
    $_OMP_tbl_fld['pkey'].' = cli.'.
    $_OMP_clients_fld['pkey'].') LEFT JOIN '.
    $_OMP_tables['suppliers'].' AS sup ON (ti.'.
    $_OMP_tbl_fld['supplier_pkey'].' = sup.'.
    $_OMP_suppliers_fld['pkey'].')';
$_OMP_sql['row'] = $_OMP_sql['select'].
    ' WHERE ti.'.$_OMP_tbl_fld['pkey'].' = ?';
$_OMP_sql['select_line'] = 'SELECT tl.'.
    $_OMP_tbl_fld_line['tl_pkey'].' AS tl_pkey, tl.'.
    $_OMP_tbl_fld_line['prod_pkey'].' AS prod_pkey, tl.'.
    $_OMP_tbl_fld_line['year'].' AS year, tl.'.
    $_OMP_tbl_fld_line['month'].' AS month, tl.'.
    $_OMP_tbl_fld_line['plan'].' AS plan, tl.'.
    $_OMP_tbl_fld_line['quantity'].' AS quantity, tl.'.
    $_OMP_tbl_fld_line['price'].' AS price, tl.'.
    $_OMP_tbl_fld_line['price_eur'].' AS price_eur, tl.'.
    $_OMP_tbl_fld_line['note'].' AS note, tl.'.
    $_OMP_tbl_fld_line['eta'].' AS eta, pd.'.
    $_OMP_products_fld['description'].' AS prod_label FROM '.
    $_OMP_tables[$_OMP_tbl_line].' AS tl LEFT JOIN '.
    $_OMP_tables['products'].' AS pd ON (tl.'.
    $_OMP_tbl_fld_line['prod_pkey'].' = pd.'.
    $_OMP_products_fld['pkey'].')';
$_OMP_sql['row_line'] = $_OMP_sql['select_line'].' WHERE tl.'.
    $_OMP_tbl_fld_line['tl_pkey'].' = ?';
// $_OMP_sql['suppliers_combo'] = 'SELECT DISTINCT '.
//     $_OMP_tables['suppliers'].'.'.
//     $_OMP_suppliers_fld['pkey'].' AS pkey FROM '.
//     $_OMP_tables['suppliers'].' LEFT JOIN '.
//     $_OMP_tables['products'].' ON '.
//     $_OMP_tables['suppliers'].'.'.
//     $_OMP_suppliers_fld['pkey'].' = '.
//     $_OMP_tables['products'].'.'.
//     $_OMP_products_fld['supplier_pkey'].' WHERE '.
//     $_OMP_tables['suppliers'].'.'.
//     $_OMP_suppliers_fld['active'].' = true ORDER BY '.
//     $_OMP_tables['suppliers'].'.'.$_OMP_suppliers_fld['pkey'];
/**
 * End of SQL code
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
    global $_OMP_clients_fld, $_OMP_db, $_OMP_LC, $_OMP_get,
        $_OMP_html, $_OMP_onload, $_OMP_prefilter, $_OMP_rec,
        $_OMP_suppliers_fld, $_OMP_table_data,
        $_OMP_table_header, $_OMP_tables, $_OMP_tbl,
        $_OMP_tbl_fld, $_OMP_TPL, $tbc_pkey;
    $tbc_pkey = $_OMP_rec['pkey'];
    $_OMP_rec = array_map('OMP_htmlentities', $_OMP_rec);
    if ($_OMP_get['action'] == 'new') {
        // $_OMP_rec is set by initRecord() in new.php
        (empty($_OMP_rec['pkey'])) or $_OMP_onload = 
            'onload="javascript:document.
                forms[0].elements[1].focus();return true"';
        $_OMP_rec['supplier_pkey'] = 'WELLMAN LTD'; // Default supplier
        return;
    } elseif ($_OMP_get['action'] == 'del') {
        $_OMP_table_header = array($_OMP_LC[100], $_OMP_LC[504]);
        $_OMP_table_data =
            array($_OMP_rec['pkey'], $_OMP_rec['supplier_pkey']);
        return;
    }
    if ($_OMP_get['action'] == 'read') {
        if (!$_OMP_get['popup'] && !$_OMP_get['list']) {
            /* OMP_popLink is defined in base.php */
            $_OMP_html['pkey'] =
                OMP_popLink($_OMP_TPL[9], OMP_PATH.
                    'clients.php?'.OMP_link('filter=1&pkey='.
                    urlencode(html_entity_decode(
                    $_OMP_rec['pkey']))),
                    $_OMP_rec['pkey']);
            $_OMP_html['supplier_pkey'] =
                OMP_popLink($_OMP_TPL[9], OMP_PATH.
                    'suppliers.php?'.OMP_link('filter=1&pkey='.
                    urlencode(html_entity_decode(
                    $_OMP_rec['supplier_pkey']))),
                    $_OMP_rec['supplier_pkey']);
        }
        if ($_OMP_get['list']) {
            $_OMP_html['rec0'] = $_OMP_rec['pkey'];
            $_OMP_html['rec1'] = nl2br($_OMP_rec['note'], false);
            $_OMP_html['label0'] = $_OMP_rec['client_name'];
            $_OMP_html['label1'] = '';
            /* mdc-table */
            /* or mdc-data-table__cell--numeric */
            $_OMP_html['cell1_numeric'] = '';
            $_OMP_html['cell2_numeric'] = '';
        } else {
            $_OMP_rec['note'] = nl2br($_OMP_rec['note'], false);
        }
    } else {
        $_OMP_html['pkey'] = $_OMP_rec['pkey'];
        $_OMP_html['supplier_pkey'] = $_OMP_rec['supplier_pkey'];
    }
}

/**
* Process the array $GLOBALS['record_sf']
*
*/
function OMP_makeVarsLine($ts = false)
{
    global $_OMP_get, $_OMP_html, $_OMP_rec_sf, $_OMP_TPL;
    $_OMP_rec_sf = array_map('OMP_htmlentities', $_OMP_rec_sf);
    $ts = ($ts) ? OMP_DB_THOUSANDS_SEP: $_SESSION['ts'];
    if ($_OMP_get['action'] == 'read' && !$_OMP_get['popup']) {
        $_OMP_html['prod_pkey'] =
            /* OMP_popLink is defined in base.php */
            OMP_popLink($_OMP_TPL[9], OMP_PATH.'products.php?'.
                OMP_link(
                    'filter=1&pkey='.
                    $_OMP_rec_sf['prod_pkey']
                ),
                $_OMP_rec_sf['prod_pkey']
            );
    }
    $_OMP_rec_sf['quantity'] = 
        number_format($_OMP_rec_sf['quantity'], 2, $_SESSION['dp'], $ts);
    $_OMP_rec_sf['price'] = 
        number_format($_OMP_rec_sf['price'], 2, $_SESSION['dp'], $ts);
    /* price_eur can be NULL */
    if (!empty($_OMP_rec_sf['price_eur'])) {
        $_OMP_rec_sf['price_eur'] =
        number_format($_OMP_rec_sf['price_eur'], 2, $_SESSION['dp'], 
            $_SESSION['ts']);
    }
    $_OMP_rec_sf['production'] = 
	    strftime('%B %Y', 
            mktime(0, 0, 0, $_OMP_rec_sf['month'], 1, $_OMP_rec_sf['year']));
    (!$ts) or $_OMP_rec_sf['eta'] = // Check if this is the line to edit
	    ($_OMP_rec_sf['eta'] == '') ? '' : 
            strftime('%x', strtotime($_OMP_rec_sf['eta']));
}
/**
* Init new line variables
*
*/
function OMP_newLine()
{
    global $_OMP_db, $_OMP_get, $_OMP_tbcs_fld, $_OMP_tbcs_lines_fld, 
        $_OMP_products_fld, $_OMP_rec, $_OMP_rec_sf, $_OMP_tables, 
        $_OMP_tbl, $_OMP_tbl_fld, $_OMP_tbl_fld_line, $_OMP_tbl_line, 
        $_OMP_orders_fld, $_OMP_orders_lines_fld;
    !empty($_OMP_rec_sf['month']) or $_OMP_rec_sf['month'] = strftime('%m');
    !empty($_OMP_rec_sf['year']) or $_OMP_rec_sf['year'] = strftime('%Y');
    !empty($_OMP_rec_sf['eta']) or $_OMP_rec_sf['eta'] = strftime('%F');
    $_OMP_rec_sf['plan'] = $_OMP_rec_sf['year']."-".
        str_pad($_OMP_rec_sf['month'], 2, "0", STR_PAD_LEFT);
    if ($_OMP_get['action'] == 'editline') {
        return;
    }
    if (!empty($_OMP_rec_sf['prod_pkey'])) {
        $sql_get_last_price = 'SELECT ol.'.
            $_OMP_tbcs_lines_fld['price'].' AS price, '.
            $_OMP_tbl_fld_line['quantity'].' AS quantity FROM '.
            $_OMP_tables['orders_lines'].' AS ol LEFT JOIN '.
            $_OMP_tables['orders'].' AS oi ON (ol.'.
            $_OMP_orders_lines_fld['oi_pkey'].' = oi.'.
            $_OMP_orders_fld['pkey'].') WHERE ol.'.
            $_OMP_orders_lines_fld['prod_pkey'].' = '.
            OMP_db_quote($_OMP_rec_sf['prod_pkey']).' AND oi.'.
            $_OMP_orders_fld['client_pkey'].' = '.
            OMP_db_quote($_OMP_rec['pkey']).' AND oi.'.
            $_OMP_orders_fld['cancelled'].' = \'No\' ORDER BY oi.'.
            $_OMP_orders_fld['date'].' DESC LIMIT 1';
        $_OMP_rec_price = 
            $_OMP_db->queryRow($sql_get_last_price);
        if (is_array($_OMP_rec_price)) {
                $_OMP_rec_sf['price'] = $_OMP_rec_price['price'];
                $_OMP_rec_sf['quantity'] = 
                    sprintf(OMP_QTY_F, $_OMP_rec_price['quantity']);
        } else {
            $sql_get_last_price = 'SELECT '.
                $_OMP_products_fld['price'].' AS price FROM '.
                $_OMP_tables['products'].' WHERE '.
                $_OMP_products_fld['pkey'].' = '.
                OMP_db_quote($_OMP_rec_sf['prod_pkey']);
            $_OMP_rec_price = $_OMP_db->queryRow($sql_get_last_price);
            if (is_array($_OMP_rec_price)) {
                    $_OMP_rec_sf['price'] = $_OMP_rec_price['price'];
            } else { // This should not happen...
                $_OMP_rec_sf['price'] = '';
            }
            $_OMP_rec_sf['quantity'] = '';
        }
        $_OMP_rec_sf['price'] = 
            sprintf(OMP_PRICE_F, $_OMP_rec_sf['price']);
    }
    !empty($_OMP_rec_sf['price']) or $_OMP_rec_sf['price'] = 0;
    !empty($_OMP_rec_sf['quantity']) or $_OMP_rec_sf['quantity'] = 0;
}

/**
* Make changes to $_POST
*
*/
function OMP_changePost()
{
    $ym = explode("-", $_POST['form']['txt_plan']);
    $_POST['form']['txt_year'] = $ym[0];
    $_POST['form']['txt_month'] = $ym[1];
    $_POST['form']['txt_plan'] .= '-01';
}
/**
* End functions
*/
$_OMP_has_subform = true; // Process detail records
$_OMP_table_alias = 'ti.'; // See makeSql
$_OMP_sort_default_sub = '0'; // Sub record default sort
$_OMP_sort_type_default_sub = 0; // Sub record default sort order
switch ($_OMP_get['action']) {
/**
* Read record
*/
case 'read': {
    $_OMP_tpl = OMP_TPL_READ.'16, 61, 62, 67, 68, 721,
        1600, 1609, 1610';
    $_OMP_lcl = OMP_LCL_READ.'23, 27, 39, 42, 43, 94, 
        98,99, 100, 106, 110, 504, 600, 700, 703, 716,
        717, 718, 719, 720';
    $_OMP_sql['sort_default'] = ' ORDER BY ti.'.
        $_OMP_tbl_fld['pkey'].' ?, ti.'.
        $_OMP_tbl_fld['note'];
    $_OMP_sql['sort_record'][0] = 
        $_OMP_sql['sort_list'][0] =
        $_OMP_sql['sort_default'];
    $_OMP_sql['sort_record'][1] =
        $_OMP_sql['sort_list'][1] = ' ORDER BY ti.'.
        $_OMP_tbl_fld['note'].' ?';
    // $_OMP_sort_idx contains the keys for
    // $_OMP_sort_list as keys
    // and the keys for $_OMP_LC as values
    // $_OMP_sort_list will be created in in read.php
    $_OMP_sort_idx = array(100, 106);
    /* not list put here on 17/01/23 to avoid errors */
    /* probably regarding the list parameter */
    $_OMP_sub_rec_tpl = 1610;
    $_OMP_sub_header_tpl = 1609;
    $_OMP_subform_tpl = 67;
    $_OMP_rec_tpl = 1600;
    if ($_OMP_get['list']) {
        $_OMP_list_tpl = 60;
        $_OMP_list_header_tpl = 61;
        $_OMP_list_rec_tpl = 62;
        $_OMP_list_wrapper ="wrapper2";
        /* mdc table */
        $_OMP_html['header1_numeric'] = ''; // or mdc-data-table__header-cell--numeric"
        $_OMP_html['header2_numeric'] = ''; // or mdc-data-table__header-cell--numeric"
    } else {
        $_OMP_headline = 27;
        $_OMP_rec_tpl = 1600;
        $_OMP_admin_tpl = 68;
        $_OMP_subform_tpl = 67;
        $_OMP_sub_rec_tpl = 1610;
        // See above for $_OMP_sort_idx
        $_OMP_sub_sort_idx = array(600, 716, 720);
    }
    $_OMP_opt_buttons_tpl = 15;
    $tbc_pkey = true;
    $_OMP_get['sort_type'] = 0;
//     }
    $_OMP_title = 27;
    $_OMP_sort['default'] = '0'; // Master record default sort
    $_OMP_sort['type'] = 1; // Master record default sort order
    $_OMP_subform_wrapper = 'wrapper7';
    require 'lib/read.php';
    break;
}
/**
* End read record
*/

/**
* Filter form
*/
case 'filter': {
    $_OMP_tpl = OMP_TPL_FILTER.'1601';
    $_OMP_lcl = OMP_LCL_FILTER.'27, 100, 106, 504, 714';
    $_OMP_drop_down = array(); // Eliminare?
    if ($_SESSION['cat'] == 0) { // Filter for clients
        $_OMP_client_combo = 
            '<input disabled type="text" name="pkey" id="pkey" 
            size="10" value="'.
            htmlentities($_SESSION['id']).'" /> 
            <input type="hidden" name="form[enc_pkey]" 
            id="form[enc_pkey]" value="'.
            htmlentities($_SESSION['id']).'" />';
    }
    $_OMP_drop_down[] = 'lib/mdc-select-clients.php';
    $_OMP_page_title_lcl = 27;
    $_OMP_include_tpl = 1601;
    require 'lib/filter.php';
    break;
}
/**
* End filter form
*/

/**
* Edit record
*/
case 'edit': {
    $_OMP_tpl = OMP_TPL_EDIT.'1611';
    $_OMP_lcl = OMP_LCL_EDIT.'27, 98, 99, 100, 103, 105, 106, 110, 
        116, 118, 504, 703';
    // Check if insert-button was pushed
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['update'] = 'UPDATE '.$_OMP_tables[$_OMP_tbl].' SET '.
            $_OMP_tbl_fld['note'].' = ? WHERE '.
            $_OMP_tbl_fld['pkey'].' = ?';
        $_OMP_datatypes = array('text', 'text');
    } else {
        $_OMP_datatypes = array('text');
        $_OMP_sql['clients_combo'] = 'SELECT '.
            $_OMP_clients_fld['pkey'].' AS pkey, active FROM '.$_OMP_tables['clients'].
            ' ORDER BY '.$_OMP_clients_fld['pkey'];
        $_OMP_drop_down = array('lib/mdc-select-clients.php');
        $_OMP_client_combo_disabled = true;
        $_OMP_supplier_combo = 'WELLMAN LTD';
        $_OMP_input_tpl = 1611;
        $_OMP_edit_tpl = 51;
        $_OMP_page_title_lcl = 27;
    }
    require 'lib/edit.php';
    break;
}
/**
* End edit record
*/

/**
* New record
*/
case 'new': {
    $_OMP_tpl = OMP_TPL_NEW.'1602';
    $_OMP_lcl = OMP_LCL_NEW.'27, 100, 106, 504';
    // Check if insert-button was pushed
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['insert'] = 'INSERT INTO '.$_OMP_tables[$_OMP_tbl].
            ' ('.$_OMP_tbl_fld['pkey'].', '.$_OMP_tbl_fld['supplier_pkey'].
            ', '.$_OMP_tbl_fld['note'].', '.$_OMP_tbl_fld['user'].
            ') VALUES (?, ?, ?, ?)';
        $_OMP_datatypes = array('text', 'text', 'text', 'text');
    } else {
        $_OMP_drop_down = array('lib/mdc-select-clients.php');
        $_OMP_onchange = false;
        $_OMP_input_tpl = 1602;
        $_OMP_include_tpl = 50;
        $_OMP_page_title_lcl = 27;
    }
    require 'lib/new.php';
    break;
}
/**
* End new record
*/

/**
* New line record
*/
case 'newdetail': {
    $_OMP_tpl = OMP_TPL_NEW.' 1600, 1604, 1605, 1606, 1608';
    $_OMP_lcl = OMP_LCL_NEW.'23, 27, 47, 48, 82, 84, 94,
        98, 99, 100, 106, 504, 600, 604, 700, 703, 716, 
        717, 719, 720';
    OMP_load();
    $_OMP_html['include'] = $_OMP_html['page_title'] =
        $_OMP_html['browser_title'] = $_OMP_html['subform'] =
        $_OMP_html['input'] = '';
    // Check if insert-button was pushed
    if (isset($_POST['insert_button']) && 
        $_POST['insert_button'] == $_OMP_LC[39]) {
        // $_OMP_html = '';
        $_OMP_change_post = true;
        $_OMP_sql['insert_line'] = 'INSERT INTO '.
            $_OMP_tables[$_OMP_tbl_line].' ('.
            $_OMP_tbl_fld_line['tl_pkey'].', '.     // 1, tl_pkey
            $_OMP_tbl_fld_line['prod_pkey'].', '.   // 2, prod_pkey
            $_OMP_tbl_fld_line['year'].', '.        // 3, year
            $_OMP_tbl_fld_line['month'].', '.       // 4, month
            $_OMP_tbl_fld_line['plan'].', '.        // 5, plan
            $_OMP_tbl_fld_line['quantity'].', '.    // 6, quantity
            $_OMP_tbl_fld_line['price'].', '.       // 7, price
            $_OMP_tbl_fld_line['note'].', '.        // 8, note
            $_OMP_tbl_fld_line['eta'].              // 9, eta
            ') VALUES ('.
            '?, '.  // 1, tl_pkey
            '?, '.  // 2, prod_pkey
            '?, '.  // 3, year
            '?, '.  // 4, month
            '?, '.  // 5, plan
            '?, '.  // 6, quantity
            '?, '.  // 7, price
            '?, '.  // 8, note
            '?)';   // 9, eta
        $_OMP_datatypes = array(
            'text',     // 1, tl_pkey
            'text',     // 2, prod_pkey
            'integer',  // 3, year
            'integer',  // 4, month
            'date',     // 5, plan
            'decimal',  // 6, quantity
            'decimal',  // 7, price
            'text',     // 8, note
            'date');    // 9, eta
    } else {
        $_OMP_master_tpl = $_OMP_TPL[1600];
        $_OMP_frame_tpl = $_OMP_TPL[1608];
        $_OMP_input_tpl = $_OMP_TPL[1604];
        $_OMP_header_tpl = $_OMP_TPL[1605];
        $_OMP_rec_tpl = $_OMP_TPL[1606];
        $sort_record[0] = $_OMP_LC[700];
        $sort_record[1] = $_OMP_LC[100];
        $sort_record[2] = $_OMP_LC[504];
        $sort_record[3] = $_OMP_LC[703];
        $_OMP_page_title_lcl = 27;
        $_OMP_html['page_title'] =
            $_OMP_LC[27].' - '.
            $_OMP_LC[39].' '.
            $_OMP_LC[23];
        $_OMP_drop_down = array(
            'lib/dd-products.php', 
        );
        $_OMP_onchange = true;
        $_OMP_noitmes_msg = $_OMP_LC[47];
    }
    require 'lib/new-line.php';
    break;
}
/**
* End new line record
*/

/**
* Edit line record
*/
case 'editline': {
    $_OMP_tpl = OMP_TPL_EDIT.'1600, 1604, 1605, 1606, 1608';
    $_OMP_lcl = OMP_LCL_EDIT.'23, 27, 82, 94, 98, 99, 100, 
        105, 106, 504, 600, 604, 700, 703, 716, 717, 719, 720';
    OMP_load();
    // Check if edit-button was pushed
    if (isset($_POST['insert_button']) && 
        $_POST['insert_button'] == $_OMP_LC[38]) {
        $_OMP_change_post = true;
        $tbc_pkey = true;
        $_OMP_sql['update_line'] = 'UPDATE '.
            $_OMP_tables[$_OMP_tbl_line].' SET '.
            $_OMP_tbl_fld_line['prod_pkey'].' = ?, '.       // 1, prod_pkey
            $_OMP_tbl_fld_line['year'].' = ?, '.            // 2, year
            $_OMP_tbl_fld_line['month'].' = ?, '.           // 3, month
            $_OMP_tbl_fld_line['plan'].' = ?, '.            // 4, plan
            $_OMP_tbl_fld_line['quantity'].' = ?, '.        // 5, quantity
            $_OMP_tbl_fld_line['price'].' = ?, '.           // 6, price
            $_OMP_tbl_fld_line['note'].' = ?, '.            // 7, note
            $_OMP_tbl_fld_line['eta'].' = ? WHERE '.        // 8, eta
            $_OMP_tbl_fld_line['tl_pkey'].' = ? AND '.      // 9, tl_pley
            $_OMP_tbl_fld_line['prod_pkey'].' = ? AND '.    // 10, prod_pkey
            $_OMP_tbl_fld_line['plan'].' = ? AND '.         // 11, plan
            $_OMP_tbl_fld_line['eta'].' = ?';               // 12, eta
        $_OMP_datatypes = array(
            'text',     // 1, prod_pkey
            'integer',  // 2, year
            'integer',  // 3, month
            'date',     // 4, plan
            'decimal',  // 5, quantity
            'decimal',  // 6, price
            'text',     // 7, note
            'date',     // 8, eta
            'text',     // 9, tl_pley
            'text',     // 10, prod_pkey
            'date',     // 11, plan
            'date');    // 12, eta
    } else {
        $_OMP_master_tpl = $_OMP_TPL[1600];
        $_OMP_frame_tpl = $_OMP_TPL[1608];
        $_OMP_input_tpl = $_OMP_TPL[1604];
        $_OMP_header_tpl = $_OMP_TPL[1605];
        $_OMP_rec_tpl = $_OMP_TPL[1606];
        $sort_record[0] = $_OMP_LC[700];
        $sort_record[1] = $_OMP_LC[100];
        $sort_record[2] = $_OMP_LC[504];
        $sort_record[3] = $_OMP_LC[703];
        $_OMP_html['page_title'] = $_OMP_LC[27].' - '.
            $_OMP_LC[33].' '.$_OMP_LC[23];
        $_OMP_drop_down = array(
            'lib/dd-products.php', 
        );
    }
    require 'lib/edit-line.php';
    break;
}
/**
* End edit line record
*/

/**
* Delete record
*/
case 'del': {
    $_OMP_tpl = OMP_TPL_DEL.'52';
    $_OMP_lcl = OMP_LCL_DEL.'27, 100, 504, 715';
    $_OMP_datatypes = array('text');
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['delete'] = 'DELETE FROM '.$_OMP_tables[$_OMP_tbl].
            ' WHERE '.$_OMP_tbl_fld['pkey'].' = ?';
    } else {
        $_OMP_sql['delete_pre'] = 'SELECT ti.'.$_OMP_tbl_fld['pkey'].
            ' AS pkey, cli.'.$_OMP_clients_fld['name'].
            ' AS client_name, ti.'.$_OMP_tbl_fld['supplier_pkey'].
            ' AS supplier_pkey, sup.'.$_OMP_suppliers_fld['name'].
            ' AS supplier_name FROM '.$_OMP_tables[$_OMP_tbl].
            ' AS ti LEFT JOIN '.$_OMP_tables['clients'].
            ' AS cli ON (ti.'.$_OMP_tbl_fld['pkey'].
            ' = cli.'.$_OMP_clients_fld['pkey'].') LEFT JOIN '.
            $_OMP_tables['suppliers'].' AS sup ON (ti.'.
            $_OMP_tbl_fld['supplier_pkey'].
            ' = sup.'.$_OMP_suppliers_fld['pkey'].
            ') WHERE ti.'.$_OMP_tbl_fld['pkey'].' = ?';
            // Please note $_OMP_LC[91] contains $_OMP_LC[715]
            // for the word 'Cancelled'
        $_OMP_del_tpl = 52;
        $_OMP_page_title_lcl = 27;
    }
    require 'lib/del.php';
    break;
}
/**
* End delete record
*/

/**
* Delete line record
*/
case 'delline': {
    $_OMP_tpl = OMP_TPL_DEL.'70, 1605, 1606, 1609, 1612';
    $_OMP_lcl = OMP_LCL_DEL.'23, 27, 106, 600, 716, 717, 719, 720';
    OMP_load();
    // Check if delete-button was pushed
    if (isset($_POST['insert_button']) && 
        $_POST['insert_button'] == $_OMP_LC[22]) {
        $_OMP_sql['delete_line'] = 'DELETE FROM '.
            $_OMP_tables[$_OMP_tbl_line].' WHERE '.
            $_OMP_tbl_fld_line['tl_pkey'].' = ? AND '.
            $_OMP_tbl_fld_line['prod_pkey'].' = ? AND '.
            $_OMP_tbl_fld_line['plan'].' = ? AND '.
            $_OMP_tbl_fld_line['eta'].' = ?';
        $_OMP_datatypes = array('text', 'text', 'date', 'date');
    } else {
        $_OMP_header_tpl = 1605;
        $_OMP_sub_header_tpl = 1605;
        $_OMP_rec_tpl = 1606;
        $_OMP_colspan = '7'; // Required by $_OMP_TPL[70]
        $_OMP_include_tpl = 1612;
        $_OMP_html['page_title'] = $_OMP_LC[27].' - '.
            $_OMP_LC[22].' '.$_OMP_LC[23];
            /*.$_OMP_LC[46];*/
        $_OMP_sql['delete_pre_line'] = $_OMP_sql['select_line'].' WHERE '.
            $_OMP_tbl_fld_line['tl_pkey'].' = ? AND tl.'.
            $_OMP_tbl_fld_line['prod_pkey'].' = ? AND '.
            $_OMP_tbl_fld_line['plan'].' = ? AND '.
            $_OMP_tbl_fld_line['eta'].' = ?';
        $_OMP_datatypes = array('text', 'text', 'date', 'date');
        $tbc_pkey = true;
        $_OMP_headline = 27;
    }
    require 'lib/del-line.php';
    break;
}
/**
* End delete line record
*/
}
?>
