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
// $Id: orders-schema.php,v 0.7 $
//
// Orders schema
//
/* vim: set expandtab tabstop=4 shiftwidth=4: */

$_OMP_orders_fld = array(
    'pkey' => '"IDOrdine"',
    'client_pkey' => '"IDCliente"',
    'supplier_pkey' => '"IDFornitore"',
    'date' => '"DataOrdine"',
    'supplier_ref' => '"RifOrdine"',
    'client_ref' => '"RifDelCliente"',
    'ref' => '"Conferma n"',
    'printed' => '"Stampato"',
    'amended' => '"Modificato"',
    'closed' => '"Saldato"',
    'cancelled' => '"Annullato"',
    'enduse_pkey' => '"EndUse"',
    'del_instr' => '"DeliveryInstructions"',
    'ship_instr' => '"ShippingInstructions"',
    'note' => '"Nota"',
    'paymnt_pkey' => '"IDModalitàPagamento"', 
    'term_pkey' => '"IDDecorrenzaPagamento"',
    'paymnt_days' => '"DilazionePagamento"',
    'curr' => '"curr"',
    'um' => '"um"',
    /* user is reserved by pgsql */
    'user' => '"user"'
);
$_OMP_orders_len = array(
    'pkey' => 15,
    'client_pkey' => 20,
    'supplier_pkey' => 20,
    'ref' => 15,
    'client_ref' => 20,
    'supplier_ref' => 15,
    'printed' => 1,
    'amended' => 1,
    'closed' => 1,
    'cancelled' => 1,
    'enduse_pkey' => 15,
    'del_instr' => 300,
    'ship_instr' => 300,
    'note' => 1000,
    'curr' => 4,
    'um' => 15
);
$_OMP_orders_key = array('pkey');
$_OMP_orders_lines_fld = array(
    'pkey' => '"IDDettaglioOrdini"',
    'oi_pkey' => '"IDOrdine"',
    'prod_pkey' => '"IDProdotto"',
    'year' => '"AnnoDiFatturazione"',
    'month' => '"MeseDiFatturazione"',
    'plan' => '"Fatturazione"',
    'quantity' => '"Quantità"',
    'price' => '"PrezzoLordo"',
    'discount' => '"Sconto"',
    'price_eur' => '"PrezzoLordoEUR"',
    'price_net' => '"PrezzoUnitario"',
    'price_net_eur' => '"PrezzoUnitarioEUR"',
    'note' => '"Destinazione"',
    'eta' => '"ETA"');
$_OMP_orders_lines_len = array(
    'oi_pkey' => 15,
    'prod_pkey' => 25,
    'year' => 4,
    'month' => 2,
    'plan' => 10,
    'note' => 50,
    'eta' => 10
);
$_OMP_orders_lines_key = array('pkey');
?>
