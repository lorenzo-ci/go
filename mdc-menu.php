<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | OMP Version 1.1                                                      |
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
// | Author: Lorenzo Ciani <lorenzo@ciani.it>                              |
// +----------------------------------------------------------------------+
//
// $Id: mdc-menu.php,v 0.9 $
//
// HREF links for mdc-menu, required by base.php
// and by its function OMP_genErr()
//
/* OMP_link() is defined in functions.php */
$href_params = OMP_link('&list=1&action=read', true);
$_OMP_mdc_menu['orders'] = 'orders.php'.$href_params;
$_OMP_mdc_menu['tbcs'] = 'tbcs.php'.$href_params;
$_OMP_mdc_menu['scan'] = 'ocr-load.php';
$_OMP_mdc_menu['deliveries'] = 'deliveries.php'.$href_params;
$_OMP_mdc_menu['invoices'] = 'invoices.php'.$href_params;
$_OMP_mdc_menu['products'] = 'products.php'.$href_params;
$_OMP_mdc_menu['orders_report'] = 'orders-report.php';
$_OMP_mdc_menu['invoices_report'] = 'invoices-report.php';
$_OMP_mdc_menu['commission_report'] = 'commission-report.php';
$_OMP_mdc_menu['deliveries_report'] = 'deliveries-report.php';
$_OMP_mdc_menu['enduses'] = 'enduses.php'.$href_params;
$_OMP_mdc_menu['currencies'] = 'currencies.php'.$href_params;
$_OMP_mdc_menu['ranges'] = 'ranges.php'.$href_params;
$_OMP_mdc_menu['clients'] = 'clients.php'.$href_params;
$_OMP_mdc_menu['client_contacts'] = 'clientcontacts.php'.$href_params;
$_OMP_mdc_menu['payments'] = 'payments.php'.$href_params;
$_OMP_mdc_menu['terms'] = 'terms.php'.$href_params;
$_OMP_mdc_menu['suppliers'] = 'suppliers.php'.$href_params;
$_OMP_mdc_menu['supplier_contacts'] = 'suppliercontacts.php'.$href_params;
$_OMP_mdc_menu['zones'] = 'zones.php'.$href_params;
$_OMP_mdc_menu['banks'] = 'banks.php'.$href_params;
$_OMP_mdc_menu['shippers'] = 'shippers.php'.$href_params;
$_OMP_mdc_menu['um'] = 'um.php'.$href_params;
$_OMP_mdc_menu['user'] = 'user.php'.OMP_link('action=read', true);
$_OMP_mdc_menu['cp'] = 'cp.php';
$_OMP_mdc_menu['exit'] = 'index.php?logout=1';
?>
