<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | OMP Version 1                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 2021-2022 Lorenzo Ciani                                |
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
// $Id: ocr.php,v 0.1$
//
// Scan PDF files
//
require_once 'base.php';
/* Show a link to ocr-load.php if you visit this page directly */
if ((!isset($_FILES['userfile'])) ||
    ($_FILES['userfile']['error'] <> 0) ||
    (mime_content_type($_FILES['userfile']['tmp_name']) <> 'application/pdf')) {
        OMP_genErr('<a href="ocr-load.php">Please load a valid PDF file</a>');
}
require_once 'vendor/autoload.php';
use thiagoalessio\TesseractOCR\TesseractOCR;
/**
* Looks up client pkey
* Called just once at invoice. Should be removed?
*/
function OMP_client_lookup($db, $tables, $order, $supplier_ref)
{
    $select = 'SELECT '.
        $order['client_pkey'].' AS client_pkey FROM '.
        $tables['orders'].' WHERE '.
        $order['supplier_ref'].' = '.
        $db->quote($supplier_ref);
    $client = $db->queryRow($select);
    return $client['client_pkey'];
}
/**
* Check shipment records
*/
function OMP_is_sr_valid()
{
    global $pages, $record_is_valid, $valid_items, $values_from_file;
    
    $record_is_full = false;
    if ($valid_items > 0) {
        $record_is_full = true;
    } else {
        /* add shipment record only if not empty
           if only date is present then record is considered empty
           like in the case of page 2 of invoice */
        $rec_date = $values_from_file['date'];
        unset($values_from_file['date']);
        $recordchk = array_filter($values_from_file, 'strlen');
        if (!empty($recordchk)) {
            $values_from_file['date'] = $rec_date;
            $record_is_full = true;
        }
    }
    /* check that record is not duplicate based on
       shipment pkey AND line items */
    $record_is_unique = true;
    if (count($pages) > 0) {
        foreach ($pages as &$record) {
            if ($record['type'] <> ('invoice' || 'ship_advice')) continue;
            if ($record['ocr']['del_pkey'] == $values_from_file['del_pkey'] ) {
                $record_is_unique = false;
                /* copy values from duplicate record where not in original */
                if (empty($record['ocr']['eta']) && 
                    !empty($values_from_file['eta'])) {
                    $record['ocr']['eta'] = $values_from_file['eta'];
                }
                if (empty($record['ocr']['invoice']) && 
                    !empty($values_from_file['invoice'])) {
                    $record['ocr']['eta'] = $values_from_file['invoice'];
                }
                if (empty($record['ocr']['truck']) && 
                    !empty($values_from_file['truck'])) {
                    $record['ocr']['truck'] = $values_from_file['truck'];
                }
                if (empty($record['ocr']['destination']) && 
                    !empty($values_from_file['destination'])) {
                    $record['ocr']['destination'] = 
                        $values_from_file['destination'];
                }
                break;
            }
        }
    }
    if ($record_is_full && $record_is_unique && isset($values_from_file['items']) )
        $record_is_valid = true;
}

$_OMP_tpl = OMP_TPL_NEW.'1701, 1702, 1703, 1704, 1705, 1706, 1707';
$_OMP_lcl = OMP_LCL_NEW.'23, 38, 100, 106, 110, 600, 604, 703, 704, 706, 708, 
    716, 717, 720, 801, 802, 803, 804, 806, 1501, 1900, 1901, 4003, 4004, 
    4005, 4006, 4007';
OMP_load(); // @see base.php
/* PDF file uploaded by the user */
$uploadfile = $_FILES['userfile']['tmp_name'];
/* check to see if PDF contains text */
$command = 'pdftotext -layout '.$uploadfile.' -';
$output = array();
exec($command, $output);
/* remove empty lines */
$output = array_filter($output, 'strlen');
/* create multiarray $files */
if (empty($output)) {
    /* PDF does not contain text so convert it to image and OCR */
    $pdf_text = false;
    $imagick = new imagick();
    $imagick->setResolution(200, 200);
    $imagick->readImage($uploadfile);
    foreach ($imagick as $i=>$image) {
        $image->setImageFormat('jpg');
        $data = $image->getImageBlob();
        $size = $image->getImageLength();
        $text = (new TesseractOCR())
            ->imageData($data, $size)
            ->quiet()
            ->lang('eng', 'ita')
            ->psm(1)
            ->dpi(200)
            ->run();
        /* put text from OCR into array $output each element 1 line */
        $output = explode(PHP_EOL, $text);
        $output = array_map('trim', $output);
        /* remove empty lines and put text from OCR into multiarray $files */
        $files[] = array_filter($output, 'strlen');
    }
    $imagick->clear();
} else {
    /* PDF contains text so no need to OCR */
    $pdf_text = true;
    $output = array_map('trim', $output);
    /* remove empty lines */
    $files[] = array_filter($output, 'strlen');
}
unset($output);
/* debug */
$html_ocr = '';
/* counter of pages with scanned text */
$valid_pages = 0;
/* array with details of each page/file */
$pages = array();
/* process text */
foreach ($files as $filename) {
    /* check the type of document */
    $doc_is = '';
    foreach ($filename as $line) {
        if(preg_match('/advice of shipment/i', $line) === 1 ||
            preg_match('/shipment\D*\s+\d+/i', $line) === 1 ||
            preg_match('/^\bshipping\s\badvice\b$/i', $line)) {
            $doc_is = 'ship_advice';
            break;
        } elseif (preg_match('/invoice\s+date\s*:/i', $line) === 1) {
            $doc_is = 'invoice';
            break;
        } elseif (preg_match('/confirmation\s+date\s*:/i', $line) 
            === 1) {
            $doc_is = 'SO';
            break;
        } elseif (preg_match('/bonific[oi]/i', $line) === 1) {
            $doc_is = 'payment';
            break;
        }
    }
    /* skip to next file if no document type is found */
    if (empty($doc_is)) { continue; }
    /* the next step reoders the array key and it is necessary otherwise 
       isset($filename[$key+1]) will think that the next element exists 
       even if it was eliminated */
    $filename = array_values($filename);
    $valid_pages++;
    /* text scanned from document to be displayed in html details */
    $html_ocr .= $_OMP_LC[4005].' '.$valid_pages.'<hr><pre>';
    $html_ocr .= implode(PHP_EOL, $filename);
    $html_ocr .= '</pre><hr>';
    $results = array();
    $update = array();
    /* counter of valid shipment items */
    $valid_items = 0;
    /* if record is valid then $values_from_file is stored in $pages array */
    $record_is_valid = false;
    switch ($doc_is) {
        case 'ship_advice':
            /* uploaded document is a shipment advice */
            /* must define explicitly the elements of this array to avoid errors */
            $values_from_file = array('del_pkey' => '', 'date' => '', 
                'origin' => '', 'eta' => '', 'inv_pkey' => '', 'truck' => '', 
                'destination' => '', 'note' => '', 'client_pkey' => '');
            /* Cycle each line of scanned text */
            foreach ($filename as $key=>$value) {
                /* must define explicitly this array to avoid errors */
                $items = array('bales' => '' , 'quantity' => '',
                    'supplier_ref' => '', 'prod_pkey' => '');
                /* look for shipment number */
                /* first we look for a line with SHIPMENT */
                if (preg_match('/^SH?I?T?PMEN\'?T\s*\S*\s+\d+$/', $value)) {
                    /* We operate on $value instead of $results because regex 
                       pattern looks for whole line and we can 
                       skip implode results array */
                    /* then we look for 6 numbers at the end of the line
                       \b[0-9]{6}\b$ */
                    $values_from_file['del_pkey'] = 
                        preg_replace('/^SH?I?T?PMEN\'?T\s*\S*\s+/', '', $value);
                } else {
                    !(empty($values_from_file['del_pkey'])) or
                        $values_from_file['del_pkey'] = '';
                }
                /* look for shipment date ^\d\d\/\d\d\/\d{4} */
                if (preg_match('/^\d\d\/\d\d\/\d{2,4}/', $value, $results)) {
                    $values_from_file['date'] = 
                        OMP_checkDateTxt(implode('', $results), 'Date');
                } else {
                    !(empty($values_from_file['date'])) or
                        $values_from_file['date'] = '';
                }
                /* look for ETA */
        	    if (preg_match('/\s*on\s+\d+/i', $value)) {
                    if (preg_match('/\d\d\/\d\d\/\d{2,4}/', $value, $results)) {
                        $eta_strings[] = implode('', $results);
                    }
                }
                /* look for origin */
                if (preg_match('/^[EX|LX][\s\S]+\w{4}$/', $value, $results)) {
                    preg_match('/\w{4}$/', $results[0], $origin);
                    $values_from_file['origin'] = trim($origin[0]);
                } else {
                    !(empty($values_from_file['origin'])) or
                        $values_from_file['origin'] = '';
                }
                /* look for container number */
                if (preg_match('/p[le]r\s*:?\s+/i', $value)) {
                    $container = preg_replace('/per\s*:?\s+/i'
                            , '', $value);
                    $container = preg_replace('/[C][NT][INT][R]\s+/', '', $container);
                    $container = preg_replace('/\d+\s+\d+[.]\d+\s+/', '', $container);
                    //$container = preg_replace('/\d+\.\d+\s+/', '', $container);
                    $container = preg_replace('/\s+\w+$/', '', $container);
                    $container = trim($container);
                    $values_from_file['truck'] = $container;
                } else {
                    !(empty($values_from_file['truck'])) or
                        $values_from_file['truck'] = '';
                }
                /* look for invoice number */
                if (preg_match('/^INV|TNV\D*\s+\d+$/', $value, $results)) {
                    $values_from_file['inv_pkey'] = 
                        'CD'.preg_replace("/^[INV|TNV]\D*\s+/", '', $value);
                } else {
                    !(empty($values_from_file['inv_pkey'])) or
                        $values_from_file['inv_pkey'] = '';
                }
                /* look for destination */
                if (preg_match('/^consigned\s+to/i', $value, $results)) {
                    for ($i = 1; $i < 7; $i++) {
                        if (!isset($filename[$key+$i]) || 
                            strcasecmp($filename[$key+$i], 'ITALY') == 0) {
                            break;
                        } else {
                            $values_from_file['destination'] .= 
                                $filename[$key+$i].' ';
                        }
                    }
                }
                /* look for ITO number */
                /* TODO gen err if order is cancelled */
                $ito_preg = "/^tto\d+|1ilt\d+|ti\S\d+|pio\d+|ito\d+/i";
                $ito_preg_replace = str_replace(array("^", "\d+"), "", $ito_preg);
                if (preg_match($ito_preg, $value, $results)) {
                    $quantities = array();
                    $ito = implode('', $results);
                    $ito = preg_replace($ito_preg_replace, 'ITO', $ito);
                    $items['supplier_ref'] = $ito;
                    /* look for the first product like H1368 or H1368-2 */
                    if(preg_match_all('/\s\D\d+\S\d/', $value, $products)>0) {
                        /* flattens multidimensional array */
                        $products =
                            call_user_func_array('array_merge', $products);
                        /* skip first element it could be the client's PO */
                        (isset($products[1])) 
                            ? $items['prod_pkey'] = trim($products[1])
                            : $items['prod_pkey'] = trim($products[0]);
                    } else {
                        !(empty($values_from_file['prod_pkey'])) or
                            $items['prod_pkey'] = '';
                    }
                    /* look for quantities, 3 per line: bales gross and net */
                    $numbers = $value;
                    /* pattern \s\d+\s|\d+$ did not catch bale number if a dot
                       was next to it */
                    if (preg_match_all('/\s\d+/', $value, $quantities)>0) {
                        /* flattens multidimensional array */
                        $quantities = 
                            call_user_func_array('array_merge', $quantities);
                        /* if there are more than 3 numbers the first one is
                           probably part of the client's order reference */
                        (count($quantities) <= 3) or array_shift($quantities);
                        /* assign first and last element of array */
                        $items['bales'] = trim(reset($quantities));
                        $items['quantity'] = trim(end($quantities));
                        /* must match format of database number */
                        if (preg_match('/[0-9]{3,}\.{0,1}[0-9]{0,}/',
                            $items['quantity'], $results)) {
                            preg_match('/\d+,{0,1}\.{0,1}/', $results[0], $unit);
                            $unit[0] = str_replace('.', '', $unit[0]);
                            preg_match('/\.[0-9][0-9][0-9]/', $results[0], $fract);
                            /* must match 4 decimal digits format of database */
                            if (!empty($fract[0])) {
                                $fract[0] = str_pad($fract[0], 5, '0');
                                $fract[0] = preg_replace('/\D/', '.', $fract[0]);
                            } else {
                                $fract[0] = '.0000';
                            }
                            $items['quantity'] = $unit[0].$fract[0];
                        }
                    } else {
                        !(empty($values_from_file['bales'])) or
                            $items['bales'] = '';
                        !(empty($values_from_file['quantity'])) or
                            $items['quantity'] = '';
                    }
                }
                /* add shipment item only if not empty
                 * this array_filter remove array elements
                 * but we need all elements even if empty
                 * or there will be a warning */
                $itemschk = array_filter($items, 'strlen');
                if (!empty($itemschk)) {
                    $values_from_file['items'][] = $items;
                    $valid_items++;
                }
                unset($itemschk);
            } // end of foreach on text lines of shipment advice
            /* look for ETA in each line and select the last date */
            if (!empty($eta_strings)) {
                $eta = array();
                $eta_matches = array();
                foreach ($eta_strings as $string) {
                    if (empty($string)) continue;
                    $eta_matches[] = OMP_checkDateTxt($string, 'ETA');
                }
                sort($eta_matches);
                $values_from_file['eta'] = end($eta_matches);
                unset($string);
            }
            /* set $record_is_valid */
            OMP_is_sr_valid();
            /* lookup client_pkey then lookup shipment using
               primary key del_pkey+client_pkey and if shipment is 
               already in the database then update it with new info 
               from scanned doc */
            if ($record_is_valid 
                && !empty($values_from_file['del_pkey'])) {
                /* DB schema */
                require_once 'schemas/db-schema.php';
                /* Orders schema required by OMP_client_lookup() */
                require_once 'schemas/orders-schema.php';
                $values_from_file['client_pkey'] = OMP_client_lookup(
                    $_OMP_db, $_OMP_tables, $_OMP_orders_fld,
                    $values_from_file['items'][0]['supplier_ref']);
                /* Deliveries schema */
                require_once 'schemas/deliveries-schema.php';
                /* Name of current master-table */
                $_OMP_tbl = 'deliveries';
                /* Table fields */
                $_OMP_tbl_fld = $_OMP_deliveries_fld;
                $_OMP_sql['select'] = 'SELECT '.
                    $_OMP_tbl_fld['pkey'].' AS pkey, '.
                    $_OMP_tbl_fld['client_pkey'].' AS client_pkey, '.
                    $_OMP_tbl_fld['date'].' AS date, '.
                    $_OMP_tbl_fld['origin'].' AS origin, '.
                    $_OMP_tbl_fld['eta'].' AS eta, '.
                    $_OMP_tbl_fld['destination'].' AS destination, '.
                    $_OMP_tbl_fld['truck'].' AS truck, '.
                    $_OMP_tbl_fld['note'].' AS note FROM '.
                    $_OMP_tables[$_OMP_tbl].' WHERE '.
                    $_OMP_tbl_fld['pkey'].' = '.
                    OMP_db_quote($values_from_file['del_pkey']).' AND '.
                    $_OMP_tbl_fld['client_pkey'].' = '.
                    OMP_db_quote($values_from_file['client_pkey']);
                $delivery = $_OMP_db->queryRow($_OMP_sql['select']);
                /* populate $values_from_file['diff'] with
                 * differences between scanned record and database record */
                if (is_array($delivery)) {
                    $items = $values_from_file['items'];
                    unset($values_from_file['items']);
                    $diff = array();
                    foreach ($delivery as $key=>$del) {
                        if (!empty($values_from_file[$key])) {
                            if ($del != $values_from_file[$key]) {
                                if (empty($del) || 
                                    stripos($values_from_file[$key], $del)) {
                                    $diff[$key] = $values_from_file[$key];
                                } elseif ($key == 'eta' || $key == 'date') {
                                    $diff[$key] = $values_from_file[$key];
                                } else {
                                    $diff[$key] = $del.' '.$values_from_file[$key];
                                }
                            }
                        }
                    }
                    unset($del);
                    $values_from_file['items'] = $items;
                    empty($diff) or $values_from_file['diff']['deliveries'][] = $diff;
                }
                /* add diff for delivery lines */
                /* Name of current master-table */
                $_OMP_tbl_line = 'deliveries_lines';
                /* Table fields */
                $_OMP_tbl_fld_line = $_OMP_deliveries_lines_fld;
                $_OMP_sql['select_line'] = 'SELECT ol.'.
                    $_OMP_orders_lines_fld['prod_pkey'].' AS prod_pkey, dl.'.
                    $_OMP_tbl_fld_line['pkey'].' AS pkey, dl.'.
                    $_OMP_tbl_fld_line['ol_pkey'].' AS ol_pkey, dl.'.
                    $_OMP_tbl_fld_line['quantity'].' AS quantity, dl.'.
                    $_OMP_tbl_fld_line['bales'].' AS bales FROM '.
                    $_OMP_tables[$_OMP_tbl_line].' AS dl LEFT JOIN '.
                    $_OMP_tables['orders_lines'].' AS ol on (dl.'.
                    $_OMP_tbl_fld_line['ol_pkey'].' = ol.'.
                    $_OMP_orders_lines_fld['pkey'].') WHERE '.
                    $_OMP_tbl_fld_line['del_pkey'].' = '.
                    OMP_db_quote($delivery['pkey']).' AND '.
                    $_OMP_tbl_fld_line['client_pkey'].' = '.
                    OMP_db_quote($delivery['client_pkey']);
                $delivery_lines_query = $_OMP_db->query($_OMP_sql['select_line']);
                $delivery_lines = $delivery_lines_query->fetchAll();
                /* check that delivery_lines match */
                $i = 0;
                $diff = array();
                $items = $values_from_file['items'];
                unset($values_from_file['items']);
                $items = array_filter($items);
                if (!empty($items)) {
                    foreach ($items as $item) {
                        if (isset($delivery_lines[$i])) {
                            /* these are set not to trigger array_diff
                            because they are set in $items but not
                            in $delivery_lines */
                            $delivery_lines[$i]['supplier_ref'] = $item['supplier_ref'];
                            $item['pkey'] = $delivery_lines[$i]['pkey'];
                            $item['ol_pkey'] = $delivery_lines[$i]['ol_pkey'];
                            $check = array_diff_assoc($item, $delivery_lines[$i]);
                            if (!empty($check)) {
                                $update[] = $check;
                                $diff[] = $check;
                            }
                            $items[$i]['pkey'] = $delivery_lines[$i]['pkey'];
                            $items[$i]['ol_pkey'] = $delivery_lines[$i]['ol_pkey'];
                        }
                        $i++;
                    }
                }
                unset($item);
                $values_from_file['items'] = $items;
                if (!empty(array_filter($diff))) {
                    $values_from_file['diff']['lines'] = $diff;
                }
            }
            break;
        /* uploaded document is an invoice */
        case 'invoice':
            /* TODO NOT YET IMPLEMENTED */
            /* when you do implement them, add to OMP_is_sr_valid() */
            $values_from_file['destination'] = $values_from_file['note'] = '';
            /* must define explicitly the elements 
               of this array to avoid errors */
            $values_from_file = array('del_pkey' => '', 'date' => '', 
                'origin' => '', 'eta' => '', 'inv_pkey' => '', 'truck' => '',
                'destination' => '', 'note' => '', 'client_pkey' => '');
            /* $supplier_ref is unique and used for all the items */
            $supplier_ref = '';
            foreach ($filename as $key=>$value) {
                /* must define explicitly this array to avoid errors */
                $items = array('bales' => '', 'quantity' => '', 
                    'supplier_ref' => $supplier_ref, 'prod_pkey' => '');
                /* look for shipment number */
                if (preg_match('/shipment\s+no:*\s+\d+$/i', 
                    $value, $results)===1) {
                    $values_from_file['del_pkey'] = 
                        preg_replace('/[^0-9]/', '', implode($results));
                }
                /* look for invoice date */
                if(preg_match(
                    '/invoice\s+date:\s+.*[0-9]+.*\/.*[0-9]+.*\/.*[0-9]+\s/i',
                    $value,
                    $results
                    ) > 0) {
                    $in_date = implode($results);
                    $in_date = 
                        preg_replace('/invoice\s+date:\s+/i', '', $in_date);
                    /* removes extra white spaces */
                    $in_date = preg_replace('/\s+/', '', $in_date);
                    $values_from_file['date'] = 
                        OMP_checkDateTxt($in_date, 'Date');
                }
                /* look for eta */
                /* there can be spaces in the date due to OCR */
                if (preg_match(
                    '/delivery\s+date:\s+.*[0-9]+.*\/.*[0-9]+.*\/.*[0-9]+/i',
                    $value,
                    $results
                    ) > 0) {
                    $in_eta = implode($results);
                    $in_eta = 
                        preg_replace('/delivery\s+date:\s+/i', '', $in_eta);
                    /* removes extra white spaces */
                    $in_eta = preg_replace('/\s+/', '', $in_eta);
                    $values_from_file['eta'] =
                        OMP_checkDateTxt($in_eta, 'ETA');
                }
                /* look for origin */
                if (preg_match('/Site:\s+\D+$/', $value, $results)===1) {
                    $values_from_file['origin'] = 
                       preg_replace('/Site:\s+/', '', implode($results));
                }
                /* look for container number */
                if (preg_match('/Container No:\s+\w+\d+$/', 
                    $value, $results) === 1) {
                        $container = implode('', $results);
                        $container = preg_replace('/Container No:\s+/'
                            , '', $container);
                        $values_from_file['truck'] = $container;
                } else {
                    !(empty($values_from_file['truck'])) or
                        $values_from_file['truck'] = '';
                }
                /* look for invoice number */
                if (preg_match('/invoice\s+no:*\s+cd\d+$/i', 
                    $value, $results) === 1) {
                    $values_from_file['inv_pkey'] = 
                       preg_replace('/invoice\s+no:*\s+/i', '', 
                        implode($results));
                }
                /* look for order reference */
                /* TODO if order reference not found try with confirmation number */
                if (preg_match(
                    '/reference:\s*[1itl][1itl][o0]\w+$/i', 
                    $value, 
                    $results
                    )===1) {
                    $items['supplier_ref'] = 
                        preg_replace(
                            '/reference:\s*[1itl][1itl][o0]/i', 
                            'ITO', 
                            implode('', $results)
                        );
                    /* $supplier_ref is unique and used for all the items */
                    $supplier_ref = $items['supplier_ref'];
                }
                /* look for product, bales and quantity */
                if (preg_match(
                    /* change for pdftxt does it work for non-pdftxt? */
                    /*'/^\d+\s+\*{0,1}\w+\-{0,1}2{0,1}\s+\d+\.{0,1}\d+\s+\d+\s+\w+\s+/', */
                    '/\d+\s+\*{0,1}\w+\-{0,1}2{0,1}\s+\d+\.{0,1}\d*\s+\d+\s+\w+\s+/',
                    $value,
                    $results) === 1 ) {
                    if (preg_match('/\s\*{0,1}\w{3,}\-{0,1}2{0,1}\s/', 
                        $results[0], $prod) === 1) {
                        $items['prod_pkey'] = trim($prod[0], " *");
                    }
                    /* look for bales number */
                    if (preg_match(
                        /* change for pdftxt does it work for non-pdftxt? */
                        /*'/\s\d{8}\s\d{1,2}\s\d{0,2},?\d{1,3}.\d{2}/',*/
                        '/\s\d{8}\s+\d{1,2}\s+\d{0,2},?\d{1,3}.\d{2}/',
                        $value,
                        $results) === 1) {
                        /* next line added for pdftxt 
                           does it work for non-pdftxt?*/
                        $results = preg_replace('/\s+/', ' ', $results);
                        $results = explode(' ', $results[0]);
                        $items['bales'] = $results[2];
                    }
                    /* look for net quantity */
                    if (isset($filename[$key+1])){ // goes to next line
                        $next_line = $filename[$key+1];
                        if(preg_match(
                            /* change for pdftxt does it work for non-pdftxt? */
                            /*'/^\d{0,2},?\d{1,3}.\d{2}\s+\d{1,3}.\d{3}\s+\d{0,2},?\d{1,3}.\d{2}/', */
                            '/\d{0,2},?\d{1,3}.\d{2}\s*\d{0,3}.\d{0,3}\s+\d{0,2},?\d{1,3}.\d{2}/', 
                            $next_line, 
                            $results)) {
                            /* change for pdftxt does it work for non-pdftxt? */
                            /*preg_match('/^\d{0,2},?\d{1,3}.\d{2}/', */
                            preg_match('/\d{0,2},?\d{1,3}.\d{2}/', 
                                $results[0], $qty);
                            $items['quantity'] = str_replace(',', '', $qty[0]);
                        }
                    }
                } else {
                    $items['supplier_ref'] = $items['prod_pkey'] = 
                        $items['bales'] = $items['quantity'] = '';
                }
                /* add shipment item only if not empty
                 * this array_filter remove array elements
                 * but we need all elements even if empty
                 * or there will be a warning */
                $itemschk = array_filter($items, 'strlen');
                if (!empty($itemschk)) {
                    $values_from_file['items'][] = $items;
                    $valid_items++;
                }
                unset($itemschk);
            } // end of foreach of text lines of invoice
            OMP_is_sr_valid();
            if ($record_is_valid) {
                /* DB schema */
                require_once 'schemas/db-schema.php';
                /* Orders schema required by OMP_client_lookup() */
                require_once 'schemas/orders-schema.php';
                $values_from_file['client_pkey'] = OMP_client_lookup(
                    $_OMP_db, $_OMP_tables, $_OMP_orders_fld, 
                    $supplier_ref);
            }
            break;
        case 'SO':
            /* uploaded document is order confirmation */
            /* must define explicitly the elements 
               of this array to avoid errors */
            $values_from_file = 
                array(  'ref' => '', 'supplier_ref' => '');
            $items = array();
            /* Cycle each line of scanned text */
            foreach ($filename as $value) {
                /* look for sales order confirmation */
                if (preg_match('/[wv]\d+/i', $value, $results) === 1) {
                    $values_from_file['ref'] =
                        preg_replace('/[wv]/i', 'W', $results[0]);
                }
                if (preg_match(
                    /* look for agent reference */
                    '/agent\s+referen[c]?[e]?:?\s+\W?\w+/i',
                    $value, 
                    $results
                    ) === 1) {
                    $values_from_file['supplier_ref'] = 
                        preg_replace(
                            '/agent\s+referen[c]?[e]?:?\s+1?[|1ilt][1ilt][O0]/i',
                            'ITO', 
                            $results[0]
                        );
                    /* looking up prod_pkey is tricky so we likely get
                       several false positives which we try to 
                       match with the values stored in the database */
                    require_once 'schemas/db-schema.php'; // Database schema
                    require_once 'schemas/orders-schema.php'; // Orders schema
                    // Name of current master-table
                    $_OMP_tbl = 'orders_lines';
                    // Table fields
                    $_OMP_tbl_fld = $_OMP_orders_lines_fld;
                    $_OMP_sql['select'] = 'SELECT '.
                        $_OMP_tbl_fld['prod_pkey'].' AS prod_pkey FROM '.
                        $_OMP_tables[$_OMP_tbl].' WHERE '.
                        $_OMP_tbl_fld['oi_pkey'].' = (';
                    // Name of current master-table
                    $_OMP_tbl = 'orders';
                    // Table fields
                    $_OMP_tbl_fld = $_OMP_orders_fld;
                    $_OMP_sql['select'] .= 'SELECT '.$_OMP_tbl_fld['pkey'].
                        ' FROM '.$_OMP_tables[$_OMP_tbl].' WHERE '.
                        $_OMP_tbl_fld['supplier_ref'].' = '.
                        OMP_db_quote($values_from_file['supplier_ref']).
                        ') ORDER BY prod_pkey';
                    $db_prod_pkey_query = $_OMP_db->query($_OMP_sql['select']);
                    $db_prod_pkey = $db_prod_pkey_query->fetchAll();
                    if (!isset($db_prod_pkey[0])) {
                        /* we didn't scan the supplier_ref correctly 
                           therefore we don't know the correct 
                           prod_pkey values */
                        unset($db_prod_pkey);
                    }
                }
                if (preg_match('/^[1-9].?\s+\w+/', $value, $results) === 1) {
                /* look for order-line details */
                    /* look for product */
                    if (preg_match('/[a-z]\d{4}/i', $results[0], $prod)) {
                        /* product found */
                        $line_from_file['prod_pkey'] = trim($prod[0]);
                        /* if prod_pkey from OCR does not match the one in
                        the database then we change it */
                        if (isset($db_prod_pkey)) {
                            $db_prod = array();
                            foreach ($db_prod_pkey as $db_product) {
                                $db_prod[] = $db_product['prod_pkey'];
                            }
// what to do if product not found?
//                             if (in_array($line_from_file['prod_pkey'],
//                                 $db_prod))
                        }
                    }
                    /* look for quantity */
                    /* old regex \s[1-9]?[0-9]{0,2}\D?[0-9]{3}\D?[0-9]{2} */
                    /* [1-9]{1}[0-9]{1,}[\s,.][0-9]{1,}[,.][0-9]{1,} */
                    if (preg_match('/[0-9]{0,3}\s[0-9]{3},[0-9]{2}/',
                        $value,
                        $results)) {
                        /* Does this number have thousands */
                        if (preg_match('/[1-9][0-9]{0,2}\D/',
                            $results[0], $thou) <> 1) $thou[0] = '';
                        preg_match('/[0-9]{3}/', $results[0], $hund);
                        preg_match('/\D[0-9]{2}$/', $results[0], $dec);
                        if (empty($hund[0])) {
                            if (empty($thou[0])) {
                                $hund[0] = '';
                            } else {
                                $hund[0] = '000';
                            }
                        }
                        /* must match 4 decimal digits format of database */
                        /* decimal point imposed instead of session[dp] */
                        if (!empty($dec[0])) {
                            $dec[0] = str_pad($dec[0], 5, '0');
                            $dec[0] = 
                             // preg_replace('/\D/', $_SESSION['dp'], $dec[0]);
                                preg_replace('/\D/', '.', $dec[0]);
                        } else {
                            // $dec[0] = $_SESSION['dp'].'0000';
                            $dec[0] = '.0000';
                        }
                        if (!empty($thou[0])) 
                            $line_from_file['quantity'] = trim($thou[0]);
                        $line_from_file['quantity'] .= $hund[0].$dec[0];
                        unset($thou); unset($hund); unset($dec);
                    }
                    /* look for price */
                    if (preg_match('/[0-9]{1,3}\.[0-9]{3}/', $value, $results)) {
                        preg_match('/\d+,{0,1}\.{0,1}/', $results[0], $unit);
                        $unit[0] = str_replace('.', '', $unit[0]);
                        preg_match('/\.[0-9][0-9][0-9]/', $results[0], $fract);
                        /* must match 4 decimal digits format of database */
                        if (!empty($fract[0])) {
                            $fract[0] = str_pad($fract[0], 5, '0');
                            $fract[0] = preg_replace('/\D/', '.', $fract[0]);
                        } else {
                            $fract[0] = '.0000';
                        }
                        $line_from_file['price'] = $unit[0].$fract[0];
                    } else {
                        $line_from_file['price'] = '';
                    }
                    if (preg_match('/\d\d\/\d\d\/\d{2,4}/', $value, $results)) {
                        $line_from_file['eta'] =
                            OMP_checkDateTxt($results[0], 'OCR ETA');
                    } else {
                        $line_from_file['eta'] = '';
                    }
                } else {
                    $line_from_file = array(
                                            'prod_pkey' => '', 
                                            'quantity' => '', 
                                            'price' => '', 
                                            'eta' => ''
                                            );
                }
                $line_check = array_filter($line_from_file, 'strlen');
                if (!empty($line_check)) 
                    $items[] = $line_check;
            } /* end of cycle through each line of page */
            /* add SO record only if not empty */
            $record_is_full = !empty(array_filter($values_from_file, 'strlen'));
            /* check that SO is not duplicate */
            $record_is_unique = true;
            if (count($pages) > 0) {
                foreach ($pages as &$record) {
                    if ($record['type'] <> 'SO') continue;
                    if ($record['ocr']['ref'] == $values_from_file['ref'] ) {
                        $record_is_unique = false;
                        break;
                    }
                }
                unset($record);
            }
            if ($record_is_full && $record_is_unique ) $record_is_valid = true;
            /* if SO record is valid then check order details */
            if ($record_is_valid) {
                /* attach orders details array to $values_from_file */
                if (!empty($items)) 
                    $values_from_file['items'] = $items;
                require_once 'schemas/db-schema.php'; // Database schema
                require_once 'schemas/orders-schema.php'; // Orders schema
                // Name of current master-table
                $_OMP_tbl = 'orders';
                // Table fields
                $_OMP_tbl_fld = $_OMP_orders_fld;
                $_OMP_sql['select'] = 'SELECT '.
                    $_OMP_tbl_fld['pkey'].' AS pkey FROM '.
                    $_OMP_tables[$_OMP_tbl].' WHERE '.
                    $_OMP_tbl_fld['supplier_ref'].' = '.
                    OMP_db_quote($values_from_file['supplier_ref']);
                $order = $_OMP_db->queryRow($_OMP_sql['select']);
                // Name of current master-table
                $_OMP_tbl = 'orders_lines';
                // Table fields
                $_OMP_tbl_fld = $_OMP_orders_lines_fld;
                $_OMP_sql['select'] = 'SELECT '.
                    $_OMP_tbl_fld['pkey'].' AS pkey, '.
                    $_OMP_tbl_fld['prod_pkey'].' AS prod_pkey, '.
                    $_OMP_tbl_fld['quantity'].' AS quantity, '.
                    $_OMP_tbl_fld['price_net'].' AS price, '.
                    $_OMP_tbl_fld['plan'].' AS plan, '.
                    $_OMP_tbl_fld['eta'].' AS eta, '.
                    $_OMP_tbl_fld['note'].' AS note FROM '.
                    $_OMP_tables[$_OMP_tbl].' WHERE '.
                    $_OMP_tbl_fld['oi_pkey'].' = '.
                    OMP_db_quote($order['pkey']).' ORDER BY prod_pkey, eta';
                    $order_lines_query = $_OMP_db->query($_OMP_sql['select']);
                    $order_lines = $order_lines_query->fetchAll();
                    /* check that orders_lines match */
                    $i = 0;
                    $diff = array();
                    foreach ($items as $item) {
                        if (isset($order_lines[$i])) {
                            /* these are set not to trigger array_diff 
                               because they are set in order_lines but not
                               in items */
                            $item['pkey'] = $order_lines[$i]['pkey'];
                            $item['plan'] = $order_lines[$i]['plan'];
                            $item['note'] = $order_lines[$i]['note'];
                            $check = array_diff_assoc($item, $order_lines[$i]);
                            if (!empty($check)) {
                                $check['pkey'] = $order_lines[$i]['pkey'];
                                $update[] = $check;
                                if (empty($check['prod_pkey'])) {
                                    /* use reverse to add prod_pkey 
                                       to first element of assoc array */
                                    $check = array_reverse($check, true);
                                    array_key_exists('prod_pkey', $item) or
                                        $item['prod_pkey'] = '';
                                    $check['prod_pkey'] = $item['prod_pkey'];
                                    $check = array_reverse($check, true);
                                }
                                $check['quantity'] = 
                                    $order_lines[$i]['quantity'];
                                if (empty($check['price'])) {
                                    $check['price'] = $order_lines[$i]['price'];
                                }
                                $check['plan'] = 
                                    preg_replace('/-\d\d$/', 
                                        '', 
                                        $order_lines[$i]['plan']);
                                if (empty($check['eta'])) {
                                    $check['eta'] = $order_lines[$i]['eta'];
                                }
                                $check['note'] = $order_lines[$i]['note'];
                                $diff[] = $check;
                            }
                            $i++;
                        }
                    }
                    unset($item);
                    /* for template 1705 */
                    if (!empty($diff[0])) $values_from_file['diff'] = $diff;
            }
            break;
        case 'payment':
            /* uploaded document is payment */
            foreach ($filename as $value) {
                /* look for invoice number(s) */
                if (preg_match_all('/cd[0-9]{6}-?\/?,?\d*/i', $value, $results) > 0) {
                    /* flattens multidimensional array */
                    $resultsi = 
                        call_user_func_array('array_merge', $results);
                    foreach ($resultsi as $result) {
                        /* check if more than 1 invoice is referred to 
                           by shorthand annotation with / or - */
                        $inv_pkey = preg_split('(-+|\/+|,+)', $result);
                        if (count($inv_pkey) > 1) {
                            for($i = 0; $i < count($inv_pkey); ++$i) {
                                $length = 
                                    strlen($inv_pkey[0]) - strlen($inv_pkey[$i]);
                                $inv_pkey[$i] = 
                                    substr($inv_pkey[0], 0, $length).$inv_pkey[$i];
                                $inv_pkeys[] = $inv_pkey[$i];
                            }
                        } else {
                            $inv_pkeys[] = $result;
                        }
                    }
                }
                /* look for date */
        	    if (preg_match('/\d\d\D\d\d\D\d{4}/', $value, $results)
        	        === 1) {
                    $date_strings[] = $results[0];
                }
            }
            /* look for date in each line and select the last date */
            $payment_date = '';
            if (!empty($date_strings)) {
                $dates = array();
                $date_matches = array();
                foreach ($date_strings as $string) {
                    if (empty($string)) continue;
                    preg_match('/\d\d\D\d\d\D\d{4}/', $string, $dates);
                    $date_matches[] = 
                        OMP_checkDateTxt(implode('', $dates), 'Date');
                }
                sort($date_matches);
                $payment_date = end($date_matches);
                unset($string);
            }
            /* HACK ALERT! If more than 1 invoice is paid then
               add all to $pages except the last one because we do that at the
               end of the document */
            if (isset($inv_pkeys)) {
                if (is_array($inv_pkeys)) { rsort($inv_pkeys); }
            }
            /* @see base.php */
            !empty($payment_date) or 
                OMP_genErr($_OMP_LC[703].' '.$_OMP_LC[110].' '.$_OMP_LC[4007]);
            //$values_from_file['date'] = $payment_date;
            if (isset($inv_pkeys)) { sort($inv_pkeys); }
            /*for($i = 0; $i < (count($inv_pkeys)-1); ++$i) {
                $pages[] = array('filename' => $filename, 
                                 'type' => $doc_is, 
                                 'ocr' => array('inv_pkey' => $inv_pkeys[$i], 
                                                'date' => $payment_date
                                               )
                                );
            }*/
            if (isset($inv_pkeys)) {
                for($i = 0; $i < count($inv_pkeys); ++$i) {
                    $pages[] = array('filename' => $filename,
                                    'type' => $doc_is,
                                    'ocr' => array('inv_pkey' => $inv_pkeys[$i],
                                                    'date' => $payment_date
                                                )
                                    );
                }

                $record_is_valid = true;
            }
    } // end of collecting values from all text lines in file
    unset($value); // reset foreach of text lines
    /* fill the details of the current page in the the $pages array */
    if ($record_is_valid) {
        if ($doc_is <> 'payment') {
            $new_page = array('filename' => $filename,
                'type' => $doc_is, 'ocr' => $values_from_file);
            if ($doc_is == 'SO') {
                $new_page['db_order'] = $order;
                unset($order);
                $new_page['db_order_lines'] = $order_lines;
                unset($order_lines);
            }
            $pages[] = $new_page;
        }
        unset($new_page);
    }
} // end of foreach files
unset ($filename);
/* no elemnts added to array pages then
 * no useful text found */
if (empty($pages)) {
    $html_error = '';
    foreach ($files as $filename) {
        $html_error .= implode('<br>', $filename);
    }
    unset ($filename);
    $_OMP_LC[4004] .= '<div style="text-align: left;"><br> '.
        $html_error.'</div><br>';
    /* @see base.php */
    OMP_genErr($_OMP_LC[4004]); 
}
/* forms and values from scanned documents */
$_OMP_html['page_title'] = $_OMP_html['browser_title'] = $_OMP_LC['4002'];
eval ("\$_OMP_html['menu'] = \"".$_OMP_TPL[2]."\";");
/* URL for FORM action */
$_OMP_url = 'ocr-post.php';
/* @see OMP_buttons */
$button_cancel_href = 'javascript:history.back();';
$_OMP_list_rec =
    $_OMP_html['record'] =
    $_OMP_html['details'] = '';
/* shipments records counter */
$srn = 0;
/* SO records counter */
$osrn = 0;
/* payments counter */
$prn = 0;
/* pages counter */
$pgn = 0;
/* HTML for document page */
$_OMP_html['ocr_pages'] = '';
require_once 'schemas/db-schema.php'; // Database schema
require_once 'schemas/deliveries-schema.php'; // Deliveries schema
require_once 'schemas/orders-schema.php'; // Orders schema
require_once 'schemas/invoices-schema.php'; // Invoices schema
$form_action = 0;
foreach ($pages as $page) {
    $record = $page['ocr'];
    if ($page['type'] == 'invoice' || $page['type'] == 'ship_advice') {
        if (!empty($values_from_file['diff']) ||
            !empty($values_from_file['diff']['lines'])) {
            /* @see base.php */
            /* update existing record */
            $form_action = 1;
            OMP_buttons('14', '38', '37', $button_cancel_href);
        } else {
            /* @see base.php */
            /* insert new record */
            /* TODO disable invoice input field */
            $form_action = 0;
            OMP_buttons('14', '39', '37', $button_cancel_href);
        }
        $_OMP_fld_len = $_OMP_deliveries_len;
        $_OMP_html_srn = $srn + 1;
        $date_mark_start = '';
        $date_mark_end = '';
        $date_change = '';
        $origin_mark_start = '';
        $origin_mark_end = '';
        $origin_change = '';
        $eta_required = '';
        $eta_disabled = '';
        $eta_mark_start = '';
        $eta_mark_end = '';
        $eta_change = '';
        $destination_mark_start = '';
        $destination_mark_end = '';
        $destination_change = '';
        $truck_mark_start = '';
        $truck_mark_end = '';
        $truck_change = '';
        $note_mark_start = '';
        $note_mark_end = '';
        $note_change     = '';
        /* record[diff] is equal to values_from_file[diff] */
        if (isset($record['diff']['deliveries'])) {
            $del_diff = 0;
            // foreach ($record['diff']['deliveries'][$pgn] as $key=>$del) {
            foreach ($record['diff']['deliveries'][$del_diff] as $key=>$del) {
                switch ($key) {
                    case 'date':
                            $date_change =
                                strftime("%d/%m/%Y", strtotime($delivery[$key]));
                            $date_change = $_OMP_LC[38].' '.
                                $_OMP_LC[1900].' '.
                                $date_change.' '.
                                $_OMP_LC[1901];
                            $date_mark_start = '<mark>';
                            $date_mark_end = '</mark>';
                            break;
                    case 'origin':
                            $origin_change =
                                empty($delivery[$key]) ? 'NULL' : $delivery[$key];
                            $origin_change = $_OMP_LC[38].' '.
                                $_OMP_LC[1900].' '.
                                $origin_change.' '.
                                $_OMP_LC[1901];
                            $origin_mark_start = '<mark>';
                            $origin_mark_end = '</mark>';
                            break;
                    case 'eta':
                            $eta_change =
                                strftime("%d/%m/%Y", strtotime($delivery[$key]));
                            $eta_change = $_OMP_LC[38].' '.
                                $_OMP_LC[1900].' '.
                                $eta_change.' '.
                                $_OMP_LC[1901];
                            $eta_mark_start = '<mark>';
                            $eta_mark_end = '</mark>';
                            break;
                    case 'destination':
                            $destination_change =
                                empty($delivery[$key]) ? 'NULL' : $delivery[$key];
                            !empty($delivery[$key]) or $change = 'NULL';
                            $destination_change = $_OMP_LC[38].' '.
                                $_OMP_LC[1900].' '.
                                $destination_change.' '.
                                $_OMP_LC[1901];
                            $destination_mark_start = '<mark>';
                            $destination_mark_end = '</mark>';
                            break;
                    case 'truck':
                            $truck_change =
                                empty($delivery[$key]) ? 'NULL' : $delivery[$key];
                            $truck_change = $_OMP_LC[38].' '.
                                $_OMP_LC[1900].' '.
                                $truck_change.' '.
                                $_OMP_LC[1901];
                            $truck_mark_start = '<mark>';
                            $truck_mark_end = '</mark>';
                            break;
                    case 'note':
                            $note_change =
                                empty($delivery[$key]) ? 'NULL' : $delivery[$key];
                            $note_change = $_OMP_LC[38].' '.
                                $_OMP_LC[1900].' '.
                                $note_change.' '.
                                $_OMP_LC[1901];
                            $note_mark_start = '<mark>';
                            $note_mark_end = '</mark>';
                            break;
                }
                $del_diff++;
            }
            unset($del);
        }
        unset($delivery);
        $_OMP_html_srn = $srn + 1;
        /* OCR Shipment Form template */
        eval ("\$_OMP_html['record'] = \"".$_OMP_TPL[1702]."\";");
        $bales_mark_start = '';
        $bales_mark_end = '';
        $bales_change = '';
        $quantity_mark_start = '';
        $quantity_mark_end = '';
        $quantity_change = '';
        /* Delivery lines counter */
        $i = 0;
        if (isset($record['diff']['lines'])) {
            foreach ($record['diff']['lines'][$pgn] as $key=>$lines) {
                if (isset($delivery_lines[$i][$key])) {
                    switch ($key) {
                        case 'bales':
                            $bales_change = $_OMP_LC[38].' '.
                                $_OMP_LC[1900].' '.
                                $delivery_lines[$i][$key].' '.
                                $_OMP_LC[1901];
                            $bales_mark_start = '<mark>';
                            $bales_mark_end = '</mark>';
                            break;
                        case 'quantity':
                            $quantity_change = $_OMP_LC[38].' '.
                                $_OMP_LC[1900].' '.
                                $delivery_lines[$i][$key].' '.
                                $_OMP_LC[1901];
                            $quantity_mark_start = '<mark>';
                            $quantity_mark_end = '</mark>';
                            break;
                    }
                    $i++;
                }
            }
        }
        if (isset($record['items'])) {
            /* items record counter for template 1703 */
            $irn = 0;
            foreach($record['items'] as $item) {
                $irn++;
                /* init $item[pkey] because it's needed only for update
                 * and $item[ol_pkey] because it is looked up in ocr-post
                 * or it will trigger notice in eval()'d code
                 * in template 1703 */
                if ($form_action === 0) $item['pkey'] = $item['ol_pkey'] = ' ';
                eval ("\$_OMP_html['details'] .= \"".$_OMP_TPL[1703]."\";");
            }
        } else {
            /* blank shipment item input subform */
            $irn = 1;
            $item = array('pkey' => '', 'bales' => '', 'quantity' => '',
                'supplier_ref' => '', 'prod_pkey' => '', 'ol_pkey' => '');
            eval ("\$_OMP_list_rec .= \"".$_OMP_TPL[1703]."\";");
        }
        $srn++;
    } elseif ($page['type'] == 'SO') {
        /* @see base.php */
        OMP_buttons('14', '38', '37', $button_cancel_href);
        $_OMP_order_lines = $_OMP_conf_lines = ''; // temp
        $_OMP_fld_len = $_OMP_orders_len;
        $_OMP_html_osrn = $osrn + 1;
        eval ("\$_OMP_list_rec .= \"".$_OMP_TPL[1704]."\";");
        /* populate template 1705 with $values_from_file['diff'] */
        $product_required = '';
        $product_disabled = '';
        $quantity_required = '';
        $quantity_disabled = '';
        $quantity_change = '';
        $quantity_mark_start = '';
        $quantity_mark_end = '';
        $price_required = '';
        $price_disabled = '';
        $price_change = '';
        $price_mark_start = '';
        $price_mark_end = '';
        $plan_required = '';
        $plan_disabled = '';
        $plan_change = '';
        $plan_mark_start = '';
        $plan_mark_end = '';
        $eta_required = '';
        $eta_disabled = '';
        $eta_change = '';
        $eta_mark_start = '';
        $eta_mark_end = '';
        $order_lines = $page['db_order_lines'];
        if (isset($record['diff'])) {
            // TODO a cosa serve questa linea?
            $_OMP_fld_len_line = $_OMP_orders_lines_len;
            $soin = 0; // items records counter
            foreach($record['diff'] as $key=>$item) {
                $product_required = '';
                $product_disabled = 'disabled';
                $product_mark_start = '';
                $product_mark_end = '';
                $quantity_required = '';
                $quantity_disabled = 'disabled';
                $quantity_change = '';
                $quantity_mark_start = '';
                $quantity_mark_end = '';
                $price_required = '';
                $price_disabled = 'disabled';
                $price_change = '';
                $price_mark_start = '';
                $price_mark_end = '';
                $plan_required = '';
                $plan_disabled = 'disabled';
                $plan_change = '';
                $plan_mark_start = '';
                $plan_mark_end = '';
                $eta_required = '';
                $eta_disabled = 'disabled';
                $eta_change = '';
                $eta_mark_start = '';
                $eta_mark_end = '';
                if (isset($update[$key])) {
                    foreach ($update[$key] as $keyup=>$itemup) {
                        /* for every item that is different from the order-line
                        record we put the different value in the form fields */
                        $item[$keyup] = $itemup;
                        switch ($keyup) {
                            case 'product':
                                if (emtpy($order_lines[$key]['product'])) {
                                    $product_required = 'required';
                                    $product_disabled = '';
                                    $product_mark_start = '<mark>';
                                    $product_mark_end = '</mark>';
                                }
                                break;
                            case 'quantity':
                                $order_lines[$key]['quantity'] =
                                    preg_replace('/\D/', $_SESSION['dp'],
                                        $order_lines[$key]['quantity']);
                                $quantity_required = 'required';
                                $quantity_disabled = '';
                                $quantity_change = $_OMP_LC[38].' '.
                                    $_OMP_LC[1900].' '.
                                    $order_lines[$key]['quantity'].' '.
                                    $_OMP_LC[1901];
                                $quantity_mark_start = '<mark>';
                                $quantity_mark_end = '</mark>';
                                break;
                            case 'price':
                                $price_required = 'required';
                                $price_disabled = '';
                                $price_change = $_OMP_LC[38].' '.$_OMP_LC[1900].' '.
                                    $order_lines[$key]['price'].' '.$_OMP_LC[1901];
                                $price_mark_start = '<mark>';
                                $price_mark_end = '</mark>';
                                break;
                            case 'plan':
                                $plan_required = 'required';
                                $plan_disabled = '';
                                $plan_change =
                                    strftime("%d/%m/%Y",
                                            strtotime($order_lines[$key]['plan'])
                                            );
                                $plan_change = $_OMP_LC[38].' '.$_OMP_LC[1900].' '.
                                    $plan_change.' '.$_OMP_LC[1901];
                                $plan_mark_start = '<mark>';
                                $plan_mark_end = '</mark>';
                                break;
                            case 'eta':
                                $eta_required = 'required';
                                $eta_disabled = '';
                                $eta_change = strftime("%d/%m/%Y",
                                                strtotime($order_lines[$key]['eta'])
                                                );
                                $eta_change = $_OMP_LC[38].' '.$_OMP_LC[1900].' '.
                                    $eta_change.' '.$_OMP_LC[1901];
                                $eta_mark_start = '<mark>';
                                $eta_mark_end = '</mark>';
                                break;
                        }
                    }
                }
                $soin++;
                eval ("\$_OMP_list_rec .= \"".$_OMP_TPL[1705]."\";");
            }
        }
        $osrn++;
    } elseif ($page['type'] == 'payment') {
        /* @see base.php */
        OMP_buttons('14', '38', '37', $button_cancel_href);
        $_OMP_fld_len = $_OMP_invoices_len;
        $_OMP_html_prn = $prn + 1;
        eval ("\$_OMP_list_rec .= \"".$_OMP_TPL[1707]."\";");
        $prn++;
    } else {
        $_OMP_list_rec = 'Doc type ('.$page['type'].
            ') not yet supported (something\'s wrong)';
    }
    /* increase pages counter */
    $pgn++;
    eval("\$_OMP_html['ocr_pages'] .= \"".$_OMP_TPL[1706]."\";");
    $_OMP_html['record'] = '';
    $_OMP_html['details'] = '';
}
unset($page);
$_OMP_html['menu_button'] = '';
/* eval HTML page with record + deails */
eval("\$_OMP_html['include'] = \"".$_OMP_TPL[1701]."\";");
eval("\$_OMP_out = \"".$_OMP_TPL[0]."\";");
OMP_lose();
?>
