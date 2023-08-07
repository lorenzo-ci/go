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
// | Author: Lorenzo Ciani <lorenzo@ciani.it>                              |
// +----------------------------------------------------------------------+
//
// $Id: set-switches.php,v 0.2 $
//
// Sets switches in filter and edit forms
//
/**
* Make changes to $_POST
*
*/
function OMP_makeSwitch($label, $switch_id, $input_id, $input_name)
{
    global $_OMP_conf, $_OMP_get, $_OMP_html,
        $_OMP_LC, $_OMP_rec, $_OMP_TPL;
    $_OMP_html['switch_label'] = $_OMP_LC[$label];
    $_OMP_html['switch_id'] = $switch_id;
    $_OMP_html['switch_input_id'] = $input_id;
    $_OMP_html['switch_input_name'] = $input_name;

    if ($_OMP_rec[$switch_id] == 't') {
        $_OMP_html['switch_input_value'] = 1;
        eval("\$_OMP_html[\$input_id] = \"".
            $_OMP_TPL[$_OMP_conf['switch_on']]."\";");
    } else {
        $_OMP_html['switch_input_value'] = 0;
        eval("\$_OMP_html[\$input_id] = \"".
            $_OMP_TPL[$_OMP_conf['switch']]."\";");
    }

    eval("\$_OMP_html['switch_script'] .= \"".
        $_OMP_TPL[$_OMP_conf['switch_script']]."\";");
}

if (isset($_OMP_printed_switch)) {
    /* switch 'active' */
    OMP_makeSwitch(
        $_OMP_printed_switch_locale,
        'printed',
        'switch_printed',
        'bol_printed'
    );
}
if (isset($_OMP_amended_switch)) {
    /* switch 'amended' */
        OMP_makeSwitch(
        $_OMP_amended_switch_locale,
        'amended',
        'switch_amended',
        'bol_amended'
    );
}
if (isset($_OMP_closed_switch)) {
    /* switch 'closed' */
    OMP_makeSwitch(
        $_OMP_closed_switch_locale,
        'closed',
        'switch_closed',
        'bol_closed'
    );
}
if (isset($_OMP_cancelled_switch)) {
    /* switch 'cancelled' */
    OMP_makeSwitch(
        $_OMP_cancelled_switch_locale,
        'cancelled',
        'switch_cancelled',
        'bol_cancelled'
    );
}
if (isset($_OMP_active_switch)) {
    /* switch 'active' */
    OMP_makeSwitch(
        $_OMP_active_switch_locale,
        'active',
        'switch_active',
        'bol_active'
    );
}
if (isset($_OMP_stock_switch)) {
    /* switch 'stock' */
    OMP_makeSwitch(
        $_OMP_stock_switch_locale,
        'constock',
        'switch_stock',
        'bol_constock'
    );
}
if (isset($_OMP_monthly_switch)) {
    /* switch 'stock' */
    OMP_makeSwitch(
        $_OMP_monthly_switch_locale,
        'monthly',
        'switch_monthly',
        'bol_monthly'
    );
}
?>
