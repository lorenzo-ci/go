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
// $Id: mdc-select-banks.php,v 0.2 $
//
// Combo box with banks records
//
/* Check that bank_pkey is initialised */
isset($_OMP_rec['client_pkey']) or $_OMP_rec['client_pkey'] = '';
/* Check that required list is initialised */
isset($_OMP_combo_required) or $_OMP_combo_required = false;
// See new-line.php
/* $_OMP_sql['banks_combo'] is defined in dd-sql.php */
if (empty($_OMP_rec['client_pkey'])) {
    $_OMP_result =
        $_OMP_db->query($_OMP_sql['banks_combo_no_filter']);
} else {
//     $_OMP_banks_query =
//         $_OMP_db->query($_OMP_sql['banks_combo'], array($_OMP_rec['client_pkey']));
    //This is unproper, but I know no other way to replace the above
    $prepared = $_OMP_db->prepare($_OMP_sql['banks_combo']);
    $_OMP_result = $prepared->execute($_OMP_rec['client_pkey']);
}
/* Add blank label item to start of list */
$_OMP_html['list_item_value'] = '';
$_OMP_html['list_item_text'] = '      ';
eval("\$_OMP_html['select_list'] = \"".$_OMP_TPL[83]."\";");
$preselected = false;
while ($_OMP_banks_rec = $_OMP_result->fetchRow()) {
    $bank_pkey_text = $_OMP_banks_rec['pkey'];
    $banks_pkey_value = urlencode($_OMP_banks_rec['pkey']);
    $bank_pkey_text = OMP_htmlentities($bank_pkey_text);
    if (isset($_OMP_rec['bank_pkey']) &&
        ($_OMP_banks_rec['pkey'] == $_OMP_rec['bank_pkey'])
        ) {
        $preselected = true;
        $_OMP_html['list_item_selected_value'] = $banks_pkey_value;
        $_OMP_html['list_item_selected_text'] = $bank_pkey_text;
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[85]."\";");
    } else {
        $_OMP_html['list_item_value'] = $banks_pkey_value;
        $_OMP_html['list_item_text'] = $bank_pkey_text;
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
    }
}
$_OMP_html['select_width'] = OMP_DDL_WIDTH_STD;
$_OMP_html['select_name'] = 'form[nul_bank_pkey]';
$_OMP_html['select_label'] =
    $_OMP_html['select_aria_label'] = $_OMP_LC[900];
/* required by TPL 80, 81, 82 and 87 */
$_OMP_html['select_id'] = 'bank_pkey';
if (!empty($_OMP_onchange)) {
    /* $_OMP_html['select_onchange'] is initialised in base.php */
    eval("\$_OMP_html['select_onchange'] .= \"".$_OMP_TPL[87]."\";");
    /* $_OMP_html['onchange'] =
        ' onchange="javascript:this.form.submit();return true;"'; */
}
if ($_OMP_combo_required && $preselected) {
    eval("\$_OMP_html['banks_combo'] = \"".$_OMP_TPL[82]."\";");
} elseif ($_OMP_combo_required) {
    eval("\$_OMP_html['banks_combo'] = \"".$_OMP_TPL[81]."\";");
} elseif (!empty($_OMP_bank_combo_disabled)) {
    // disabled in invoices.php edit record
    eval("\$_OMP_html['banks_combo'] = \"".$_OMP_TPL[86]."\";");
} else {
    /* also for $preselected */
    eval("\$_OMP_html['banks_combo'] = \"".$_OMP_TPL[80]."\";");
}
$_OMP_result->free();
unset($_OMP_banks_rec);
?>
