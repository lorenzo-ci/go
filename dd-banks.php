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
// $Id: dd-banks.php,v 0.8 $
//
// Combo box with bank records and javascript
//

isset($_OMP_rec['bank_pkey']) or $_OMP_rec['bank_pkey'] = '';
isset($_OMP_combo_required) or $_OMP_combo_required = false;
// See new-line.php
isset($_OMP_prefilter) or 
    $_OMP_prefilter = ($_OMP_rec['bank_pkey'] == '' || 
        $_OMP_rec['bank_pkey'] == 'NULL');
$_OMP_bank_combo =
    '<select id="bank_pkey" name="form[nul_bank_pkey]"';
// disabled in invoices.php add new record
empty($_OMP_bank_combo_disabled) or 
    $_OMP_bank_combo .= ' disabled';
!$_OMP_combo_required or $_OMP_bank_combo .= 'required'; // see filter.php
$_OMP_bank_combo .= '>';
!$_OMP_prefilter or 
    $_OMP_bank_combo .=
        '<option value=\'\'>'.$_OMP_LC['29'].'</option>';
if (empty($_OMP_rec['client_pkey'])) {
//     $_OMP_banks_query = 
    $_OMP_result = 
        $_OMP_db->query($_OMP_sql['banks_combo_no_filter']);
} else {
//     $_OMP_banks_query = 
//         $_OMP_db->query($_OMP_sql['banks_combo'], array($_OMP_rec['client_pkey']));
    //This is unproper, but I know no other way to replace the above
    $prepared = $_OMP_db->prepare($_OMP_sql['banks_combo']);
    $_OMP_result = $prepared->execute($_OMP_rec['client_pkey']);
}
// !empty($_OMP_rec['client_pkey']) or $_OMP_sql['banks_combo'] = $_OMP_sql['banks_combo_no_filter'];
// $_OMP_banks_query = $_OMP_db->query($_OMP_sql['banks_combo'], array($_OMP_rec['client_pkey']));
while ($_OMP_banks_rec = $_OMP_result->fetchRow()) {
    $_OMP_tmp_a = urlencode($_OMP_banks_rec['pkey']);
    $_OMP_banks_rec['pkey'] = OMP_htmlentities($_OMP_banks_rec['pkey']);
    $_OMP_bank_combo .= '<option ';
    ($_OMP_banks_rec['pkey'] != $_OMP_rec['bank_pkey']) or 
        $_OMP_bank_combo .= 'selected="selected" ';
    $_OMP_bank_combo .= 'value="'.$_OMP_tmp_a.'">'.
        ((strlen($_OMP_banks_rec['pkey']) > $_SESSION['ddl_width']) 
            ? substr($_OMP_banks_rec['pkey'], 0, $_SESSION['ddl_width'] - 3).
                '...' 
            : $_OMP_banks_rec['pkey']);
    $_OMP_bank_combo .= '</option>';
}
$_OMP_bank_combo .= '</select>';
$_OMP_result->free();
unset($_OMP_banks_rec);
?>
