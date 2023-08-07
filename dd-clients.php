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
// $Id: dd-clients.php,v 0.7 $
//
// Combo box with clients records and javascript
//
isset($_OMP_rec['client_pkey']) or $_OMP_rec['client_pkey'] = '';
isset($_OMP_prefilter) or
    $_OMP_prefilter = ($_OMP_rec['client_pkey'] == '');
isset($_OMP_combo_required) or $_OMP_combo_required = false;
// $_OMP_client_combo = '<select name="client_id" id="client_id"';
$_OMP_client_combo =
    '<select id="client_pkey" name="form[enc_client_pkey]"';
// disabled in invoices.php edit record
empty($_OMP_client_combo_disabled) or 
    $_OMP_client_combo .= ' disabled';
empty($_OMP_onchange) or 
    $_OMP_client_combo .= 
        ' onchange="javascript:this.form.submit();return true;"';
$actions_client_combo_required = array('new' => 1, 'multi' => 1);
 /* see filter.php */
if ($_OMP_combo_required ||
    isset($actions_client_combo_required[$_OMP_get['action']]))
     $_OMP_client_combo .= ' required';
/* see filter.php */
//!$_OMP_combo_required or $_OMP_client_combo .= ' required';
$_OMP_client_combo .= '>'."\n";
!$_OMP_prefilter or
    $_OMP_client_combo .=
        '     <option value=\'\'>'.$_OMP_LC['29'].'</option>';
$_OMP_client_combo .= "\n";
$_OMP_client_combo .= '     <optgroup label=\"'.$_OMP_LC[120].'\">';
/* $_OMP_sql['clients_combo'] is defined in dd-sql.php */
$_OMP_cli_query = $_OMP_db->query($_OMP_sql['clients_combo']);
$group = true;
while ($_OMP_cli_rec = $_OMP_cli_query->fetchRow()) {
    if ($_OMP_cli_rec['active'] == 'f') {
        if ($group) {
            $_OMP_client_combo .= "\n";
            $_OMP_client_combo .= '     </optgroup>';
            $_OMP_client_combo .= "\n";
            $_OMP_client_combo .=
                '     <optgroup label=\"'.$_OMP_LC[121].'\">';
            $group = false;
        }
    }
    $client_pkey_enc = urlencode($_OMP_cli_rec['pkey']);
    $_OMP_cli_rec['pkey'] = OMP_htmlentities($_OMP_cli_rec['pkey']);
    $_OMP_client_combo .= "\n";
    $_OMP_client_combo .= '      <option ';
    ($_OMP_cli_rec['pkey'] != $_OMP_rec['client_pkey']) or 
        $_OMP_client_combo .= 'selected="selected" ';
    $_OMP_client_combo .= 'value="'.$client_pkey_enc.'">';
    if (strlen($_OMP_cli_rec['pkey']) > $_SESSION['ddl_width']) {
        $_OMP_client_combo .=
            substr($_OMP_cli_rec['pkey'], 0, $_SESSION['ddl_width'] - 3).
                '...';
    } else {
        $_OMP_client_combo .= $_OMP_cli_rec['pkey'];
    }
    $_OMP_client_combo .= '</option>';
}
$_OMP_client_combo .= "\n";
$_OMP_client_combo .= '     </optgroup>';
$_OMP_client_combo .= "\n";
$_OMP_client_combo .= '    </select>';
$_OMP_cli_query->free();
unset($_OMP_cli_rec);
?>
