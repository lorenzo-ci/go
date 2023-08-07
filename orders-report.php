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
// | See the GNU General Public License                                   |
// | <http://www.gnu.org/copyleft/gpl.html> for more details.             |
// +----------------------------------------------------------------------+
// | Author: Lorenzo Ciani <lciani@yahoo.it>                              |
// +----------------------------------------------------------------------+
//
// $Id: orders-report.php,v 0.8 $
//
// Orders report
//
require_once 'base.php';
require_once 'schemas/db-schema.php';
require_once 'schemas/clients-schema.php';
require_once 'schemas/deliveries-schema.php';
require_once 'schemas/products-schema.php';
require_once 'schemas/suppliers-schema.php';
require_once 'schemas/orders-schema.php';
require_once 'schemas/tbcs-schema.php';
require_once 'schemas/ranges-schema.php';
require_once 'lib/dd-sql.php'; // SQL for drop-down lists
$spreadsheet = isset($_POST['spreadsheet_button']);
if ($spreadsheet) {
    require_once 'Spreadsheet/Excel/Writer.php';
    $workbook = new Spreadsheet_Excel_Writer();
    $format_title = $workbook->addFormat();
    $format_title->setBold();
    $format_title->setHAlign('center');
    $format_column_header = $workbook->addFormat();
    $format_column_header-> setBottomColor('black');
    $format_column_header->setBottom(1);
    $format_column_header->setBold();
    $format_column_footer = $workbook->addFormat();
    $format_column_footer-> setBottomColor('black');
    $format_column_footer->setTop(1);
    $format_column_footer->setBold();
    $format_date = $workbook->addFormat();
    $format_date->setNumFormat('YYYY-MM-DD');
    $format_date->setHAlign('center');
    $format_number = $workbook->addFormat();
    $format_number->setNumFormat('#,##0');
    $format_pc = $workbook->addFormat();
    $format_pc->setNumFormat('0%');
    $format_pc->setHAlign('center');
    $format_country = $workbook->addFormat();
    $format_country->setBold();
    $format_country->setItalic();
    $format_country->setHAlign('center');
    $format_region = $workbook->addFormat();
    $format_region->setItalic();
    $format_region->setHAlign('center');
    $format_agent = $workbook->addFormat();
    $format_agent->setItalic();
    $format_agent->setHAlign('center');
    $format_agent->setColor('red');
    $format_italic = $workbook->addFormat();
    $format_italic->setItalic();
    $format_product = $workbook->addFormat();
    $format_product->setBold();
    $format_product->setFgColor(44);
    $format_confirmed = $workbook->addFormat();
    $format_confirmed->setBold();
    $format_confirmed->setHAlign('center');
    $format_confirmed->setColor('red');
    $format_tbc = $workbook->addFormat();
    $format_tbc->setBold();
    $format_tbc->setHAlign('center');
    $format_order = $workbook->addFormat();
    $format_order->setHAlign('center');
    $format_comment = $workbook->addFormat();
    $format_comment->setHAlign('center');
    $workbook->send('report.xls');
    $worksheet = $workbook->addWorksheet('Report');
    $worksheet->setLandscape();
    $worksheet->centerHorizontally();
    $worksheet->centerVertically();
    $worksheet->setInputEncoding($_OMP_encoding);
}
/**
 * SQL code
 */
/* $_OMP_sql['sort_default'] = ' ORDER BY oi.'.$_OMP_orders_fld['client_pkey'].
    ' ?%, oi.'.$_OMP_orders_fld['supplier_pkey'].
    ', ol.'.$_OMP_orders_lines_fld['eta'].
    ', ol.'.$_OMP_orders_lines_fld['prod_pkey'];
$_OMP_sql['sort_record'][0] = $_OMP_sql['sort_default'];
$_OMP_sql['sort_record'][1] = ' ORDER BY oi.'.$_OMP_orders_fld['supplier_pkey'].
    ' ?%, oi.'.$_OMP_orders_fld['client_pkey'].
    ', ol.'.$_OMP_orders_lines_fld['eta'];
$_OMP_sql['sort_record'][2] = ' ORDER BY ol.'.
    $_OMP_orders_lines_fld['prod_pkey'].
    ' ?%, oi.'.$_OMP_orders_fld['client_pkey'].', ol.'.
    $_OMP_orders_lines_fld['eta'];
$_OMP_sql['sort_record'][3] = ' ORDER BY ol.'.
    $_OMP_orders_lines_fld['eta'].' ?%';*/
    
$_OMP_sql['sort_default'] = ' ORDER BY client_pkey'.
    ' ?%, supplier_pkey, rta, prod_pkey';
$_OMP_sql['sort_record'][0] = $_OMP_sql['sort_default'];
$_OMP_sql['sort_record'][1] = ' ORDER BY supplier_pkey ?%, client_pkey, rta';
$_OMP_sql['sort_record'][2] = ' ORDER BY prod_pkey ?%, client_pkey, rta';
$_OMP_sql['sort_record'][3] = ' ORDER BY rta ?%';
$_OMP_sql['sort_tbc'] = ' ORDER BY client_label, rta, product_label, prod_pkey';
$_OMP_sql['select'] = 'SELECT DISTINCT oi.'.$_OMP_orders_fld['client_pkey'].
    ' AS client_pkey, cli.'.$_OMP_clients_fld['zone'].
    ' AS zone, cli.'.$_OMP_clients_fld['name'].
    ' AS client_label, oi.'.$_OMP_orders_fld['supplier_pkey'].
    ' AS supplier_pkey, sup.'.$_OMP_suppliers_fld['name'].
    ' AS supplier_label, oi.'.$_OMP_orders_fld['pkey'].
    ' AS order_pkey, oi.'.$_OMP_orders_fld['ref'].
    ' AS ref, oi.'.$_OMP_orders_fld['supplier_ref'].
    ' AS supplier_ref, oi.'.$_OMP_orders_fld['client_ref'].
    ' AS client_ref, oi.'.$_OMP_orders_fld['closed'].
    ' AS status, ol.'.$_OMP_orders_lines_fld['prod_pkey'].
    ' AS prod_pkey, prod.'.$_OMP_products_fld['description'].
    ' AS product_label, prod.'.$_OMP_products_fld['grade'].
    ' AS grade, range.'.$_OMP_ranges_fld['name'].
    ' AS product_range, ol.'.$_OMP_orders_lines_fld['price'].
    ' AS price, ol.'.$_OMP_orders_lines_fld['discount'].
    ' AS discount, ol.'.$_OMP_orders_lines_fld['price_net'].
    ' AS price_net, ol.'.$_OMP_orders_lines_fld['quantity'].
    ' AS quantity, dl.'.$_OMP_deliveries_lines_fld['quantity'].
    ' AS delivered, dl.'.$_OMP_deliveries_lines_fld['del_pkey'].
    ' AS del_pkey, ol.'.$_OMP_orders_lines_fld['eta'].
    ' AS rta, del.'.$_OMP_deliveries_fld['eta'].
    ' AS eta, ol.'.$_OMP_orders_lines_fld['note'].
    ' AS note'.
    ' FROM '.$_OMP_tables['orders'].' AS oi'.
    ' LEFT JOIN '.$_OMP_tables['clients'].' AS cli'.
    ' ON (oi.'.$_OMP_orders_fld['client_pkey'].
    ' = cli.'.$_OMP_clients_fld['pkey'].')'.
    ' LEFT JOIN '.$_OMP_tables['tbcs'].' AS ti'.
    ' ON (ti.'.$_OMP_tbcs_fld['pkey'].
    ' = cli.'.$_OMP_clients_fld['pkey'].')'.
    ' LEFT JOIN '.$_OMP_tables['tbcs_lines'].' AS tl'. 
    ' ON (ti.'.$_OMP_tbcs_fld['pkey'].
    ' = tl.'.$_OMP_tbcs_lines_fld['tl_pkey'].')'.
    ' LEFT JOIN '.$_OMP_tables['suppliers'].' AS sup'.
    ' ON (oi.'.$_OMP_orders_fld['supplier_pkey'].
    ' = sup.'.$_OMP_suppliers_fld['pkey'].')'.
    ' LEFT JOIN '.$_OMP_tables['orders_lines'].' AS ol'.
    ' ON (oi.'.$_OMP_orders_fld['pkey'].
    ' = ol.'.$_OMP_orders_lines_fld['oi_pkey'].')'.
    ' LEFT JOIN '.$_OMP_tables['products'].' AS prod'.
    ' ON (ol.'.$_OMP_orders_lines_fld['prod_pkey'].
    ' = prod.'.$_OMP_products_fld['pkey'].')'.
    ' LEFT JOIN '.$_OMP_tables['ranges'].' AS range'.
    ' ON (prod.'.$_OMP_products_fld['range_pkey'].
    ' = range.'.$_OMP_ranges_fld['pkey'].')'.
    ' LEFT JOIN '.$_OMP_tables['deliveries_lines'].' AS dl'.
    ' ON (ol.'.$_OMP_orders_lines_fld['pkey'].
    ' = dl.'.$_OMP_deliveries_lines_fld['ol_pkey'].')'.
    ' LEFT JOIN '.$_OMP_tables['deliveries'].' AS del'.
    ' ON (dl.'.$_OMP_deliveries_lines_fld['del_pkey'].
    ' = del.'.$_OMP_deliveries_fld['pkey'].' AND '.
    'oi.'.$_OMP_orders_fld['client_pkey'].
    ' = del.'.$_OMP_deliveries_fld['client_pkey'].
    ')';
$_OMP_sql['select_tbcs'] = 'SELECT ti.'.$_OMP_tbcs_fld['pkey'].
    ' AS client_pkey, cli.'.$_OMP_clients_fld['zone'].
    ' AS zone, cli.'.$_OMP_clients_fld['name'].
    ' AS client_label, ti.'.$_OMP_tbcs_fld['supplier_pkey'].
    ' AS supplier_pkey, sup.'.$_OMP_suppliers_fld['name'].
    ' AS supplier_label, '.$_OMP_tbcs_fld['order_pkey'].
    ' AS order_pkey, \'\' AS ref, \'\' AS supplier_ref, \'\''.
    ' AS client_ref, NULL AS status, tl.'.$_OMP_tbcs_lines_fld['prod_pkey'].
    ' AS prod_pkey, prod.'.$_OMP_products_fld['description'].
    ' AS product_label, prod.'.$_OMP_products_fld['grade'].
    ' AS grade, range.'.$_OMP_ranges_fld['name'].
    ' AS product_range, tl.'.$_OMP_tbcs_lines_fld['price'].
    ' AS price, 0 AS discount, tl.'.$_OMP_tbcs_lines_fld['price'].
    ' AS price_net, tl.'.$_OMP_tbcs_lines_fld['quantity'].
    ' AS quantity, NULL AS delivered, \'\''.
    ' AS del_pkey, tl.'.$_OMP_tbcs_lines_fld['eta'].
    ' AS rta, NULL'.
    ' AS eta, tl.'.$_OMP_tbcs_lines_fld['note'].
    ' AS note FROM '.$_OMP_tables['tbcs'].
    ' AS ti LEFT JOIN '.$_OMP_tables['clients'].
    ' AS cli ON (ti.'.$_OMP_tbcs_fld['pkey'].' = cli.'.
    $_OMP_clients_fld['pkey'].') LEFT JOIN '.$_OMP_tables['suppliers'].
    ' AS sup ON (ti.'.$_OMP_tbcs_fld['supplier_pkey'].
    ' = sup.'.$_OMP_suppliers_fld['pkey'].
    ') LEFT JOIN '.$_OMP_tables['tbcs_lines'].
    ' AS tl ON (ti.'.$_OMP_tbcs_fld['pkey'].
    ' = tl.'.$_OMP_tbcs_lines_fld['tl_pkey'].
    ') LEFT JOIN '.$_OMP_tables['products'].' AS prod ON (tl.'.
    $_OMP_tbcs_lines_fld['prod_pkey'].' = prod.'.$_OMP_products_fld['pkey'].
    ') LEFT JOIN '.$_OMP_tables['ranges'].' AS range ON (prod.'.
    $_OMP_products_fld['range_pkey'].' = range.'.$_OMP_ranges_fld['pkey'].
    ')';
/**
 * End of SQL code
 */
 
/**
* Function definitions
*/
/**
* Puts the order from the array $GLOBALS['_OMP_rec'] into global variables.
*
*/
function OMP_makeVars()
{
    global $_OMP_html_rec, $_OMP_rec_list, $statistics, $_OMP_TPL;
    
    $_OMP_rec_list = array_map('OMP_htmlentities', $_OMP_rec_list);
    if (empty($_OMP_rec_list['delivered'])) {
        $statistics['quantity'] += $_OMP_rec_list['quantity'];
        $statistics['volume'] += $_OMP_rec_list['quantity']; 
        $statistics['turnover'] += 
          ($_OMP_rec_list['quantity'] * $_OMP_rec_list['price_net']);
    } else {
        $statistics['quantity'] += $_OMP_rec_list['delivered'];
        $statistics['delivered'] += $_OMP_rec_list['delivered'];
        $statistics['volume'] += $_OMP_rec_list['delivered'];
        $statistics['turnover'] += 
          ($_OMP_rec_list['delivered'] * $_OMP_rec_list['price_net']);
    }
    $url = OMP_PATH.
        'clients.php?'.
        OMP_link(
            'filter=1&pkey='.
            urlencode(
                html_entity_decode(
                    $_OMP_rec_list['client_pkey']
                )
            )
        );
    $_OMP_html_rec['client_pkey'] =
        OMP_popLink(
            $_OMP_TPL[9],
            $url,
            $_OMP_rec_list['client_pkey']
        );
    $url = OMP_PATH.
        'suppliers.php?'.
        OMP_link(
            'filter=1&pkey='.
            urlencode(
                html_entity_decode(
                    $_OMP_rec_list['supplier_pkey']
                )
            )
        );
    $_OMP_html_rec['supplier_pkey'] =
        OMP_popLink(
            $_OMP_TPL[9],
            $url,
            $_OMP_rec_list['supplier_pkey']
        );
    if ($_OMP_rec_list['order_pkey'] <> 'TBC') {
        $url = OMP_PATH.
            'orders.php?'.
            OMP_link(
                'filter=1&pkey='.
                urlencode(
                    html_entity_decode(
                        $_OMP_rec_list['order_pkey']
                    )
                )
            );
        $_OMP_html_rec['order_pkey'] =
            OMP_popLink(
                $_OMP_TPL[9],
                $url,
                $_OMP_rec_list['order_pkey']
            );
    } else {
        $url = OMP_PATH.
            'tbcs.php?'.
            OMP_link(
                'filter=1&pkey='.
                urlencode(
                    html_entity_decode(
                        $_OMP_rec_list['client_pkey']
                    )
                )
        );
        $_OMP_html_rec['order_pkey'] =
            OMP_popLink(
                $_OMP_TPL[9],
                $url,
                $_OMP_rec_list['order_pkey']
            );
    }
    $url = OMP_PATH.
        'products.php?'.
        OMP_link(
            'filter=1&pkey='.
            urlencode(
                html_entity_decode(
                    $_OMP_rec_list['prod_pkey']
            )
        )
    );
    $_OMP_html_rec['prod_pkey'] =
        OMP_popLink(
            $_OMP_TPL[9],
            $url,
            $_OMP_rec_list['prod_pkey']
        );
    $_OMP_html_rec['ref'] =
        $_OMP_rec_list['ref'].' '.
        $_OMP_rec_list['supplier_ref'].' '.
        $_OMP_rec_list['client_ref'];
    if ($_OMP_rec_list['rta'] == $_OMP_rec_list['eta']) {
        $_OMP_html_rec['eta'] = '';
    }
    $_OMP_html_rec['rta'] =
        ($_OMP_rec_list['rta'] == '') ? '' : strftime('%x',
        strtotime($_OMP_rec_list['rta']));
    $_OMP_html_rec['eta'] =
        ($_OMP_rec_list['eta'] == '') ? '' : strftime('%x',
        strtotime($_OMP_rec_list['eta']));
    $_OMP_html_rec['volume'] =
        number_format(
            $_OMP_rec_list['volume'],
            0,
            $_SESSION['dp'],
            $_SESSION['ts']
        );
    if ($_OMP_rec_list['delivered'] > 0) {
        $_OMP_html_rec['volume'] =
            OMP_popLink($_OMP_TPL[9],
                OMP_PATH.'deliveries.php?'.
                OMP_link(
                    'filter=1&pkey='.
                    urlencode(
                        html_entity_decode(
                            $_OMP_rec_list['del_pkey']
                        )
                    )
                ),
                $_OMP_html_rec['volume']
            );
    }
    $_OMP_html_rec['price'] = number_format($_OMP_rec_list['price'], 2,
        $_SESSION['dp'], $_SESSION['ts']);
    $_OMP_html_rec['discount'] =
        number_format($_OMP_rec_list['discount'] * 100, 0, $_SESSION['dp'], 
        $_SESSION['ts']).'%';
    $_OMP_html_rec['price_net'] =
        number_format($_OMP_rec_list['price_net'], 2, $_SESSION['dp'], 
        $_SESSION['ts']);
    $_OMP_html_rec['note'] = $_OMP_rec_list['note'];
}

function makeSqlReport()
{
    global $_OMP_db, $_OMP_clients_fld, $_OMP_orders_fld, 
        $_OMP_orders_lines_fld, $_OMP_get, $_OMP_products_fld, $_OMP_rec, 
        $_OMP_rec_sf, $_OMP_sql, $spreadsheet;
    // Makes SQL WHERE parameters
    $_OMP_rec_sf['date_start'] = 
        empty($_POST['date_start']) ? strftime("%Y-%m") : $_POST['date_start'];
    $_OMP_rec_sf['date_end'] = 
        empty($_POST['date_end']) ? strftime("%Y-%m") : $_POST['date_end'];
    // No data before this date
    //$_OMP_rec_sf['date_end'] > OMP_YEAR_START or 
    //    $_OMP_rec_sf['date_end'] = OMP_YEAR_START;
    //if ($_OMP_rec_sf['date_start'] > $_OMP_rec_sf['date_end']) {
    //    $_OMP_rec_sf['date_start'] = $_OMP_rec_sf['date_end'];
    //}
    // Max years if no filters to avoid memory full
    $year_end = (int)substr($_OMP_rec_sf['date_end'], 0, 4);
    $year_start = (int)substr($_OMP_rec_sf['date_start'], 0, 4);
    if ($year_end - $year_start > OMP_YEAR_MAX_REPORT 
        && (empty($_OMP_rec['client_pkey']) 
            && empty($_OMP_rec['supplier_pkey']) 
            && empty($_OMP_rec_sf['prod_pkey'])
            && empty($_OMP_rec['monthly']) 
            && empty($_OMP_rec['range_pkey'])
        )) {
        $month_start = substr($_OMP_rec_sf['date_start'], -2, 2);
        $_OMP_rec_sf['date_end'] = 
            (string)($year_start + OMP_YEAR_MAX_REPORT)."-".$month_start;
    }
    if ($_OMP_rec_sf['date_start'] > $_OMP_rec_sf['date_end']) {
        $_OMP_rec_sf['date_end'] = $_OMP_rec_sf['date_start'];
    }
    $_OMP_get['plan_start'] = $_OMP_rec_sf['date_start'].'-01';
    $_OMP_get['plan_end'] = $_OMP_rec_sf['date_end'].'-01';
    // Makes SQL statement
    // Let's start with the SQL statement to query orders
    $sql_filter = '';
    if (isset($_POST['search_button']) || $_OMP_get['where'] == '') {
        $defaultwhere = true;
        $_OMP_get['where'] = 'WHERE (ol.'.$_OMP_orders_lines_fld['plan'].
        ' BETWEEN '.OMP_db_quote($_OMP_get['plan_start']).
        ' AND '.OMP_db_quote($_OMP_get['plan_end']).') AND oi.'.
        $_OMP_orders_fld['cancelled'].' = \'No\'';
    } else {
        $defaultwhere = false;
    }
    if (isset($_POST['form'])) {
        for (reset($_POST['form']);
                $key = key($_POST['form']); 
                next($_POST['form'])) {
            if (!empty($_POST['form'][$key])) {
                if ($key == 'enc_client_pkey') {
                    $_OMP_rec['client_pkey'] = urldecode($_POST['form'][$key]);
                    $sql_filter .= ' AND lower(oi.'.
                    $_OMP_orders_fld['client_pkey'].') = lower('.
                    $_OMP_db->quote(stripslashes($_OMP_rec['client_pkey'])).')';
                } elseif ($key == 'enc_supplier_pkey') {
                    $_OMP_rec['supplier_pkey'] = 
                    urldecode($_POST['form'][$key]);
                    $sql_filter .= ' AND lower(oi.'.
                    $_OMP_orders_fld['supplier_pkey'].') = lower('.
                    OMP_db_quote(stripslashes($_OMP_rec['supplier_pkey'])).
                    ')';
                } elseif ($key == 'txt_prod_pkey') {
                    $_OMP_rec_sf['prod_pkey'] = urldecode($_POST['form'][$key]);
                    $sql_filter .= ' AND ol.'.
                    $_OMP_orders_lines_fld['prod_pkey'].' = '.
                    OMP_db_quote(stripslashes($_OMP_rec_sf['prod_pkey']));
                } elseif ($key == 'grade') {
                    $_OMP_rec['grade'] = $_POST['form'][$key];
                    $sql_filter .= ' AND prod.'.
                        $_OMP_products_fld['grade'].' = '.
                        OMP_db_quote($_OMP_rec['grade']);
                } elseif ($key == 'zone') {
                    $_OMP_rec['zone'] = $_POST['form'][$key];
                    $sql_filter .= ' AND cli.'.$_OMP_clients_fld['zone'].
                        ' = '.OMP_db_quote($_OMP_rec['zone']);
                } elseif ($key == 'bol_status') {
                    $_OMP_rec['status'] = $_POST['form'][$key];
                    $sql_filter .= ' AND oi.'.$_OMP_orders_fld['closed'].
                        ' = '.OMP_db_quote($_OMP_rec['status']);
                    if ($_OMP_rec['status'] == 'f') {
                        $_OMP_rec['status_false'] = 'selected="selected"';
                        $_OMP_rec['status_true'] = '';
                    } else {
                        $_OMP_rec['status_true'] = 'selected="selected"';
                        $_OMP_rec['status_false'] = '';
                    }
                } elseif ($key == 'bol_union') {
                    $_OMP_rec['union'] = $_POST['form'][$key];
//                     $sql_filter .= ' AND prod.'.$_OMP_products_fld['monthly'].
//                         ' = '.$_OMP_db->quote($_OMP_rec['monthly']);
                    if ($_OMP_rec['union'] == 'f') {
                        $_OMP_rec['union_false'] = 'selected="selected"';
                        $_OMP_rec['union_true'] = '';
                    } else {
                        $_OMP_rec['union_true'] = 'selected="selected"';
                        $_OMP_rec['union_false'] = '';
                    }
                } elseif ($key == 'num_range_pkey') {
                    $_OMP_rec['range_pkey'] = $_POST['form'][$key];
                    $sql_filter .= ' AND prod.'.
                        $_OMP_products_fld['range_pkey'].
                        ' = '.$_OMP_rec['range_pkey'];
                }
            } elseif ($key == 'grade' && $_POST['form'][$key] <> '') {
                /* cannot use empty() because 0 is meaningful */
                $_OMP_rec['grade'] = $_POST['form'][$key];
                $sql_filter .= ' AND prod.'.
                    $_OMP_products_fld['grade'].' = '.
                    OMP_db_quote($_OMP_rec['grade']);
            }
        }
        $_OMP_get['where'] .= $sql_filter;
    } else {
        if ($_SESSION['cat'] == 0) { // Filter for clients
            $_OMP_get['where'] .= ' AND lower(oi.'.
                $_OMP_orders_fld['client_pkey'].') = lower('.
                OMP_db_quote($_SESSION['id']).')';
        } elseif ($_SESSION['cat'] == 1) { // Filter for suppliers
            $_OMP_get['where'] .= ' AND lower(oi.'.
                $_OMP_orders_fld['supplier_pkey'].') = lower('.
                OMP_db_quote($_SESSION['id']).')';
        }
    }
    $sql_orders = $_OMP_sql['select'].' '.$_OMP_get['where'];
    // Then let's make the SQL statement for TBCs
    $sql_filter = '';
    if ($defaultwhere) {
        $_OMP_get['where'] = 'WHERE (tl.'.$_OMP_orders_lines_fld['plan'].
        ' BETWEEN '.$_OMP_db->quote($_OMP_get['plan_start']).
        ' AND '.$_OMP_db->quote($_OMP_get['plan_end']).')';
    }
    if (isset($_POST['form'])) {
        for (reset($_POST['form']); 
                $key = key($_POST['form']); 
                next($_POST['form'])) {
            if (!empty($_POST['form'][$key])) {
                if ($key == 'enc_client_pkey') {
                    $_OMP_rec['client_pkey'] = urldecode($_POST['form'][$key]);
                    $sql_filter .= ' AND lower(ti.'.
                    $_OMP_orders_fld['client_pkey'].') = lower('.
                    $_OMP_db->quote(stripslashes($_OMP_rec['client_pkey'])).')';
                } elseif ($key == 'enc_supplier_pkey') {
                    $_OMP_rec['supplier_pkey'] = 
                    urldecode($_POST['form'][$key]);
                    $sql_filter .= ' AND lower(ti.'.
                    $_OMP_orders_fld['supplier_pkey'].') = lower('.
                    $_OMP_db->quote(stripslashes($_OMP_rec['supplier_pkey'])).
                    ')';
                } elseif ($key == 'txt_prod_pkey') {
                    $_OMP_rec_sf['prod_pkey'] = urldecode($_POST['form'][$key]);
                    $sql_filter .= ' AND tl.'.
                    $_OMP_orders_lines_fld['prod_pkey'].' = '.
                    $_OMP_db->quote(stripslashes($_OMP_rec_sf['prod_pkey']));
                } elseif ($key == 'grade') {
                    $_OMP_rec['grade'] = $_POST['form'][$key];
                    $sql_filter .= ' AND prod.'.
                        $_OMP_products_fld['grade'].' = '.
                        OMP_db_quote($_OMP_rec['grade']);
                } elseif ($key == 'bol_union') {
                    $_OMP_rec['union'] = $_POST['form'][$key];
//                    $sql_filter .= ' AND prod.'.$_OMP_products_fld['monthly'].
//                        ' = '.$_OMP_db->quote($_OMP_rec['monthly']);
                    if ($_OMP_rec['union'] == 'f') {
                        $_OMP_rec['union_false'] = 'selected="selected"';
                        $_OMP_rec['union_true'] = '';
                    } else {
                        $_OMP_rec['union_true'] = 'selected="selected"';
                        $_OMP_rec['union_false'] = '';
                    }
                } elseif ($key == 'zone') {
                    $_OMP_rec['zone'] = $_POST['form'][$key];   
                    $sql_filter .= ' AND cli.'.$_OMP_clients_fld['zone'].
                        ' = '.$_OMP_db->quote($_OMP_rec['zone']);
                } elseif ($key == 'num_range_pkey') {
                    $_OMP_rec['range_pkey'] = $_POST['form'][$key];
                    $sql_filter .= ' AND prod.'.
                        $_OMP_products_fld['range_pkey'].
                        ' = '.$_OMP_rec['range_pkey'];
                }
            } elseif ($key == 'grade' && $_POST['form'][$key] <> '') {
                $_OMP_rec['grade'] = $_POST['form'][$key];
                $sql_filter .= ' AND prod.'.
                    $_OMP_products_fld['grade'].' = '.
                    OMP_db_quote($_OMP_rec['grade']);
            }
        }
        $_OMP_get['where'] .= $sql_filter;
    } else {
        if ($_SESSION['cat'] == 0) { // Filter for clients
            $_OMP_get['where'] .= ' AND lower(ti.'.
                $_OMP_orders_fld['client_pkey'].') = lower('.
                $_OMP_db->quote($_SESSION['id']).')';
        } elseif ($_SESSION['cat'] == 1) { // Filter for suppliers
            $_OMP_get['where'] .= ' AND lower(ti.'.
                $_OMP_orders_fld['supplier_pkey'].') = lower('.
                $_OMP_db->quote($_SESSION['id']).')';
        }
    }
    $sql_tbcs = $_OMP_sql['select_tbcs'].' '.$_OMP_get['where'];
    /* Check if UNION SQL is required */
    if (isset($_OMP_rec['union'])) {
        if ($_OMP_rec['union'] == 't') {
            /* Orders SQL */
            $_OMP_get['sql'] = $sql_orders;
        } else {
            /* TBCs SQL */
            $_OMP_get['sql'] = $sql_tbcs;
        }
    } else {
        /* Union SQL */
        $_OMP_get['sql'] = $sql_orders.' UNION '.$sql_tbcs;
    }
    
    // Add sorting to sql
    if ($spreadsheet) {
        $_OMP_get['sql'] .= $_OMP_sql['sort_tbc'];
        return;
    }
    if ($_OMP_get['sort'] != '') {
        $_OMP_get['sql'] .= $_OMP_sql['sort_record'][$_OMP_get['sort']];
    } else {
        $_OMP_get['sql'] .= $_OMP_sql['sort_default'];
    }
    $_OMP_get['sql'] = str_replace(' ?%', 
        (($_OMP_get['sort_type']) ? ' DESC' : ' ASC'), $_OMP_get['sql']);
}
/**
* End functions
*/
// Templates and i18n text
$_OMP_tpl = OMP_TPL_MENU.'9, 30, 1900, 1901, 1902, 1903';
// Please note: need 85 for OMP_genErr() (see base.php)
$_OMP_lcl = '24, 27, 29, 35, 36, 41, 50, 51, 83, 85, 86, 100, 106,
    117, 120, 121, 504, 600, 602, 604, 616, 700, 706, 713, 714,
    717, 718, 719, 720, 721, 722, 801, 806, 1900, 1901';
OMP_load(); // See base.php
// Sort field
$_OMP_get['sort'] = isset($_OMP_get['sort']) ? $_OMP_get['sort'] : '';
// $_OMP_get['sort_type'] values are 0 (ascending sort) or 1 (descending sort)
$_OMP_get['sort_type'] = isset($_OMP_get['sort_type']) ? 
    $_OMP_get['sort_type'] : 0;
$_OMP_get['sort_type'] = ($_OMP_get['sort'] == '') ? $_OMP_get['sort_type'] : 
    $_OMP_get['sort_type'] + 1;
if ($_OMP_get['sort_type'] >= 2) { $_OMP_get['sort_type'] = 0; }
$sort_record[0] = $_OMP_LC[100];
$sort_record[1] = $_OMP_LC[504];
$sort_record[2] = $_OMP_LC[600];
$sort_record[3] = $_OMP_LC[720];
$_OMP_rec['union_true'] = $_OMP_rec['union_false'] =
    $_OMP_rec['zone'] = $_OMP_rec['grade'] =
    $_OMP_rec['status_true'] = $_OMP_rec['status_false'] = '';
makeSqlReport();
$statistics['quantity'] = $statistics['delivered'] = 
    $statistics['turnover'] = $statistics['volume'] = 0;
$_OMP_html['table'] = $_OMP_html['table_body'] = '';
$_OMP_db_result = $_OMP_db->query($_OMP_get['sql']);
if ($_OMP_db_result->numRows() > 0) {
    if ($spreadsheet) {
        if (isset($_OMP_rec['supplier_pkey']) && 
            $_OMP_rec['supplier_pkey'] === 'WELLMAN LTD') {
            $format_column_header->setFgColor(46);
            $format_column_header->setColor('blue');
            $format_column_header->setItalic();
            $format_column_header->setSize(11);
            $worksheet->write(0, 0, 'Manager', $format_column_header);
            $worksheet->write(0, 1, 'Agent/Customer No', $format_column_header);
            $worksheet->write(0, 2, 'Region', $format_column_header);
            $worksheet->write(0, 4, 'Customer', $format_column_header);
            $worksheet->write(0, 5, 'Product Family', $format_column_header);
            $worksheet->write(0, 6, 'Product', $format_column_header);
            $format_column_header->setHAlign('center');
            $worksheet->write(0, 3, 'Country', $format_column_header);
            $worksheet->write(0, 7, 'Grade', $format_column_header);
            $worksheet->write(0, 8, 'Weight', $format_column_header);
            $worksheet->write(0, 9, 'Currency', $format_column_header);
            $worksheet->write(0, 10, 'Price', $format_column_header);
            $worksheet->write(0, 11, 'Discount', $format_column_header);
            $worksheet->write(0, 12, 'Status', $format_column_header);
            $worksheet->write(0, 13, 'Date', $format_column_header);
            $worksheet->write(0, 14, 'WIL Order No', $format_column_header);
            $worksheet->write(0, 15, 'Cust PO', $format_column_header);
            $worksheet->write(0, 16, 'Source of order', $format_column_header);
            $worksheet->write(0, 17, 'Agent Ref', $format_column_header);
            $worksheet->write(0, 18, 'Support Site', $format_column_header);
            $worksheet->write(0, 19, 'Estimated Prod Date', 
                $format_column_header);
            $worksheet->write(0, 20, 'Lates', $format_column_header);
            $worksheet->write(0, 21, 'Comments', $format_column_header);
            $row = 1;
        } else {
            $workbook->setCountry(39);
            $worksheet->setInputEncoding("UTF-8");
            $worksheet->setMerge(0, 0, 0, 11);
            $firstline = $_OMP_LC[50].' '.$_OMP_LC[41].' '.
                strtolower($_OMP_LC[1900]).' '.$_POST['date_start'].' '.
                strtolower($_OMP_LC[1901]).' '.$_POST['date_end'];
            $firstline = $_OMP_LC[50].' '.$_OMP_LC[41].' '.
                strtolower($_OMP_LC[1900]).' '.$_POST['date_start'].' '.
                strtolower($_OMP_LC[1901]).' '.$_POST['date_end'];
            $worksheet->write(0, 0, $firstline, $format_title);
            $worksheet->write(1, 0, $_OMP_LC[100], $format_column_header);
            $worksheet->write(1, 1, $_OMP_LC[504], $format_column_header);
            $worksheet->write(1, 2, $_OMP_LC[700], $format_column_header);
            $worksheet->write(1, 3, $_OMP_LC[706], $format_column_header);
            $worksheet->write(1, 4, $_OMP_LC[600], $format_column_header);
            $worksheet->write(1, 5, $_OMP_LC[604], $format_column_header);
            $worksheet->write(1, 6, $_OMP_LC[718], $format_column_header);
            $worksheet->write(1, 7, $_OMP_LC[719], $format_column_header);
            $worksheet->write(1, 8, $_OMP_LC[717], $format_column_header);
            $worksheet->write(1, 9, $_OMP_LC[801], $format_column_header);
            $worksheet->write(1, 10, $_OMP_LC[720], $format_column_header);
            $row = 2;
        }
    } else {
        // Main GET elements go in $getstring, skip sql
        $new_get = $_OMP_get;
        unset($new_get['sql']);
        $getstring = OMP_makeGet($new_get); // See functions.php
        $tmp = ($_OMP_get['where'] != '') ? 
            '&where='.urlencode($_OMP_get['where']) : '';
    }
    while ($_OMP_rec_list = $_OMP_db_result->fetchRow()) {
        $_OMP_rec_list['volume'] = empty($_OMP_rec_list['delivered']) ? 
            $_OMP_rec_list['quantity'] : $_OMP_rec_list['delivered'];
        if ($spreadsheet) {
            if (isset($_OMP_rec['supplier_pkey']) && 
                $_OMP_rec['supplier_pkey'] === 'WELLMAN LTD') {
                switch($_OMP_rec_list['zone']) {
                    case 2:
                        $worksheet->write($row, 0, OMP_PIC_2);
                        break;
                    case 4:
                        $worksheet->write($row, 0, OMP_PIC_4);
                        break;
                    default:
                        $worksheet->write($row, 0, OMP_PIC_2);
                }
                $worksheet->write($row, 1, 'Chaney', $format_agent);
                $worksheet->write($row, 2, 'Region '.$_OMP_rec_list['zone'], 
                    $format_region);
                $worksheet->write($row, 3, 'Italy', $format_country);
                $worksheet->write($row, 4, $_OMP_rec_list['client_label'], 
                    $format_italic);
                $worksheet->write($row, 5, $_OMP_rec_list['product_range']);
                $worksheet->write($row, 6, $_OMP_rec_list['prod_pkey'], 
                    $format_product);
                if ($_OMP_rec_list['grade'] == 0) {
                    $worksheet->write($row, 7, 'STD', $format_comment);
                } elseif ($_OMP_rec_list['grade'] == 2) {
                    $worksheet->write($row, 7, 'SUB', $format_comment);
                } else {
                    $worksheet->write($row, 7, $_OMP_rec_list['grade'], 
                        $format_comment);
                }
                $format_number->setHAlign('center');
                $worksheet->write($row, 8, $_OMP_rec_list['volume'], 
                    $format_number);
                $worksheet->write($row, 9, 'EUR', $format_comment);
                $_OMP_rec_list['price'] = number_format($_OMP_rec_list['price'],
                    2, $_SESSION['dp'], $_SESSION['ts']);
                $worksheet->write($row, 10, 
                    $_OMP_rec_list['price'], $format_comment);
                if ($_OMP_rec_list['discount'] > 0) {
                    $worksheet->write($row, 11, $_OMP_rec_list['discount'], 
                        $format_pc);
                }
                if ($_OMP_rec_list['order_pkey'] == 'TBC') {
                    $worksheet->write($row, 12, 'TBC', $format_tbc);
                } else {
                    $worksheet->write($row, 12, 'Confirmed', $format_confirmed);
                    $worksheet->write($row, 14, 
                        $_OMP_rec_list['ref'], $format_order);
                    $worksheet->write($row, 15, 
                        $_OMP_rec_list['client_ref'], $format_order);
                    $worksheet->write($row, 16, 'AGENT', $format_order);
                    $worksheet->write($row, 17, 
                        $_OMP_rec_list['supplier_ref'], $format_order);
                }
                $start = new DateTime($_OMP_rec_list['rta']);
                $span = $start->diff(new DateTime('1899-12-30'));
                $worksheet->write($row, 13, $span->days, $format_date);
                if ($_OMP_rec_list['client_pkey'] == 'ESWL') {
                    $worksheet->write($row, 21, 'ESWL', $format_comment);
                }
            } else {
                $worksheet->write($row, 0, $_OMP_rec_list['client_pkey']);
                $worksheet->write($row, 1, $_OMP_rec_list['supplier_pkey']);
                $worksheet->write($row, 2, $_OMP_rec_list['order_pkey']);
                $_OMP_rec_list['ref'] = ltrim($_OMP_rec_list['ref'].' '.
                $_OMP_rec_list['supplier_ref'].' '.
                    $_OMP_rec_list['client_ref']);
                $worksheet->write($row, 3, $_OMP_rec_list['ref']);
                $worksheet->write($row, 4, $_OMP_rec_list['prod_pkey']);
                $worksheet->write($row, 5, $_OMP_rec_list['price']);
                $worksheet->write($row, 6, $_OMP_rec_list['discount'], 
                    $format_pc);
                $worksheet->write($row, 7, $_OMP_rec_list['price_net']);
                $worksheet->write($row, 8, $_OMP_rec_list['volume'], 
                    $format_number);
                $worksheet->write($row, 9, $_OMP_rec_list['del_pkey']);
                $start = new DateTime($_OMP_rec_list['rta']);
                $span = $start->diff(new DateTime('1899-12-30'));
                $worksheet->write($row, 10, $span->days, $format_date);
            }
            $row++;
        } else {
            OMP_makeVars();
            eval("\$_OMP_html['table_body'] .= \"".$_OMP_TPL[1903]."\";");
        }
    }
    if ($spreadsheet) {
        $workbook->close();
    } else {
        $statistics['avg_price'] = ($statistics['volume'] > 0) ? 
            $statistics['turnover'] / $statistics['volume'] : 0;
        $statistics['volume'] = number_format($statistics['volume'], 0, 
            $_SESSION['dp'], $_SESSION['ts']);
        $statistics['turnover'] = ($statistics['turnover'] == 0) ? 
            '' : $_OMP_LC[51].' '.number_format($statistics['turnover'], 0, 
            $_SESSION['dp'], $_SESSION['ts']);
        $statistics['avg_price'] = ($statistics['avg_price'] == 0) ? '' : 
            number_format($statistics['avg_price'], 2, $_SESSION['dp'], 
            $_SESSION['ts']);

        // HTML links to sort records. See functions.php
        $sort_record = OMP_recSortLinks($sort_record, 'sort_record', $tmp);

        eval("\$_OMP_html['table'] = \"".$_OMP_TPL[1901]."\";");
    }
} else {
    $statistics['avg_price'] = $statistics['quantity'] = 
        $statistics['delivered'] = 
        $statistics['turnover'] = $statistics['avg_price'] = 0;
    $_OMP_html['table'] = '<table class="record"><tr class="label"><th>'.
        $_OMP_LC[83].'</th></tr></table>';
    $products_select_no_filter = true;
}
$_OMP_db_result->free();
$_OMP_onchange = false;
$_OMP_prefilter = true;
if ($_SESSION['cat'] == 0) { // Filter for clients
    $_OMP_client_combo = '<input disabled type="text" name="client_pkey" '. 
        'size="10" value="'.htmlentities($_SESSION['id']).'" /> '.
        '<input type="hidden" name="form[enc_client_pkey]" value="'.
        htmlentities($_SESSION['id']).'" />';
    require 'lib/dd-suppliers.php';
} elseif ($_SESSION['cat'] == 1) { // Filter for suppliers
    $_OMP_supplier_combo =
        '<input disabled type="text" name="supplier_pkey"'.
        'size="10" value="'.htmlentities($_SESSION['id']).'" /> '.
        '<input type="hidden" name="form[enc_supplier_pkey]" value="'.
        htmlentities($_SESSION['id']).'" />';
    require 'lib/dd-clients.php';
} else {
    require 'lib/dd-clients.php';
    require 'lib/dd-suppliers.php';
}
require 'lib/dd-products.php';
require 'lib/dd-ranges.php';
$_OMP_LC['614'] = $_OMP_LC['117'].' 1';
$_OMP_LC['615'] = $_OMP_LC['117'].' 2';
$_OMP_url = OMP_PATH_SCRIPT;
$_OMP_subform_class = 'subform-report';
eval("\$_OMP_html['include'] = \"".$_OMP_TPL[1900]."\";");
$_OMP_html['page_title'] = 
    ($_OMP_get['popup']) ? '' : $_OMP_LC[50].' '.$_OMP_LC[41];
$_OMP_html['browser_title'] = $_OMP_LC[50].' '.$_OMP_LC[41];
/* @see functions.php */
OMP_drawer();
/* mdc-toolbar */
$_OMP_html['toolbar'] = '';
eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
OMP_lose();
?>
