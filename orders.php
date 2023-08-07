<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | OMP Version 1                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2023 Lorenzo Ciani                                |
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
// $Id: orders.php,v 0.8 $
//
// Manage orders records
//

require_once 'base.php';
// Database schema
require_once 'schemas/db-schema.php';
// Name of current master-table
$_OMP_tbl = 'orders';
$_OMP_tbl_line = 'orders_lines';
/* Table schemas */
require_once 'schemas/clients-schema.php';
require_once 'schemas/enduses-schema.php';
require_once 'schemas/suppliers-schema.php';
require_once 'schemas/products-schema.php';
require_once 'schemas/payments-schema.php';
require_once 'schemas/terms-schema.php';
require_once 'schemas/currencies-schema.php';
require_once 'schemas/um-schema.php';
require_once 'schemas/orders-schema.php';
/* Table fields, length and keys */
$_OMP_tbl_fld = $_OMP_orders_fld;
$_OMP_fld_len = $_OMP_orders_len;
$_OMP_tbl_key = $_OMP_orders_key;
$_OMP_tbl_fld_line = $_OMP_orders_lines_fld;
$_OMP_fld_len_line = $_OMP_orders_lines_len;
$_OMP_tbl_line_key = $_OMP_orders_lines_key;
/**
 * SQL code
 */
$_OMP_sql['sort_default_sub'] = ' ORDER BY ol.'.
    $_OMP_tbl_fld_line['prod_pkey'].', ol.'.
    $_OMP_tbl_fld_line['plan'].', ol.'.
    $_OMP_tbl_fld_line['eta'];
$_OMP_sql['sort_record_sub'][0] = ' ORDER BY ol.'.
    $_OMP_tbl_fld_line['prod_pkey'].' ?, ol.'.
    $_OMP_tbl_fld_line['plan'].', ol.'.
    $_OMP_tbl_fld_line['eta'];
$_OMP_sql['sort_record_sub'][1] = ' ORDER BY ol.'.
    $_OMP_tbl_fld_line['plan'].' ?, ol.'.
    $_OMP_tbl_fld_line['prod_pkey'].', ol.'.
    $_OMP_tbl_fld_line['eta'];
$_OMP_sql['sort_record_sub'][2] = ' ORDER BY ol.'.
    $_OMP_tbl_fld_line['eta'].' ?, ol.'.
    $_OMP_tbl_fld_line['prod_pkey'].', ol.'.
    $_OMP_tbl_fld_line['plan'];
$_OMP_sql['select'] = 'SELECT oi.'.
    $_OMP_tbl_fld['pkey'].' AS pkey, oi.'.
    $_OMP_tbl_fld['client_pkey'].' AS client_pkey, oi.'.
    $_OMP_tbl_fld['supplier_pkey'].' AS supplier_pkey, oi.'.
    $_OMP_tbl_fld['date'].' AS date, oi.'.
    $_OMP_tbl_fld['ref'].' AS ref, oi.'.
    $_OMP_tbl_fld['client_ref'].' AS client_ref, oi.'.
    $_OMP_tbl_fld['supplier_ref'].' AS supplier_ref, oi.'.
    $_OMP_tbl_fld['printed'].' AS printed, oi.'.
    $_OMP_tbl_fld['amended'].' AS amended, oi.'.
    $_OMP_tbl_fld['closed'].' AS closed, oi.'.
    $_OMP_tbl_fld['cancelled'].' AS cancelled, oi.'.
    $_OMP_tbl_fld['enduse_pkey'].' AS enduse_pkey, oi.'.
    $_OMP_tbl_fld['del_instr'].' AS del_instr, oi.'.
    $_OMP_tbl_fld['ship_instr'].' AS ship_instr, oi.'.
    $_OMP_tbl_fld['note'].' AS note, oi.'.
    $_OMP_tbl_fld['paymnt_pkey'].' AS paymnt_pkey, oi.'.
    $_OMP_tbl_fld['term_pkey'].' AS term_pkey, oi.'.
    $_OMP_tbl_fld['paymnt_days'].' AS paymnt_days, oi.'.
    $_OMP_tbl_fld['curr'].' AS curr, oi.'.
    $_OMP_tbl_fld['um'].' AS um, cli.'.
    $_OMP_clients_fld['name'].' AS client_name, cli.'.
    $_OMP_clients_fld['wilclient'].' AS wilclient, sup.'.
    $_OMP_suppliers_fld['name'].' AS supplier_name, eu.'.
    $_OMP_enduses_fld['name'].' AS enduse_label, pmt.'.
    $_OMP_payments_fld['name'].' AS paymnt_name, pmt.'.
    $_OMP_payments_fld['description'].' AS paymnt_label, term.'.
    $_OMP_terms_fld['name'].' AS term_name, term.'.
    $_OMP_terms_fld['description'].' AS term_label FROM '.
    $_OMP_tables[$_OMP_tbl].' AS oi LEFT JOIN '.
    $_OMP_tables['clients'].' AS cli ON (oi.'.
    $_OMP_tbl_fld['client_pkey'].' = cli.'.
    $_OMP_clients_fld['pkey'].') LEFT JOIN '.
    $_OMP_tables['suppliers'].' AS sup ON (oi.'.
    $_OMP_tbl_fld['supplier_pkey'].' = sup.'.
    $_OMP_suppliers_fld['pkey'].') LEFT JOIN '.
    $_OMP_tables['enduses'].' AS eu ON (oi.'.
    $_OMP_tbl_fld['enduse_pkey'].' = eu.'.
    $_OMP_enduses_fld['pkey'].') LEFT JOIN '.
    $_OMP_tables['payments'].' AS pmt ON (oi.'.
    $_OMP_tbl_fld['paymnt_pkey'].' = pmt.'.
    $_OMP_payments_fld['pkey'].') LEFT JOIN '.
    $_OMP_tables['terms'].' AS term ON (oi.'.
    $_OMP_tbl_fld['term_pkey'].' = term.'.
    $_OMP_terms_fld['pkey'].')';
$_OMP_sql['row'] = $_OMP_sql['select'].' WHERE oi.'.
    $_OMP_tbl_fld['pkey'].' = ?';
$_OMP_sql['select_line'] = 'SELECT ol.'.
    $_OMP_tbl_fld_line['pkey'].' AS pkey, ol.'.
    $_OMP_tbl_fld_line['oi_pkey'].' AS master_pkey, oi.'.
    $_OMP_tbl_fld['date'].' AS master_date, ol.'.
    $_OMP_tbl_fld_line['prod_pkey'].' AS prod_pkey, ol.'.
    $_OMP_tbl_fld_line['year'].' AS year, ol.'.
    $_OMP_tbl_fld_line['month'].' AS month, ol.'.
    $_OMP_tbl_fld_line['plan'].' AS plan, ol.'.
    $_OMP_tbl_fld_line['quantity'].' AS quantity, ol.'.
    $_OMP_tbl_fld_line['price'].' AS price, ol.'.
    $_OMP_tbl_fld_line['price_eur'].' AS price_eur, ol.'.
    $_OMP_tbl_fld_line['discount'].' AS discount, ol.'.
    $_OMP_tbl_fld_line['price_net'].' AS price_net, ol.'.
    $_OMP_tbl_fld_line['price_net_eur'].' AS price_net_eur, ol.'.
    $_OMP_tbl_fld_line['note'].' AS note, ol.'.
    $_OMP_tbl_fld_line['eta'].' AS eta, pd.'.
    $_OMP_products_fld['description'].' AS prod_label FROM '.
    $_OMP_tables[$_OMP_tbl_line].' AS ol LEFT JOIN '.
    $_OMP_tables[$_OMP_tbl].' AS oi ON (ol.'.
    $_OMP_tbl_fld_line['oi_pkey'].' = oi.'.
    $_OMP_tbl_fld['pkey'].') LEFT JOIN '.
    $_OMP_tables['products'].' AS pd ON (ol.'.
    $_OMP_tbl_fld_line['prod_pkey'].' = pd.'.
    $_OMP_products_fld['pkey'].')';
$_OMP_sql['row_line'] = $_OMP_sql['select_line'].' WHERE ol.'.
    $_OMP_tbl_fld_line['oi_pkey'].' = ?';
// Modify this. It is set in dd-sql.php
/*$_OMP_sql['suppliers_combo'] = 'SELECT DISTINCT '.
    $_OMP_tables['suppliers'].'.'.
    $_OMP_suppliers_fld['pkey'].' AS pkey FROM '.
    $_OMP_tables['suppliers'].' LEFT JOIN '.
    $_OMP_tables['products'].' ON '.
    $_OMP_tables['suppliers'].'.'.
    $_OMP_suppliers_fld['pkey'].' = '.
    $_OMP_tables['products'].'.'.
    $_OMP_products_fld['supplier_pkey'].' WHERE '.
    $_OMP_tables['suppliers'].'.'.
    $_OMP_suppliers_fld['active'].' = true ORDER BY '.
    $_OMP_tables['suppliers'].'.'.
    $_OMP_suppliers_fld['pkey'];*/
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
    global $_OMP_clients_fld, $_OMP_conf,
        $_OMP_db, $_OMP_html, $_OMP_LC, $_OMP_get,
        $_OMP_onload, $_OMP_prefilter,
        $_OMP_products_fld, $_OMP_rec,
        $_OMP_rec_sf, $_OMP_suppliers_fld, $_OMP_sql,
        $_OMP_tables, $_OMP_tbl, $_OMP_tbl_fld,
        $_OMP_tbl_line, $_OMP_tbl_fld_line, $_OMP_TPL;
    $_OMP_rec = array_map('OMP_htmlentities', $_OMP_rec);
    if ($_OMP_get['action'] == 'multi') {
        if (empty($_OMP_rec['client_pkey'])) {
            $_OMP_rec_cli['ship_instr'] = '';
        } else {
            $sql_cli_details = 'SELECT '.$_OMP_clients_fld['del_addr'].
                ' AS ship_instr FROM '.$_OMP_tables['clients'].
                ' WHERE '.$_OMP_clients_fld['pkey'].' = '.
                OMP_db_quote($_OMP_rec['client_pkey']);
            $_OMP_rec_cli = 
                $_OMP_db->queryRow($sql_cli_details);
            $_OMP_sql['products_combo'] = 'SELECT DISTINCT ol.'.
                $_OMP_tbl_fld_line['prod_pkey'].
                ' AS pkey, \'t\' AS '.
                $_OMP_products_fld['active'].' FROM '.
                $_OMP_tables[$_OMP_tbl_line].' AS ol LEFT JOIN '.
                $_OMP_tables[$_OMP_tbl].' AS oi ON (ol.'.
                $_OMP_tbl_fld_line['oi_pkey'].' = oi.'.
                $_OMP_tbl_fld['pkey'].') WHERE oi.'.
                $_OMP_tbl_fld['client_pkey'].' = '.
                OMP_db_quote($_OMP_rec['client_pkey']).' AND ol.'.
                $_OMP_tbl_fld_line['plan'].' BETWEEN CURRENT_DATE - INTERVAL
                    \'1 YEAR\' AND CURRENT_DATE AND oi.'.
                $_OMP_tbl_fld['cancelled'].' = FALSE ORDER BY ol.'.
                $_OMP_tbl_fld_line['prod_pkey'].' ASC';
            $_OMP_sql['products_combo_no_filter'] = $_OMP_sql['products_combo'];
            $_OMP_onload = 
                'onload="javascript:document.forms[0].
                    elements[2].focus();return true"';
        }
        $_OMP_rec = array_merge($_OMP_rec, $_OMP_rec_cli);
        $_OMP_rec['del_instr'] = 'DDP - TO ARRIVE ON ';
        if (empty($_OMP_rec['prod_pkey'])) {
            $_OMP_rec['price'] = '';
        } else {
            $_OMP_rec_sf['prod_pkey'] = $_OMP_rec['prod_pkey'];
            $sql_get_last_price = 'SELECT ol.'.
                $_OMP_tbl_fld_line['price'].' AS price, ol.'.
                $_OMP_tbl_fld_line['quantity'].' AS quantity FROM '.
                $_OMP_tables[$_OMP_tbl_line].' AS ol LEFT JOIN '.
                $_OMP_tables[$_OMP_tbl].' AS oi ON (ol.'.
                $_OMP_tbl_fld_line['oi_pkey'].' = oi.'.
                $_OMP_tbl_fld['pkey'].') WHERE ol.'.
                $_OMP_tbl_fld_line['prod_pkey'].' = '.
                OMP_db_quote($_OMP_rec['prod_pkey']).' AND oi.'.
                $_OMP_tbl_fld['client_pkey'].' = '.
                OMP_db_quote($_OMP_rec['client_pkey']).' AND oi.'.
                $_OMP_tbl_fld['cancelled'].' = \'No\' ORDER BY oi.'.
                $_OMP_tbl_fld['date'].' DESC LIMIT 1';
            $_OMP_rec_price = 
                $_OMP_db->queryRow($sql_get_last_price);
            if (is_array($_OMP_rec_price)) {
                $_OMP_rec['price'] = 
                    sprintf(OMP_PRICE_F, $_OMP_rec_price['price']);
                $_OMP_rec['quantity'] = 
                    sprintf(OMP_QTY_F, $_OMP_rec_price['quantity']);
            } else {
                $sql_get_last_price = 'SELECT '.$_OMP_products_fld['price'].
                    ' AS price FROM '.$_OMP_tables['products'].' WHERE '.
                    $_OMP_products_fld['pkey'].' = '.
                    OMP_db_quote($_OMP_rec['prod_pkey']);
                $_OMP_rec_price = $_OMP_db->queryRow($sql_get_last_price);
                if (is_array($_OMP_rec_price)) {
                    $_OMP_rec['price'] = 
                        sprintf(OMP_PRICE_F, $_OMP_rec_price['price']);
                } else { // This should not happen...
                    $_OMP_rec['price'] = '';
                }
            }
            // Form focus
            $GLOBALS['_OMP_onload'] = 'onload="javascript:document.
                forms[0].elements[5].focus();return true"';
        }
        // Default to current date
        !empty($_OMP_rec['date']) or $_OMP_rec['date'] = strftime('%F');
        return;
    } elseif ($_OMP_get['action'] == 'new') {
        // $_OMP_rec is set by initRecord() in new.php
        if (empty($_OMP_rec['client_pkey'])) {
            $_OMP_rec_cli['enduse_pkey'] = '';
            $_OMP_rec_cli['paymnt_pkey'] = '';
            $_OMP_rec_cli['paymnt_days'] = '';
            $_OMP_rec_cli['term_pkey'] = '';
            $_OMP_rec_cli['ship_instr'] = '';
        } else {
            $sql_cli_details = 'SELECT '.$_OMP_clients_fld['del_addr'].
                ' AS ship_instr, '.$_OMP_clients_fld['enduse_pkey'].
                ' AS enduse_pkey, '.$_OMP_clients_fld['paymnt_pkey'].
                ' AS paymnt_pkey, '.$_OMP_clients_fld['term_pkey'].
                ' AS term_pkey, '.$_OMP_clients_fld['paymnt_days'].
                ' AS paymnt_days FROM '.$_OMP_tables['clients'].
                ' WHERE '.$_OMP_clients_fld['pkey'].' = '.
                OMP_db_quote($_OMP_rec['client_pkey']);
            !is_array($_OMP_rec_cli = 
                $_OMP_db->queryRow($sql_cli_details)) or 
                $_OMP_onload = 'onload="javascript:'.
                    'document.forms[0].elements[2].focus();return true"';
        }
        $_OMP_rec = array_merge($_OMP_rec, $_OMP_rec_cli);
        if (empty($_OMP_rec['supplier_pkey'])) {
            $_OMP_rec_sup['um'] = '';
            $_OMP_rec_sup['curr'] = '';
        } else {
            $_OMP_onload = 
                'onload='.
                    '"javascript:document.'.
                    'forms[0].elements[5].focus();return true"';
            $_OMP_rec['supplier_ref'] = 
                OMP_supplierRef($_OMP_rec['supplier_pkey']);
            $sql_sup_details = 'SELECT '.$_OMP_suppliers_fld['curr'].
                ' AS curr, '.$_OMP_suppliers_fld['um'].
                ' AS um FROM '.$_OMP_tables['suppliers'].
                ' WHERE '.$_OMP_suppliers_fld['pkey'].' = '.
                OMP_db_quote($_OMP_rec['supplier_pkey']);
            $_OMP_rec_sup = $_OMP_db->queryRow($sql_sup_details);
        }
        $_OMP_rec = array_merge($_OMP_rec, $_OMP_rec_sup);
        /* Default to current date */
        $_OMP_rec['date'] = strftime('%F');
        return;
    } elseif ($_OMP_get['action'] == 'del') {
        $_OMP_rec['date'] =
            strftime('%x', strtotime($_OMP_rec['date']));
        return;
    }
    $_OMP_rec['paymnt_label'] = 
        ($_SESSION['LC'] == 'it') ? $_OMP_rec['paymnt_label'] : 
            $_OMP_rec['paymnt_name'];
    $_OMP_rec['term_label'] = 
        ($_SESSION['LC'] == 'it') ? $_OMP_rec['term_label'] : 
            $_OMP_rec['paymnt_name'];
    $_OMP_rec['status'] = '';
    if ($_OMP_rec['printed'] == 't') {
        $_OMP_rec['printed'] = true;
        $_OMP_rec['status'] = $_OMP_LC[711];
    } else {
        $_OMP_rec['printed'] = false;
    }
    if ($_OMP_get['action'] == 'print') {
        $_OMP_rec['amended'] != 't' or
            $_OMP_rec['pkey'] .= ' AMENDED';
    } else {
        if ($_OMP_rec['amended'] == 't') {
            $_OMP_rec['amended'] = true;
            $_OMP_rec['status'] .= ' '.$_OMP_LC[712];
        } else {
            $_OMP_rec['amended'] = false;
        }
    }
    if ($_OMP_rec['closed'] == 't') {
        $_OMP_rec['closed'] = true;
        $_OMP_rec['status'] .= ' '.$_OMP_LC[713];
    } else {
        $_OMP_rec['closed'] = false;
    }
    if ($_OMP_rec['cancelled'] == 't') {
        $_OMP_rec['cancelled'] = true;
        $_OMP_rec['status'] .= ' '.$_OMP_LC[715];
    } else {
        $_OMP_rec['cancelled'] = false;
    }
    $_OMP_rec['status'] = ltrim($_OMP_rec['status']);
    if ($_OMP_get['action'] == 'read' ||
        $_OMP_get['action'] == 'newdetail' ||
        $_OMP_get['action'] == 'editline') {
        $_OMP_rec['date'] = strftime('%x', strtotime($_OMP_rec['date']));
        if (!$_OMP_get['list']) {
            /* apply CSS class no_display to empty cells */
            if (empty($_OMP_rec['client_ref'])) {
                $_OMP_html['client_ref_display'] = 'no_display';
            } else {
                $_OMP_html['client_ref_display'] = '';
            }
            if (empty($_OMP_rec['supplier_ref'])) {
                $_OMP_html['supplier_ref_display'] = 'no_display';
            } else {
                $_OMP_html['supplier_ref_display'] = '';
            }
            if (empty($_OMP_rec['ref'])) {
                $_OMP_html['ref_display'] = 'no_display';
            } else {
                $_OMP_html['ref_display'] = '';
            }
            if (empty($_OMP_rec['note'])) {
                $_OMP_html['note_display'] = 'no_display';
            } else {
                $_OMP_html['note_display'] = '';
            }
            if (empty($_OMP_rec['status'])) {
                $_OMP_html['status_display'] = 'no_display';
            } else {
                $_OMP_html['status_display'] = '';
            }
            if (empty($_OMP_rec['del_instr'])) {
                $_OMP_html['del_instr_display'] = 'no_display';
            } else {
                $_OMP_html['del_instr_display'] = '';
            }
            if (empty($_OMP_rec['ship_instr'])) {
                $_OMP_html['ship_instr_display'] = 'no_display';
            } else {
                $_OMP_html['ship_instr_display'] = '';
            }
        }
        if ($_OMP_get['action'] == 'newdetail' ||
            $_OMP_get['action'] == 'editline') {
            $_OMP_html['client_link'] = $_OMP_rec['client_pkey'];
            $_OMP_html['supplier_link'] = $_OMP_rec['supplier_pkey'];
            $_OMP_html['enduse_link'] = $_OMP_rec['enduse_pkey'];
            return;
        }
        if (!$_OMP_get['popup']) {
            /* OMP_popLink is defined in base.php */
            $_OMP_html['client_link'] = OMP_popLink(
                $_OMP_TPL[9], OMP_PATH.'clients.php?q='.
                base64_encode('filter=1&pkey='.
                urlencode(html_entity_decode($_OMP_rec['client_pkey']))),
                    $_OMP_rec['client_pkey']);
            $_OMP_html['supplier_link'] = OMP_popLink(
                $_OMP_TPL[9], OMP_PATH.'suppliers.php?q='.
                base64_encode('filter=1&pkey='.
                urlencode(html_entity_decode($_OMP_rec['supplier_pkey']))),
                    $_OMP_rec['supplier_pkey']);
            $_OMP_html['enduse_link'] = OMP_popLink(
                $_OMP_TPL[9], OMP_PATH.'enduses.php?q='.
                base64_encode('filter=1&pkey='.
                urlencode(html_entity_decode($_OMP_rec['enduse_pkey']))),
                    $_OMP_rec['enduse_pkey']);
            if (is_array($_OMP_db->queryRow(
                "SELECT \"IDOrdine\" from \"OrdiniAppunti\"
                WHERE \"IDOrdine\" = '".$_OMP_rec['pkey']."'"))) {
                    /* OMP_popLink is defined in base.php */
                    $_OMP_LC[724] = OMP_popLink(
                        $_OMP_TPL[9], OMP_PATH.'ordersnotes.php?q='.
                        base64_encode('list=1&filter=1&pkey='.
                        urlencode(html_entity_decode($_OMP_rec['pkey']))),
                            $_OMP_LC['724']);
                } else {
                    $_OMP_LC[724] = OMP_popLink(
                        $_OMP_TPL[9], OMP_PATH.'ordersnotes.php?q='.
                        base64_encode('action=new&filter=1&pkey='.
                        urlencode(html_entity_decode($_OMP_rec['pkey'])).
                        '&list=1'), $_OMP_LC['724']);
                }
        } else {
            $_OMP_html['client_link'] = $_OMP_rec['client_pkey'];
            $_OMP_html['supplier_link'] = $_OMP_rec['supplier_pkey'];
            $_OMP_html['enduse_link'] = $_OMP_rec['enduse_pkey'];
            $_OMP_LC[724] = '';
        }
        if ($_OMP_get['list']) {
            $_OMP_html['rec0'] = $_OMP_rec['pkey'];
            $_OMP_html['rec1'] = $_OMP_rec['client_pkey'];
            $_OMP_html['rec2'] = $_OMP_rec['supplier_pkey'];
            $_OMP_html['rec3'] = $_OMP_rec['date'];
            $_OMP_html['label0'] = '';
            $_OMP_html['label1'] = $_OMP_rec['client_name'];
            $_OMP_html['label2'] = $_OMP_rec['supplier_name'];
            $_OMP_html['label3'] = '';
            /* mdc-list */
            /* or mdc-data-table__cell--numeric */
            $_OMP_html['cell1_numeric'] = '';
            $_OMP_html['cell2_numeric'] = '';
            $_OMP_html['cell3_numeric'] = '';
            $_OMP_html['cell4_numeric'] = '';
        } else {
            // Change option buttons template with extra Print option
            $_OMP_TPL[7] = $_OMP_TPL[$_OMP_conf['record_template']];
            $_OMP_rec['del_instr'] = nl2br($_OMP_rec['del_instr'], false);
            $_OMP_rec['ship_instr'] = nl2br($_OMP_rec['ship_instr'], false);
            $_OMP_rec['note'] = nl2br($_OMP_rec['note'], false);
        }
    } elseif ($_OMP_get['action'] == 'edit') { // HTML variables
        $_OMP_rec['date'] = strftime('%F', strtotime($_OMP_rec['date']));
        // if ($_OMP_rec['um'] == 'kg') {
        //     $_OMP_rec['um_kg'] = 'selected="selected"';
        //     $_OMP_rec['um_lbs'] = '';
        // } else {
        //     $_OMP_rec['um_lbs'] = 'selected="selected"';
        //     $_OMP_rec['um_kg'] = '';
        // }
        if ($_OMP_rec['curr'] == 'EUR') {
            $_OMP_rec['curr_eur'] = 'selected="selected"';
            $_OMP_rec['curr_usd'] = '';
        } else {
            $_OMP_rec['curr_usd'] = 'selected="selected"';
            $_OMP_rec['curr_eur'] = '';
        }
        if ($_OMP_rec['printed'] == 't') {
            $_OMP_rec['printed'] = true;
            $_OMP_rec['printed_true'] = 'selected="selected"';
            $_OMP_rec['printed_false'] = '';
        } else {
            $_OMP_rec['printed'] = false;
            $_OMP_rec['printed_false'] = 'selected="selected"';
            $_OMP_rec['printed_true'] = '';
        }
        if ($_OMP_rec['amended'] == 't') {
            $_OMP_rec['amended'] = true;
            $_OMP_rec['amended_true'] = 'selected="selected"';
            $_OMP_rec['amended_false'] = '';
        } else {
            $_OMP_rec['amended'] = false;
            $_OMP_rec['amended_false'] = 'selected="selected"';
            $_OMP_rec['amended_true'] = '';
        }
        if ($_OMP_rec['closed'] == 't') {
            $_OMP_rec['closed'] = true;
            $_OMP_rec['closed_true'] = 'selected="selected"';
            $_OMP_rec['closed_false'] = '';
        } else {
            $_OMP_rec['closed'] = false;
            $_OMP_rec['closed_false'] = 'selected="selected"';
            $_OMP_rec['closed_true'] = '';
        }
        if ($_OMP_rec['cancelled'] == 't') {
            $_OMP_rec['cancelled'] = true;
            $_OMP_rec['cancelled_true'] = 'selected="selected"';
            $_OMP_rec['cancelled_false'] = '';
        } else {
            $_OMP_rec['cancelled'] = false;
            $_OMP_rec['cancelled_false'] = 'selected="selected"';
            $_OMP_rec['cancelled_true'] = '';
        }
    } elseif ($_OMP_get['action'] == 'print') {
        $_OMP_rec['date'] = strftime('%x', strtotime($_OMP_rec['date']));
        $_OMP_rec['supplier_ref'] = (empty($_OMP_rec['supplier_ref']))
                ? $_OMP_rec['pkey'] : $_OMP_rec['supplier_ref'];
        if (OMP_MONTH_INV &&
            strcasecmp($_OMP_rec['supplier_pkey'], 'wellman ltd') == 0 &&
            strcasecmp($_OMP_rec['client_pkey'], 'fiberweb') <> 0  &&
            strcasecmp($_OMP_rec['client_pkey'], 'albis intl') <> 0) {
              //   $_OMP_rec['del_instr'] .=
                // ' - TO BE INVOICED IN THE SAME MONTH OF THE ARRIVAL DATE';
        }
    }
}

/**
* Process the array $GLOBALS['record_sf']
*
*/
function OMP_makeVarsLine($ts = false)
{
    global $_OMP_html, $_OMP_get,
        $_OMP_rec_sf, $_OMP_TPL, $_OMP_LC;
    $_OMP_rec_sf = array_map('OMP_htmlentities', $_OMP_rec_sf);
    $ts = ($ts) ? OMP_DB_THOUSANDS_SEP: $_SESSION['ts'];
    if ($_OMP_get['action'] == 'read') {
        $url = OMP_PATH.'products.php?q='.
            base64_encode(
                'filter=1&pkey='.
                $_OMP_rec_sf['prod_pkey']
            );
        $_OMP_html['product_link'] =
            OMP_popLink(
                $_OMP_TPL[9],
                $url,
                $_OMP_rec_sf['prod_pkey']
            );
    }
    if ($_OMP_get['action'] == 'print') {
        $_OMP_rec_sf['quantity'] =
            number_format($_OMP_rec_sf['quantity'], 2, '.', ',');
        $_OMP_rec_sf['price_net'] = 
            number_format($_OMP_rec_sf['price_net'], 2, '.', ',');
        $_OMP_rec_sf['price'] = 
            number_format($_OMP_rec_sf['price'], 2, '.', ',');
        $_OMP_rec_sf['discount'] = 
            number_format($_OMP_rec_sf['discount']*100, 0, '.', ',');
        return;
    }
    $_OMP_rec_sf['quantity'] =
        number_format($_OMP_rec_sf['quantity'], 2, $_SESSION['dp'], $ts);
    $_OMP_rec_sf['price_net'] =
        number_format($_OMP_rec_sf['price_net'], 2, $_SESSION['dp'], $ts);
    $_OMP_rec_sf['price'] =
        number_format($_OMP_rec_sf['price'], 2, $_SESSION['dp'], $ts);
    $_OMP_rec_sf['price_eur'] =
        number_format($_OMP_rec_sf['price_eur'], 2, $_SESSION['dp'], $ts);
    $_OMP_rec_sf['price_net_eur'] = 
        number_format(
            $_OMP_rec_sf['price_net_eur'], 
            2, 
            $_SESSION['dp'], 
            $_SESSION['ts']
        );
    $_OMP_rec_sf['production'] = 
        strftime(
            '%B %Y', 
            mktime(0, 0, 0, $_OMP_rec_sf['month'], 1, $_OMP_rec_sf['year'])
        );
    !$ts or $_OMP_rec_sf['eta'] = 
        ($_OMP_rec_sf['eta'] == '') ? '' : 
            strftime('%x', strtotime($_OMP_rec_sf['eta']));
    // Need this in edit-line as we use both discount and discount_curr
    if ($_OMP_rec_sf['discount'] > 0) {
        $_OMP_rec_sf['discount_curr'] = 
        number_format(
            $_OMP_rec_sf['discount'] * 100, 
            2, 
            $_SESSION['dp'], $_SESSION['ts']
        );
        $_OMP_rec_sf['discount'] = $_OMP_rec_sf['discount_curr'].'%';
        if ($_OMP_get['action'] == 'read') {
            $_OMP_rec_sf['price'] = $_OMP_rec_sf['price'].'-<br>'.
                $_OMP_rec_sf['discount'].'=<br>'.
                $_OMP_rec_sf['price_net'];
        }
    } else {
        $_OMP_rec_sf['discount'] = $_OMP_rec_sf['discount_curr'] = '';
    }
    if ($_OMP_get['action'] == 'delline') {
        $_OMP_rec_sf['master_date'] =
            strftime('%x', strtotime($_OMP_rec_sf['master_date']));
    }
}
/**
* Init new line variables
*
*/
function OMP_newLine()
{
    global $_OMP_db, $_OMP_get, $_OMP_products_fld, $_OMP_rec, $_OMP_rec_sf, 
        $_OMP_rec_orig, $_OMP_tables, $_OMP_tbl, $_OMP_tbl_fld, 
        $_OMP_tbl_fld_line, $_OMP_tbl_line;
    !empty($_OMP_rec_sf['month']) or 
        $_OMP_rec_sf['month'] = strftime('%m');
    !empty($_OMP_rec_sf['year']) or 
        $_OMP_rec_sf['year'] = strftime('%Y');
    !empty($_OMP_rec_sf['eta']) or 
        $_OMP_rec_sf['eta'] = $_OMP_rec_orig['date'];
    $_OMP_rec_sf['plan'] = $_OMP_rec_sf['year'].'-'.
        str_pad($_OMP_rec_sf['month'], 2, '0', STR_PAD_LEFT);
    $_OMP_rec_sf['discount_curr'] = '';
    if (!empty($_OMP_rec_sf['prod_pkey']) && 
        $_OMP_get['action'] == 'newdetail') {
        $sql_get_last_price = 'SELECT ol.'.
            $_OMP_tbl_fld_line['price'].' AS price, '.
            $_OMP_tbl_fld_line['quantity'].' AS quantity FROM '.
            $_OMP_tables[$_OMP_tbl_line].' AS ol LEFT JOIN '.
            $_OMP_tables[$_OMP_tbl].' AS oi ON (ol.'.
            $_OMP_tbl_fld_line['oi_pkey'].' = oi.'.
            $_OMP_tbl_fld['pkey'].') WHERE ol.'.
            $_OMP_tbl_fld_line['prod_pkey'].' = '.
            OMP_db_quote($_OMP_rec_sf['prod_pkey']).' AND oi.'.
            $_OMP_tbl_fld['client_pkey'].' = '.
            OMP_db_quote($_OMP_rec['client_pkey']).' AND oi.'.
            $_OMP_tbl_fld['cancelled'].' = \'No\' ORDER BY oi.'.
            $_OMP_tbl_fld['date'].' DESC LIMIT 1';
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
/* Process detail records */
$_OMP_has_subform = true;
/* See makeSql */
$_OMP_table_alias = 'oi.';
/* Sub record default sort */
$_OMP_sort_default_sub = '0';
/* Sub record default sort order */
$_OMP_sort_type_default_sub = 0;
/* See template 700, 721, 722, 724 */
$_OMP_html['note_display'] =
    $_OMP_html['status_display'] =
        $_OMP_html['note_header'] =
            $_OMP_html['note_record'] ='';
$_OMP_title = 41;
$_OMP_headline = '';
switch ($_OMP_get['action']) {
    /**
    * Read record
    */
    case 'read': {
        $_OMP_tpl = OMP_TPL_READ.'15, 16, 65, 66, 67,
            68, 700, 707, 708, 720, 721, 722';
        $_OMP_lcl = OMP_LCL_READ.'23, 24, 39, 41, 42,
            43, 52, 98, 99, 100, 105, 106, 110,
            504, 600, 604, 700, 703, 704, 705, 706,
            707, 708, 710, 711, 712, 713, 714, 715,
            716, 717, 718, 719, 720, 724, 1401';
        $_OMP_sql['sort_default'] =
            ' ORDER BY oi.'.$_OMP_tbl_fld['date'].' ?, oi.'.
            $_OMP_tbl_fld['pkey'].' DESC';
        $_OMP_sql['sort_record'][0] =
            $_OMP_sql['sort_list'][0] = ' ORDER BY oi.'.
            $_OMP_tbl_fld['pkey'].' ?';
        $_OMP_sql['sort_record'][1] =
            $_OMP_sql['sort_list'][1] = ' ORDER BY oi.'.
                $_OMP_tbl_fld['client_pkey'].' ?, oi.'.
                $_OMP_tbl_fld['date'].', oi.'.
                $_OMP_tbl_fld['supplier_pkey'];
        $_OMP_sql['sort_record'][2] =
            $_OMP_sql['sort_list'][2] = ' ORDER BY oi.'.
            $_OMP_tbl_fld['supplier_pkey'].' ?';
        $_OMP_sql['sort_record'][3] = $_OMP_sql['sort_list'][3] =
            $_OMP_sql['sort_default'];
        // $_OMP_sort_idx contains the keys for $_OMP_sort_list as keys
        // and the keys for $_OMP_LC as values
        // $_OMP_sort_list will be created in in read.php
        $_OMP_sort_idx = array(700, 100, 504, 703);
        if ($_OMP_get['list']) {
            $_OMP_list_tpl = 60;
            $_OMP_list_header_tpl = 65;
            $_OMP_list_rec_tpl = 66;
            $_OMP_list_wrapper = '';
            /* mdc table */
            // or mdc-data-table__header-cell--numeric"
            $_OMP_html['header1_numeric'] = '';
            $_OMP_html['header2_numeric'] = '';
            $_OMP_html['header3_numeric'] = '';
            $_OMP_html['header4_numeric'] = '';
        } else {
            $_OMP_rec_tpl = 700;
            $_OMP_admin_tpl = 68;
            $_OMP_subform_tpl = 67;
            $_OMP_sub_rec_tpl = 708;
            $_OMP_sub_header_tpl = 707;
            // See above for $_OMP_sort_idx
            $_OMP_sub_sort_idx = array(600, 716, 720);
            $_OMP_opt_buttons_tpl = 15;
        }
        $_OMP_sort['default'] = '3'; // Master record default sort
        $_OMP_sort['type'] = 1; // Master record default sort order
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
        $_OMP_tpl = OMP_TPL_FILTER.'701, 723, 724,
            725, 726, 727, 728, 729, 730';
        $_OMP_lcl = OMP_LCL_FILTER.'41, 100, 105,
            106, 110, 116, 118, 504, 700, 703,
            704, 705, 706, 707, 708, 710, 711,
            712, 713, 714, 715, 726, 1401';
        /* SQL queries for drop-down lists */
        $_OMP_get['popup'] or require_once 'lib/dd-sql.php';
        /* drop-down lists array */
        $_OMP_drop_down = array(
            'lib/mdc-select-enduses.php',
            'lib/mdc-select-payments.php',
            'lib/mdc-select-terms.php',
            'lib/mdc-select-currencies.php',
            'lib/mdc-select-um.php',
        );
        if ($_SESSION['cat'] == 0) {
            /* Filter for clients */
            $_OMP_client_combo =
                '<input disabled type="text" name="client_pkey" id="client_pkey" '.
                'size="10" value="'.OMP_htmlentities($_SESSION['id']).'" /> '.
                '<input type="hidden" name="form[enc_client_pkey]" '.
                'id="form[enc_client_pkey]" value="'.
                OMP_htmlentities($_SESSION['id']).'" />';
        } elseif ($_SESSION['cat'] == 1) {
            /* Filter for suppliers */
            $_OMP_supplier_combo =
                '<input disabled type="text" name="supplier_pkey" '.
                'id="supplier_pkey" '.'size="10" value="'.
                OMP_htmlentities($_SESSION['id']).'" /> '.
                '<input type="hidden" name="form[enc_supplier_pkey]" '.
                'id="form[enc_supplier_pkey]" value="'.
                OMP_htmlentities($_SESSION['id']).'" />';
        }
        $_OMP_drop_down[] = 'lib/mdc-select-suppliers.php';
        $_OMP_drop_down[] = 'lib/mdc-select-clients.php';
        $_OMP_page_title_lcl = 41;
        $_OMP_include_tpl = 701;
        /* set variables for drop-down lists */
        $_OMP_html['printed_combo'] = $_OMP_html['amended_combo'] =
        $_OMP_html['closed_combo'] = $_OMP_html['cancelled_combo'] = '';
        $_OMP_rec['printed'] = $_OMP_rec['amended'] = $_OMP_rec['closed'] =
        $_OMP_rec['cancelled'] = '';
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
        $_OMP_tpl = OMP_TPL_EDIT.'704, 723, 724, 725,
            726, 727, 728, 729, 730';
        $_OMP_lcl = OMP_LCL_EDIT.'41, 100, 105, 106,
            110, 116, 118, 504, 700, 703, 704, 705,
            706, 707, 708, 710, 711, 712, 713, 715,
            726, 1401';
        // Check if insert-button was pushed
        if (isset($_POST['insert_button'])) {
            $_OMP_sql['update'] = 'UPDATE '.
                $_OMP_tables[$_OMP_tbl].' SET '.
                $_OMP_tbl_fld['client_pkey'].' = ?, '.
                $_OMP_tbl_fld['supplier_pkey'].' = ?, '.
                $_OMP_tbl_fld['date'].' = ?, '.
                $_OMP_tbl_fld['ref'].' = ?, '.
                $_OMP_tbl_fld['client_ref'].' = ?, '.
                $_OMP_tbl_fld['supplier_ref'].' = ?, '.
                $_OMP_tbl_fld['printed'].' = ?, '.
                $_OMP_tbl_fld['amended'].' = ?, '.
                $_OMP_tbl_fld['closed'].' = ?, '.
                $_OMP_tbl_fld['cancelled'].' = ?, '.
                $_OMP_tbl_fld['enduse_pkey'].' = ?, '.
                $_OMP_tbl_fld['del_instr'].' = ?, '.
                $_OMP_tbl_fld['ship_instr'].' = ?, '.
                $_OMP_tbl_fld['note'].' = ?, '.
                $_OMP_tbl_fld['paymnt_pkey'].' = ?, '.
                $_OMP_tbl_fld['term_pkey'].' = ?, '.
                $_OMP_tbl_fld['paymnt_days'].' = ?, '.
                $_OMP_tbl_fld['curr'].' = ?, '.
                $_OMP_tbl_fld['um'].' = ? WHERE '.
                $_OMP_tbl_fld['pkey'].' = ?';
            $_OMP_datatypes = array(
                'text',
                'text',
                'date',
                'text',
                'text',
                'text',
                'boolean',
                'boolean',
                'boolean',
                'boolean',
                'text',
                'text',
                'text',
                'text',
                'text',
                'integer',
                'integer',
                'text',
                'text',
                'text'
            );
        } else {
            $_OMP_datatypes = array('text');
            /* see dd-sql.php */
            /* Query all clients - if order is old
             * it may have an inactive client
            $_OMP_sql['clients_combo'] = 'SELECT '.
                $_OMP_clients_fld['pkey'].' AS pkey FROM '.
                $_OMP_tables['clients'].
                ' ORDER BY '.$_OMP_clients_fld['pkey']; */
            // SQL queries for drop-down lists
            $_OMP_combo_required = true;
            $_OMP_get['popup'] or require_once 'lib/dd-sql.php';
            $_OMP_drop_down = array(
                'lib/mdc-select-clients.php',
                'lib/mdc-select-enduses.php',
                'lib/mdc-select-payments.php',
                'lib/mdc-select-terms.php',
                'lib/mdc-select-um.php',
                'lib/mdc-select-currencies.php'
            );
            $_OMP_input_tpl = 704;
            $_OMP_edit_tpl = 51;
            $_OMP_page_title_lcl = 41;
            /* switches and scripts to set their value */
            $_OMP_printed_switch = true;
            $_OMP_printed_switch_locale = 711;
            $_OMP_amended_switch = true;
            $_OMP_amended_switch_locale = 712;
            $_OMP_closed_switch = true;
            $_OMP_closed_switch_locale = 713;
            $_OMP_cancelled_switch = true;
            $_OMP_cancelled_switch_locale = 715;
        }
        require 'lib/edit.php';
        break;
    }
    /**
    * End edit record
    */

    /**
    * New orders for multiple arrival dates
    */
    case 'multi': {
        $_OMP_tpl = OMP_TPL_NEW.'716, 717, 718, 719, 720';
        $_OMP_lcl = OMP_LCL_NEW.'100, 106, 120, 121, 600, 604,
            617, 703, 705, 707, 708, 711, 712, 713, 715, 717,
            720, 4000';
        // Check if insert-button was pushed
        if (isset($_POST['insert_button'])) {
            $_OMP_tplg = '126';
            $_OMP_lcl .= OMP_LCL_READ.'700, 716'; // from print
            // Gets id of new order for redirect
            $_OMP_sql['get_last'] = 'SELECT pkey FROM orders_log WHERE "user" = ';
            OMP_load(); // @see base.php
            // Lookup end-use, payment, payment days, accrue date
            // from client table and add to POST[FORM]
            $_OMP_sql['sql_cli_details'] = 'SELECT '.
                $_OMP_clients_fld['enduse_pkey'].' AS txt_enduse_pkey, '.
                $_OMP_clients_fld['paymnt_pkey'].' AS int_paymnt_pkey, '.
                $_OMP_clients_fld['term_pkey'].' AS int_term_pkey, '.
                $_OMP_clients_fld['paymnt_days'].' AS int_paymnt_days FROM '.
                $_OMP_tables['clients'].' WHERE '.
                $_OMP_clients_fld['pkey'].' = '.
                OMP_db_quote(urldecode($_POST['form']['enc_client_pkey']));
            $cli_details = $_OMP_db->queryRow($_OMP_sql['sql_cli_details']);
            $_POST['form'] = array_merge($_POST['form'], $cli_details);
            // fetch product_label that will be added to $_OMP_data_ol array
            // and supplier_pkey that will be added to $_POST['form']
            $_OMP_sql['prod_details'] = 'SELECT '.
                $_OMP_products_fld['description'].' AS prod_label, '.
                $_OMP_products_fld['supplier_pkey'].' AS supp_pkey FROM '.
                $_OMP_tables['products'].' WHERE '.
                $_OMP_products_fld['pkey'].' = '.
                OMP_db_quote(urldecode($_POST['form']['txt_prod_pkey']));
            $product_details = $_OMP_db->queryRow($_OMP_sql['prod_details']);
            $product_label = $product_details['prod_label'];
            $supplier_pkey = $product_details['supp_pkey'];
            // Lookup currency, UM from supplier table and add to POST[FORM]
            $_OMP_sql['sql_sup_details'] = 'SELECT '.
                $_OMP_suppliers_fld['um'].' AS int_um, '.
                $_OMP_suppliers_fld['curr'].' AS txt_curr FROM '.
                $_OMP_tables['suppliers'].' WHERE '.
                $_OMP_suppliers_fld['pkey'].' = '.
                OMP_db_quote(urldecode($supplier_pkey));
            $sup_details = $_OMP_db->queryRow($_OMP_sql['sql_sup_details']);
            $_POST['form'] = array_merge($_POST['form'], $sup_details);
            $_POST['form']['enc_supplier_pkey'] = $supplier_pkey;
            // makes array of arrival dates
            $date_arr =
                preg_split('/[\s,;:]+/',
                    $_POST['form']['schedule'], -1, PREG_SPLIT_NO_EMPTY);
            unset($_POST['form']['schedule']);
            // prepares the SQL statement for orders
            $_OMP_sql['insert'] = 'INSERT INTO '.
                $_OMP_tables[$_OMP_tbl].' ('.
                $_OMP_tbl_fld['date'].', '.
                $_OMP_tbl_fld['supplier_pkey'].', '.
                $_OMP_tbl_fld['client_pkey'].', '.
                $_OMP_tbl_fld['client_ref'].', '.
                $_OMP_tbl_fld['ship_instr'].', '.
                $_OMP_tbl_fld['note'].', '.
                $_OMP_tbl_fld['del_instr'].', '.
                $_OMP_tbl_fld['enduse_pkey'].', '.
                $_OMP_tbl_fld['paymnt_pkey'].', '.
                $_OMP_tbl_fld['term_pkey'].', '.
                $_OMP_tbl_fld['paymnt_days'].', '.
                $_OMP_tbl_fld['supplier_ref'].', '.
                $_OMP_tbl_fld['curr'].', '.
                $_OMP_tbl_fld['um'].', '.
                $_OMP_tbl_fld['user'].
                ') VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $_OMP_datatypes = array(
                'date',
                'text',
                'text',
                'text',
                'text',
                'text',
                'text',
                'text',
                'integer',
                'text',
                'integer',
                'text',
                'text',
                'text',
                'text');
            $_OMP_prepared =
                $_OMP_db->prepare($_OMP_sql['insert'], $_OMP_datatypes);
            // Order info html template fields sorted as $_OMP_data_oi
            // Extra fields note, paymnt_pkey, term_pkey, supplier_ref, user
            // are not required to print order html template
            $oi_keys = array('date', 'supplier_name', 'client_pkey', 'client_ref',
                'ship_instr', 'note', 'del_instr', 'enduse_pkey', 'paymnt_pkey',
                'term_pkey', 'paymnt_days', 'supplier_ref', 'curr', 'um',
                'user');
            // lookup paymnt_name, term_name and enduse_label
            // that will be added to the $_OMP_data_oi array
            $_OMP_sql['payment_info'] = 'SELECT '.
                $_OMP_payments_fld['name'].' AS paymnt_name FROM '.
                $_OMP_tables['payments'].' WHERE '.
                $_OMP_payments_fld['pkey'].' = '.
                $_POST['form']['int_paymnt_pkey'];
            $payment_name = $_OMP_db->queryOne($_OMP_sql['payment_info']);
            $_OMP_sql['terms_info'] = 'SELECT '.
                $_OMP_terms_fld['name'].' AS term_name FROM '.
                $_OMP_tables['terms'].' WHERE '.
                $_OMP_terms_fld['pkey'].' = '.
                $_POST['form']['int_term_pkey'];
            $terms_name = $_OMP_db->queryOne($_OMP_sql['terms_info']);
            $_OMP_sql['enduse_info'] = 'SELECT '.$_OMP_enduses_fld['name'].
                ' AS enduse_label FROM '.$_OMP_tables['enduses'].
                ' WHERE '.$_OMP_enduses_fld['pkey'].' = '.
                OMP_db_quote($_POST['form']['txt_enduse_pkey']);
            $enduse_pkey = $_OMP_db->queryOne($_OMP_sql['enduse_info']);
            // insert new order records looping through arrival dates
            $i = 0;
            foreach ($date_arr as $rtas) {
                // save Post[form] because it changes in OMP_makeData
                $form = $_POST['form'];
                // Lookup supplier ref and add to POST[FORM]
                $_POST['form']['nul_supplier_ref'] =
                    OMP_supplierRef($supplier_pkey);
                $_POST['form']['txt_del_instr'] .= $rtas;
                $_OMP_data = OMP_makeData($_OMP_sql['insert'], true);
                $_OMP_prepared->execute($_OMP_data);
                $_OMP_data_oi[$i] = array_combine($oi_keys, $_OMP_data);
                // fetch additional fields
                $_OMP_data_oi[$i]['paymnt_name'] = $payment_name;
                $_OMP_data_oi[$i]['term_name'] = $terms_name;
                $_OMP_data_oi[$i]['enduse_label'] = $enduse_pkey;
                // fetch order keys needed for inserting order lines
                $order_pkey[] = $_OMP_db->queryOne($_OMP_sql['get_last'].
                    OMP_db_quote($_SESSION['id']), 'text');
                $_POST['form'] = $form; // return POST[form] to its original value
                $i++;
            } // end of loop through arrival dates
            // prepares the SQL statement for orders lines
            $_OMP_sql['insert_line'] = 'INSERT INTO '.
                $_OMP_tables[$_OMP_tbl_line].' ('.
                $_OMP_tbl_fld_line['prod_pkey'].', '.
                $_OMP_tbl_fld_line['price'].', '.
                $_OMP_tbl_fld_line['quantity'].', '.
                $_OMP_tbl_fld_line['oi_pkey'].', '.
                $_OMP_tbl_fld_line['year'].', '.
                $_OMP_tbl_fld_line['month'].', '.
                $_OMP_tbl_fld_line['eta'].', '.
                $_OMP_tbl_fld_line['plan'].
                ') VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
            $_OMP_datatypes = array('text', 'decimal', 'decimal',
                'text', 'integer', 'integer', 'date', 'date');
            $_OMP_prepared =
                $_OMP_db->prepare($_OMP_sql['insert_line'], $_OMP_datatypes);
            // creates array with order pkeys and arrival dates
            $ol_eta = array_combine($order_pkey, $date_arr);
            // taken from new-line.php
            $_OMP_tmp_a = $_OMP_tbl_fld;
            $_OMP_tmp_b = $_OMP_fld_len;
            $_OMP_tbl_fld = $_OMP_tbl_fld_line;
            $_OMP_fld_len = $_OMP_fld_len_line;
            setlocale(LC_NUMERIC, $_SESSION['locale']);
            // end of code from new-line.php
            // Order line html template fields sorted as $_OMP_data_ol
            // Extra fields oi_pkey, year, month, eta and plan
            // are not required to print order html template
            $ol_keys = array('prod_pkey', 'price', 'quantity', 'oi_pkey',
                'year', 'month', 'eta', 'plan');
            // insert new order line records looping through arrival dates
            $i = 0;
            foreach ($ol_eta as $pkey => $eta) {
                // Post[form] is changed in OMP_makeData so we save it
                $form = $_POST['form'];
                // add elements for Post[form]
                $_POST['form']['txt_oi_pkey'] = $pkey;
                $eta_formatted = OMP_checkDateTxt($eta, 'ETA');
                $eta_array = explode("-", $eta_formatted);
                $_POST['form']['txt_year'] = $eta_array[0];
                $_POST['form']['txt_month'] = $eta_array[1];
                $_POST['form']['eta'] = $eta_formatted;
                $_POST['form']['txt_plan'] =
                    $_POST['form']['txt_year'].'-'.
                    $_POST['form']['txt_month'].'-01';
                // taken from new-line.php
                $_OMP_data = OMP_makeData($_OMP_sql['insert_line'], true);
                $_OMP_prepared->execute($_OMP_data); // Insert new order lines
                $_OMP_data_ol[$i] = array_combine($ol_keys, $_OMP_data);
                $_OMP_data_ol[$i]['prod_label'] = $product_label;
                // return POST[form] to its original value
                $_POST['form'] = $form;
                $i++;
            } // end of loop through arrival dates
            // taken from new-line.php
            // Go back to original values
            $_OMP_tbl_fld = $_OMP_tmp_a;
            $_OMP_fld_len = $_OMP_tmp_b;
            // end of code from new-line.php
            $_OMP_prepared->free();
            // taken from read.php
            $_OMP_html['include'] = '';
            // order info template
            $_OMP_html['att'] = 'Denise Govern';
            $_OMP_html['cc'] = 'Claus Petersen';
            $_OMP_html['discount'] = ''; // See template 719
            $_OMP_title = 41;
            $_OMP_has_script = false; // Popup-window script
            $_OMP_html['subform'] = '';
            // end of code from print
            $_OMP_out = '';
            $_OMP_html['pagebreak'] = '';
            $pages = count($_OMP_data_oi) - 1;
            // loop orders
            foreach ($_OMP_data_oi as $i => $order) {
                $_OMP_rec = $order;
                $_OMP_rec['date'] = strftime('%x', strtotime($_OMP_rec['date']));
                // order line
                $_OMP_rec_sf = $_OMP_data_ol[$i];
                $_OMP_rec_sf['quantity'] =
                    number_format($_OMP_rec_sf['quantity'], 2, '.', ',');
                $_OMP_rec_sf['price'] =
                    number_format($_OMP_rec_sf['price'], 2, '.', ',');
                // taken from read.php
                // eval("\$_OMP_html['list'] = \"".
                //     $_OMP_TPL[718]."\";"); // subform header
                $tmp_var_a = $_OMP_html['list'];
                $tmp_var_b = $_OMP_rec_sf;
                // eval("\$_OMP_html['list'] .= \"".
                //     $_OMP_TPL[719]."\";"); // record subform
                $_OMP_html['list'] = $tmp_var_a;
                $_OMP_rec_sf = $tmp_var_b;
                // original from read.php
                //$_OMP_rec_sf['prod_label'] .=
                //str_repeat('<br />', (27 - $tmp_var_c));
                // updated
                $_OMP_rec_sf['prod_label'] .= str_repeat('<br />', 26);
                /* Order Print Sub Header */
                eval("\$_OMP_html['table_body'] = \"".
                    $_OMP_TPL[718]."\";");
                /* Order Print Sub Record */
                eval("\$_OMP_html['table_body'] .= \"".
                    $_OMP_TPL[719]."\";");
                // eval("\$_OMP_html['subform'] = \"".
                //     $_OMP_TPL[716]."\";"); // subform
                if ($i == $pages) { // pagebreak
                    $_OMP_html['pagebreak'] = '';
                } else {
                    eval("\$_OMP_html['pagebreak'] = \"".
                        $_OMP_TPL[720]."\";");
                }
                eval("\$_OMP_html['include'] = \"".$_OMP_TPL[717].
                    "\";"); // record
                // end order line
                $_OMP_html['browser_title'] = $_OMP_rec['client_pkey'].
                    ' '.$_OMP_rec['client_ref'];
                eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
                // end of code from read.php
            } // end loop orders
            $_OMP_db->disconnect();
            $_OMP_out = stripslashes($_OMP_out);
            $filename_html = 'order.html';
            $handle = fopen($filename_html, 'w');
            fwrite($handle, $_OMP_out);
            fclose($handle);
            /* Redirect browser */
            header('Location: '.OMP_PATH.$filename_html);
            exit;
        } else {
            // SQL queries for drop-down lists
            $_OMP_get['popup'] or require_once 'lib/dd-sql.php';
            $_OMP_combo_required = true;
            $_OMP_drop_down = array(
                'lib/mdc-select-clients.php',
                /* 'lib/mdc-select-products.php' */
                'lib/dd-products.php'
            );
            $_OMP_onchange = true;
            $_OMP_orders_multi = true;
            $_OMP_input_tpl = 717;
            $_OMP_include_tpl = 50;
            $_OMP_page_title_lcl = 41;
            /* avoid undefined index */
            $_OMP_rec['eta'] = '';
            /* avoid undefined index in evalued code */
            $_OMP_rec['quantity'] = '';
            require 'lib/new.php';
        }
        break;
    }
    /**
    * End multiple
    */

    /**
    * New record
    */
    case 'new': {
        $_OMP_tpl = OMP_TPL_NEW.'702, 723, 724';
        $_OMP_lcl = OMP_LCL_NEW.'41, 100, 105,
            106, 110, 116, 118, 504, 703, 704, 705,
            707, 708, 710, 711, 712, 713, 715, 726,
            1401';
        // Check if insert-button was pushed
        if (isset($_POST['insert_button'])) {
            $_OMP_sql['insert'] = 'INSERT INTO '.
                $_OMP_tables[$_OMP_tbl].' ('.
                $_OMP_tbl_fld['client_pkey'].', '.
                $_OMP_tbl_fld['supplier_pkey'].', '.
                $_OMP_tbl_fld['date'].', '.
                $_OMP_tbl_fld['client_ref'].', '.
                $_OMP_tbl_fld['supplier_ref'].', '.
                $_OMP_tbl_fld['enduse_pkey'].', '.
                $_OMP_tbl_fld['del_instr'].', '.
                $_OMP_tbl_fld['ship_instr'].', '.
                $_OMP_tbl_fld['note'].', '.
                $_OMP_tbl_fld['paymnt_pkey'].', '.
                $_OMP_tbl_fld['term_pkey'].', '.
                $_OMP_tbl_fld['paymnt_days'].', '.
                $_OMP_tbl_fld['curr'].', '.
                $_OMP_tbl_fld['um'].', '.
                $_OMP_tbl_fld['user'].
                ') VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $_OMP_datatypes = array(
                'text',
                'text',
                'date',
                'text',
                'text',
                'text',
                'text',
                'text',
                'text',
                'integer',
                'integer',
                'integer',
                'text',
                'text',
                'text'
            );
            // Gets id of new order
            $_OMP_sql['get_last'] = 'SELECT pkey FROM orders_log WHERE "user" = ';
        } else {
            // SQL queries for drop-down lists
            $_OMP_get['popup'] or require_once 'lib/dd-sql.php';
            $_OMP_combo_required = true;
            $_OMP_drop_down = array(
                'lib/mdc-select-clients.php',
                'lib/mdc-select-enduses.php',
                'lib/mdc-select-payments.php',
                'lib/mdc-select-terms.php',
                'lib/mdc-select-suppliers.php',
                'lib/dd-currencies.php',
                'lib/mdc-select-um.php',
                'lib/mdc-select-currencies.php'
            );
            $_OMP_onchange = true;
            $_OMP_input_tpl = 702;
            $_OMP_include_tpl = 50;
            $_OMP_page_title_lcl = 41;
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
        $_OMP_tpl = OMP_TPL_NEW.'700, 709, 710, 712, 713,
            721, 722';
        $_OMP_lcl = OMP_LCL_NEW.'23, 24, 41, 42, 47, 48,
            52, 82, 84, 98, 99, 100, 105, 106, 110,
            116, 118, 120, 121, 504, 600, 604, 700, 703,
            704, 705, 706, 707, 708, 710, 711, 712, 713,
            715, 716, 717, 718, 720, 724, 1401';
        OMP_load();
        // Check if insert-button was pushed
        if (isset($_POST['insert_button']) &&
            $_POST['insert_button'] == $_OMP_LC[39]) {
            $_OMP_html = '';
            $_OMP_change_post = true;
            $_OMP_sql['insert_line'] = 'INSERT INTO '.
                $_OMP_tables[$_OMP_tbl_line].' ('.
                $_OMP_tbl_fld_line['oi_pkey'].', '.
                $_OMP_tbl_fld_line['prod_pkey'].', '.
                $_OMP_tbl_fld_line['year'].', '.
                $_OMP_tbl_fld_line['month'].', '.
                $_OMP_tbl_fld_line['plan'].', '.
                $_OMP_tbl_fld_line['quantity'].', '.
                $_OMP_tbl_fld_line['price'].', '.
                $_OMP_tbl_fld_line['discount'].', '.
                $_OMP_tbl_fld_line['note'].', '.
                $_OMP_tbl_fld_line['eta'].
                ') VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $_OMP_datatypes = array('text', 'text', 'integer', 'integer',
                'date', 'decimal', 'decimal', 'decimal', 'text', 'date');
        } else {
            $_OMP_master_tpl = $_OMP_TPL[700];
            $_OMP_frame_tpl = $_OMP_TPL[713];
            $_OMP_input_tpl = $_OMP_TPL[709];
            $_OMP_header_tpl = $_OMP_TPL[710];
            $_OMP_rec_tpl = $_OMP_TPL[712];
            $sort_record[0] = $_OMP_LC[700];
            $sort_record[1] = $_OMP_LC[100];
            $sort_record[2] = $_OMP_LC[504];
            $sort_record[3] = $_OMP_LC[703];
            $_OMP_page_title_lcl = 41;
            // $_OMP_html['page_title'] =
            //     $_OMP_LC[41].' - '.$_OMP_LC[39].' '.$_OMP_LC[23];
            /* SQL queries for drop-down lists */
            $_OMP_get['popup'] or require_once 'lib/dd-sql.php';
            $_OMP_combo_required = true;
            $_OMP_onchange = true;
            $_OMP_drop_down = array('lib/dd-products.php');
            $_OMP_noitmes_msg = $_OMP_LC[47];
            $_OMP_subform_wrapper = 'wrapper7';
        }
        require 'lib/new-line.php';
        break;
    }
    /**
    * End new line record
    */

    /**
    * Recipient select
    */
    case 'recptsel': {
        $_OMP_tpl = '0, 2, 14, '.$_OMP_conf['filter_template'].', 71';
        $_OMP_lcl = OMP_LCL_FILTER.'26, 3001, 3002, 3003';
        // Supplier contacts schema
        require_once 'schemas/suppliercontacts-schema.php';
        $_OMP_rec['supplier_pkey'] = $_OMP_db->queryOne('SELECT '.
            $_OMP_tbl_fld['supplier_pkey'].' FROM '.$_OMP_tables[$_OMP_tbl].
            ' WHERE '.$_OMP_tbl_fld['pkey'].' = '.
            OMP_db_quote($_OMP_get['pk_pkey'], 'text'));
        $_OMP_sql['att_combo'] = 'SELECT '.$_OMP_suppliercontacts_fld['pkey'].
            ' AS pkey, '.$_OMP_suppliercontacts_fld['first_name'].' AS fname, '.
            $_OMP_suppliercontacts_fld['last_name'].' AS lname FROM '.
            $_OMP_tables['suppliercontacts'].
            ' WHERE '.$_OMP_suppliercontacts_fld['supplier_pkey'].
            ' = \''.$_OMP_rec['supplier_pkey'].'\' ORDER BY fname, lname';
        $_OMP_fav_supplier = $_OMP_db->queryRow('SELECT fav_supplier_to AS to,
            fav_supplier_cc AS cc FROM favourite_supplier_contacts
            WHERE username = '.OMP_db_quote($_SESSION['id'], 'text').'
            AND supplier_id = '.OMP_db_quote($_OMP_rec['supplier_pkey'], 'text'));
        $_OMP_drop_down = array(
            'lib/dd-supplierscontacts.php', 'lib/dd-supplierscontacts.php'
        );
        $_OMP_include_tpl = 71;
        /* _OMP_page_title is set in filter.php */
        /* $_OMP_page_title_lcl = 26; */
        require 'lib/filter.php';
        break;
    }
    /**
    * End recipient select
    */

    /**
    * Print record
    */
    case 'print': {
        $_OMP_tplg = '126';
        $_OMP_tpl = '0, 2, 716, 717, 718, 719';
        $_OMP_lcl = OMP_LCL_READ.'41, 100, 504, 600,
            700, 703, 713, 715, 716, 720';
        $_OMP_sql['sort_default'] = ' ORDER BY oi.'.
            $_OMP_tbl_fld['date'].' ?, oi.'.
            $_OMP_tbl_fld['client_pkey'];
        $_OMP_sql['sort_record'][0] =
            $_OMP_sql['sort_list'][0] = ' ORDER BY oi.'.
            $_OMP_tbl_fld['pkey'].' ?';
        $_OMP_sql['sort_record'][1] =
            $_OMP_sql['sort_list'][1] = ' ORDER BY oi.'.
            $_OMP_tbl_fld['client_pkey'].' ?, oi.'.
            $_OMP_tbl_fld['date'].', oi.'.
            $_OMP_tbl_fld['supplier_pkey'];
        $_OMP_sql['sort_record'][2] =
            $_OMP_sql['sort_list'][2] = ' ORDER BY oi.'.
            $_OMP_tbl_fld['supplier_pkey'].' ?';
        $_OMP_sql['sort_record'][3] = $_OMP_sql['sort_list'][3] =
            $_OMP_sql['sort_default'];
        $_OMP_sql['select_line'] = 'SELECT ol.'.
            $_OMP_tbl_fld_line['prod_pkey'].' AS prod_pkey, pd.'.
            $_OMP_products_fld['description'].' AS prod_label, SUM(ol.'.
            $_OMP_tbl_fld_line['quantity'].') AS quantity, AVG(ol.'.
            $_OMP_tbl_fld_line['discount'].') AS discount, AVG(ol.'.
            $_OMP_tbl_fld_line['price'].') AS price, AVG(ol.'.
            $_OMP_tbl_fld_line['price_net'].') AS price_net FROM '.
            $_OMP_tables[$_OMP_tbl_line].' AS ol LEFT OUTER JOIN '.
            $_OMP_tables['products'].' AS pd ON (ol.'.
            $_OMP_tbl_fld_line['prod_pkey'].' = pd.'.
            $_OMP_products_fld['pkey'].')';
        $_OMP_sql['row_line'] = $_OMP_sql['select_line'].' WHERE ol.'.
            $_OMP_tbl_fld_line['oi_pkey'].' = ? GROUP BY ol.'.
            $_OMP_tbl_fld_line['prod_pkey'].', pd.'.
            $_OMP_products_fld['description'];
        $_OMP_sql['sort_default_sub'] =
            $_OMP_sql['sort_record_sub'][0] =
            $_OMP_sql['sort_record_sub'][1] =
            $_OMP_sql['sort_record_sub'][2] =
            ' ORDER BY ol.'.$_OMP_tbl_fld_line['prod_pkey'];
        // $_OMP_sort_idx contains the keys for
        // $_OMP_sort_list as keys
        // and the keys for $_OMP_LC as values
        // $_OMP_sort_list will be created in in read.php
        $_OMP_sort_idx = array(700, 100, 504, 703);
        $_OMP_rec_tpl = 717;
        $_OMP_subform_tpl = 716;
        $_OMP_sub_rec_tpl = 719;
        $_OMP_sub_header_tpl = 718;
        $_OMP_html['logo'] = 'images/logo.png';
        $_OMP_html['att'] = $_POST['att'];
        $_OMP_html['cc'] = $_POST['cc'];
        // See above for $_OMP_sort_idx
        $_OMP_sub_sort_idx = array(600, 716, 720);
        $_OMP_title = 41;
        $_OMP_has_script = false; // Popup-window script
        $_OMP_sort['default'] = '3'; // Master record default sort
        $_OMP_sort['type'] = 1; // Master record default sort order
        $_OMP_html['subform'] = '';
        $_OMP_html['pagebreak'] = ''; // see read.php
        $_OMP_pdf = true;
        require 'lib/save-favs.php';
        require 'lib/read.php';
        break;
    }
    /**
    * End print record
    */

    /**
    * Edit line record
    */
    case 'editline': {
        $_OMP_tpl = OMP_TPL_EDIT.'9, 15, 700, 709,
            710, 712, 713, 721, 722';
        $_OMP_lcl = OMP_LCL_EDIT.'23, 24, 41,
            42, 52, 82, 98, 99, 100, 105,
            106,  110, 116, 118, 120, 121, 504,
            600, 604, 700, 703, 704, 705, 706,
            707, 708, 710, 711, 712, 713, 715,
            716, 717, 718, 720, 724, 1401';
        OMP_load();
        // Check if edit-button was pushed
        if (isset($_POST['insert_button']) &&
            $_POST['insert_button'] == $_OMP_LC[38]) {
            $_OMP_change_post = true;
            $_OMP_sql['update_line'] = 'UPDATE '.
                $_OMP_tables[$_OMP_tbl_line].' SET '.
                $_OMP_tbl_fld_line['prod_pkey'].' = ?, '.
                $_OMP_tbl_fld_line['year'].' = ?, '.
                $_OMP_tbl_fld_line['month'].' = ?, '.
                $_OMP_tbl_fld_line['plan'].' = ?, '.
                $_OMP_tbl_fld_line['quantity'].' = ?, '.
                $_OMP_tbl_fld_line['price'].' = ?, '.
                $_OMP_tbl_fld_line['discount'].' = ?, '.
                $_OMP_tbl_fld_line['note'].' = ?, '.
                $_OMP_tbl_fld_line['eta'].' = ? WHERE '.
                $_OMP_tbl_fld_line['pkey'].' = ?';
            $_OMP_datatypes = array('text', 'integer', 'integer',
                'date', 'decimal', 'decimal', 'decimal', 'text',
                'date', 'integer');
        } else {
            $_OMP_master_tpl = $_OMP_TPL[700];
            $_OMP_frame_tpl = $_OMP_TPL[713];
            $_OMP_input_tpl = $_OMP_TPL[709];
            $_OMP_header_tpl = $_OMP_TPL[710];
            $_OMP_rec_tpl = $_OMP_TPL[712];
            $sort_record[0] = $_OMP_LC[700];
            $sort_record[1] = $_OMP_LC[100];
            $sort_record[2] = $_OMP_LC[504];
            $sort_record[3] = $_OMP_LC[703];
            $_OMP_html['page_title'] = $_OMP_LC[41].' - '.
                $_OMP_LC[33].' '.$_OMP_LC[23];
            // Need un-filtered combo to list active and
            // inactive products
            $_OMP_sql['products_combo'] = 'SELECT '.
                $_OMP_products_fld['pkey'].
                ' AS pkey, active FROM '.
                $_OMP_tables['products'].' WHERE lower('.
                $_OMP_products_fld['supplier_pkey'].
                ') = lower(?) ORDER BY active DESC';
            $_OMP_drop_down = array('lib/dd-products.php');
            $_OMP_subform_wrapper = 'wrapper7';
        //$_OMP_onchange = true; does not bind parameter 0 in prepared statement
            // in $_OMP_result = $_OMP_prepared->execute(OMP_keyCheck($_OMP_get));
        // in input-line.php
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
        $_OMP_tpl = OMP_TPL_DEL.'706';
        $_OMP_lcl = OMP_LCL_DEL.'41, 91, 100, 504, 700, 703, 715';
        $_OMP_datatypes = array('text');
        if (isset($_POST['insert_button'])) {
            $_OMP_sql['delete'] = 'DELETE FROM '.$_OMP_tables[$_OMP_tbl].
                ' WHERE '.$_OMP_tbl_fld['pkey'].' = ?';
        } else {
            $_OMP_sql['delete_pre'] = 'SELECT oi.'.
                $_OMP_tbl_fld['pkey'].' AS pkey, oi.'.
                $_OMP_tbl_fld['client_pkey'].' AS client_pkey, cli.'.
                $_OMP_clients_fld['name'].' AS client_name, oi.'.
                $_OMP_tbl_fld['supplier_pkey'].' AS supplier_pkey, sup.'.
                $_OMP_suppliers_fld['name'].' AS supplier_name, oi.'.
                $_OMP_tbl_fld['date'].' AS date FROM '.
                $_OMP_tables[$_OMP_tbl].' AS oi LEFT JOIN '.
                $_OMP_tables['clients'].' AS cli ON (oi.'.
                $_OMP_tbl_fld['client_pkey'].' = cli.'.
                $_OMP_clients_fld['pkey'].') LEFT JOIN '.
                $_OMP_tables['suppliers'].' AS sup ON (oi.'.
                $_OMP_tbl_fld['supplier_pkey'].' = sup.'.
                $_OMP_suppliers_fld['pkey'].') WHERE '.
                $_OMP_tbl_fld['pkey'].' = ?';
                // Please note $_OMP_LC[91] contains $_OMP_LC[715]
                // for the word 'Cancelled'
                // eval("\$_OMP_html['page_title'] = \"".$_OMP_LC[41].
                // ' - '.$_OMP_LC[22].$_OMP_LC[46].$_OMP_LC[91]."\";");
                $_OMP_del_tpl = 706;
                $_OMP_page_title_lcl = 41;
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
        $_OMP_tpl = OMP_TPL_DEL.'70, 710, 712,
            719, 721, 722';
        $_OMP_lcl = OMP_LCL_DEL.'23, 41, 106,
            600, 604, 716, 717, 718, 720';
        OMP_load();
        // Check if delete-button was pushed
        if (isset($_POST['insert_button']) &&
            $_POST['insert_button'] == $_OMP_LC[22]) {
            $_OMP_sql['delete_line'] = 'DELETE FROM '.
                $_OMP_tables[$_OMP_tbl_line].
                ' WHERE '.$_OMP_tbl_fld_line['pkey'].' = ?';
            $_OMP_datatypes = array('integer');
        } else {
            $_OMP_sub_rec_tpl = 712;
            $_OMP_rec_tpl = 712;
            $_OMP_colspan = '7'; // Required by $_OMP_TPL[70]
            $_OMP_sub_header_tpl = 710;
            $_OMP_include_tpl = 719;
            $_OMP_html['page_title'] = $_OMP_LC[41].' - '.$_OMP_LC[22].
                ' '.$_OMP_LC[23];
            /* no longer adding $_OMP_LC[46] at the end of page_title
            * because it does not fit the header*/
            $_OMP_sql['delete_pre_line'] = $_OMP_sql['select_line'].' WHERE '.
                $_OMP_tbl_fld_line['pkey'].' = ?';
            $_OMP_datatypes = array('integer');
            $_OMP_subform_wrapper = 'wrapper7';
        }
        require 'lib/del-line.php';
        break;
    }
    /**
    * End delete line record
    */
/*
 * End switch action
 */
}
?>
