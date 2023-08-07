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
// $Id: filter.php,v 0.7 $
//
// Filter records
//
OMP_load();
if (isset($_OMP_drop_down)) {
    /* Requires drop-down lists */
    foreach($_OMP_drop_down as $_OMP_tmp_a) {
        require $_OMP_tmp_a;
    }
}
require 'lib/mdc-select-yesno.php';
if (empty($_OMP_page_title_lcl)) {
    $_OMP_html['page_title'] = '';
} else {
    $_OMP_html['page_title'] = $_OMP_LC[$_OMP_page_title_lcl].' - ';
}
if ($_OMP_get['action'] == 'recptsel') {
    $_OMP_html['page_title'] .= $_OMP_LC[26];
    OMP_buttons('14', '26', '37', 'javascript:window.close();');
    // URI for FORM action
    $_OMP_url = OMP_link('action=print&filter='.$_OMP_get['filter']);
    $_OMP_html['menu_button'] = '';
    $_OMP_html['menu'] = '';
} else {
    // $_OMP_combo_required = true;
    $_OMP_html['page_title'] .= $_OMP_LC[20];
    OMP_buttons('14', '36', '37', 'javascript:history.back();return true;');
    /* 'javascript:window.location = \''.OMP_PATH_SCRIPT.'\';return true;' */
    // URI for FORM action
    $_OMP_url = OMP_link('filter=1&list=1');
}
$_OMP_html['browser_title'] = $_OMP_html['page_title'];
$_OMP_html['logo'] = '';
// Process and print templates
eval("\$_OMP_html['record'] =
    \"".$_OMP_TPL[$_OMP_include_tpl]."\";");
eval("\$_OMP_html['include'] =
    \"".$_OMP_TPL[$_OMP_conf['filter_template']]."\";");
eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
OMP_lose();
?>
