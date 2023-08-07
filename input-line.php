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
// $Id: input-line.php,v 0.8 $
//
// Prepare input line records
//

$_OMP_prepared = $_OMP_db->prepare($_OMP_sql['row']);
$_OMP_result = $_OMP_prepared->execute(OMP_keyCheck($_OMP_get));
$_OMP_result->numRows() or //Record not found
    OMP_genErr($_OMP_LC[83], 'Table: '.$_OMP_tables[$_OMP_tbl]);
$_OMP_rec = $_OMP_result->fetchRow();
$_OMP_rec_orig = $_OMP_rec; // see OMP_newLine, dd-products-dl.php
OMP_makeVars();
$_OMP_result->free();
$_OMP_prepared->free();
eval ("\$_OMP_list_rec = \"".$_OMP_header_tpl."\";");
// Default sort
isset($_OMP_get['sort_sub']) or 
    $_OMP_get['sort_sub'] = $_OMP_sort_default_sub;
// Sort order: $_OMP_get['sort_sub_type']
// Values are 0 (ascending sort) or 1 (descending sort)
isset($_OMP_get['sort_sub_type']) or 
    $_OMP_get['sort_sub_type'] = $_OMP_sort_type_default_sub;
$_OMP_get['sql_sub'] = OMP_makeSqlSub();
/*$_OMP_get['sql_sub'] = str_replace('?', $_OMP_db->quote($_OMP_rec['pkey']), 
    $_OMP_get['sql_sub']);*/
// $_OMP_result_sf = $_OMP_db->query($_OMP_get['sql_sub']);
// 2011-08-14 Changed the two lines above because of deliveries lines
// having additional client_pkey field key now
$_OMP_prepared = $_OMP_db->prepare($_OMP_get['sql_sub']);
$_OMP_result_sf = $_OMP_prepared->execute(OMP_keyCheck($_OMP_get));
?>
