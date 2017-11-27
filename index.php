<?php

// error_reporting(E_ALL);

/**
 * Front-end of BookStore_XH.
 *
 * Copyright (c) 2012 Lubomyr Kudray (see license.txt)
 */

if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/* SEO - external URL redirect */
if (isset($_GET['bs_url']))
{
  header('Content-Type: text/html; charset=utf-8');
//
  $bs_invalid_request = $plugin_tx['bookstore']['bs_redirect'];
//
  if($_GET['bs_url']) {
    $bs_redir_url = $_GET['bs_url']; //
  if (!$bs_redir_url) { // if $bs_redir_url id empty, redirect to current page
    $bs_redir_url = $_SERVER['HTTP_REFERER'];
  }
/* validate URL */
  if (!preg_match('#(https?|ftp)://\S+[^\s.,>)\];\'\"!?]#i',$bs_redir_url)) {
    exit ('<htmL>
            <head>
             <title>'.$bs_invalid_request.'</title>
            </head>
            <body>
             <div style="color:#ff0000;margin-top:200px;text-align:center;">
              <h1>'.$bs_invalid_request.'</h1>
             </div>
            </body>
           </html>');
  }
  header("Location:$bs_redir_url"); // external link redirect
    exit();
  }
}

global $hjs;

define('BOOKSTORE_NAME','BookStore_XH');
define('BOOKSTORE_VERSION','1.2 2012.12.10');
include_once $pth['folder']['plugins'] . 'jquery/jquery.inc.php';
include_jQuery();
include_jQueryUI();

// include the upload class, as we will need it here to deal with the uploaded file
include($pth['folder']['plugins'].'bookstore/upload/class.upload.php');

// include the Zebra Pagination class
require $pth['folder']['plugins'] . 'bookstore/paging/Zebra_Pagination.php';

$hjs .=
    '<script type="text/javascript" src="' . $pth['folder']['plugins'] . 'bookstore/js/bookstore.js"></script>' . "\n"
  . '<script type="text/javascript" src="' . $pth['folder']['plugins'] . 'bookstore/js/jto/rowstyle.js"></script>'

/* zebra pagination CSS */
  . tag('link rel="stylesheet" type="text/css" media="all"
        href="' . $pth['folder']['plugins'] . 'bookstore/paging/css/zebra_pagination.css"') . "\n"
  . '<script type="text/javascript" src="' . $pth['folder']['plugins'] . 'bookstore/paging/js/zebra_pagination.js"></script>' . "\n";

/* tablePagination - style in bookstore stylesheet.css */
  include_jQueryPlugin('tablePagination',
      $pth['folder']['plugins'] . 'bookstore/js/jtp/jquery.tablePagination.0.5.ru.js') . "\n";

/* Easy Confirm Dialog SCRIPT */
  include_jQueryPlugin('easy-confirm-dialog',
      $pth['folder']['plugins'] . 'bookstore/js/jquery.easy-confirm-dialog.js') . "\n";

/* charCount SCRIPT */
  include_jQueryPlugin('charCount',
      $pth['folder']['plugins'] . 'bookstore/js/jquery.charCount.js') . "\n";

/* expandable SCRIPT */
  include_jQueryPlugin('expandable',
      $pth['folder']['plugins'] . 'bookstore/js/jquery.expandable.min.js') . "\n";

/* MaskEdInput SCRIPT */
  include_jQueryPlugin('maskedinput',
      $pth['folder']['plugins'] . 'bookstore/js/jquery.maskedinput-1.3.min.js') . "\n";

/* my-tooltips SCRIPT  */
  include_jQueryPlugin('style-my-tooltips',
      $pth['folder']['plugins'] . 'bookstore/js/jmt/jquery.style-my-tooltips.js') . "\n";

/* placeholder SCRIPT */
  include_jQueryPlugin('placeholder',
      $pth['folder']['plugins'] . 'bookstore/js/jph/jquery.placeholder.min.js') . "\n";

function PMA_getIp()
{
 global $REMOTE_ADDR;
 global $HTTP_X_FORWARDED_FOR, $HTTP_X_FORWARDED, $HTTP_FORWARDED_FOR,
        $HTTP_FORWARDED;
 global $HTTP_VIA, $HTTP_X_COMING_FROM, $HTTP_COMING_FROM;
 // Get some server/environment variables values
 if (empty($REMOTE_ADDR))
  {
   if (!empty($_SERVER) && isset($_SERVER['REMOTE_ADDR']))
    { $REMOTE_ADDR = $_SERVER['REMOTE_ADDR']; }
    else if (!empty($_ENV) && isset($_ENV['REMOTE_ADDR']))
     { $REMOTE_ADDR = $_ENV['REMOTE_ADDR'];    }
    else if (@getenv('REMOTE_ADDR'))
     { $REMOTE_ADDR = getenv('REMOTE_ADDR');   }
    } // end if
   if (empty($HTTP_X_FORWARDED_FOR))
    {
     if (!empty($_SERVER) && isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      { $HTTP_X_FORWARDED_FOR = $_SERVER['HTTP_X_FORWARDED_FOR']; }
      else if (!empty($_ENV) && isset($_ENV['HTTP_X_FORWARDED_FOR']))
       { $HTTP_X_FORWARDED_FOR = $_ENV['HTTP_X_FORWARDED_FOR']; }
      else if (@getenv('HTTP_X_FORWARDED_FOR'))
       { $HTTP_X_FORWARDED_FOR = getenv('HTTP_X_FORWARDED_FOR');}
    } // end if
   if (empty($HTTP_X_FORWARDED))
    {
     if (!empty($_SERVER) && isset($_SERVER['HTTP_X_FORWARDED']))
      { $HTTP_X_FORWARDED = $_SERVER['HTTP_X_FORWARDED']; }
      else if (!empty($_ENV) && isset($_ENV['HTTP_X_FORWARDED']))
      { $HTTP_X_FORWARDED = $_ENV['HTTP_X_FORWARDED']; }
      else if (@getenv('HTTP_X_FORWARDED'))
      { $HTTP_X_FORWARDED = getenv('HTTP_X_FORWARDED'); }
    } // end if
   if (empty($HTTP_FORWARDED_FOR))
    {
     if (!empty($_SERVER) && isset($_SERVER['HTTP_FORWARDED_FOR']))
      { $HTTP_FORWARDED_FOR = $_SERVER['HTTP_FORWARDED_FOR']; }
      else if (!empty($_ENV) && isset($_ENV['HTTP_FORWARDED_FOR']))
      { $HTTP_FORWARDED_FOR = $_ENV['HTTP_FORWARDED_FOR']; }
      else if (@getenv('HTTP_FORWARDED_FOR'))
      { $HTTP_FORWARDED_FOR = getenv('HTTP_FORWARDED_FOR'); }
    } // end if
   if (empty($HTTP_FORWARDED))
    {
     if (!empty($_SERVER) && isset($_SERVER['HTTP_FORWARDED']))
      { $HTTP_FORWARDED = $_SERVER['HTTP_FORWARDED']; }
      else if (!empty($_ENV) && isset($_ENV['HTTP_FORWARDED']))
       { $HTTP_FORWARDED = $_ENV['HTTP_FORWARDED']; }
      else if (@getenv('HTTP_FORWARDED'))
       { $HTTP_FORWARDED = getenv('HTTP_FORWARDED'); }
    } // end if
   if (empty($HTTP_VIA))
    {
     if (!empty($_SERVER) && isset($_SERVER['HTTP_VIA']))
      { $HTTP_VIA = $_SERVER['HTTP_VIA']; }
      else if (!empty($_ENV) && isset($_ENV['HTTP_VIA']))
       { $HTTP_VIA = $_ENV['HTTP_VIA']; }
      else if (@getenv('HTTP_VIA'))
       { $HTTP_VIA = getenv('HTTP_VIA'); }
    } // end if
   if (empty($HTTP_X_COMING_FROM))
    {
     if (!empty($_SERVER) && isset($_SERVER['HTTP_X_COMING_FROM']))
       { $HTTP_X_COMING_FROM = $_SERVER['HTTP_X_COMING_FROM']; }
      else if (!empty($_ENV) && isset($_ENV['HTTP_X_COMING_FROM']))
       { $HTTP_X_COMING_FROM = $_ENV['HTTP_X_COMING_FROM']; }
      else if (@getenv('HTTP_X_COMING_FROM'))
      { $HTTP_X_COMING_FROM = getenv('HTTP_X_COMING_FROM'); }
    } // end if
   if (empty($HTTP_COMING_FROM))
    {
     if (!empty($_SERVER) && isset($_SERVER['HTTP_COMING_FROM']))
      { $HTTP_COMING_FROM = $_SERVER['HTTP_COMING_FROM']; }
      else if (!empty($_ENV) && isset($_ENV['HTTP_COMING_FROM']))
       { $HTTP_COMING_FROM = $_ENV['HTTP_COMING_FROM']; }
      else if (@getenv('HTTP_COMING_FROM'))
      { $HTTP_COMING_FROM = getenv('HTTP_COMING_FROM'); }
    } // end if
   // Gets the default ip sent by the user
   if (!empty($REMOTE_ADDR)) { $direct_ip = $REMOTE_ADDR; }
   // Gets the proxy ip sent by the user
   $proxy_ip   = '';
   if (!empty($HTTP_X_FORWARDED_FOR))
     { $proxy_ip = $HTTP_X_FORWARDED_FOR; }
    else if (!empty($HTTP_X_FORWARDED))
     { $proxy_ip = $HTTP_X_FORWARDED;     }
    else if (!empty($HTTP_FORWARDED_FOR))
     { $proxy_ip = $HTTP_FORWARDED_FOR;   }
    else if (!empty($HTTP_FORWARDED))
     { $proxy_ip = $HTTP_FORWARDED;       }
    else if (!empty($HTTP_VIA))
     { $proxy_ip = $HTTP_VIA;             }
    else if (!empty($HTTP_X_COMING_FROM))
     { $proxy_ip = $HTTP_X_COMING_FROM;   }
    else if (!empty($HTTP_COMING_FROM))
     { $proxy_ip = $HTTP_COMING_FROM;     }
   // end if... else if...
   // Returns the true IP if it has been found, else FALSE
   if (empty($proxy_ip)) { return $direct_ip; /* True IP without proxy */ }
   else
   {
   $is_ip = preg_match('|^([0-9]{1,3}\.){3,3}[0-9]{1,3}|', $proxy_ip, $regs);
   if ($is_ip && (count($regs) > 0))
    {
     // True IP behind a proxy
     return $regs[0];
    }
    else
    {
     // Can't define IP: there is a proxy but we don't have
     // information about the true IP
     return FALSE;
    }
   } // end if... else...
} // end of the 'PMA_getIp()' function

function bookstore() {
  global $su,$sl,$pth,$ptx,$plugin_tx,$plugin_cf,$bs_output,$action,$bs_adddescription;

  $ptx = $plugin_tx['bookstore'];
  $pcf = $plugin_cf['bookstore'];
  $bs_imgpth = $pth['folder']['plugins']."bookstore/data/images/";
  $bs_iconpth = $pth['folder']['plugins']."bookstore/images/";
  $bs_datapth = $pth['folder']['plugins']."bookstore/data/";
  $bs_dataname = $pcf['file_data_name'].".db";
  $bs_datafile = $bs_datapth.$bs_dataname; // data file name

/* currentsection cookie */
  if (isset($_COOKIE['currentsection'])) {
    $bs_currentsection = $_COOKIE['currentsection'];
  }
/* books per page cookie */
  if (isset($_COOKIE['bookperpage'])) {
    $pcf['books_per_page'] = $_COOKIE['bookperpage'];
  }

/* file-substitute can be removed along with the book, restore file from backup */
  if(file_exists($bs_imgpth."bookstore_nocover.png")==false) {
    copy($bs_iconpth."bookstore_nocover.png",$bs_imgpth."bookstore_nocover.png");
  }
/* clean data file */
  if(file_exists($bs_datafile)) {
    $bs_arr_input = file($bs_datafile);
/* remove duplicates */
    $bs_arr_uniq = array_unique($bs_arr_input);
/* remove wrong string */
    for ($i=0; $i<count($bs_arr_uniq);$i++) {
      if (strstr($bs_arr_uniq[$i],'^^^^^^^^^^^^^^^^^^^^') or strstr($bs_arr_uniq[$i],'^^^^^^^^^^^^^^^^^')) {
        unset($bs_arr_uniq[$i]);
      }
/* correct book count */
      $bs_approve_data  = $bs_arr_uniq[$i];
      $bs_approve_info  = explode('^', $bs_approve_data);
      $bs_approve_value = $bs_approve_info[0];
      if ($bs_approve_value == "true") {
        $bs_data[] = $bs_arr_uniq[$i];
      }
    }
/* write file */
    $bs_text = implode('', $bs_arr_uniq);
    $f=fopen($bs_datafile,'w');
    flock($f,2);
    fwrite($f,"$bs_text");
    fclose($f);
  }
/* end - clean data file */
  else {
    $o .= '<h1 style="color:#ff0000;">Data file' . $bs_datafile . ' missing.';
/* if file missing, restore file from backup copy */
    copy($bs_datapth.$pcf['file_data_name'].".bak",$bs_datapth.$pcf['file_data_name'].".db");
  }

/* last book as "new" */
  $bs_last_book      = end($bs_data);
  $bs_last_book_info  = explode('^', $bs_last_book);
  $bs_last_book_title = $bs_last_book_info[4];

/* books data array */
/* show last added book first - set in Config */
  if ($pcf['book_last_first'] == true) {
    $bs_data = array_reverse($bs_data);
  }

  $bs_data_total = sizeof($bs_data);

/* books stored */
  $bs_sectionfile = $bs_datapth . $pcf['file_section_name'] . ".dat";
  $bs_arr_section = file($bs_sectionfile);
  $bs_arr_bookperpage = array($ptx['bs_books_show_all'], "1", "3", "5", "10");
  $bs_books_stored = sprintf($ptx['bs_books_stored'],$bs_data_total);

  $bs_output .=
      '<div class="bs_books_stored">'."\n"
    . '<h5 class="bs_books_stored">'.$bs_books_stored.'</h5>'
    . '</div>';
/* end - books stored */

/* manage book block */
  $bs_output .=
      '<div id="bs_book_manage_block">'
/* search */
    . '<fieldset class="bs_noborder">'
    . '<legend class="bs_legend">'.$ptx['bs_search_title'].'</legend>'
    . '<form action="#" name="books_search_form" method="post" enctype="multipart/form-data">'
    . tag('input name="input_books_search_text" id="bs_books_search_search" type="text" value="" placeholder="'.$ptx['bs_search_placeholder'].'"')."\n"
    . tag('input name="input_books_search_submit" id="bs_books_search_submit" type="submit" value="'.$ptx['bs_search_btn'].'" title="'.$ptx['bs_search_title'].'" ')."\n"
    . '</form>'
    . '</fieldset>'
/* end - search */

/* filter section array */
    . '<fieldset class="bs_noborder">'
    . '<legend class="bs_legend">'.$ptx['bs_sort_section'].'</legend>'
    . '<form action="#bs_book_section" name="section_sort_form" method="post" enctype="multipart/form-data">'
    . '<select id="bs_bookselect" name="current_section_select">';
  foreach ($bs_arr_section as $key => $bs_val) {
    $bs_output .= '<option class="bs_option">'.$bs_val.'</option>';
  }
  $bs_output .=
      '</select>'
    . '&nbsp;'
    . tag('input name="input_section_sort_submit" id="bs_section_sort_submit" type="submit" value="'.$ptx['bs_show_button'].'" title="'.$ptx['bs_sort_section_title'].'" ')."\n"
    . '</form>'
    . '</fieldset>'
/* end - filter section array */

/* books per page */
    . '<fieldset class="bs_noborder">'
    . '<legend class="bs_legend">'.$ptx['bs_books_per_page'].'</legend>'
    . '<form action="#bs_book_section" name="books_per_page_form" method="post" enctype="multipart/form-data">'
    . '<select name="books_per_page_select">';
    foreach($bs_arr_bookperpage as $key => $value) {
      $bs_output .= '<option class="bs_option_text_align">'.$value.'</option>';
    }
  $bs_output .=
      '</select>'
    . '&nbsp;'
    . tag('input name="input_books_per_page_submit" id="bs_books_per_page_submit" type="submit" value="'.$ptx['bs_show_button'].'" title="'.$ptx['bs_books_per_page_title'].'" ')."\n"
    . '</form>'
    . '</fieldset>'
/* end - books per page */
    . '</div>';
/* end - manage book block */

/* using search */
  if (isset($_POST['input_books_search_submit'])) {
    $bs_searchword = trim($_POST['input_books_search_text']);
    if (empty($bs_searchword)) {
      $bs_output .=
      '<div class="bs_bookalert">'.$ptx['bs_search_field_blank'].'</div>'."\n";
    }
    else {
      for ($i=0; $i<$bs_data_total;$i++) {
        if (stristr($bs_data[$i],$bs_searchword)==true) {
          $bs_arr_temp[]=$bs_data[$i];
        }
      }
      if (sizeof($bs_arr_temp) != 0) {
        $bs_data = $bs_arr_temp;
        $pcf['books_per_page'] = count($bs_arr_temp);
      }
      else {
        $bs_output .=
          '<div class="bs_bookalert">'.$ptx['bs_search_not_found'].'</div>'."\n";
      }
    }
  }
/* end - using search */

/* using section select */
  if (isset($_POST['input_section_sort_submit'])) {
    $bs_currentsection = trim($_POST['current_section_select']);
    if (!empty($bs_currentsection)) {
      for ($i=0; $i<$bs_data_total;$i++) {
       $bs_info = explode('^', $bs_data[$i]);
        $bs_section = $bs_info[3];
        if ($bs_section==$bs_currentsection)
          $bs_arr_temp[]=$bs_data[$i];
      }
      $bs_data = $bs_arr_temp;
    }
    if (sizeof($bs_data) == 0) {
      $bs_output .=
          '<div class="bs_bookalert">'.$ptx['bs_section_no_books'].'</div>'."\n";
    }
    $pcf['books_per_page'] = count($bs_arr_temp);
/* currentsection cookie */
    if ($ptx['cf_allow_cookies'] == true) {
      setcookie('currentsection', $bs_currentsection, time()+3600*24*7);
    }
/* end - currentsection cookie */
  }
/* end - using section select */

/* using pagination */

/* (Optional) Indicates whether values in query strings, other than
   those set in $base_url, should be preserved. Default is TRUE.
   */
  // $preserve_query_strings = $pcf['base_preserve_query_strings'];
/* (Optional) The base URL to be used when generating the navigation links */
  //  $base_url = $pcf['base_url_page'];

/* how many records should be displayed on a page? */

/* books per page cookie */
  if (isset($_POST['input_books_per_page_submit'])) {
    $pcf['books_per_page'] = trim($_REQUEST['books_per_page_select']);
    if ($pcf['books_per_page'] == $ptx['bs_books_show_all']) {
//      $pcf['books_per_page'] = count(file($bs_datafile));
      $pcf['books_per_page'] = $bs_data_total;
    }
    $bs_bookperpage = $pcf['books_per_page'];
    if ($ptx['cf_allow_cookies'] == true) {
      setcookie('bookperpage', $bs_bookperpage, time()+3600*24*7);
    }
  }
/* end - books per page cookie */

/* instantiate the pagination object */
  $pagination = new Zebra_Pagination();
/* the number of total records is the number of records in the array */
  $pagination->records($bs_data_total);
/* the number of records displayed on one page. */
  $records_per_page = $pcf['books_per_page'];
/* tell the class that there are 20 records displayed on one page */
  $pagination->records_per_page($records_per_page);
/* enable or disable padding numbers with zeroes */
  $pagination->padding(true);
/* sets the 1 page as the current page */
  // $pagination->set_page(1);
/* display links to number of pages */
  $pagination->selectable_pages(10);
/* here's the magick: we need to display *only* the records for the current page */
  $bs_data = array_slice($bs_data,
                           (($pagination->get_page() - 1) * $records_per_page),
                           $records_per_page
                          );
/* end - using pagination */

/* current page from total */
  $bs_pages_total = ceil(intval($bs_data_total)/intval($records_per_page));
  $bs_current_page = sprintf($ptx['bs_books_page_from'],$pagination->get_page(),$bs_pages_total);
/* render the pagination links generate output but don't echo it, but return it instead*/
  $bs_output .=
      $pagination->render(true)
    . '<div style="clear:both"></div>'
    . '<div><em>'.$bs_current_page.tag('br').$ptx['bs_books_per_page'].": ". $pcf['books_per_page'].'</em></div>';
/* end - current page from total */

/* DRAW BOOKS */

  foreach ($bs_data as $index => $bs_bookrecord) {
    $bs_info = explode('^', $bs_data[$index]);
    $bs_approve         = $bs_info[0];
    $bs_user            = $bs_info[1];
    $bs_email           = $bs_info[2];
    $bs_section         = $bs_info[3];
    $bs_title           = $bs_info[4];
    $bs_author          = $bs_info[5];
    $bs_editor          = $bs_info[6];
    $bs_year            = $bs_info[7];
    $bs_genre           = $bs_info[8];
    $bs_site            = $bs_info[9];
    $bs_sitename        = $bs_info[10];
    $bs_customfieldname = $bs_info[11];
    $bs_customfieldtext = $bs_info[12];    
    $bs_external        = $bs_info[13];
    $bs_online          = $bs_info[14];
    $bs_desc            = $bs_info[15];
    $bs_image           = $bs_info[16];
    $bs_date            = $bs_info[17];
    $bs_ip              = $bs_info[18];
    $bs_ref             = $bs_info[19];

/* optomise link for SEO  */
    $bs_site      = '?bs_url='.$bs_site;
    $bs_external  = '?bs_url='.$bs_external;
    $bs_online    = '?bs_url='.$bs_online;

/* clean tags */
    $bs_desc = strip_tags($bs_desc);
/* load default image */
    if (empty($bs_image)) {
      $bs_image = "bookstore_nocover.png";
    }
/* draw book */
    if ($bs_approve == "true") {
      if (!empty($bs_title)) {
        $bs_output .=
          '<div id="bs_bookblock" class="bs_main">
            <div class="bs_image">';
        if ($pcf['show_cover_image'] == "true") {
/* draw new book image */
          if ($bs_title == $bs_last_book_title) {
            if ($sl == 'ru') {
              $bs_output .= tag('img src="'.$bs_iconpth.'book_new_ru.png"')."\n";
            }
            else {
              $bs_output .= tag('img src="'.$bs_iconpth.'book_new.png"')."\n";
            }
          }
/* draw cover */
          $bs_output .=
            tag('img style="margin-bottom:'.$pcf['image_margin_bottom'].'px" src="'.$bs_imgpth.$bs_image.'" width="'.$pcf['image_width'].'" height="'.$pcf['image_height'].'" border="0" alt=""').tag('br')."\n";
        }
        if ($pcf['show_external_file'] == "true") {
          $bs_output .=
            tag('img src="'.$bs_iconpth.'download.png" width="'.$pcf['icon_size'].'" height="'.$pcf['icon_size'].'" alt=""')."&nbsp;"."\n"
          . '<a href="'.$bs_external.'" title="'.$ptx['bs_external_file'].'" target="_blank">'.$ptx['bs_external_download'].'</a><br />';
        }
/* draw ecternal links */
        if ($pcf['show_read_online'] == "true") {
          $bs_output .=
            tag('img src="'.$bs_iconpth.'binoculars.png" border="0" width="'.$pcf['icon_size'].'" height="'.$pcf['icon_size'].'" alt=""')."&nbsp;"."\n"
          . '<a href="'.$bs_online.'" title="'.$ptx['bs_read_online_link'].'" target="_blank">'.$ptx['bs_read_online'].'</a>';
        }
        $bs_output .=
            '</div>
            <div>
              <h6 class="bs_section">'.$bs_section.'</h6>
              <h3 class="bs_title">'.$bs_title.'</h3>
              <p class="bs_para">'.$ptx['bs_author'].'<span class="bs_string">'.$bs_author.'</span></p>';
        if ($pcf['show_editor'] == "true") {
          $bs_output .=
            '<p class="bs_para">'.$ptx['bs_editor'].'<span class="bs_string">'.$bs_editor.'</span></p>';
        }
        if ($pcf['show_year'] == "true") {
          $bs_output .=
            '<p class="bs_para">'.$ptx['bs_year'].'<span class="bs_string">'.$bs_year.'</span></p>';
        }
        if ($pcf['show_genre'] == "true") {
          $bs_output .=
            '<p class="bs_para">'.$ptx['bs_genre'].'<span class="bs_string">'.$bs_genre.'</span></p>';
        }
        if ($pcf['show_site'] == "true") {
          $bs_output .=
            '<p class="bs_para">'.$ptx['bs_site'].'<span class="bs_string"><a href="'.$bs_site.'" target="_blank">'.$bs_sitename.'</a></span></p>';
        }
        if ($pcf['show_custom_field']=="true") {
          if (!empty($bs_customfieldname) && !empty($bs_customfieldtext)) {    
            $bs_output .=
              '<p class="bs_para">'.$bs_customfieldname.'<span class="bs_string">'.$bs_customfieldtext.'</span></p>';
          }
        }
        if ($pcf['show_desc'] == "true") {
          $bs_output .=
            '<h6 class="bs_desc">'.$ptx['bs_description'].'</h6>'
          . '<span>'.tag('br').$bs_desc.'</span>';
        }
        $bs_output .=
          '</div>
          <div style="clear:both"></div>
          </div>';
      }
    }
  }
/* end - DRAW BOOKS */

/* current page from total */
  $bs_output .=
      $pagination->render(true)
    . '<div style="clear:both"></div>'
    . '<div><em>'.$bs_current_page.tag('br').$ptx['bs_books_per_page'].": ". $pcf['books_per_page'].'</em></div>';
/* end - current page from total */

/* ADD BOOK */

  $text_length_a = sprintf($ptx['bs_max_text_length'],$pcf['book_maxlength_a']);
  $text_length_b = sprintf($ptx['bs_max_text_length'],$pcf['book_maxlength_b']);
  $text_length_c = sprintf($ptx['bs_max_text_length'],$pcf['book_maxlength_c']);
  $text_length_d = sprintf($ptx['bs_max_text_length'],$pcf['book_maxlength_d']);
  $text_length_e = sprintf($ptx['bs_max_text_length'],$pcf['book_maxlength_e']);
  $text_length_f = sprintf($ptx['bs_max_text_length'],$pcf['book_maxlength_f']);

  $bs_output .=
/* new book button */
      '<div id="add_new_book">'
    . tag('input id="add_new_book_btn" type="button" value="'.$ptx['bs_newbook_btn'].'"')."\n"
    . '</div>'
/* end - new book button */
    . '<div id="show_book_form">'
    . '<fieldset class="bs_noborder">'
    . '<legend class="bs_bookfield_edit">'.$ptx['bs_new_book_form'].'</legend><br />'
    . '<form id="bs_add_new_book_form" action="#add_new_book" name="book_form" method="post" enctype="multipart/form-data">'
/* book User */
    . '<label class="bs_label" for="bs_book_add_user_limit"><span class="bs_require">*</span>'.$ptx['bs_user']." ".'<span class="bs_optional">'.$text_length_b.'</span></label>'."\n"
    . tag('input id="bs_book_add_user_limit" name="book_add_user" type="text" maxlength="'.$pcf['book_maxlength_b'].'" value="" placeholder="'.$ptx['bs_not_displayed'].'" required'). tag('br')."\n"
/* book Email */
    . '<label class="bs_label" for="bs_book_add_email"><span class="bs_require">*</span>'.$ptx['bs_email']." ".'<span class="bs_optional">'.$text_length_c.'</span></label>'."\n"
    . tag('input id="bs_book_add_email" name="book_add_email" type="email" maxlength="'.$pcf['book_maxlength_c'].'" value="" placeholder="'.$ptx['bs_not_displayed'].'" required') . tag('br')."\n"
/* book Section */
    . '<label class="bs_label" for="bs_book_add_section_select"><span class="bs_require">*</span>'.$ptx['bs_section'].'</label>'."\n"
    . '<select id="bs_book_add_section_select" name="book_add_section">';
    foreach ($bs_arr_section as $bs_val) {
      $bs_output .= '<option>'.$bs_val.'</option>';
    }
  $bs_output .=
      '</select>'. tag('br')."\n"
/* book Title */
    . '<label class="bs_label" for="bs_book_add_title"><span class="bs_require">*</span>'.$ptx['bs_title']." ".'<span class="bs_optional">'.$text_length_d.'</span></label>'."\n"
    . tag('input id="bs_book_add_title" name="book_add_title" type="text" maxlength="'.$pcf['book_maxlength_d'].'" value="" required') . tag('br')."\n"
/* book Author */
    . '<label class="bs_label" for="bs_book_add_author"><span class="bs_require">*</span>'.$ptx['bs_author']." ".'<span class="bs_optional">'.$text_length_c.'</span></label>'."\n"
    . tag('input id="bs_book_add_author" name="book_add_author" type="text" maxlength="'.$pcf['book_maxlength_c'].'" value="" required') . tag('br')."\n"
/* book Editor */
    . '<label class="bs_label" for="bs_book_add_editor"><span class="bs_optional">*</span>'.$ptx['bs_editor']." ".'<span class="bs_optional">'.$text_length_c.'</span></label>'."\n"
    . tag('input id="bs_book_add_editor" name="book_add_editor" type="text" maxlength="'.$pcf['book_maxlength_c'].'" value=""') . tag('br')."\n"
/* book Year */
    . '<label class="bs_label" for="book_add_year_input"><span class="bs_require">*</span>'.$ptx['bs_year'].'</label>'."\n"
    . tag('input id="book_add_year_input" name="book_add_year" type="text" value="" required'). tag('br')."\n"
/* book Genre */
    . '<label class="bs_label" for="bs_book_add_genre"><span class="bs_require">*</span>'.$ptx['bs_genre']." ".'<span class="bs_optional">'.$text_length_a.'</span></label>'."\n"
    . tag('input id="bs_book_add_genre" name="book_add_genre" type="text" maxlength="'.$pcf['book_maxlength_a'].'" value="" required') . tag('br')."\n"
/* book Site */
    . '<label class="bs_label" for="bs_book_add_site"><span class="bs_require">*</span>'.$ptx['bs_site']." ".'<span class="bs_optional">'.$text_length_e.'</span></label>'."\n"
    . tag('input id="bs_book_add_site" name="book_add_site" type="url" maxlength="'.$pcf['book_maxlength_e'].'" value="http://"') . tag('br')."\n"
/* book Site name*/
    . '<label class="bs_label" for="bs_book_add_sitename"><span class="bs_require">*</span>'.$ptx['bs_sitename']." ".'<span class="bs_optional">'.$text_length_b.'</span></label>'."\n"
    . tag('input id="bs_book_add_sitename" name="book_add_sitename" type="text" maxlength="'.$pcf['book_maxlength_b'].'" value=""') . tag('br')."\n"
/* book External file */
    . '<label class="bs_label" for="bs_book_add_external"><span class="bs_optional">*</span>'.$ptx['bs_external_file']." ".'<span class="bs_optional">'.$text_length_e.'</span></label>'."\n"
    . tag('input id="bs_book_add_external" name="book_add_external" type="url" maxlength="'.$pcf['book_maxlength_e'].'" value="http://"') . tag('br')."\n"
/* book Read online */
    . '<label class="bs_label" for="bs_book_read_online_link"><span class="bs_optional">*</span>'.$ptx['bs_read_online']." ".'<span class="bs_optional">'.$text_length_e.'</span></label>'."\n"
    . tag('input id="bs_book_read_online_link" name="book_read_online_link" type="url" maxlength="'.$pcf['book_maxlength_e'].'" value="http://"') . tag('br')."\n"
/* book Custom field name */
    . '<label class="bs_label" for="bs_book_custom_field_name"><span class="bs_optional">*</span>'.$ptx['bs_custom_field']." ".'<span class="bs_optional">'.$text_length_a.'</span></label>'."\n"
    . tag('input id="bs_book_custom_field_name" name="book_custom_field_name" type="text" maxlength="'.$pcf['book_maxlength_a'].'" value="" placeholder="'.$ptx['bs_custom_field_name'].'" ') . tag('br')."\n"
/* book Custom field text*/
    . '<label class="bs_label" for="bs_book_custom_field_text"><span class="bs_optional">*</span>'.$ptx['bs_custom_field_text']." ".'<span class="bs_optional">'.$text_length_a.'</span></label>'."\n"
    . tag('input id="bs_book_custom_field_text" name="book_custom_field_text" type="text" maxlength="'.$pcf['book_maxlength_a'].'" value="" ') . tag('br')."\n"
/* book Description */
    . '<label class="bs_label" for="bs_description_area"><span class="bs_require">*</span>'.$ptx['bs_description'].'</label>'.'<span class="bs_optional">'.$ptx['bs_description_len']." ".$pcf['descript_len'].'</span>'.tag('br')."\n"
    . '<textarea id="bs_description_area" name="book_add_description" class="bs_textarea" rows="'.$pcf['area_height'].'" cols="'.$pcf['area_width'].'" maxlength="'.$pcf['book_maxlength_f'].'" required></textarea>' . tag('br')."\n"
/* book Image Upload */
    . '<label class="bs_label" for="bs_book_add_image_input"><span class="bs_require">*</span>'.$ptx['bs_addimg'].'</label>'."\n"
    . tag('input type="file" id="bs_book_add_image_input" name="book_add_image" required '). tag('br')."\n"
/* required field warning */
    . tag('hr')."\n"
    . '<span class="bs_require">*</span>- '.$ptx['bs_books_required'].tag('br')."\n";
/* book Approve Message */
    if ($pcf['book_approve'] == "false") {
      $bs_output .=
          '<span class="bs_approve_false">'.$ptx['bs_book_approve_mess'].'</span>'. tag('br')."\n";
    }
/* add book button */
  $bs_output .=
      tag('br')."\n"
    . tag('input id="save_new_book" name="form_submit" type="submit" value="'.$ptx['bs_addbook_btn'].'"')."\n"
/* book Form */
    . '</form>' // id="show_book_form"
    . '</fieldset>'
    . '<div style="clear:both"></div>'
    . '</div>'; // id="show_book_form"

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bs_approve             = "";
    $bs_adduser             = isset($_POST["book_add_user"]) ? $_POST["book_add_user"] : null; /* required */
    $bs_addemail            = isset($_POST["book_add_email"]) ? $_POST["book_add_email"] : null; /* required */
    $bs_addsection          = isset($_POST["book_add_section"]) ? $_POST["book_add_section"] : null; /* required */
    $bs_addtitle            = isset($_POST["book_add_title"]) ? $_POST["book_add_title"] : null; /* required */
    $bs_addauthor           = isset($_POST["book_add_author"]) ? $_POST["book_add_author"] : null; /* required */
    $bs_addeditor           = isset($_POST["book_add_editor"]) ? $_POST["book_add_editor"] : null;
    $bs_addyear             = isset($_POST["book_add_year"]) ? $_POST["book_add_year"] : null; /* required */
    $bs_addgenre            = isset($_POST["book_add_genre"]) ? $_POST["book_add_genre"] : null; /* required */
    $bs_addsite             = isset($_POST["book_add_site"]) ? $_POST["book_add_site"] : null;
    $bs_addsitename         = isset($_POST["book_add_sitename"]) ? $_POST["book_add_sitename"] : null;
    $bs_addcustomfieldname  = isset($_POST["book_custom_field_name"]) ? $_POST["book_custom_field_name"] : null; 
    $bs_addcustomfieldtext  = isset($_POST["book_custom_field_text"]) ? $_POST["book_custom_field_text"] : null;
    $bs_addexternal         = isset($_POST["book_add_external"]) ? $_POST["book_add_external"] : null;
    $bs_addreadonline       = isset($_POST["book_read_online_link"]) ? $_POST["book_read_online_link"] : null;
    $bs_adddescription      = isset($_POST["book_add_description"]) ? $_POST["book_add_description"] : null; /* required */
    $bs_addimage            = isset($_POST["book_add_image"]) ? $_POST["book_add_image"] : null; /* required */
    $bs_addimage_name       = isset($_FILES["book_add_image"]["name"]) ? $_FILES["book_add_image"]["name"] : null;
    $bs_adddate             = date('d.m.Y, H:i:s'); // date("Y-m-d H:i:s") , date('j.m.Y')
    $bs_addip               = PMA_getIp();
    $bs_addref              = $_SERVER['HTTP_REFERER'];
    
    if ($pcf['book_approve'] == "false") {
      $bs_approve = "false";
    }
    else {
      $bs_approve = "true";
    }
/* slashes handing */
//    $bs_adddescription = stsl($bs_adddescription);
/* HTML and PHP tags stripped from a given str */
//    $bs_adddescription = strip_tags($bs_adddescription);

    $bs_added = strip_tags("$bs_approve^$bs_adduser^$bs_addemail^$bs_addsection^$bs_addtitle^$bs_addauthor^$bs_addeditor^$bs_addyear^$bs_addgenre^$bs_addsite^$bs_addsitename^$bs_addcustomfieldname^$bs_addcustomfieldtext^$bs_addexternal^$bs_addreadonline^$bs_adddescription^$bs_addimage_name^$bs_adddate^$bs_addip^$bs_addref^\n");
    $bs_added = stsl($bs_added);
/* return if sting broken */
      if (strstr($bs_added,'^^^^^^^^^^^^^^^^^^^^')) {
        header('Location: ?' . $su);
      }
/* else - write file */
    $f=fopen($bs_datafile,'a');
    flock ($f,2);
    fwrite($f,$bs_added);
    fclose($f);
  }

  $bs_desc_len = strlen($bs_adddescription);

  if (isset($_POST['form_submit'])) {
/* Image Upload */
    $bs_file     = $_FILES["book_add_image"];
    $bs_handle   = new Upload($bs_file, $pcf['file_class_lang']);
/* Settings */
  /* uoliad images only */
    $bs_handle->allowed = array("image/*");
  /* file_safe_name formats the filename (spaces changed to _) (default: true) */
    $bs_handle->file_safe_name = false; // false;
  /* file_max_size sets maximum upload size (default: upload_max_filesize from php.ini) */
    $bs_handle->file_max_size = '204800'; // 200 KB
  /* if filename exists */
    $bs_handle->file_auto_rename = $pcf['file_auto_rename']; // false";
  /* rewrite file, if exists */
    $bs_handle->file_overwrite = false; // true;
  /* add Watermark left bottom */
    //  $bs_handle->image_watermark = $pth['folder']['plugins'].$pcf['image_watermark'];
  /* watermark on greyscale pic, absolute position */
    //  $bs_handle->image_watermark_x = $pcf['image_watermark_x']; // 10;
    //  $bs_handle->image_watermark_y = $pcf['image_watermark_y']; // 10;
  /* image greyscale */
    //  $bs_handle->image_greyscale = $pcf['image_greyscale']; //"false";
  /* Resize the image in compliance with the proportions */
    $bs_handle->image_resize = $pcf['image_resize'];
    $bs_handle->image_x      = $pcf['image_width'];
    $bs_handle->image_y      = $pcf['image_height'];
    $bs_handle->image_ratio  = $pcf['image_ratio'];
  /* increase image to full size with background color */
    $bs_handle->image_ratio_fill        = $pcf['image_ratio_fill'];
    $bs_handle->image_background_color  = $pcf['image_background_color'];
  /* Frame */
    $bs_handle->image_border       = $pcf['image_border'];
    $bs_handle->image_border_color = $pcf['image_border_color'];
  /* Fade Effect to black with reflection and 60 percent reflection opacity */
    //  $bs_handle->image_reflection_height  = "25%";
    //  $bs_handle->image_default_color      = "#000000";
    //  $bs_handle->image_reflection_opacity = 60;
  /* 20px black and white bevel */
    //  $bs_handle->image_bevel           = 10;
    //  $bs_handle->image_bevel_color1    = '#FFFFFF';
    //  $bs_handle->image_bevel_color2    = '#000000';
  /* overlayed transparent label with absolute position */
    $bs_handle->image_text                    = $pcf['label_text'];
    $bs_handle->image_text_background         = $pcf['label_text_background'];
    $bs_handle->image_text_background_opacity = $pcf['label_text_background_opacity'];
    $bs_handle->image_text_x                  = $pcf['label_text_x'];
    $bs_handle->image_text_y                  = $pcf['label_text_y'];
    $bs_handle->image_text_padding            = $pcf['label_text_padding'];
    $bs_handle->image_text_font               = $pcf['label_text_font'];
    $bs_handle->image_text_direction          = $pcf['label_text_direction'];
/* Processing */
    $bs_handle->Process($bs_imgpth);
    if ($bs_handle->processed) {
      $bs_output .= $ptx['bs_adding_succes'];
    }
    else {
      $bs_output .= "&#8277;&nbsp;".$ptx['bs_just_error'] . $bs_handle->error . tag('br')."\n";
    }
    $bs_handle->Clean();

/* end - Image Upload */
    $bs_output .= '<div id="bs_book_alert" class="bs_bookalert">';
    if (empty($bs_adduser))             {$bs_output .= "&#8277;&nbsp;".$ptx['bs_name_alert'].tag('br')."\n";}
    else if (empty($bs_addemail))       {$bs_output .= "&#8277;&nbsp;".$ptx['bs_email_alert'].tag('br')."\n";}
    else if (empty($bs_addtitle))       {$bs_output .= "&#8277;&nbsp;".$ptx['bs_title_alert'].tag('br')."\n";}
    else if (empty($bs_addauthor))      {$bs_output .= "&#8277;&nbsp;".$ptx['bs_author_alert'].tag('br')."\n";}
    else if (empty($bs_addyear))        {$bs_output .= "&#8277;&nbsp;".$ptx['bs_year_alert'].tag('br')."\n";}
    else if (empty($bs_addgenre))       {$bs_output .= "&#8277;&nbsp;".$ptx['bs_genre_alert'].tag('br')."\n";}
    else if (empty($bs_adddescription)) {$bs_output .= "&#8277;&nbsp;".$ptx['bs_decript_alert'].tag('br')."\n";}
    else if ($bs_desc_len > $pcf['descript_len'])
                                        {$bs_output .= "&#8277;&nbsp;".$ptx['bs_description_len'] .
                                                        $bs_desc_len .
                                                        $ptx['bs_decription_maxlen'] .
                                                        $pcf['descript_len'].tag('br')."\n";}
    else {
      header('Location: ?' . $su);
    }
    $bs_output .= '</div>';
  }

/* end - ADD BOOK */

  return $bs_output;

}
?>