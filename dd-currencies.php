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
// $Id: dd-currencies.php,v 0.7 $
//
// Combo box with currency records and javascript
//

isset($_OMP_rec['currency_pkey']) or $_OMP_rec['currency_pkey'] = '';
$_OMP_prefilter = ($_OMP_rec['currency_pkey'] == '');
isset($_OMP_orders_fld) ?
    $_OMP_currency_combo =
        '<select id="curr" name="form[enc_curr]"' :
    $_OMP_currency_combo =
        '<select id="form[enc_currency_pkey]" name="form[enc_currency_pkey]"';
/* see filter.php */
!$_OMP_combo_required or $_OMP_currency_combo .= ' required';
$_OMP_currency_combo .= '>';
!$_OMP_prefilter or
    $_OMP_currency_combo .=
        "\n".'<option value=\'\'>'.$_OMP_LC['29'].'\&nbsp\;</option>';
$_OMP_cur_query = $_OMP_db->query($_OMP_sql['currencies_combo']);
while ($_OMP_cur_rec = $_OMP_cur_query->fetchRow()) {
    $_OMP_tmp_a = urlencode($_OMP_cur_rec['pkey']);
    $_OMP_cur_rec['pkey'] = OMP_htmlentities($_OMP_cur_rec['pkey']);
    $_OMP_currency_combo .= '<option ';
    ($_OMP_cur_rec['pkey'] != $_OMP_rec['currency_pkey']) or 
        $_OMP_currency_combo .= 'selected="selected" ';
    $_OMP_currency_combo .= 'value="'.$_OMP_tmp_a.'">'.
        ((strlen($_OMP_cur_rec['pkey']) > $_SESSION['ddl_width']) ? 
            substr($_OMP_cur_rec['pkey'], 0, $_SESSION['ddl_width'] - 3).
            '...' : $_OMP_cur_rec['pkey']);
    $_OMP_currency_combo .= '</option>';
}
$_OMP_currency_combo .= '</select>';
$_OMP_cur_query->free();
unset($_OMP_cur_rec);
?>
