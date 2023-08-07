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
// | See the GNU General Public License                                   |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the                        |
// | Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston,  |
// | MA 02111-1307 USA                                                    |
// +----------------------------------------------------------------------+
// | Author: Lorenzo Ciani <lciani@yahoo.it>                              |
// +----------------------------------------------------------------------+
//
// $Id: user.php,v 0.8 $
//
// Save favourite items
//

// Supplier contacts schema
require_once 'schemas/suppliercontacts-schema.php';
// Step 1: retrieve numeric IDs of contacts
$_OMP_fav_supplier['to'] = $_OMP_db->queryOne('SELECT '.
    $_OMP_suppliercontacts_fld['pkey'].
    ' FROM '.$_OMP_tables['suppliercontacts'].
    ' WHERE '.$_OMP_suppliercontacts_fld['first_name'].' || \' \' || '.
    $_OMP_suppliercontacts_fld['last_name'].' = '.
    $_OMP_db->quote($_POST['att'], 'text').
    ' AND '.$_OMP_suppliercontacts_fld['supplier_pkey'].' = '.
    $_OMP_db->quote($_POST['supplier_pkey'], 'text'));
$_OMP_fav_supplier['cc'] = $_OMP_db->queryOne('SELECT '.
    $_OMP_suppliercontacts_fld['pkey'].
    ' FROM '.$_OMP_tables['suppliercontacts'].
    ' WHERE '.$_OMP_suppliercontacts_fld['first_name'].' || \' \' || '.
    $_OMP_suppliercontacts_fld['last_name'].' = '.
    $_OMP_db->quote($_POST['cc'], 'text').
    ' AND '.$_OMP_suppliercontacts_fld['supplier_pkey'].' = '.
    $_OMP_db->quote($_POST['supplier_pkey'], 'text'));
unset ($_OMP_tmp_b);
// Step 2: prepare SQL
// No favourite contacts saved yet
if (empty($_POST['fav_supplier_to'])) {
    $_OMP_tmp_a = 'INSERT INTO favourite_supplier_contacts VALUES (?, ?, ? ,?)';
    $_OMP_tmp_b = array($_SESSION['id'], $_POST['supplier_pkey'], 
        $_OMP_fav_supplier['to'], $_OMP_fav_supplier['cc']);
    $_OMP_datatypes = array('text', 'text', 'integer', 'integer');
} elseif ($_OMP_fav_supplier['to'] != $_POST['fav_supplier_to'] || 
    $_OMP_fav_supplier['cc'] != $_POST['fav_supplier_cc']) {
    // If contacts changed in recipient form
    $_OMP_tmp_a = 'UPDATE favourite_supplier_contacts SET fav_supplier_to = ?, ';
    $_OMP_tmp_a .= 'fav_supplier_cc = ? WHERE username = ? AND supplier_id = ?';
    $_OMP_tmp_b = array($_OMP_fav_supplier['to'], $_OMP_fav_supplier['cc'], 
        $_SESSION['id'], $_POST['supplier_pkey']);
    $_OMP_datatypes = array('integer', 'integer', 'text', 'text');
}
// Step 3: execute SQL
if (isset($_OMP_tmp_b)) {
    $_OMP_prepared = $_OMP_db->prepare($_OMP_tmp_a, $_OMP_datatypes);
    $_OMP_db_result = $_OMP_prepared->execute($_OMP_tmp_b);
    $_OMP_prepared->free();
}
?>
