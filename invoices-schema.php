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
// $Id: invoices-schema.php,v 0.7 $
//
// Invoices schema
//

$_OMP_invoices_fld = array('pkey' => '"IDFattura"', 
    'date' => '"DataFattura"', 'client_pkey' => '"IDCliente"', 
    'supplier_pkey' => '"IDFornitore"', 'oi_pkey' => '"IDOrdine"', 
    'del_pkey' => '"IDConsegna"', 'paymnt_date' => '"DataPagamento"', 
    'bank_pkey' => '"IDBanca"', 'bank' => '"Banca"', 
    'br_ref' => '"NumeroRB"', 'amount' => '"Importo"', 
    'amount_eur' => '"ImportoEUR"', 'due_date' => '"Scadenza"', 
    'note' => '"Nota"', 'rim' => '"Rim"', 'list' => '"Lista"', 
    'commission' => '"Commissione"', 
    'commission_eur' => '"CommissioneEUR"');
$_OMP_invoices_len = array('pkey' => 50, 'client_pkey' => 20, 
    'supplier_pkey' => 20, 'oi_pkey' => 15, 'del_pkey' => 15,
    'bank_pkey' => 15, 'bank' => 30, 'br_ref' => 20, 'note' => 1000, 
    'rim' => 20, 'list' => 50, 'br_ref' => 20);
$_OMP_invoices_key = array('pkey', 'date', 'supplier_pkey');
$_OMP_invoices_lines_fld = array('inv_pkey' => '"IDFattura"', 
    'dl_pkey' => '"IDDettagliConsegne"', 
    'supplier_pkey' => '"IDFornitore"', 'date' => '"DataFattura"');
$_OMP_invoices_lines_len = array('inv_pkey' => 50, 'supplier_pkey' => 20);
$_OMP_invoices_lines_key = array('inv_pkey', 'date', 'supplier_pkey', 'dl_pkey');
?>
