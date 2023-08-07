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
// $Id: del.php,v 0.8 $
//
// Delete record
//

OMP_load();
// Check if insert-button was pushed
if (isset($_POST['insert_button'])) {
    $_OMP_post = unserialize(base64_decode($_POST['post_to_get']));
    $_OMP_prepared = $_OMP_db->prepare($_OMP_sql['delete'], $_OMP_datatypes);
    $_OMP_result = $_OMP_prepared->execute(OMP_keyCheck($_OMP_post));
    $_OMP_result->free();
    $_OMP_prepared->free();
    $_OMP_post = OMP_keyUnset($_OMP_post, $_OMP_tbl_key);
    // URL for redirection in template number 4
    if (isset($_OMP_post['index'])) {
        // we deleted the only record we just added
        $_OMP_tmp_a = OMP_redirectUrl('', true, false, 'index.php');
    } else {
        $_OMP_tmp_a =
            OMP_redirectUrl(OMP_makeGet($_OMP_post), true, false);
    }
    $_OMP_tmp_b = 'Record '.$_OMP_LC[44]; // Record deleted
    OMP_redirPage('4', $_OMP_tmp_a, $_OMP_tmp_b, $_OMP_tmp_b);
} else { // Show record to be deleted
    $_OMP_tmp_a = OMP_keyCheck($_OMP_get);
    $_OMP_prepared = $_OMP_db->prepare($_OMP_sql['delete_pre'], $_OMP_datatypes);
    $_OMP_result = $_OMP_prepared->execute($_OMP_tmp_a);
    $_OMP_result->numRows() or // Record not found
        OMP_genErr($_OMP_LC[83],'Table: '.$_OMP_tables[$_OMP_tbl]);
    $_OMP_rec = $_OMP_result->fetchRow();
    $_OMP_result->free();
    $_OMP_prepared->free();
    OMP_makeVars(); // makes variables for HTML form
    // Check to see if we are deleting the last record
    $_OMP_tmp_a = str_replace($_OMP_table_alias, '', $_OMP_get['where']);
    if (1 == OMP_sqlCount($_OMP_tmp_a)) {
        if ($_OMP_get['filter']) {
            unset($_OMP_get['filter']);
            unset($_OMP_get['where']);
        } else {
            // are we deleting the only record that we just added?
            if ($_OMP_get['read_new']) { unset($_OMP_get['read_new']); }
            $_OMP_get['index'] = '1';
        }
    }
    // See ordersnotes.php
    if (!empty($_OMP_cannot_filter)) {
        $_OMP_get['list'] = 1;
        unset ($_OMP_get['row_num']);
    }
    // Hidden form field with md5 hash
    $_OMP_post = OMP_makeHidden($_OMP_get);
    OMP_buttons('14', '22', '37', 'javascript:history.back();return true');
    // URI for FORM action
    $_OMP_url = OMP_link('action=del');
    // Process and print templates
    eval("\$_OMP_html['record'] =
        \"".$_OMP_TPL[$_OMP_del_tpl]."\";");
    eval("\$_OMP_html['include'] =
        \"".$_OMP_TPL[$_OMP_conf['delete_template']]."\";");

    // eval("\$_OMP_html['include'] = \"".$_OMP_TPL[$_OMP_del_tpl]."\";");
    $_OMP_html['browser_title'] = $_OMP_LC[22];
    $_OMP_html['page_title'] = $_OMP_LC[$_OMP_page_title_lcl].
        ' - '.$_OMP_LC[22];
    /* this doesn't fit in page_title anymore */
    //.$_OMP_LC[46];
    // eval ("\$_OMP_html['menu'] = \"".$_OMP_TPL[2]."\";");
    /* mdc-toolbar mdc-drawer button */
    $_OMP_html['toolbar'] = $_OMP_html['menu_button'] = '';
}
eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
OMP_lose();
?>
