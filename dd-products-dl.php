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
// $Id: dd-products-dl.php,v 0.8 $
//
// Combo box with product records for order-line form
//
isset($_OMP_rec_sf['ol_pkey']) or $_OMP_rec_sf['ol_pkey'] = '';
isset($_OMP_prefilter) or 
	$_OMP_prefilter = ($_OMP_rec_sf['ol_pkey'] == '');
isset($_OMP_combo_required) or $_OMP_combo_required = false;
!empty($_OMP_rec_sf['supplier_pkey']) or 
    $_OMP_rec_sf['supplier_pkey'] = '%';
// MDB cannot replace placeholders in products_combo
$_OMP_sql['products_combo'] = str_replace(':client', $_OMP_rec['client_pkey'], 
    $_OMP_sql['products_combo']);
$_OMP_sql['products_combo'] = 
        str_replace(
            ':supplier', 
            $_OMP_rec_sf['supplier_pkey'], 
            $_OMP_sql['products_combo']
        );
$_OMP_sql['products_combo'] = 
    str_replace(':year', $_OMP_rec_orig['date'], $_OMP_sql['products_combo']);
$_OMP_sql['products_combo'] = 
    str_replace(':month', $_OMP_rec_orig['date'], $_OMP_sql['products_combo']);
$_OMP_prod_query = $_OMP_db->query($_OMP_sql['products_combo']);
if ($_OMP_prod_query->numRows() == 0) {
    // See new-line.php
    $_OMP_noitems = true;
    $_OMP_product_combo = '';
} else {
    // See new-line.php
    $_OMP_noitems = false;
    $_OMP_product_combo =
        '<select id="form[num_ol_pkey]" name="form[num_ol_pkey]"';
    !$_OMP_combo_required or $_OMP_product_combo .= ' required';
    $_OMP_product_combo .= '>';
    !$_OMP_prefilter or 
        $_OMP_product_combo .=
            '<option value=\'\'>'.$_OMP_LC['29'].'</option>';
    $_OMP_product_combo .= "\n";
    $_OMP_product_combo .= '        <optgroup label=\"'.$_OMP_LC[120].'\">';
    $group = true;
    while ($_OMP_prod_rec = $_OMP_prod_query->fetchRow()) {
        if ($_OMP_prod_rec['active'] == 'f') {
            if ($group) {
                $_OMP_product_combo .= "\n";
                $_OMP_product_combo .= '        </optgroup>';
                $_OMP_product_combo .= "\n";
                $_OMP_product_combo .=
                    '        <optgroup label=\"'.$_OMP_LC[121].'\">';
                $group = false;
            }
        }
        $_OMP_prod_rec['prod_pkey'] = 
            OMP_htmlentities($_OMP_prod_rec['prod_pkey']);
        empty($_OMP_prod_rec['eta']) or 
            $_OMP_prod_rec['eta'] = 
                strftime('%x', strtotime($_OMP_prod_rec['eta']));
        $_OMP_prod_rec['quantity'] = 
            number_format(
                $_OMP_prod_rec['quantity'], 
                0, 
                $_SESSION['dp'], 
                $_SESSION['ts']
            );
        $_OMP_product_combo .= "\n";
        $_OMP_product_combo .= '<option ';
        ($_OMP_prod_rec['ol_pkey'] != $_OMP_rec_sf['ol_pkey']) or 
            $_OMP_product_combo .= 'selected="selected" ';
        $_OMP_tmp_b = $_OMP_prod_rec['prod_pkey'].' | '.
            $_OMP_prod_rec['oi_pkey'].' | '.$_OMP_prod_rec['quantity'].
                ' | '.$_OMP_prod_rec['eta'];
        $_OMP_product_combo .= 'value="'.$_OMP_prod_rec['ol_pkey'].'">'.
            $_OMP_tmp_b.'</option>';
    }
    $_OMP_product_combo .= "\n";
    $_OMP_product_combo .= '        </optgroup>';
    $_OMP_product_combo .= "\n";
    $_OMP_product_combo .= '</select>';
}
unset($_OMP_prod_rec);
$_OMP_prod_query->free();
?>
