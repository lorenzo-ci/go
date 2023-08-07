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
// $Id: mdc-select-yesno.php,v 0.1 $
//
// Combo box with yes / no values
//

/**
* Makes HTML drop-down list with yes / no values
*
* @param string $field name of the HTML control
* @param string $index form[] index
* @param string $label $_OMP_LC index for the label of the HTML control
* @param bool $required is the HTML control required?
* @param bool $disabeld is the HTML control disabled?
* @return HTML control string
*/

function OMP_mdcSelectYesNo($field, $index, $label, $required = false, $disabled = false)
{
    global $_OMP_html, $_OMP_LC, $_OMP_rec, $_OMP_TPL;
    /* Check that field is initialised */
    isset($_OMP_rec[$field]) or $_OMP_rec[$field] = '';
    /* Add blank label item to start of list */
    $_OMP_html['list_item_value'] = '';
    $_OMP_html['list_item_text'] = '      ';
    eval("\$_OMP_html['select_list'] = \"".$_OMP_TPL[83]."\";");
    if ($_OMP_rec[$field] === true) {
        $preselected = true;
        /* List where Kg is selected */
        $_OMP_html['list_item_selected_value'] = "1";
        $_OMP_html['list_item_selected_text'] = $_OMP_LC[98];
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[85]."\";");
        $_OMP_html['list_item_value'] = "1";
        $_OMP_html['list_item_text'] = $_OMP_LC[98];
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
    } elseif ($_OMP_rec[$field] === false) {
        $preselected = true;
        /* List where Lbs is selected */
        $_OMP_html['list_item_value'] = "0";
        $_OMP_html['list_item_text'] = $_OMP_LC[99];
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
        $_OMP_html['list_item_selected_value'] = "1";
        $_OMP_html['list_item_selected_text'] = $_OMP_LC[99];
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[85]."\";");
    } else {
        $preselected = false;
        /* List where no value is selected */
        $_OMP_html['list_item_value'] = '';
        $_OMP_html['list_item_text'] = '';
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[84]."\";");
        $_OMP_html['list_item_value'] = "1";
        $_OMP_html['list_item_text'] = $_OMP_LC[98];
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
        $_OMP_html['list_item_value'] = "0";
        $_OMP_html['list_item_text'] = $_OMP_LC[99];
        eval("\$_OMP_html['select_list'] .= \"".$_OMP_TPL[83]."\";");
    }
    $_OMP_html['select_width'] = OMP_DDL_WIDTH_SML;
    $_OMP_html['select_name'] = 'form['.$index.']';
    $_OMP_html['select_label'] =
        $_OMP_html['select_aria_label'] = $_OMP_LC[$label];
    /* required by TPL 80, 81, 82 and 87 */
    $_OMP_html['select_id'] = $field;
    $_OMP_html['onchange'] = '';
    if ($required && $preselected) {
        eval("\$html_control = \"".$_OMP_TPL[82]."\";");
    } elseif ($required) {
        eval("\$html_control = \"".$_OMP_TPL[81]."\";");
    } elseif ($disabled) {
        eval("\$html_control = \"".$_OMP_TPL[86]."\";");
    } else {
        /* also for $preselected */
        eval("\$html_control = \"".$_OMP_TPL[80]."\";");
    }
    return $html_control;
}
if (isset($_OMP_html['printed_combo'])) {
    $_OMP_html['printed_combo'] =
        OMP_mdcSelectYesNo('printed', 'bol_printed', 711);
}
if (isset($_OMP_html['amended_combo'])) {
    $_OMP_html['amended_combo'] =
        OMP_mdcSelectYesNo('amended', 'bol_amended', 712);
}
if (isset($_OMP_html['closed_combo'])) {
    $_OMP_html['closed_combo'] =
        OMP_mdcSelectYesNo('closed', 'bol_closed', 713);
}
if (isset($_OMP_html['cancelled_combo'])) {
    $_OMP_html['cancelled_combo'] =
        OMP_mdcSelectYesNo('cancelled', 'bol_cancelled', 715);
}
if (isset($_OMP_html['active_combo'])) {
    $_OMP_html['active_combo'] =
        OMP_mdcSelectYesNo('active', 'bol_active', 119);
}
if (isset($_OMP_html['constock_combo'])) {
    $_OMP_html['constock_combo'] =
        OMP_mdcSelectYesNo('constock', 'bol_constock', 122);
}
if (isset($_OMP_html['monthly_combo'])) {
    $_OMP_html['monthly_combo'] =
        OMP_mdcSelectYesNo('monthly', 'bol_monthly', 614);
}
$_OMP_html['switch_script'] = '';
?>
