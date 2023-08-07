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
// $Id: clientcontacts.php,v 0.8 $
//
// Manage client-contacts records
//

require_once 'base.php';
require_once 'lib/credentials.php';
OMP_checkCredentials(array(2, 3)); // Only operators and admins are allowed
require_once 'schemas/db-schema.php'; // Database schema
// Name of current master-table
$_OMP_tbl = 'clientcontacts';
// Clients schema
require_once 'schemas/clients-schema.php';
// SQL for drop-down lists
$_OMP_get['popup'] or require_once 'lib/dd-sql.php';
// Client-contacts schema
require_once 'schemas/clientcontacts-schema.php';
// Table fields, length and keys
$_OMP_tbl_fld = $_OMP_clientcontacts_fld;
$_OMP_fld_len = $_OMP_clientcontacts_len;
$_OMP_tbl_key = $_OMP_clientcontacts_key;

/**
* SQL code
*/
$_OMP_sql['select'] = 'SELECT clicon.'.$_OMP_tbl_fld['pkey'].
    ' AS pkey, clicon.'.$_OMP_tbl_fld['client_pkey'].
    ' AS client_pkey, cli.'.$_OMP_clients_fld['name'].
    ' AS client_label, clicon.'.$_OMP_tbl_fld['first_name'].
    ' AS first_name, clicon.'.$_OMP_tbl_fld['last_name'].
    ' AS last_name, '.$_OMP_tbl_fld['title'].
    ' AS title, clicon.'.$_OMP_tbl_fld['addr'].
    ' AS addr, clicon.'.$_OMP_tbl_fld['zip'].
    ' AS zip, clicon.'.$_OMP_tbl_fld['city'].
    ' AS city, clicon.'.$_OMP_tbl_fld['state'].
    ' AS state, clicon.'.$_OMP_tbl_fld['region'].
    ' AS region, clicon.'.$_OMP_tbl_fld['country'].
    ' AS country, clicon.'.$_OMP_tbl_fld['tel_home'].
    ' AS tel_home, clicon.'.$_OMP_tbl_fld['tel_office'].
    ' AS tel_office, clicon.'.$_OMP_tbl_fld['mobile'].
    ' AS mobile, clicon.'.$_OMP_tbl_fld['tel_other'].
    ' AS tel_other, clicon.'.$_OMP_tbl_fld['fax'].
    ' AS fax, clicon.'.$_OMP_tbl_fld['email'].
    ' AS email FROM '.$_OMP_tables[$_OMP_tbl].' AS clicon LEFT JOIN '.
    $_OMP_tables['clients'].' AS cli ON (clicon.'.
    $_OMP_tbl_fld['client_pkey'].' = cli.'.$_OMP_clients_fld['pkey'].')';
$_OMP_sql['row'] = $_OMP_sql['select'].' WHERE clicon.'.
    $_OMP_tbl_fld['pkey'].' = ? AND clicon.'.
    $_OMP_tbl_fld['client_pkey'].' = ?';
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
        return;
    }
    $_OMP_rec = array_map('OMP_htmlentities', $_OMP_rec);
    if ($_OMP_get['action'] == 'del') {
        $_OMP_table_header = array($_OMP_LC[1000].'&nbsp;'.
            $_OMP_LC[1001], $_OMP_LC[100]);
        $_OMP_table_data = array($_OMP_rec['first_name'].
            ' '.$_OMP_rec['last_name'], $_OMP_rec['client_pkey']);
    } elseif ($_OMP_get['action'] == 'read') {
        if (!$_OMP_get['popup']) {
            $tmp = OMP_PATH.'clients.php?'.OMP_link(
                'filter=1&pkey='.urlencode(
                    html_entity_decode(
                        $_OMP_rec['client_pkey']
                    ).'&popup=1';
                )
            );
            $_OMP_rec['client_pkey'] = 
                OMP_popLink(
                    $GLOBALS['_OMP_TPL'][9],
                    $tmp,
                    $_OMP_rec['client_pkey']
                );
        }
        $_OMP_html['full_addr'] = $_OMP_rec['full_addr'] = '';
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
            $_OMP_html['rec0'] = $_OMP_rec['last_name'].' '.
                $_OMP_rec['first_name'];
            $_OMP_html['rec1'] = $_OMP_rec['client_pkey'];
            $_OMP_html['rec2'] = $_OMP_rec['full_addr'];
            $_OMP_html['label0'] = '';
            $_OMP_html['label1'] = $_OMP_rec['client_label'];
            $_OMP_html['label2'] = '';
            /* mdc-list */
            /* or mdc-data-table__cell--numeric */
            $_OMP_html['cell1_numeric'] = '';
            $_OMP_html['cell2_numeric'] = '';
            $_OMP_html['cell3_numeric'] = '';
        } else {
            /* OMP_html_tel_link is defined in functions.php */
            if (!empty($_OMP_rec['tel_home'])) {
                $_OMP_html['tel_home'] =
                    OMP_html_tel_link($_OMP_rec['tel_home']);
            }
            if (!empty($_OMP_rec['tel_office'])) {
                $_OMP_html['tel_office'] =
                    OMP_html_tel_link($_OMP_rec['tel_office']);
            }
            if (!empty($_OMP_rec['mobile'])) {
                $_OMP_html['mobile'] =
                    OMP_html_tel_link($_OMP_rec['mobile']);
            }
            if (!empty($_OMP_rec['tel_other'])) {
                $_OMP_html['tel_other'] =
                    OMP_html_tel_link($_OMP_rec['tel_other']);
            }
            if (!empty($_OMP_rec['fax'])) {
                $_OMP_html['fax'] =
                    OMP_html_tel_link($_OMP_rec['fax']);
            }
            if (!empty($_OMP_rec['email'])) {
                $_OMP_html['email'] = $_OMP_rec['fax'];
            }
        }
    }
}
/**
* End functions
*/
// Taken from suppliercontacts.php
!isset($_OMP_get['first_name']) or 
    $_OMP_get['first_name'] = stripslashes($_OMP_get['first_name']);
!isset($_OMP_get['last_name']) or 
    $_OMP_get['last_name'] = stripslashes($_OMP_get['last_name']);
// See makeSql
$_OMP_table_alias = 'clicon.';
switch ($_OMP_get['action']) {
    /**
    * Read record
    */
    case 'read': {
        $_OMP_tpl = OMP_TPL_READ.'9, 63, 64, 1000';
        $_OMP_lcl = OMP_LCL_READ.'1, 42, 53, 100,
            102, 104, 1000, 1001, 1002, 1003,
            1004, 1005, 1006, 1007';
        $_OMP_sql['sort_default'] = ' ORDER BY clicon.'.
            $_OMP_tbl_fld['last_name'].' ?, clicon.'.
            $_OMP_tbl_fld['first_name'];
        $_OMP_sql['sort_record'][0] = $_OMP_sql['sort_list'][0] =
            $_OMP_sql['sort_default'];
        $_OMP_sql['sort_record'][1] = $_OMP_sql['sort_list'][1] =
            ' ORDER BY clicon.'.$_OMP_tbl_fld['client_pkey'].' ?, clicon.'.
            $_OMP_tbl_fld['last_name'].', clicon.'.$_OMP_tbl_fld['first_name'];
        $_OMP_sql['sort_record'][2] = $_OMP_sql['sort_list'][2] =
            ' ORDER BY clicon.'.$_OMP_tbl_fld['first_name'].
            ' ?, clicon.'.$_OMP_tbl_fld['last_name'];
        if ($_OMP_get['list']) {
            $_OMP_list_tpl = 60;
            $_OMP_list_header_tpl = 63;
            $_OMP_list_rec_tpl = 64;
            $_OMP_sort_idx = array(1000, 100, 102);
            $_OMP_list_wrapper = 'wrapper3';
            /* mdc table */
            // or mdc-data-table__header-cell--numeric"
            $_OMP_html['header1_numeric'] = '';
            $_OMP_html['header2_numeric'] = '';
            $_OMP_html['header3_numeric'] = '';
        } else {
            $_OMP_rec_tpl = 1000;
            $_OMP_sort_idx = array(1001, 100, 1000);
        }
        $_OMP_html['tel_home'] =
            $_OMP_html['tel_office'] =
            $_OMP_html['mobile'] =
            $_OMP_html['tel_other'] =
            $_OMP_html['fax'] =
            $_OMP_html['full_addr'] =
            $_OMP_html['email'] = '';
        $_OMP_title = 53;
        $_OMP_sort['default'] = '0'; // Default sort
        $_OMP_sort['type'] = 0; // Default sort order
        require 'lib/read.php'; // Load record-read script
        break;
    }
    /**
    * End read record
    */

    /**
    * Edit record
    */
    case 'edit': {
        $_OMP_tpl = OMP_TPL_EDIT.'1002';
        $_OMP_lcl = OMP_LCL_EDIT.'100, 102, 104, 111, 112, 113, 114,
            115, 1000, 1001, 1002, 1003, 1004, 1005, 1006, 1007';
        if (isset($_POST['insert_button'])) {
            $_OMP_sql['update'] = 'UPDATE '.$_OMP_tables[$_OMP_tbl].' SET '.
                $_OMP_tbl_fld['client_pkey'].' = ?, '.
                $_OMP_tbl_fld['first_name'].' = ?, '.
                $_OMP_tbl_fld['last_name'].' = ?, '.
                $_OMP_tbl_fld['title'].' = ?, '.
                $_OMP_tbl_fld['addr'].' = ?, '.
                $_OMP_tbl_fld['zip'].' = ?, '.
                $_OMP_tbl_fld['city'].' = ?, '.
                $_OMP_tbl_fld['state'].' = ?, '.
                $_OMP_tbl_fld['region'].' = ?, '.
                $_OMP_tbl_fld['country'].' = ?, '.
                $_OMP_tbl_fld['tel_home'].' = ?, '.
                $_OMP_tbl_fld['tel_office'].' = ?, '.
                $_OMP_tbl_fld['mobile'].' = ?, '.
                $_OMP_tbl_fld['tel_other'].' = ?, '.
                $_OMP_tbl_fld['fax'].' = ?, '.
                $_OMP_tbl_fld['email'].' = ? WHERE '.
                $_OMP_tbl_fld['pkey'].' = ? AND '.
                $_OMP_tbl_fld['client_pkey'].' = ?';
            $_OMP_datatypes = array('text', 'text', 'text', 'text', 'text',
                'text', 'text', 'text', 'text', 'text', 'text', 'text',
                'text', 'text', 'text', 'text', 'text', 'text');
        } else {
            $_OMP_datatypes = array('integer', 'text');
            // SQL queries for drop-down lists
            $_OMP_get['popup'] or
                $_OMP_drop_down[] = 'lib/mdc-select-clients.php';
            $_OMP_input_tpl = 1002;
            $_OMP_edit_tpl = 51;
            $_OMP_page_title_lcl = 25;
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
        $_OMP_tpl = OMP_TPL_FILTER.'1001';
        $_OMP_lcl = OMP_LCL_FILTER.'53, 100, 102, 104, 111, 112,
            113, 114, 115, 1000, 1001, 1002, 1003, 1004, 1005, 1006, 1007';
        if ($_SESSION['cat'] == 0) { // Filter for clients
            $_OMP_html['client_combo'] = '<input disabled type="text" '.
            'name="client_pkey" id="client_pkey" '.
            'size="10" value="'.htmlentities($_SESSION['id']).'" /> '.
            '<input type="hidden" name="form[enc_client_pkey]" '.
            'id="form[enc_client_pkey]" value="'.
            htmlentities($_SESSION['id']).'" />';
        } else {
            // SQL queries for drop-down lists
            $_OMP_combo_required = true;
            $_OMP_drop_down[] = 'lib/mdc-select-clients.php';
        }
        $_OMP_page_title_lcl = 53;
        $_OMP_include_tpl = 1001;
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
        $_OMP_tpl = OMP_TPL_NEW.'1002';
        $_OMP_lcl = OMP_LCL_NEW.'53, 100, 102, 104, 111, 112, 113,
            114, 115, 1000, 1001, 1002, 1003, 1004, 1005, 1006, 1007';
        if (isset($_POST['insert_button'])) {
            $_OMP_sql['insert'] = 'INSERT INTO '.$_OMP_tables[$_OMP_tbl].' ('.
                $_OMP_tbl_fld['client_pkey'].', '.
                $_OMP_tbl_fld['first_name'].', '.
                $_OMP_tbl_fld['last_name'].', '.
                $_OMP_tbl_fld['addr'].', '.
                $_OMP_tbl_fld['zip'].', '.
                $_OMP_tbl_fld['city'].', '.
                $_OMP_tbl_fld['state'].', '.
                $_OMP_tbl_fld['region'].', '.
                $_OMP_tbl_fld['country'].', '.
                $_OMP_tbl_fld['tel_home'].', '.
                $_OMP_tbl_fld['tel_office'].', '.
                $_OMP_tbl_fld['mobile'].', '.
                $_OMP_tbl_fld['tel_other'].', '.
                $_OMP_tbl_fld['fax'].', '.
                $_OMP_tbl_fld['email'].', '.
                $_OMP_tbl_fld['user'].
                ') VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $_OMP_datatypes = array('text', 'text', 'text', 'text', 'text',
                'text', 'text', 'text', 'text', 'text', 'text', 'text',
                'text', 'text', 'text', 'text');
            $_OMP_sql['get_last'] =
                'SELECT pkey, client_pkey FROM clicon_log WHERE "user" = ';
        } else {
            // SQL queries for drop-down lists
            $_OMP_get['popup'] or require_once 'lib/dd-sql.php';
            $_OMP_combo_required = true;
            $_OMP_drop_down[] = 'lib/mdc-select-clients.php';
            $_OMP_input_tpl = 1002;
            $_OMP_include_tpl = 50;
            $_OMP_page_title_lcl = 53;
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
        $_OMP_lcl = OMP_LCL_DEL.'53, 100, 1000, 1001';
        $_OMP_datatypes = array('integer', 'text');
        if (isset($_POST['insert_button'])) {
            $_OMP_sql['delete'] = 'DELETE FROM '.$_OMP_tables[$_OMP_tbl].
                ' WHERE '.$_OMP_tbl_fld['pkey'].' = ? AND '.
                $_OMP_tbl_fld['client_pkey'].' = ?';
        } else {
            $_OMP_sql['delete_pre'] = 'SELECT '.$_OMP_tbl_fld['client_pkey'].
                ' AS client_pkey, '.$_OMP_tbl_fld['first_name'].
                ' AS first_name, '.$_OMP_tbl_fld['last_name'].
                ' AS last_name FROM '.$_OMP_tables[$_OMP_tbl].
                ' WHERE '.$_OMP_tbl_fld['pkey'].' = ? AND '.
                $_OMP_tbl_fld['client_pkey'].' = ?';
            $_OMP_del_tpl = 52;
            $_OMP_page_title_lcl = 53;
        }
        require 'lib/del.php';
        break;
    }
    /**
    * End delete record
    */
}
?>
