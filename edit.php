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
// $Id: edit.php,v 0.9 $
//
// Edit record
//

OMP_load();
// Check if insert-button was pushed
if (isset($_POST['insert_button'])) {
    $_OMP_post = unserialize(base64_decode($_POST['post_to_get']));
    /* Make array $_OMP_data with $_POST variables.
     * See functions.php */
    $_OMP_data = OMP_makeData($_OMP_sql['update']);
    $_OMP_data = OMP_keyCheck($_OMP_post, false, $_OMP_data);
    $_OMP_prepared = $_OMP_db->prepare($_OMP_sql['update'], $_OMP_datatypes);
    $_OMP_db_result = $_OMP_prepared->execute($_OMP_data);
    unset($_OMP_data);
    $_OMP_prepared->free();
    $_OMP_post = OMP_keyUnset($_OMP_post, $_OMP_tbl_key);
    // Redirection in template number 4
    $_OMP_tmp_a = OMP_redirectUrl(OMP_makeGet($_OMP_post), true, false);
    $_OMP_tmp_b = 'Record '.$_OMP_LC[40]; // Record updated
    OMP_redirPage('4', $_OMP_tmp_a, $_OMP_tmp_b, $_OMP_tmp_b);
} else {
    $_OMP_data = OMP_keyCheck($_OMP_get);
    $_OMP_prepared = $_OMP_db->prepare($_OMP_sql['row'], $_OMP_datatypes);
    $_OMP_db_result = $_OMP_prepared->execute($_OMP_data);
    $_OMP_rec = $_OMP_db_result->fetchRow();
    unset($_OMP_data);
    $_OMP_prepared->free();
    if (!is_array($_OMP_rec)) { // Record not found
        OMP_genErr($_OMP_LC[83],'Table: '.$_OMP_tables[$_OMP_tbl]);
    }
    OMP_makeVars(); // makes variables for HTML form
    if (isset($_OMP_drop_down)) { // Requires drop-down lists
        foreach($_OMP_drop_down as $_OMP_tmp_a) {
            require $_OMP_tmp_a;
        }
    }
    unset($_OMP_get['action']);
    $_OMP_post = OMP_makeHidden($_OMP_get);
    OMP_buttons('14', '38', '37', 'javascript:history.back();return true');
    // URI for FORM action
    $_OMP_url = OMP_link('action=edit');
    $_OMP_get['action'] = 'edit';
    require 'lib/set-switches.php';
    // if (!empty($_OMP_input_tpl)) {
    //     eval("\$_OMP_html['input'] = \"".$_OMP_TPL[$_OMP_input_tpl]."\";");
    // }
    // eval("\$_OMP_html['record'] =
    //     \"".$_OMP_TPL[$_OMP_edit_tpl]."\";");
    // eval("\$_OMP_html['include'] =
    //     \"".$_OMP_TPL[$_OMP_conf['record_template']]."\";");
    if (!empty($_OMP_input_tpl)) {
        eval(
            "\$_OMP_html['record'] = \"".
            $_OMP_TPL[$_OMP_input_tpl]."\";"
        );
    }
    eval(
        "\$_OMP_html['input'] = \"".
        $_OMP_TPL[$_OMP_conf['record_template']]."\";"
    );
    eval(
        "\$_OMP_html['include'] = \"".
        $_OMP_TPL[$_OMP_edit_tpl]."\";"
    );
    // Form focus
    $_OMP_onload = '';
    $_OMP_html['browser_title'] = $_OMP_html['page_title'] =
        $_OMP_LC[$_OMP_page_title_lcl].' - '.$_OMP_LC[33];
    /* mdc-toolbar mdc-drawer button */
    $_OMP_html['toolbar'] = $_OMP_html['menu_button'] =
        $_OMP_html['logo'] = '';
}
$_OMP_db_result->free();
eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
OMP_lose();
?>
