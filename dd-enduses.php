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
// $Id: dd-enduses.php,v 0.7 $
//
// Combo box with enduse records
//

isset($_OMP_rec['enduse_pkey']) or
    $_OMP_rec['enduse_pkey'] = '';
isset($_OMP_prefilter) or
    $_OMP_prefilter = ($_OMP_rec['enduse_pkey'] == '');
isset($_OMP_combo_required) or
    $_OMP_combo_required = false;
$_OMP_enduse_combo =
    '<select id="enduse_pkey" name="form[enc_enduse_pkey]"';
!$_OMP_combo_required or
    $_OMP_enduse_combo .= ' required'; // see filter.php
$_OMP_enduse_combo .= '>'."\n";
!$_OMP_prefilter or
    $_OMP_enduse_combo .=
        '          <option value=\'\'>'.$_OMP_LC['29'].'</option>';
$_OMP_enduse_query = $_OMP_db->query($_OMP_sql['enduse_combo']);
while ($_OMP_enduse_rec = $_OMP_enduse_query->fetchRow()) {
    $_OMP_enduse_rec = array_map('htmlentities', $_OMP_enduse_rec);
    $_OMP_enduse_combo .= "\n".'          <option ';
    ($_OMP_enduse_rec['pkey'] != $_OMP_rec['enduse_pkey']) or 
        $_OMP_enduse_combo .= 'selected="selected" ';
    $_OMP_tmp_a = $_OMP_enduse_rec['pkey'].' ('.$_OMP_enduse_rec['name'];
    (strlen($_OMP_tmp_a) <= $_SESSION['ddl_width']) or 
        $_OMP_tmp_a = substr($_OMP_tmp_a, 0, $_SESSION['ddl_width'] - 4).
            '...';
    $_OMP_tmp_a .= ')';
    $_OMP_enduse_combo .= 'value="'.$_OMP_enduse_rec['pkey'].'">'.
        $_OMP_tmp_a.'</option>';
}
$_OMP_enduse_combo .= "\n".'         </select>';
$_OMP_enduse_query->free();
unset($_OMP_enduse_rec);
?>
