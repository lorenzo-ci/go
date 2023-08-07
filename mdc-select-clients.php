<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | OMP Version 1                                                        |
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
// | Author: Lorenzo Ciani <lorenzo@ciani.it>                             |
// +----------------------------------------------------------------------+
//
// $Id: dd-clients.php,v 0.8 $
//
// Combo box with clients records
//
/* Check that client_pkey is initialised */
isset($_OMP_rec['client_pkey']) or $_OMP_rec['client_pkey'] = '';
/* Check that required list is initialised */
isset($_OMP_combo_required) or $_OMP_combo_required = false;
/* $_OMP_sql['clients_combo'] is defined in dd-sql.php */
$_OMP_cli_query = $_OMP_db->query($_OMP_sql['clients_combo']);
/* Add blank label item to start of list */
$_OMP_html['list_item_value'] = '';
$_OMP_html['list_item_text'] = '      ';
eval("\$_OMP_html['select_list'] = \"".$_OMP_TPL[83]."\";");
/* Add active clients label item */
$_OMP_html['list_item_value'] = '';
$_OMP_html['list_item_text'] = $_OMP_LC[120];
eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[84]."\";");
$group = true;
$preselected = false;
while ($_OMP_cli_rec = $_OMP_cli_query->fetchRow()) {
    /* Add inactive client label item to list */
    /* $_OMP_sql['clients_combo'] lists active clients first */
    if ($_OMP_cli_rec['active'] == 'f') {
        if ($group) {
            $_OMP_html['list_item_value'] = '';
            $_OMP_html['list_item_text'] = $_OMP_LC[121];
            eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[84]."\";");
            $group = false;
        }
    }
    /* Replace last 3 chars with ellipsis if client_pkey too long */
    // if (strlen($_OMP_cli_rec['pkey']) > $_SESSION['ddl_width']) {
    //     $client_pkey_text =
    //         substr($_OMP_cli_rec['pkey'], 0, $_SESSION['ddl_width'] - 3).
    //             '...';
    // } else {
        $client_pkey_text = $_OMP_cli_rec['pkey'];
    // }
    $client_pkey_value = urlencode($_OMP_cli_rec['pkey']);
    $client_pkey_text = OMP_htmlentities($client_pkey_text);
    if ($_OMP_cli_rec['pkey'] == $_OMP_rec['client_pkey']) {
        $preselected = true;
        $_OMP_html['list_item_selected_value'] = $client_pkey_value;
        $_OMP_html['list_item_selected_text'] = $client_pkey_text;
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[85]."\";");
    } else {
        $_OMP_html['list_item_value'] = $client_pkey_value;
        $_OMP_html['list_item_text'] = $client_pkey_text;
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
    }
}
$_OMP_html['select_width'] = OMP_DDL_WIDTH_STD;
if ($_OMP_tbl == 'tbcs') {
    $_OMP_html['select_name'] = 'form[enc_pkey]';
} else {
    $_OMP_html['select_name'] = 'form[enc_client_pkey]';
}
$_OMP_html['select_label'] =
    $_OMP_html['select_aria_label'] = $_OMP_LC[100];
/* required by TPL 80, 81, 82 and 87 */
$_OMP_html['select_id'] = 'enc_client_pkey';
if (!empty($_OMP_onchange)) {
    /* $_OMP_html['select_onchange'] is initialised in base.php */
    eval("\$_OMP_html['select_onchange'] .= \"".$_OMP_TPL[87]."\";");
    /* $_OMP_html['onchange'] =
        ' onchange="javascript:this.form.submit();return true;"'; */
}
if ($_OMP_combo_required && $preselected) {
    eval("\$_OMP_html['client_combo'] = \"".$_OMP_TPL[82]."\";");
} elseif ($_OMP_combo_required) {
    eval("\$_OMP_html['client_combo'] = \"".$_OMP_TPL[81]."\";");
} elseif (!empty($_OMP_client_combo_disabled)) {
    // disabled in invoices.php edit record
    eval("\$_OMP_html['client_combo'] = \"".$_OMP_TPL[86]."\";");
} else {
    /* also for $preselected */
    eval("\$_OMP_html['client_combo'] = \"".$_OMP_TPL[80]."\";");
}
$_OMP_cli_query->free();
unset($_OMP_cli_rec);
?>
