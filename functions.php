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
// | Author: Lorenzo Ciani <lorenzo@ciani.it>                              |
// +----------------------------------------------------------------------+
//
// $Id: functions.php,v 0.7 $
//
// General purpose functions
//

/* CHANGE LOG
 * 2023-07-28
 * OMP_makeData() extra white spaces at Look for 'whole words'
/**
* Check HTML input type date
*
* @param string $value string to be checked
* @param string $label name of form input field
* @return formatted date
* @see OMP_makeData()
*/
function OMP_checkDate($value, $label)
{
    $input = new DateTime($value);
    $limit = new DateTime(OMP_YEAR_START.'-01-01');
    if (empty($label)) $label = 'Date';
    if ($input < $limit) {
        OMP_genErr($label.' cannot be earlier than '.OMP_YEAR_START, '');
    }
    // We make no further checks
    // because we use input type date
    return $value;
}

/**
* Check HTML input type text for date
*
* @param string $value string to be checked
* @param string $label name of form input field
* @return formatted date
* @see orders.php 'multi'
*/
function OMP_checkDateTxt($value, $label)
{
    global $_OMP_tbl_fld;

    $value = trim($value);
    if (empty($value) &&
        !strcmp($label, $_OMP_tbl_fld['paymnt_date'])) {
        /* payment date can be empty */
        return;
    }
    if (empty($label)) $label = 'Date';
    if (empty($value)) {
        OMP_genErr($label.' is empty', '');
    }
    // The following line is for
    // Mac OS X 10.3.5 or earlier and locale = it_IT
    $value = str_replace('.', '/', $value);
    $date_arr = preg_split('/(\/|-)/', $value);
    is_array($date_arr) or OMP_genErr($label.' is not a date', '');
    if (count($date_arr) < 2 || count($date_arr) > 3) {
        OMP_genErr($label.' is not a date', '');
    } elseif (count($date_arr) == 2) {
        // aggiungi anno
        $date_arr[] = date('Y');
    }
    /* make ISO format date, assume date_arr is "MM DD YYYY" */
    if ($_SESSION['locale'] === 'it_IT') {
        /*  is day first? */
        if (checkdate($date_arr[1], $date_arr[0], $date_arr[2])) {
            $date_arr[2] = OMP_checkYear($date_arr[2]);
            $value = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];
        } else {
            /* it didn't work so try ISO format */
            $date_arr[0] = OMP_checkYear($date_arr[0]);
            $value = $date_arr[0].'-'.$date_arr[1].'-'.$date_arr[2];
        }
    } else {
        /* is month first? */
        if (checkdate($date_arr[1], $date_arr[2], $date_arr[0])) {
            $date_arr[0] = OMP_checkYear($date_arr[0]);
            $value = $date_arr[0].'-'.$date_arr[1].'-'.$date_arr[2];
        } else {
            $date_arr[1] = OMP_checkYear($date_arr[1]);
            $value = $date_arr[1].'-'.$date_arr[0].'-'.$date_arr[2];
        }
    }
    return $value;
}


/**
* Check user input for invalid chars
*
* @param string $value string to be checked
* @param string $label name of form input field
* @param bool $auto if true automatically strips special chars and tags
* @return bool true if user input is ok and $auto is true
* @return string if $auto is true
* @see OMP_makeData()
*/
function OMP_checkIllegalChars($value, $label = false, $auto = false)
{
    $value = htmlspecialchars($value);
    if ($auto) {
        $value = strip_tags($value);
        return $value;
    } else {
        if ($label == false) { $label = 'text' ; }
        if ($value <> strip_tags($value)) {
            OMP_genErr('HTML tags not allowed. Please amend '.$label, '');
        }
        return true;
    }
}

/**
* Check user input for number
*
* @param string $value the string to be checked
* @param string $label name of form input field
* @return string the number checked ('' is converted to '0');
* @see OMP_makeData()
*/
function OMP_checkNum($value, $label)
{
    if ($value === '') {
        return '0';
    }
    // If locale is not english, then replace European
    // notation for thousand separator and decimal point
    // No longer needed since we changed to input type number
/*     if ($_SESSION['LC'] != 'en' && $_SESSION['locale_ok']) {
        $value = str_replace($_SESSION['ts'], '', $value);
        $value = str_replace($_SESSION['dp'], '.', $value);
    }*/
    if (is_numeric($value)) {
        return $value;
    } else {
        OMP_genErr($label.' is not a number', '');
    }
}

/**
* Check user input for text
*
* @param string $value the string to be checked
* @param string $label name of form input field
* @param string $len max length of string
* @param bool $nul can string be empty
* @see OMP_makeData()
*/
function OMP_checkTxt($value, $label, $len, $nul = false)
{
    global $_OMP_encoding;
    
    $value = filter_var($value, FILTER_SANITIZE_STRING);
    $value = OMP_htmlentities($value);
    is_string($value) or OMP_genErr($label.' is not text', '');
    ($len == -1 || strlen($value) <= $len) or
        OMP_genErr($label.' cannot exceed '.$len.' chars', '');
    if (empty($value)) {
        if (!$nul) {
            OMP_genErr($label.' is empty', '');
        } else {
            $value = '';
        }
    }
    return $value;
}

/**
* Tries to correct malformed year
*
* @param string $year string of year
* @return string $year
* @see OMP_checkDateTxt
*/
function OMP_checkYear($year)
{
    $century = substr(date('Y'), 0, 2);
    $year_lenght = strlen($year);
    if ($year_lenght == 2) {
        $year = $century.$year;
    } elseif ($year_lenght == 1 || $year_lenght == 3) {
        $year = date('Y');
    }
    return $year;
}

/**
* Evals the drawer
*
*/
function OMP_drawer()
{
    global $_OMP_html, $_OMP_LC, $_OMP_mdc_menu, $_OMP_TPL;
    eval ("\$_OMP_html['drawer'] = \"".$_OMP_TPL[2]."\";");
    eval ("\$_OMP_html['drawer_button'] = \"".$_OMP_TPL[12]."\";");
    eval ("\$_OMP_html['logo'] = \"".$_OMP_TPL[32]."\";");
    eval ("\$_OMP_html['drawer_init'] = \"".$_OMP_TPL[30]."\";");
}

/**
* Returns the end of a given month
*
* @param string $yearmonth (YYYY-mm)
* @return string date of end of month
* @see deliveries-report, invoices-report.php
*/
function OMP_endOfMonth($yearmonth)
{
    $ym = explode("-", $yearmonth);
    $date = new DateTime();
    $date->setDate($ym[0], $ym[1], 1);
    $date->modify("+1 month");
    $date->modify("-1 day");
    return $date->format("Y-m-d");
}

/**
* Check array with primary keys against md5 hashes
* Adds key values to select record to edit
*
* @param array $myarray parameters
* @param bool $line are we checking line records or master records
* @param array $data_arry data
* @return array with data for SQL
* @see del.php, del-line.php, edit.php, edit-line.php, input-line.php
*/
function OMP_keyCheck($myarray, $line = false, $data_array = array())
{
    if ($line) {
        $data_line = array();
    } else {
        $data = array();
    }
    foreach ($myarray as $key => $value) {
        $type = substr($key, 0, 3);
        $new_key = substr($key, 3);
        if ($type == 'lh_') {
            if (md5(OMP_SECRET.$myarray['kl_'.$new_key]) != $value) {
                OMP_genErr('Invalid data. Possible security breach.', '', true);
            } else {
                $newvalue = $myarray['kl_'.$new_key];
                $newvalue = urldecode($newvalue);
                $newvalue = html_entity_decode($newvalue);
                $data_line[] = $newvalue;
            }
        } elseif ($type == 'kh_') {
            if (md5(OMP_SECRET.$myarray['pk_'.$new_key]) != $value) {
                OMP_genErr('Invalid data. Possible security breach.', '', true);
            } else {
                $newvalue = $myarray['pk_'.$new_key];
                $newvalue = urldecode($newvalue);
                $newvalue = html_entity_decode($newvalue);
                $data[] = $newvalue;
            }
        }
    }
    unset($key);
    unset($value);
    if ($line) {
        return array_merge($data_array, $data_line);
    } else {
        return array_merge($data_array, $data);
    }
}

/**
* Make string with primary keys
*
* @return string
* @see new.php, read.php
*/
function OMP_keyMake($no_hash = false, $no_prefix = false)
{
    global $_OMP_tbl_line;
    $getkey = '';
    if (isset($_OMP_tbl_line)) {
        global $_OMP_rec_sf, $_OMP_tbl_line_key;
        foreach ($_OMP_tbl_line_key as $key) {
            if (isset($_OMP_rec_sf[$key])) {
                $getkey .= '&';
                if (!$no_prefix) { $getkey .= 'kl_'; }
                // urlencode-stripslashes not needed 
                // if $_OMP_rec_sf[$key] is numeric
                $getkey .= $key.'='.
                    urlencode(htmlentities(stripslashes($_OMP_rec_sf[$key])));
                if (!$no_hash) { $getkey .= '&lh_'.$key.'='.
                    md5(OMP_SECRET.htmlentities($_OMP_rec_sf[$key])); }
            }
        }
    }
    global $_OMP_rec, $_OMP_tbl_key;
    foreach ($_OMP_tbl_key as $key) {
        if (isset($_OMP_rec[$key])) {
            $getkey .= '&';
            if (!$no_prefix) { $getkey .= 'pk_'; }
            // urlencode-stripslashes not needed if $_OMP_rec[$key] is numeric
               $getkey .= $key.'='.
                urlencode(htmlentities(stripslashes($_OMP_rec[$key])));
            if (!$no_hash) {
                $getkey .= '&kh_'.$key.'='.
                md5(OMP_SECRET.htmlentities($_OMP_rec[$key]));
            }
        }
    }
    return $getkey;
}

/**
* Unset array against primary keys
*
* @param array $array_one values
* @param array $array_key primary keys
* @param string $hash_only unset hash only
* @param string $add_slash add slashes
* @return string $array_one array without keys unset
* @see del.php, edit.php, edit-line.php
*/
function OMP_keyUnset($array_one, $array_key, $hash_only = false, 
    $add_slash = false)
{
    foreach ($array_key as $key) {
        $new_key = 'lh_'.$key;
        if (isset($array_one[$new_key])) {
            unset($array_one[$new_key]);
        }
        $new_key = 'kh_'.$key;
        if (isset($array_one[$new_key])) {
            unset($array_one[$new_key]);
        }
        if (!$hash_only) {
            $new_key = 'kl_'.$key;
            if (isset($array_one[$new_key])) {
                unset($array_one[$new_key]);
            }
            $new_key = 'pk_'.$key;
            if (isset($array_one[$new_key])) {
                unset($array_one[$new_key]);
            }
        }
        if ($add_slash) {
            $new_key = 'pk_'.$key;
            if (isset($array_one[$new_key])) {
                addslashes($array_one[$new_key]);
            }
        }
    }
    return $array_one;
}

/**
* Returns HREF with path_script and otpional parameter encoding
*
*/
function OMP_link($action, $no_path = false)
{
    if (OMP_ACTION_ENCODE) { $action = base64_encode($action); }
    return ($no_path) ? '?'.$action : OMP_SCRIPT.'?'.$action;
}

/**
* Make data array for edit and insert queries
*
* @param string SQL string
* @param bool if we are preparing 1 record of a set
* @return array
* @see edit.php, edit-line.php, new.php, new-line.php
*/
function OMP_makeData($sql, $multiple = false, $post = '')
{
    global $_OMP_fld_len, $_OMP_tbl_fld;
    (!empty($post)) or $post = $_POST['form'];
    $data = array();
    for (reset($post); $key = key($post); next($post)) {
        $pos = strpos($key, 'date');
        if ($pos !== false || $key == 'eta' || $key == 'rta') {
            $new_key = $key;
        } else {
            $new_key = substr($key, 4);
        }
        $type = substr($key, 0, 4);
        if (!isset($_OMP_tbl_fld[$new_key]) && 
            !isset($_OMP_fld_len[$new_key]) && 
            (($type != 'num_' && $pos === false && $key != 'eta')
            && !$multiple)) {
            OMP_genErr('Wrong field name in OMP_makeData: '.$key,
                'Please contact your system administrator');
        }
        if (isset($_OMP_tbl_fld[$new_key])) {
            $label = $_OMP_tbl_fld[$new_key];
            $value = trim($post[$key]);
            OMP_checkIllegalChars($value, $label);
            $length = isset($_OMP_fld_len[$new_key]) ? 
                $_OMP_fld_len[$new_key] : false;
         /* The following should prevent cross-side scripting
          * See Learning PHP 5 page 100 - 103
          * But it can also mess up text input
          * for records edited multiple times */
         $post[$key] = htmlentities($post[$key]);
            switch ($type) {
                case 'bol_';
                    if ($value) {
                        $value = '1';
                    } else {
                        $value = '0';
                    }
                    break;
                case 'enc_':
                    $value = urldecode($value);
                    $value = OMP_checkTxt($value, $label, $length);
                    break;
                case 'nul_':
                    $value = OMP_checkTxt($value, $label, $length, true);
                    break;
                case 'txt_':
                    $value = OMP_checkTxt($value, $label, $length);
                    break;
                case 'int_':
                    $value = OMP_checkNum($value, $label);
                    $value = number_format($value, 0, '', '');
                    break;
                case 'num_':
                    $value = OMP_checkNum($value, $label);
                    $value = number_format($value, 
                        OMP_DB_DECIMALS, OMP_DB_DEC_POINT, 
                        OMP_DB_THOUSANDS_SEP);
                    break;
                case 'prc_':
                    $value = OMP_checkNum($value, $label);
                    $value = $value / 100;
                    $value = number_format($value, 
                        OMP_DB_DECIMALS, OMP_DB_DEC_POINT, 
                        OMP_DB_THOUSANDS_SEP);
                    break;
                default:
                    if ($pos !== false || $key == 'eta' || $key == 'rta') {
                        $value = OMP_checkDateTxt($value, $label, $pos);
                        if ($value != 'NULL') {
                              // MDB2 date datatype quotes automatically
                              // $post[$key] =
                              // OMP_db_quote($post[$key], 'date');
                        }
                    } else {
                        // Check length of fields of unknown type
                        if (strlen($value) > OMP_DB_MAXSIZE) {
                            OMP_genErr($label.' cannot exceed '.
                                OMP_DB_MAXSIZE.' chars', '');
                        }
                    }
            }
            /* Look for 'whole words' */
            /* 2023-07-28 added lead and trail spaces for fields
             * such as um. Then added line without trail spaces
             * for sqls like INSERT INTO
             * To avoid having data in the wrong position of the
             * SQL make sure that schemas have column names
             * inside double quotes
             */
            $pos = strpos($sql, ' '.$label.' ');
            if (!$pos) { $pos = strpos($sql, $label); }
            if ($pos !== false) {
                $data[$pos] = $value;
            }
        }
    }
    ksort($data);
    $data = array_values($data);
    return $data;
}

/**
* Build GET string
*
* @param array $get_array array passed by GET
* @return string
* @see del.php, del-line.php, edit.php, edit-line.php
* @see new-line.php, orders-report.php, read.php
*/
function OMP_makeGet($get_array = '')
{
    $getstring = '';
    for (reset($get_array); $key = key($get_array); next($get_array)) {
        if ($get_array[$key] != '' && $key != 'action') {
            $getstring .= '&'.$key.'='.urlencode($get_array[$key]);
        }
    }
    return $getstring;
}

/**
* Return array for hidden form field
*
* @param array $hidden_array
* @return array
* @see del.php, del-line.php, edit.php, edit-line.php
*/
function OMP_makeHidden($hidden_array = '')
{
    return base64_encode(serialize($hidden_array));
//     return base64_encode(serialize(array_map('stripslashes', $hidden_array)));
}

/**
* Make SQL sort for subform
*
* @return string $sql_sub Sort for SQL of subform
* @see input-line.php, read.php
*/
function OMP_makeSqlSub()
{
    global $_OMP_get, $_OMP_sql;
    $ord = ($_OMP_get['sort_sub_type']) ? ' DESC' : ' ASC';
    $_OMP_sql['sort_record_sub'][$_OMP_get['sort_sub']] = 
        str_replace(' ?', $ord, $_OMP_sql['sort_record_sub'][$_OMP_get['sort_sub']]);
    $sql_sub = $_OMP_sql['row_line'].$_OMP_sql['sort_record_sub'][$_OMP_get['sort_sub']];
    return $sql_sub;
}

/**
* Set $sort_record[] or $sort_list[]
*
* @param array $sort_what
* @param string $sql_index
* @return string $sort_what sorted record or sorted list
* @see orders-report.php, read.php
*/
function OMP_recSortLinks($sort_what, $sql_index, $tmp = '')
{
    global $_OMP_get, $_OMP_sort_type_tmp, $_OMP_sql;
    !$_OMP_get['list'] or $tmp = '&list=1';
    !$_OMP_get['filter'] or 
         $tmp .= '&filter=1&where='.urlencode($_OMP_get['where']);
    for ($key = count($_OMP_sql[$sql_index]) - 1; $key >= 0; $key--) {
        $_OMP_sort_type_tmp = ($_OMP_get['sort'] == $key) ? 
            'sort_type='.$_OMP_get['sort_type'] : 'sort_type=0';
        $_OMP_sort_type_tmp .= '&sort='.$key.$tmp;
        $sort_what[$key] = OMP_PATH_SCRIPT.'?q='.
            base64_encode($_OMP_sort_type_tmp);
        /* not needed since Google Material icons
        $sort_what[$key] = '<a href="'.OMP_PATH_SCRIPT.'?q='.
            base64_encode($_OMP_sort_type_tmp).'">'.$sort_what[$key].'</a>'; */
    }
    return $sort_what;
}

/**
* Count records, called by OMP_sqlCountCheck() in read.php
*
* @param string SQL string to count filtered records
* @return array
* @see read.php, del.php
*/
function OMP_sqlCount($sql_filter_count = '')
{
    global $_OMP_db, $_OMP_sql, $_OMP_tables, $_OMP_tbl, $_OMP_tbl_fld;
    if ($_OMP_tbl == 'deliveries' && $_SESSION['cat'] == 1) {
        /* Magic for deliveries */
        $query = 'SELECT COUNT(del.'.$_OMP_tbl_fld['pkey'];
        $query .= ') AS count FROM '.$_OMP_sql['select_for_sup_from'];
        $query .= ' '.$sql_filter_count;
        $result = $_OMP_db->queryOne($query);
    } elseif ($_OMP_tbl == 'shippers' && $_SESSION['cat'] < 2) {
        /* Magic for shippers */
        $query = 'SELECT COUNT(ship.'.$_OMP_tbl_fld['pkey'];
        $query .= ') AS count FROM '.$_OMP_tables[$_OMP_tbl];
        $query .= ' AS ship '.$_OMP_sql['select_join'];
        $query .= ' '.$sql_filter_count;
        $result = $_OMP_db->queryOne($query);
    } else {
        $query = 'SELECT COUNT('.$_OMP_tbl_fld['pkey'];
        $query .= ') AS count FROM '.$_OMP_tables[$_OMP_tbl];
        $query .= ' '.$sql_filter_count;
        $result = $_OMP_db->queryOne($query);
    }
    return $result;
}

/**
* Returns the supplier order reference
*
* @param string supplier ID
* @return string
* @see orders.php, ordersmultiple.php
*/
function OMP_supplierRef($supplier_pkey)
{
    global $_OMP_db, $_OMP_tables, $_OMP_tbl, $_OMP_tbl_fld;
//     if (strcasecmp($_OMP_rec['supplier_pkey'], 'wellman ltd') == 0) {
//     $sql_get_last_ito = 'SELECT \'ITO\' || (max(substring('.
//     $_OMP_tbl_fld['supplier_ref'].' from 4)::int4 + 1))::text as ito FROM '.
//           $_OMP_tables[$_OMP_tbl].' WHERE substring('.
//           $_OMP_tbl_fld['supplier_ref'].' from 1 for 3) = \'ITO\''.
//    ' AND substring('.$_OMP_tbl_fld['supplier_ref'].' from 4) ~ \'^[0-9]+$\'';
    if (strcasecmp($supplier_pkey, 'wellman ltd') == 0) {
        $sql_get_last_ito = 'SELECT \'ITO\' || (max(substring('.
            $_OMP_tbl_fld['supplier_ref'].' from 4)::int4 + 1))::text 
                as ito FROM '.
            $_OMP_tables[$_OMP_tbl].' WHERE substring('.
            $_OMP_tbl_fld['supplier_ref'].' from 1 for 3) = \'ITO\'
             AND substring('.
            $_OMP_tbl_fld['supplier_ref'].' from 4) ~ \'^[0-9]+$\'
             AND "DataOrdine" NOT between \'2018-08-06\' and \'2018-10-31\'
             and "RifOrdine" NOT in 
            (\'ITO127839\', \'ITO127838\', \'ITO127837\', 
            \'ITO127836\', \'ITO127835\', \'ITO127834\', \'ITO127833\', 
            \'ITO127832\', \'ITO127831\', \'ITO127830\', \'ITO127829\', 
            \'ITO127828\', \'ITO127827\', \'ITO127826\', \'ITO127825\', 
            \'ITO127824\', \'ITO127823\', \'ITO127822\', \'ITO127821\', 
            \'ITO127820\', \'ITO127819\', \'ITO127774\', \'ITO127773\');';
        !is_array($_OMP_rec_ito = 
            $_OMP_db->queryRow($sql_get_last_ito)) or 
            $supplier_ref = $_OMP_rec_ito['ito'];
    } else {
        $supplier_ref = '';
    }
    return $supplier_ref;
}
/**
* From boolean to yes / no
*
* @param bool
* @return string
* @see orders.php, ordersmultiple.php
*/
function OMP_yesno($truefalse) {
    global $_OMP_LC;

    if ($truefalse == 't') {
        return $_OMP_LC[98];
    } else {
        return $_OMP_LC[99];
    }
}
/**
* Returns the telephone number without spaces or parenthesis
*
* @param string telephone number
* @return string
* @see clients.php, suppliers.php, etc...
*/
function OMP_html_tel_link($tel) {
    $tel = preg_replace('/[\s+()]/', '', $tel);
    return $tel;
}
?>
