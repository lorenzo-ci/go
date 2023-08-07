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
// $Id: clientcontacts-schema.php,v 0.7 $
//
// Client contacts schema
//

$_OMP_clientcontacts_fld = array('pkey' => '"IDContattiClienti"', 
    'client_pkey' => '"IDCliente"', 'first_name' => '"Nome"', 
    'last_name' => '"Cognome"', 'title' => '"Titolo"', 
    'addr' => '"Indirizzo"', 'zip' => '"CAP"', 'city' => '"Città"', 
    'state' => '"Provincia"', 'region' => '"Regione"', 
    'country' => '"Nazione"', 'tel_home' => '"TelefonoDomicilio"', 
    'tel_office' => '"TelefonoUfficio"', 'mobile' => '"TelefonoPortatile"', 
    'tel_other' => '"NumeroTelefonoAlternativo"', 'fax' => '"NumeroFax"', 
    'email' => '"PostaElettronica"', 'user' => '"user"');
$_OMP_clientcontacts_len = array('pkey' => -1, 'client_pkey' => 20, 
    'first_name' => 50, 'last_name' => 50, 'title' => 20, 'addr' => 255, 
    'zip' => 20, 'city' => 50, 'state' => 2, 'region' => 50, 
    'country' => 50, 'tel_home' => 30, 'tel_office' => 30, 'mobile' => 30, 
    'tel_other' => 30, 'fax' => 30, 'email' => 500);
$_OMP_clientcontacts_key = array('pkey', 'client_pkey');
?>
