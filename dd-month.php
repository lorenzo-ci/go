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
// $_OMP_tmp_ad: dd-month.php,v 0.7 $
//
// Combo box with list of months
//

isset($_OMP_rec_sf['month']) or $_OMP_rec_sf['month'] = '';
isset($_OMP_prefilter) or $_OMP_prefilter = ($_OMP_rec_sf['month'] == '');
!empty($_OMP_month_combo) or 
    $_OMP_month_combo =
        '<select name="form[txt_month]" id="form[txt_month]"';
!$_OMP_combo_required or $_OMP_month_combo .= ' required'; // see filter.php
$_OMP_month_combo .= '>';
!$_OMP_prefilter or 
    $_OMP_month_combo .=
        '<option value=\'\'>'.$_OMP_LC['29'].'</option> ';
for ($_OMP_tmp_a = 1; $_OMP_tmp_a < 13; $_OMP_tmp_a++) {
    $_OMP_month_combo .= '<option ';
    // $_OMP_rec_sf['month'] is set in edit-line.php and in new-line.php
    ($_OMP_tmp_a != $_OMP_rec_sf['month'])  or 
        $_OMP_month_combo .= 'selected="selected" ';
    $_OMP_month_combo .= 'value="'.$_OMP_tmp_a.'">'.
        htmlentities($_OMP_LC[$_OMP_tmp_a + 69]).'</option>';
}
$_OMP_month_combo .= '</select>';
?>
