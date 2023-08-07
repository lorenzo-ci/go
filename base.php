<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | OMP Version 1.1                                                      |
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
// $Id: base.php,v 0.9 $
//
// Functions, user login, database connection, profiling, cookies
//
require_once 'conf.php';
require_once 'lib/omp_db.php';
require_once 'lib/functions.php';

// Redirect to login page on SSL or not
// http://it.php.net/manual/en/function.header.php#51542
// http://it.php.net/manual/en/function.header.php#51111
if (OMP_SSL == empty($_SERVER['HTTPS'])) {
    session_write_close();
    header('HTTP/1.1 303 See Other');
    header('Location: '.OMP_ABS_PATH.'index.php?logout=1');
}

/**
* Common functions
*
*/

/**
* Create $_OMP_html['buttons']
* @param string $tpl $_OMP_TPL index
* @param string $insert $_OMP_LC index for what to display for "OK"
* @param string $abort $_OMP_LC index for what to display for "Abort"
* @param string $abort_onclick javascript for "Abort" onclick
* @see edit.php, edit-line.php, del.php, del-line.php
* @see new.php, new-line.php, user.php
*/
function OMP_buttons($tpl, $insert, $abort, $abort_onclick)
{
    global $_OMP_html, $_OMP_LC, $_OMP_TPL;
    $abort_value = $_OMP_LC[$abort];
    $insert_value = $_OMP_LC[$insert];
    eval("\$_OMP_html['buttons'] = \"".$_OMP_TPL[$tpl]."\";");
}
/**
* Returns valid file name
* @param string $filename
* @return $filename
* @see edit.php, edit-line.php, del.php, del-line.php
* @see new.php, new-line.php, user.php
*/
function OMP_CheckFileName($filename) {
//     $filename = strtolower($filename);
    $filename = str_replace('#','_',$filename);
//     $filename = str_replace(' ','_',$filename);
    $filename = str_replace('\'','',$filename);
    $filename = str_replace('"','',$filename);
    $filename = str_replace('__','_',$filename);
    $filename = str_replace('&','and',$filename);
    $filename = str_replace('/','-',$filename);
    $filename = str_replace('\\','_',$filename);
    $filename = str_replace('?','',$filename);
    return $filename;
}

/**
* Configure PHP options
*
* @see OMP_SetSession()
*/
function OMP_configPHP()
{
    ini_set('session.use_cookies', '1');
    ini_set('session.auto_start', '0'); // So we can set session name
    ini_set('arg_separator.output', '&amp;');
    ini_set('zlib.output_compression', '1');
    ini_set('zlib.output_compression_level', '4');
    ini_set('url_rewriter.tags',
        'a=href,area=href,frame=src,input=src,fieldset=');
    date_default_timezone_set(OMP_DB_TZ);
}

/**
* Print error message (using templates), also called by OMP_handlePearError
*
* @param string $error error message
* @param string $description debug message
* @param string $disconnect optionally prompts to log-out page
* @see OMP_handlePearError()
*/
function OMP_genErr($error = '', $description = '', $disconnect = false)
{
    // Need $_OMP_conf['org'] for template 0, $_OMP_LC for template 12
    global $_OMP_conf, $_OMP_encoding, $_OMP_get, $_OMP_html,
        $_OMP_mdc_menu, $_OMP_out, $_OMP_refresh, $_OMP_timer,
        $_OMP_url, $_OMP_LC, $_SESSION;

    $_OMP_html['browser_title'] =
        $_OMP_html['page_title'] =
        (isset($_OMP_LC[85])) ? $_OMP_LC[85] : 'Error';
    if ($disconnect) {
        $message = ($error != '') ? $error : 'Invalid credentials';
        $_OMP_url = OMP_PATH.'index.php?logout=1';
        OMP_template($_OMP_html['$_OMP_include_tpl'], '4');
        $_OMP_refresh = true; // See OMP_lose(), $_OMP_url
    } else { // Debug message only for admin user
        OMP_template($button_ok, '11');
        eval("\$button_ok = \"".$button_ok."\";");
        OMP_template($_OMP_html['$_OMP_include_tpl'], '3');
    }
    eval("\$_OMP_html['include'] = \"".
        $_OMP_html['$_OMP_include_tpl']."\";");
    // See template 0
    $_OMP_onload = '';
    OMP_template($_OMP_out, '0');
    eval("\$_OMP_out = \"".$_OMP_out."\";");
    !$disconnect or OMP_SetSession(); // Destroy session
    OMP_lose();
}

/**
* show DB error (no templates required)
*
* @param string $error error message
* @param string $details additional error message
*/
function OMP_genDbErr($error = 'Unknown error occurred', $details)
{
    echo 'A database error has occurred.<br><i>'.$error.'</i><br><i>'.
        $details.'</i><br>Please contact your system administrator.';
    exit();
}

/**
* PEAR error handler
*
* @param object $error error handle
* @see OMP_genErr()
*/
function OMP_handlePearError($error)
{
    OMP_genErr($error->getMessage(), $error->getDebugInfo());
}

/**
* Callback for htmlentities used in array_map
*
* @param string $str string to be converted
* @see OMP_makeVars()
*/
function OMP_htmlentities($str)
{
    global $_OMP_encoding;
    return html_entity_decode($str, ENT_QUOTES, $_OMP_encoding);
//    return htmlentities($str, ENT_QUOTES, $_OMP_encoding);
}

/**
* Returns array with installed languages
*
* @see base.php, user.php
*/
function OMP_languages()
{
    global $_OMP_db;
    return $_OMP_db->queryAll('SELECT * FROM language');
}

/**
* Load templates and locale strings and
* sets $_OMP_TPL and $_OMP_LC global arrays
*
* @see new.php, read.php
*/
function OMP_load()
{
    global $_OMP_db, $_OMP_LC, $_OMP_lcl, $_OMP_timer, $_OMP_TPL,
        $_OMP_tpl, $_OMP_tplg, $_SESSION;
    // Arrays already set or empty template/locale string
    if ((isset($_OMP_LC) && isset($_OMP_TPL)) || empty($_OMP_tpl)) {
        return;
    }
    isset($_OMP_tplg) or $_OMP_tplg = 0; // Template Group
    // Fetch templates
    // sort and remove duplicates from list
    $tpl_array = explode(", ", $_OMP_tpl);
    sort($tpl_array);
    $tpl_array = array_unique($tpl_array);
    /* remove empty elements but not elements with value 0 */
    $tpl_array = array_filter($tpl_array, 'strlen');
    // the list of templates is now filtered and sorted
    $_OMP_tpl = implode(", ", $tpl_array);
    unset($tpl_array);
    $query = $_OMP_db->query('SELECT pkey, template FROM template WHERE
        pkey IN ('.$_OMP_tpl.') AND grp = '.$_OMP_tplg);
    if ($query->numRows() == 0) {
        echo 'Sorry, templates '.$_OMP_tpl.
            ' not found.<br>Please contact your system administrator.';
        $_OMP_db->disconnect();
        exit();
    }
    while ($row = $query->fetchRow(MDB2_FETCHMODE_ORDERED)) {
        $_OMP_TPL[$row[0]] = $row[1];
    }
    // Fetch locale strings
    if (!empty($_OMP_lcl)) { // Not always needed, e.g. cp.php
//         $sql = 'SELECT * FROM locale WHERE pkey IN (!) AND lang = ?';
//         $params = array($_OMP_lcl, $_SESSION['LC']);
//         $query = $_OMP_db->query($sql, $params);
        $_OMP_lcl = OMP_LCL_MENU.$_OMP_lcl; // List of locale strings for menu
        // sort and remove duplicates from list
        $lcl_array = explode(", ", $_OMP_lcl);
        sort($lcl_array);
        $lcl_array = array_unique($lcl_array);
        /* remove empty elements but not elements with value 0 */
        $lcl_array = array_filter($lcl_array, 'strlen');
        // the list of locale strings is now filtered and sorted
        $_OMP_lcl = implode(", ", $lcl_array);
        unset($lcl_array);
        $sql_lcl = 'SELECT * FROM locale WHERE pkey IN 
            ('.$_OMP_lcl.') AND lang = '.OMP_db_quote($_SESSION['LC']);
        $query = $_OMP_db->query($sql_lcl);
        if ($query->numRows() == 0) {
            echo 'Sorry, no locale available for language code \''.
                $_SESSION['LC'].
                '\'.<br>Please contact your system administrator.';
            $_OMP_db->disconnect();
            exit();
        }
        while ($lc_row = $query->fetchRow(MDB2_FETCHMODE_ORDERED)) {
            $_OMP_LC[$lc_row[0]] = $lc_row[2];
        }
    }
    $query->free();
}

/**
* Generate HTML output
*
*/
function OMP_lose()
{
    global $_OMP_db, $_OMP_out,
        $_OMP_pdf, $_OMP_redir,
        $_OMP_refresh, $_OMP_url;

    !isset($_OMP_db) or $_OMP_db->disconnect();
    $_OMP_out = stripslashes($_OMP_out);
    // HTTP Headers
    empty($_OMP_refresh) or
        OMP_refresh('', false, false, 'refresh: 1; url='.$_OMP_url);
    empty($_OMP_redir) or OMP_redir();
    if (isset($_OMP_pdf)) {
        $passreturn = '';
        $filename_html = 'order.html';
        $handle = fopen($filename_html, 'w');
        fwrite($handle, $_OMP_out);
        fclose($handle);
        /* Redirect browser to output page*/
        header('Location: '.OMP_PATH.$filename_html);
        exit;
    }
    // header("Pragma:");
    header('Cache-Control: max-age=84600, public');
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: no-referrer');
    header('X-Content-Type-Options: nosniff');
    header('Permissions-Policy: geolocation=(self "https://go.chaneyinternational.com"), microphone=()');
    ob_start();
    echo $_OMP_out;
    ob_end_flush();
    exit();
}

/**
* Exits without html output
*
*/
function OMP_lose_no_html()
{
    global $_OMP_db;

    !isset($_OMP_db) or $_OMP_db->disconnect();
    exit();
}

/**
* Generate and returns HREF link
*
* @param string $template template to be used to generate HREF link
* @param string $href HREF link
* @param string $link_name label for HREF link
* @return string HREF link
*/
function OMP_popLink($template, $href, $link_name)
{
    eval("\$output = \"".$template."\";");
    return stripslashes($output);
}

/**
* Removes slashes added by PHP if magic_quotes_gpc is on
* @param array $my_array COOKIE, GET or POST array
*/
function OMP_raw_param(&$my_array)
{
    if (ini_get('magic_quotes_gpc')) {
        echo 'here';
        $my_array = array_map('stripslashes', $my_array);
    }
}

/**
* URL redirection
*
* @see 
*/
function OMP_redir()
{
    session_write_close();
    // http://it.php.net/manual/en/function.header.php#51542
    global $_OMP_url;
    header('HTTP/1.1 303 See Other');
    header("Location: ".$_OMP_url);
}

/**
* Make redirection page
*
* @param string $tpl template number
* @param string $b_t browser title
* @param string $p_t page title
* @param string $message
* @see del.php, del-line.php, edit.php, edit-line.php, new.php, new-line.php
* @see user.php
*/
function OMP_redirPage($tpl = 4, $url, $message = '', $b_t =  '', $p_t = '')
{
    global $_OMP_html, $_OMP_url, $_OMP_refresh, $_OMP_LC, $_OMP_TPL;
    $_OMP_html['browser_title'] = $b_t;
    $_OMP_html['page_title'] = $p_t;
    $_OMP_url = $url;
    /* mdc-toolbar mdc-drawer button */
    $_OMP_html['toolbar'] = $_OMP_html['drawer_button'] =
        $_OMP_html['logo'] = '';
    // Please note template must have $_OMP_url and $message
    eval("\$_OMP_html['include'] = \"".$_OMP_TPL[$tpl]."\";");
    $_OMP_refresh = true;
}

/**
* Return URL for redirection
* Append SID if necessary
*
* @param string $encoded string to be encoded in URL, default empty
* @param string $abs use absolute URI, default false
* @param string $ampersand use HTML special ampersand, default true
* @param string $script script file name to be added to the URL
* @return string
* @see OMP_header()
*/
function OMP_redirectUrl($encoded = '', $abs = false, $ampersand = true, 
    $script = '')
{
    if (!empty($script)) {
        $url = ($abs) ? OMP_ABS_PATH : OMP_PATH;
        $url .= $script;
    } else {
        $url = ($abs) ? OMP_ABS_PATH_SCRIPT : OMP_PATH_SCRIPT;
    }
    $url .= '?'.((SID == '') ? '' : $GLOBALS['_OMP_sid']);
    $url .= (($ampersand) ? '&amp;' : '&');
    $url .= OMP_link($encoded);
    return $url;
}

/**
* Refreshes page to a new URL
* Save session data
*
* @param string $encoded string to be encoded in URL, default empty
* @param string $abs use absolute URI, default false
* @param string $ampersand use HTML special ampersand, default true
* @see del.php, del-line.php, edit.php, edit-line.php, new.php, new-line.php
*/
function OMP_refresh($location = '', $abs = false, $ampersand = true, 
    $header = 'Location: ')
{
    session_write_close();
    header($header.(($header == 'Location: ') ? 
        OMP_redirectUrl($location, $abs, $ampersand) : ''));
}

/**
* Create and destry session
* Set $_OMP_sid to be used in templates to append SID to URLs
*
* @param bool $action create or destroy session
*/
function OMP_SetSession($action = false)
{
    global $_OMP_sid, $_OMP_sid_val, $_OMP_sname, $_SESSION;
    if ($action) {
        session_set_cookie_params(OMP_IDLE, OMP_COOKIE_PATH,
            $_SERVER['SERVER_NAME'], OMP_SSL, true);
        session_name($_OMP_sname);
        session_start();
        // If cookies not allowed then set $_OMP_sid and $_OMP_sid_val
        if (SID == '') {
            $_OMP_sid = $_OMP_sid_val = '';
        } else {
            $_OMP_sid = strip_tags(SID);
            $_OMP_sid_val =
                str_replace($_OMP_sname.'=', '' ,$_OMP_sid);
        }
        isset($_SESSION['time']) or $_SESSION['time'] = time();
    } else {
        session_unset();
        session_destroy(); // Destroy session
        $_SESSION = array(); // Unset all session variables
        // Unset session cookie
        setcookie($_OMP_sname, '', time() - 86400, OMP_COOKIE_PATH,
            $_SERVER['SERVER_NAME'], OMP_SSL, true);
    }
}

/**
* Read template from database
*
* @param string $template template
* @param string $pkey template number
* @return string
* @see OMP_genDbErr()
*/
function OMP_template(&$template, $pkey)
{
    global $_OMP_db;
    $rec = $_OMP_db->queryRow('SELECT template FROM template WHERE 
        pkey = '.$pkey.' AND grp = 0');
    !MDB2::isError($rec) or
        OMP_genDbErr($rec->getMessage(),
            'Error in function OMP_template()');
    //See cp.php where using PEAR quote adds backslashes
    $template = $rec['template']; // it was valid for PEAR-DB
//     $template = $rec['0'];
}

/**
* End of common functions
*
*/
OMP_configPHP(); // Configure PHP options
OMP_SetSession(true); // Start session
require_once 'MDB2.php'; // PEAR_MDB2
// Connect to database
$_OMP_db = OMP_backendConnect(OMP_DB_ADMNAME, OMP_DB_ADMPASS);
// Database (server/client) and XML encoding/charset
// Adapted from phpPgAdmin 4.0.1 classes/database/Postgres.php line 489
$_OMP_DB_SRV_ENC = 
    $_OMP_db->queryOne('SELECT getdatabaseencoding() AS encoding');
$_OMP_DB_CLI_ENC = 
    $_OMP_db->queryOne('SELECT pg_client_encoding() AS encoding');
// Adapted from phpPgAdmin 4.0.1 libraries/lib.inc.php line 188
($_OMP_DB_CLI_ENC == $_OMP_DB_SRV_ENC) or 
    $_OMP_db->query('SET CLIENT_ENCODING TO \''.$_OMP_DB_SRV_ENC.'\'');
($_OMP_encoding == $_OMP_DB_SRV_ENC) or $_OMP_encoding = $_OMP_DB_SRV_ENC;
// Map encoding to what is compatible with htmlentities see OMP_htmlentities()
if ($_OMP_encoding == 'UTF8') $_OMP_encoding = 'UTF-8';
// Login
!empty($_GET['login']) or $_GET['login'] = false;
!empty($_GET['logout']) or
    $_GET['logout'] = empty($_SESSION['id']) && !$_GET['login'];
if ($_GET['logout']) { // Prepares to logout
    $_OMP_tpl = '0, 1, 8'; // Template list
    $_OMP_lcl = '10, 11, 12, 13, 14, 32'; // Locale list
    $_OMP_tplg = 0; // Template group
    OMP_SetSession(); // Destroy session
    /* Load default user prefs */
    /* Max no. of items in combo box */
    $_SESSION['maxlist'] = OMP_MAXLIST;
    /* Max width of combo box */
    $_SESSION['ddl_width'] = OMP_DDL_WIDTH_STD;
    $_SESSION['country'] = OMP_COUNTRY;
    /* Thousands Separator */
    $_SESSION['ts'] = OMP_TS;
    /* Decimal Point */
    $_SESSION['dp'] = OMP_DP;
    /* User category */
    $_SESSION['cat'] = '';
    /* Cascading Style Sheet */
    $_SESSION['css'] = OMP_CSS;
    // Set locale to browser language or load default
    $_OMP_tmp_c = OMP_languages();
    $_SESSION['LC'] = 'it';
} elseif ($_GET['login']) { // Check user credentials from login form
    /* Avoid errors in OMP_genErr() due to $_SESSION indexes */
    !empty($_SESSION['css']) or $_SESSION['css'] = OMP_CSS;
    $_SESSION['LC'] = 'it';
    if ((!empty($_POST['username'])) || (!empty($_POST['password']))) {
        #// Check on PostgreSQL pg_shadow system table
        #if(!is_array($_OMP_db->queryRow('SELECT usename, passwd 
        #   FROM pg_shadow WHERE
        #   usename = '.$_OMP_db->quote($_POST['username']).' AND
        #   passwd = '.$_OMP_db->quote('md5'.md5($_POST['password'].
        #    $_POST['username']))))) {
        #   OMP_genErr('', '', true); // Incorrect username - password
        #}
        if (strlen($_POST['username']) > 50 || 
            strlen($_POST['password']) > 32) {
            OMP_genErr('','', true);
        }
        $_OMP_tmp_b = 'SELECT locale, maxlist, country, ddl_width, 
            cat, css FROM profile WHERE
            username = '.$_OMP_db->quote($_POST['username']).
            ' AND password = '.$_OMP_db->quote(md5($_POST['password']));
        $_OMP_db->setFetchMode(MDB2_FETCHMODE_ORDERED);
        $_OMP_tmp_a = $_OMP_db->queryRow($_OMP_tmp_b);
        $_OMP_db->setFetchMode(MDB2_FETCHMODE_ASSOC);
        if (MDB2::isError($_OMP_tmp_a) || empty($_OMP_tmp_a)) {
            if (OMP_STAT) {
                $_OMP_timer->leaveSection('Base');
            }
            OMP_genErr('','', true); // Incorrect username / password
        } else {
            list($_SESSION['locale'], $_SESSION['maxlist'],
                $_SESSION['country'], $_SESSION['ddl_width'],
                $_SESSION['cat'], $_SESSION['css']) = $_OMP_tmp_a;
            $_SESSION['id'] = $_POST['username'];
            $_SESSION['css'] != '' or $_SESSION['css'] = OMP_CSS;
            $_SESSION['LC'] = substr($_SESSION['locale'], 0, 2);
        }
        // Locale settings
        if (false !== setlocale(LC_ALL, $_SESSION['locale'])) {
                $_SESSION['locale_ok'] = true; // See OMP_checkNum()
                $_OMP_tmp_a = localeconv();
                $_SESSION['ts'] = $_OMP_tmp_a["mon_thousands_sep"];
                if (!empty($_OMP_tmp_a["mon_decimal_point"])) {
                    $_SESSION['dp'] = $_OMP_tmp_a["mon_decimal_point"];
                } else {
                    $_SESSION['dp'] = $_OMP_tmp_a["decimal_point"];
                }
        } else {
                $_SESSION['locale_ok'] = false;
                $_SESSION['ts'] = '';
                $_SESSION['dp'] = '.';
        }
    } else {
        OMP_genErr('','', true); // Empty username / password
    }
} else {
    // Check if session expired
    if (OMP_IDLE >0 && ($_SESSION['time'] + OMP_IDLE) <= time()){
        OMP_genErr('Session expired', '', true);
    }
    // More locale settings
    setlocale(LC_ALL, 'C');
    if ($_SESSION['locale_ok']) {
        setlocale(LC_CTYPE, $_SESSION['locale']);
        setlocale(LC_TIME, $_SESSION['locale']);
    }
}
// PEAR error handler
PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'OMP_handlePearError');
 // Load templates and locale strings
empty($_OMP_lcl) or  OMP_load();
// drawer, navbar and drawer_button are displayed in template 1
isset($_OMP_html['drawer']) or $_OMP_html['drawer'] = '';
/* init variables for various MDC components */
$_OMP_html['drawer_init'] = $_OMP_html['drawer_button'] =
    $_OMP_html['drawer'] = $_OMP_html['toolbar'] =
    $_OMP_html['options_init'] = $_OMP_html['list_init'] =
    $_OMP_html['select_onchange'] = $_OMP_html['select_id'] =
    $_OMP_html['switch_printed_script'] =
    $_OMP_html['switch_amended_script'] =
    $_OMP_html['switch_closed_script'] =
    $_OMP_html['switch_cancelled_script'] =
    $_OMP_html['switch_active_script'] =
    $_OMP_html['switch_stock_script'] =
    /* required by $_OMP_conf['record_template'] */
    $_OMP_html['details'] =
    $_OMP_html['switch'] =
    $_OMP_html['switch_script'] = '';
/* delete this line when toolbar is ready */
isset($_OMP_html['navbar']) or $_OMP_html['navbar'] = '';
if ($_GET['logout']) {
    require 'login.php'; // Print login page
}
switch ($_SESSION['cat']) { // Connects to database with proper user-class
    case 0: // Clients
        // Control Panel for admins only!
        if (OMP_SCRIPT == 'cp.php') {
            OMP_genErr('Page Not Available','');
        }
        $_OMP_db->disconnect();
        $_OMP_db = OMP_backendConnect(OMP_DB_CLINAME, OMP_DB_CLIPASS);
        break;
    case 1: // Suppliers
        // Control Panel for admins only!
//         if (OMP_SCRIPT == 'cp.php') {
//             OMP_genErr('Page Not Available','');
//         }
        $_OMP_db->disconnect();
        $_OMP_db = OMP_backendConnect(OMP_DB_SUPNAME, OMP_DB_SUPPASS);
        break;
    case 2: // Operators
        // Control Panel for admins only!
        if (OMP_SCRIPT == 'cp.php') {
            OMP_genErr('Page Not Available','');
        }
        $_OMP_db->disconnect();
        $_OMP_db = OMP_backendConnect(OMP_DB_OPNAME, OMP_DB_OPPASS);
        break;
    case 3: // Admins, we are already connected as admins
        break;
    default: // Other values for $_SESSION['cat'] not allowed, logout
        OMP_genErr('Session unauthorized','', 1);
        break;
}
/**
* Parse GET query string, all in
* just one variable $_GET['q'], into array $_OMP_get
*/
if (isset($_GET) && $_GET != '') {
    if (OMP_ACTION_ENCODE) {
        // parse_str(base64_decode($_GET), $_OMP_get);
        $_OMP_get = array_map('base64_decode', $_GET);
    } else {
        $_OMP_get = $_GET;
    }
    OMP_raw_param($_OMP_get);
}
$_OMP_get['popup'] = !empty($_OMP_get['popup']);
$_OMP_get['filter'] = !empty($_OMP_get['filter']);
// if (!empty($_OMP_get['unfilter'])) {
//     unset($_OMP_get['unfilter']);
//     $_SESSION['filter'] = 0;
//     $_SESSION['where'] = ''; // see also index.php
// };
$_OMP_get['read_new'] =
    !empty($_OMP_get['read_new']); // See read.php
$_OMP_get['list'] =
    !empty($_OMP_get['list']); // Show list of records
$_OMP_get['where'] =
    (isset($_OMP_get['where'])) ? $_OMP_get['where'] : '';
if (empty($_OMP_get['action'])) {
    $_OMP_get['action'] = 'read';
}
if ($_OMP_get['action'] == 'read') {
    /* HREF links for mdc-menu */
    require_once 'lib/mdc-menu.php';
} else {
    // When editing the record just added
    // see read.php at if ($_OMP_get['read_new'])
    // see also del.php at if ($_OMP_get['filter'])
    // CHECK IF THIS MESSES THINGS UP!
    !empty($_OMP_get['where']) or
        $_OMP_get['filter'] = true;
}
!empty($_OMP_get['row_num']) or $_OMP_get['row_num'] = '';
?>
