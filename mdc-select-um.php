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
// $Id: mdc-select-um.php,v 0.8 $
//
// Combo box with units of measure kg or lb
//
/* Check that um is initialised */
isset($_OMP_rec['um']) or $_OMP_rec['um'] = '';
/* Check that required list is initialised
 * this field is REQUIRED BY DEFAULT
 */
isset($_OMP_combo_required) or $_OMP_combo_required = true;
/* $_OMP_sql['um_combo'] is defined in dd-sql.php */
$_OMP_um_query = $_OMP_db->query($_OMP_sql['um_combo']);
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
while ($_OMP_um_rec = $_OMP_um_query->fetchRow()) {
    /* Add inactive currency label item to list
     * $_OMP_sql['currencies_combo'] lists
     * active currencies first
     */
    if ($_OMP_um_rec['active'] == 'f') {
        if ($group) {
            $_OMP_html['list_item_value'] = '';
            $_OMP_html['list_item_text'] = $_OMP_LC[125];
            eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[84]."\";");
            $group = false;
        }
    }

    $um_pkey_value = urlencode($_OMP_um_rec['pkey']);
    $um_pkey_txt = $_OMP_um_rec['pkey'];
    $um_pkey_txt = OMP_htmlentities($um_pkey_txt);
    if ($_OMP_um_rec['pkey'] == $_OMP_rec['um']) {
        $preselected = true;
        $_OMP_html['list_item_selected_value'] = $um_pkey_value;
        $_OMP_html['list_item_selected_text'] = $um_pkey_txt;
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[85]."\";");
    } else {
        $_OMP_html['list_item_value'] = $um_pkey_value;
        $_OMP_html['list_item_text'] = $um_pkey_txt;
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
    }
}
// if ($_OMP_rec['um'] == 'kg') {
//     $preselected = true;
//     /* List where kg is selected */
//     $_OMP_html['list_item_selected_value'] = 0;
//     $_OMP_html['list_item_selected_text'] = 'kg';
//     eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[85]."\";");
//     $_OMP_html['list_item_value'] = 1;
//     $_OMP_html['list_item_text'] = 'lb';
//     eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
// } elseif ($_OMP_rec['um'] == 'lb') {
//     $preselected = true;
//     /* List where lb is selected */
//     $_OMP_html['list_item_value'] = 0;
//     $_OMP_html['list_item_text'] = 'kg';
//     eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
//     $_OMP_html['list_item_selected_value'] = 1;
//     $_OMP_html['list_item_selected_text'] = 'lb';
//     eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[85]."\";");
// } else {
//     $preselected = false;
//     /* List where no um is selected */
//     $_OMP_html['list_item_value'] = '';
//     $_OMP_html['list_item_text'] = '';
//     eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[84]."\";");
//     $_OMP_html['list_item_value'] = 0;
//     $_OMP_html['list_item_text'] = 'kg';
//     eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
//     $_OMP_html['list_item_value'] = 1;
//     $_OMP_html['list_item_text'] = 'lb';
//     eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
// }
$_OMP_html['select_width'] = OMP_DDL_WIDTH_SML;
$_OMP_html['select_name'] = 'form[enc_um]';
$_OMP_html['select_label'] =
    $_OMP_html['select_aria_label'] = $_OMP_LC[726];
/* required by TPL 80, 81, 82 and 87 */
$_OMP_html['select_id'] = 'um';
$_OMP_html['onchange'] = '';
if ($_OMP_combo_required && $preselected) {
    eval("\$_OMP_html['um_combo'] = \"".$_OMP_TPL[82]."\";");
} elseif ($_OMP_combo_required) {
    eval("\$_OMP_html['um_combo'] = \"".$_OMP_TPL[81]."\";");
} elseif (!empty($_OMP_um_combo_disabled)) {
    /* is this necessary for um? */
    eval("\$_OMP_html['um_combo'] = \"".$_OMP_TPL[86]."\";");
} else {
    /* also for $preselected */
    eval("\$_OMP_html['um_combo'] = \"".$_OMP_TPL[80]."\";");
}
$_OMP_um_query->free();
unset($_OMP_um_rec);
?>
