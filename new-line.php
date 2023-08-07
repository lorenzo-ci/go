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
// $Id: new-line.php,v 0.8 $
//
// Add new line record
//

/**
* Make $_OMP_rec_sf array for edit and insert forms
*
* @access public
*/
function OMP_initRecLine()
{
    global $_OMP_rec_sf, $_OMP_tbl_fld_line;
    // Preserve 'year', 'month', 'eta' for usability
    if (empty($_POST['form'])) {
        for (reset($_OMP_tbl_fld_line); $key = key($_OMP_tbl_fld_line); 
            next($_OMP_tbl_fld_line)) {
            if ($key != 'year' && $key != 'month' && $key != 'eta') {
                $_OMP_rec_sf[$key] = '';
            }
        }
    } else {
        for (reset($_POST['form']); $key = key($_POST['form']); 
            next($_POST['form'])) {
            if ($key == 'date' || $key == 'eta') {
                $new_key = $key;
            } else {
                $new_key = substr($key, 4);
            }
            $_OMP_rec_sf[$new_key] = $_POST['form'][$key];
        }
    }
}
// Main GET elements go in $_OMP_get_str
$_OMP_get_str = OMP_makeGet(array_map('stripslashes', $_OMP_get));
// Check if insert-button was pushed
if (isset($_POST['insert_button'])) {
    // See orders.php and deliveries.php
    empty($_OMP_change_post) or OMP_changePost();
    setlocale(LC_NUMERIC, $_SESSION['locale']);
    // Temporarily change table descriptor and field lengths
    $_OMP_tmp_a = $_OMP_tbl_fld;
    $_OMP_tmp_b = $_OMP_fld_len;
    $_OMP_tbl_fld = $_OMP_tbl_fld_line;
    $_OMP_fld_len = $_OMP_fld_len_line;
    // Make array $data with $_POST vars
    $_OMP_data = OMP_makeData($_OMP_sql['insert_line']);
    // Go back to original values
    $_OMP_tbl_fld = $_OMP_tmp_a;
    $_OMP_fld_len = $_OMP_tmp_b;
    unset($_OMP_tmp_a); unset($_OMP_tmp_b);
    $_OMP_prepared = 
        $_OMP_db->prepare($_OMP_sql['insert_line'], $_OMP_datatypes);
    $_OMP_result = $_OMP_prepared->execute($_OMP_data);
    $_OMP_result->free();
    $_OMP_prepared->free();
    if (isset($_OMP_trans)) { // Delivery-line
        $ol_pkey = $_POST['form']['num_ol_pkey'];
        $inv_pkey = $_POST['inv_pkey'];
        $invoice_date = $_POST['date'];
        $del_pkey = $_POST['form']['txt_del_pkey'];
        $quantity = OMP_checkNum($_POST['form']['num_quantity'], 'Quantity');
        $quantity = number_format($quantity, OMP_DB_DECIMALS, 
          OMP_DB_DEC_POINT, OMP_DB_THOUSANDS_SEP);
        $_OMP_data = 
            array($ol_pkey, $inv_pkey, $invoice_date, $del_pkey, $quantity);
        $_OMP_datatypes = array('integer', 'text', 'date', 'text', 'decimal');
        $_OMP_prepared = 
            $_OMP_db->prepare('SELECT dl_new(?,?,?,?,?,0)', $_OMP_datatypes);
        $_OMP_result = $_OMP_prepared->execute($_OMP_data);
        $_OMP_result->free();
        $_OMP_prepared->free();
    }
    // Redir in template number 4
    $_OMP_tmp_a = 'action=newdetail'.stripslashes($_OMP_get_str);
    $_OMP_url = OMP_redirectUrl($_OMP_tmp_a, true, false);
    // Record added
    $_OMP_redir = true;
} else {
    require 'lib/input-line.php';
    $_OMP_tmp_a = array();
    while ($_OMP_rec_sf = $_OMP_result_sf->fetchRow()) {
        $_OMP_tmp_a = $_OMP_rec_sf;
        OMP_makeVarsLine();
        eval ("\$_OMP_list_rec .= \"".$_OMP_rec_tpl."\";");
    }
    // PEAR.MDB2 (not PEAR.DB) empties $_OMP_rec_sf after the while cycle
    $_OMP_rec_sf = $_OMP_tmp_a;
    $_OMP_result_sf->free();
    OMP_initRecLine();
    OMP_newLine();
    // Requires eventual drop-down lists
    if (isset($_OMP_drop_down)) {
        foreach($_OMP_drop_down as $_OMP_tmp_a) {
            $_OMP_prefilter = true;
            require $_OMP_tmp_a;
        }
    }
    // This is for order-line and delivery-line
    // when supplier has no products in database
    if ($_OMP_noitems) {
        $_OMP_tmp_a = '';
        ('' == $_OMP_get['row_num']) or 
            $_OMP_tmp_a = 'row_num='.$_OMP_get['row_num'];
        $_OMP_tmp_a .= OMP_keyMake(true, true);
        // Redirection in template number 4.
        $_OMP_tmp_a = OMP_redirectUrl($_OMP_tmp_a, true, false);
        OMP_redirPage('4', $_OMP_tmp_a, $_OMP_noitmes_msg, 
            $_OMP_LC[85], $_OMP_LC[48]);
    } else {
        eval("\$_OMP_list_rec .= \"".$_OMP_input_tpl."\";");
        $_OMP_tmp_a = 'javascript:window.location=\'';
        $_OMP_tmp_a .= OMP_redirectUrl($_OMP_get_str).'\';return true';
        OMP_buttons('14', '39', '82', $_OMP_tmp_a);
        $_OMP_post = '';
        isset($_OMP_html['script']) or $_OMP_html['script'] = '';
        eval("\$_OMP_html['input'] = \"".$_OMP_frame_tpl."\";");
        require 'lib/record_title.php';
        // URI for form action
        $_OMP_tmp_a = 'action=newdetail'.$_OMP_get_str;
        $_OMP_url = OMP_link($_OMP_tmp_a);
        eval("\$_OMP_html['details'] = \"".$_OMP_TPL[69]."\";");
        $_OMP_button_close_popup = $_OMP_html['navbar'] = $_OMP_LC[86] = '';
        eval("\$_OMP_html['record'] = \"".$_OMP_master_tpl."\";");
        eval("\$_OMP_html['include'] = \"".$_OMP_TPL[15]."\";");
        $_OMP_html['browser_title'] = $_OMP_html['page_title'] =
            $_OMP_LC[$_OMP_page_title_lcl].' - '.
            $_OMP_LC[28].' '.$_OMP_LC[23];
        /* mdc-toolbar mdc-drawer button */
        $_OMP_html['toolbar'] = $_OMP_html['menu_button'] =
            $_OMP_html['logo'] = '';
    }
}
eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
OMP_lose();
?>
