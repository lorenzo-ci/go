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
// $Id: dd-sql.php,v 0.7 $
//
// SQL for drop-down lists
//
if (isset($_OMP_banks_fld)) {
    $_OMP_sql['banks_combo'] = 'SELECT DISTINCT '.
        $_OMP_banks_fld['pkey'].' AS pkey FROM '.
        $_OMP_tables['banks'].' WHERE lower('.
        $_OMP_banks_fld['client_pkey'].') = lower(?) ORDER BY '.
        $_OMP_banks_fld['pkey'];
    $_OMP_sql['banks_combo_no_filter'] = 'SELECT DISTINCT'.
        $_OMP_banks_fld['pkey'].' AS pkey FROM '.
        $_OMP_tables['banks'].' ORDER BY '.
        $_OMP_banks_fld['pkey'];
}
if (isset($_OMP_clients_fld)) {
    if ($_SESSION['cat'] == 1) {// Filter for suppliers
        $_OMP_sql['clients_combo'] = 'SELECT DISTINCT '.
            $_OMP_tables['clients'].'.'.
            $_OMP_clients_fld['pkey'].' AS pkey FROM '.
            $_OMP_tables['orders'].', '.
            $_OMP_tables['clients'].' WHERE '.
            $_OMP_tables['orders'].'.'.
            $_OMP_orders_fld['client_pkey'].' = '.
            $_OMP_tables['clients'].'.'.
            $_OMP_clients_fld['pkey'].'AND '.
            $_OMP_orders_fld['supplier_pkey'].' = \''.
            $_SESSION['id'].'\' AND '.
            $_OMP_orders_fld['date'].
            ' >= current_date - interval \'13 months\' AND '.
            $_OMP_tables['clients'].'.'.
            $_OMP_clients_fld['active'].' = TRUE ORDER BY '.
            $_OMP_tables['clients'].'.'.
            $_OMP_clients_fld['pkey'];
    } else {
        /* lists active clients first */
        $_OMP_sql['clients_combo'] = 'SELECT '.
                $_OMP_clients_fld['pkey'].' AS pkey, '.
                $_OMP_clients_fld['active'].' AS active FROM '.
                $_OMP_tables['clients'].' ORDER BY '.
                $_OMP_clients_fld['active'].' DESC, '.
                $_OMP_clients_fld['pkey'];
    }
}
if (isset($_OMP_clientcontacts_fld)) {
    $_OMP_sql['clientcontacts_combo'] = 'SELECT '.
    $_OMP_clientcontacts_fld['pkey'].' AS pkey, '.
    $_OMP_clientcontacts_fld['last_name'].' AS last_name, '.
    $_OMP_clientcontacts_fld['first_name'].' AS first_name FROM '.
    $_OMP_tables['clientcontacts'].' ORDER BY '.
    $_OMP_clientcontacts_fld['last_name'];
}
if (isset($_OMP_currencies_fld)) {
    /* lists active clients first */
    $_OMP_sql['currencies_combo'] = 'SELECT '.
            $_OMP_currencies_fld['pkey'].' AS pkey, '.
            $_OMP_currencies_fld['active'].' AS active FROM '.
            $_OMP_tables['currencies'].' ORDER BY '.
            $_OMP_currencies_fld['active'].' DESC, '.
            $_OMP_currencies_fld['pkey'];
}
if (isset($_OMP_enduses_fld)) {
    $_OMP_sql['enduse_combo'] = 'SELECT '.
    $_OMP_enduses_fld['pkey'].' AS pkey, '.$_OMP_enduses_fld['name'].
    ' AS name FROM '.$_OMP_tables['enduses'].' ORDER BY '.
    $_OMP_enduses_fld['pkey'];
}
if (isset($_OMP_products_fld)) {
/*    $_OMP_sql['products_combo'] = 'SELECT '.$_OMP_products_fld['pkey'].
        ' AS pkey FROM '.$_OMP_tables['products'].' WHERE lower('.
        $_OMP_products_fld['supplier_pkey'].') = lower(?) AND '.
        $_OMP_products_fld['active'].' = TRUE ORDER BY '.
        $_OMP_products_fld['pkey']; */
        /* lists active products first */
    $_OMP_sql['products_combo'] = 'SELECT '.
        $_OMP_products_fld['pkey'].' AS pkey, '.
        $_OMP_products_fld['active'].' AS active FROM '.
        $_OMP_tables['products'].' WHERE lower('.
        $_OMP_products_fld['supplier_pkey'].') = lower(?) ORDER BY '.
        $_OMP_products_fld['active'].' DESC, '.$_OMP_products_fld['pkey'];
    if ($_SESSION['cat'] == 1) { // Filter for suppliers
        $_OMP_sql['products_combo'] = str_replace('?', 
            $_OMP_db->quote($_SESSION['id']), $_OMP_sql['products_combo']);
        $_OMP_sql['products_combo_no_filter'] = $_OMP_sql['products_combo'];
    } else {
/*
        $_OMP_sql['products_combo_no_filter'] = 'SELECT DISTINCT '.
            $_OMP_tables['products'].'.'.$_OMP_products_fld['pkey'].
            ' AS pkey FROM '.$_OMP_tables['products'].
            ', '.$_OMP_tables['suppliers'].' WHERE '.
            $_OMP_tables['suppliers'].'.'.$_OMP_suppliers_fld['pkey'].
            ' = '.$_OMP_tables['products'].'.'.
            $_OMP_products_fld['supplier_pkey'].
            ' AND '.$_OMP_tables['suppliers'].'.'.$_OMP_suppliers_fld['active'].
            ' = TRUE AND '.$_OMP_tables['products'].'.'.
            $_OMP_products_fld['active'].' = TRUE '.
            ' ORDER BY '.$_OMP_tables['products'].'.'.
            $_OMP_products_fld['pkey'];
*/
        $_OMP_sql['products_combo_no_filter'] = 'SELECT DISTINCT '.
            $_OMP_tables['products'].'.'.$_OMP_products_fld['pkey'].
            ' AS pkey, '.
            $_OMP_tables['products'].'.'.$_OMP_products_fld['active'].
            ' FROM '.$_OMP_tables['products'].
            ', '.$_OMP_tables['suppliers'].' WHERE '.
            $_OMP_tables['suppliers'].'.'.$_OMP_suppliers_fld['pkey'].
            ' = '.$_OMP_tables['products'].'.'.
            $_OMP_products_fld['supplier_pkey'].
            ' ORDER BY '.$_OMP_tables['products'].'.'.
            $_OMP_products_fld['active'].' DESC, '.$_OMP_tables['products'].'.'.
            $_OMP_products_fld['pkey'];
    }
}
!isset($_OMP_payments_fld) or $_OMP_sql['paymnt_combo'] = 'SELECT '.
    $_OMP_payments_fld['pkey'].' AS pkey, '.
    $_OMP_payments_fld['name'].' AS name, '.$_OMP_payments_fld['description'].
    ' AS description FROM '.
    $_OMP_tables['payments'].' ORDER BY '.$_OMP_payments_fld['pkey'];
if (isset($_OMP_ranges_fld)) {
     if ($_SESSION['cat'] == 1) { // Filter for suppliers
        $_OMP_sql['ranges_combo'] = 'SELECT DISTINCT '.
            $_OMP_tables['ranges'].'.'.$_OMP_ranges_fld['pkey'].
            ' AS pkey, '.$_OMP_ranges_fld['name'].
            ' AS name FROM '.$_OMP_tables['products'].
            ', '.$_OMP_tables['ranges'].
            ', '.$_OMP_tables['orders_lines'].
            ' WHERE '.
            $_OMP_tables['products'].'.'.$_OMP_products_fld['range_pkey'].
            ' = '.$_OMP_tables['ranges'].'.'.$_OMP_ranges_fld['pkey'].
            ' AND '.
            $_OMP_tables['orders_lines'].'.'.$_OMP_orders_lines_fld['prod_pkey'].
            ' = '.$_OMP_tables['products'].'.'.$_OMP_products_fld['pkey'].
            ' AND '.
            $_OMP_tables['ranges'].'.'.$_OMP_ranges_fld['active'].
            ' = TRUE'.
            ' AND '.
            $_OMP_tables['products'].'.'.$_OMP_products_fld['supplier_pkey'].
            ' = \''.htmlentities($_SESSION['id']).'\''.
            ' AND '.
            $_OMP_tables['orders_lines'].'.'.$_OMP_orders_lines_fld['plan'].
            ' >= current_date - interval \'13 months\''.
            ' ORDER BY '.$_OMP_ranges_fld['name'];
    } else {
        $_OMP_sql['ranges_combo'] = 'SELECT '.$_OMP_ranges_fld['pkey'].
            ' AS pkey, '.$_OMP_ranges_fld['name'].
            ' AS name FROM '.$_OMP_tables['ranges'];
        isset($_OMP_tbl) or $_OMP_tbl = '';
        if ($_OMP_tbl != 'products') {
            $_OMP_sql['ranges_combo'] .= ' WHERE '.
            $_OMP_ranges_fld['active'].' = TRUE';
        }
        $_OMP_sql['ranges_combo'] .= ' ORDER BY '.
        $_OMP_ranges_fld['name'];
    }
}
if (isset($_OMP_suppliers_fld)) {
    $_OMP_sql['suppliers_combo'] = 'SELECT DISTINCT '.
    $_OMP_tables['suppliers'].'.'.
    $_OMP_suppliers_fld['pkey'].' AS pkey, '.
    $_OMP_tables['suppliers'].'.'.
    $_OMP_suppliers_fld['active'].' AS active FROM '.
    $_OMP_tables['suppliers'].' LEFT JOIN '.
    $_OMP_tables['products'].' ON ('.
    $_OMP_tables['suppliers'].'.'.
    $_OMP_suppliers_fld['pkey'].' = '.
    $_OMP_tables['products'].'.'.
    $_OMP_suppliers_fld['pkey'].') ORDER BY '.
    $_OMP_tables['suppliers'].'.'.
    $_OMP_suppliers_fld['active'].' DESC, '.
    $_OMP_tables['suppliers'].'.'.
    $_OMP_suppliers_fld['pkey'];
    /*$_OMP_sql['suppliers_combo'] = 'SELECT '.
        $_OMP_suppliers_fld['pkey'].' AS pkey FROM '.
        $_OMP_tables['suppliers'].' ORDER BY '.
        $_OMP_suppliers_fld['pkey'];*/
    /* active no longer used
    $_OMP_sql['suppliers_combo'] = 'SELECT '.
        $_OMP_suppliers_fld['pkey'].' AS pkey FROM '.$_OMP_tables['suppliers'].
        ' WHERE '.$_OMP_suppliers_fld['active'].' = TRUE ORDER BY '.
        $_OMP_suppliers_fld['pkey']; */
}
if (isset($_OMP_terms_fld)) {
    $_OMP_sql['term_combo'] = 'SELECT '.
    $_OMP_terms_fld['pkey'].' AS pkey, '.
    $_OMP_terms_fld['name'].' AS name, '.
    $_OMP_terms_fld['description'].' AS description FROM '.
    $_OMP_tables['terms'].' ORDER BY '.$_OMP_terms_fld['pkey'];
}
if (isset($_OMP_um_fld)) {
    /* lists active um first */
    $_OMP_sql['um_combo'] = 'SELECT '.
            $_OMP_um_fld['pkey'].' AS pkey, '.
            $_OMP_um_fld['active'].' AS active FROM '.
            $_OMP_tables['um'].' ORDER BY '.
            $_OMP_um_fld['active'].' DESC, '.
            $_OMP_um_fld['pkey'];
}
?>
