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
// $Id: dd-ranges.php,v 0.8 $
//
// Combo box with range records
//

isset($_OMP_rec['range_pkey']) or $_OMP_rec['range_pkey'] = '';
isset($_OMP_prefilter) or $_OMP_prefilter = ($_OMP_rec['range_pkey'] == '');
$_OMP_ranges_combo = '<select name="form[num_range_pkey]" id="range_pkey"';
!$_OMP_combo_required or $_OMP_ranges_combo .= ' required'; // see filter.php
$_OMP_ranges_combo .= '>';
!$_OMP_prefilter or 
    $_OMP_ranges_combo .=
        '<option value=\'\'>'.$_OMP_LC['29'].'</option>';
$_OMP_ranges_query = $_OMP_db->query($_OMP_sql['ranges_combo']);
while ($_OMP_ranges_rec = $_OMP_ranges_query->fetchRow()) {
    $_OMP_ranges_rec['name'] = OMP_htmlentities($_OMP_ranges_rec['name']);
    $_OMP_ranges_combo .= '<option ';
    ($_OMP_ranges_rec['pkey'] != $_OMP_rec['range_pkey']) or
        $_OMP_ranges_combo .= 'selected="selected" ';
    $_OMP_ranges_combo .= 'value="'.$_OMP_ranges_rec['pkey'].'">'.
        ((strlen($_OMP_ranges_rec['name']) > $_SESSION['ddl_width']) ? 
            substr($_OMP_ranges_rec['name'], 0, $_SESSION['ddl_width'] - 3).
            '...' : $_OMP_ranges_rec['name']);
    $_OMP_ranges_combo .= '</option>';
}
$_OMP_ranges_combo .= '</select>';
$_OMP_ranges_query->free();
unset($_OMP_ranges_rec);
?>
