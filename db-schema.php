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
// $Id: db-schema.php,v 0.7 $
//
// Database schema
//

$_OMP_tables = array(
    'banks' => '"Banche"', 
    'clients' => '"Clienti"', 
    'clientcontacts' => '"ContattiClienti"', 
    'currencies' => 'currencies',
    'deliveries' => '"Consegne"', 
    'deliveries_lines' => '"DettagliConsegne"', 
    'enduses' => '"EndUse"', 
    'invoices' => '"Fatture"', 
    'invoices_lines' => '"DettagliFatture"', 
    'language' => 'language',
    'orders' => '"Ordini"', 
    'orders_lines' => '"DettagliOrdini"', 
    'ordersnotes' => '"OrdiniAppunti"', 
    'othercontacts' => '"Varie"', 
    'payments' => '"ModalitàPagamento"', 
    'products' => '"Prodotti"', 
    'ranges' => '"Categorie"', 
    'shippers' => '"Spedizionieri"', 
    'shippers-cli' => '"SpedizionieriClienti"', 
    'shippers-sup' => '"SpedizionieriFornitori"', 
    'suppliers' => '"Fornitori"', 
    'suppliercontacts' => '"ContattiFornitori"', 
    'terms' => '"DecorrenzaPagamenti"', 
    'tbcs' => '"OrdiniTBA"', 
    'tbcs_lines' => '"DettagliTBA"',
    'users' => '"profile"',
    'um' => '"um"',
    'zones' => '"zones"'
);
#$sequences = array('order_id' => 'order_id', 'dl_pkey' => '"dl_seq"');
?>
