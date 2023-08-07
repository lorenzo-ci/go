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
// | See the GNU General Public License                                   |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the                        |
// | Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston,  |
// | MA 02111-1307 USA                                                    |
// +----------------------------------------------------------------------+
// | Author: Lorenzo Ciani <lciani@yahoo.it>                              |
// +----------------------------------------------------------------------+
//
// $Id: cp.php,v 0.8 $
//
// Manage templates
//

$_OMP_tplg = '127';
require_once 'base.php';
require_once 'lib/credentials.php';
OMP_checkCredentials(array(3)); // Only admins are allowed

/**
* Control panel functions
*
*/

/**
* Prepare the index page of the control panel
*/
function OMP_cpIndex()
{
    // $_OMP_conf is in $_OMP_TPL[0]
    global $_OMP_conf, $_OMP_db, $_OMP_html, $_OMP_out, $_OMP_TPL;
    $_OMP_tmp_a = 'SELECT pkey, template, grp, description 
        FROM template ORDER BY grp, pkey';
    $rec = $_OMP_db->query($_OMP_tmp_a);
    $template_list = '<table class="record"><tr>';
    $i = 0;
    $group = 0;
    while ($row = $rec->fetchRow()) {
        if ($i <>0 && ($i == 4 || $row['grp'] != $group || 
            is_int($row['pkey'] / 100))) {
            $template_list .= '</tr>';
            if (is_int($row['pkey'] / 100)) {
                $template_list .= '</table><table class="record">';
            }
            $template_list .= '<tr>';
            if ($row['grp'] != $group) {
                $group = 127;
            }
            $i = 0;
        }
        $tpl_description = $row['description'];
        $tpl_grp = $row['grp'];
        $tpl_pkey = $row['pkey'];
        $tpl_href = OMP_PATH_SCRIPT.'?action=edit&amp;pkey=';
        $tpl_href .= $tpl_pkey.'&amp;grp='.$tpl_grp;
        eval ("\$template_list .= \"".$_OMP_TPL[3]."\";");
        $i++;
    }
    $template_list .= '</tr></table>';
    $rec->free();
    $_OMP_html['browser_title'] = 'Control Panel';
    $_OMP_html['page_title'] = '| Control Panel';
    $menu = 'Menu';
    $tpl_new = OMP_PATH_SCRIPT.'?action=new';
    eval("\$_OMP_html['include'] = \"".$_OMP_TPL[1]."\";");
    eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
}

/**
* Prepare the template-edit page
*/
function OMP_tplEdit()
{
    // $_OMP_conf is in $_OMP_TPL[0]
    global $_OMP_conf, $_OMP_db, $_OMP_html, $_OMP_out, 
        $_OMP_sname, $_OMP_sid_val, $_OMP_TPL;
    $_OMP_tmp_a = 'SELECT pkey, template, grp, description 
        FROM template WHERE pkey = '.$_GET['pkey'].
        ' AND grp = '.$_GET['grp'];
    $rec = $_OMP_db->queryRow(
        $_OMP_tmp_a, 
        array('integer', 'text', 'integer', 'text'));
    $rec['template'] = 
        htmlspecialchars(stripslashes($rec['template']));
    $rec['description'] = 
        htmlspecialchars(stripslashes($rec['description']));
    $_OMP_html['browser_title'] = 'Edit Template';
    $_OMP_html['page_title'] = $_OMP_html['browser_title'];
    $_OMP_html['page_title'] .= ' #'.$rec['pkey'];
    $url = OMP_PATH_SCRIPT.'?action=update&amp;pkey=';
    $url .= $rec['pkey'].'&amp;grp='.$rec['grp'];
    eval("\$_OMP_html['include'] = \"".$_OMP_TPL[2]."\";");
    $menu = '';
    eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
}

/**
* Insert new template
*/
function OMP_tplInsert()
{
    // $_OMP_conf is in $_OMP_TPL[0]
    global $_OMP_conf, $_OMP_db, $_OMP_html, $_OMP_out, $_OMP_url, 
        $_OMP_refresh, $_OMP_TPL;
    $_OMP_tmp_a = 'INSERT INTO template (pkey, template, grp, description) 
        VALUES (?, ?, ?, ?)';
    $_OMP_tmp_b = array(
            $_POST['pkey'], 
            addslashes($_POST['template']), 
            $_POST['grp'], 
            $_POST['description']
        );
    $_OMP_datatypes = array('integer', 'text', 'integer', 'text');
    $_OMP_prepared = $_OMP_db->prepare($_OMP_tmp_a, $_OMP_datatypes);
    $_OMP_db_result = $_OMP_prepared->execute($_OMP_tmp_b);
    $_OMP_prepared->free();
    $browser_title = $page_title = 
        'Template Added';
    $message = 'Template # '.$_POST['pkey'];
    $message .= ' Added In Group # '.$_POST['grp'];
//     $url = OMP_PATH_SCRIPT;
//     eval("\$_OMP_html['include'] = \"".$_OMP_TPL[4]."\";");
//     $_OMP_refresh = true;
//     $_OMP_url = $url;
    // Redirection in template number 4
    $_OMP_tmp_a = OMP_redirectUrl('', OMP_PATH_SCRIPT, true, false);
    OMP_redirPage('4', $_OMP_tmp_a, $message, $browser_title);
    $menu = '';
    eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
}

/**
* Prepare the new-template page
*/
function OMP_tplNew()
{
    // $_OMP_conf is in $_OMP_TPL[0]
    global $_OMP_conf, $_OMP_html, $_OMP_out, $_OMP_sname, 
        $_OMP_sid_val, $_OMP_TPL;
    $template = $pkey = $grp = $description = $menu = '';
    $_OMP_html['browser_title'] = $_OMP_html['page_title'] = 'Add Template';
    $url = OMP_PATH_SCRIPT.'?action=insert';
    eval("\$_OMP_html['include'] = \"".$_OMP_TPL[5]."\";");
    eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
}

/**
* Update a template
*/
function OMP_tplUpdate()
{
    // $_OMP_conf is in $_OMP_TPL[0]
    global $_OMP_conf, $_OMP_db, $_OMP_html, $_OMP_out, $_OMP_url, 
        $_OMP_refresh, $_OMP_TPL;
    $_OMP_tmp_a = 'UPDATE template SET template = ?, description = ?, 
        pkey = ? WHERE pkey = ? AND grp = ?';
    $_POST['form']['template'] = addslashes($_POST['form']['template']);
    $_OMP_tmp_b = array_values($_POST['form']);
    array_push($_OMP_tmp_b, $_GET['pkey'], $_GET['grp']);
    $_OMP_datatypes = array('text', 'text', 'integer', 'integer', 'integer');
    $_OMP_prepared = $_OMP_db->prepare($_OMP_tmp_a, $_OMP_datatypes);
    $_OMP_db_result = $_OMP_prepared->execute($_OMP_tmp_b);
    $_OMP_prepared->free();
    $menu = $page_title = '';
    $browser_title = 'Template Updated';
    $message = $browser_title.' (good luck)';
//     $url = OMP_PATH_SCRIPT;
//     eval("\$_OMP_html['include'] = \"".$_OMP_TPL[4]."\";");
//     $_OMP_refresh = true;
//     $_OMP_url = $url; // See OMP_lose() in base.php

    // Redirection in template number 4
    $_OMP_tmp_a = OMP_redirectUrl('', OMP_PATH_SCRIPT, true, false);
    OMP_redirPage('4', $_OMP_tmp_a, $message, $browser_title);
    
    eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
}
/**
* End of control panel functions
*
*/

!empty($_GET['action']) or $_GET['action'] = 'read';
switch ($_GET['action']) {
case 'edit': {
    $_OMP_tpl = '0, 2';
    OMP_load();
    OMP_tplEdit();
    break;
}
case 'update': {
    $_OMP_tpl = '0, 4';
    OMP_load();
    OMP_tplUpdate();
    break;
}
case 'new': {
    $_OMP_tpl = '0, 5';
    OMP_load();
    OMP_tplNew();
    break;
}
case 'insert': {
    $_OMP_tpl = '0, 4';
    OMP_load();
    OMP_tplInsert();
    break;
}
case 'read': {
    $_OMP_tpl = '0, 1, 3';
    OMP_load();
    OMP_cpIndex();
    break;
}
}
OMP_lose();
?>
