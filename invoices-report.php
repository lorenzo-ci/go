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
// $Id: invoices-report.php,v 0.8 $
//
// Invoices report
//

require_once 'base.php';
require_once 'schemas/db-schema.php';
require_once 'schemas/clients-schema.php';
// require_once 'schemas/deliveries-schema.php';
require_once 'schemas/products-schema.php';
// require_once 'schemas/ranges-schema.php';
require_once 'schemas/suppliers-schema.php';
require_once 'lib/dd-sql.php'; // SQL for drop-down lists
// require_once 'schemas/orders-schema.php';
require_once 'schemas/invoices-schema.php';
$spreadsheet = isset($_POST['spreadsheet_button']);
if ($spreadsheet) {
    require_once 'Spreadsheet/Excel/Writer.php';
    $workbook = new Spreadsheet_Excel_Writer();
    $format_title = $workbook->addFormat();
    $format_title->setBold();
    $format_title->setAlign('center');
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
    $format_number = $workbook->addFormat();
    $format_number->setNumFormat('#,##0.00');
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
if (isset($_POST['form']['bol_cli_total']) 
    && $_POST['form']['bol_cli_total'] == 't') {
    $_OMP_sql['sort_default'] = ' ORDER BY inv.'.
        $_OMP_invoices_fld['client_pkey'].' ?%';
    $_OMP_cli_total = 1;
} else {
    $_OMP_sql['sort_default'] = ' ORDER BY inv.'.
        $_OMP_invoices_fld['client_pkey'].' ?%, inv.'.
        $_OMP_invoices_fld['date'];
    $_OMP_sql['sort_record'][1] = ' ORDER BY inv.'.
        $_OMP_invoices_fld['supplier_pkey'].
        ' ?%, inv.'.$_OMP_invoices_fld['client_pkey'].
        ', inv.'.$_OMP_invoices_fld['date'];
    $_OMP_sql['sort_record'][2] = ' ORDER BY inv.'.
        $_OMP_invoices_fld['date'].
        ' ?%, inv.'.$_OMP_invoices_fld['client_pkey'].', inv.'.
        $_OMP_invoices_fld['supplier_pkey'];
    $_OMP_sql['sort_record'][3] = ' ORDER BY inv.'.
        $_OMP_invoices_fld['due_date'].
        ' ?%, inv.'.$_OMP_invoices_fld['client_pkey'].', inv.'.
        $_OMP_invoices_fld['supplier_pkey'];
    $_OMP_cli_total = 0;
}
$_OMP_sql['sort_record'][0] = $_OMP_sql['sort_default'];
$_OMP_sql['select'] = 'SELECT DISTINCT inv.'.$_OMP_invoices_fld['client_pkey'].
    ' AS client_pkey, cli.'.$_OMP_clients_fld['name'].
    ' AS client_label, inv.'.$_OMP_invoices_fld['pkey'].
    ' AS invoice_pkey, inv.'.$_OMP_invoices_fld['date'].
    ' AS date, inv.'.$_OMP_invoices_fld['due_date'].
    ' AS due_date, inv.'.$_OMP_invoices_fld['paymnt_date'].
    ' AS paymnt_date, inv.'.$_OMP_invoices_fld['amount'].
    ' AS amount, inv.'.$_OMP_invoices_fld['supplier_pkey'].
    ' AS supplier_pkey, sup.'.$_OMP_suppliers_fld['name'].
    ' AS supplier_label, inv.'.$_OMP_invoices_fld['note'].
    ' AS note FROM '.$_OMP_tables['clients'].
    ' AS cli INNER JOIN '.$_OMP_tables['invoices'].
    ' AS inv ON (cli.'.$_OMP_clients_fld['pkey'].
    ' = inv.'.$_OMP_invoices_fld['client_pkey'].
    ') INNER JOIN "Fornitori" AS sup ON (inv.'.
    $_OMP_invoices_fld['supplier_pkey'].' = sup.'.
    $_OMP_suppliers_fld['pkey'].')';
/**
 * End of SQL code
 */
 
/**
* Function definitions
*/
/**
* Formats variables in $GLOBALS['_OMP_rec_list']
*
*/
function OMP_makeVars()
{
    global $_OMP_rec, $_OMP_rec_list, $statistics, $_OMP_TPL;

    #!isset($statistics['turnover']) or $statistics['turnover'] = 0;
    $statistics['turnover'] += $_OMP_rec_list['amount'];
    $_OMP_rec_list = array_map('OMP_htmlentities', $_OMP_rec_list);
    $url = OMP_PATH.
        'invoices.php?'.
        OMP_link('filter=1&pkey='.
            urlencode(
                html_entity_decode(
                    $_OMP_rec_list['invoice_pkey']
                )
            )
            // .'&popup=1'
        );
    $_OMP_rec_list['invoice_pkey'] =
        OMP_popLink(
            $_OMP_TPL[9],
            $url,
            $_OMP_rec_list['invoice_pkey']
        );
    $url = OMP_PATH.
        'clients.php?'.
        OMP_link('filter=1&pkey='.
            urlencode(
                html_entity_decode(
                    $_OMP_rec_list['client_pkey']
                )
            ).
            '&popup=1'
        );
    $_OMP_rec_list['client_pkey'] =
        OMP_popLink(
            $_OMP_TPL[9],
            $url,
            $_OMP_rec_list['client_pkey']
        );
    $url = OMP_PATH.
        'suppliers.php?'.
        OMP_link('filter=1&pkey='.
            urlencode(
                html_entity_decode(
                    $_OMP_rec_list['supplier_pkey']
                )
            ).
            '&popup=1'
        );
    $_OMP_rec_list['supplier_pkey'] =
        OMP_popLink(
            $_OMP_TPL[9],
            $url,
            $_OMP_rec_list['supplier_pkey']
        );
    $_OMP_rec_list['amount'] = number_format($_OMP_rec_list['amount'], 
        2, $_SESSION['dp'], $_SESSION['ts']);
    $_OMP_rec_list['date'] = ($_OMP_rec_list['date'] == '') ? '' : 
        strftime('%x', strtotime($_OMP_rec_list['date']));
    $_OMP_rec_list['due_date'] = ($_OMP_rec_list['due_date'] == '') ? '' : 
        strftime('%x', strtotime($_OMP_rec_list['due_date']));
    $_OMP_rec_list['paymnt_date'] = ($_OMP_rec_list['paymnt_date'] == '') ? '' :
        strftime('%x', strtotime($_OMP_rec_list['paymnt_date']));
}

function makeSqlReport()
{
    global $_OMP_cli_total, $_OMP_db, $_OMP_invoices_fld, $_OMP_get, 
        $_OMP_html, $_OMP_rec, $_OMP_rec_sf, $_OMP_sql, $_OMP_tables;
    // Makes SQL WHERE parameters
    $_OMP_rec_sf['date_start'] = 
        empty($_POST['date_start']) ? strftime('%Y-%m') : $_POST['date_start'];
    $_OMP_rec_sf['date_end'] = 
        empty($_POST['date_end']) ? strftime('%Y-%m') : $_POST['date_end'];
    if ($_OMP_rec_sf['date_start'] > $_OMP_rec_sf['date_end']) {
        $_OMP_rec_sf['date_end'] = $_OMP_rec_sf['date_start'];
    }
    $year_end = (int)substr($_OMP_rec_sf['date_end'], 0, 4);
    $year_start = (int)substr($_OMP_rec_sf['date_start'], 0, 4);
    // Max years if no filters to avoid memory full
    if ($year_end - $year_start > OMP_YEAR_MAX_REPORT 
        && (empty($_OMP_rec['client_pkey']) 
        && empty($_OMP_rec['supplier_pkey']) 
        && empty($_OMP_rec['bol_paid'])
        )) {
            $month_start = substr($_OMP_rec_sf['date_start'], -2, 2);
            $_OMP_rec_sf['date_end'] = 
                (string)($year_start + OMP_YEAR_MAX_REPORT)."-".$month_start;
    }
    $_OMP_get['plan_start'] = $_OMP_rec_sf['date_start'].'-01';
    $_OMP_get['plan_end'] = OMP_endOfMonth($_OMP_rec_sf['date_end']);
    // Makes SQL statement
    if ($_OMP_cli_total) {
        empty($_POST['form']['enc_client_pkey']) or
            $_POST['form']['enc_client_pkey'] = $_OMP_rec['client_pkey'] = '';
        $_OMP_sql['select'] = 'SELECT DISTINCT inv.'.
            $_OMP_invoices_fld['client_pkey'].
            ' AS client_pkey, NULL as client_label, ';
        if (!empty($_POST['form']['enc_supplier_pkey'])) {
            $_OMP_sql['select'] .= 'inv.'.
                $_OMP_invoices_fld['supplier_pkey'].
                ' AS supplier_pkey, ';
        } else {
            $_OMP_sql['select'] .= ' NULL AS supplier_pkey, ';
        }
        $_OMP_sql['select'] .= ' NULL as supplier_label, NULL AS invoice_pkey, 
            NULL AS date, NULL AS due_date, NULL AS paymnt_date, Sum(inv.'.
            $_OMP_invoices_fld['amount'].') AS amount, NULL AS note FROM '.
            $_OMP_tables['invoices'].' AS inv';
    } else {
        $_OMP_cli_total = 0;
    }
    if (isset($_POST['form']['int_filterdate'])) {
        $filterdate = $_POST['form']['int_filterdate'];
    } else {
        $filterdate = 0;
    }
    switch ($filterdate) {
        case 0:
            $_OMP_date_filter = 'due_date';
            $_OMP_html['due_date'] = 'selected="selected"';
            $_OMP_html['date'] = $_OMP_html['paymnt_date'] = '';
            break;
        case 1:
            $_OMP_date_filter = 'date';
            $_OMP_html['date'] = 'selected="selected"';
            $_OMP_html['due_date'] = $_OMP_html['paymnt_date'] = '';
            break;
        case 2:
            $_OMP_date_filter = 'paymnt_date';
            $_OMP_html['paymnt_date'] = 'selected="selected"';
            $_OMP_html['due_date'] = $_OMP_html['date'] = '';
            break;
    }
    if (isset($_POST['search_button']) || $_OMP_get['where'] == '') {
        $_OMP_get['where'] = 'WHERE inv.'.
        $_OMP_invoices_fld[$_OMP_date_filter].
        ' BETWEEN '.OMP_db_quote($_OMP_get['plan_start']).
        ' AND '.OMP_db_quote($_OMP_get['plan_end']);
    }
    $sql_filter = '';
    if (isset($_POST['form'])) {
        for (reset($_POST['form']); 
                $key = key($_POST['form']); 
                next($_POST['form'])) {
            if (!empty($_POST['form'][$key])) {
                if ($key == 'enc_client_pkey') {
                    $_OMP_rec['client_pkey'] = urldecode($_POST['form'][$key]);
                    $sql_filter .= ' AND lower(inv.'.
                    $_OMP_invoices_fld['client_pkey'].') = lower('.
                    OMP_db_quote(stripslashes($_OMP_rec['client_pkey'])).')';
                } elseif ($key == 'enc_supplier_pkey') {
                    $_OMP_rec['supplier_pkey'] = 
                    urldecode($_POST['form'][$key]);
                    $sql_filter .= ' AND lower(inv.'.
                    $_OMP_invoices_fld['supplier_pkey'].') = lower('.
                    OMP_db_quote(stripslashes($_OMP_rec['supplier_pkey'])).
                    ')';
                } elseif ($key == 'bol_paid') {
                    if ($_POST['form'][$key] == 't') {
                        $sql_filter .= ' AND (inv.'.
                            $_OMP_invoices_fld['paymnt_date'].
                            ' <= \'today\' AND inv.'.
                            $_OMP_invoices_fld['paymnt_date'].' IS NOT NULL)';
                        $_OMP_html['paid_true'] = 'selected="selected"';
                        $_OMP_html['paid_false'] = '';
                    } else {
                        $sql_filter .= ' AND (inv.'.
                            $_OMP_invoices_fld['paymnt_date'].
                            ' > \'today\' OR inv.'.
                            $_OMP_invoices_fld['paymnt_date'].' IS NULL)';
                        $_OMP_html['paid_false'] = 'selected="selected"';
                        $_OMP_html['paid_true'] = '';
                    }
                } elseif ($key == 'bol_br') {
                    if ($_POST['form'][$key] == 't') {
                        $sql_filter .= ' AND inv.'.
                            $_OMP_invoices_fld['br_ref'].' IS NOT NULL';
                        $_OMP_html['br_true'] = 'selected="selected"';
                        $_OMP_html['br_false'] = '';
                    } else {
                        $_OMP_html['br_false'] = 'selected="selected"';
                        $_OMP_html['br_true'] = '';
                    }
                } elseif ($key == 'bol_cli_total') {
                    if ($_POST['form'][$key] == 't') {
                        $_OMP_html['cli_total_true'] = 'selected="selected"';
                        $_OMP_html['cli_total_false'] = '';
                    } else {
                        $_OMP_html['cli_total_false'] = 'selected="selected"';
                        $_OMP_html['cli_total_true'] = '';
                    }
                }
            } else {
                 if ($key == 'bol_paid') {
                    $_OMP_html['paid_true'] = $_OMP_html['paid_false'] = '';
                } elseif ($key == 'bol_br') {
                    $_OMP_html['br_true'] = $_OMP_html['br_false'] = '';
                }
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
        $_OMP_html['paid_true'] = $_OMP_html['paid_false'] = '';
        $_OMP_html['br_true'] = $_OMP_html['br_false'] = '';
        $_OMP_html['cli_total_true'] = $_OMP_html['cli_total_false'] = '';
    }
    if ($_OMP_cli_total) {
        $_OMP_get['where'] .= ' GROUP BY inv.'.
            $_OMP_invoices_fld['client_pkey'];
        if (!empty($_POST['form']['enc_supplier_pkey'])) {
            $_OMP_get['where'] .= ', '.
                $_OMP_invoices_fld['supplier_pkey'];
        }
    }
    $_OMP_get['sql'] = $_OMP_sql['select'].' '.$_OMP_get['where'];
    if ($_OMP_get['sort'] != '') { // Add sorting to sql
        $_OMP_get['sql'] .= $_OMP_sql['sort_record'][$_OMP_get['sort']];
    } else {
        $_OMP_get['sql'] .= $_OMP_sql['sort_default'];
    }
    $_OMP_get['sql'] = str_replace(
        ' ?%',
        (($_OMP_get['sort_type']) ? ' DESC' : ' ASC'),
        $_OMP_get['sql']
    );
    return;
}
/**
* End functions
*/
// Templates and i18n text
$_OMP_tpl = OMP_TPL_MENU.'0, 9, 30, 1910, 1911, 1913';
// Please note: need 85 for OMP_genErr() (see base.php)
$_OMP_lcl = '29, 35, 36, 50, 51, 83, 85, 86, 98, 99, 100,
    106, 110, 120, 121, 504, 703, 1500, 1501, 1502, 1503,
    1504, 1900, 1901, 1902, 1903, 3004';
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
$sort_record[2] = $_OMP_LC[703];
$sort_record[3] = $_OMP_LC[1503];
makeSqlReport(); // Makes sql
$statistics['turnover'] = 0;
$_OMP_html['table'] = $_OMP_html['table_body'] = '';
$_OMP_db_result = $_OMP_db->query($_OMP_get['sql']);
if ($_OMP_db_result->numRows() > 0) {
    if ($spreadsheet) {
        $worksheet->setMerge(0, 0, 0, 7);
        $firstline = $_OMP_LC[50].' '.$_OMP_LC[1500].' '.
          strtolower($_OMP_LC[1900]).' '.$_POST['date_start'].
          '/'.$_POST['date_start'].' '.strtolower($_OMP_LC[1901]).
          ' '.$_POST['date_end'].'/'.$_POST['date_end'];
        $worksheet->write(0, 0, $firstline, $format_title);
        $worksheet->write(1, 0, $_OMP_LC[100], $format_column_header);
        $worksheet->write(1, 1, $_OMP_LC[504], $format_column_header);
        $worksheet->write(1, 2, $_OMP_LC[1501], $format_column_header);
        $worksheet->write(1, 3, $_OMP_LC[703], $format_column_header);
        $worksheet->write(1, 4, $_OMP_LC[1503], $format_column_header);
        $worksheet->write(1, 5, $_OMP_LC[110], $format_column_header);
        $worksheet->write(1, 6, $_OMP_LC[1502], $format_column_header);
        $worksheet->write(1, 7, $_OMP_LC[106], $format_column_header);
        $row = 2;
    } else {
        // Main GET elements go in $getstring, skip sql
        $new_get = $_OMP_get;
        unset($new_get['sql']);
        $getstring = OMP_makeGet($new_get); // See functions.php
        $tmp = ($_OMP_get['where'] != '') ? 
            '&where='.urlencode($_OMP_get['where']) : '';
        // HTML links to sort records. See functions.php
        $sort_record = OMP_recSortLinks($sort_record, 'sort_record', $tmp);
    }
    while ($_OMP_rec_list = $_OMP_db_result->fetchRow()) {
        if ($spreadsheet) {
            $worksheet->write($row, 0, $_OMP_rec_list['client_pkey']);
            $worksheet->write($row, 1, $_OMP_rec_list['supplier_pkey']);
            $worksheet->write($row, 2, $_OMP_rec_list['invoice_pkey']);
            $start = new DateTime($_OMP_rec_list['date']);
            $span = $start->diff(new DateTime('1899-12-30'));
            $worksheet->write($row, 3, $span->days, $format_date);
            $start = new DateTime($_OMP_rec_list['due_date']);
            $span = $start->diff(new DateTime('1899-12-30'));
            $worksheet->write($row, 4, $span->days, $format_date);
            if (!empty($_OMP_rec_list['paymnt_date'])) {
                $start = new DateTime($_OMP_rec_list['paymnt_date']);
                $span = $start->diff(new DateTime('1899-12-30'));
                $worksheet->write($row, 5, $span->days, $format_date);
            }
            $worksheet->writeNumber($row, 6, $_OMP_rec_list['amount'], 
                $format_number);
            $worksheet->write($row, 7, $_OMP_rec_list['note']);
            $row++;
        } else {
            OMP_makeVars();
            eval("\$_OMP_html['table_body'] .= \"".$_OMP_TPL[1913]."\";");
        }
    }
    if ($spreadsheet) {
        $_OMP_db->disconnect();
        $workbook->close();
        exit();
    } else {
        $statistics['turnover'] = ($statistics['turnover'] == 0) ? 
            '' : $_OMP_LC[51].' '.number_format($statistics['turnover'], 2, 
            $_SESSION['dp'], $_SESSION['ts']);

        eval("\$_OMP_html['table'] = \"".$_OMP_TPL[1911]."\";");
    }
} else {
    $statistics['turnover'] = 0;
    $subform = '<table class="record"><tr class="label"><th>'.
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
$_OMP_url = OMP_PATH_SCRIPT;
eval("\$_OMP_html['include'] = \"".$_OMP_TPL[1910]."\";");
$_OMP_html['page_title'] = ($_OMP_get['popup']) ? '' : $_OMP_LC[50].' '.$_OMP_LC[1500];
$_OMP_html['browser_title'] = $_OMP_LC[50].' '.$_OMP_LC[1500];
/* @see functions.php */
OMP_drawer();
/* mdc-toolbar */
$_OMP_html['toolbar'] = '';
eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
OMP_lose();
?>
