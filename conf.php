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
// $Id: conf.php,v 0.7 $
//
// System-wide default values. Required by base.php
//

// Prints alert on orders to be invoiced
define('OMP_MONTH_INV', false);
define('OMP_PIC_2', 'Susan');
define('OMP_PIC_4', 'Claus');
// Path for setcookie - see also $_OMP_conf['path']
define('OMP_COOKIE_PATH', '/'.substr($_SERVER['PHP_SELF'], 1,
    strrpos($_SERVER['PHP_SELF'],'/')));
// Shall we run on SSL - see also $_OMP_conf['path']
define('OMP_SSL', 1);
/* Choose if HREF link parameters should be
 * passed through base64_encode */
define('OMP_ACTION_ENCODE', false);
// URI for HREF links
define('OMP_PATH', OMP_COOKIE_PATH);
// URL for HREF links
define('OMP_ABS_PATH', 'http'.((OMP_SSL == 1) ? 's' : '').
    '://'.$_SERVER['SERVER_NAME'].OMP_COOKIE_PATH);
// File name of the current script
define('OMP_SCRIPT', substr($_SERVER['PHP_SELF'],
    strrpos($_SERVER['PHP_SELF'],'/') + 1));
// File name of script with path
define('OMP_PATH_SCRIPT', OMP_PATH.OMP_SCRIPT);
// File name of script with absolute URL
define('OMP_ABS_PATH_SCRIPT', OMP_ABS_PATH.OMP_SCRIPT);
// Secret word for md5 hash
define('OMP_SECRET', 'lovemywife');
// MSAccess-style filter strings
// e.g. * and % instead of ? and _
define('OMP_FILTER_STYLE', true);
// Default country
define('OMP_COUNTRY', 'Italia');
// Max items on combo list
define('OMP_MAXLIST', 10);
// Width of combo list
/* this is now the width of the mdc lists */
/* it was 20 for HTML drop-down lists */
define('OMP_DDL_WIDTH_STD', '400');
define('OMP_DDL_WIDTH_SML', '150');
// Default locale for setlocale()
// DO WE REALLY NEED THIS?
// define('OMP_LOCALE', 'it_IT');
// Default currency
define('OMP_CURRENCY', 'EUR');
$_OMP_conf['currency'] = 'EUR';
// Thousands Separator
define('OMP_TS', '.');
// Decimal Point
define('OMP_DP', ',');
// Price decimal points
define('OMP_PRICE_D', '2');
// Price format for sprintf
define('OMP_PRICE_F', '%01.'.OMP_PRICE_D.'f');
// Price step for html input
$_OMP_conf['price_step'] = '0.01';
// Quantity decimal points
define('OMP_QTY_D', '2');
// Quantity format for sprintf
define('OMP_QTY_F', '%01.'.OMP_QTY_D.'f');
// Quantity step for html input
$_OMP_conf['qty_step'] = '0.01';
$_OMP_conf['qty_max'] = '1000000000';
$_OMP_conf['price_max'] = '1000000';
$_OMP_conf['amount_max'] =
    $_OMP_conf['qty_max'] * $_OMP_conf['price_max'];
$_OMP_conf['amount_min'] = -$_OMP_conf['amount_max'];
// Idle time in seconds
define('OMP_IDLE', 0);
// Default Cascading Style Sheet
define('OMP_CSS', 'css/def.css');
/* switch and switch script templates */
define('OMP_TPL_SWITCH', '88, 89, 90, ');
$_OMP_conf['switch'] = 88;
$_OMP_conf['switch_script'] = 89;
$_OMP_conf['switch_on'] = 90;
// Default templates for menu
define('OMP_TPL_MENU', '0, 2, 12, 15, 32, ');
$_OMP_conf['record_template'] = 15;
// Default templates for record-read
define('OMP_TPL_READ', OMP_TPL_MENU.'5, 6, 7, 9, 13,
    19, 20, 21, 22, 23, 28, 30, 31, 32, 48, 60, ');
// Default templates for mdc-select
define('OMP_TPL_SELECT', '80,
    81, 82, 83, 84, 85, 86, 87, ');
// Default templates for record-edit
define('OMP_TPL_EDIT', OMP_TPL_MENU.
    '4, 14, 48, 51, '.
    OMP_TPL_SELECT.
    OMP_TPL_SWITCH);
// Default templates for record-filter
define('OMP_TPL_FILTER', OMP_TPL_MENU.
    '14, 20, 49, '.
    OMP_TPL_SELECT.
    OMP_TPL_SWITCH);
$_OMP_conf['filter_template'] = 49;
// Default templates for record-new
define('OMP_TPL_NEW', OMP_TPL_MENU.
    '4, 14, 48, 50, 69, '.
    OMP_TPL_SELECT.
    OMP_TPL_SWITCH);
// Default templates for record-delete
define('OMP_TPL_DEL', OMP_TPL_MENU.'4, 14, 47, 48, ');
$_OMP_conf['delete_template'] = 47;
// Default value of required field symbol //
// $_OMP_TPL[74] = '';
// List of locale strings for menu
define('OMP_LCL_MENU', '1, 2, 3, 4, 5, 6, 7, 8, 9,
    20, 21, 22, 25, 27, 28, 30, 31, 32, 33, 35,
    41, 49, 50, 55, 60, 700, 800, 801, 999, 1100,
    1300, 1400, 1500, 1505, 2000, 4000, 4001,
    4002, 5000, 5100, ');
// List of locale strings for record read.
// Need 85 for genError() (see base.php)
define('OMP_LCL_READ', OMP_LCL_MENU.'15, 16,
    17, 18, 19, 20, 21, 22, 26, 33, 34, 35,
    69, 73, 83, 85, 86, 90, 94, 98, 99, ');
// List of locale strings for record edit.
// Need 85 for genError() (see base.php)
define('OMP_LCL_EDIT', OMP_LCL_MENU.
    '19, 29, 33, 37, 38, 40, 83, 84, 85,
    88, 98, 99, 119, 120, 121, 124, 125, ');
// List of locale strings for record filter
define('OMP_LCL_FILTER', OMP_LCL_MENU.
    '20, 29, 36, 37, 98, 99,
    119, 120, 121, 124, 125, ');
// List of locale strings for new record.
// Need 85 for genError() (see functions.php)
define('OMP_LCL_NEW', OMP_LCL_MENU.
    '19, 21, 29, 37, 39, 45,
    83, 85, 88, 120, 121, 124, 125, ');
// List of locale strings for record delete.
// Need 85 for genError() (see functions.php)
define('OMP_LCL_DEL', OMP_LCL_MENU.'22, 37, 44,
    46, 52, 83, 84, 85, 87, ');
// First year for which we have records
define('OMP_YEAR_START', 1992);
define('OMP_YEAR_MAX', date("Y") + 5);
// No data before this year-month
define('OMP_MONTH_MIN', OMP_YEAR_START.'-01');
$_OMP_month_min = OMP_MONTH_MIN;
// No data after this year-month
define('OMP_MONTH_MAX', OMP_YEAR_MAX.'-01');
$_OMP_month_max = OMP_MONTH_MAX;
// No data before this date
define('OMP_DATE_MIN', OMP_MONTH_MIN.'-01');
$_OMP_date_min = OMP_DATE_MIN;
// No data after this date
define('OMP_DATE_MAX', OMP_MONTH_MAX.'-01');
$_OMP_date_max = OMP_DATE_MAX;
// Number of years for reports
define('OMP_YEAR_MAX_REPORT', 1);
// Default XML encoding and charset
// This will be set according to the database server encoding
$_OMP_encoding = 'UTF-8';
// Session name, need variable for eval templates
$_OMP_sname = '_OMP_';
// Copyright notice
$_OMP_conf['copyright'] = 'Copyright &copy; 2003 - 2023 ';
// Link to corporate website
$_OMP_conf['org_link'] = 'http://www.chaneyinternational.com';
$_OMP_conf['org'] = 'Chaney International S.a.s.';
/* Company logo */
$_OMP_html['logo_img'] = 'images/logo.png';
$_OMP_html['logo'] = $_OMP_html['record_title'] = '';
// Placeholder for javascript form onload. See template no. 0
$_OMP_onload = '';
// See dd-*.php
$_OMP_combo_required = false;
$_OMP_default_supplier = "WELLMAN LTD";
?>
