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
// $Id: del-line.php,v 0.7 $
//
// Delete line record
//

/**
* @return array
* @param array $src
* @param array $in
* @param int|string $pos
*/
function array_push_before($src,$in,$pos) {
    if(is_int($pos)) $R=array_merge(array_slice($src,0,$pos), $in, array_slice($src,$pos));
    else{
        foreach($src as $k=>$v){
            if($k==$pos)$R=array_merge($R,$in);
            $R[$k]=$v;
        }
    }return $R;
}

// Check if insert-button was pushed
if (isset($_POST['insert_button'])) {
    $_OMP_post = unserialize(base64_decode($_POST['post_to_get']));
    $_OMP_tmp_a = OMP_keyCheck($_OMP_post, true);
    $_OMP_prepared = $_OMP_db->prepare($_OMP_sql['delete_line'], $_OMP_datatypes);
    $_OMP_result = $_OMP_prepared->execute($_OMP_tmp_a);
    $_OMP_result->free();
    $_OMP_prepared->free();
    // URL for redirection in template number 4
    $_OMP_tmp_a = OMP_redirectUrl(OMP_makeGet($_OMP_post), true, false);
    $_OMP_tmp_b = 'Record '.$_OMP_LC[44]; // Record deleted
    OMP_redirPage('4', $_OMP_tmp_a, $_OMP_tmp_b, $_OMP_tmp_b);
} else {
//     if (isset($tbc_pkey)) {
//         $_OMP_tmp_a['kl_tl_pkey'] = $_OMP_get['pk_pkey'];
//         $_OMP_tmp_a['lh_tl_pkey'] = $_OMP_get['kh_pkey'];
//         $_OMP_get = array_push_before($_OMP_get, $_OMP_tmp_a, 0);
//     }
    $_OMP_tmp_a = OMP_keyCheck($_OMP_get, true);
    $_OMP_prepared = $_OMP_db->prepare($_OMP_sql['delete_pre_line'], $_OMP_datatypes);
    $_OMP_result = $_OMP_prepared->execute($_OMP_tmp_a);
    if (PEAR::isError($_OMP_result)) {
        OMP_genErr($_OMP_LC[83], 'Table: '.$_OMP_tables[$tbl_line]);
    }
    $_OMP_rec_sf = $_OMP_result->fetchRow();
    $_OMP_result->free();
    $_OMP_prepared->free();
    OMP_makeVarsLine();
    $_OMP_html['browser_title'] = $_OMP_LC[22];
    // Hidden form field with md5 hash
    $_OMP_post = OMP_makeHidden($_OMP_get);
    eval("\$_OMP_list_rec = \"".$_OMP_TPL[$_OMP_sub_header_tpl]."\";");
    eval("\$_OMP_list_rec .= \"".$_OMP_TPL[$_OMP_rec_tpl]."\";");
    // URL for form action
    $_OMP_url = OMP_link('action=delline');
    OMP_buttons('14', '22', '37', 'javascript:history.back();return true');
    $_OMP_html['record'] = '';
    eval("\$_OMP_html['details'] = \"".$_OMP_TPL[$_OMP_include_tpl]."\";");
    if ($_OMP_tbl == 'tbcs') {
        $_OMP_rec_sf['master_pkey'] = $_OMP_rec_sf['prod_pkey'];
        $_OMP_rec_sf['master_date'] = $_OMP_rec_sf['eta'];
    }
    $_OMP_html['headline'] =
        $_OMP_LC[$_OMP_headline].' '.
        $_OMP_rec_sf['master_pkey'].' '.
        $_OMP_LC[52].' '.
        $_OMP_rec_sf['master_date'];
    eval ("\$_OMP_html['record_title'] = \"".$_OMP_TPL[48]."\";");
    eval("\$_OMP_html['include'] = \"".
        $_OMP_TPL[$_OMP_conf['record_template']]."\";");
    /* mdc-toolbar mdc-drawer button */
    $_OMP_html['toolbar'] = $_OMP_html['menu_button'] = '';
}
eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
OMP_lose();
?>
