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
// $Id: mdc-select-suppliers.php,v 0.8 $
//
// Combo box with suppliers records and javascript
//
/* Check that client_pkey is initialised */
isset($_OMP_rec['supplier_pkey']) or $_OMP_rec['supplier_pkey'] = '';
/* Check that required list is initialised */
isset($_OMP_combo_required) or $_OMP_combo_required = false;
/* $_OMP_sql['suppliers_combo'] is defined in dd-sql.php */
$_OMP_cli_query = $_OMP_db->query($_OMP_sql['suppliers_combo']);
/* Add blank label item to start of list */
$_OMP_html['list_item_value'] = '';
$_OMP_html['list_item_text'] = '      ';
eval("\$_OMP_html['select_list'] = \"".$_OMP_TPL[83]."\";");
/* Add active suppliers label item */
$_OMP_html['list_item_value'] = '';
$_OMP_html['list_item_text'] = $_OMP_LC[120];
eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[84]."\";");
$group = true;
$preselected = false;
while ($_OMP_sup_rec = $_OMP_cli_query->fetchRow()) {
    /* Add inactive supplier label item to list */
    /* $_OMP_sql['suppliers_combo'] lists active suppliers first */
    if ($_OMP_sup_rec['active'] == 'f') {
        if ($group) {
            $_OMP_html['list_item_value'] = '';
            $_OMP_html['list_item_text'] = $_OMP_LC[121];
            eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[84]."\";");
            $group = false;
        }
    }
    $supplier_pkey_text = $_OMP_sup_rec['pkey'];
    $supplier_pkey_value = urlencode($_OMP_sup_rec['pkey']);
    $supplier_pkey_text = OMP_htmlentities($supplier_pkey_text);
    if ($_OMP_sup_rec['pkey'] == $_OMP_rec['supplier_pkey']) {
        $preselected = true;
        $_OMP_html['list_item_selected_value'] = $supplier_pkey_value;
        $_OMP_html['list_item_selected_text'] = $supplier_pkey_text;
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[85]."\";");
    } else {
        $_OMP_html['list_item_value'] = $supplier_pkey_value;
        $_OMP_html['list_item_text'] = $supplier_pkey_text;
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
    }
}
$_OMP_html['select_width'] = OMP_DDL_WIDTH_STD;
$_OMP_html['select_name'] = 'form[enc_supplier_pkey]';
$_OMP_html['select_label'] =
    $_OMP_html['select_aria_label'] = $_OMP_LC[504];
/* required by TPL 80, 81, 82 and 87 */
$_OMP_html['select_id'] = 'enc_supplier_pkey';
if (!empty($_OMP_onchange)) {
    /* $_OMP_html['select_onchange'] is initialised in base.php */
    eval("\$_OMP_html['select_onchange'] .= \"".$_OMP_TPL[87]."\";");
    /* $_OMP_html['onchange'] =
        ' onchange="javascript:this.form.submit();return true;"'; */
}
if ($_OMP_combo_required && $preselected) {
    eval("\$_OMP_html['supplier_combo'] = \"".$_OMP_TPL[82]."\";");
} elseif ($_OMP_combo_required) {
    eval("\$_OMP_html['supplier_combo'] = \"".$_OMP_TPL[81]."\";");
} elseif (!empty($_OMP_supplier_combo_disabled)) {
    /* Do I need this? */
    eval("\$_OMP_html['supplier_combo'] = \"".$_OMP_TPL[86]."\";");
} else {
    /* also for $preselected */
    eval("\$_OMP_html['supplier_combo'] = \"".$_OMP_TPL[80]."\";");
}
$_OMP_cli_query->free();
unset($_OMP_sup_rec);
?>
