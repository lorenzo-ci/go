<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | OMP Version 1                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2022 Lorenzo Ciani                                |
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
// | Author: Lorenzo Ciani <lorenzo@ciani.it>                              |
// +----------------------------------------------------------------------+
//
// $Id: invoices.php,v 0.8 $
//
// Manage invoices records
//

require_once 'base.php';
require_once 'schemas/db-schema.php'; // Database schema
// Name of current master-table
$_OMP_tbl = 'invoices';
$_OMP_tbl_line = 'invoices_lines';
require_once 'schemas/banks-schema.php'; // Banks schema
require_once 'schemas/clients-schema.php'; // Clients schema
require_once 'schemas/suppliers-schema.php'; // Suppliers schema
require_once 'schemas/deliveries-schema.php'; // Deliveries schema
require_once 'schemas/invoices-schema.php'; // Invoices schema
// Table fields, length and keys
$_OMP_tbl_fld = $_OMP_invoices_fld;
$_OMP_fld_len = $_OMP_invoices_len;
$_OMP_tbl_key = $_OMP_invoices_key;
$_OMP_tbl_fld_line = $_OMP_invoices_lines_fld;
$_OMP_fld_len_line = $_OMP_invoices_lines_len;
$_OMP_tbl_line_key = $_OMP_invoices_lines_key;
require_once 'schemas/orders-schema.php'; // Orders schema
require_once 'schemas/products-schema.php'; // Products schema
// SQL for drop-down lists
if (!$_OMP_get['popup']) { require_once 'lib/dd-sql.php'; }

/**
 * SQL code
 */
$_OMP_sql['sort_default_sub'] = ' ORDER BY ol.'.
    $_OMP_orders_lines_fld['prod_pkey'].' ?';
$_OMP_sql['sort_record_sub'][0] = $_OMP_sql['sort_default_sub'];
$_OMP_sql['select'] = 'SELECT inv.'.
    $_OMP_tbl_fld['pkey'].' AS pkey, inv.'.
    $_OMP_tbl_fld['date'].' AS date, inv.'.
    $_OMP_tbl_fld['client_pkey'].' AS client_pkey, inv.'.
    $_OMP_tbl_fld['supplier_pkey'].' AS supplier_pkey, inv.'.
    $_OMP_tbl_fld['oi_pkey'].' AS oi_pkey, inv.'.
    $_OMP_tbl_fld['del_pkey'].' AS del_pkey, inv.'.
    $_OMP_tbl_fld['paymnt_date'].' AS paymnt_date, inv.'.
    $_OMP_tbl_fld['bank_pkey'].' AS bank_pkey, inv.'.
    $_OMP_tbl_fld['bank'].' AS bank, inv.'.
    $_OMP_tbl_fld['br_ref'].' AS br_ref, inv.'.
    $_OMP_tbl_fld['amount'].' AS amount, inv.'.
    $_OMP_tbl_fld['amount_eur'].' AS amount_eur, inv.'.
    $_OMP_tbl_fld['due_date'].' AS due_date, inv.'.
    $_OMP_tbl_fld['note'].' AS note, inv.'.
    $_OMP_tbl_fld['commission'].' AS commission, inv.'.
    $_OMP_tbl_fld['commission_eur'].' AS commission_eur, inv.'.
    $_OMP_tbl_fld['rim'].' AS remittance, cli.'.
    $_OMP_clients_fld['name'].' AS client_name, sup.'.
    $_OMP_suppliers_fld['name'].' AS supplier_name, oi.'.
    $_OMP_orders_fld['curr'].' AS curr, oi.'.
    $_OMP_orders_fld['um'].' AS um, bank.'.
    $_OMP_banks_fld['name'].' AS bank_name FROM '.
    $_OMP_tables[$_OMP_tbl].' AS inv LEFT JOIN '.
    $_OMP_tables['clients'].' AS cli USING ('.
    $_OMP_tbl_fld['client_pkey'].') LEFT JOIN '.
    $_OMP_tables['suppliers'].' AS sup USING ('.
    $_OMP_suppliers_fld['pkey'].') LEFT JOIN '.
    $_OMP_tables['invoices_lines'].' AS inv_l ON (inv_l.'.
    $_OMP_invoices_lines_fld['inv_pkey'].' = inv.'.
    $_OMP_invoices_fld['pkey'].' AND inv_l.'.
    $_OMP_invoices_lines_fld['date'].' = inv.'.
    $_OMP_invoices_fld['date'].' AND inv_l.'.
    $_OMP_invoices_lines_fld['supplier_pkey'].' = inv.'.
    $_OMP_invoices_fld['supplier_pkey'].') LEFT JOIN '.
    $_OMP_tables['deliveries_lines'].' AS dl USING ('.
    $_OMP_deliveries_lines_fld['pkey'].') LEFT JOIN '.
    $_OMP_tables['orders_lines'].' AS ol ON (ol.'.
    $_OMP_orders_lines_fld['pkey'].' = dl.'.
    $_OMP_deliveries_lines_fld['ol_pkey'].') LEFT JOIN '.
    $_OMP_tables['orders'].' AS oi ON (oi.'.
    $_OMP_orders_fld['pkey'].' = ol.'.
    $_OMP_orders_lines_fld['oi_pkey'].') LEFT JOIN '.
    $_OMP_tables['banks'].' AS bank ON (inv.'.
    $_OMP_tbl_fld['bank_pkey'].' = bank.'.
    $_OMP_banks_fld['pkey'].')';
$_OMP_sql['row'] = $_OMP_sql['select'].' WHERE inv.'.$_OMP_tbl_fld['pkey'].
    ' = ? AND inv.'.$_OMP_tbl_fld['date'].' = ? AND inv.'.
    $_OMP_tbl_fld['supplier_pkey'].' = ?';
$_OMP_sql['select_line'] = 'SELECT il.'.
    $_OMP_tbl_fld_line['dl_pkey'].' AS pkey, dl.'.
    $_OMP_deliveries_lines_fld['quantity'].' AS quantity, ol.'.
    $_OMP_orders_lines_fld['prod_pkey'].' AS prod_pkey, prod.'.
    $_OMP_products_fld['description'].' AS prod_label, ol.'.
    $_OMP_orders_lines_fld['price'].' AS price, ol.'.
    $_OMP_orders_lines_fld['discount'].' AS discount, oi.'.
    $_OMP_orders_fld['um'].' AS um FROM '.
    $_OMP_tables[$_OMP_tbl_line].' AS il LEFT JOIN '.
    $_OMP_tables['deliveries_lines'].' AS dl ON (il.'.
    $_OMP_tbl_fld_line['dl_pkey'].' = dl.'.
    $_OMP_deliveries_lines_fld['pkey'].') LEFT JOIN '.
    $_OMP_tables['orders_lines'].' AS ol ON (dl.'.
    $_OMP_deliveries_lines_fld['ol_pkey'].' = ol.'.
    $_OMP_orders_lines_fld['pkey'].') LEFT JOIN '.
    $_OMP_tables['products'].' AS prod ON (ol.'.
    $_OMP_orders_lines_fld['prod_pkey'].' = prod.'.
    $_OMP_products_fld['pkey'].') LEFT JOIN '.
    $_OMP_tables['orders'].' AS oi ON (ol.'.
    $_OMP_orders_lines_fld['oi_pkey'].' = oi.'.
    $_OMP_orders_fld['pkey'].')';
$_OMP_sql['row_line'] = $_OMP_sql['select_line'].' WHERE il.'.
    $_OMP_tbl_fld_line['inv_pkey'].' = ? AND '.
    $_OMP_tbl_fld_line['date'].' = ? AND il.'.
    $_OMP_tbl_fld_line['supplier_pkey'].' = ?';
/**
 * End of SQL code
 */
 
/**
* Function definitions
*/
/**
* Make changes to $_POST. If called by new-line, 
* insert new delivery-line, new invoice and new invoice-line
*
*/
function OMP_changePost()
{
    global $post_to_get, $transaction, $_OMP_get;
    if ($_OMP_get['action'] == 'editline') {
        $_POST['form']['txt_del_pkey'] = $post_to_get['pkey'];
    } elseif ($_OMP_get['action'] == 'newdetail') {
        $_POST['form']['txt_del_pkey'] = $_OMP_get['pkey'];
    }
    if ($_POST['form']['num_inv_pkey'] == '') { 
        unset($_POST['form']['num_inv_pkey']);
    }
    if ($_POST['form']['date'] == '') { unset($_POST['form']['date']); }
    if ($_OMP_get['action'] == 'newdetail' && 
        isset($_POST['form']['num_inv_pkey']) && 
        isset($_POST['form']['date'])) {
        $transaction = true;
    }
}

/**
* Process the array $GLOBALS['_OMP_rec']
*
*/
function OMP_makeVars()
{
    global $_OMP_LC, $_OMP_get, $_OMP_html, $_OMP_onload, $_OMP_rec,
        $_OMP_table_data, $_OMP_table_header, $_OMP_TPL;
    // Changed second param of OMP_genErr from 1 to 0 to avoid
    // logging-out if reading empty table invoices
    is_array($_OMP_rec) or OMP_genErr('Internal error','OMP_rec not array', 0);
    $_OMP_rec = array_map('OMP_htmlentities', $_OMP_rec);
    if ($_OMP_get['action'] == 'del') {
        $_OMP_table_header = array($_OMP_LC[1501], $_OMP_LC[703]);
        $_OMP_table_data = array($_OMP_rec['pkey'], $_OMP_rec['date']);
        return;
    } elseif ($_OMP_get['action'] == 'new') {
        $_OMP_rec['date'] = empty($_OMP_rec['date']) ? strftime('%F') : 
            $_OMP_rec['date'];
        $_OMP_rec['due_date'] = empty($_OMP_rec['due_date']) ? '' : 
            strftime('%F', strtotime($_OMP_rec['due_date']));
        $_OMP_rec['paymnt_date'] = empty($_OMP_rec['paymnt_date']) ? '' : 
            strftime('%F', strtotime($_OMP_rec['paymnt_date']));
        if (!empty($_OMP_rec['client_pkey'])) {
             // Form focus
            $_OMP_onload = 
                'onload="javascript:document.forms[0].
                    elements[5].focus();return true"';
        }
        if (!empty($_OMP_rec['supplier_pkey'])) {
             // Form focus
            $_OMP_onload = 
                'onload="javascript:document.forms[0].
                    elements[6].focus();return true"';
        }
        return;
    } elseif ($_OMP_get['action'] == 'edit') {
        $_OMP_rec['date'] = strftime('%F', strtotime($_OMP_rec['date']));
        $_OMP_rec['due_date'] = empty($_OMP_rec['due_date']) ? '' : 
            strftime('%F', strtotime($_OMP_rec['due_date']));
        $_OMP_rec['paymnt_date'] = empty($_OMP_rec['paymnt_date']) ? '' : 
            strftime('%F', strtotime($_OMP_rec['paymnt_date']));
        return;
    } else {
        $_OMP_rec['date'] = strftime('%x', strtotime($_OMP_rec['date']));
        $_OMP_rec['due_date'] = empty($_OMP_rec['due_date']) ? '' : 
            strftime('%x', strtotime($_OMP_rec['due_date']));
        $_OMP_rec['paymnt_date'] = empty($_OMP_rec['paymnt_date']) ? '' : 
            strftime('%x', strtotime($_OMP_rec['paymnt_date']));
    }
    if ($_OMP_get['action'] == 'newdetail' || 
        $_OMP_get['action'] == 'editline') { 
        return;
    }
    (empty($_OMP_rec['amount'])) ?
        $_OMP_rec['amount'] = 0 :
        $_OMP_rec['amount'] =
            number_format(
                $_OMP_rec['amount'],
                2,
                $_SESSION['dp'],
                $_SESSION['ts']
            );
    (empty($_OMP_rec['amount_eur'])) ?
        $_OMP_rec['amount_eur'] = 0 :
        $_OMP_rec['amount_eur'] =
            number_format(
                $_OMP_rec['amount_eur'],
                2,
                $_SESSION['dp'],
                $_SESSION['ts']
            );
    if ($_OMP_get['action'] == 'read') {
        if ($_OMP_get['list']) {
            $_OMP_html['rec0'] = $_OMP_rec['pkey'];
            $_OMP_html['rec1'] = $_OMP_rec['date'];
            $_OMP_html['rec2'] = $_OMP_rec['client_pkey'];
            $_OMP_html['rec3'] = $_OMP_rec['supplier_pkey'];
            $_OMP_html['label0'] = '';
            $_OMP_html['label1'] = '';
            $_OMP_html['label2'] = $_OMP_rec['client_name'];
            $_OMP_html['label3'] = $_OMP_rec['supplier_name'];
            /* mdc-table */
            $_OMP_html['cell1_numeric'] = ''; // or mdc-data-table__cell--numeric
            $_OMP_html['cell2_numeric'] = ''; // or mdc-data-table__cell--numeric
            $_OMP_html['cell3_numeric'] = ''; // or mdc-data-table__cell--numeric
            $_OMP_html['cell4_numeric'] = ''; // or mdc-data-table__cell--numeric
        } elseif (!$_OMP_get['popup']) {
            /* OMP_popLink is defined in base.php */
            $_OMP_rec['supplier_pkey'] = OMP_popLink($_OMP_TPL[9], OMP_PATH.
                'suppliers.php?'.
                OMP_link('filter=1&pkey='.
                urlencode(html_entity_decode($_OMP_rec['supplier_pkey']))),
                $_OMP_rec['supplier_pkey']);
            $_OMP_rec['oi_pkey'] = OMP_popLink($_OMP_TPL[9], OMP_PATH.
                'orders.php?'.
                OMP_link('filter=1&pkey='.
                urlencode(html_entity_decode($_OMP_rec['oi_pkey']))),
                $_OMP_rec['oi_pkey']);
            $_OMP_rec['del_pkey'] = OMP_popLink($_OMP_TPL[9], OMP_PATH.
                'deliveries.php?'.
                OMP_link('filter=1&pkey='.
                urlencode(html_entity_decode($_OMP_rec['del_pkey'])).
                '&client_pkey='.
                urlencode(html_entity_decode($_OMP_rec['client_pkey']))),
                $_OMP_rec['del_pkey']);
            $_OMP_rec['client_pkey'] = OMP_popLink($_OMP_TPL[9], OMP_PATH.
                'clients.php?'.
                OMP_link('filter=1&pkey='.
                urlencode(html_entity_decode($_OMP_rec['client_pkey']))),
                $_OMP_rec['client_pkey']);
            if (!empty($_OMP_rec['bank_pkey'])) {
                $_OMP_rec['bank_pkey'] =
                OMP_popLink($_OMP_TPL[9], OMP_PATH.
                    'banks.php?'.
                    OMP_link('filter=1&pkey='.
                    urlencode(html_entity_decode($_OMP_rec['bank_pkey']))),
                    $_OMP_rec['bank_pkey']
                );
            }
            (empty($_OMP_rec['commission'])) ?
                $_OMP_rec['commission'] = 0 :
                $_OMP_rec['commission'] =
                number_format(
                    $_OMP_rec['commission'],
                    2,
                    $_SESSION['dp'],
                    $_SESSION['ts']
                );
        }
    }
}

/**
* Process the array $GLOBALS['record_sf']
*
*/
function OMP_makeVarsLine($ts = false)
{
    global $_OMP_rec_sf, $_OMP_rec, $_OMP_TPL;
    $_OMP_rec_sf = array_map('OMP_htmlentities', $_OMP_rec_sf);
    $_OMP_rec_sf['um'] = ($_OMP_rec_sf['um'] == 0) ? 'Kg' : 'Lbs';
    if ($GLOBALS['_OMP_get']['action'] == 'read' 
        && !$GLOBALS['_OMP_get']['popup']) {
        $_OMP_rec_sf['prod_pkey'] = OMP_popLink($_OMP_TPL[9], OMP_PATH.
            'products.php?'.
            OMP_link('filter=1&pkey='.
            urlencode($_OMP_rec_sf['prod_pkey'])),
            $_OMP_rec_sf['prod_pkey']);
    }
    if (is_numeric($_OMP_rec_sf['quantity']) && 
        is_numeric($_OMP_rec_sf['price'])) {
        /* this should not happen but sometimes DettagliFatture
           points to non-existing DettagliConsegne */
        $_OMP_rec_sf['amount'] = 
            $_OMP_rec_sf['quantity'] * $_OMP_rec_sf['price'];
            $_OMP_rec_sf['amount'] = 
                $_OMP_rec_sf['amount'] * (1 - $_OMP_rec_sf['discount']);
            $ts = ($ts) ? OMP_DB_THOUSANDS_SEP: $_SESSION['ts'];
            $_OMP_rec_sf['price'] = 
                number_format($_OMP_rec_sf['price'], 2, $_SESSION['dp'], $ts);
            $_OMP_rec_sf['discount'] = 
                number_format($_OMP_rec_sf['discount'] * 100, 0, 
                $_SESSION['dp'], $_SESSION['ts']).'%';
            $_OMP_rec_sf['quantity'] = 
                number_format($_OMP_rec_sf['quantity'], 2, $_SESSION['dp'], $ts);
            $_OMP_rec_sf['amount'] = 
                number_format($_OMP_rec_sf['amount'], 2, $_SESSION['dp'], $ts);
    } else {
        $_OMP_rec_sf['quantity'] = $_OMP_rec_sf['price'] = 
            $_OMP_rec_sf['amount'] = $_OMP_rec_sf['discount'] = 0;
    }
/*     if ($GLOBALS['_OMP_get']['action'] == 'editline') { 
            $_OMP_rec_sf['del_pkey'] = $_OMP_rec['pkey'];
    }*/
}
/**
* Init new line variables
*
*/
function OMP_newLine()
{
    global $invoices_fields, $_OMP_db, $_OMP_rec, $_OMP_rec_sf, $_OMP_sql;
    $_OMP_rec_sf['del_pkey'] = $_OMP_rec['pkey'];
    $_OMP_rec_sf['date'] = '';
    if (!empty($_POST['form']['num_inv_pkey'])) {
        formCheckNum($_POST['form']['num_inv_pkey'], $invoices_fields['pkey']);
        $prepared = $_OMP_db->prepare($_OMP_sql['get_invoice'], array('text'));
        if (is_array($_OMP_rec_inv = 
            $prepared->execute(array($_POST['form']['num_inv_pkey'])))) {
            $_OMP_rec_sf['inv_pkey'] = $_POST['form']['num_inv_pkey'];
            $_OMP_rec_sf['date'] = 
                strftime('%F', strtotime($_OMP_rec_inv['date']));
            $GLOBALS['onload'] = 'onload="javascript:
            document.forms[0].elements[3].focus();return true"'; // Form focus
        } else {
            $_OMP_rec_sf['inv_pkey'] = $_POST['form']['num_inv_pkey'];
            $GLOBALS['onload'] = 'onload="javascript:
            document.forms[0].elements[2].focus();return true"'; // Form focus
        }
    } else {
        $_OMP_rec_sf['inv_pkey'] = '';
    }
}
/**
* End functions
*/
$_OMP_has_subform = true; // Process detail records
// We cannot add invoice-lines: use deliveries.php instead
$_OMP_cannot_add_line = true;
$_OMP_table_alias = 'inv.'; // See makeSql
$_OMP_sort_default_sub = '0'; // Sub record default sort
$_OMP_sort_type_default_sub = 0; // Sub record default sort order
$_OMP_headline = 1501;
switch ($_OMP_get['action']) {
/**
* Read record
*/
case 'read': {
    $_OMP_tpl = OMP_TPL_READ.'9, 65, 66, 67, 68, 1500, 1504, 1505';
    $_OMP_lcl = OMP_LCL_READ.'23, 39, 43, 52, 100, 106, 110, 504,
        600, 604, 703, 718, 801, 802, 900, 1500, 1501,
        1502, 1503, 1504, 1506, 1507';
    $_OMP_sql['sort_default'] = ' ORDER BY inv.'.$_OMP_tbl_fld['date'].
        ' ?, inv.'.$_OMP_tbl_fld['client_pkey'].', inv.'.
        $_OMP_tbl_fld['supplier_pkey'];
    $_OMP_sql['sort_record'][0] = $_OMP_sql['sort_list'][0] = ' ORDER BY inv.'.
        $_OMP_tbl_fld['pkey'].
        ' ?, inv.'.$_OMP_tbl_fld['date'].', inv.'.$_OMP_tbl_fld['client_pkey'].
        ', inv.'.$_OMP_tbl_fld['supplier_pkey'];
    $_OMP_sql['sort_record'][1] = $_OMP_sql['sort_list'][1] = 
        $_OMP_sql['sort_default'];
    $_OMP_sql['sort_record'][2] = $_OMP_sql['sort_list'][2] = ' ORDER BY inv.'.
        $_OMP_tbl_fld['client_pkey'].' ?, inv.'.
        $_OMP_tbl_fld['date'].', inv.'.$_OMP_tbl_fld['supplier_pkey'];
    $_OMP_sql['sort_record'][3] = $_OMP_sql['sort_list'][3] = ' ORDER BY inv.'.
        $_OMP_tbl_fld['supplier_pkey'].' ?, inv.'.
        $_OMP_tbl_fld['date'].', inv.'.$_OMP_tbl_fld['client_pkey'];
    $_OMP_sort_idx = array(1501, 703, 100, 504);
    if ($_OMP_get['list']) {
        $_OMP_list_tpl = 60;
        $_OMP_list_header_tpl = 65;
        $_OMP_list_rec_tpl = 66;
        /* mdc table */
        /* or mdc-data-table__header-cell--numeric" */
        $_OMP_html['header1_numeric'] = '';
        $_OMP_html['header2_numeric'] = '';
        $_OMP_html['header3_numeric'] = '';
        $_OMP_html['header4_numeric'] = '';
    } else {
        $_OMP_rec_tpl = 1500;
        $_OMP_subform_tpl = 67;
        $_OMP_sub_rec_tpl = 1505;
        $_OMP_sub_header_tpl = 1504;
        $_OMP_sub_sort_idx = array(600);
    }
    $_OMP_title = 1500;
    /* Master record default sort */
    $_OMP_sort['default'] = '1';
    /* Master record default sort order */
    $_OMP_sort['type'] = 1;
    require 'lib/read.php';
    break;
}
/**
* End read record
*/

/**
* Edit record
*/
case 'edit': {
    $_OMP_tpl = OMP_TPL_EDIT.'1502';
    $_OMP_lcl = OMP_LCL_EDIT.'100, 106, 110,
        504,  703, 801, 802, 900, 1500, 1501,
        1502, 1503, 1504';
    // Check if insert-button was pushed
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['update'] = 'UPDATE '.
            $_OMP_tables[$_OMP_tbl].' SET '.
            $_OMP_tbl_fld['pkey'].' = ?, '.
            $_OMP_tbl_fld['date'].' = ?, '.
            $_OMP_tbl_fld['supplier_pkey'].' = ?, '.
            $_OMP_tbl_fld['del_pkey'].' = ?, '.
            $_OMP_tbl_fld['paymnt_date'].' = ?, '.
            $_OMP_tbl_fld['bank_pkey'].' = ?, '.
            $_OMP_tbl_fld['bank'].' = ?, '.
            $_OMP_tbl_fld['br_ref'].' = ?, '.
            $_OMP_tbl_fld['amount'].' = ?,  '.
            $_OMP_tbl_fld['amount_eur'].' = ?,  '.
            $_OMP_tbl_fld['due_date'].' = ?, '.
            $_OMP_tbl_fld['note'].' = ? WHERE '.
            $_OMP_tbl_fld['pkey'].' = ? AND '.
            $_OMP_tbl_fld['date'].' = ? AND '.
            $_OMP_tbl_fld['supplier_pkey'].' = ?';
        $_OMP_datatypes = array(
            'text',     // pkey
            'date',     // date
            'text',     // del_pkey
            'date',     // paymnt_date
            'text',     // bank_pkey
            'text',     // bank
            'text',     // br_ref
            'decimal',  // amount
            'decimal',  // amount_eur
            'date',     // due_date
            'text',     // note
            'text',     // pkey
            'date',     // date
            'text');    // supplier_pkey
    } else {
        $_OMP_client_combo_disabled = true;
        $_OMP_supplier_combo_disabled = true;
        $_OMP_drop_down = array(
            'lib/mdc-select-banks.php',
            'lib/mdc-select-clients.php',
            'lib/mdc-select-suppliers.php'
        );
        $_OMP_oipkey_disabled = 'disabled';
        $_OMP_control_disabled = '';
        $_OMP_input_tpl = 1502;
        $_OMP_edit_tpl = 51;
        $_OMP_page_title_lcl = 1500;
        $_OMP_datatypes = array('text', 'date', 'text');
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
    $_OMP_tpl = OMP_TPL_FILTER.'1501';
    $_OMP_lcl = OMP_LCL_FILTER.'100, 106, 110,
        504, 703, 801, 802, 900, 1500,
        1501, 1502, 1503, 1504';
    $_OMP_drop_down = array(
        'lib/mdc-select-banks.php'
    );
    if ($_SESSION['cat'] == 0) {
        /* Filter for clients */
        // $_OMP_client_combo =
        // '<input disabled type="text" name="client_pkey" id="client_pkey" '.
        // 'size="10" value="'.htmlentities($_SESSION['id']).'" /> '.
        // '<input type="hidden"
        //     name="form[enc_client_pkey]" id="form[enc_client_pkey]" value="'.
        // htmlentities($_SESSION['id']).'" />';
        $_OMP_client_combo_disabled = true;
        $_OMP_drop_down[] = 'lib/mdc-select-suppliers.php';
        // Banks drop-down lists only banks for client
        $_OMP_rec['client_pkey'] = $_SESSION['id'];
    } elseif ($_SESSION['cat'] == 1) {
        /* Filter for suppliers */
        // $_OMP_supplier_combo =
        // '<input disabled type="text"
        //     name="supplier_pkey" id="supplier_pkey" '.
        // 'size="10" value="'.htmlentities($_SESSION['id']).'" /> '.
        // '<input type="hidden" name="form[enc_supplier_pkey]"
        //     id="form[enc_supplier_pkey]" value="'.
        // htmlentities($_SESSION['id']).'" />';
        $_OMP_supplier_combo_disabled = true;
        $_OMP_drop_down[] =  'lib/mdc-select-suppliers.php';
    } else {
        $_OMP_drop_down[] =  'lib/mdc-select-clients.php';
        $_OMP_drop_down[] =  'lib/mdc-select-suppliers.php';
    }
    $_OMP_page_title_lcl = 1500;
    $_OMP_include_tpl = 1501;
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
    $_OMP_tpl = OMP_TPL_NEW.'1502';
    $_OMP_lcl = OMP_LCL_NEW.'100, 106, 110,
        504, 703, 801, 802, 900, 1500, 1501,
        1502, 1503, 1504';
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['insert'] = 'INSERT INTO '.$_OMP_tables[$_OMP_tbl].
            ' ('.$_OMP_tbl_fld['pkey'].
            ', '.$_OMP_tbl_fld['date'].
            ', '.$_OMP_tbl_fld['client_pkey'].
            ', '.$_OMP_tbl_fld['supplier_pkey'].
            // ', '.$_OMP_tbl_fld['oi_pkey'].
            ', '.$_OMP_tbl_fld['del_pkey'].
            ', '.$_OMP_tbl_fld['amount'].
            ', '.$_OMP_tbl_fld['amount_eur'].
            ', '.$_OMP_tbl_fld['note'].
            ') VALUES (?, !, ?, ?, ?, !, !, !)';
            $_OMP_has_subform = false; // Don't add new detail records
    } else {
        $_OMP_drop_down = array(
            'lib/mdc-select-banks.php',
            'lib/mdc-select-clients.php',
            'lib/mdc-select-suppliers.php'
        );
        $_OMP_oipkey_disabled = '';
        $_OMP_control_disabled = 'disabled';
        $_OMP_bank_combo_disabled = true;
        $_OMP_input_tpl = 1502;
        $_OMP_include_tpl = 50;
        $_OMP_page_title_lcl = 1500;
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
    $_OMP_tpl = OMP_TPL_NEW.'800, 806, 807, 808, 809, 810';
    $_OMP_lcl = OMP_LCL_NEW.'23, 47, 48, 82, 100, 106, 703, 
        708, 717, 720, 800, 801, 802, 803, 804, 805, 1501';
    OMP_load();
//     checkId($_OMP_get['pkey'], $_OMP_get['hash_pkey']);
    // Main GET elements go in $getstring
    $getstring = makeGetstring(array_map('stripslashes', $_OMP_get));
    // Check if insert-button was pushed
    if (isset($_POST['insert_button']) && $_POST['insert_button'] == $_OMP_LC[39]) {
        $change_post = true;
// $_OMP_sql['insert_line'] = 'INSERT INTO '.$_OMP_tables[$_OMP_tbl_line].
// ' ('.$_OMP_tbl_fld_line['del_pkey'].', '.
//     $_OMP_tbl_fld_line['ol_pkey'].', '.$_OMP_tbl_fld_line['bales'].
// ', '.$_OMP_tbl_fld_line['quantity'].
//     ') VALUES (?, !, !, !)';
    } else {
        $_OMP_master_tpl = $_OMP_TPL[800];
        $_OMP_frame_tpl = $_OMP_TPL[810];
        $_OMP_input_tpl = $_OMP_TPL[806];
        $_OMP_header_tpl = $_OMP_TPL[807];
        $_OMP_rec_tpl = $_OMP_TPL[809];
        $sort_record[0] = $_OMP_LC[801];
        $sort_record[1] = $_OMP_LC[100];
        $sort_record[2] = $_OMP_LC[703];
        $sort_record[3] = $_OMP_LC[720];
        // Javascript form validation
        eval("\$_OMP_html['script']_subform = \"".$_OMP_TPL[808]."\";");
        $_OMP_html['page_title'] = $_OMP_LC[800].' - '.
            $_OMP_LC[39].' '.$_OMP_LC[23];
        $_OMP_drop_down = array('lib/dd-products-dl.php');
        $on_change_inv = 
            'onchange="javascript:this.form.submit();return true"';
        // TODO: Find a better way to manage this!!!
        $_OMP_rec['supplier_pkey'] = '';
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
    $_OMP_tpl = OMP_TPL_EDIT.'800, 806, 807, 808, 809, 810';
    $_OMP_lcl = OMP_LCL_EDIT.'23, 47, 48, 82, 100, 106, 
        703, 708, 717, 720, 800, 801, 802, 803, 804, 805, 1501';
    OMP_load();
    // Check if edit-button was pushed
    if (isset($_POST['insert_button']) && 
        $_POST['insert_button'] == $_OMP_LC[38]) {
        $change_post = true;
// $_OMP_sql['update_line'] = 'UPDATE '.$_OMP_tables[$_OMP_tbl_line].' SET '.$_OMP_tbl_fld_line['del_pkey'].' = ?, '.
//     $_OMP_tbl_fld_line['ol_pkey'].' = ?, '.$_OMP_tbl_fld_line['bales'].' = ?, '.$_OMP_tbl_fld_line['quantity'].' = ? WHERE '.
//     $_OMP_tbl_fld_line['pkey'].' = ?';
    } else {
        $_OMP_master_tpl = $_OMP_TPL[800];
        $_OMP_frame_tpl = $_OMP_TPL[810];
        $_OMP_input_tpl = $_OMP_TPL[806];
        $_OMP_header_tpl = $_OMP_TPL[807];
        $_OMP_rec_tpl = $_OMP_TPL[809];
        $sort_record[0] = $_OMP_LC[801];
        $sort_record[1] = $_OMP_LC[100];
        $sort_record[2] = $_OMP_LC[703];
        $sort_record[3] = $_OMP_LC[720];
        $_OMP_html['page_title'] = 
            $_OMP_LC[800].' - '.$_OMP_LC[33].' '.$_OMP_LC[23];
        $_OMP_drop_down = array('lib/dd-products-dl.php');
        $on_change_inv = 
            'onchange="javascript:this.form.submit();return true"';
        // Javascript form validation
        eval("\$_OMP_html['script']_subform = \"".$_OMP_TPL[808]."\";");
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
    $_OMP_lcl = OMP_LCL_DEL.'703, 1500, 1501';
    $_OMP_datatypes = array('text', 'date', 'text');
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['delete'] = 'DELETE FROM '.
            $_OMP_tables[$_OMP_tbl].' WHERE '.
            $_OMP_tbl_fld['pkey'].' = ? AND '.
            $_OMP_tbl_fld['date'].' = ? AND '.
            $_OMP_tbl_fld['supplier_pkey'].' = ?';
    } else {
        $_OMP_sql['delete_pre'] = 'SELECT '.$_OMP_tbl_fld['pkey'].
            ' AS pkey, '.$_OMP_tbl_fld['client_pkey'].
            ' AS client_pkey, '.$_OMP_tbl_fld['supplier_pkey'].
            ' AS supplier_pkey, '.$_OMP_tbl_fld['date'].
            ' AS date FROM '.$_OMP_tables[$_OMP_tbl].' WHERE '.
            $_OMP_tbl_fld['pkey'].' = ? AND '.$_OMP_tbl_fld['date'].
            ' = ? AND '.$_OMP_tbl_fld['supplier_pkey'].' = ?';
        $_OMP_del_tpl = 52;
        $_OMP_page_title_lcl = 1500;
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
    $_OMP_tpl = OMP_TPL_DEL.'70, 807, 809';
    $_OMP_lcl = OMP_LCL_DEL.'23, 703, 717, 800, 803, 804, 807, 811, 805, 1501';
    OMP_load();
    // Check if delete-button was pushed
    if (isset($_POST['insert_button']) && 
        $_POST['insert_button'] == $_OMP_LC[22]) {
// $_OMP_sql['delete_pre_line'] = $_OMP_sql['select_line'].' WHERE dl.'.$_OMP_tbl_fld_line['pkey'].' = ?';
// $_OMP_sql['delete_line'] = 'DELETE FROM '.$_OMP_tables[$_OMP_tbl_line].' WHERE '.$_OMP_tbl_fld_line['pkey'].' = ?';
    } else {
        $_OMP_header_tpl = $_OMP_TPL[807];
        $_OMP_rec_tpl = $_OMP_TPL[809];
        $_OMP_colspan = '5'; // Required by $_OMP_TPL[70]
        $_OMP_include_tpl = $_OMP_TPL[70];
        $_OMP_html['page_title'] = $_OMP_LC[800].' - '.$_OMP_LC[22].
            ' '.$_OMP_LC[23].$_OMP_LC[46];
    }
    require 'lib/del-line.php';
    break;
}
/**
* End delete order line record
*/
}
?>
