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
// $Id: edit-line.php,v 0.8 $
//
// Edit line record
//

require_once 'functions.php';
/**
* Match arrays against primary keys
*
* @param array values, array parameters, array primary keys
* @return bool if arrays match
*/
function OMP_keyLineMatch($array_one, $array_two, $array_key)
{
    $match = true;
    foreach ($array_key as $key) {
        $new_key = 'kl_'.$key;
        if (isset($array_two[$new_key])) {
            if ($array_one[$key] != $array_two[$new_key]) {
                $match = false;
            }
        }
    }
    return $match;
}

// Check if insert-button was pushed
if (isset($_POST['insert_button'])) {
    $_OMP_post = unserialize(base64_decode($_POST['post_to_get']));
    // Temporarily change table descriptor and field lengths
    $_OMP_tmp_a = $_OMP_tbl_fld;
    $_OMP_tmp_b = $_OMP_fld_len;
    $_OMP_tbl_fld = $_OMP_tbl_fld_line;
    $_OMP_fld_len = $_OMP_fld_len_line;
    empty($_OMP_change_post) or OMP_changePost();
    // Must add client_pkey to form when adding new delivery-line
//     (!isset($_OMP_trans) && empty($POST['inv_pkey'])) or 
    !(isset($_OMP_trans)) or // Delivery-line
        $_POST['form']['txt_client_pkey'] = $_OMP_post['pk_client_pkey'];
    // Make array $data with $_POST variables
    $_OMP_data = OMP_makeData($_OMP_sql['update_line']);
    // Go back to original values
    $_OMP_tbl_fld = $_OMP_tmp_a;
    $_OMP_fld_len = $_OMP_tmp_b;
    if (isset($tbc_pkey)) { unset($_OMP_data[8]);}
    $_OMP_data = OMP_keyCheck($_OMP_post, true, $_OMP_data);
    $_OMP_prepared = 
        $_OMP_db->prepare($_OMP_sql['update_line'], $_OMP_datatypes);
    $_OMP_result = $_OMP_prepared->execute($_OMP_data);
    $_OMP_result->free();
    $_OMP_prepared->free();
    unset($_OMP_data);
    // See OMP_changePost in deliveries.php
    if (isset($_OMP_trans)) { // Delivery-line
        if (!empty($POST['inv_pkey'])) {
            $ol_pkey = $_POST['form']['num_ol_pkey'];
            $inv_pkey = $_POST['inv_pkey'];
            $invoice_date = 
                OMP_checkDate($_POST['date'],'Invoice date' , false);
            $del_pkey = $_POST['form']['txt_del_pkey'];
            $quantity = 
                OMP_checkNum($_POST['form']['num_quantity'], 'Quantity');
            $quantity = 
                number_format(
                    $_POST['form']['num_quantity'], 
                    OMP_DB_DECIMALS, 
                    OMP_DB_DEC_POINT, 
                    OMP_DB_THOUSANDS_SEP
                );
            $kl_key = $_OMP_post['kl_pkey'];
            $_OMP_data = 
                array(
                    $ol_pkey,
                    $inv_pkey, 
                    $invoice_date, 
                    $del_pkey, 
                    $quantity, 
                    $kl_key
                );
            $_OMP_datatypes = 
                array(
                    'integer', 
                    'text', 
                    'date', 
                    'text', 
                    'decimal', 
                    'integer'
                );
            $_OMP_prepared = 
                $_OMP_db->prepare(
                    'SELECT dl_new(?,?,?,?,?,?)', 
                    $_OMP_datatypes
                );
            $_OMP_result = $_OMP_prepared->execute($_OMP_data);
            $_OMP_result->free();
            $_OMP_prepared->free();
        }
        unset($_OMP_post['pk_client_pkey']);
        unset($_OMP_post['kh_client_pkey']);
    }
    $_OMP_post = OMP_keyUnset($_OMP_post, $_OMP_tbl_line_key);
    // Redirection on template number 4
    $_OMP_tmp_a = OMP_redirectUrl(OMP_makeGet($_OMP_post), true, false);
    $_OMP_tmp_b = 'Record '.$_OMP_LC[40];
    OMP_redirPage('4', $_OMP_tmp_a, $_OMP_tmp_b, $_OMP_tmp_b);
} else {
    require 'lib/input-line.php';
    // Form focus. It could be changed by OMP_newLine()
    // $_OMP_onload = 'onload="javascript:document.forms[0].elements[0].'.
    //     'focus();return true"';
    $_OMP_onload = '';
    while ($_OMP_rec_sf = $_OMP_result_sf->fetchRow()) {
        // Check if current record is the one to be edited
        $_OMP_tmp_a = 
            OMP_keyLineMatch($_OMP_rec_sf, $_OMP_get, $_OMP_tbl_line_key);
        if ($_OMP_tmp_a) {
            // Requires drop-down lists
            if (isset($_OMP_drop_down)) {
                foreach($_OMP_drop_down as $_OMP_tmp_a) {
                    require $_OMP_tmp_a;
                }
            }
//            OMP_makeVarsLine(true);
            OMP_newLine(); // @see orders.php
            eval ("\$_OMP_list_rec .= \"".$_OMP_input_tpl."\";");
        } else {
            OMP_makeVarsLine();
            eval ("\$_OMP_list_rec .= \"".$_OMP_rec_tpl."\";");
        }
    }
    $_OMP_result_sf->free();
    unset($_OMP_get['sql']);
    unset($_OMP_get['sql_sub']);
    $_OMP_post = OMP_makeHidden($_OMP_get);
    // URI for FORM action
    $_OMP_url = OMP_link('action=editline');
    OMP_buttons('14', '38', '37', 'javascript:history.back();return true;');
    isset($_OMP_html['script']) or $_OMP_html['script'] = '';
    eval("\$_OMP_html['input'] = \"".$_OMP_frame_tpl."\";");
    $_OMP_html['record'] = '';
    require 'lib/record_title.php';
    eval("\$_OMP_html['details'] =
            \"".$_OMP_TPL[51]."\";");
    eval("\$_OMP_html['record'] =
        \"".$_OMP_master_tpl."\";");
    $_OMP_button_close_popup = $_OMP_html['navbar'] =
        $_OMP_html['script'] = $_OMP_LC[86] =
        $_OMP_html['logo'] = '';
    eval("\$_OMP_html['include'] =
        \"".$_OMP_TPL[$_OMP_conf['record_template']]."\";");
    // eval("\$_OMP_html['include'] = \"".$_OMP_master_tpl."\";");
    $_OMP_html['browser_title'] = $_OMP_LC[33];
}
eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
OMP_lose();
?>
