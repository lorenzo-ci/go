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
// $Id: banks.php,v 0.9 $
//
// Manage bank records
//

require_once 'base.php';
/* Database schema */
require_once 'schemas/db-schema.php';
/* Name of current master-table */
$_OMP_tbl = 'banks';
/* Clients schema */
require_once 'schemas/clients-schema.php';
/* SQL for drop-down lists */
$_OMP_get['popup'] or require_once 'lib/dd-sql.php';
/* Banks schema */
require_once 'schemas/banks-schema.php';
/* Table fields, length and keys */
$_OMP_tbl_fld = $_OMP_banks_fld;
$_OMP_fld_len = $_OMP_banks_len;
$_OMP_tbl_key = $_OMP_banks_key;

/**
* SQL code
*/
$_OMP_sql['select'] = 'SELECT banks.'.$_OMP_tbl_fld['pkey'].
    ' AS pkey, banks.'.$_OMP_tbl_fld['client_pkey'].
    ' AS client_pkey, cli.'.$_OMP_clients_fld['name'].
    ' AS client_label, banks.'.$_OMP_tbl_fld['name'].
    ' AS name, banks.'.$_OMP_tbl_fld['addr'].
    ' AS addr, banks.'.$_OMP_tbl_fld['zip'].
    ' AS zip, banks.'.$_OMP_tbl_fld['city'].
    ' AS city, banks.'.$_OMP_tbl_fld['state'].
    ' AS state, banks.'.$_OMP_tbl_fld['region'].
    ' AS region, banks.'.$_OMP_tbl_fld['country'].
    ' AS country, banks.'.$_OMP_tbl_fld['tel'].
    ' AS tel, banks.'.$_OMP_tbl_fld['fax'].
    ' AS fax, banks.'.$_OMP_tbl_fld['note'].
    ' AS note FROM '.$_OMP_tables[$_OMP_tbl].
    ' AS banks LEFT JOIN '.$_OMP_tables['clients'].
    ' AS cli ON (banks.'.$_OMP_tbl_fld['client_pkey'].' = cli.'.
    $_OMP_clients_fld['pkey'].')';
$_OMP_sql['row'] = $_OMP_sql['select'].' WHERE banks.'.
    $_OMP_tbl_fld['pkey'].' = ? AND banks.'.
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
    global $_OMP_html, $_OMP_table_header, $_OMP_table_data,
        $_OMP_LC, $_OMP_get, $_OMP_rec, $_OMP_TPL;
    $_OMP_rec['um'] = $_OMP_rec['vat'] = '';
    if ($_OMP_get['action'] == 'new') {
        $_OMP_rec['country'] = $_SESSION['country'];
        return;
    }
    $_OMP_rec = array_map('OMP_htmlentities', $_OMP_rec);
    if ($_OMP_get['action'] == 'del') {
        $_OMP_table_header = array($_OMP_LC[900], $_OMP_LC[101]);
        $_OMP_table_data = array($_OMP_rec['pkey'], $_OMP_rec['name']);
        return;
    } elseif ($_OMP_get['action'] == 'read') {
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
        /* OMP_html_tel_link is defined in functions.php */
        if(!empty($_OMP_rec['tel'])) {
            $_OMP_html['tel'] = OMP_html_tel_link($_OMP_rec['tel']);
        }
        if(!empty($_OMP_rec['fax'])) {
            $_OMP_html['fax'] = OMP_html_tel_link($_OMP_rec['fax']);
        }
        $_OMP_get['popup'] or $_OMP_rec['client_pkey'] =
            OMP_popLink(
                $_OMP_TPL[9],
                OMP_PATH.'clients.php?'.
                    OMP_link(
                        'filter=1&pkey='.
                        urlencode(
                            html_entity_decode(
                                $_OMP_rec['client_pkey']
                            )
                        ).
                        '&popup=1'
                    ),
                $_OMP_rec['client_pkey']
            );
        if ($_OMP_get['list']) {
            $_OMP_html['rec0'] = $_OMP_rec['pkey'];
            $_OMP_html['rec1'] = $_OMP_rec['name'];
            $_OMP_html['rec2'] = $_OMP_rec['client_pkey'];
            $_OMP_html['label0'] = '';
            $_OMP_html['label1'] = '';
            $_OMP_html['label2'] = $_OMP_rec['client_label'];
            /* mdc-list */
            /* or mdc-data-table__cell--numeric */
            $_OMP_html['cell1_numeric'] = '';
            $_OMP_html['cell2_numeric'] = '';
            $_OMP_html['cell3_numeric'] = '';
        } else {
            $_OMP_rec['note'] = nl2br($_OMP_rec['note'], false);
        }
    }
}
/**
* End functions
*/
$_OMP_table_alias = 'banks.'; // See makeSql
switch ($_OMP_get['action']) {
/**
* Read record
*/
case 'read': {
    $_OMP_tpl = OMP_TPL_READ.'9, 63, 64, 900';
    $_OMP_lcl = OMP_LCL_READ.'100, 101, 102, 103, 104, 106, 108, 900, 999';
    $_OMP_sql['sort_default'] = ' ORDER BY banks.'.$_OMP_tbl_fld['pkey'].
        ' ?, banks.'.$_OMP_tbl_fld['client_pkey'];
    $_OMP_sql['sort_record'][0] = 
        $_OMP_sql['sort_list'][0] = 
        $_OMP_sql['sort_default'];
    $_OMP_sql['sort_record'][1] = $_OMP_sql['sort_list'][1] = 
        ' ORDER BY banks.'.$_OMP_tbl_fld['name'].' ?';
    $_OMP_sql['sort_record'][2] = $_OMP_sql['sort_list'][2] = 
        ' ORDER BY banks.'.$_OMP_tbl_fld['client_pkey'].' ?, banks.'.
        $_OMP_tbl_fld['pkey'];
    $_OMP_sort_idx = array(900, 101, 100);
    if ($_OMP_get['list']) {
        $_OMP_list_tpl = 60;
        $_OMP_list_header_tpl = 63;
        $_OMP_list_rec_tpl = 64;
        /* mdc table */
        // or mdc-data-table__header-cell--numeric"
        $_OMP_html['header1_numeric'] = '';
        $_OMP_html['header2_numeric'] = '';
        $_OMP_html['header3_numeric'] = '';
    } else {
        $_OMP_rec_tpl = 900;
        $_OMP_headline = 900;
    }
    $_OMP_html_tel = $_OMP_html_fax = '';
    $_OMP_title = 999;
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
    $_OMP_tpl = OMP_TPL_EDIT.'902';
    $_OMP_lcl = OMP_LCL_EDIT.'100, 101, 102, 103, 104, 106, 108, 111, '.
        '112, 113, 114, 115, 900, 999';
    // Check if insert-button was pushed
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['update'] = 'UPDATE '.$_OMP_tables[$_OMP_tbl].' SET '.
            $_OMP_tbl_fld['pkey'].' = ?, '.
            $_OMP_tbl_fld['client_pkey'].' = ?, '.
            $_OMP_tbl_fld['name'].' = ?, '.
            $_OMP_tbl_fld['addr'].' = ?, '.
            $_OMP_tbl_fld['zip'].' = ?, '.
            $_OMP_tbl_fld['city'].' = ?, '.
            $_OMP_tbl_fld['state'].' = ?, '.
            $_OMP_tbl_fld['region'].' = ?, '.
            $_OMP_tbl_fld['country'].' = ?, '.
            $_OMP_tbl_fld['tel'].' = ?, '.
            $_OMP_tbl_fld['fax'].' = ?, '.
            $_OMP_tbl_fld['note'].' = ? WHERE '.
            $_OMP_tbl_fld['pkey'].' = ? AND '.
            $_OMP_tbl_fld['client_pkey'].' = ?';
        $_OMP_datatypes = array('text', 'text', 'text', 'text', 'text', 
            'text', 'text', 'text', 'text', 'text', 'text', 'text', 
            'text', 'text');
    } else {
        $_OMP_datatypes = array('text');
        $_OMP_combo_required = true;
        $_OMP_drop_down = array('lib/mdc-select-clients.php');
        $_OMP_input_tpl = 902;
        $_OMP_edit_tpl = 51;
        $_OMP_page_title_lcl = 999;
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
    $_OMP_tpl = OMP_TPL_FILTER.'901';
    $_OMP_lcl = OMP_LCL_FILTER.'100, 101, 102, 103, 104, 106, 108, 111, '.
        '112, 113, 114, 115, 900, 999';
    if ($_SESSION['cat'] == 0) { // Filter for clients
        $_OMP_client_combo = 
        '<input disabled type="text" name="client_pkey" id="client_pkey" '.
        'size="10" value="'.htmlentities($_SESSION['id']).'" /> '.
        '<input type="hidden" name="form[enc_client_pkey]" '.
        'id="form[enc_client_pkey]" value="'.
        htmlentities($_SESSION['id']).'" />';
    } else {
        $_OMP_drop_down = array('lib/mdc-select-clients.php');
    }
    $_OMP_page_title_lcl = 999;
    $_OMP_include_tpl = 901;
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
    $_OMP_tpl = OMP_TPL_NEW.'902';
    $_OMP_lcl = OMP_LCL_NEW.'100, 101, 102, 103, 104, 105, 106, '.
        '108, 111, 112, 113, 114, 115, 900, 999';
    // Check if insert-button was pushed
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['insert'] = 'INSERT INTO '.$_OMP_tables[$_OMP_tbl].' ('.
        $_OMP_tbl_fld['pkey'].', '.$_OMP_tbl_fld['client_pkey'].', '.
        $_OMP_tbl_fld['name'].', '.$_OMP_tbl_fld['addr'].', '.
        $_OMP_tbl_fld['zip'].', '.$_OMP_tbl_fld['city'].', '.
        $_OMP_tbl_fld['state'].', '.$_OMP_tbl_fld['region'].', '.
        $_OMP_tbl_fld['country'].', '.$_OMP_tbl_fld['tel'].', '.
        $_OMP_tbl_fld['fax'].', '.$_OMP_tbl_fld['note'].
        ') VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $_OMP_datatypes = array('text', 'text', 'text', 'text', 'text', 
            'text', 'text', 'text', 'text', 'text', 'text', 'text');
    } else {
        $_OMP_combo_required = true;
        $_OMP_drop_down = array('lib/mdc-select-clients.php');
        $_OMP_input_tpl = 902;
        $_OMP_include_tpl = 50;
        $_OMP_page_title_lcl = 999;
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
    $_OMP_lcl = OMP_LCL_DEL.'101, 900, 999';
    $_OMP_datatypes = array('text', 'text');
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['delete'] = 'DELETE FROM '.$_OMP_tables[$_OMP_tbl].
            ' WHERE '.$_OMP_tbl_fld['pkey'].
            ' = ? AND '.$_OMP_tbl_fld['client_pkey'].' = ?';
    } else {
        $_OMP_sql['delete_pre'] = 'SELECT '.$_OMP_tbl_fld['pkey'].
            ' AS pkey, '.$_OMP_tbl_fld['name'].' AS name FROM '.
            $_OMP_tables[$_OMP_tbl].' WHERE '.$_OMP_tbl_fld['pkey'].
            ' = ? AND '.$_OMP_tbl_fld['client_pkey'].' = ?';
        $_OMP_del_tpl = 52;
        $_OMP_page_title_lcl = 999;
    }
    require 'lib/del.php';
    break;
}
/**
* End delete record
*/
}
?>
