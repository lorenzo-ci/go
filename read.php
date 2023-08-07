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
// $Id: read.php,v 0.8 $
//
// Read record
//
/* start of functions */
/**
* Make SQL parameters array with keys for line record
*
* @return array
*/
function OMP_keySub()
{
    global $_OMP_rec, $_OMP_tbl_key;
    $data = array();
    foreach ($_OMP_tbl_key as $key) {
        if (isset($_OMP_rec[$key])) {
            $data[] = $_OMP_rec[$key];
        } else {
            return;
        }
    }
    return $data;
}

/**
* Format filter expressions
*
* @param string $subject the string sent by the HTML filter form
* @param string $field_pattern the string pattern for regexp
* @param string $quote must we quote $subject?
* @return string the filter expression
* @see OMP_makeSql()
*/
function OMP_formatFilter($subject, $field_pattern = '', $quote = false)
{
    $operators_pattern = '<>|<=|>=|!=|<|>|=';
    $quoted_date = array();
    $norm_subject = '';
    $operator = ' =';
    $subject = trim($subject);
    if (preg_match('/not\snull/i', $subject, $matches)) {
        return ' IS NOT NULL';
    }
    if (preg_match('/null/i', $subject, $matches)) {
        return ' IS NULL';
    }
    // MSAccess style: strtr is used to translate * and ? into % and _
    if (OMP_FILTER_STYLE) {
        $norm_subject = strtr($subject, array('*' => '%', '?' => '_'));
        if ($norm_subject != $subject) $operator = ' LIKE';
        $subject = $norm_subject;
    }
    if ($field_pattern == '') {
        return $operator.' lower('.
            (($quote) ? OMP_db_quote($subject) : $subject).')';
    }
    $count_matches =
        preg_match_all('/'.$field_pattern.'/', $subject, $matches);
    if ($count_matches > 1) {
        for ($i = 0; $i <= $count_matches - 1; $i++) {
            $quoted_date[$i] = $matches[0][$i];
            if ($quote) {
                $quoted_date[$i] = OMP_db_quote($quoted_date[$i]);
            }
        }
    }
    if (!empty($quoted_date)) {
        $count_matches =
            preg_match_all('/between\s'.$field_pattern.'\sand\s'.
                $field_pattern.'/i', $subject, $matches);
        if ($count_matches) {
            $date_filter = ' BETWEEN '.$quoted_date[0].' AND '.
                $quoted_date[1];
        } else {
            $count_matches = preg_match_all('/'.$operators_pattern.
                '/', $subject, $matches);
            if ($count_matches) {
                $date_filter = ' '.$matches[0][0].' '.$quoted_date[0];
            } else {
                $date_filter = ' = '.$quoted_date[0];
            }
        }
        return $date_filter;
    } else {
        return ' = '.(($quote) ? OMP_db_quote($subject) : $subject);
    }
}

/**
* Make links for list mode
*
* @param string $_OMP_table_alias Table prefix in SQL
* @see base.php for $_SESSION['cat']
*/
function OMP_list_commands($getstring, $getstrlist, $new_get, $row_max, $row_tot)
{
    /* CHECK IF ALL PARAMETERS AND GLOBALS ARE NEEDED */

    // For $_OMP_test and $_OMP_cannot_filter see ordersnotes.php
    global $_OMP_cannot_add_line, $_OMP_cannot_filter,
        $_OMP_get, $_OMP_getkey, $_OMP_has_subform, $_OMP_html,
        $_OMP_test, $_OMP_LC, $_OMP_TPL, $tbc_pkey;

    if ($_OMP_get['list']) { // Are we in "list-mode"
        $_OMP_html['counter'] = ($_OMP_get['row_num'] + 1).
            ' - '.$row_max.$_OMP_LC[94].$row_tot;
        if ($row_tot == $row_max) {
            $next_ref = $last_ref = '';
        } else {
            $next_ref = OMP_link('row_num='.$row_max.$getstring);
            $last_ref = OMP_link('row_num='.
                    (floor($row_tot / $_SESSION['maxlist']) *
                        $_SESSION['maxlist']).$getstring);
        }
        // eval("\$option_buttons = \"".$_OMP_TPL[6]."\";");
    }
}
/**
* Make $GLOBAL['_OMP_get']['sql'], return filter for SQL record-count
*
* @param string $_OMP_table_alias Table prefix in SQL
* @return string $_OMP_rec_count_filter Filter for SQL record-count
* @see base.php for $_SESSION['cat']
*/
function OMP_makeSql($_OMP_table_alias = '')
{
    global $_OMP_fld_len, $_OMP_orders_fld, $_OMP_get,
        $_OMP_sql, $_OMP_tbl, $_OMP_tbl_fld;
    $sql_filter = $sql_filter_count = '';
    if ($_OMP_get['filter']) {
        // $_OMP_get['popup'] needed to filter records in popup windows
        // Browsing records already filtered
        if (!empty($_OMP_get['where']) && !$_OMP_get['popup']) {
            // Browsing records already filtered
            $_OMP_sql['select'] .= ' '.$_OMP_get['where'];
            // Magic for deliveries & shippers
            if (($_OMP_tbl == 'deliveries' && $_SESSION['cat'] == 1) ||
                $_OMP_tbl == 'shippers') {
                $sql_filter_count = $_OMP_get['where'];
            } else {
                $sql_filter_count =
                    str_replace($_OMP_table_alias, '', $_OMP_get['where']);
            }
        } elseif (isset($_POST['form'])) {
            for (reset($_POST['form']); $key = key($_POST['form']);
                next($_POST['form'])) {
                if ($_POST['form'][$key] <> '') {
                    $pos = strpos($key, 'date');
                    if ($pos !== false || $key == 'eta') {
                        $sql_filter .= ' AND '.$_OMP_table_alias.
                            $_OMP_tbl_fld[$key].
                            OMP_formatFilter($_POST['form'][$key],
                            '[0-9]+(\/|-)[0-9]+(\/|-)[0-9]+', true);
                    } else {
                        $new_key = substr($key, 4);
                        if (!isset($_OMP_tbl_fld[$new_key])) {
                            OMP_genErr('Wrong field name in OMP_makeSql: '.
                                $key, 'Please contact
                                    your system administrator');
                        }
                        $type = substr($key, 0, 4);
                        $label = (isset($_OMP_tbl_fld[$new_key]) ?
                            $_OMP_tbl_fld[$new_key] :
                            $_POST['form'][$key]);
                        if ($type == 'enc_') {
                            OMP_checkTxt($_POST['form'][$key], $label,
                                $_OMP_fld_len[$new_key]);
                            $_POST['form'][$key] =
                                urldecode($_POST['form'][$key]);
                            $sql_filter .= ' AND lower('.
                                $_OMP_table_alias.$_OMP_tbl_fld[$new_key].')'.
                                OMP_formatFilter($_POST['form'][$key],'', true);
                        } elseif ($type == 'txt_') {
                            OMP_checkTxt($_POST['form'][$key], $label,
                                $_OMP_fld_len[$new_key]);
                            if ($_POST['form'][$key] != '%' &&
                                (OMP_FILTER_STYLE &&
                                $_POST['form'][$key] != '*')) {
                                $sql_filter .= ' AND lower('.
                                    $_OMP_table_alias.$_OMP_tbl_fld[$new_key].')'.
                                    OMP_formatFilter($_POST['form'][$key],'',
                                        true);
                            }
                        } elseif ($type == 'num_' || $type == 'int_') {
                            OMP_checkNum($_POST['form'][$key], $label);
                            $sql_filter .= ' AND '.
                                $_OMP_table_alias.$_OMP_tbl_fld[$new_key].
                                OMP_formatFilter($_POST['form'][$key],
                                    '\d{0,9}');
                        } else {  // includes $type == 'bol_'
                            $sql_filter .= ' AND '.
                                $_OMP_table_alias.$_OMP_tbl_fld[$new_key].' = '.
                                OMP_db_quote($_POST['form'][$key]);
                        }
                    }
                }
            }
            if ($sql_filter != '') {
                if ($_OMP_get['popup']) {
                    // Don't change $_OMP_get['where'] if popup window
                    $sql_filter = ' '.'WHERE'.substr($sql_filter, 4);
                } else {
                    $_OMP_get['where'] = 'WHERE'.substr($sql_filter, 4);
                    $sql_filter = ' '.$_OMP_get['where'];
                }
                // Magic for deliveries
                if ($_OMP_tbl == 'deliveries' && $_SESSION['cat'] == 1) {
                    $tmp_var = ' AND lower(oi.'.
                        $_OMP_orders_fld['supplier_pkey'].
                        ') = lower('.OMP_db_quote($_SESSION['id']).') ';
                    ($_OMP_get['popup']) or $_OMP_get['where'] .= $tmp_var;
                    $sql_filter .= $tmp_var;
                    $sql_filter_count = $sql_filter;
                } elseif ($_OMP_tbl == 'shippers' && $_SESSION['cat'] < 2) {
                    $sql_filter_count = $sql_filter;
                } else {
                    $sql_filter_count =
                        str_replace($_OMP_table_alias, '', $sql_filter);
                }
                $_OMP_sql['select'] .= $sql_filter;
            } else {
                // There is actually no filter in place, so we reset filter
                $_OMP_get['filter'] = false;
            }
        } else {
            // There is actually no filter in place, so we reset filter
            $_OMP_get['filter'] = false;
        }
    } else { /* $_OMP_get['filter'] is false */
        switch ($_SESSION['cat']) {
            case 0: // Filter for clients
                if (isset($_OMP_tbl_fld['client_pkey'])) {
                    $new_key = $_OMP_tbl_fld['client_pkey'];
                } elseif ($_OMP_tbl == 'clients') {
                    $new_key = $_OMP_tbl_fld['pkey'];
                }
                break;
            case 1: // Filter for suppliers
                if (isset($_OMP_tbl_fld['supplier_pkey'])) {
                    $new_key = $_OMP_tbl_fld['supplier_pkey'];
                } elseif ($_OMP_tbl == 'suppliers') {
                    $new_key = $_OMP_tbl_fld['pkey'];
                } elseif ($_OMP_tbl == 'deliveries') { // Magic for deliveries
                    $new_key = $_OMP_orders_fld['supplier_pkey'];
                }
                break;
            default: // No filter for operators (case 2) and admins (case 3)
                $new_key = '';
                $sql_filter_count = '';
        }
        if (!empty($new_key)) {
            $new_key .= ') = lower('.OMP_db_quote($_SESSION['id']).')';
            // Magic for deliveries
            if ($_OMP_tbl == 'deliveries' && $_SESSION['cat'] == 1){
                $sql_filter = ' WHERE lower('.$new_key;
            } else {
                $sql_filter = ' WHERE lower('.$_OMP_table_alias.$new_key;
            }
            $sql_filter_count = ' WHERE lower('.$new_key;
            $_OMP_sql['select'] .= $sql_filter;
        }
    }
    if (isset($_OMP_get['sort'])) { // Sorting
        if (isset($_OMP_sql['sort_record'])) {
            if ($_OMP_get['list']) {
                $_OMP_sql['select'] .=
                    $_OMP_sql['sort_list'][$_OMP_get['sort']];
            } else {
                $_OMP_sql['select'] .=
                    $_OMP_sql['sort_record'][$_OMP_get['sort']];
            }
        } else {
            $_OMP_sql['select'] .= $_OMP_sql['sort_'.$_OMP_get['sort']];
        }
        $_OMP_sql['select'] = str_replace(' ?',
            (($_OMP_get['sort_type']) ? ' DESC' : ' ASC'), $_OMP_sql['select']);
    } else {
        if (isset($_OMP_sql['sort_default'])) {
            $_OMP_sql['select'] .= ' '.$_OMP_sql['sort_default'];
        } else {
            $_OMP_sql['select'] .= ' ORDER BY '.$_OMP_tbl_fld['pkey'];
        }
    }
    $_OMP_get['sql'] = $_OMP_sql['select'];
    return $sql_filter_count;
}

/* remove when OMP_toolbar() is ready */
function OMP_navBar($getstring, $getstrlist, $new_get, $row_max, $row_tot)
{
    // For $_OMP_test and $_OMP_cannot_filter see ordersnotes.php
    global $_OMP_cannot_add_line, $_OMP_cannot_filter,
        $_OMP_get, $_OMP_getkey, $_OMP_has_subform, $_OMP_html,
        $_OMP_test, $_OMP_LC, $_OMP_TPL, $tbc_pkey;
    /* No nav-bar for print page */
    if ($_OMP_cannot_filter ||
        $_OMP_get['action'] == 'print') {
        $_OMP_html['navbar'] = '';
        return;
    } else {
        if (isset($_OMP_test)) {
            $getstring .= $_OMP_test;
            $getstrlist .= $_OMP_test;
        } else {
            $_OMP_test = '';
        }
        $new_ref = OMP_link('action=new'.$_OMP_test);
        if ($_OMP_get['filter'] && empty($_OMP_cannot_filter)) {
            unset($new_get['filter']);
//             $new_get['unfilter'] = '1';
            unset($new_get['sql']);
            unset($new_get['where']);
            $all_ref = OMP_link(OMP_makeGet($new_get));
        } else {
            $all_ref = '';
        }
        if (0 == $_OMP_get['row_num']) {
            $first_ref = $prev_ref = '';
        } else {
            $first_ref = OMP_link('row_num=0'.$getstring);
            $prev_ref = OMP_link('row_num='.
                    (($_OMP_get['list'])
                        ? $_OMP_get['row_num'] - $_SESSION['maxlist']
                        : $_OMP_get['row_num'] - 1).$getstring);
        }
//        if ($tbc_pkey || $_OMP_get['list']) {
        if ($_OMP_get['list']) {
            $list_ref = '';
        } else {
            $list_ref = ($_OMP_get['list'] || $row_tot < 2) ? $_OMP_LC[19] :
                OMP_link('list=1'.$getstrlist);
        }
        // Filter link - no link if user is client
        // or table has just one record
        // For $_OMP_cannot_filter see ordersnotes.php
        if ((1 == $row_tot && !$_OMP_get['filter'])
            ||  !empty($_OMP_cannot_filter)) {
                $filter_ref = '';
        } else {
            $_OMP_html['filter_ref'] = $filter_ref =
                OMP_link('list='.$_OMP_get['list'].'&action=filter');
        }
        if ($_OMP_get['list']) { // Are we in "list-mode"
            $counter = ($_OMP_get['row_num'] + 1).' - '.$row_max.'/'.$row_tot;
            if ($row_tot == $row_max) {
                $next_ref = $last_ref = '';
            } else {
                $next_ref = OMP_link('row_num='.$row_max.$getstring);
                $last_ref = OMP_link('row_num='.
                        (floor($row_tot / $_SESSION['maxlist']) *
                            $_SESSION['maxlist']).$getstring);
            }
            eval("\$option_buttons = \"".$_OMP_TPL[6]."\";");
        } else {
            // For admins and operators only
            if ($_SESSION['cat'] == 3 || $_SESSION['cat'] == 2) {
                $multi_ref = OMP_link('action=multi'.$_OMP_test);
                $print_ref = OMP_link(
                        'action=recptsel'.$_OMP_getkey.$getstring
                    );
                $edit_ref = OMP_link('action=edit'.$_OMP_getkey.
                        '&row_num='.$_OMP_get['row_num'].$getstring);
                // Link to delete record. Careful with $sql
                // if we are deleting the only record filtered
                $delete_ref = OMP_link('action=del&row_num='.
                        ((($_OMP_get['row_num'] - 1) < 0)
                            ? '0'
                            : ($_OMP_get['row_num'] - 1)).
                        $_OMP_getkey.$getstring.'&filter='.$_OMP_get['filter']);
                if (!empty($_OMP_has_subform) && empty($_OMP_cannot_add_line)) {
                    $new_detail_ref = ''.OMP_link('action=newdetail&row_num='.
                        $_OMP_get['row_num'].$_OMP_getkey.$getstring);
                        eval("\$new_detail_button = \"".
                            $_OMP_TPL[16]."\";");
                } else {
                    $new_detail_ref = '';
                    $new_detail_button = '';
                }
            } else {
                $multi_ref = '';
            }
            $counter = ($_OMP_get['row_num'] + 1).'/'.$row_tot;
            if ($_OMP_get['row_num'] + 1 >= $row_max) {
                $next_ref = $last_ref = '';
            } else {
                $next_ref = OMP_link('row_num='.($_OMP_get['row_num'] + 1).
                    $getstring);
                $last_ref = OMP_link('row_num='.($row_tot - 1).$getstring);
            }
            eval("\$option_buttons = \"".$_OMP_TPL[7]."\";");
        }
    }
    eval("\$nav_buttons = \"".$_OMP_TPL[5]."\";");
    eval("\$_OMP_html['navbar'] = \"".$_OMP_TPL[13]."\";");
}

/**
* Count records and returns array with $row_tot, $row_max, $new_get,
* $getstring, $getstrlist
*
* @param string SQL string to count [filtered] records
* @return array
*/
function OMP_sqlCountCheck($sql_filter_count = '')
{
    global $_OMP_LC, $_OMP_get, $_OMP_url;
    /* Record count OMP_sqlCount() defined in functions.php */
    $row_tot = OMP_sqlCount($sql_filter_count);
    if ($row_tot > 0) {
        // Max row number in list
        $row_max = $_OMP_get['row_num'] + $_SESSION['maxlist'];
        $row_max = ($row_max >= $row_tot) ? $row_tot : $row_max;
    } else { // No records
        if ($_OMP_get['filter']) {
            // Record not found
            $_OMP_get['filter'] = false;
            OMP_genErr($_OMP_LC[83]);
            // $_OMP_get['where'] = '';
            // OMP_genErr($_OMP_LC[83], 'Query: '.$_OMP_get['sql']);
        } else {
            // If no filter then redirect to insert new record
            // Use absolute URI and standard ampersand
            $_OMP_url = OMP_link('action=new&empty_table=1');
            OMP_redir('', true, false);
        }
//         } elseif ($_SESSION['cat'] > 1) {
//             // If no filter then redirect to insert new record
//             // Use absolute URI and standard ampersand
//             OMP_redir('action=new&empty_table=1', true, false);
//         } else {
//             // Empty table
//             OMP_genErr($_OMP_LC[90], 'Query: '.$_OMP_get['sql']);
//         }
    }
    // Main GET elements go in $getstring, skip row_num, id and hash_id
    $new_get = $_OMP_get;
    unset($new_get['row_num']);
    unset($new_get['pkey']);
    unset($new_get['hash_pkey']);
    unset($new_get['sql']);
    // After adding new record or pop-up
    if ($_OMP_get['read_new']) {
        unset($new_get['read_new']);
        unset($new_get['filter']);
    }
    $getstring = OMP_makeGet($new_get);
    // $getstrlist = str_replace ('&list=1', '', $getstring);
    $getstrlist = str_replace ('list=1', '', $getstring);
    return array($row_tot, $row_max, $new_get, $getstring, $getstrlist);
}

/**
* Make toolbar
*/

/* REMOVE OMP_navBar WHEN THIS IS FINISHED */
function OMP_toolbar($getstring, $getstrlist, $new_get, $row_max, $row_tot)
{
    // For $_OMP_test and $_OMP_cannot_filter see ordersnotes.php
    global $_OMP_cannot_add_line, $_OMP_cannot_filter,
        $_OMP_get, $_OMP_getkey, $_OMP_has_subform, $_OMP_html,
        $_OMP_test, $_OMP_LC, $_OMP_TPL, $tbc_pkey;

    // Filter link - no link if user is client
    // or table has just one record
    // For $_OMP_cannot_filter see ordersnotes.php
    if (1 == $row_tot) $_OMP_get['list'] = '';
    if ( $_OMP_get['action'] == 'print') {
        $_OMP_html['navbar'] = '';
        return;
    }
    /* are we admins or operators? */
    $superuser = ($_SESSION['cat'] == 3 || $_SESSION['cat'] == 2);
    /* array $_OMP_tools contains all the tools
     * with numerical index for positioning
     * it's sorted after it's populated */
    $_OMP_tools = array();
    if ($_OMP_get['list']) {
        /* tools only for lists */
        /* for single records, filter and unfilter
         * are in the options menu */
        $_OMP_tools += ['list_counter' => 0];
        if ($row_tot > 1 || empty($_OMP_cannot_filter)) {
            $_OMP_tools += ['filter' => 4];
        }
        if ($_OMP_get['filter']) {
            $_OMP_tools += ['filter_off' => 5];
        }
        // For admins and operators only
        if ($superuser) {
            $_OMP_tools += ['new' => 6];
        }
    } else {
        /* tools only for single record */
        $_OMP_tools += ['counter' => 0];
        $_OMP_tools += ['list' => 3];
    }
    $_OMP_tools += ['before' => 1];
    $_OMP_tools += ['next' => 2];
    /* sorts the tools in numerical order */
    asort($_OMP_tools);
    $_OMP_html['toolbar_tools'] =
        $_OMP_html['counter'] = '';
    foreach ($_OMP_tools as $tool => $index) {
        switch ($tool) {
            case 'list_counter';
                $row_curr_f = number_format(
                    $_OMP_get['row_num'] + 1,
                    0,
                    $_SESSION['dp'],
                    $_SESSION['ts']
                );
                $row_max_f = number_format(
                    $row_max,
                    0,
                    $_SESSION['dp'],
                    $_SESSION['ts']
                );
                $row_tot_f = number_format(
                    $row_tot,
                    0,
                    $_SESSION['dp'],
                    $_SESSION['ts']
                );
                $_OMP_html['counter'] = $row_curr_f;
                $_OMP_html['counter'] .= ' - '.$row_max_f.' ';
                $_OMP_html['counter'] .= $_OMP_LC[94].' ';
                $_OMP_html['counter'] .= $row_tot_f;
                break;
            case 'counter';
                $row_curr_f = number_format(
                    $_OMP_get['row_num'] + 1,
                    0,
                    $_SESSION['dp'],
                    $_SESSION['ts']
                );
                $row_tot_f = number_format(
                    $row_tot,
                    0,
                    $_SESSION['dp'],
                    $_SESSION['ts']
                );
                $_OMP_html['counter'] = $row_curr_f.' ';
                $_OMP_html['counter'] .= $_OMP_LC[94].' ';
                $_OMP_html['counter'] .= $row_tot_f;
                break;
            case 'before';
                /* action string */
                if (0 == $_OMP_get['row_num']) {
                    if ($_OMP_get['list']) {
                        $action = '&list=1';
                        $action .= '&row_num=0';
                    }
                    if ($_OMP_get['filter']) {
                        $action = '&filter=1';
                        $action .= '&row_num=0';
                        $action .= '&where='.
                            $_OMP_get['where'];
                    }
                } else {
                    if ($_OMP_get['list']) {
                        $action = '&list=1';
                        $action .= '&row_num=';
                        $action .=
                            $_OMP_get['row_num'] -
                            $_SESSION['maxlist'];
                    } else {
                        $action = '&row_num=';
                        $action .= ($_OMP_get['row_num'] - 1);
                        if ($_OMP_get['filter']) {
                            $action .= '&filter=1';
                            $action .= '&where='.
                                $_OMP_get['where'];
                        }
                    }
                }
                if (isset($action)) {
                    $action .= '&action=read';
                } else {
                    $action = '&action=read';
                };
                /* end of action string */
                $_OMP_tool_href =
                    OMP_link($action);
                $_OMP_tool_aria_label = $_OMP_LC[16];
                $_OMP_tool_icon = 'navigate_before';
                eval("\$_OMP_html['toolbar_tools'] .= \"".
                    $_OMP_TPL[21]."\";");
                break;
            case 'next';
                $action = 'action=read';
                if ($_OMP_get['list']) {
                    $action .= '&list=1';
                    if ($row_tot == $row_max) {
                        $action .= '&row_num='.
                            $_OMP_get['row_num'];
                        if ($_OMP_get['filter']) {
                            $action .= '&filter=1';
                            $action .= '&where='.
                                $_OMP_get['where'];
                        }
                    } else {
                        $action .= '&row_num=';
                        $action .= $_OMP_get['row_num'] +
                            $_SESSION['maxlist'];
                        $action .= $getstrlist;
                    }
                } else {
                    if ($_OMP_get['row_num'] + 1 >= $row_max) {
                        $action .= '&row_num=';
                        $action .= $_OMP_get['row_num'];
                        if ($_OMP_get['filter']) {
                            $action .= '&filter=1';
                            $action .= '&where='.
                                $_OMP_get['where'];
                        }
                    } else {
                        $action .= '&row_num='.
                            ($_OMP_get['row_num'] + 1);
                        if ($_OMP_get['filter']) {
                            $action .= '&filter=1';
                            $action .= '&where='.
                                $_OMP_get['where'];
                        }
                    }
                }
                $_OMP_tool_href = OMP_link($action);
                $_OMP_tool_aria_label = $_OMP_LC[17];
                $_OMP_tool_icon = 'navigate_next';
                eval("\$_OMP_html['toolbar_tools'] .= \"".
                    $_OMP_TPL[21]."\";");
                break;
            case 'list':
                $action = '&list=1&action=read';
                /* this works only if OMP_MAXLIST
                 * is 10 or a multiple of 10, I think */
                $row_num = $_OMP_get['row_num'] / OMP_MAXLIST;
                $rom_num = intval($row_num) * OMP_MAXLIST;
                $action .= '&row_num='.$rom_num;

                if ($_OMP_get['filter']) {
                    $action .= '&filter=1';
                    $action .= '&where='.$_OMP_get['where'];
                }
                $_OMP_tool_href =
                    OMP_link($action);
                $_OMP_tool_aria_label = $_OMP_LC[19];
                $_OMP_tool_icon = 'list';
                eval("\$_OMP_html['toolbar_tools'] .= \"".
                    $_OMP_TPL[21]."\";");
                break;
            case 'filter':
                $action = '&list='.$_OMP_get['list'];
                $action = '&action=filter';
                $_OMP_tool_href = OMP_link($action);
                $_OMP_tool_aria_label = $_OMP_LC[20];
                $_OMP_tool_icon = 'filter_alt';
                eval("\$_OMP_html['toolbar_tools'] .= \"".
                    $_OMP_TPL[21]."\";");
                break;
            case 'filter_off';
                unset($new_get['filter']);
                unset($new_get['sql']);
                unset($new_get['where']);
                $_OMP_tool_href = OMP_link(OMP_makeGet($new_get));
                $_OMP_tool_aria_label = $_OMP_LC[34];
                $_OMP_tool_icon = 'filter_alt_off';
                eval("\$_OMP_html['toolbar_tools'] .= \"".
                    $_OMP_TPL[21]."\";");
                break;
            case 'new':
                $action = '&list='.$_OMP_get['list'];
                $action .= '&action=new';
                $_OMP_tool_href = OMP_link($action);
                $_OMP_tool_aria_label = $_OMP_LC[21];
                $_OMP_tool_icon = 'add';
                eval("\$_OMP_html['toolbar_tools'] .= \"".
                    $_OMP_TPL[21]."\";");
                break;
        }
    }
    unset($tool);
    /* array $_OMP_options contains the items
     * with numerical index for positioning
     * it's sorted after it's populated */
    $_OMP_options = array();
    if ($_OMP_get['list'] || !$superuser)  {
        /* don't show options_menu when in list mode
           or when logged in as simple user */
        $_OMP_html['options_menu'] = '';
    } else {
        /* For superusers */
        $_OMP_options += ['edit' => 1];
        if (!empty($_OMP_has_subform) &&
            empty($_OMP_cannot_add_line)) {
                $_OMP_options += ['new_detail' => 2];
        }
        $_OMP_options += ['new' => 3];
        if ($row_tot > 1 || empty($_OMP_cannot_filter)) {
            $_OMP_options += ['filter' => 4];
        }
        if ($_OMP_get['filter']) {
            $_OMP_options += ['filter_off' => 5];
        }
        /* multi and print only with orders */
        if (OMP_SCRIPT == 'orders.php') {
            $_OMP_options += ['multi' => 6];
            $_OMP_options += ['print' => 7];
        }
        $_OMP_options += ['delete' => 8];
        /* sorts the options in numerical order */
        // asort($_OMP_options);
        $_OMP_html['options_list'] = '';
        $_OMP_option_target = '_self';
        foreach ($_OMP_options as $option => $index) {
            switch ($option) {
                case 'edit':
                    $action = '&action=edit'.$_OMP_getkey;
                    // $action .= '&row_num='.$_OMP_get['row_num'];
                    $action .= '&'.$getstring;
                    $_OMP_option_href = OMP_link($action);
                    $_OMP_option_aria_label = $_OMP_LC[33];
                    eval("\$_OMP_html['options_list'] .= \"".
                        $_OMP_TPL[23]."\";");
                    break;
                case 'new':
                    $action = '&list='.$_OMP_get['list'];
                    $action .= '&action=new';
                    $_OMP_option_href = OMP_link($action);
                    $_OMP_option_aria_label = $_OMP_LC[21];
                    eval("\$_OMP_html['options_list'] .= \"".
                        $_OMP_TPL[23]."\";");
                    break;
                case 'new_detail':
                    $action = '&action=newdetail';
                    $action .= $_OMP_getkey.'&'.$getstring;
                    $_OMP_option_href = OMP_link($action);
                    $_OMP_option_aria_label = $_OMP_LC[28].' '.$_OMP_LC[23];
                    eval("\$_OMP_html['options_list'] .= \"".
                        $_OMP_TPL[23]."\";");
                    break;
                case 'multi':
                    $_OMP_option_href = OMP_link('action=multi');
                    $_OMP_option_aria_label = $_OMP_LC[4000];
                    eval("\$_OMP_html['options_list'] .= \"".
                        $_OMP_TPL[23]."\";");
                    break;
                case 'filter':
                    $action = '&list='.$_OMP_get['list'];
                    $action .= '&action=filter';
                    $_OMP_option_href = OMP_link($action);
                    $_OMP_option_aria_label = $_OMP_LC[20];
                    eval("\$_OMP_html['options_list'] .= \"".
                        $_OMP_TPL[23]."\";");
                    break;
                case 'filter_off';
                    unset($new_get['filter']);
                    unset($new_get['sql']);
                    unset($new_get['where']);
                    $_OMP_option_href = OMP_link(OMP_makeGet($new_get));
                    $_OMP_option_aria_label = $_OMP_LC[34];
                    eval("\$_OMP_html['options_list'] .= \"".
                        $_OMP_TPL[23]."\";");
                    break;
                case 'print':
                    $_OMP_option_target = '_blank';
                    $_OMP_option_href =
                        OMP_link('action=recptsel'.$_OMP_getkey.$getstring);
                    $_OMP_option_aria_label = $_OMP_LC[26];
                    eval("\$_OMP_html['options_list'] .= \"".
                        $_OMP_TPL[23]."\";");
                    $_OMP_option_target = '_self';
                    break;
                case 'delete':
                    $action = '&action=del';
                    $action .= $_OMP_getkey.'&'.$getstring;
                    $action .=
                        (empty($_OMP_get['filter'])) ? '' : '&filter=1';
                    $_OMP_option_href = OMP_link($action);
                    $_OMP_option_aria_label = $_OMP_LC[22];
                    eval("\$_OMP_html['options_list'] .= \"".
                        $_OMP_TPL[23]."\";");
                    break;
            }
        }
        eval("\$_OMP_html['options_menu'] = \"".$_OMP_TPL[22]."\";");
    }
    /* Template 20 requires toolbar_tools and options_menu */
    eval("\$_OMP_html['toolbar'] = \"".$_OMP_TPL[20]."\";");
}
/* End of functions */

// keyFind($_OMP_get);
foreach ($_OMP_tbl_key as $key) {
    if (isset($_OMP_get[$key])) {
        if ('pkey' === $key) {
            if (-1 === $_OMP_fld_len['pkey']) {
            // pkey is numeric. Good for clientcontacts
            // This does not work because if
            // pkey is a numeric string then it is not
            // properly quoted in sql: if (is_numeric($_OMP_get[$key])) {
                $new_key = 'num_'.$key;
            } else {
                // Good for primary keys that
                // are strings containing a number
                // Not good for clientcontacts
                $new_key = 'enc_'.$key;
            }
        } else {
            if (is_numeric($_OMP_get[$key])) {
                $new_key = 'num_'.$key;
            } elseif (is_string($_OMP_get[$key])) {
                if ('date' === $key) {
                    $new_key = $key;
                } else {
                    $new_key = 'enc_'.$key;
                }
            }
        }
        empty($_OMP_get[$key]) or
            // type txt, num etc...???
            $_POST['form'][$new_key] = $_OMP_get[$key];
    }
}
/* @see base.php */
OMP_load();
$_OMP_html['include'] = '';
// Current row number
$_OMP_get['row_num'] = (empty($_OMP_get['row_num']) ||
    $_OMP_get['row_num'] <= 0) ? 0 : $_OMP_get['row_num'];
// After adding new record or pop-up
!$_OMP_get['read_new'] or $_OMP_get['filter'] = true;
// Master record default sort
isset($_OMP_get['sort']) or $_OMP_get['sort'] = $_OMP_sort['default'];
// Values are 0 (ascending sort) or 1 (descending sort)
isset($_OMP_get['sort_type']) or
    $_OMP_get['sort_type'] = $_OMP_sort['type'];
list($row_tot, $row_max, $new_get, $getstring, $getstrlist) =
    OMP_sqlCountCheck(OMP_makeSql($_OMP_table_alias));
$_OMP_get['sort_type']++;
if ($_OMP_get['sort_type'] >= 2) { $_OMP_get['sort_type'] = 0; }
/* mdc table for list records and for subform table */
$_OMP_html['aria_label'] = $_OMP_LC['19'].' '.$_OMP_LC[$_OMP_title];
// Fetch records
if ($_OMP_get['list']) {
    /* Are we in "list-mode" */
    /* 04/08/2023 se rimetto setLimit, prima pagina tutto ok,
     * secoda pagina limit 15 offset 15 che per pgsql va bene
     * ma per pear_db da seek: [Error message: tried to seek to
     * an invalid row number (15)]
     * sql finisce con ORDER BY oi."DataOrdine" DESC, oi."IDOrdine"
     * DESC LIMIT 15 OFFSET 15 ho provato ad aggiungere
     * l'ultima riga direttamente alla sql_filter
     * ma l'errore resta. non so cosa fare.
     */
    // $_OMP_db->setLimit($_OMP_get['row_num'], $row_max - $_OMP_get['row_num']);
    $_OMP_db_result = $_OMP_db->query($_OMP_get['sql']);
    // Create $_OMP_sort_list
    if (!empty($_OMP_sort_idx)) {
        foreach ($_OMP_sort_idx as $sort_key => $lc_key) {
            $_OMP_html_header[$sort_key] =
            $_OMP_sort_list[$sort_key] = $_OMP_LC[$lc_key];
        }
    }
    // HTML links to sort records
    $_OMP_sort_list = OMP_recSortLinks($_OMP_sort_list, 'sort_list');
    eval ("\$_OMP_html['list'] = \"".
        $_OMP_TPL[$_OMP_list_header_tpl]."\";");
    $rn = 0;
    for ($record_count = $_OMP_get['row_num'];
        $record_count < $row_max; $record_count++) {
        $_OMP_rec = $_OMP_db_result->fetchRow(
            MDB2_FETCHMODE_ASSOC,
            $record_count
        );
        $_OMP_getkey = OMP_keyMake();
        OMP_makeVars();
        // For $_OMP_test see ordersnotes.php
        isset($_OMP_test) or $_OMP_test = '';
        $_OMP_html['rec_link'] = OMP_link(
            'row_num='.($_OMP_get['row_num'] + $rn).
            $getstrlist.$_OMP_test
        );
        $row_id = 'row'.$rn;
        eval(
            "\$_OMP_html['list'] .= \"".
            $_OMP_TPL[$_OMP_list_rec_tpl]."\";"
        );
        $rn++;
    }
    $_OMP_db_result->free();
    OMP_list_commands(
        $getstring,
        $getstrlist,
        $new_get,
        $row_max,
        $row_tot
    );
    eval(
        "\$_OMP_html['include'] .= \"".
        $_OMP_TPL[$_OMP_list_tpl]."\";"
    );
} else {
    $_OMP_db_result = $_OMP_db->query($_OMP_get['sql']);
    $_OMP_rec =
        $_OMP_db_result->fetchRow(MDB2_FETCHMODE_ASSOC, $_OMP_get['row_num']);
    $_OMP_getkey = OMP_keyMake();
    if (!empty($_OMP_has_subform)) { $array_sub = OMP_keySub(); }
    OMP_makeVars();
    // Create $sort_record
    if (!empty($_OMP_sort_idx)) {
        foreach ($_OMP_sort_idx as $sort_key => $lc_key) {
            $sort_record[$sort_key] = $_OMP_LC[$lc_key];
        }
    }
    // Are we in a popup-window
    OMP_recSortLinks($sort_record, 'sort_record');
    //** BEGIN SUBFORM **
    if (!empty($_OMP_has_subform)) {
        // Default sort
        isset($_OMP_get['sort_sub']) or
            $_OMP_get['sort_sub'] = $_OMP_sort_default_sub;
        // Sort order: $_OMP_get['sort_sub_type']
        // Values are 0 (ascending sort) or 1 (descending sort)
        isset($_OMP_get['sort_sub_type']) or
            $_OMP_get['sort_sub_type'] = $_OMP_sort_type_default_sub;
        if (OMP_SCRIPT == 'orders.php') {
            /* check if order lines have notes
             * see OMP_makeVarsLine in orders.php */
            $query = 'SELECT COUNT(*) FROM '.
                $_OMP_tables[$_OMP_tbl_line].' WHERE '.
                $_OMP_tbl_fld_line['oi_pkey'].' = \''.$array_sub[0].
                '\' AND '.$_OMP_tbl_fld_line['note'].' IS NOT NULL';
            $data = $_OMP_db->queryAll($query);
            $_OMP_column_note = ($data[0]['count'] > 0) ? true : false;
        } else {
            $_OMP_column_note = false;
        }
        unset($query); unset ($data);
        /* OMP_makeSqlSub is defined in functions.php */
        $_OMP_get['sql_sub'] = OMP_makeSqlSub();
        $prepared = $_OMP_db->prepare($_OMP_get['sql_sub']);
        $_OMP_db_result_sf = $prepared->execute($array_sub);
        if ($_OMP_db_result_sf->numRows() > 0) {
            if (!$_OMP_get['popup']) {
                $tmp_var_b = $_OMP_get['sort_sub_type'];
                $_OMP_get['sort_sub_type']++;
                if ($_OMP_get['sort_sub_type'] >= 2) {
                    $_OMP_get['sort_sub_type'] = 0;
                }
                // recordSortLinkSub(); // Links to sort subform
                $getstring = 'row_num='.$_OMP_get['row_num'].$getstring;
                /* Create $sort_record_sub */
                for ($key = count($_OMP_sql['sort_record_sub']) - 1;
                    $key >= 0; $key--) {
                    $_OMP_sort_type_tmp = $getstring.'&sort_sub='.$key;
                    $_OMP_sort_type_tmp .= ($_OMP_get['sort_sub'] == $key)
                        ? '&sort_sub_type='.$_OMP_get['sort_sub_type']
                        : '&sort_sub_type=0';
                    $_OMP_html['sort_link'] =
                        OMP_link($_OMP_sort_type_tmp);
                    // eval("\$_OMP_html_sort_record_sub[\$key] = \"".
                    //     $_OMP_TPL[73]."\";");
                }
            } else {
                /* put column name in $sort_record_sub */
                if (!empty($_OMP_sub_sort_idx)) {
                    foreach ($_OMP_sub_sort_idx as $sort_key => $lc_key) {
                        $_OMP_html_sort_record_sub[$sort_key] = '';
                    }
                }
            }
            // See template 719 Order Print Sub Record
            $_OMP_html['discount'] = '';
            $tmp_var_c = '';
            // TBCs pkey is ClientID which by now is set in $_OMP_rec['pkey']
            // as a URI to open the Clients pop-up window
            // $tbc_pkey is set as empty in tbcs.php
            if (isset($tbc_pkey)) {
                $OMP_rec_pkey = $_OMP_rec['pkey'];
                $_OMP_rec['pkey'] = $tbc_pkey;
            }
            if ($_OMP_get['action'] == 'print' && $tmp_var_c <= 7) {
                eval("\$_OMP_html['table_body'] = \"".
                        $_OMP_TPL[$_OMP_sub_header_tpl]."\";");
            } else {
                $_OMP_html['table_body'] = $_OMP_html['list'] = '';
            }
            $line_number = 0;
            while ($_OMP_rec_sf = $_OMP_db_result_sf->fetchRow()) {
                $getkeyline = OMP_keyMake();
                // Links to edit and delete order-lines.
                // Edit and delete link only for admins and operators!
                $tools = 'tools'.$line_number;
                $eta = 'eta'.$line_number;
                if (($_SESSION['cat'] != 0 && $_SESSION['cat'] != 1)
                    && !$_OMP_get['popup'] &&
                        !empty($_OMP_admin_tpl)) {
                    $tmp_var_b = '';
                    $tmp_var_b = $getstring.'&sort_sub='.
                                $_OMP_get['sort_sub'].'&sort_sub_type='.
                                $tmp_var_b.$getkeyline;
                    $detail_edit_ref =
                        OMP_link('action=editline&'.$tmp_var_b);
                    $detail_delete_ref =
                        OMP_link('action=delline&'.$tmp_var_b);
                } else {
                    $detail_edit_ref = $detail_delete_ref = '';
                }
                if ($_OMP_get['action'] <> 'print') {
                    if (isset($_OMP_admin_tpl) && !$_OMP_get['popup']) {
                        eval("\$admin = \"".
                            $_OMP_TPL[$_OMP_admin_tpl]."\";");
                    } else { // for invoices.php read
                        $admin = '';
                    }
                }
                OMP_makeVarsLine();
                /* record detail menu
                    * only for admins and operators */
                empty($_OMP_rec_sf['discount']) ?
                    $_OMP_html['discount'] = '' :
                    $_OMP_html['discount'] = ' LESS '.
                    $_OMP_rec_sf['discount'].'% DISCOUNT';
                $tmp_var_a = $_OMP_html['table_body'];
                $tmp_var_b = $_OMP_rec_sf;
                /* if line records have notes then add
                * the table column to display them */
                if ($_OMP_column_note) {
                    eval("\$_OMP_html['note_record'] = \"".
                        $_OMP_TPL[722]."\";");
                }
                eval("\$_OMP_html['table_body'] .= \"".
                    $_OMP_TPL[$_OMP_sub_rec_tpl]."\";");
                $tmp_var_c++;
                $line_number++;
            }
            /* if line records have notes then add
            * the table column to display them */
            if ($_OMP_column_note) {
                eval("\$_OMP_html['note_header'] = \"".
                    $_OMP_TPL[721]."\";");
            }
            eval("\$_OMP_html['table_body'] = \"".
                $_OMP_TPL[$_OMP_sub_header_tpl]."\";");
            if (isset($tbc_pkey)) {
                $_OMP_rec['pkey'] = $OMP_rec_pkey;
            }
            // Order print
            if ($_OMP_get['action'] == 'print' && $tmp_var_c <= 7) {
                $_OMP_html['table_body'] = $tmp_var_a;
                $_OMP_rec_sf = $tmp_var_b;
                $_OMP_rec_sf['prod_label'] .=
                    str_repeat('<br />', (27 - $tmp_var_c));
                eval("\$_OMP_html['table_body'] .= \"".
                    $_OMP_TPL[$_OMP_sub_rec_tpl]."\";");
            }
            eval("\$_OMP_html['subform'] = \"".
                $_OMP_TPL[$_OMP_subform_tpl]."\";");
        } else {
            /* table_body empty if no detail lines */
            $_OMP_html['table_body'] = '';
        }
        $_OMP_db_result_sf->free();
    }
    //** END OF SUBFORM
    !empty($_OMP_html['subform']) or $_OMP_html['subform'] = '';
    if (empty($_OMP_has_subform) || empty($_OMP_html['subform'])) {
        $_OMP_html['details'] = '';
    } else {
        eval("\$_OMP_html['details'] =
            \"".$_OMP_TPL[$_OMP_subform_tpl]."\";");
    }
    if ($_OMP_get['action'] == 'print') {
        eval("\$_OMP_html['include'] =
        \"".$_OMP_TPL[$_OMP_rec_tpl]."\";");
    } else {
        require 'lib/record_title.php';
        eval("\$_OMP_html['record'] =
            \"".$_OMP_TPL[$_OMP_rec_tpl]."\";");
        eval("\$_OMP_html['include'] =
            \"".$_OMP_TPL[$_OMP_conf['record_template']]."\";");
    }
}
if ($_OMP_get['action'] == 'print') {
    $_OMP_html['browser_title'] = $_OMP_rec['supplier_ref'].' '.
        $_OMP_rec['client_pkey'];
    $_OMP_rec['amended'] != 't' or $_OMP_html['browser_title'] .= ' AMENDED';
} else {
    /* OMP_toolbar needs _OMP_getkey */
    OMP_toolbar($getstring, $getstrlist, $new_get, $row_max, $row_tot);
    $_OMP_html['page_title'] = $_OMP_LC[$_OMP_title];
    $_OMP_html['browser_title'] = $_OMP_LC[$_OMP_title];
    /* @see functions.php */
    OMP_drawer();
    /* options menu only for admins and operators */
    if ((!$_OMP_get['list']) &&
        ($_SESSION['cat'] == 2 || $_SESSION['cat'] == 3)) {
        eval ("\$_OMP_html['options_init'] = \"".$_OMP_TPL[31]."\";");
    }
}
eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
OMP_lose();
?>
