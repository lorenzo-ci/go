<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | OMP Version 1                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 2021-2022 Lorenzo Ciani                                |
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
// $Id: ocr-load.php,v 0.1 $
//
// Scan delivery files
//
/* (isset($_OMP_get['empty_table'])) ?
    'javascript:history.back();return true' : */
require_once 'base.php';
$_OMP_tpl = OMP_TPL_READ.'14, 1700';
$_OMP_lcl = OMP_LCL_MENU.'39, 37, 4003';
OMP_load();
/* mdc-toolbar */
// $_OMP_html['toolbar'] = '';
// $button_cancel_href = 'javascript:history.back();';
$button_cancel_href = 'javascript:window.location.href=\'index.php\'\;';
/* @see base.php */
OMP_buttons('14', '39', '37', $button_cancel_href);
$_OMP_html['page_title'] = $_OMP_html['browser_title'] = $_OMP_LC['4002'];
/* @see functions.php */
OMP_drawer();
eval("\$_OMP_html['include'] = \"".$_OMP_TPL['1700']."\";");
eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
OMP_lose();
?>
