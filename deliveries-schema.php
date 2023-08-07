<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | OMP Version 1                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2023 Lorenzo Ciani                                |
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
// $Id: deliveries-schema.php,v 0.8 $
//
// Deliveries schema
//

$_OMP_deliveries_fld = array(
    'pkey' => '"IDConsegna"',
    'client_pkey' => '"IDCliente"',
    'date' => '"DataSpedizione"',
    'origin' => '"LuogoPartenzaSpedizione"',
    'eta' => '"ETA"',
    'destination' => '"IndirizzoDestinazione"',
    'truck' => '"Camion/Container"',
    'note' => '"Nota"',
    'printed' => '"Stampato"'
);
$_OMP_deliveries_len = array(
    'pkey' => 15, 'client_pkey' => 20,
    'origin' => 50, 'destination' => 255,
    'truck' => 50,
    'note' => 1000, 'printed' => 1
);
$_OMP_deliveries_key = array('pkey', 'client_pkey');
$_OMP_deliveries_lines_fld = array(
    'pkey' => '"IDDettagliConsegne"',
    'del_pkey' => '"IDConsegna"',
    'client_pkey' => '"IDCliente"',
    'ol_pkey' => '"IDDettaglioOrdini"',
    'bales' => '"Balle n"',
    'quantity' => '"Quantità"'
);
$_OMP_deliveries_lines_len = array('del_pkey' => 15, 'client_pkey' => 20);
$_OMP_deliveries_lines_key = array('pkey');
$_OMP_del_sql_insert = 'INSERT INTO '.
    $_OMP_tables['deliveries'].' ('.
    $_OMP_deliveries_fld['pkey'].', '.
    $_OMP_deliveries_fld['client_pkey'].', '.
    $_OMP_deliveries_fld['date'].', '.
    $_OMP_deliveries_fld['origin'].', '.
    $_OMP_deliveries_fld['eta'].', '.
    $_OMP_deliveries_fld['destination'].', '.
    $_OMP_deliveries_fld['truck'].', '.
    $_OMP_deliveries_fld['note'].
    ') VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
$_OMP_datatypes_del_sql_insert = array(
    'text', 'text', 'date', 'text', 'date',
    'text', 'text', 'text'
);
$_OMP_del_sql_insert_line = 'INSERT INTO '.
    $_OMP_tables['deliveries_lines'].' ('.
    $_OMP_deliveries_lines_fld['del_pkey'].', '.
    $_OMP_deliveries_lines_fld['client_pkey'].', '.
    $_OMP_deliveries_lines_fld['ol_pkey'].', '.
    $_OMP_deliveries_lines_fld['bales'].', '.
    $_OMP_deliveries_lines_fld['quantity'].
    ') VALUES (?, ?, ?, ?, ?)';
$_OMP_datatypes_del_sql_insert_line = array(
    'text', 'text', 'integer',
    'integer', 'decimal'
);
$_OMP_del_sql_update = 'UPDATE '.
    $_OMP_tables['deliveries'].' SET '.
    $_OMP_deliveries_fld['pkey'].' = ?, '.
    $_OMP_deliveries_fld['client_pkey'].' = ?, '.
    $_OMP_deliveries_fld['date'].' = ?, '.
    $_OMP_deliveries_fld['origin'].' = ?, '.
    $_OMP_deliveries_fld['eta'].' = ?, '.
    $_OMP_deliveries_fld['destination'].' = ?, '.
    $_OMP_deliveries_fld['truck'].' = ?, '.
    $_OMP_deliveries_fld['note'].' = ? WHERE '.
    $_OMP_deliveries_fld['pkey'].' = ? AND '.
    $_OMP_deliveries_fld['client_pkey'].' = ?';
$_OMP_datatypes_del_sql_update = array(
    'text', 'text', 'date', 'text', 'date',
    'text', 'text', 'text', 'text', 'text'
);
$_OMP_del_sql_update_line = 'UPDATE '.
    $_OMP_tables['deliveries_lines'].' SET '.
    $_OMP_deliveries_lines_fld['del_pkey'].' = ?, '.
    $_OMP_deliveries_fld['client_pkey'].' = ?, '.
    $_OMP_deliveries_lines_fld['ol_pkey'].' = ?, '.
    $_OMP_deliveries_lines_fld['bales'].' = ?, '.
    $_OMP_deliveries_lines_fld['quantity'].' = ? WHERE '.
    $_OMP_deliveries_lines_fld['pkey'].' = ?';
$_OMP_datatypes_del_sql_update_line = array(
    'text', 'text', 'integer', 'integer',
    'decimal', 'integer'
);
?>
