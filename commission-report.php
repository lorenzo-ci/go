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
// | See the GNU General Public License                                   |
// | <http://www.gnu.org/copyleft/gpl.html> for more details.             |
// +----------------------------------------------------------------------+
// | Author: Lorenzo Ciani <lorenzo@ciani.it>                             |
// +----------------------------------------------------------------------+
//
// $Id: commission-report.php,v 0.8 $
//
// Commission report
//
require_once 'base.php';
require_once 'schemas/db-schema.php';
require_once 'schemas/clients-schema.php';
require_once 'schemas/suppliers-schema.php';
require_once 'schemas/products-schema.php';
require_once 'lib/dd-sql.php'; // SQL for drop-down lists
require_once 'schemas/invoices-schema.php'; // Invoices schema
$spreadsheet = $report_commission = isset($_POST['report_commission_button']);
 // print_r($_POST); exit;
if ((!empty($_POST['commission_button']) || !empty($_POST['remittance_button']))
    && ($_POST['commission'] != '' && isset($_POST['selected']))) {
    $comm_rate = OMP_checkNum($_POST['commission'], 'Commission')/100;
    $commission = true;
} else {
    $comm_rate = '';
    $commission = false;
}
if (isset($_POST['remittance_button']) && isset($_POST['remittance'])
    && isset($_POST['selected'])) {
    $remit_label = OMP_checkTxt($_POST['remittance'], 'Remittance', 50, true);
    !empty($remit_label) or $remit_label = 'NULL';
    $remittance = true;
} else {
    $remit_label = '';
    $remittance = false;
}
if (isset($_POST['report_remittance_button']) && !empty($_POST['remittance'])) {
    $remit_label = OMP_checkTxt($_POST['remittance'], 'Remittance', 50);
    $spreadsheet = $report_remittance = true;
} else {
    $report_remittance = false;
}
if ($report_remittance || $report_commission) {
    require_once 'Spreadsheet/Excel/Writer.php';
    $workbook = new Spreadsheet_Excel_Writer();
    $format_title = $workbook->addFormat();
    $format_title->setBold();
    $format_title->setAlign('center');
    $format_column_header = $workbook->addFormat();
    $format_column_header->setBottom(1);
    $format_column_header->setBold();
    $format_column_header->setTextWrap();
    $format_column_header_right = $workbook->addFormat();
    $format_column_header_right->setBottom(1);
    $format_column_header_right->setBold();
    $format_column_header_right->setTextWrap();
    $format_column_header_right->setHAlign('right');
    $format_column_footer = $workbook->addFormat();
    $format_column_footer->setTop(1);
    $format_column_footer->setBold();
    $format_column_footer->setHAlign('right');
    $format_date = $workbook->addFormat();
    $format_date->setNumFormat('date');
    $format_date->setHAlign('left');
    $format_number = $workbook->addFormat();
    $format_number->setNumFormat('#,##0.00');
    $format_number_footer = $workbook->addFormat();
    $format_number_footer->setTop(1);
    $format_number_footer->setBold();
    $format_number_footer->setNumFormat('#,##0.00');
    $workbook->send('report.xls');
    $worksheet = $workbook->addWorksheet('Report');
    $worksheet->hideGridlines();
    $worksheet->centerHorizontally();
    $worksheet->centerVertically();
    $worksheet->setInputEncoding($_OMP_encoding);
}
/**
 * SQL code
 */
$_OMP_sql['sort'] = ' ORDER BY '.
    $_OMP_invoices_fld['supplier_pkey'].', '.
    $_OMP_invoices_fld['client_pkey'].', '.
    $_OMP_invoices_fld['date'].', '.
    $_OMP_invoices_fld['pkey'];

$_OMP_sql['select'] = 'SELECT DISTINCT '.
    $_OMP_invoices_fld['supplier_pkey'].
    ' AS supplier_pkey, '.$_OMP_invoices_fld['client_pkey'].
    ' AS client_pkey, '.$_OMP_invoices_fld['pkey'].
    ' AS invoice_pkey, '.$_OMP_invoices_fld['date'].
    ' AS date, '.$_OMP_invoices_fld['due_date'].
    ' AS due_date, '.$_OMP_invoices_fld['paymnt_date'].
    ' AS paymnt_date, '.$_OMP_invoices_fld['amount'].
    ' AS amount, '.$_OMP_invoices_fld['commission'].
    ' AS commission, '.$_OMP_invoices_fld['rim'].
    ' AS remittance FROM '.$_OMP_tables['invoices'];

$_OMP_get['sql_remittance'] = 'SELECT DISTINCT '.
    $_OMP_invoices_fld['supplier_pkey'].
    ' AS supplier_pkey, '.$_OMP_clients_fld['wilclient'].
    ' AS wilclient, '.$_OMP_invoices_fld['client_pkey'].
    ' AS client_pkey, '.$_OMP_invoices_fld['pkey'].
    ' AS invoice_pkey, '.$_OMP_invoices_fld['date'].
    ' AS date, '.$_OMP_invoices_fld['due_date'].
    ' AS due_date, '.$_OMP_invoices_fld['paymnt_date'].
    ' AS paymnt_date, '.$_OMP_invoices_fld['amount'].
    ' AS amount, '.$_OMP_invoices_fld['commission'].
    ' AS commission, '.$_OMP_invoices_fld['rim'].
    ' AS remittance FROM '.$_OMP_tables['invoices'].
    ' LEFT JOIN '.$_OMP_tables['clients'].
    ' USING ('.$_OMP_clients_fld['pkey'].')';
    
// select and order by supplier, year of invoice, month of invoice, cli_id, date of invoice
// SELECT DISTINCT "Fatture"."IDFornitore" AS supplier_pkey, cli_id, "Fatture"."IDCliente" AS client_pkey, "Fatture"."IDFattura" AS invoice_pkey, "Fatture"."DataFattura" AS date, "Fatture"."DataPagamento" AS paymnt_date, "Fatture"."Importo" AS amount, "Fatture"."Commissione" AS commission, "Fatture"."Rim" AS remittance FROM "Fatture" LEFT JOIN sup_cli_id USING ("IDFornitore", "IDCliente") WHERE "Fatture"."Rim" = '04/05/20' ORDER BY "Fatture"."IDFornitore", "Fatture"."IDCliente", "Fatture"."DataFattura", "Fatture"."IDFattura"

$_OMP_sql['update_comm'] = 'UPDATE '.$_OMP_tables['invoices'].
    ' SET '.$_OMP_invoices_fld['commission'].' = '.(($comm_rate == 0) ? 'NULL' : 
    $_OMP_invoices_fld['amount'].' * '.$comm_rate);

$_OMP_sql['update_remit'] = 'UPDATE '.$_OMP_tables['invoices'].
    ' SET '.$_OMP_invoices_fld['rim'].' = '.OMP_db_quote($remit_label);

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
    global $checkbox_value, $_OMP_rec, $_OMP_rec_list, $statistics, $_OMP_TPL;
    $statistics['amount'] += $_OMP_rec_list['amount'];
    $statistics['commission'] += $_OMP_rec_list['commission'];
    $_OMP_rec_list = array_map('OMP_htmlentities', $_OMP_rec_list);
    $url = OMP_PATH.'suppliers.php?'.
        OMP_link(
            'filter=1&pkey='.
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
    $url = OMP_PATH.'clients.php?'.
        OMP_link(
            'filter=1&pkey='.
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
    $checkbox_value = $_OMP_rec_list['invoice_pkey'];
    $url = OMP_PATH.'invoices.php?'.
        OMP_link(
            'filter=1&pkey='.
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
    $_OMP_rec_list['date'] = 
        ($_OMP_rec_list['date'] == '') ? '' : strftime('%x', 
            strtotime($_OMP_rec_list['date']));
    $_OMP_rec_list['due_date'] = 
        ($_OMP_rec_list['due_date'] == '') ? '' : strftime('%x', 
            strtotime($_OMP_rec_list['due_date']));
    $_OMP_rec_list['paymnt_date'] = 
        ($_OMP_rec_list['paymnt_date'] == '') ? '' : strftime('%x', 
            strtotime($_OMP_rec_list['paymnt_date']));
    empty($_OMP_rec_list['amount']) or 
        $_OMP_rec_list['amount'] = number_format($_OMP_rec_list['amount'], 
            2, $_SESSION['dp'], $_SESSION['ts']);
    empty($_OMP_rec_list['commission']) 
        or $_OMP_rec_list['commission'] = 
            number_format($_OMP_rec_list['commission'], 
                2, $_SESSION['dp'], $_SESSION['ts']);
}

function makeSqlReport()
{
    global $commission, $_OMP_db, $_OMP_invoices_fld, $_OMP_get, $_OMP_html, 
        $_OMP_rec, $_OMP_rec_sf, $_OMP_sql, $_OMP_tables, $remit_label, 
        $remittance, $report_remittance;
    // Makes SQL WHERE parameters
    $_OMP_rec_sf['date_start'] = 
        empty($_POST['date_start']) ? strftime("%Y-%m") : $_POST['date_start'];
    $_OMP_rec_sf['date_end'] = 
        empty($_POST['date_end']) ? strftime("%Y-%m") : $_POST['date_end'];
    if ($_OMP_rec_sf['date_start'] > $_OMP_rec_sf['date_end']) {
        $_OMP_rec_sf['date_start'] = $_OMP_rec_sf['date_end'];
    }
    $year_end = (int)substr($_OMP_rec_sf['date_end'], 0, 4);
    $year_start = (int)substr($_OMP_rec_sf['date_start'], 0, 4);
    // Max years if no filters to avoid memory full
    if ($year_end - $year_start > OMP_YEAR_MAX_REPORT 
        && (empty($_OMP_rec['client_pkey']) 
        && empty($_OMP_rec['supplier_pkey']))) {
        $month_start = substr($_OMP_rec_sf['date_start'], -2, 2);
        $_OMP_rec_sf['date_end'] = 
            (string)($year_start + OMP_YEAR_MAX_REPORT)."-".$month_start;
    }
    if ($_OMP_rec_sf['date_start'] > $_OMP_rec_sf['date_end']) {
        $_OMP_rec_sf['date_end'] = $_OMP_rec_sf['date_start'];
    }
    $_OMP_get['plan_start'] = $_OMP_rec_sf['date_start'].'-01';
    $_OMP_get['plan_end'] = OMP_endOfMonth($_OMP_rec_sf['date_end']);
    // Makes SQL statement
    if (isset($_POST['search_button']) || $_OMP_get['where'] == '') {
        $_OMP_get['where'] = 'WHERE ('.
        $_OMP_invoices_fld['date'].
        ' BETWEEN '.OMP_db_quote($_OMP_get['plan_start']).
        ' AND '.OMP_db_quote($_OMP_get['plan_end']).')';
    }
    $sql_filter = '';
    if (isset($_POST['form'])) {
        for (reset($_POST['form']); 
                $key = key($_POST['form']); 
                next($_POST['form'])) {
            if (!empty($_POST['form'][$key])) {
                if ($key == 'enc_client_pkey') {
                    $_OMP_rec['client_pkey'] = urldecode($_POST['form'][$key]);
                    $sql_filter .= ' AND lower('.
                    $_OMP_invoices_fld['client_pkey'].') = lower('.
                    OMP_db_quote(stripslashes($_OMP_rec['client_pkey'])).')';
                } elseif ($key == 'enc_supplier_pkey') {
                    $_OMP_rec['supplier_pkey'] = 
                    urldecode($_POST['form'][$key]);
                    $sql_filter .= ' AND lower('.
                    $_OMP_invoices_fld['supplier_pkey'].') = lower('.
                    OMP_db_quote(stripslashes($_OMP_rec['supplier_pkey'])).')';
                }
            }
        }
        $_OMP_get['where'] .= $sql_filter;
    }
    if ($commission) {
        $_OMP_get['sql'] = $_OMP_sql['update_comm'].' '.$_OMP_get['where'];
        $invoice_pkey = array_map('stripslashes', $_POST['selected']);
        $invoice_pkey = array_map('OMP_db_quote', $_POST['selected']);
        $_OMP_get['sql'] .= ' AND '.$_OMP_invoices_fld['pkey'].
            ' IN ('.implode(', ', $invoice_pkey).')';
        $_OMP_db->exec($_OMP_get['sql']);
    }
    if ($remittance) {
        $_OMP_get['sql'] = $_OMP_sql['update_remit'].' '.$_OMP_get['where'];
        $invoice_pkey = array_map('stripslashes', $_POST['selected']);
        $invoice_pkey = array_map('OMP_db_quote', $_POST['selected']);
        $_OMP_get['sql'] .= ' AND '.$_OMP_invoices_fld['pkey'].
            ' IN ('.implode(', ', $invoice_pkey).')';
        $_OMP_db->exec($_OMP_get['sql']);
    }
    if ($report_remittance) {
        $_OMP_sql['select'] = $_OMP_get['sql_remittance'];
        $_OMP_get['where'] = 'WHERE '.$_OMP_invoices_fld['rim']. ' = '.
        OMP_db_quote($remit_label);
    }
    $_OMP_get['sql'] = $_OMP_sql['select'].' '.$_OMP_get['where'].
        $_OMP_sql['sort'];
    return;
}
/**
* End functions
*/
// Templates and i18n text
$_OMP_tpl = OMP_TPL_MENU.'9, 30, 72, 1930, 1931, 1932, 1933';
// Please note: need 85 for OMP_genErr() (see base.php)
$_OMP_lcl = '29, 35, 36, 38, 50, 83, 85, 97, 100, 101, 110, 
    106, 120, 121, 504, 703, 721, 722, 1501, 1502, 1503, 1505,
    1506, 1507, 1900, 1901, 1904';
if (!$report_commission && !$report_remittance) {
    OMP_load(); // See base.php
    // Select all checkboxes script
    eval("\$_OMP_html['script'] = \"".$_OMP_TPL[72]."\";");
}
// No data before this year
$_OMP_date_min = OMP_YEAR_START."-01";
makeSqlReport(); // Makes sql
$statistics['amount'] = $statistics['commission'] = 0;
$_OMP_url = OMP_PATH_SCRIPT;
$_OMP_html['table'] = $_OMP_html['table_body'] = '';
$_OMP_db_result = $_OMP_db->query($_OMP_get['sql']);
if ($_OMP_db_result->numRows() > 0) {
    if ($report_commission || $report_remittance) {
        $_OMP_tmp_a = $_OMP_db->queryOne('SELECT '.
            $_OMP_suppliers_fld['country'].' FROM '.$_OMP_tables['suppliers'].
            ' WHERE '.$_OMP_suppliers_fld['pkey'].' = '.
            OMP_db_quote($_OMP_rec['supplier_pkey']));
        if (strtolower($_OMP_tmp_a) != 'italia') {
            setlocale(LC_TIME, 'en_US');
            $query = $_OMP_db->query('SELECT * FROM locale WHERE pkey IN '. 
                '(94, 100, 101, 504, 703, 1501, 1502, 1506, 1900, 1901, 1902, '.
                '1904, 1905, 1906, 1907) AND lang = '.OMP_db_quote('en'));
            if ($query->numRows() == 0) {
                echo 'Sorry, no locale available for language code \'en\''.
                    '.<br>Please contact your system administrator.';
                $_OMP_db->disconnect();
                exit();
            }
            while ($lc_row = $query->fetchRow(MDB2_FETCHMODE_ORDERED)) {
                $_OMP_LC[$lc_row[0]] = $lc_row[2];
            }
            $query->free();
        }
        if ($report_commission) {
            $array_start = explode('-', $_POST['date_start']);
            $time_start = mktime(0, 0, 0, $array_start[1], 1, $array_start[0]);
            $date_start = date('Y-m-d', $time_start);
            $array_end = explode('-', $_POST['date_end']);
            $time_end = mktime(0, 0, 0, $array_end[1], 1, $array_end[0]);
            $date_end = date('Y-m-d', $time_end);
            $firstline = $_OMP_LC[1506].' '.$_OMP_LC[1905].' ';
            if ($_POST['date_start'] != $_POST['date_end']) {
                $firstline .= $_OMP_LC[1900].' '.$date_start.' '.
                    $_OMP_LC[1901].' '.$date_end;
            } else {
                $firstline .= $date_start;
            }
            $worksheet->setHeader($firstline);
    //         $worksheet->setMerge(0, 0, 0, 5);
    //         $worksheet->write(0, 0, $firstline, $format_title);
            $worksheet->write(0, 0, $_OMP_LC[504], $format_column_header);
            $worksheet->write(0, 1, $_OMP_LC[100], $format_column_header);
            $worksheet->write(0, 2, $_OMP_LC[1501], $format_column_header);
            $worksheet->write(0, 3, $_OMP_LC[703], $format_column_header);
            $worksheet->write(0, 4, $_OMP_LC[1502], $format_column_header);
            $worksheet->write(0, 5, $_OMP_LC[1506], $format_column_header);
        } else {
            $firstline = $_OMP_LC[1906].' '.$_OMP_LC[94].' '.$_OMP_LC[1905];
            $worksheet->setHeader($firstline);
            $worksheet->write(0, 0, $_OMP_LC[1907], $format_column_header);
            $worksheet->write(0, 1, $_OMP_LC[504], $format_column_header);
            $worksheet->write(0, 2, $_OMP_LC[100], $format_column_header);
            $worksheet->write(0, 3, $_OMP_LC[101], $format_column_header);
            $worksheet->write(0, 4, $_OMP_LC[1501], $format_column_header);
            $worksheet->write(0, 5, $_OMP_LC[703], $format_column_header);
            $worksheet->write(0, 6, $_OMP_LC[1502], 
                $format_column_header_right);
            $worksheet->write(0, 7, $_OMP_LC[1506], 
                $format_column_header_right);
        }
        $row = 1;
    } else {
        // Main GET elements go in $getstring, skip sql
        $new_get = $_OMP_get;
        unset($new_get['sql']);
        $getstring = OMP_makeGet($new_get); // See functions.php
        $tmp = ($_OMP_get['where'] != '') ? 
            '&where='.urlencode($_OMP_get['where']) : '';
        // eval("\$list_record = \"".$_OMP_TPL[1932]."\";");
    }
    while ($_OMP_rec_list = $_OMP_db_result->fetchRow()) {
        if ($report_commission) {
            $worksheet->write($row, 0, $_OMP_rec_list['supplier_pkey']);
            $worksheet->write($row, 1, $_OMP_rec_list['client_pkey']);
            $worksheet->write($row, 2, $_OMP_rec_list['invoice_pkey']);
            $start = new DateTime($_OMP_rec_list['date']);
            $span = $start->diff(new DateTime('1899-12-30'));
            $worksheet->write($row, 3, $span->days, $format_date);
            $worksheet->write($row, 4, $_OMP_rec_list['amount'], 
                $format_number);
            $worksheet->write($row, 5, $_OMP_rec_list['commission'], 
                $format_number);
            $row++;
        } elseif ($report_remittance) {
            $worksheet->write($row, 0, $_OMP_rec_list['remittance']);
            $worksheet->write($row, 1, $_OMP_rec_list['supplier_pkey']);
            $worksheet->write($row, 2, $_OMP_rec_list['wilclient']);
            $worksheet->write($row, 3, $_OMP_rec_list['client_pkey']);
            $worksheet->write($row, 4, $_OMP_rec_list['invoice_pkey']);
            $start = new DateTime($_OMP_rec_list['date']);
            $span = $start->diff(new DateTime('1899-12-30'));
            $worksheet->write($row, 5, $span->days, $format_date);
            $worksheet->write($row, 6, $_OMP_rec_list['amount'], 
                $format_number);
            $worksheet->write($row, 7, $_OMP_rec_list['commission'], 
                $format_number);
            $row++;
        } else {
            OMP_makeVars();
            eval("\$_OMP_html['table_body'] .= \"".$_OMP_TPL[1933]."\";");
        }
    }
    if ($report_commission) {
        $worksheet->writeBlank($row, 3, $format_column_footer);    
        $worksheet->setMerge($row, 0, $row, 3);
        $worksheet->write($row, 0, $_OMP_LC[1904], $format_column_footer);
        $worksheet->writeFormula($row, 4, '=SUM(E2:E'.($row).')', 
            $format_number_footer);
        $worksheet->writeFormula($row, 5, '=SUM(F2:F'.($row).')', 
            $format_number_footer);
        $workbook->close();
        OMP_lose_no_html();
    } elseif ($report_remittance) {
        $worksheet->setMerge($row, 0, $row, 5);
        $worksheet->writeBlank($row, 0, $format_column_footer);
        $worksheet->writeBlank($row, 1, $format_column_footer);
        $worksheet->writeBlank($row, 2, $format_column_footer);
        $worksheet->writeBlank($row, 3, $format_column_footer);
        $worksheet->writeBlank($row, 4, $format_column_footer);
        $worksheet->write($row, 5, $_OMP_LC[1904], $format_column_footer);
        $worksheet->writeFormula($row, 6, '=SUM(G2:G'.($row).')', 
            $format_number_footer);
        $worksheet->writeFormula($row, 7, '=SUM(H2:H'.($row).')', 
            $format_number_footer);
        $workbook->close();
        OMP_lose_no_html();
    } else {
        $statistics['amount'] = number_format($statistics['amount'], 2, 
            $_SESSION['dp'], $_SESSION['ts']);
        $statistics['commission'] = number_format($statistics['commission'], 2, 
            $_SESSION['dp'], $_SESSION['ts']);
        eval("\$_OMP_html['table'] = \"".$_OMP_TPL[1931]."\";");
    }
} else {
    $statistics['amount'] = $statistics['commission'] = 0;
    $subform = '<table class="record"><tr class="label"><th>'.
        $_OMP_LC[83].'</th></tr></table>';
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
eval("\$_OMP_html['include'] = \"".$_OMP_TPL[1930]."\";");
$_OMP_html['page_title'] = ($_OMP_get['popup']) ? '' : $_OMP_LC[50].' '.
    $_OMP_LC[1505];
/* @see functions.php */
OMP_drawer();
$_OMP_html['browser_title'] = $_OMP_LC[50].' '.$_OMP_LC[1505];
/* mdc-toolbar */
$_OMP_html['toolbar'] = '';
eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
OMP_lose();
?>
