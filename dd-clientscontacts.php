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
// $Id: dd-clientscontacts.php,v 0.8 $
//
// Combo box with clients records and javascript
//

isset($_OMP_rec['client_pkey']) or $_OMP_rec['client_pkey'] = '';
isset($_OMP_prefilter) or $_OMP_prefilter = ($_OMP_rec['client_pkey'] == '');
$_OMP_att_combo =
    '<select name="form[enc_att]" id="form[enc_att]"';
// disabled in invoices.php edit record
// empty($_OMP_att_combo_disabled) or 
//     $_OMP_att_combo .= ' disabled';
empty($_OMP_onchange) or 
    $_OMP_att_combo .= ' onchange="javascript:this.form.submit();return true"';
!$_OMP_combo_required or $_OMP_att_combo .= ' required'; // see filter.php
$_OMP_att_combo .= '>';
!$_OMP_prefilter) or $_OMP_att_combo .=
    '<option value=\'\'>'.$_OMP_LC['29'].'</option>';
$_OMP_att_query = $_OMP_db->query($_OMP_sql['clients_combo']);
while ($_OMP_cli_rec = $_OMP_att_query->fetchRow()) {
    $_OMP_tmp_a = urlencode($_OMP_cli_rec['pkey']);
    $_OMP_cli_rec['pkey'] = OMP_htmlentities($_OMP_cli_rec['pkey']);
    $_OMP_att_combo .= '<option ';
    ($_OMP_cli_rec['pkey'] != $_OMP_rec['client_pkey']) or 
        $_OMP_att_combo .= 'selected="selected" ';
    $_OMP_att_combo .= 'value="'.$_OMP_tmp_a.'">'.
        ((strlen($_OMP_cli_rec['pkey']) > $_SESSION['ddl_width']) ? 
            substr($_OMP_cli_rec['pkey'], 0, $_SESSION['ddl_width'] - 3).'...' : 
            $_OMP_cli_rec['pkey']);
    $_OMP_att_combo .= '</option>';
}
$_OMP_att_combo .= '</select>';
$_OMP_att_query->free();
unset($_OMP_cli_rec);
?>
