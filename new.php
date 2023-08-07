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
// $Id: new.php,v 0.8 $
//
// Add new record
//

/**
* Make string with primary keys based on $_POST['form']
*
* @return string
*/
function OMP_keyMakeRedirect()
{
    global $_OMP_has_subform, $_OMP_tbl_key;
    $getkey = '';
    for (reset($_POST['form']); 
            $key = key($_POST['form']); 
            next($_POST['form'])) {
        $pos = strpos($key, 'date');
        if ($pos !== false || $key == 'eta') {
            $new_key = $key;
        } else {
            $new_key = substr($key, 4);
        }
        foreach ($_OMP_tbl_key as $mykey => $value) {
            if ($value == $new_key) {
                if (empty($_OMP_has_subform)) {
                    $getkey .= '&'.$new_key.'='.
                        urlencode($_POST['form'][$key]);
                } else {
                    $getkey .= '&pk_'.$new_key.'='.
                        urlencode($_POST['form'][$key]);
                    $getkey .= '&kh_'.$new_key.'='.
                        md5(OMP_SECRET.$_POST['form'][$key]);
                }
            }
        }
    }
    return $getkey;
}

/**
* Make $_OMP_rec array for edit and insert forms
*
*/
function OMP_initRecord()
{
    global $_OMP_rec, $_OMP_tbl_fld;
    if (empty($_POST['form'])) {
        for (reset($_OMP_tbl_fld); 
                $key = key($_OMP_tbl_fld); 
                next($_OMP_tbl_fld)) {
            isset($_OMP_rec[$key]) or $_OMP_rec[$key] = '';
        }
    } else {
        for (reset($_POST['form']); 
                $key = key($_POST['form']); 
                next($_POST['form'])) {
            $type = substr($key, 0, 4);
            if ($key == 'date' || $key == 'eta') {
                $new_key = $key;
            } else {
                $new_key = substr($key, 4);
            }
            if ($type == 'enc_') {
                $_OMP_rec[$new_key] = urldecode($_POST['form'][$key]);
            } else {
                $_OMP_rec[$new_key] = $_POST['form'][$key];
            }
        }
    }
}
OMP_load(); // @see base.php
// Check if insert-button was pushed
if (isset($_POST['insert_button'])) { // Insert new record
    $_OMP_data = OMP_makeData($_OMP_sql['insert']); // @see functions.php
// var_dump($_OMP_data);
// echo 'sql '.$_OMP_sql['insert'];
// var_dump($_OMP_datatypes);
// OMP_lose_no_html();
    $_OMP_prepared = $_OMP_db->prepare($_OMP_sql['insert'], $_OMP_datatypes);
    $_OMP_db_result = $_OMP_prepared->execute($_OMP_data);
    $_OMP_prepared->free();
    $_OMP_tmp_b = empty($_OMP_has_subform) ? '' : '&action=newdetail';
    if (isset($_OMP_sql['get_last'])) {
        // $_OMP_rec is used by OMP_keyMake()
        $_OMP_rec = $_OMP_db->queryRow($_OMP_sql['get_last'].
            OMP_db_quote($_SESSION['id'], 'text'));
        if (PEAR::isError($_OMP_rec)) { // Record not found
            OMP_genErr($_OMP_LC[83], 'Table: '.$_OMP_tables[$_OMP_tbl]);
        }
        $_OMP_tmp_a = empty($_OMP_has_subform);
        $_OMP_tmp_b .= OMP_keyMake($_OMP_tmp_a, $_OMP_tmp_a);
    } else {
            $_OMP_tmp_b .= OMP_keyMakeRedirect();
    }
    if (isset($_OMP_post)) { // See ordersnotes.php, go back to list
        $_OMP_tmp_b = 'pkey='.$_OMP_post['pkey'].'&filter=1&list=1&popup=1';
    } else {
        $_OMP_tmp_b = 'read_new=1'.$_OMP_tmp_b;  // Go to record just added
    }
    // Redirection in template number 4.
    $_OMP_tmp_a = OMP_redirectUrl($_OMP_tmp_b, true, false);
    // Record Added
    OMP_redirPage('4', $_OMP_tmp_a, $_OMP_LC[45], $_OMP_LC[45]);
    // End insert new record
} else { // Show new record form
    // URL for FORM action
    $_OMP_url = OMP_link('action='.$_OMP_get['action']);
    $_OMP_tmp_a = 'javascript:history.back();return true';
    // $_OMP_tmp_a = (isset($_OMP_get['empty_table'])) ?
    //     'javascript:history.back();return true;' :
    //     'javascript:window.location=\''.OMP_redirectUrl().'\';return true;';
    /* @see base.php */
    OMP_buttons('14', '39', '37', $_OMP_tmp_a);
    OMP_initRecord();
// var_dump($_OMP_rec);
    OMP_makeVars();
// var_dump($_OMP_rec); OMP_lose_no_html();
    /* Requires eventual drop-down lists */
    if (isset($_OMP_drop_down)) {
        foreach($_OMP_drop_down as $_OMP_tmp_a) {
            $_OMP_prefilter = true;
            require $_OMP_tmp_a;
        }
    }
    require 'lib/set-switches.php';
    /* For !empty($_OMP_post) see ordersnotes.php */
    !empty($_OMP_post) or $_OMP_post = '';
    eval("\$_OMP_html['input'] = \"".$_OMP_TPL[$_OMP_input_tpl]."\";");
    eval("\$_OMP_html['include'] = \"".$_OMP_TPL[$_OMP_include_tpl]."\";");
    $_OMP_html['browser_title'] = $_OMP_html['page_title'] =
        $_OMP_LC[$_OMP_page_title_lcl].' - '.
        $_OMP_LC[21];
    /* mdc-toolbar mdc-drawer button */
    $_OMP_html['toolbar'] = $_OMP_html['menu_button'] =
        $_OMP_html['logo'] = '';
}
eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
OMP_lose();
?>
