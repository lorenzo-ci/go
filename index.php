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
// $Id: index.php,v 0.8 $
//
// Main menu
//
$_OMP_tpl = '0, 2, 12, 15, 30, 32, 48, 60, 65, 66';
$_OMP_lcl = '1, 3, 4, 5, 6, 7, 9, 25, 27, 32, 35,
    41, 49, 50, 100, 127, 700, 703, 800, 801, 806,
    807, 809, 999, 1100, 1300, 1400, 1500, 1501,
    1503, 1505, 2000, 4000';
require 'base.php';
isset($_GET['sub']) or $_GET['sub'] = '';
$_OMP_html['browser_title'] = $_OMP_LC[35];
eval ("\$_OMP_html['drawer'] = \"".$_OMP_TPL[2]."\";");
eval ("\$_OMP_html['drawer_button'] = \"".$_OMP_TPL[12]."\";");
eval ("\$_OMP_html['logo'] = \"".$_OMP_TPL[32]."\";");
eval ("\$_OMP_html['drawer_init'] = \"".$_OMP_TPL[30]."\";");
/* mdc-list */
/* open orders */
/* or mdc-data-table__cell--numeric */
$_OMP_html['header1_numeric'] = $_OMP_html['header2_numeric'] =
    $_OMP_html['header3_numeric'] = $_OMP_html['header4_numeric'] = '';
$_OMP_html_header[0] = $_OMP_LC[700];
$_OMP_html_header[1] = $_OMP_LC[100];
$_OMP_html_header[2] = $_OMP_LC[806];
$_OMP_html_header[3] = $_OMP_LC[807];
eval ("\$_OMP_html['list'] = \"".$_OMP_TPL[65]."\";");
$row_id = 0;
$_OMP_db_result = $_OMP_db->query('SELECT * FROM open_orders');
while ($_OMP_rec = $_OMP_db_result->fetchRow()) {
    // Assuming DB's default fetchmode is DB_FETCHMODE_ORDERED
    $row_id++;
    $_OMP_html['rec_link'] = 'orders.php'.
        OMP_link(
            '&filter=1&pkey='.
            urlencode(html_entity_decode($_OMP_rec['pkey'])),
            true
        );
    $_OMP_html['label0'] = $_OMP_html['label1'] =
        $_OMP_html['label2'] = $_OMP_html['label3'] =
        $_OMP_html['cell1_numeric'] = $_OMP_html['cell2_numeric'] =
        $_OMP_html['cell3_numeric'] = $_OMP_html['cell4_numeric'] = '';
    $_OMP_html['rec0'] = $_OMP_rec['pkey'];
    $_OMP_html['rec1'] = $_OMP_rec['client_pkey'];
    $_OMP_html['rec2'] = strftime('%x', strtotime($_OMP_rec['eta']));
    $_OMP_html['rec3'] = strftime('%x', strtotime($_OMP_rec['ship_before']));
    eval("\$_OMP_html['list'] .= \"".$_OMP_TPL[66]."\";");
}
$_OMP_html['aria_label'] = $_OMP_LC[809];
eval("\$_OMP_html['list1'] = \"".$_OMP_TPL[60]."\";");
/* open deliveries */
$_OMP_html_header[0] = $_OMP_LC[801];
$_OMP_html_header[1] = $_OMP_LC[100];
$_OMP_html_header[2] = $_OMP_LC[703];
$_OMP_html_header[3] = $_OMP_LC[806];
eval ("\$_OMP_html['list'] = \"".$_OMP_TPL[65]."\";");
$row_id = 0;
$_OMP_db_result = $_OMP_db->query('SELECT * FROM open_deliveries');
while ($_OMP_rec = $_OMP_db_result->fetchRow()) {
    // Assuming DB's default fetchmode is DB_FETCHMODE_ORDERED
    $row_id++;
    $_OMP_html['rec_link'] = 'deliveries.php'.
        OMP_link(
            '&filter=1&pkey='.
            urlencode(html_entity_decode($_OMP_rec['pkey'])),
            true
        );
    $_OMP_html['label0'] = $_OMP_html['label1'] =
        $_OMP_html['label2'] = $_OMP_html['label3'] =
        $_OMP_html['cell1_numeric'] = $_OMP_html['cell2_numeric'] =
        $_OMP_html['cell3_numeric'] = $_OMP_html['cell4_numeric'] = '';
    $_OMP_html['rec0'] = $_OMP_rec['pkey'];
    $_OMP_html['rec1'] = $_OMP_rec['client_pkey'];
    $_OMP_html['rec2'] = strftime('%x', strtotime($_OMP_rec['date']));
    $_OMP_html['rec3'] = strftime('%x', strtotime($_OMP_rec['eta']));
    eval("\$_OMP_html['list'] .= \"".$_OMP_TPL[66]."\";");
}
$_OMP_html['aria_label'] = $_OMP_LC[800];
eval("\$_OMP_html['list2'] = \"".$_OMP_TPL[60]."\";");
/* open invoices */
$_OMP_html_header[0] = $_OMP_LC[1501];
$_OMP_html_header[1] = $_OMP_LC[100];
$_OMP_html_header[2] = $_OMP_LC[703];
$_OMP_html_header[3] = $_OMP_LC[1503];
eval ("\$_OMP_html['list'] = \"".$_OMP_TPL[65]."\";");
$row_id = 0;
$_OMP_db_result = $_OMP_db->query('SELECT * FROM open_invoices');
while ($_OMP_rec = $_OMP_db_result->fetchRow()) {
    // Assuming DB's default fetchmode is DB_FETCHMODE_ORDERED
    $row_id++;
    $_OMP_html['rec_link'] = 'invoices.php'.
        OMP_link(
            '&filter=1&pkey='.
            urlencode(html_entity_decode($_OMP_rec['pkey'])).
            '&client_pkey='.
            urlencode(html_entity_decode($_OMP_rec['client_pkey'])).
            '&date='.
            urlencode(html_entity_decode($_OMP_rec['date'])),
            true
        );
    $_OMP_html['label0'] = $_OMP_html['label1'] =
        $_OMP_html['label2'] = $_OMP_html['label3'] =
        $_OMP_html['cell1_numeric'] = $_OMP_html['cell2_numeric'] =
        $_OMP_html['cell3_numeric'] = $_OMP_html['cell4_numeric'] = '';
    $_OMP_html['rec0'] = $_OMP_rec['pkey'];
    $_OMP_html['rec1'] = $_OMP_rec['client_pkey'];
    $_OMP_html['rec2'] = strftime('%x', strtotime($_OMP_rec['date']));
    $_OMP_html['rec3'] = strftime('%x', strtotime($_OMP_rec['due_date']));
    eval("\$_OMP_html['list'] .= \"".$_OMP_TPL[66]."\";");
}
unset($_OMP_rec);
$_OMP_html['aria_label'] = $_OMP_LC[1500];
eval ("\$_OMP_html['list3'] = \"".$_OMP_TPL[60]."\";");
$_OMP_html['include'] =
    $_OMP_html['list1'].
    $_OMP_html['list2'].
    $_OMP_html['list3'];
// eval ("\$_OMP_html['include'] = \"".$_OMP_html['list']."\";");
$_OMP_html['page_title'] = $_OMP_LC[32];
eval ("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
OMP_lose();
?>
