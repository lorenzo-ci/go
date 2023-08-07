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
// $Id: deliveries.php,v 0.8 $
//
// Manage deliveries records
//

require_once 'base.php';
require_once 'schemas/db-schema.php'; // Database schema
// Name of current master-table
$_OMP_tbl = 'deliveries';
$_OMP_tbl_line = 'deliveries_lines';
require_once 'schemas/clients-schema.php'; // Clients schema
require_once 'schemas/deliveries-schema.php'; // Deliveries schema
// Table fields, length and keys
$_OMP_tbl_fld = $_OMP_deliveries_fld;
$_OMP_fld_len = $_OMP_deliveries_len;
$_OMP_tbl_key = $_OMP_deliveries_key;
$_OMP_tbl_fld_line = $_OMP_deliveries_lines_fld;
$_OMP_fld_len_line = $_OMP_deliveries_lines_len;
$_OMP_tbl_line_key = $_OMP_deliveries_lines_key;
require_once 'schemas/invoices-schema.php'; // Invoices schema
require_once 'schemas/orders-schema.php'; // Orders schema
// SQL for drop-down lists
if (empty($_OMP_get['popup'])) { 
    // This is required by dd-sql.php
    unset($_OMP_suppliers_fld, $_OMP_products_fld);
    require_once 'lib/dd-sql.php';
}
require_once 'schemas/products-schema.php'; // Products schema
/**
 * SQL code
 */
$_OMP_sql['sort_default_sub'] = ' ORDER BY ol.'.
    $_OMP_orders_lines_fld['prod_pkey'].' ?, ol.'.
    $_OMP_orders_lines_fld['eta'];
$_OMP_sql['sort_record_sub'][0] = $_OMP_sql['sort_default_sub'];
$_OMP_sql['sort_record_sub'][1] = ' ORDER BY ol.'.
    $_OMP_orders_lines_fld['oi_pkey'].' ?, ol.'.
    $_OMP_orders_lines_fld['prod_pkey'].', ol.'.
    $_OMP_orders_lines_fld['eta'];
$_OMP_sql['select'] = 'SELECT del.'.
    $_OMP_tbl_fld['pkey'].' AS pkey, del.'.
    $_OMP_tbl_fld['client_pkey'].' AS client_pkey, del.'.
    $_OMP_tbl_fld['date'].' AS date, del.'.
    $_OMP_tbl_fld['origin'].' AS origin, del.'.
    $_OMP_tbl_fld['eta'].' AS eta, del.'.
    $_OMP_tbl_fld['destination'].' AS destination, del.'.
    $_OMP_tbl_fld['truck'].' AS truck, del.'.
    $_OMP_tbl_fld['note'].' AS note, cli.'.
    $_OMP_clients_fld['name'].' AS client_name FROM '.
    $_OMP_tables[$_OMP_tbl].' AS del LEFT JOIN '.
    $_OMP_tables['clients'].' AS cli ON (del.'.
    $_OMP_tbl_fld['client_pkey'].' = cli.'.
    $_OMP_clients_fld['pkey'].')';
$_OMP_sql['select_for_sup'] = 'SELECT del.'.
    $_OMP_tbl_fld['pkey'].' AS pkey, del.'.
    $_OMP_tbl_fld['client_pkey'].' AS client_pkey, del.'.
    $_OMP_tbl_fld['date'].' AS date, del.'.
    $_OMP_tbl_fld['origin'].' AS origin, del.'.
    $_OMP_tbl_fld['eta'].' AS eta, del.'.
    $_OMP_tbl_fld['destination'].' AS destination, del.'.
    $_OMP_tbl_fld['truck'].' AS truck, del.'.
    $_OMP_tbl_fld['note'].' AS note, cli.'.
    $_OMP_clients_fld['name'].' AS client_name, oi.'.
    $_OMP_orders_fld['supplier_pkey'].' AS supplier_pkey FROM ';
$_OMP_sql['select_for_sup_from'] = $_OMP_tables[$_OMP_tbl].
    ' AS del LEFT JOIN '.$_OMP_tables['clients'].
    ' AS cli ON (del.'.$_OMP_tbl_fld['client_pkey'].' = cli.'.
    $_OMP_clients_fld['pkey'].') LEFT JOIN '.$_OMP_tables[$_OMP_tbl_line].
    ' AS dl ON (dl.'.$_OMP_tbl_fld_line['del_pkey'].
    ' = del.'.$_OMP_tbl_fld['pkey'].') LEFT JOIN '.
    $_OMP_tables['orders_lines'].
    ' AS ol ON (ol.'.$_OMP_orders_lines_fld['pkey'].' = dl.'.
    $_OMP_tbl_fld_line['ol_pkey'].') LEFT JOIN '.
    $_OMP_tables['orders'].' AS oi ON (oi.'.$_OMP_orders_fld['pkey'].
    ' = ol.'.$_OMP_orders_lines_fld['oi_pkey'].')';
if ($_SESSION['cat'] == 1) {
    $_OMP_sql['select'] = $_OMP_sql['select_for_sup'].
        $_OMP_sql['select_for_sup_from'];
}
$_OMP_sql['row'] = $_OMP_sql['select'].' WHERE del.'.
    $_OMP_tbl_fld['pkey'].' = ? AND del.'.
    $_OMP_tbl_fld['client_pkey'].' = ?';
$_OMP_sql['select_line'] = 'SELECT dl.'.
    $_OMP_tbl_fld_line['pkey'].' AS pkey, dl.'.
    $_OMP_tbl_fld_line['ol_pkey'].' AS ol_pkey, dl.'.
    $_OMP_tbl_fld_line['quantity'].' AS quantity, dl.'.
    $_OMP_tbl_fld_line['bales'].' AS bales, il.'.
    $_OMP_invoices_lines_fld['inv_pkey'].' AS inv_pkey, il.'.
    $_OMP_invoices_lines_fld['date'].' AS date, il.'.
    $_OMP_invoices_lines_fld['supplier_pkey'].' AS supplier_pkey, ol.'.
    $_OMP_orders_lines_fld['oi_pkey'].' AS oi_pkey, ol.'.
    $_OMP_orders_lines_fld['oi_pkey'].' AS master_pkey, ol.'.
    $_OMP_orders_lines_fld['prod_pkey'].' AS prod_pkey, ol.'.
    $_OMP_orders_lines_fld['eta'].' AS eta, ol.'.
    $_OMP_orders_lines_fld['quantity'].' AS ordered, prod.'.
    $_OMP_products_fld['description'].' AS prod_label, oi.'.
    $_OMP_orders_fld['date'].' AS master_date, oi.'.
    $_OMP_orders_fld['um'].' AS um FROM '.
    $_OMP_tables[$_OMP_tbl_line].' AS dl LEFT JOIN '.
    $_OMP_tables['invoices_lines'].' AS il ON (dl.'.
    $_OMP_tbl_fld_line['pkey'].' = il.'.
    $_OMP_invoices_lines_fld['dl_pkey'].') LEFT JOIN '.
    $_OMP_tables['orders_lines'].' AS ol ON (dl.'.
    $_OMP_tbl_fld_line['ol_pkey'].' = ol.'.
    $_OMP_orders_lines_fld['pkey'].') LEFT JOIN '.
    $_OMP_tables['products'].' AS prod ON (ol.'.
    $_OMP_orders_lines_fld['prod_pkey'].' = prod.'.
    $_OMP_products_fld['pkey'].') LEFT JOIN '.
    $_OMP_tables['orders'].' AS oi ON (ol.'.
    $_OMP_orders_lines_fld['oi_pkey'].' = oi.'.
    $_OMP_orders_fld['pkey'].')';
$_OMP_sql['row_line'] = $_OMP_sql['select_line'].' WHERE dl.'.
    $_OMP_tbl_fld_line['del_pkey'].' = ? AND dl.'.
    $_OMP_tbl_fld_line['client_pkey'].' = ?';
$_OMP_sql['products_combo'] = 'SELECT ol.'.
    $_OMP_orders_lines_fld['prod_pkey'].' AS prod_pkey, coalesce (oi.'.
    $_OMP_orders_fld['supplier_ref'].', oi.'.
    $_OMP_orders_fld['ref'].', oi.'.
    $_OMP_orders_fld['pkey'].') AS oi_pkey, ol.'.
    $_OMP_orders_lines_fld['pkey'].' AS ol_pkey, sum(ol.'.
    $_OMP_orders_lines_fld['quantity'].') AS quantity, ol.'.
    $_OMP_orders_lines_fld['month'].' AS month, ol.'.
    $_OMP_orders_lines_fld['year'].' AS year, ol.'.
    $_OMP_orders_lines_fld['eta'].' AS eta, \'t\' as active FROM '.
    $_OMP_tables['orders_lines'].' AS ol LEFT JOIN '.
    $_OMP_tables['orders'].' AS oi ON (ol.'.
    $_OMP_orders_lines_fld['oi_pkey'].' = oi.'.
    $_OMP_orders_fld['pkey'].') WHERE oi.'.
    $_OMP_orders_fld['client_pkey'].' = \':client\' AND oi.'.
    $_OMP_orders_fld['supplier_pkey'].' LIKE \':supplier\' AND ';
$_OMP_sql['products_combo_end'] = 
    $_OMP_orders_lines_fld['year'].
    ' = EXTRACT(YEAR FROM TIMESTAMP \':year\') AND ol.'.
    $_OMP_orders_lines_fld['month'].
    ' = EXTRACT(MONTH FROM TIMESTAMP \':month\') GROUP BY ol.'.
    $_OMP_orders_lines_fld['prod_pkey'].', oi.'.
    $_OMP_orders_fld['supplier_ref'].', oi.'.
    $_OMP_orders_fld['ref'].', oi.'.
    $_OMP_orders_fld['pkey'].', ol.'.
    $_OMP_orders_lines_fld['pkey'].', ol.'.
    $_OMP_orders_lines_fld['month'].', ol.'.
    $_OMP_orders_lines_fld['year'].', ol.'.
    $_OMP_orders_lines_fld['eta'].' ORDER BY ol.'.
    $_OMP_orders_lines_fld['prod_pkey'].', ol.'.
    $_OMP_orders_lines_fld['eta'];

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
    global $_OMP_table_header, $_OMP_table_data, $_OMP_clients_fld, $_OMP_db, 
        $_OMP_html, $_OMP_LC, $_OMP_get, $_OMP_onload, $_OMP_orders_fld, 
        $_OMP_rec, $_OMP_tables, $_OMP_TPL;
    $_OMP_rec = array_map('OMP_htmlentities', $_OMP_rec);
    if ($_OMP_get['action'] == 'new') {
        $_OMP_rec['date'] = strftime('%F');
        if (empty($_OMP_rec['client_pkey'])) {
                $_OMP_rec_cli['destination'] = '';
                $_OMP_rec['eta'] = '';
        } else {
             // Form focus
            $sql_cli_details = 'SELECT '.$_OMP_clients_fld['del_addr'].
                ' AS destination FROM '.
                $_OMP_tables['clients'].' WHERE '.
                $_OMP_clients_fld['pkey'].' = '.
                $_OMP_db->quote($_OMP_rec['client_pkey']);
            $_OMP_row = $_OMP_db->queryRow($sql_cli_details);
            if (PEAR::isError($_OMP_row)) {
                $_OMP_onload = '';
            } else {
                $_OMP_onload = '';
                // $_OMP_onload =
                //     'onload="javascript:
                //      document.forms[0].elements[2].
                //     focus();return true"';
                $_OMP_rec_cli['destination'] = $_OMP_row['destination'];
            }
            $sql_cli_details = 'SELECT DISTINCT "ETA" AS eta 
                FROM "DettagliOrdini" 
                LEFT JOIN "Ordini" ON 
                ("DettagliOrdini"."IDOrdine" = "Ordini"."IDOrdine") 
                WHERE "IDCliente" = '.
                OMP_db_quote($_OMP_rec['client_pkey']).' AND '.
                OMP_db_quote($_OMP_orders_fld['closed'], 'boolean').
                ' = false AND '.
                OMP_db_quote($_OMP_orders_fld['cancelled'], 'boolean').
                ' = false and extract(\'year\' from "ETA") = 
                extract(\'year\' from timestamp '.
                OMP_db_quote($_OMP_rec['date']).
                ') and (extract(\'month\' from "ETA") = 
                extract(\'month\' from timestamp '.
                OMP_db_quote($_OMP_rec['date']).
                ') or extract(\'month\' from "ETA") = 
                extract(\'month\' from date '.
                OMP_db_quote($_OMP_rec['date']).
                ' + interval \'1 month\')) ORDER BY "ETA" LIMIT 1';
            $_OMP_row = $_OMP_db->queryRow($sql_cli_details);
            if (PEAR::isError($_OMP_row) or empty($_OMP_row['eta'])) {
                $_OMP_rec_cli['eta'] = $_OMP_rec['date'];
            } else {
                $_OMP_rec_cli['eta'] = strftime('%F', 
                    strtotime($_OMP_row['eta']));
            }
        }
        $_OMP_rec = array_merge($_OMP_rec, $_OMP_rec_cli);
        return;
    } elseif ($_OMP_get['action'] == 'del') {
        $_OMP_table_header = array($_OMP_LC[801], $_OMP_LC[100]);
        $_OMP_table_data = array($_OMP_rec['pkey'], $_OMP_rec['client_pkey']);
        return;
    }
    $_OMP_html['client_link'] = '';
    if ($_OMP_get['action'] != 'edit') {
        $_OMP_rec['date'] = strftime('%x', strtotime($_OMP_rec['date']));
        $_OMP_rec['eta'] = strftime('%x', strtotime($_OMP_rec['eta']));
        if ($_OMP_get['popup'] || $_OMP_get['action'] == 'newdetail') {
            $_OMP_html['client_link'] = $_OMP_rec['client_pkey'];
        } else { 
            // I use _OMP_html here and made it global
            // because I need _OMP_rec['client_pkey'] clean without
            // the poplink
            //$_OMP_html['client_pkey']
            /* OMP_popLink is defined in base.php */
            $_OMP_html['client_link'] = OMP_popLink(
                $_OMP_TPL[9], OMP_PATH.'clients.php?'.
                OMP_link('filter=1&pkey='.
                urlencode(html_entity_decode($_OMP_rec['client_pkey']))),
                    $_OMP_rec['client_pkey']);
        }
        if ($_OMP_get['list']) {
            $_OMP_html['rec0'] = $_OMP_rec['pkey'];
            $_OMP_html['rec1'] = $_OMP_rec['client_pkey'];
            $_OMP_html['rec2'] = $_OMP_rec['date'];
            $_OMP_html['rec3'] = $_OMP_rec['eta'];
            $_OMP_html['label0'] = '';
            $_OMP_html['label1'] = $_OMP_rec['client_name'];
            $_OMP_html['label2'] = '';
            $_OMP_html['label3'] = '';
            /* mdc-list */
            /* or mdc-data-table__cell--numeric */
            $_OMP_html['cell1_numeric'] = '';
            $_OMP_html['cell2_numeric'] = '';
            $_OMP_html['cell3_numeric'] = '';
            $_OMP_html['cell4_numeric'] = '';
        }
    }
}

/**
* Process the array $GLOBALS['record_sf']
*
*/
function OMP_makeVarsLine($ts = false)
{
    global $_OMP_db, $_OMP_html, $_OMP_invoices_lines_fld, 
        $_OMP_get, $_OMP_rec_sf, $_OMP_rec, $_OMP_tables, 
        $_OMP_LC, $_OMP_TPL;
    $_OMP_rec_sf = array_map('OMP_htmlentities', $_OMP_rec_sf);
    $ts = ($ts) ? OMP_DB_THOUSANDS_SEP: $_SESSION['ts'];
    $_OMP_rec_sf['um'] = ($_OMP_rec_sf['um'] == 0) ? 'Kg' : 'Lbs';
    if ($_OMP_get['action'] == 'read') {
        if (!$_OMP_get['popup']) {
            /* OMP_popLink is defined in base.php */
            $_OMP_rec_sf['prod_pkey'] = OMP_popLink($_OMP_TPL[9], OMP_PATH.
                'products.php?'.OMP_link('filter=1&pkey='.
                urlencode($_OMP_rec_sf['prod_pkey'])),
                $_OMP_rec_sf['prod_pkey']);
            $_OMP_rec_sf['oi_pkey'] = OMP_popLink($_OMP_TPL[9], OMP_PATH.
                'orders.php?'.OMP_link('filter=1&pkey='.
                urlencode($_OMP_rec_sf['oi_pkey'])),
                $_OMP_rec_sf['oi_pkey']);
            $_OMP_rec_sf['inv_pkey'] = OMP_popLink($_OMP_TPL[9], OMP_PATH.
                'invoices.php?'.OMP_link('filter=1&pkey='.
                $_OMP_rec_sf['inv_pkey'].
                '&date='.$_OMP_rec_sf['date'].'&supplier_pkey='.
                urlencode($_OMP_rec_sf['supplier_pkey'])),
                $_OMP_rec_sf['inv_pkey']);
        }
/*        $_OMP_rec_sf['date'] = 
            ($_OMP_rec_sf['date'] == '') ? 
                '' : strftime('%x', strtotime($_OMP_rec_sf['date']));*/
    }
    $_OMP_rec_sf['quantity'] = 
        number_format($_OMP_rec_sf['quantity'], 2, $_SESSION['dp'], $ts);
    $_OMP_rec_sf['ordered'] = 
        number_format($_OMP_rec_sf['ordered'], 0, $_SESSION['dp'], $ts);
    $_OMP_rec_sf['bales'] = 
        number_format($_OMP_rec_sf['bales'], 0, $_SESSION['dp'], $ts);
    if ($_OMP_get['action'] == 'editline') {
        $_OMP_html['client_pkey'] = $_OMP_rec['client_pkey'];
        $_OMP_rec_sf['del_pkey'] = $_OMP_rec['pkey'];
    } elseif ($_OMP_get['action'] == 'delline' && 
        $_OMP_rec_sf['inv_pkey'] != '') {
        /* don't add these to the page_title anymore */
        // Please note $_OMP_LC[92] contains $_OMP_rec_sf['inv_pkey']
        //eval("\$_OMP_html['page_title'] .= \"".$_OMP_LC[92]."\";");
        $sql_get_invoice_check = 'SELECT COUNT('.
            $_OMP_invoices_lines_fld['inv_pkey'].') AS il_count FROM '.
            $_OMP_tables['invoices_lines'].' WHERE '.
            $_OMP_invoices_lines_fld['inv_pkey'].' = '.
            $_OMP_db->quote($_OMP_rec_sf['inv_pkey']).' AND '.
            $_OMP_invoices_lines_fld['date'].' = '.
            $_OMP_db->quote(OMP_checkDate($_OMP_rec_sf['date'], '')).' AND '.
            $_OMP_invoices_lines_fld['supplier_pkey'].' = '.
            $_OMP_db->quote($_OMP_rec_sf['supplier_pkey']);
            /* don't add these to the page_title anymore */
            // $_OMP_rec_check = $_OMP_db->queryRow($sql_get_invoice_check);
            // // More than one invoice-lines. Invoice will be updated
            // if ($_OMP_rec_check['il_count'] > 1) {
            //     $_OMP_html['page_title'] .= ' '.$_OMP_LC[40];
            // } else { // Just one invoice-line. Invoice will be deleted
            //     $_OMP_html['page_title'] .= ' '.$_OMP_LC[44];
            // }
    }
    if ($ts) {
        $_OMP_rec_sf['eta'] =
            ($_OMP_rec_sf['eta'] == '') ? '' :
                strftime('%x', strtotime($_OMP_rec_sf['eta']));
        $_OMP_rec_sf['date'] =
            ($_OMP_rec_sf['date'] == '') ?
                '' : strftime('%x', strtotime($_OMP_rec_sf['date']));
    }
}
/**
* Init new line variables
*
*/
function OMP_newLine()
{
    global $_OMP_db, $_OMP_invoices_fld, $_OMP_invoices_len, $_OMP_orders_fld, 
        $_OMP_orders_lines_fld, $_OMP_get, $_OMP_onload, $_OMP_rec, 
        $_OMP_rec_orig, $_OMP_rec_sf, $_OMP_tables;
    $_OMP_rec_sf['del_pkey'] = $_OMP_rec['pkey'];
    if (!empty($_POST['inv_pkey']) && !empty($_POST['form']['num_ol_pkey'])) {
        OMP_checkTxt($_POST['inv_pkey'], $_OMP_invoices_fld['pkey'], 
            $_OMP_invoices_len['pkey'], true);
        OMP_checkDate($_POST['date'], $_OMP_invoices_fld['date'], true);
        $sql_get_ol = 'SELECT '.
            $_OMP_orders_lines_fld['oi_pkey'].' AS oi_pkey FROM '.
            $_OMP_tables['orders_lines'].' WHERE '.
            $_OMP_orders_lines_fld['pkey'].' = '.$_POST['form']['num_ol_pkey'];
        if (is_array($_OMP_rec_inv = $_OMP_db->queryRow($sql_get_ol))) {
            $sql_get_oi = 'SELECT '.$_OMP_orders_fld['supplier_pkey'].
                ' AS supplier_pkey FROM '.
                $_OMP_tables['orders'].' WHERE '.
                $_OMP_orders_fld['pkey'].' = '.
                $_OMP_db->quote($_OMP_rec_inv['oi_pkey']);
            if (is_array($_OMP_rec_inv = $_OMP_db->queryRow($sql_get_oi))) {
                $_OMP_rec_sf['supplier_pkey'] = $_OMP_rec_inv['supplier_pkey'];
                $sql_get_invoice = 'SELECT '.$_OMP_invoices_fld['date'].
                    ' AS date FROM '.
                    $_OMP_tables['invoices'].' WHERE '.
                    $_OMP_invoices_fld['pkey'].' = '.
                    OMP_db_quote($_POST['inv_pkey']).' AND '.
                    $_OMP_invoices_fld['del_pkey'].' = '.
                    OMP_db_quote($_OMP_rec['pkey']).' AND '.
                    $_OMP_invoices_fld['supplier_pkey'].' = '.
                    OMP_db_quote($_OMP_rec_sf['supplier_pkey']);
                if (is_array($_OMP_rec_inv = 
                        $_OMP_db->queryRow($sql_get_invoice))) {
                    $_OMP_rec_sf['inv_pkey'] = $_POST['inv_pkey'];
                    $_OMP_rec_sf['date'] = 
                        strftime('%F', strtotime($_OMP_rec_inv['date']));
                    // $_OMP_onload = 'onload="javascript:document.forms[0].
                    //     elements[3].focus();return true"';
                } else {
                    $_OMP_rec_sf['inv_pkey'] = $_POST['inv_pkey'];
		            $_OMP_rec_sf['date'] = strftime('%F');
                    // $_OMP_onload = 'onload="javascript:document.forms[0].
                    //     elements[2].focus();return true"';
                }
            }
        } else {
            OMP_genErr($_OMP_db->getMessage(), 
                'Order line '.$_POST['form']['num_ol_pkey']);
        }
    } else {
        if ($_OMP_get['action'] != 'editline') {
            $_OMP_rec_sf['inv_pkey'] = '';
            $_OMP_rec_sf['date'] = '';
        }
    }
}

/**
* Make changes to $_POST. If called by new-line, insert new delivery-line, 
* new invoice and new invoice-line
*
* TODO: DO WE STILL NEED TO UNSET INV-PKEY AND DATE? 
* THEY ARE NOT IN POST-FORM NOW
*/
function OMP_changePost()
{
    global $_OMP_invoices_fld, $_OMP_invoices_len, $_OMP_post, 
        $_OMP_trans, $_OMP_get;
    if ($_OMP_get['action'] == 'editline') {
        $_POST['form']['txt_del_pkey'] = $_OMP_post['pk_pkey'];
    }
    if ($_OMP_get['action'] == 'newdetail') {
        $_POST['form']['txt_del_pkey'] = $_OMP_get['pk_pkey'];
    }
    if ($_POST['inv_pkey'] == '') {
      unset($_POST['inv_pkey']);
    }
    if ($_POST['date'] == '') {
      unset($_POST['date']);
    }
    // Must add client_pkey to form when adding or editing delivery-line
    if ($_OMP_get['action'] == 'newdetail' || 
        $_OMP_get['action'] == 'editline' && 
        isset($_OMP_get['pk_client_pkey'])) {
	    $_POST['form']['txt_client_pkey'] = 
	      html_entity_decode(urldecode($_OMP_get['pk_client_pkey']));
    }
    if (($_OMP_get['action'] == 'newdetail' || 
        $_OMP_get['action'] == 'editline') && 
        isset($_POST['inv_pkey']) && isset($_POST['date'])) {
        OMP_checkTxt($_POST['inv_pkey'], $_OMP_invoices_fld['pkey'], 
            $_OMP_invoices_len['pkey']);
        $_OMP_trans = true;
    }
}
/**
* End functions
*/
$_OMP_has_subform = true;
/* See OMP_makeSql() in read.php */
$_OMP_table_alias = 'del.';
$_OMP_sort_default_sub = '0';
$_OMP_sort_type_default_sub = 0;
$_OMP_headline = 801;
switch ($_OMP_get['action']) {
/**
* Read record
*/
case 'read': {
    $_OMP_tpl = OMP_TPL_READ.'9, 16, 65, 66, 67, 68,
        73, 720, 800, 804, 805';
    $_OMP_lcl = OMP_LCL_READ.'23, 24, 39, 43, 52, 100,
        106, 600, 700, 703, 708, 717, 720, 800,
        801, 802, 803, 804, 806, 1501';
    $_OMP_sql['sort_default'] = ' ORDER BY del.'.$_OMP_tbl_fld['date'].
        ' ?, del.'.$_OMP_tbl_fld['client_pkey'];
    $_OMP_sql['sort_record'][0] = $_OMP_sql['sort_list'][0] = 
        ' ORDER BY del.'.$_OMP_tbl_fld['pkey'].' ?';
    $_OMP_sql['sort_record'][1] = $_OMP_sql['sort_list'][1] = 
        ' ORDER BY del.'.$_OMP_tbl_fld['client_pkey'].' ?, del.'.
        $_OMP_tbl_fld['eta'];
    $_OMP_sql['sort_record'][2] = $_OMP_sql['sort_list'][2] = 
        $_OMP_sql['sort_default'];
    $_OMP_sql['sort_record'][3] = $_OMP_sql['sort_list'][3] = 
        ' ORDER BY del.'.$_OMP_tbl_fld['eta'].' ?, del.'.
        $_OMP_tbl_fld['client_pkey'];
    $_OMP_sort_idx = array(801, 100, 703, 806);
    if ($_OMP_get['list']) {
        $_OMP_list_tpl = 60;
        $_OMP_list_header_tpl = 65;
        $_OMP_list_rec_tpl = 66;
        $_OMP_list_wrapper ="wrapper4";
        /* mdc table */
        /* or mdc-data-table__header-cell--numeric" */
        $_OMP_html['header1_numeric'] = '';
        $_OMP_html['header2_numeric'] = '';
        $_OMP_html['header3_numeric'] = '';
        $_OMP_html['header4_numeric'] = '';
    } else {
        $_OMP_rec_tpl = 800;
        $_OMP_admin_tpl = 68;
        $_OMP_subform_tpl = 67;
        $_OMP_sub_rec_tpl = 805;
        $_OMP_sub_header_tpl = 804;
        $_OMP_sub_sort_idx = array(600, 700);
    }
    $_OMP_title = 800;
    $_OMP_sort['default'] = '2'; // Master record default sort
    $_OMP_sort['type'] = 1; // Master record default sort order
    $_OMP_subform_wrapper = 'wrapper8';
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
    $_OMP_tpl = OMP_TPL_EDIT.'802';
    $_OMP_lcl = OMP_LCL_EDIT.'100, 106, 703, 708, 800, 801, 802, 803, 806';
    // Check if insert-button was pushed
    if (isset($_POST['insert_button'])) {
        /* $_OMP_del_sql_update @see deliveries-schema.php */
        $_OMP_sql['update'] = $_OMP_del_sql_update;
        /* $_OMP_datatypes_del_sql_update @see deliveries-schema.php */
        $_OMP_datatypes = $_OMP_datatypes_del_sql_update;
    } else {
        $_OMP_datatypes = array('text');
        $_OMP_sql['clients_combo'] = 'SELECT '.
            $_OMP_clients_fld['pkey'].' AS pkey, active FROM '.
            $_OMP_tables['clients'].' ORDER BY '.
            $_OMP_clients_fld['pkey'];
        $_OMP_drop_down = array('lib/mdc-select-clients.php');
        $_OMP_input_tpl = 802;
        $_OMP_edit_tpl = 51;
        $_OMP_page_title_lcl = 800;
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
    $_OMP_tpl = OMP_TPL_FILTER.'801';
    $_OMP_lcl = OMP_LCL_FILTER.'100, 106, 703, 708, 800, 801, 802, 803, 806';
    
    if ($_SESSION['cat'] == 0) { // Filter for clients
        $_OMP_client_combo = 
            '<input disabled type="text" name="client_pkey" id="client_pkey" '. 
            'size="10" value="'.htmlentities($_SESSION['id']).'" /> '.
            '<input type="hidden" name="form[enc_client_pkey]" '.
            'id="form[enc_client_pkey]" value="'.
            htmlentities($_SESSION['id']).'" />';
    } elseif ($_SESSION['cat'] == 1) { // Filter for suppliers
        $_OMP_supplier_combo = 
            '<input disabled type="text" name="supplier_pkey" '.
            'id="supplier_pkey" '.'size="10" value="'.
            htmlentities($_SESSION['id']).'" /> '.
            '<input type="hidden" name="form[enc_supplier_pkey]" '.
            'id="form[enc_supplier_pkey]" value="'.
            htmlentities($_SESSION['id']).'" />';
        $_OMP_drop_down = array('lib/mdc-select-clients.php');
    } else {
        $_OMP_drop_down = array('lib/mdc-select-clients.php');
    }
    $_OMP_page_title_lcl = 800;
    $_OMP_include_tpl = 801;
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
    $_OMP_tpl = OMP_TPL_NEW.'802';
    $_OMP_lcl = OMP_LCL_NEW.'100, 106, 703, 708, 800, 801, 802, 803, 806';
    // Check if insert-button was pushed
    if (isset($_POST['insert_button'])) {
        /* $_OMP_del_sql_insert @see deliveries-schema.php */
        $_OMP_sql['insert'] = $_OMP_del_sql_insert;
        /* $_OMP_datatypes_del_sql_insert @see deliveries-schema.php */
        $_OMP_datatypes = $_OMP_datatypes_del_sql_insert;
    } else {
        $_OMP_drop_down = array('lib/mdc-select-clients.php');
        $_OMP_onchange = true;
        $_OMP_input_tpl = 802;
        $_OMP_include_tpl = 50;
        $_OMP_page_title_lcl = 800;
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
    $_OMP_lcl = OMP_LCL_NEW.'23, 48, 52, 82, 89, 100,
        106, 703, 708, 717, 800, 801, 802, 803, 804, 805,
        806, 1501';
    OMP_load();
    $_OMP_html['include'] = $_OMP_html['page_title'] =
        $_OMP_html['browser_title'] = $_OMP_html['subform'] =
        $_OMP_html['input'] = '';
    // Check if insert-button was pushed
    if (isset($_POST['insert_button']) && 
        $_POST['insert_button'] == $_OMP_LC[39]) {
        /* $_OMP_del_sql_insert_line @see deliveries-schema.php */
        $_OMP_sql['insert_line'] = $_OMP_del_sql_insert_line;
        /* $_OMP_datatypes_del_sql_insert_line @see deliveries-schema.php */
        $_OMP_datatypes = $_OMP_datatypes_del_sql_insert_line;
        $_OMP_change_post = true;
    } else {
        $_OMP_sql['products_combo'] .= $_OMP_orders_fld['cancelled'].
            ' = \'No\' AND oi.'.$_OMP_orders_fld['closed'].
            ' = \'No\' AND ol.'.$_OMP_sql['products_combo_end'];
        $_OMP_master_tpl = $_OMP_TPL[800];
        $_OMP_frame_tpl = $_OMP_TPL[809];
        $_OMP_input_tpl = $_OMP_TPL[806];
        $_OMP_header_tpl = $_OMP_TPL[807];
        $_OMP_rec_tpl = $_OMP_TPL[808];
        $sort_record[0] = $_OMP_LC[801];
        $sort_record[1] = $_OMP_LC[100];
        $sort_record[2] = $_OMP_LC[703];
        $sort_record[3] = $_OMP_LC[806];
        $_OMP_page_title_lcl = 800;
        $_OMP_html['page_title'] = $_OMP_LC[800].' - '.
            $_OMP_LC[39].' '.$_OMP_LC[23];
        $_OMP_combo_required = true;
        $_OMP_drop_down = array('lib/dd-products-dl.php');
        $on_change_inv = 
            'onchange="javascript:this.form.submit();return true"';
        $_OMP_noitmes_msg = $_OMP_LC[89];
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
    $_OMP_tpl = OMP_TPL_EDIT.'9, 800, 806, 807, 808, 809, 810';
    $_OMP_lcl = OMP_LCL_EDIT.'23, 47, 48, 52, 82, 100, 106,
        703, 708, 717, 800, 801, 802, 803, 804, 805, 806, 1501';
    OMP_load();
    // Check if edit-button was pushed
    if (isset($_POST['insert_button']) && 
        $_POST['insert_button'] == $_OMP_LC[38]) {
        $_OMP_change_post = true;
        /* $_OMP_del_sql_update_line @see deliveries-schema.php */
        $_OMP_sql['update_line'] = $_OMP_del_sql_update_line;
        /* $_OMP_datatypes_del_sql_update_line @see deliveries-schema.php */
        $_OMP_datatypes = $_OMP_datatypes_del_sql_update_line;
    } else {
        $_OMP_sql['products_combo'] .= $_OMP_orders_fld['cancelled'].
            ' = \'No\' AND ol.'.$_OMP_sql['products_combo_end'];
        $_OMP_master_tpl = $_OMP_TPL[800];
        $_OMP_frame_tpl = $_OMP_TPL[809];
        $_OMP_input_tpl = $_OMP_TPL[806];
        $_OMP_header_tpl = $_OMP_TPL[807];
        $_OMP_rec_tpl = $_OMP_TPL[808];
        $sort_record[0] = $_OMP_LC[801];
        $sort_record[1] = $_OMP_LC[100];
        $sort_record[2] = $_OMP_LC[703];
        $sort_record[3] = $_OMP_LC[806];
        $_OMP_html['page_title'] = $_OMP_LC[800].' - '.
            $_OMP_LC[33].' '.$_OMP_LC[23];
        $_OMP_combo_required = true;
        $_OMP_drop_down = array('lib/dd-products-dl.php');
        $on_change_inv = 
            'onchange="javascript:this.form.submit();return true"';
        // We need this because of $on_change_inv
        // See also $abort_onclick in edit-line.php
        !isset($_POST['post_to_get']) or $_OMP_get =
            unserialize(base64_decode($_POST['post_to_get']));
        $_OMP_headline = 801;
    }
    require 'lib/edit-line.php';
    break;
}
/**
* End edit order-line record
*/

/**
* Delete record
*/
case 'del': {
    $_OMP_tpl = OMP_TPL_DEL.'52';
    $_OMP_lcl = OMP_LCL_DEL.'100, 800, 801';
    $_OMP_datatypes = array('text');
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['delete'] = 'DELETE FROM '.
            $_OMP_tables[$_OMP_tbl].' WHERE '.
            $_OMP_tbl_fld['pkey'].' = ? AND '.
            $_OMP_tbl_fld['client_pkey'].' = ?';
    } else {
        $_OMP_sql['delete_pre'] = 'SELECT '.
            $_OMP_tbl_fld['pkey'].' AS pkey, '.
            $_OMP_tbl_fld['client_pkey'].' AS client_pkey, '.
            $_OMP_tbl_fld['date'].' AS date, '.
            $_OMP_tbl_fld['eta'].' AS eta FROM '.
            $_OMP_tables[$_OMP_tbl].' WHERE '.
            $_OMP_tbl_fld['pkey'].' = ? AND '.
            $_OMP_tbl_fld['client_pkey'].' = ?';
        $_OMP_del_tpl = 52;
        $_OMP_page_title_lcl = 800;
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
    $_OMP_tpl = OMP_TPL_DEL.'70, 807, 808, 810';
    // Need 40 for OMP_makeVarsLine()
    $_OMP_lcl = OMP_LCL_DEL.'23, 40, 92, 703, 717, 800, 803, 
        804, 807, 811, 805, 1501';
    OMP_load();
    // Check if delete-button was pushed
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['delete_line'] = 'DELETE FROM '.
            $_OMP_tables[$_OMP_tbl_line].' WHERE '.
            $_OMP_tbl_fld_line['pkey'].' = ?';
        $_OMP_datatypes = array('integer');
    } else {
        $_OMP_header_tpl = 807;
        $_OMP_sub_header_tpl = 807;
        $_OMP_rec_tpl = 808;
        $_OMP_colspan = '5'; // Required by $_OMP_TPL[70]
        $_OMP_include_tpl = 810;
        $_OMP_html['page_title'] = $_OMP_LC[800].' - '.
            $_OMP_LC[22].' '.$_OMP_LC[23].' ';
        $_OMP_headline = 801;
        $_OMP_sql['delete_pre_line'] = $_OMP_sql['select_line'].' WHERE dl.'.
            $_OMP_tbl_fld_line['pkey'].' = ?';
        $_OMP_datatypes = array('integer');
    }
    require 'lib/del-line.php';
    break;
}
/**
* End delete order line record
*/
}
?>
