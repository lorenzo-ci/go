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
// | See the GNU General Public License                                   |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the                        |
// | Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston,  |
// | MA 02111-1307 USA                                                    |
// +----------------------------------------------------------------------+
// | Author: Lorenzo Ciani <lorenzo@ciani.it>                             |
// +----------------------------------------------------------------------+
//
// $Id: user.php,v 0.9 $
//
// User preferences
//

require_once 'base.php';
/* Database schema */
require_once 'schemas/db-schema.php';
/* Name of current master-table */
$_OMP_tbl = 'users';
/* Language schema */
require_once 'schemas/language-schema.php';
/* SQL for drop-down lists */
$_OMP_get['popup'] or require_once 'lib/dd-sql.php';
/* Users schema */
require_once 'schemas/users-schema.php';
/* Table fields, length and keys */
$_OMP_tbl_fld = $_OMP_users_fld;
$_OMP_fld_len = $_OMP_users_len;
$_OMP_tbl_key = $_OMP_users_key;

/**
* SQL code
*/
$_OMP_sql['select'] = 'SELECT '.$_OMP_tbl.'.'.
    $_OMP_tbl_fld['pkey'].' AS pkey, '.
    $_OMP_tbl.'.'.
    $_OMP_tbl_fld['password'].', '.
    $_OMP_tbl.'.'.
    $_OMP_tbl_fld['locale'].', language.'.
    $_OMP_language_fld['name'].' AS language, '.
    $_OMP_tbl.'.'.
    $_OMP_tbl_fld['maxlist'].', '.
    $_OMP_tbl.'.'.
    $_OMP_tbl_fld['country'].', '.
    $_OMP_tbl.'.'.
    $_OMP_tbl_fld['ddl_width'].', '.
    $_OMP_tbl.'.'.
    $_OMP_tbl_fld['cat'].', '.
    $_OMP_tbl.'.'.
    $_OMP_tbl_fld['css'].' FROM '.
    $_OMP_tables[$_OMP_tbl].' AS '.
    $_OMP_tbl.' LEFT JOIN '.
    $_OMP_tables['language'].' ON ('.
    $_OMP_tbl.'.'.
    $_OMP_tbl_fld['locale'].' = '.
    $_OMP_tables['language'].'.'.
    $_OMP_language_fld['pkey'].')';
$_OMP_sql['row'] = $_OMP_sql['select'].' WHERE '.
    $_OMP_tbl.'.'.
    $_OMP_tbl_fld['pkey'].' = ?';
/**
* End SQL code
*/

/**
* Function definitions
*/
/**
* Process the array $GLOBALS['_OMP_rec']
*
*/
function OMP_makeVars()
{
    return;
}

/**
* Make HTML code for drop-down list
* @param string $combo_name name of select control
* @param array $my_array label/value pairs in the list
* @param string $selected list element to be pre-selected
* @return string with HTML code
*/
function OMP_combo($combo_name, $my_array, $selected = '', $disabled)
{
    $disabled = ($disabled) ? 'disabled' : '';
    $combo = '<select name="'.
        $combo_name.'" id="'.
        $combo_name.'"'.
        $disabled.'>';
    foreach ($my_array as $value) {
        $combo .= '<option ';
        if ($selected != '' && $selected == $value[0]) {
            $combo .= 'selected="selected" ';
        }
        $combo .= 'value="'.$value[0].'">';
        $combo .= $value[1].'</option>';
    }
    $combo .= '</select>';
    return $combo;
}
/**
* End functions
*/
$_OMP_table_alias = 'users.'; // See makeSql
switch ($_OMP_get['action']) {
/**
* Read record
*/
case 'read': {
    $_OMP_tpl = OMP_TPL_READ.'9, 61, 62, 2000';
    $_OMP_lcl = OMP_LCL_READ.'2000, 2001,
        2002, 2003, 2004, 2005, 2006, 2007';
    $_OMP_sql['sort_default'] = ' ORDER BY '.
        $_OMP_tbl_fld['pkey'];
    $_OMP_sql['sort_record'][0] =
        $_OMP_sql['sort_list'][0] =
        $_OMP_sql['sort_default'];
    $_OMP_sql['sort_record'][1] =
        $_OMP_sql['sort_list'][1] =
        ' ORDER BY .'.$_OMP_tbl_fld['pkey'].' ?';
    $_OMP_sql['sort_record'][2] =
        $_OMP_sql['sort_list'][2] =
        ' ORDER BY '.$_OMP_tbl_fld['pkey'];
    $_OMP_sort_idx = array(2001, 2002);
    if ($_OMP_get['list']) {
        $_OMP_list_tpl = 60;
        $_OMP_list_header_tpl = 61;
        $_OMP_list_rec_tpl = 62;
        /* mdc table */
        // or mdc-data-table__header-cell--numeric"
        $_OMP_html['header1_numeric'] = '';
        $_OMP_html['header2_numeric'] = '';
    } else {
        $_OMP_rec_tpl = 2000;
        $_OMP_headline = 2001;
//            $_OMP_LC[2001].': '.$_OMP_rec['pkey'];
    }
    $_OMP_title = 2000;
    $_OMP_sort['default'] = '0'; // Default sort
    $_OMP_sort['type'] = 0; // Default sort order
    require 'lib/read.php';
    break;
}
/**
* End read record
*/

/**
* Edit record
*/
case 'edit': {
    $_OMP_tpl = OMP_TPL_EDIT.', 2001';
    $_OMP_lcl = OMP_LCL_EDIT.'2000, 2001,
        2002, 2003, 2004, 2005, 2006, 2007';

    // Check if insert-button was pushed
    if (isset($_POST['insert_button'])) {
        $_OMP_sql['update'] = 'UPDATE '.
            $_OMP_tables[$_OMP_tbl].' SET '.
            $_OMP_tbl_fld['pkey'].' = ?, '.
            $_OMP_tbl_fld['locale'].' = ? '.
            // $_OMP_tbl_fld['maxlist'].' = ?, '.
            // $_OMP_tbl_fld['ddl_width'].' = ?, '.
            // $_OMP_tbl_fld['css'].' = ? '.
            'WHERE '.
            $_OMP_tbl_fld['pkey'].' = ?';
        $_OMP_datatypes = array(
            'text',
            'text',
            // 'integer',
            // 'integer',
            // 'text',
            'text'
        );
        $_SESSION['locale'] = $_POST['form']['txt_locale'];
        $_SESSION['LC'] = substr($_SESSION['locale'], 0, 2);
        setlocale(LC_ALL, $_SESSION['locale']);
    } else {
        $_OMP_datatypes = array('text');
    //    $_OMP_combo_required = true;
    //    $_OMP_drop_down = array('lib/mdc-select-clients.php');
        $lang_rec = OMP_languages();
        $tmp = array();
        foreach ($lang_rec as $lang) {
            $tmp[] = array_values($lang);
        }
        $locale_combo =
            OMP_combo('form[txt_locale]', $tmp, $_SESSION['locale'], false);
        /* URI for FORM action */
        $url = OMP_PATH_SCRIPT.'?update=1';
        $_OMP_input_tpl = 2001;
        $_OMP_edit_tpl = 51;
        $_OMP_page_title_lcl = 2000;
    }
    require 'lib/edit.php';
    break;
}
/**
* End edit record
*/
}
// if (isset($_GET['update']) && $_GET['update'] == '1') {
//     // Update user profile
//     $_OMP_tmp_a = 'UPDATE profile SET locale = ?, ';
//     $_OMP_tmp_a .= 'maxlist = ?, ddl_width = ?, ';
//     $_OMP_tmp_a .= 'css = ? WHERE username = ?';
//     $_OMP_tmp_b = array_values($_POST['form']);
//     $_OMP_tmp_b[] = $_SESSION['id'];
//     $_OMP_datatypes =
//         array('text', 'integer', 'integer', 'text', 'text');
//     $_OMP_prepared =
//         $_OMP_db->prepare($_OMP_tmp_a, $_OMP_datatypes);
//     $_OMP_db_result = $_OMP_prepared->execute($_OMP_tmp_b);
//     $_OMP_prepared->free();
//     $_SESSION['locale'] = $_POST['form']['locale'];
//     $_SESSION['LC'] = substr($_SESSION['locale'], 0, 2);
//     setlocale(LC_ALL, $_SESSION['locale']);
//     $_OMP_tmp_a = localeconv();
//     $_SESSION['ts'] = $_OMP_tmp_a["mon_thousands_sep"];
//     $_SESSION['dp'] = $_OMP_tmp_a["mon_decimal_point"];
//     $_SESSION['maxlist'] = $_POST['form']['maxlist'];
//     $_SESSION['ddl_width'] = $_POST['form']['ddl_width'];
//     $_SESSION['css'] = $_POST['form']['css'];
//     $_OMP_tmp_a = OMP_PATH.'index.php';
//     OMP_redirPage(
//         '4',
//         $_OMP_tmp_a,
//         $_OMP_LC[2007],
//         $_OMP_LC[2007]
//     );
// } else {
//     // Size of pop-up windows, see conf.php
//     $lang_rec = OMP_languages();
//     // Language drop-down list
//     // We need to remove the column names from $lang_rec
//     // or OMP_combo() won't use it
//     // array_values does not travel through nested arrays
//     // Is there a PHP function that does this?
//     $tmp = array();
//     foreach ($lang_rec as $lang) {
//         $tmp[] = array_values($lang);
//     }
//     $locale_combo =
//         OMP_combo('form[locale]', $tmp, $_SESSION['locale'], false);
//     // Max number of lines in listings
//     $tmp = array();
//     $tmp[0] = array('10', '10 lines');
//     $tmp[1] = array('15', '15 lines');
//     $tmp[2] = array('20', '20 lines');
//     $tmp[3] = array('30', '30 lines');
//     $maxlist_combo =
//         OMP_combo('form[maxlist]', $tmp, $_SESSION['maxlist'], false);
//     // Size of combo list...combo!
//     $tmp = array();
//     $tmp[0] = array('20', str_pad('Small', 20, '.'));
//     $tmp[1] = array('30', str_pad('Medium', 30, '.'));
//     $tmp[2] = array('40', str_pad('Large', 40, '.'));
//     $ddl_width_combo =
//         OMP_combo(
//             'form[ddl_width]',
//             $tmp,
//             $_SESSION['ddl_width'],
//             true
//         );
//     // Cascading Style Sheet drop-down list
//     $tmp = array();
//     $tmp[0] = array('css/def.css', 'Default');
//     $tmp[1] = array('css/tone.css', 'Tone');
//     $css_combo =
//         OMP_combo('form[css]', $tmp, $_SESSION['css'], true);
//     $_OMP_html['page_title'] =
//         $_OMP_html['browser_title'] =
//         $_OMP_LC[2000];
//     /* Set $_OMP_html['buttons'] */
//     OMP_buttons(
//         '14',
//         '38',
//         '37',
//         'javascript:history.back();return true;'
//     );
//     /* URI for FORM action */
//     $url = OMP_PATH_SCRIPT.'?update=1';
//     eval ("\$_OMP_html['include'] = \"".$_OMP_TPL[2000]."\";");
// }
// /* mdc-toolbar */
// $_OMP_html['toolbar'] = '';
// eval ("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
// OMP_lose();
?>
