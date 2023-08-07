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
// $Id: mdc-select-enduses.php,v 0.8 $
//
// Combo box with enduses records
//
/* Check that enduse_pkey is initialised */
isset($_OMP_rec['enduse_pkey']) or $_OMP_rec['enduse_pkey'] = '';
/* Check that required list is initialised */
isset($_OMP_combo_required) or $_OMP_combo_required = false;
/* $_OMP_sql['enduse_combo'] is defined in dd-sql.php */
$_OMP_enduse_query = $_OMP_db->query($_OMP_sql['enduse_combo']);
/* Add blank label item to start of list */
$_OMP_html['list_item_value'] = '';
$_OMP_html['list_item_text'] = '      ';
eval("\$_OMP_html['select_list'] = \"".$_OMP_TPL[83]."\";");
$preselected = false;
while ($_OMP_enduse_rec = $_OMP_enduse_query->fetchRow()) {
    $enduse_pkey_value = urlencode($_OMP_enduse_rec['pkey']);
    $enduse_pkey_text = $_OMP_enduse_rec['pkey'];
    $enduse_pkey_text = OMP_htmlentities($enduse_pkey_text);
    if ($_OMP_enduse_rec['pkey'] == $_OMP_rec['enduse_pkey']) {
        $preselected = true;
        $_OMP_html['list_item_selected_value'] = $enduse_pkey_value;
        $_OMP_html['list_item_selected_text'] = $enduse_pkey_text;
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[85]."\";");
    } else {
        $_OMP_html['list_item_value'] = $enduse_pkey_value;
        $_OMP_html['list_item_text'] = $enduse_pkey_text;
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
    }
}
$_OMP_html['select_width'] = OMP_DDL_WIDTH_STD;
$_OMP_html['select_name'] = 'form[enc_enduse_pkey]';
$_OMP_html['select_label'] =
    $_OMP_html['select_aria_label'] = $_OMP_LC[105];
/* required by TPL 80, 81, 82 and 87 */
$_OMP_html['select_id'] = 'enduse_pkey';
if ($_OMP_combo_required && $preselected) {
    eval("\$_OMP_html['enduse_combo'] = \"".$_OMP_TPL[82]."\";");
} elseif ($_OMP_combo_required) {
    eval("\$_OMP_html['enduse_combo'] = \"".$_OMP_TPL[81]."\";");
} elseif (!empty($_OMP_enduse_combo_disabled)) {
    /* is this necessary for enduse? */
    eval("\$_OMP_html['enduse_combo'] = \"".$_OMP_TPL[86]."\";");
} else {
    /* also for $preselected */
    eval("\$_OMP_html['enduse_combo'] = \"".$_OMP_TPL[80]."\";");
}
$_OMP_enduse_query->free();
unset($_OMP_enduse_rec);
?>
