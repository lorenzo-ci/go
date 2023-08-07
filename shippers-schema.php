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
// $Id: shippers-schema.php,v 0.7 $
//
// Shippers schema
//

$_OMP_shippers_fld = array('pkey' => '"IDSpedizioniere"', 
    'name' => '"RagioneSociale"', 'addr' => '"Indirizzo"',
    'zip' => '"CAP"', 'city' => '"Città"', 'state' => '"Provincia"', 
    'region' => '"Regione"', 'country' => '"Nazione"', 
    'vat' => '"PartitaIVA"', 'tel' => '"NumeroTelefono"', 
    'fax' => '"NumeroFax"', 'note' => '"Nota"');
$_OMP_shippers_len= array('pkey' => 20, 'name' => 60, 'addr' => 255, 
    'zip' => 20, 'city' => 50, 'state' => 2, 'region' => 50, 
    'country' => 50, 'vat' => 20, 'tel' => 30, 'fax' => 30, 
    'note' => 1000);
$_OMP_shippers_key = array('pkey');
?>
