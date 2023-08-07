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
// $Id: login.php,v 0.7 $
//
// Print login form
//
$_OMP_html['browser_title'] = $_OMP_LC[10];
$_OMP_html['page_title'] = '';
$_OMP_html['login_title'] = $_OMP_LC[11].' '.$_OMP_LC[32];
$_OMP_onload = '';
/* URL for login form */
$_OMP_url = OMP_PATH.'index.php?login=1';
/* Copyright */
// eval ("\$_OMP_html['page_title'] .= \"".$_OMP_TPL[8]."\";");
// eval ("\$_OMP_html['menu'] .= \"".$_OMP_TPL[8]."\";");
/* Login form */
eval ("\$_OMP_html['include'] = \"".$_OMP_TPL[1]."\";");
/* mdc-toolbar */
$_OMP_html['toolbar'] = '';
/* drawer button is not displayed in login and print */
$_OMP_html['drawer_button'] = '';
eval ("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
OMP_lose();
?>
