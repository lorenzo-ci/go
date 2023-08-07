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
// $Id: dd-supplierscontacts.php,v 0.8 $
//
// Combo box with suppliers contacts records and javascript
//

// isset($_OMP_rec['supplier_pkey']) or $_OMP_rec['supplier_pkey'] = '';
isset($_OMP_prefilter) or $_OMP_prefilter = ($_OMP_rec['supplier_pkey'] == '');
if (empty($_OMP_combo)) {
    $_OMP_combo = '<select name="att" id="att"';
    $_OMP_tmp_b = $_OMP_fav_supplier['to'];
} else {
    $_OMP_combo = '<select name="cc" id="cc"';
    $_OMP_tmp_b = $_OMP_fav_supplier['cc'];
}
!isset($_OMP_combo_required) or $_OMP_combo .= 'required'; // see filter.php
empty($_OMP_onchange) or 
    $_OMP_combo .= ' onchange="javascript:this.form.submit();return true"';
$_OMP_combo .= '>';
if ($_OMP_prefilter) { $_OMP_combo .=
    '<option value=\'\'>'.$_OMP_LC['29'].'</option>'; }
$_OMP_att_query = $_OMP_db->query($_OMP_sql['att_combo']);
while ($_OMP_att_rec = $_OMP_att_query->fetchRow()) {
//     $_OMP_tmp_a = urlencode($_OMP_att_rec['pkey']);
    $_OMP_tmp_a = OMP_htmlentities($_OMP_att_rec['fname'].' '. $_OMP_att_rec['lname']);
    $_OMP_combo .= '<option ';
    ($_OMP_att_rec['pkey'] != $_OMP_tmp_b) or $_OMP_combo .= 'selected="selected" ';
//     (empty($_OMP_att_combo) && $_OMP_tmp_a == 0) or $_OMP_combo .= 'selected="selected" ';
    $_OMP_combo .= 'value="'.$_OMP_tmp_a.'">'.
        ((strlen($_OMP_tmp_a) > $_SESSION['ddl_width']) ? 
            substr($_OMP_tmp_a, 0, $_SESSION['ddl_width'] - 3).'...' : 
            $_OMP_tmp_a);
    $_OMP_combo .= '</option>';
}
$_OMP_combo .= '</select>';
$_OMP_att_query->free();
unset($_OMP_att_rec);
empty($_OMP_att_combo) ? $_OMP_att_combo = $_OMP_combo : $_OMP_cc_combo = $_OMP_combo;
?>
