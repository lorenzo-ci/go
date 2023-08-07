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
// $Id: dd-suppliers.php,v 0.7 $
//
// Combo box with suppliers records and javascript
//

isset($_OMP_rec['supplier_pkey']) or $_OMP_rec['supplier_pkey'] = '';
isset($_OMP_prefilter) or 
    $_OMP_prefilter = ($_OMP_rec['supplier_pkey'] == '');
isset($_OMP_combo_required) or $_OMP_combo_required = false;
$_OMP_supplier_combo =
    '<select id="supplier_pkey" name="form[enc_supplier_pkey]"';
// disabled in invoices.php edit record
empty($_OMP_supplier_combo_disabled) or 
    $_OMP_supplier_combo .= ' disabled';
// Commented out assuming needed only by orders.php 'new'
empty($_OMP_onchange) or  
    $_OMP_supplier_combo .= 
        ' onchange="javascript:this.form.submit();return true"';
!$_OMP_combo_required or $_OMP_supplier_combo .= ' required';
$_OMP_supplier_combo .= '>'."\n";
!$_OMP_prefilter or 
    $_OMP_supplier_combo .=
        '    <option value=\'\'>'.$_OMP_LC['29'].'</option>';
/* $_OMP_sql['suppliers_combo'] is defined in dd-sql.php */
$_OMP_sup_query = $_OMP_db->query($_OMP_sql['suppliers_combo']);
while ($_OMP_sup_rec = $_OMP_sup_query->fetchRow()) {
    $_OMP_tmp_a = urlencode($_OMP_sup_rec['pkey']);
    $_OMP_sup_rec['pkey'] = OMP_htmlentities($_OMP_sup_rec['pkey']);
    $_OMP_supplier_combo .= "\n".'    <option ';
    ($_OMP_sup_rec['pkey'] != $_OMP_rec['supplier_pkey']) or 
        $_OMP_supplier_combo .= 'selected="selected" ';
    $_OMP_supplier_combo .= 'value="'.$_OMP_tmp_a.'">'.
        ((strlen($_OMP_sup_rec['pkey']) > $_SESSION['ddl_width']) 
        ? substr($_OMP_sup_rec['pkey'], 0, $_SESSION['ddl_width'] - 3).
            '...' 
        : $_OMP_sup_rec['pkey']);
    $_OMP_supplier_combo .= '</option>';
}
$_OMP_supplier_combo .= "\n".'     </select>';
$_OMP_sup_query->free();
unset($_OMP_sup_rec);
?>
