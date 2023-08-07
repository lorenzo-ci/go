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
// | Author: Lorenzo Ciani <lorenzo@ciani.it>                             |
// +----------------------------------------------------------------------+
//
// $Id: clients.php,v 0.9 $
//
// Manage clients records
//

require_once 'base.php';
require_once 'schemas/db-schema.php'; // Database schema
// Name of current master-table
$_OMP_tbl = 'clients';
require_once 'schemas/enduses-schema.php'; // Enduses schema
require_once 'schemas/payments-schema.php'; // Payments schema
require_once 'schemas/terms-schema.php'; // Terms schema
// SQL for drop-down lists
$_OMP_get['popup'] or require_once 'lib/dd-sql.php';
require_once 'schemas/clients-schema.php'; // Clients schema
// Table fields, length and keys
$_OMP_tbl_fld = $_OMP_clients_fld;
$_OMP_fld_len = $_OMP_clients_len;
$_OMP_tbl_key = $_OMP_clients_key;
/**
* SQL code
*/
$_OMP_sql['select'] = 'SELECT cli.'.$_OMP_tbl_fld['pkey'].
    ' AS pkey, cli.'.$_OMP_tbl_fld['name'].
    ' AS name, cli.'.$_OMP_tbl_fld['addr'].
    ' AS addr, cli.'.$_OMP_tbl_fld['zip'].
    ' AS zip, cli.'.$_OMP_tbl_fld['city'].
    ' AS city, cli.'.$_OMP_tbl_fld['state'].
    ' AS state, cli.'.$_OMP_tbl_fld['region'].
    ' AS region, cli.'.$_OMP_tbl_fld['country'].
    ' AS country, cli.'.$_OMP_tbl_fld['vat'].
    ' AS vat, cli.'.$_OMP_tbl_fld['tel'].
    ' AS tel, cli.'.$_OMP_tbl_fld['fax'].
    ' AS fax, cli.'.$_OMP_tbl_fld['note'].
    ' AS note, cli.'.$_OMP_tbl_fld['del_addr'].
    ' AS del_addr, cli.'.$_OMP_tbl_fld['zone'].
    ' AS zone, cli.'.$_OMP_tbl_fld['enduse_pkey'].
    ' AS enduse_pkey, cli.'.$_OMP_tbl_fld['credit'].
    ' AS credit, cli.'.$_OMP_tbl_fld['paymnt_pkey'].
    ' AS paymnt_pkey, cli.'.$_OMP_tbl_fld['paymnt_days'].
    ' AS paymnt_days, cli.'.$_OMP_tbl_fld['term_pkey'].
    ' AS term_pkey, cli.'.$_OMP_tbl_fld['active'].
    ' AS active, cli.'.$_OMP_tbl_fld['constock'].
    ' AS constock, cli.'.$_OMP_tbl_fld['wilclient'].
    ' AS wilclient, eu.'.$_OMP_enduses_fld['name'].
    ' AS enduse_label, payment.'.$_OMP_payments_fld['description'].
    ' AS paymnt_label, term.'.$_OMP_terms_fld['description'].
    ' AS term_label FROM '.$_OMP_tables[$_OMP_tbl].' AS cli LEFT JOIN '.
    $_OMP_tables['enduses'].' AS eu ON (cli.'.
    $_OMP_tbl_fld['enduse_pkey'].' = eu.'.$_OMP_enduses_fld['pkey'].
    ') LEFT JOIN '.$_OMP_tables['payments'].' AS payment ON (cli.'.
    $_OMP_tbl_fld['paymnt_pkey'].' = payment.'.$_OMP_payments_fld['pkey'].
    ') LEFT JOIN '.$_OMP_tables['terms'].' AS term ON (cli.'.
    $_OMP_tbl_fld['term_pkey'].' = term.'.$_OMP_terms_fld['pkey'].')';
$_OMP_sql['row'] = $_OMP_sql['select'].' WHERE cli.'.
    $_OMP_tbl_fld['pkey'].' = ?';
/**
* End SQL code
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
    global $_OMP_html, $_OMP_table_data, $_OMP_table_header,
        $_OMP_LC, $_OMP_get, $_OMP_rec;
    if ($_OMP_get['action'] == 'new') {
        $_OMP_rec['country'] = $_SESSION['country'];
        $_OMP_rec['active_true'] = 'selected="selected"';
        $_OMP_rec['active_false'] = '';
        $_OMP_rec['zone'] = '2';
        $_OMP_rec['vat'] = '';
        $_OMP_rec['credit'] = '0';
        $_OMP_rec['paymnt_pkey'] = '6';
        $_OMP_rec['paymnt_days'] = '60';
        $_OMP_rec['term_pkey'] = '1';
        $_OMP_rec['enduse_pkey'] = 'FW';
        return;
    }
    $_OMP_rec = array_map('OMP_htmlentities', $_OMP_rec);
    if ($_OMP_get['action'] == 'del') {
        $_OMP_table_header = array($_OMP_LC[100], $_OMP_LC[101]);
        $_OMP_table_data = array($_OMP_rec['pkey'], $_OMP_rec['name']);
        return;
    }
    $_OMP_rec['credit'] = 
        number_format($_OMP_rec['credit'], 0, $_SESSION['dp'], $_SESSION['ts']);
    if ($_OMP_get['action'] == 'read') {
        $_OMP_rec['full_paymnt'] = $_OMP_rec['paymnt_label'].' '.
            $_OMP_rec['paymnt_days'].' '.$_OMP_LC[42].' '.
                $_OMP_rec['term_label'];
        $_OMP_html['full_addr'] =
        $_OMP_rec['full_addr'] =
        $_OMP_html['tel'] =
        $_OMP_html['fax'] = '';
        if (!empty($_OMP_rec['addr'])) {
            $_OMP_rec['full_addr'] = $_OMP_rec['addr'];
        }
        if (!empty($_OMP_rec['zip'])) {
            $_OMP_rec['full_addr'] .= ' '.$_OMP_rec['zip'];
        }
        if (!empty($_OMP_rec['city'])) {
            $_OMP_rec['full_addr'] .= ' '.$_OMP_rec['city'];
        }
        if (!empty($_OMP_rec['state'])) {
            $_OMP_rec['full_addr'] .= ' ('.$_OMP_rec['state'].')';
        }
        if (!empty($_OMP_rec['region'])) {
            $_OMP_rec['full_addr'] .= ' '.$_OMP_rec['region'];
        }
        if ($_OMP_rec['country'] != $_SESSION['country']) {
            $_OMP_rec['full_addr'] .= ' '.$_OMP_rec['country'];
        }
        if (!empty(trim($_OMP_rec['full_addr']))) {
            $_OMP_html['full_addr'] = urlencode($_OMP_rec['full_addr']);
        }
        if ($_OMP_get['list']) {
            $_OMP_html['rec0'] = $_OMP_rec['pkey'];
            $_OMP_html['rec1'] = $_OMP_rec['name'];
            $_OMP_html['rec2'] = $_OMP_rec['full_addr'];
            $_OMP_html['label0'] = '';
            $_OMP_html['label1'] = '';
            $_OMP_html['label2'] = '';
            /* mdc-list */
            /* or mdc-data-table__cell--numeric */
            $_OMP_html['cell1_numeric'] = '';
            $_OMP_html['cell2_numeric'] = '';
            $_OMP_html['cell3_numeric'] = '';
        } else {
            /* OMP_html_tel_link is defined in functions.php */
            if(!empty($_OMP_rec['tel'])) {
                $_OMP_html['tel'] = OMP_html_tel_link($_OMP_rec['tel']);
            }
            if(!empty($_OMP_rec['fax'])) {
                $_OMP_html['fax'] = OMP_html_tel_link($_OMP_rec['fax']);
            }
            $_OMP_rec['note'] = nl2br($_OMP_rec['note'], false);
            $_OMP_rec['del_addr'] = nl2br($_OMP_rec['del_addr'], false);
        }
        /* OMP_yesno @see functions.php */
        $_OMP_rec['active'] = OMP_yesno($_OMP_rec['active']);
        if ($_OMP_rec['constock'] == 't') {
            $_OMP_rec['constock'] = $_OMP_LC[98];
        } else {
            $_OMP_rec['constock'] = $_OMP_LC[99];
        }
    } elseif ($_OMP_get['action'] == 'edit') {
        if ($_OMP_rec['active'] == 't') {
            $_OMP_rec['active_true'] = 'selected="selected"';
            $_OMP_rec['active_false'] = '';
        } else {
            $_OMP_rec['active_false'] = 'selected="selected"';
            $_OMP_rec['active_true'] = '';
        }
        if ($_OMP_rec['constock'] == 't') {
            $_OMP_rec['constock_true'] = 'selected="selected"';
            $_OMP_rec['constock_false'] = '';
        } else {
            $_OMP_rec['constock_false'] = 'selected="selected"';
            $_OMP_rec['constock_true'] = '';
        }
    }
}
/**
* End functions
*/
$_OMP_table_alias = 'cli.'; // See makeSql()
/* see OMP_makeVars() */
$_OMP_html_tel_link = '';
$_OMP_headline = 100;
switch ($_OMP_get['action']) {
    /**
    * Read record
    */
    case 'read': {
        $_OMP_tpl = OMP_TPL_READ.'9, 63, 64, 100';
        $_OMP_lcl = OMP_LCL_READ.'1, 42, 52, 98, 99, 100, 101, 102,
            103, 104, 105, 106, 107, 108, 109, 110, 117, 119,
            122, 123';
        $_OMP_sql['sort_default'] = ' ORDER BY cli.'.$_OMP_tbl_fld['pkey'].' ?';
        $_OMP_sql['sort_record'][0] = $_OMP_sql['sort_list'][0] =
            $_OMP_sql['sort_default'];
        $_OMP_sql['sort_record'][1] = $_OMP_sql['sort_list'][1] = ' ORDER BY cli.'.
            $_OMP_tbl_fld['name'].' ?';
        if ($_OMP_get['list']) {
            $_OMP_list_tpl = 60;
            $_OMP_list_header_tpl = 63;
            $_OMP_list_rec_tpl = 64;
            // $_OMP_sort_idx contains the keys for $_OMP_sort_list as keys
            // and the keys for $_OMP_LC as values
            // $_OMP_sort_list will be created in in read.php
            $_OMP_sort_idx = array(100, 101, 102);
            /* mdc table */
            $_OMP_html['header1_numeric'] = ''; // or mdc-data-table__header-cell--numeric"
            $_OMP_html['header2_numeric'] = ''; // or mdc-data-table__header-cell--numeric"
            $_OMP_html['header3_numeric'] = ''; // or mdc-data-table__header-cell--numeric"

        } else {
            $_OMP_rec_tpl = 100;
            $_OMP_sort_idx = array(100, 101);
        }
        $_OMP_html_tel = $_OMP_html_fax ='';
        $_OMP_title = 1;
        // Default sort
        $_OMP_sort['default'] = '0';
        // Default sort order
        $_OMP_sort['type'] = 0;
        // Load record-read script
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
        $_OMP_tpl = OMP_TPL_EDIT.'103';
        $_OMP_lcl = OMP_LCL_EDIT.'1, 100, 101,
            102, 103, 104, 105, 106, 107, 108,
            109, 110, 111, 112, 113, 114, 115,
            116, 117, 118, 122, 123';
        if (isset($_POST['insert_button'])) {
            $_OMP_sql['update'] = 'UPDATE '.
                $_OMP_tables[$_OMP_tbl].' SET '.
                $_OMP_tbl_fld['pkey'].' = ?, '.
                $_OMP_tbl_fld['name'].' = ?, '.
                $_OMP_tbl_fld['addr'].' = ?, '.
                $_OMP_tbl_fld['zip'].' = ?, '.
                $_OMP_tbl_fld['city'].' = ?, '.
                $_OMP_tbl_fld['state'].' = ?, '.
                $_OMP_tbl_fld['region'].' = ?, '.
                $_OMP_tbl_fld['country'].' = ?, '.
                $_OMP_tbl_fld['vat'].' = ?, '.
                $_OMP_tbl_fld['tel'].' = ?, '.
                $_OMP_tbl_fld['fax'].' = ?, '.
                $_OMP_tbl_fld['note'].' = ?, '.
                $_OMP_tbl_fld['del_addr'].' = ?, '.
                $_OMP_tbl_fld['enduse_pkey'].' = ?, '.
                $_OMP_tbl_fld['zone'].' = ?, '.
                $_OMP_tbl_fld['credit'].' = ?, '.
                $_OMP_tbl_fld['paymnt_pkey'].' = ?, '.
                $_OMP_tbl_fld['paymnt_days'].' = ?, '.
                $_OMP_tbl_fld['term_pkey'].' = ?, '.
                $_OMP_tbl_fld['active'].' = ?, '.
                $_OMP_tbl_fld['constock'].' = ?, '.
                $_OMP_tbl_fld['wilclient'].' = ? WHERE '.
                $_OMP_tbl_fld['pkey'].' = ?';
                /* From postgresql-8.4.2 and php-5.3.1 and
                 * MDB2_Driver_pgsql-1.4.1
                 * datatype boolean does not work
                 * with 'active'
                 */
            $_OMP_datatypes = array('text', 'text',
                'text', 'text', 'text',
                'text', 'text', 'text', 'text',
                'text', 'text', 'text',
                'text', 'text', 'integer', 'integer',
                'integer', 'integer',
                'integer', 'text', 'text', 'text', 'text');
        } else {
            $_OMP_datatypes = array('text');
            $_OMP_combo_required = false;
            $_OMP_prefilter = true;
            $_OMP_drop_down = array(
                'lib/mdc-select-enduses.php',
                'lib/mdc-select-payments.php',
                'lib/mdc-select-terms.php'
            );
            $_OMP_input_tpl = 103;
            $_OMP_edit_tpl = 51;
            $_OMP_page_title_lcl = 1;
            /* switches and scripts to set their value */
            $_OMP_active_switch = true;
            $_OMP_active_switch_locale = 119;
            $_OMP_stock_switch = true;
            $_OMP_stock_switch_locale = 122;
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
        $_OMP_tpl = OMP_TPL_FILTER.'101';
        $_OMP_lcl = OMP_LCL_FILTER.'1, 100, 101,
            102, 103, 104, 105, 106, 107, 108,
            109, 110, 111, 112, 113, 114, 115,
            116, 117, 118, 122, 123';
        $_OMP_prefilter = 't';
        $_OMP_drop_down = array(
            'lib/mdc-select-enduses.php',
            'lib/mdc-select-payments.php',
            'lib/mdc-select-terms.php',
        );
        $_OMP_page_title_lcl = 1;
        $_OMP_include_tpl = 101;
        /* set variables for drop-down lists */
        $_OMP_html['active_combo'] =
            $_OMP_html['constock_combo'] = '';
        /* switches and scripts to set their value */
        $_OMP_active_switch = true;
        $_OMP_active_switch_locale = 119;
        $_OMP_stock_switch = true;
        $_OMP_stock_switch_locale = 122;
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
        $_OMP_tpl = OMP_TPL_NEW.'102';
        $_OMP_lcl = OMP_LCL_NEW.'1, 98, 99, 100, 101, 102, 103, 104, 105,
            106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117,
            118, 119, 122, 123';
        /* Check if insert-button was pushed */
        if (isset($_POST['insert_button'])) {
            $_OMP_sql['insert'] = 'INSERT INTO '.$_OMP_tables[$_OMP_tbl].' ('.
                $_OMP_tbl_fld['pkey'].', '.         // 1 pkey
                $_OMP_tbl_fld['name'].', '.         // 2 name
                $_OMP_tbl_fld['addr'].', '.         // 3 addr
                $_OMP_tbl_fld['zip'].', '.          // 4 zip
                $_OMP_tbl_fld['city'].', '.         // 5 city
                $_OMP_tbl_fld['state'].', '.        // 6 state
                $_OMP_tbl_fld['region'].', '.       // 7 region
                $_OMP_tbl_fld['country'].', '.      // 8 country
                $_OMP_tbl_fld['vat'].', '.          // 9 vat
                $_OMP_tbl_fld['tel'].', '.          // 10 tel
                $_OMP_tbl_fld['fax'].', '.          // 11 fax
                $_OMP_tbl_fld['note'].', '.         // 12 note
                $_OMP_tbl_fld['del_addr'].', '.     // 13 del_addr
                $_OMP_tbl_fld['enduse_pkey'].', '.  // 14 enduse_pkey
                $_OMP_tbl_fld['zone'].', '.         // 15 zone
                $_OMP_tbl_fld['credit'].', '.       // 16 credit
                $_OMP_tbl_fld['paymnt_pkey'].', '.  // 17 paymnt_pkey
                $_OMP_tbl_fld['paymnt_days'].', '.  // 18 paymnt_days
                $_OMP_tbl_fld['term_pkey'].         // 19 term_pkey
                ') VALUES ('.
                    '?, '.  // 1 pkey
                    '?, '.  // 2 name
                    '?, '.  // 3 addr
                    '?, '.  // 4 zip
                    '?, '.  // 5 city
                    '?, '.  // 6 state
                    '?, '.  // 7 region
                    '?, '.  // 8 country
                    '?, '.  // 9 vat
                    '?, '.  // 10 tel
                    '?, '.  // 11 fax
                    '?, '.  // 12 note
                    '?, '.  // 13 del_addr
                    '?, '.  // 14 enduse_pkey
                    '?, '.  // 15 zone
                    '?, '.  // 16 credit
                    '?, '.  // 17 paymnt_pkey
                    '?, '.  // 18 paymnt_days
                    '?)';   // 19 term_pkey
            $_OMP_datatypes = array(
                'text',     // 1 pkey
                'text',     // 2 name
                'text',     // 3 addr
                'text',     // 4 zip
                'text',     // 5 city
                'text',     // 6 state
                'text',     // 7 region
                'text',     // 8 country
                'text',     // 9 vat
                'text',     // 10 tel
                'text',     // 11 fax
                'text',     // 12 note
                'text',     // 13 del_addr
                'text',     // 14 enduse_pkey
                'integer',  // 15 zone
                'integer',  // 16 credit
                'integer',  // 17 paymnt_pkey
                'integer',  // 18 paymnt_days
                'integer'); // 19 term_pkey
        } else {
            $_OMP_combo_required = true;
            $_OMP_prefilter = true;
            $_OMP_drop_down = array(
                'lib/mdc-select-enduses.php',
                'lib/mdc-select-payments.php',
                'lib/mdc-select-terms.php'
            );
            $_OMP_input_tpl = 102;
            $_OMP_include_tpl = 50;
            $_OMP_page_title_lcl = 1;
            /* switches and scripts to set their value */
            $_OMP_html['switch_active'] = '';
            $_OMP_stock_switch = true;
            $_OMP_stock_switch_locale = 122;
            /* see HTML template for client input */
            $_OMP_rec['constock_true'] = $_OMP_rec['constock_false'] = false;
        }
        require 'lib/new.php';
        break;
    }
    /**
    * End new record
    */

    /**
    * Delete record
    */
    case 'del': {
        $_OMP_tpl = OMP_TPL_DEL.'52';
        $_OMP_lcl = OMP_LCL_DEL.'1, 100, 101';
        $_OMP_datatypes = array('text');
        if (isset($_POST['insert_button'])) {
            $_OMP_sql['delete'] = 'DELETE FROM '.$_OMP_tables[$_OMP_tbl].
                ' WHERE '.$_OMP_tbl_fld['pkey'].' = ?';
        } else {
            $_OMP_sql['delete_pre'] = 'SELECT '.$_OMP_tbl_fld['pkey'].
                ' AS pkey, '.$_OMP_tbl_fld['name'].
                ' AS name FROM '.$_OMP_tables[$_OMP_tbl].
                ' WHERE '.$_OMP_tbl_fld['pkey'].' = ?';
            $_OMP_del_tpl = 52;
            $_OMP_page_title_lcl = 1;
        }
        require 'lib/del.php';
        break;
    }
    /**
    * End delete record
    */
}
?>
