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
// $Id: mdc-select-currencies.php,v 0.8 $
//
// Combo box with currencies (EUR and USD)
//
/* Check that curr is initialised */
isset($_OMP_rec['curr']) or $_OMP_rec['curr'] = '';
/* Check that required list is initialised */
isset($_OMP_combo_required) or $_OMP_combo_required = false;
/* $_OMP_sql['currencies_combo'] is defined in dd-sql.php */
$_OMP_curr_query = $_OMP_db->query($_OMP_sql['currencies_combo']);
/* Add blank label item to start of list */
$_OMP_html['list_item_value'] = '';
$_OMP_html['list_item_text'] = '      ';
eval("\$_OMP_html['select_list'] = \"".$_OMP_TPL[83]."\";");
/* Add active currencies label item */
$_OMP_html['list_item_value'] = '';
$_OMP_html['list_item_text'] = $_OMP_LC[124];
eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[84]."\";");
$group = true;
$preselected = false;
while ($_OMP_curr_rec = $_OMP_curr_query->fetchRow()) {
    /* Add inactive currency label item to list
     * $_OMP_sql['currencies_combo'] lists
     * active currencies first
     */
    if ($_OMP_curr_rec['active'] == 'f') {
        if ($group) {
            $_OMP_html['list_item_value'] = '';
            $_OMP_html['list_item_text'] = $_OMP_LC[125];
            eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[84]."\";");
            $group = false;
        }
    }
    /* Replace last 3 chars with ellipsis if client_pkey too long */
    // if (strlen($_OMP_curr_rec['pkey']) > $_SESSION['ddl_width']) {
    //     $curr_pkey_text =
    //         substr($_OMP_curr_rec['pkey'], 0, $_SESSION['ddl_width'] - 3).
    //             '...';
    // } else {
        $curr_pkey_text = $_OMP_curr_rec['pkey'];
    // }
    $curr_pkey_value = urlencode($_OMP_curr_rec['pkey']);
    $curr_pkey_text = OMP_htmlentities($curr_pkey_text);
    if ($_OMP_curr_rec['pkey'] == $_OMP_rec['curr']) {
        $preselected = true;
        $_OMP_html['list_item_selected_value'] = $curr_pkey_value;
        $_OMP_html['list_item_selected_text'] = $curr_pkey_text;
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[85]."\";");
    } else {
        $_OMP_html['list_item_value'] = $curr_pkey_value;
        $_OMP_html['list_item_text'] = $curr_pkey_text;
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
    }
}
// if ($_OMP_rec['curr'] == 'EUR') {
//     $preselected = true;
//     /* List where EUR is selected */
//     $_OMP_html['list_item_selected_value'] = 'EUR';
//     $_OMP_html['list_item_selected_text'] = 'EUR';
//     eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[85]."\";");
//     $_OMP_html['list_item_value'] = 'USD';
//     $_OMP_html['list_item_text'] = 'USD';
//     eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
// } elseif ($_OMP_rec['curr'] == 'USD') {
//     $preselected = true;
//     /* List where USD is selected */
//     $_OMP_html['list_item_value'] = 'EUR';
//     $_OMP_html['list_item_text'] = 'EUR';
//     eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
//     $_OMP_html['list_item_selected_value'] = 'USD';
//     $_OMP_html['list_item_selected_text'] = 'USD';
//     eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[85]."\";");
// } else {
//     $preselected = false;
//     /* List where no curr is selected */
//     $_OMP_html['list_item_value'] = '';
//     $_OMP_html['list_item_text'] = '';
//     eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[84]."\";");
//     $_OMP_html['list_item_value'] = 'EUR';
//     $_OMP_html['list_item_text'] = 'EUR';
//     eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
//     $_OMP_html['list_item_value'] = 'USD';
//     $_OMP_html['list_item_text'] = 'USD';
//     eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
// }
$_OMP_html['select_width'] = OMP_DDL_WIDTH_SML;
$_OMP_html['select_name'] = 'form[enc_curr]';
$_OMP_html['select_label'] =
    $_OMP_html['select_aria_label'] = $_OMP_LC[1401];
/* required by TPL 80, 81, 82 and 87 */
$_OMP_html['select_id'] = 'curr';
$_OMP_html['onchange'] = '';
if ($_OMP_combo_required && $preselected) {
    eval("\$_OMP_html['curr_combo'] = \"".$_OMP_TPL[82]."\";");
} elseif ($_OMP_combo_required) {
    eval("\$_OMP_html['curr_combo'] = \"".$_OMP_TPL[81]."\";");
} elseif (!empty($_OMP_curr_combo_disabled)) {
    /* is this necessary for curr? */
    eval("\$_OMP_html['curr_combo'] = \"".$_OMP_TPL[86]."\";");
} else {
    /* also for $preselected */
    eval("\$_OMP_html['curr_combo'] = \"".$_OMP_TPL[80]."\";");
}
$_OMP_curr_query->free();
unset($_OMP_curr_rec);
?>
