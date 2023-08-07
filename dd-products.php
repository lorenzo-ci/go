<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | OMP Version 1                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2022 Lorenzo Ciani                                |
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
// | Author: Lorenzo Ciani <lorenzo@ciani.it>                             |
// +----------------------------------------------------------------------+
//
// $Id: dd-products.php,v 0.8 $
//
// Combo box with product records
//

!empty($_OMP_rec['supplier_pkey']) or
    $_OMP_rec['supplier_pkey'] = '%';
isset($_OMP_rec_sf['prod_pkey']) or
    $_OMP_rec_sf['prod_pkey'] = '';
isset($_OMP_combo_required) or
    $_OMP_combo_required = false;
isset($_OMP_prefilter) or
    $_OMP_prefilter = ($_OMP_rec_sf['prod_pkey'] == '');
isset($_OMP_product_combo_af) or
    $_OMP_product_combo_af = '';
// Please see orders-report.php
if ($_OMP_rec['supplier_pkey'] == '%') {
    $_OMP_product_query = 
        $_OMP_db->query($_OMP_sql['products_combo_no_filter']);
} else {
    $_OMP_sql['products_combo'] = 
        str_replace('?', $_OMP_db->quote($_OMP_rec['supplier_pkey']), 
        $_OMP_sql['products_combo']);
    $_OMP_product_query =
        $_OMP_db->query($_OMP_sql['products_combo']);
}


//echo $_OMP_sql['products_combo']; exit;


if ($_OMP_product_query->numRows() == 0) {
    $_OMP_noitems = true; // Please see orders.php
    $_OMP_product_combo = '';
} else {
    $_OMP_noitems = false;
    $_OMP_product_combo =
        '<select id="prod_pkey" name="form[txt_prod_pkey]"';
    empty($_OMP_onchange) or 
        $_OMP_product_combo .= 
            ' onchange="javascript:this.form.submit();return true"';
    // see filter.php
    !$_OMP_combo_required or
        $_OMP_product_combo .= ' required';
    $_OMP_product_combo .= '>';
    !$_OMP_prefilter or 
        $_OMP_product_combo .= "\n".
        '        <option value=\'\'>'.$_OMP_LC['29'].'</option>';
    $_OMP_product_combo .= "\n";
    $_OMP_product_combo .= '        <optgroup label=\"';
    if (empty($_OMP_orders_multi)) {
        $_OMP_product_combo .= $_OMP_LC[120];
    } else {
        if (empty($_OMP_rec['client_pkey'])) {
            $_OMP_product_combo .= $_OMP_LC[120];
        } else {
            $_OMP_product_combo .= $_OMP_LC[617];
        }
    }
    $_OMP_product_combo .= '\">';
    $group = true;
    while ($_OMP_product_rec = $_OMP_product_query->fetchRow()) {
        if ($_OMP_product_rec['active'] == 'f') {
            if ($group) {
                $_OMP_product_combo .= "\n";
                $_OMP_product_combo .= '        </optgroup>';
                $_OMP_product_combo .= "\n";
                $_OMP_product_combo .=
                    '        <optgroup label=\"'.$_OMP_LC[121].'\">';
                $group = false;
            }
        }
        $_OMP_product_rec['pkey'] = 
            OMP_htmlentities($_OMP_product_rec['pkey']);
        $_OMP_product_combo .= "\n";
        $_OMP_product_combo .= '         <option ';
        ($_OMP_product_rec['pkey'] != 
            OMP_htmlentities($_OMP_rec_sf['prod_pkey'])) or 
                $_OMP_product_combo .= 'selected="selected" ';
        if (strlen($_OMP_product_rec['pkey']) > $_SESSION['ddl_width']) {
            $_OMP_tmp_a = 
                substr($_OMP_product_rec['pkey'], 
                    0, 
                    $_SESSION['ddl_width'] - 3).
                    '...';
        } else {
            $_OMP_tmp_a = $_OMP_product_rec['pkey'];
        }
        $_OMP_product_combo .= 'value="'.$_OMP_product_rec['pkey'].'">'.
            $_OMP_tmp_a.'</option>';
    }
    $_OMP_product_combo .= "\n";
    $_OMP_product_combo .= '        </optgroup>';
    $_OMP_product_combo .= "\n";
    $_OMP_product_combo .= '       </select>';
}
$_OMP_product_query->free();
unset($_OMP_prod_rec);
?>
