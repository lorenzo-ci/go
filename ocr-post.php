<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | OMP Version 1                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 2021-2022 Lorenzo Ciani                                |
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
// $Id: ocr-post.php,v 0.1 $
//
// Scan delivery files
//
require_once 'base.php';
$_OMP_tpl = OMP_TPL_NEW;
$_OMP_lcl = OMP_LCL_NEW.'38, 40, 4003';
OMP_load(); // @see base.php
/* Show a link to the ocr-load page if visiting this page directly */
isset($_POST['form']) or 
    OMP_genErr('<a href="ocr-load.php">'.$_OMP_lcl[4003].'</a>');
require_once 'schemas/db-schema.php'; // Database schema
require_once 'schemas/deliveries-schema.php'; // Deliveries schema
require_once 'schemas/orders-schema.php'; // Orders schema
/* message on redirect page */
$OMP_redirPage_msg = '';
if (isset($_POST['form']['PMT'])) {
    require_once 'schemas/invoices-schema.php'; // Invoices schema
    // Name of current master-table
    $_OMP_tbl = 'invoices';
    // Table fields, length and keys
    $_OMP_tbl_fld = $_OMP_invoices_fld;
    $_OMP_fld_len = $_OMP_invoices_len;
    $_OMP_tbl_key = $_OMP_invoices_key;
    $_OMP_sql['update'] = 'UPDATE '.$_OMP_tables[$_OMP_tbl].' SET '.
        $_OMP_tbl_fld['paymnt_date'].' = ? WHERE '.
        $_OMP_tbl_fld['pkey'].' = ?';
        /* the complete command should contain all elements of the 
        primary key but we don't have all that info 
        ' AND '.$_OMP_tbl_fld['date'].' = ? AND '.
        $_OMP_tbl_fld['supplier_pkey'].' = ?';*/
    $_OMP_datatypes = array('date', 'text');
    foreach ($_POST['form']['PMT'] as $PMT) {
        /* @see functions.ph for OMP_makeData() */
        $_OMP_data = OMP_makeData($_OMP_sql['update'], false, $PMT);
        $_OMP_db_prepared = $_OMP_db->prepare($_OMP_sql['update'], 
            $_OMP_datatypes);
        $_OMP_db_result = $_OMP_db_prepared->execute($_OMP_data);
        $_OMP_db_prepared->free();
    }
    $OMP_redirPage_msg = $_OMP_LC[2].' '.$_OMP_LC[40];
}
if (isset($_POST['form']['SO'])) {
    foreach ($_POST['form']['SO'] as $SO) {
        $items = (isset($SO['items'])) ? $SO['items'] : array();
        /* the next step reoders the array key and it is necessary otherwise 
         isset($items[0]) may point to an index that doesn't exist */
        $items = array_values($items);
        unset($SO['items']);
        // Name of current master-table
        $_OMP_tbl = 'orders';
        // Table fields, length and keys
        $_OMP_tbl_fld = $_OMP_orders_fld;
        $_OMP_fld_len = $_OMP_orders_len;
        $_OMP_tbl_key = $_OMP_orders_key;
        $_OMP_sql['update'] = 'UPDATE '.$_OMP_tables[$_OMP_tbl].' SET '.
            $_OMP_tbl_fld['ref'].' = ? WHERE '.
            $_OMP_tbl_fld['supplier_ref'].' = ?';
        $_OMP_datatypes = array('text', 'text');
        /* @see functions.ph for OMP_makeData() */
        $_OMP_data = OMP_makeData($_OMP_sql['update'], false, $SO);
        $_OMP_db_prepared = $_OMP_db->prepare($_OMP_sql['update'], 
            $_OMP_datatypes);
        $_OMP_db_result = $_OMP_db_prepared->execute($_OMP_data);
        $_OMP_db_prepared->free();
        foreach($items as $item) {
            $item_pkey = (isset($item['pkey'])) ? $item['pkey'] : '';
            unset($item['int_pkey']);
            // Name of current master-table
            $_OMP_tbl = 'orders_lines';
            // Table fields, length and keys
            $_OMP_tbl_fld = $_OMP_orders_lines_fld;
            $_OMP_fld_len = $_OMP_orders_lines_len;
            $_OMP_tbl_key = $_OMP_orders_lines_key;
            $_OMP_sql['update_line'] = 
                'UPDATE '.$_OMP_tables[$_OMP_tbl].' SET ';
            foreach ($item as $key=>$value) {
                /* note can be NULL so we don't add it to sql update */
                if (!empty($value)) {
                    if ($key == 'plan') { $value .= "-01" ; }
                    /* filter string @see functions.php */
                    $value = OMP_checkIllegalChars($value, false, true);
                    $_OMP_sql['update_line'] .= $_OMP_tbl_fld[$key].' = ';
                    $_OMP_sql['update_line'] .= OMP_db_quote($value);
                    $_OMP_sql['update_line'] .= ', ';
                }
            }
            $_OMP_sql['update_line'] .= 'WHERE '.$_OMP_tbl_fld['pkey'].' = ';
            $_OMP_sql['update_line'] .= $item_pkey;
            /* remove the last comma */
            $_OMP_sql['update_line'] = 
                str_replace(', WHERE', ' WHERE', $_OMP_sql['update_line']);
                $_OMP_db->exec($_OMP_sql['update_line']);
        }
    }
    $OMP_redirPage_msg = $_OMP_LC[2].' '.$_OMP_LC[40];
}
if (isset($_POST['form']['shipments'])) {
    $items = array();
    foreach ($_POST['form']['shipments'] as $shipment) {
        // Name of current master-table
        $_OMP_tbl = 'deliveries';
        // Table fields, length and keys
        $_OMP_tbl_fld = $_OMP_deliveries_fld;
        $_OMP_fld_len = $_OMP_deliveries_len;
        $_OMP_tbl_key = $_OMP_deliveries_key;
        # lines below @see new.php
        /* move the invoice pkey from $shipment to $inv_pkey */
        $inv_pkey = $shipment['inv_pkey'];
        unset($shipment['inv_pkey']);
        /* get date information from shipment date */
        $shipment_date = getdate(strtotime($shipment['date']));
        /* populate $items and unset $shipment['items'] */
        /* must do this before OMP_makeData or it will make error */
        if (isset($shipment['items'])) {
            $items = $shipment['items'];
            /* the next step reoders the array key and it is necessary otherwise 
             isset($items[0]) may point to an index that doesn't exist */
            $items = array_values($items);
            unset($shipment['items']);
        } else {
            $items = array();
        }
        if ($_POST['update'] == 1) {
            /* update delivery record */
            /* OMP_makeData @see functions.php */
            /* $_OMP_del_sql_update @see deliveries-schema.php */
            $_OMP_data = 
                OMP_makeData($_OMP_del_sql_update, false, $shipment);
            $_OMP_data[] = $shipment['txt_pkey'];
            /* strtoupper for when user inputs client name not found
             * in ocr-form.php */
            $_OMP_data[] = strtoupper($shipment['enc_client_pkey']);
            $_OMP_post['pkey'] = $shipment['txt_pkey'];
            $_OMP_post['client_pkey'] = $shipment['enc_client_pkey'];
            /* OMP_keyCheck @see functions.php */
            /* $_OMP_datatypes_del_sql_update @see deliveries-schema.php */
            $_OMP_data = OMP_keyCheck($_OMP_post, false, $_OMP_data);
            $_OMP_db_prepared = $_OMP_db->prepare($_OMP_del_sql_update,
                $_OMP_datatypes_del_sql_update);
            $_OMP_db_result = $_OMP_db_prepared->execute($_OMP_data);
            $_OMP_db_prepared->free();
        } else {
            /* insert new delivery record */
            $shipment['enc_client_pkey'] = strtoupper($shipment['enc_client_pkey']);
            /* @see deliveries-schema.php for $_OMP_del_sql_insert and
                $_OMP_datatypes_del_sql_insert */
            $_OMP_data = OMP_makeData($_OMP_del_sql_insert, false, $shipment);
            $_OMP_db_prepared = $_OMP_db->prepare($_OMP_del_sql_insert,
                $_OMP_datatypes_del_sql_insert);
            $_OMP_db_result = $_OMP_db_prepared->execute($_OMP_data);
            $_OMP_db_prepared->free();
        }
        // Name of current master-table
        $_OMP_tbl = 'deliveries_lines';
        // Table fields, length and keys
        $_OMP_tbl_fld = $_OMP_deliveries_lines_fld;
        $_OMP_fld_len = $_OMP_deliveries_lines_len;
        $_OMP_tbl_key = $_OMP_deliveries_lines_key;
        foreach($items as $item) {
            if ($_POST['update'] == 1) {
                /* update delivery line record */
                /* these are not in $_OMP_del_sql_update_line */
                unset($item['supplier_ref']);
                unset($item['prod_pkey']);
                /* these are in $_OMP_del_sql_update_line */
                $item['enc_client_pkey'] = $shipment['enc_client_pkey'];
                $item['del_pkey'] = $shipment['txt_pkey'];
                /* change key */
                $item['txt_del_pkey'] = $item['del_pkey'];
                unset($item['del_pkey']);
                /* OMP_makeData @see functions.php */
                /* $_OMP_del_sql_update_line @see deliveries-schema.php */
                /* $_OMP_datatypes_del_sql_update_line @see deliveries-schema.php */
                $_OMP_data = OMP_makeData($_OMP_del_sql_update_line, false, $item);
                $_OMP_post['pkey'] = $item['int_pkey'];
                /* OMP_keyCheck @see functions.php */
                $_OMP_data = OMP_keyCheck($_OMP_post, false, $_OMP_data);
                $_OMP_db_prepared = $_OMP_db->prepare($_OMP_del_sql_update_line,
                    $_OMP_datatypes_del_sql_update_line);
                $_OMP_db_prepared->execute($_OMP_data);
                $_OMP_db_prepared->free();
            } else {
                /* insert new delivery line record */
                /* move the product pkey from $item to $prod_pkey */
                $prod_pkey = $item['prod_pkey'];
                unset($item['prod_pkey']);
                /* move the supplier ref from $item to $supplier_ref */
                $supplier_ref = $item['supplier_ref'];
                unset($item['supplier_ref']);
                /* 08/03/2022 changed from txt_client_pkey to enc_client_pkey */
                $item['enc_client_pkey'] = $shipment['enc_client_pkey'];
                $item['txt_del_pkey'] = $shipment['txt_pkey'];
                /* lookup ol_pkey using supplier_ref and prod_pkey */
                $sql_ol_pkey = 'SELECT '.$_OMP_orders_lines_fld['pkey'].
                    ' AS ol_pkey FROM '.$_OMP_tables['orders_lines'].
                    ' INNER JOIN '.$_OMP_tables['orders'].' ON '.
                    $_OMP_tables['orders_lines'].'.'.
                    $_OMP_orders_lines_fld['oi_pkey'].' = '.
                    $_OMP_tables['orders'].'.'.$_OMP_orders_fld['pkey']
                    .' WHERE '.$_OMP_orders_fld['supplier_ref'].' = '.
                    $_OMP_db->quote($supplier_ref).' AND '.
                    $_OMP_orders_lines_fld['year'].' = '.$shipment_date['year'].
                    ' AND '.$_OMP_orders_lines_fld['month'].' = '.
                    $shipment_date['mon'].' AND '.
                    $_OMP_orders_lines_fld['prod_pkey'].' = '.
                    $_OMP_db->quote($prod_pkey);
                $_OMP_row = $_OMP_db->queryRow($sql_ol_pkey);
                if (!($_OMP_row)) {
                    OMP_genErr('Could not retrieve ol_pkey for
                        order ref. '.$items[0]['supplier_ref']);
                } else {
                    $item['int_ol_pkey'] = $_OMP_row['ol_pkey'];
                }
                /* unset pkey because it's not in
                 * $_OMP_datatypes_del_sql_insert_line
                 */
                unset($item['int_pkey']);
                /* @see functions.php for OMP_makeData()
                @see deliveries-schema.php for $_OMP_del_sql_insert_line and
                $_OMP_datatypes_del_sql_insert_line */
                $_OMP_data = OMP_makeData($_OMP_del_sql_insert_line, false, $item);
                $_OMP_db_prepared = $_OMP_db->prepare($_OMP_del_sql_insert_line,
                    $_OMP_datatypes_del_sql_insert_line);
                $_OMP_db_prepared->execute($_OMP_data);
                $_OMP_db_prepared->free();
                $_OMP_data =
                    array(
                        $item['int_ol_pkey'],
                        $inv_pkey,
                        $shipment['date'],
                        $item['txt_del_pkey'],
                        $item['num_quantity']
                    );
                $_OMP_datatypes =
                    array(
                        'integer',
                        'text',
                        'date',
                        'text',
                        'decimal'
                    );
                /* adds invoice */
                $_OMP_db_prepared =
                    $_OMP_db->prepare('SELECT dl_new(?,?,?,?,?,0)',
                        $_OMP_datatypes);
                $_OMP_db_result = $_OMP_db_prepared->execute($_OMP_data);
                $_OMP_db_prepared->free();
            }
        }
        // if ($_POST['update'] == 1) {
        //     $OMP_redirPage_msg .= $_OMP_LC[2].' '.$_OMP_LC[40];
        // } else {
        //     $OMP_redirPage_msg .= $_OMP_LC[45];
        // }
        if (!$_POST['update']) {
            $OMP_redirPage_msg .= $_OMP_LC[45];
        }
    }
}
if (empty($OMP_redirPage_msg)) {
    /* form of unknown type */
    OMP_genErr('<a href="ocr-load.php">'.$_OMP_lcl[4003].'</a>');
}
OMP_redirPage('4', 'ocr-load.php', $OMP_redirPage_msg, $_OMP_LC[4002]);
eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
OMP_lose();
?>
