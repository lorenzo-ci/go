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
// $Id: record_title.php,v 0.8 $
//
// Adds the record title
//
if (!empty($_OMP_headline)) {
    $_OMP_html['headline'] =
        $_OMP_LC[$_OMP_headline].' '.
        $_OMP_rec['pkey'];
    if (isset($_OMP_rec['date'])) {
        $_OMP_html['headline'] .=
            ' '.$_OMP_LC[52].' '.
            $_OMP_rec['date'];
    }
    eval ("\$_OMP_html['record_title'] = \"".$_OMP_TPL[48]."\";");
} else {
    $_OMP_html['record_title'] = '';
}
?>
