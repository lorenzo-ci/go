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
// $Id: dd-terms.php,v 0.7 $
//
// Combo box with payment terms records
//

isset($_OMP_rec['term_pkey']) or $_OMP_rec['term_pkey'] = '';
isset($_OMP_combo_required) or $_OMP_combo_required = false;
$_OMP_prefilter = ($_OMP_rec['term_pkey'] == '');
$_OMP_term_combo =
    '<select id="term_pkey" name="form[int_term_pkey]"';
/* see filter.php */
!$_OMP_combo_required or $_OMP_term_combo .= ' required';
$_OMP_term_combo .= '>'."\n";
!$_OMP_prefilter or $_OMP_term_combo .=
    '       <option value=\'\'>'.$_OMP_LC['29'].'</option>';
$_OMP_term_query = $_OMP_db->query($_OMP_sql['term_combo']);
while ($_OMP_term_rec = $_OMP_term_query->fetchRow()) {
    $_OMP_term_rec = array_map('htmlentities', $_OMP_term_rec);
    $_OMP_term_combo .= "\n".'       <option ';
    ($_OMP_term_rec['pkey'] != $_OMP_rec['term_pkey']) or 
        $_OMP_term_combo .= 'selected="selected" ';
    $_OMP_tmp_a = ($_SESSION['LC'] == 'it') ? $_OMP_term_rec['description'] : 
        $_OMP_term_rec['name'];
    $_OMP_term_combo .= 'value="'.$_OMP_term_rec['pkey'].'">'.
        ((strlen($_OMP_tmp_a) > $_SESSION['ddl_width']) ? 
            substr($_OMP_tmp_a, 0, $_SESSION['ddl_width'] - 3).
            '...' : $_OMP_tmp_a);
    $_OMP_term_combo .= '</option>';
}
$_OMP_term_combo .= "\n".'      </select>';
$_OMP_term_query->free();
unset($_OMP_term_rec);
?>
