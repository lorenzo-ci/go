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
// $Id: credentials.php,v 0.7 $
//
// Function to check credentials
//

/**
* Check credentials
*
* @param array $cat array with user categories allowed
* @see cp.php, clientcontacts.php, suppliercontacts.php
*/
function OMP_checkCredentials($cat)
{
    global $_OMP_lcl, $_OMP_tpl, $_OMP_LC;
    if (isset($cat) && in_array($_SESSION['cat'], $cat)) {
        return;
    } else {
        $_OMP_tpl = OMP_TPL_READ;
        $_OMP_lcl = OMP_LCL_READ.'0, 93';
//         OMP_load();
        OMP_genErr($_OMP_LC[93]); // Access denied
    }
}
?>
