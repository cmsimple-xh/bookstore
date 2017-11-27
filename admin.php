<?php

// error_reporting(E_ALL);

/**
 * Back-end of BookStore .
 *
 * Copyright (c) 2012 Lubomyr Kudray (http://codestore.netii.net/)
 */

if( ! defined('CMSIMPLE_XH_VERSION'))
{
  header('HTTP/1.0 403 Forbidden');
  exit;
}

function bookstore_version()
{
   global $pth,$plugin_tx;

   $ptx = $plugin_tx['bookstore'];
   return '<h1><a href="http://do.comeze.com/?BookStore_XH_Plugin" target="_blank">' . BOOKSTORE_NAME . '</a></h1>' . "\n"
    . tag('img src="' . $pth['folder']['plugins'] . 'bookstore/bookstore.png" class="bookstore_plugin_icon"')
    . '<p>Version: ' . BOOKSTORE_VERSION . tag('br') . "\n"
    . 'Copyright &copy; 2012 <a class="pushLink" href="http://codestore.netii.net/">Lubomyr Kudray</a></p>'
    . tag('br')
    . '<p>Create and manage online book catalog with Bookstore_XH plugin.</p>' . "\n"
    . '<p>Further features are explained in a <a href="' . $pth['folder']['plugins'] . 'bookstore/help/help.htm" target="_blank">help</a>.</p>'
    . tag('br') . "\n"
    . 'Huge thanks to <a class="pushLink" href="http://3-magi.net/" target="_blank">Christoph M. Becker</a>.</p>'
    . tag('hr')
    . '<p class="distributes">This program is free software: you can redistribute it and/or modify'
    . ' it under the terms of the GNU General Public License as published by'
    . ' the Free Software Foundation, either version 3 of the License, or'
    . ' (at your option) any later version.</p>' . "\n"
    . '<p class="distributes">This program is distributed in the hope that it will be useful,'
    . ' but WITHOUT ANY WARRANTY; without even the implied warranty of'
    . ' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
    . ' GNU General Public License for more details.</p>' . "\n"
    . '<p class="distributes">You should have received a copy of the GNU General Public License'
    . ' along with this program.<br />If not, see'
    . ' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>' . "\n"
    . '<p class="warranty">there is NO WARRANTY for the program, to the extent permitted by applicable'
    . ' law. except when otherwise stated in writing the copyright holders and/or other parties provide'
    . ' the program "as is" without warranty of any kind, either expressed or implied, including,'
    . ' but not limited to, the implied warranties of merchantability and fitness for a particular'
    . ' purpose. the entire risk as to the quality and performance of the program is with you.'
    . ' should the program prove defective, you assume the cost of all necessary servicing,'
    . ' repair or correction.</p>';
}

/**
 * Returns requirements information view.
 *
 * @return string  The (X)HTML.
 */
function bookstore_system_check()
{
  global $pth, $tx, $plugin_tx, $plugin_cf;
  define('BOOKSTORE_PHP_VERSION', '5.4.4');
  $ptx = $plugin_tx['bookstore'];
  $imgdir = $pth['folder']['plugins'] . 'bookstore/images/';
  $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
  $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
  $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
  $o = tag('hr') . '<h4>' . $ptx['syscheck_title'] . '</h4>'
    . (version_compare(PHP_VERSION, BOOKSTORE_PHP_VERSION) >= 0? $ok: $fail)
    . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_phpversion'], BOOKSTORE_PHP_VERSION)
    . tag('br') . tag('br');
  foreach(array() as $ext) {
    $o .= (extension_loaded($ext)? $ok: $fail)
       . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_extension'], $ext) . tag('br');
  }
  $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8'? $ok: $warn)
    . '&nbsp;&nbsp;' . $ptx['syscheck_encoding'] . tag('br');
  $o .= ( ! get_magic_quotes_runtime()? $ok: $warn)
    . '&nbsp;&nbsp;' . $ptx['syscheck_magic_quotes'] . tag('br') . tag('br');
	$folders = array();
  foreach(array('config/', 'css/', 'data', 'languages/') as $folder) {
    $folders[] = $pth['folder']['plugins'] . 'bookstore/' . $folder;
  }
  foreach($folders as $folder) {
    $o .= (is_writable($folder)? $ok: $warn)
      . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_writable'], $folder) . tag('br');
  }
  return $o;
}

/**
 * Handle the plugin administration.
 */
if(function_exists('XH_wantsPluginAdministration') && XH_wantsPluginAdministration('bookstore')
  || isset($bookstore) && $bookstore == 'true')
{
  $ERROR = '';
  $plugin = basename(dirname(__FILE__), "/");
  $ptx = $plugin_tx['bookstore'];
  $o .= print_plugin_admin('on');

  global  $pth,$ptx,$plugin_tx,$plugin_cf,$ed_section;

  $ptx = $plugin_tx['bookstore'];
  $pcf = $plugin_cf['bookstore'];

  $bs_imgpth = $pth['folder']['plugins']."bookstore/data/images/";
  $bs_iconpth = $pth['folder']['plugins']."bookstore/images/";
  $bs_datapth = $pth['folder']['plugins']."bookstore/data/";
  $bs_dataname = $pcf['file_data_name'].".db";
  $bs_backupname = $pcf['file_data_name'].".bak";
  $bs_datafile = $bs_datapth.$bs_dataname; // data file name
  $bs_backupfile = $bs_datapth.$bs_backupname; // backup file name

  switch($admin)
  {
    case '': $o .= bookstore_version() . bookstore_system_check();
    break;
    case 'plugin_main': $o .= '<h3>'.$ptx['bs_book_list'].'</h3>';
/* data file */
   if(file_exists($bs_datafile)) {
		$bs_data = file($bs_datafile);
    }
    else {
      $o .= '<h1 class="bs_bookalert">'.$ptx['bs_file_data_missing'] . $bs_datafile . '<h1>';
    }
/* user wants to delete a book */
    if ($action == 'delete_book') {
/* write backup file */
      $bs_temptext = implode('', $bs_data);
      $fp_backup = fopen($bs_backupfile, "w"); // write mode
      fwrite($fp_backup, "$bs_temptext");
      fclose($fp_backup);
/* remove linked image */
      $index = $_GET['book_id'];
      foreach ($bs_data as $index => $bs_bookrecord) {
        $bs_info = explode('^', $bs_data[$index]);
      }
      $bs_filename = $bs_imgpth.$bs_info[16]; 
      if(!empty($bs_info[16])) {
        unlink($bs_filename);
      }
/* remove book from already read $bs_data */
      array_splice($bs_data, $index, 1);
/* write $bs_data back to file */
      if (($bs_temp = fopen($bs_datafile, 'wb')) === false
          || fwrite($bs_temp, implode('', $bs_data)) === false)
      {
        e('cntsave', 'file', $bs_datafile);
      }
      if ($bs_temp !== false) fclose($bs_temp);
        header('Location: ?&bookstore&admin=plugin_main&action=plugin_text');
    }
/* section file */
    $bs_sectionfile = $bs_datapth . $pcf['file_section_name'] . ".dat";
    if(file_exists($bs_sectionfile)) {
      $bs_section_array = file($bs_sectionfile);
    }
    else {
      $o .= '<h1 style="color:#ff0000;">'.$ptx['bs_file_section_missing'] . $bs_datafile . '<h1>';
    }
/* records count from file */
    $bs_total = sizeof($bs_data);
/* book table icon */
    $bs_book_blank = tag('img id="blank_book_img" src="'.$bs_iconpth.'book_blank.png" width="16" height="16" alt="'.$ptx['bs_book_info'].'" border="0" ')."\n";
    $bs_book_info = tag('img id="info_book_img" src="'.$bs_iconpth.'book_info.png" width="16" height="16" alt="'.$ptx['bs_book_info'].'" title="'.$ptx['bs_book_info'].'" border="0" ')."\n";
    $bs_book_edit = tag('img id="edit_book_img" src="'.$bs_iconpth.'book_edit.png" width="16" height="16" alt="'.$ptx['bs_book_edit'].'" title="'.$ptx['bs_book_edit'].'" border="0" ')."\n";
    $bs_book_delete = tag('img src="'.$bs_iconpth.'book_delete.png" width="16" height="16" alt="'.$ptx['bs_book_delete'].'" title="'.$ptx['bs_book_delete'].'" border="0" ')."\n";
    $bs_book_approve_true_img = tag('img src="'.$bs_iconpth.'book_approve_true.png" width="16" height="16" alt="'.$ptx['bs_book_approve_true'].'" title="'.$ptx['bs_book_approve_true'].'" border="0" ')."\n";
    $bs_book_approve_false_img = tag('img src="'.$bs_iconpth.'book_approve_false.png" width="16" height="16" alt="'.$ptx['bs_book_approve_false'].'" title="'.$ptx['bs_book_approve_false'].'" border="0" ')."\n";
    $bs_book_damaged_record_img = tag('img src="'.$bs_iconpth.'book_error.png" width="16" height="16" alt="'.$ptx['bs_book_approve_false'].'" title="'.$ptx['bs_book_damaged_record'].'" border="0" ')."\n";
/* books stored */
    $bs_books_stored = sprintf($ptx['bs_books_stored'],$bs_total);
    $o .=
        '<div class="bs_books_stored">'
      . '<h5 class="bs_books_stored">'.$bs_books_stored.'</h5>'
      . '</div>'
/* end - books stored */
      . '<div id="bs_book_list">'
      . '<table id="book_table">'
      . '<thead class="bs_thead">'
      . '<th width="20" class="{sorter: \'currency\'}">ID</th>'
      . '<th width="200" class="{sorter: \'text\'}">' . $ptx['bs_author'] . '</th>'
      . '<th width="300" class="{sorter: \'text\'}">' . $ptx['bs_title'] . '</th>'
      . '<th width="20">' . $bs_book_blank . '</th>'
      . '<th width="20">' . $bs_book_info . '</th>'
      . '<th width="20">' . $bs_book_edit . '</th>'
      . '<th width="20">' . $bs_book_delete . '</th>'
      . '</thead><tbody>';

    $bs_bool = true;
    for($i = 0; $i < $bs_total; $i++)
    {
      $bs_info = explode('^', $bs_data[$i]);
      $bs_approve = $bs_info[0];
      $bs_title   = $bs_info[4];
      $bs_author  = $bs_info[5];
/* book table */      
      if (!empty($bs_title) || !empty($bs_author)) {
        $o .=
          '<tr class="' . (($bs_bool) ? 'bs_odd' : 'bs_even') . '">' // Zebra
        . '<td width="20" align="center">'.($i+1).'</td>'
        . '<td width="200">'.$bs_author.'</td>'
        . '<td width="300">'.$bs_title.'</td>'
        . '<td width="20" align="center">'
        . '<a class="bs_book_approve_link" id="bs_book_approve" href="?&amp;bookstore&amp;admin=plugin_main&amp;action=approve_book&amp;book_idx=' . $i . '" оnClick="src();">';
        if ($bs_approve == "true") {
          $o .= $bs_book_approve_true_img."\n";
        }
        else if ($bs_approve == "false") {
          $o .= $bs_book_approve_false_img."\n";
        }
        else {
          $o .= $bs_book_damaged_record_img."\n";
        }
        $bs_confirm_delete = $ptx['bs_book_delete_confirm'];
        $bs_confirm_status = $ptx['bs_book_approve_confirm'];
        $o .=
            '</a></td>'
          . '<td width="20" align="center"><a id="bs_show_preview_link" href="?&amp;bookstore&amp;admin=plugin_main&amp;action=info_book&amp;book_id=' . $i . '" оnClick="src();">' .$bs_book_info.'</a></td>'
          . '<td width="20" align="center"><a id="bs_show_edit_form_link" href="?&amp;bookstore&amp;admin=plugin_main&amp;action=edit_book&amp;book_id=' . $i . '" оnClick="src();">' .$bs_book_edit.'</a></td>'
          . '<td width="20" align="center"><a class="bs_book_delete_link" href="?&amp;bookstore&amp;admin=plugin_main&amp;action=delete_book&amp;book_id='.$i.'">' .$bs_book_delete. '</a></td>'
          . '</tr>';
          $bs_bool = !$bs_bool; // Zebra
      }
    }
    $o .=
        '</tbody></table></div>'
/* book delete confirm dialog */   
      . '<div class="bs_book_delete_dialog" id="bs_book_delete_question">'.tag('img src="'.$bs_iconpth.'book_help.png" width="48" height="48" alt=""')." ".$bs_confirm_delete.'</div>'
/* book approve confirm dialog */   
      . '<div class="bs_book_approve_dialog" id="bs_book_approve_question">'.tag('img src="'.$bs_iconpth.'book_help.png" width="48" height="48" alt=""')." ".$bs_confirm_status.'</div>'
/* Image Notice */
      . '<div class="bs_section_block">'
      . $bs_book_approve_true_img." ".$ptx['bs_book_approve_true_note'].tag('br')."\n"
      . $bs_book_approve_false_img." ".$ptx['bs_book_approve_false_note'].tag('br')."\n"
      . $bs_book_damaged_record_img." ".$ptx['bs_book_damaged_record_note']."."."\n"
      . $bs_book_info." ".$ptx['bs_book_info_note']."."."\n"
      . $bs_book_edit." ".$ptx['bs_edit_book_note']."\n"
      . $bs_book_delete." ".$ptx['bs_book_delete_note']."\n"
      . '</div>';

/* approve book */
    if ($action == 'approve_book') {
      $id_approve = $_GET['book_idx'];
      for($i = 0; $i < $bs_total; $i++) {
        $approve_data = $bs_data[$id_approve];
        $approve_info = explode('^', $approve_data);
//
        $a_approve          = $approve_info[0];
        $a_user             = $approve_info[1];
        $a_email            = $approve_info[2];
        $a_section          = $approve_info[3];
        $a_title            = $approve_info[4];
        $a_author           = $approve_info[5];
        $a_editor           = $approve_info[6];
        $a_year             = $approve_info[7];
        $a_genre            = $approve_info[8];
        $a_site             = $approve_info[9];
        $a_sitename         = $approve_info[10];
        $a_customfieldname  = $approve_info[11];
        $a_customfieldtext  = $approve_info[12];    
        $a_external         = $approve_info[13];
        $a_online           = $approve_info[14];
        $a_desc             = $approve_info[15];
        $a_imagename        = $approve_info[16];
        $a_date             = $approve_info[17];
        $a_ip               = $approve_info[18];
        $a_ref              = $approve_info[19];
      }
//
      $a_desc = stsl($a_desc);
      $a_desc = strip_tags($a_desc);
//
      switch($a_approve) {
        case "false" : $a_approve = "true"; break;
        case "true" : $a_approve = "false"; break;
        case !"true"||!"false" : $a_approve = "error"; break;
        default : break;
      }
//      $a_approve == "false" ? $a_approve = "true" : $a_approve = "false";
/* replace string */
      $bs_approve_str = strip_tags("$a_approve^$a_user^$a_email^$a_section^$a_title^$a_author^$a_editor^$a_year^$a_genre^$a_site^$a_sitename^$a_customfieldname^$a_customfieldtext^$a_external^$a_online^$a_desc^$a_imagename^$a_date^$a_ip^$a_ref^\n");
      
      $bs_approve_str = stsl($bs_approve_str);
/* replace string in file */
      $fopen = file($bs_datafile);
      foreach ($fopen as $key => $value) {
        if (substr_count($value, $approve_data)) {
          if (isset($fopen[$key])) {
            array_splice($fopen, $key, 1, $bs_approve_str);
          }
        }
        $f = fopen($bs_datafile, "w");
        flock ($f,2);
        for ($i = 0; $i < count($fopen); $i++) {
          fwrite($f, $fopen[$i]);
        }
        fclose($f);
      }
      header('Location:?&bookstore&admin=plugin_main&action=plugin_text');
      exit;
    }
/* end - approve book */

/* book info */
    if ($action == 'info_book') {
      $id_info = $_GET['book_id'];
      for($i = 0; $i < $bs_total; $i++)
      {
        $info_data = $bs_data[$id_info];
        $i_info = explode('^', $info_data);
//
        $info_approve         = $i_info[0];
        $info_user            = $i_info[1];
        $info_email           = $i_info[2];
        $info_section         = $i_info[3];
        $info_title           = $i_info[4];
        $info_author          = $i_info[5];
        $info_editor          = $i_info[6];
        $info_year            = $i_info[7];
        $info_genre           = $i_info[8];
        $info_site            = $i_info[9];
        $info_sitename        = $i_info[10];
        $info_customfieldname = $i_info[11];
        $info_customfieldtext = $i_info[12];    
        $info_external        = $i_info[13];
        $info_online          = $i_info[14];
        $info_desc            = $i_info[15];
        $info_image           = $i_info[16];
        $info_date            = $i_info[17];
        $info_ip              = $i_info[18];
        $info_ref             = $i_info[19];
      }

/* optomise link for SEO  */
    $info_site      = '?bs_url='.$info_site;
    $info_external  = '?bs_url='.$info_external;
    $info_online    = '?bs_url='.$info_online;
    $info_ref       = '?bs_url='.$info_ref;
/* remove redirect for anchor */
    $info_site_anchor      =  str_replace('?bs_url=', '', $info_site);
    $info_external_anchor  =  str_replace('?bs_url=', '', $info_external);
    $info_online_anchor    =  str_replace('?bs_url=', '', $info_online);
    $info_ref_anchor       =  str_replace('?bs_url=', '', $info_ref);

/* strip tags */
      $info_desc = strip_tags($info_desc);
/* cover */
      if (empty($info_image)) {
        $info_image = $ptx['bs_book_nocover'];
      }
/* ecternak */
      if ($info_external != "http://") {
        $info_external = '<a href="'.$info_external.'" target="_blank">'.$info_external_anchor.'</a>';
      }
      else {
        $info_external = "(".$ptx['bs_notavailable'].")";
      }
/* online */
      if ($info_online != "http://") {
        $info_online = '<a href="'.$info_online.'" target="_blank">'.$info_online_anchor.'</a>';
      }
      else {
        $info_online = "(".$ptx['bs_notavailable'].")";
      }
/* referer */
      if (!empty($info_ref)) {
        $info_ref = '<a href="'.$info_ref.'" target="_blank">'.$info_ref_anchor.'</a>';
      }
      else {
        $info_ref = "(".$ptx['bs_notavailable'].")";
      }
/* info book form */
    $o .=
        '<fieldset class="bs_fieldset">'
      . '<legend class="bs_legend">'.$ptx['bs_book_info_note'].'</legend>'
      . '<div id="bs_book_edit_description" class="bs_book_info_img">'
      . '<span class="bookinfo">'.$ptx['bs_book_cover'].'</span>'.tag('br')."\n";
    if (empty($info_image)) {
      $o .=
        tag('img style="bs_book_img_preview" src="" width="0" height="" border="1" alt="'.$ptx['bs_book_nocover'].'"')."\n";
    }
    else {
      $o .=
/* Image Enlarger */
          '<div class="ienlarger">'
        . '<a href="#nogo">'
        . tag('img src="'.$bs_imgpth.$info_image.'" alt="Tumb:'.$info_image.'" width="90" height="151" ')."\n"
        . '<span>'
        . tag('img src="'.$bs_imgpth.$info_image.'" alt="Large:'.$info_image.'" ')."\n"
        . tag('br')."\n"
        . $info_title.tag('hr').$info_image."\n" /* Текст можно написать здесь. */
        . '</span>'
        . '</a>'
        . '</div>'
        . tag('br style="clear:left" ')."\n";
/* end - Image Enlarger */
    }
    $o .= '</div>';
    $o .=
        '<div id="bs_book_info_div">'
      . '<span class="bs_bookinfo">ID</span>'.($id_info+1).tag('br')."\n"
      . '<span class="bs_bookinfo">'.$ptx['bs_book_approve_status'].'</span>'."\n";
      if ($info_approve == "true") {
        $o .=
          '<span class="bs_approve_true">'.$ptx['bs_book_approve_true'].'</span>'.tag('br')."\n";
      }
      else {
        $o .=
          '<span class="bs_approve_false">'.$ptx['bs_book_approve_false'].'</span>'.tag('br')."\n";
      }
    $o .=
        '<span class="bs_bookinfo">'.$ptx['bs_date'].'</span>'.$info_date.tag('br')."\n"
      . '<span class="bs_bookinfo">'.$ptx['bs_user_current'].'</span>'.$info_user.tag('br')."\n"
      . '<span class="bs_bookinfo">'.$ptx['bs_email'].'</span><a href="mailto:'.$info_email.'" target="_blank">'.$info_email.'</a>'.tag('br')."\n"
      . '<span class="bs_bookinfo">'.$ptx['bs_ip'].'</span>'.$info_ip.tag('br')."\n"
      . '<span class="bs_bookinfo">'.$ptx['bs_ref'].'</span>'.$info_ref.tag('br')."\n"
      . '<span class="bs_bookinfo">'.$ptx['bs_section'].'</span>'.$info_section.tag('br')."\n"
      . '<span class="bs_bookinfo">'.$ptx['bs_title'].'</span>'.$info_title.tag('br')."\n"
      . '<span class="bs_bookinfo">'.$ptx['bs_author'].'</span>'.$info_author.tag('br')."\n"
      . '<span class="bs_bookinfo">'.$ptx['bs_editor'].'</span>'.$info_editor.tag('br')."\n"
      . '<span class="bs_bookinfo">'.$ptx['bs_year'].'</span>'.$info_year.tag('br')."\n"
      . '<span class="bs_bookinfo">'.$ptx['bs_genre'].'</span>'.$info_genre.tag('br')."\n"
      . '<span class="bs_bookinfo">'.$ptx['bs_site'].'</span><a href="'.$info_site.'" target="_blank">'.$info_site_anchor.'</a>'.tag('br')."\n"
      . '<span class="bs_bookinfo">'.$ptx['bs_sitename'].'</span>'.$info_sitename.tag('br')."\n";
    if (!empty($info_customfieldname) || !empty($info_customfieldtext)) {    
      $o .=
        '<span class="bs_bookinfo">'.$info_customfieldname.'</span>'.$info_customfieldtext.tag('br')."\n";
    }
    $o .=
        '<span class="bs_bookinfo">'.$ptx['bs_external_file'].'</span>'.$info_external.tag('br')."\n"
      . '<span class="bs_bookinfo">'.$ptx['bs_read_online_link'].'</span>'.$info_online.tag('br')."\n"
      . '<span class="bs_bookinfo">'.$ptx['bs_description'].'</span>'.tag('br')."\n"
      . '<span>'.$info_desc.'</span>'."\n"
      . '</div>'
      . '<div style="clear:both"></div>'
      . '</fieldset>';
    } // if ($action == 'info_book')

/* edit book */
    if ($action == 'edit_book') {
      $id_edit = $_GET['book_id'];
      for($i = 0; $i < $bs_total; $i++)
      {
        $ed_data = $bs_data[$id_edit];
        $ed_info = explode('^', $ed_data);

        $ed_approve         = $ed_info[0];
        $ed_user            = $ed_info[1];
        $ed_email           = $ed_info[2];
        $ed_section         = $ed_info[3];
        $ed_title           = $ed_info[4];
        $ed_author          = $ed_info[5];
        $ed_editor          = $ed_info[6];
        $ed_year            = $ed_info[7];
        $ed_genre           = $ed_info[8];
        $ed_site            = $ed_info[9];
        $ed_sitename        = $ed_info[10];
        $ed_customfieldname = $ed_info[11];
        $ed_customfieldtext = $ed_info[12];    
        $ed_external        = $ed_info[13];
        $ed_online          = $ed_info[14];
        $ed_desc            = $ed_info[15];
        $ed_image           = $ed_info[16];
        $ed_date            = $ed_info[17];
        $ed_ip              = $ed_info[18];
        $ed_ref             = $ed_info[19];
      }
/* strip html tags */
      $ed_desc = strip_tags($ed_desc);
/* replace empty value with default image */
      if (empty($ed_image)) {
        $ed_image = "bookstore_nocover.png";
      }
/* edit book form */
      $o .=
          '<fieldset class="bs_fieldset">'
        . '<legend class="bs_legend">'.$ptx['bs_edit_book_form'].'</legend>'
        . '<form action="#bs_edit_form_div" name="book_edit_form" method="post" enctype="multipart/form-data">'
/* book ID */
        . '<label class="bs_label" for="bs_book_id"><span class="bs_require">ID</span></label>'."\n"
        . '<span id="bs_book_id">'.($id_edit+1).'</span>'. tag('br')."\n"
/* book Approved */
        . '<label class="bs_label" for="bs_approved_select"><span class="bs_require">*</span>'.$ptx['bs_book_approve_true'].'</label>'."\n"
        . '<select id="bs_approved_select" name="book_approved_select">'."\n"
        . '<option style="color:#ff0000;" selected>'.$ed_approve.'</option>'."\n"
        . '<option>true</option>'."\n"
        . '<option>false</option>'."\n"
        . '</select>'. tag('br')."\n"
/* book Section */
        . '<label class="bs_label" for="bs_book_edit_section_select"><span class="bs_require">*</span>'.$ptx['bs_section'].'</label>'."\n"
        . '<select id="bs_book_edit_section_select" name="book_edit_section_select" onchange="textSelected();">'
        . '<option selected>'.$ed_section.'</option>';
        foreach ($bs_section_array as $bs_val) {
          $o .= '<option>'.$bs_val.'</option>';
        }
      $o .=
          '</select>'. tag('br')."\n"
/* book Title */
        . '<label class="bs_label" for="bs_book_edit_title"><span class="bs_require">*</span>'.$ptx['bs_title'].'</label>'."\n"
        . tag('input id="bs_book_edit_title" name="book_edit_title" type="text" size="55" value="'.$ed_title.'" required'). tag('br')."\n"
/* book Author */
        . '<label class="bs_label" for="bs_book_edit_author"><span class="bs_require">*</span>'.$ptx['bs_author'].'</label>'."\n"
        . tag('input id="bs_book_edit_author" name="book_edit_author" type="text" size="55" value="'.$ed_author.'" required'). tag('br')."\n"
/* book Editor */
        . '<label class="bs_label" for="bs_book_edit_editor"><span class="bs_optional">*</span>'.$ptx['bs_editor'].'</label>'."\n"
        . tag('input id="bs_book_edit_editor" name="book_edit_editor" type="text" size="55" value="'.$ed_editor.'"'). tag('br')."\n"
/* book Year */
        . '<label class="bs_label" for="book_edit_year_input"><span class="bs_require">*</span>'.$ptx['bs_year'].'</label>'."\n"
        . tag('input id="book_edit_year_input" name="book_edit_year" type="text" size="55" value=" required'.$ed_year.'"'). tag('br')."\n"
/* book Genre */
        . '<label class="bs_label" for="bs_book_edit_genre"><span class="bs_require">*</span>'.$ptx['bs_genre'].'</label>'."\n"
        . tag('input id="bs_book_edit_genre" name="book_edit_genre" type="text" size="55" value="'.$ed_genre.'" required'). tag('br')."\n"
/* book Site */
        . '<label class="bs_label" for="bs_book_edit_site"><span class="bs_optional">*</span>'.$ptx['bs_site'].'</label>'."\n"
        . tag('input id="bs_book_edit_site" name="book_edit_site" type="url" size="55" value="'.$ed_site.'"'). tag('br')."\n"
/* book Site name*/
        . '<label class="bs_label" for="bs_book_edit_sitename"><span class="bs_optional">*</span>'.$ptx['bs_sitename'].'</label>'."\n"
        . tag('input id="bs_book_edit_sitename" name="book_edit_sitename" type="text" size="55" value="'.$ed_sitename.'"'). tag('br')."\n"
/* book Custom field name*/
        . '<label class="bs_label" for="bs_book_edit_customfieldname"><span class="bs_optional">*</span>'.$ptx['bs_custom_field_name'].'</label>'."\n"
        . tag('input id="bs_book_edit_customfieldname" name="book_edit_customfieldname" type="text" size="55" value="'.$ed_customfieldname.'"'). tag('br')."\n"
/* book Custom field text*/
        . '<label class="bs_label" for="bs_book_edit_customfieldtext"><span class="bs_optional">*</span>'.$ptx['bs_custom_field_text'].'</label>'."\n"
        . tag('input id="bs_book_edit_customfieldtext" name="book_edit_customfieldtext" type="text" size="55" value="'.$ed_customfieldtext.'"'). tag('br')."\n"
/* book External file */
        . '<label class="bs_label" for="bs_book_edit_external"><span class="bs_optional">*</span>'.$ptx['bs_external_file'].'</label>'."\n"
        . tag('input id="bs_book_edit_external" name="book_edit_external" type="url" size="55" value="'.$ed_external.'"'). tag('br')."\n"
/* book Read online */
        . '<label class="bs_label" for="bs_book_edit_read_online_link"><span class="bs_optional">*</span>'.$ptx['bs_read_online'].'</label>'."\n"
        . tag('input id="bs_book_edit_read_online_link" name="book_edit_read_online_link" type="url" size="55" value="'.$ed_online.'"'). tag('br')."\n"
/* book Description */
        . '<label class="bs_label" for="bs_edit_description_area"><span class="bs_require">*</span>'.$ptx['bs_description'].'</label>'.tag('br')."\n"
        . '<textarea class="bs_area" id="bs_edit_description_area" name="book_edit_description" rows="'.$pcf['area_height'].'" cols="'.$pcf['area_width'].'" required>'.$ed_desc.'</textarea>'.tag('br')."\n"
/* book Img preview */
        . '<div id="bs_book_edit_description" class="bs_bookfield_edit_img">'
        . '<span>'.$ptx['bs_book_cover'].'</span>'.tag('br')."\n"
/* Image Enlarger */
        . '<div class="ienlarger">'
        . '<a href="#nogo">'
        . tag('img src="'.$bs_imgpth.$ed_image.'" alt="Tumb:'.$ed_image.'" width="50" height="84" ')."\n"
        . '<span>'
        . tag('img src="'.$bs_imgpth.$ed_image.'" alt="Large:'.$ed_image.'" ')."\n"
        . tag('br')."\n"
        . $ed_title.tag('hr').$ed_image /* Текст можно написать здесь. */
        . '</span>'
        . '</a>'
        . '</div>'
        . tag('br style="clear:left" ')."\n"
/* end - Image Enlarger */
        . '</div>'
/* book Image Upload */
        . tag('br')."\n"
        . '<em>'.$ptx['bs_current_img'].'</em>'.tag('br')."\n"
        . $ed_image.tag('br')."\n"
        . '<span style="font-style: oblique;">'.$ptx['bs_current_img_save'].'</span>'.tag('br')."\n"
        . '<label class="bs_label" for="bs_new_image_upload"><span class="bs_require">*</span>'.$ptx['bs_addimg'].'</label>'."\n"
        . tag('input id="bs_new_image_upload" type="file" name="book_add_new_image"').tag('br')."\n"
/* book Save */
        . tag('input id="save_new_book" class="bs_edinput" name="form_edit_submit" type="submit" value="'.$ptx['bs_edit_book_save_btn'].'"')."\n"
/* book Form */
        . '</form>'
        . '</fieldset>';
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $ed_approve             = isset($_POST["book_approved_select"]) ? $_POST["book_approved_select"] : null; /* required */
      $bs_editsection         = isset($_POST["book_edit_section_select"]) ? $_POST["book_edit_section_select"] : null; /* required */
      $bs_edittitle           = isset($_POST["book_edit_title"]) ? $_POST["book_edit_title"] : null; /* required */
      $bs_editauthor          = isset($_POST["book_edit_author"]) ? $_POST["book_edit_author"] : null; /* required */
      $bs_editeditor          = isset($_POST["book_edit_editor"]) ? $_POST["book_edit_editor"] : null;
      $bs_edityear            = isset($_POST["book_edit_year"]) ? $_POST["book_edit_year"] : null; /* required */
      $bs_editgenre           = isset($_POST["book_edit_genre"]) ? $_POST["book_edit_genre"] : null; /* required */
      $bs_editsite            = isset($_POST["book_edit_site"]) ? $_POST["book_edit_site"] : null;
      $bs_editsitename        = isset($_POST["book_edit_sitename"]) ? $_POST["book_edit_sitename"] : null;
      $bs_editcustomfieldname = isset($_POST["book_edit_customfieldname"]) ? $_POST["book_edit_customfieldname"] : null;
      $bs_editcustomfieldtext = isset($_POST["book_edit_customfieldtext"]) ? $_POST["book_edit_customfieldtext"] : null;
      $bs_editexternal        = isset($_POST["book_edit_external"]) ? $_POST["book_edit_external"] : null;
      $bs_editreadonline      = isset($_POST["book_edit_read_online_link"]) ? $_POST["book_edit_read_online_link"] : null;
      $bs_editdescription     = isset($_POST["book_edit_description"]) ? $_POST["book_edit_description"] : null; /* required */
      $bs_editimage           = isset($_POST["book_add_new_image"]) ? $_POST["book_add_new_image"] : null; /* required */
      $bs_editimage_name      = isset($_FILES["book_add_new_image"]["name"]) ? $_FILES["book_add_new_image"]["name"] : null;

/* slashes handing */
      $bs_editdescription     = stsl($bs_editdescription);
      $bs_editdescription     = strip_tags($bs_editdescription);
/* leave the old record if image file infut empty */
      if (empty($bs_editimage_name)) {
        $bs_editimage_name = $ed_image;
      }
      // $ed_data - string for replace
      // string to replace
      $bs_edited = strip_tags("$ed_approve^$ed_user^$ed_email^$bs_editsection^$bs_edittitle^$bs_editauthor^$bs_editeditor^$bs_edityear^$bs_editgenre^$bs_editsite^$bs_editsitename^$bs_editcustomfieldname^$bs_editcustomfieldtext^$bs_editexternal^$bs_editreadonline^$bs_editdescription^$bs_editimage_name^$ed_date^$ed_ip^$ed_ref^\n");
      $bs_edited = stsl($bs_edited);
/* replace string in file */
      $fopen = @file($bs_datafile);
      foreach ($fopen as $key => $value) {
        if (substr_count($value, $ed_data)) {
          if (isset($fopen[$key])) {
            array_splice($fopen, $key, 1, $bs_edited);
          }
        }
        $f = fopen($bs_datafile, "w");
        flock ($f,2);
        for ($i = 0; $i < count($fopen); $i++) {
          fwrite($f, $fopen[$i]);
        }
        fclose($f);
      }
    } // if ($_SERVER['REQUEST_METHOD']
/* save edited record */
    if (isset($_POST['form_edit_submit'])) {
/* Image Upload */
      $bs_file     =  $_FILES["book_add_new_image"];
      $bs_handle   = new Upload($bs_file, $pcf['file_class_lang']);
      $bs_savepath = $bs_imgpth;
/* Settings */
    /* only image upload */
      $bs_handle->allowed = array("image/*");
    /* file_safe_name formats the filename (spaces changed to _) (default: true) */
      $bs_handle->file_safe_name = false;
    /* file_max_size sets maximum upload size (default: upload_max_filesize from php.ini) */
      $handle->file_max_size = '204800'; // 200 KB
    /* if filename exists */
      $bs_handle->file_auto_rename = $pcf['file_auto_rename']; // false";
    /* rewrite file, if exists */
      $bs_handle->file_overwrite = $pcf['file_overwrite']; // true;
    /* add Watermark left bottom */
      // $bs_handle->image_watermark = $pth['folder']['plugins'].$pcf['image_watermark'];
    /* watermark on greyscale pic, absolute position */
      // $bs_handle->image_watermark_x = $pcf['image_watermark_x']; // 10;
      // $bs_handle->image_watermark_y = $pcf['image_watermark_y']; // 10;
    /* image greyscale */
      // $bs_handle->image_greyscale = $pcf['image_greyscale']; //"false";
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
      $bs_handle->Process($bs_savepath);
      if ($bs_handle->processed) {
        $o .= $ptx['bs_adding_succes'];
      }
      else {
        $o .= "&#8277;&nbsp;".$ptx['bs_just_error'] . $bs_handle->error . tag('br')."\n";
      }
      $bs_handle->Clean();

/* end - Image Upload */

      $o .= '<div id="bs_book_alert" class="bs_bookalert">';
      if (empty($bs_edittitle))            {$o .= "&#8277;&nbsp;".$ptx['bs_title_alert'].tag('br')."\n";}
      else if (empty($bs_editauthor))      {$o .= "&#8277;&nbsp;".$ptx['bs_author_alert'].tag('br')."\n";}
      else if (empty($bs_edityear))        {$o .= "&#8277;&nbsp;".$ptx['bs_year_alert'].tag('br')."\n";}
      else if (empty($bs_editgenre))       {$o .= "&#8277;&nbsp;".$ptx['bs_genre_alert'].tag('br')."\n";}
      else if (empty($bs_editdescription)) {$o .= "&#8277;&nbsp;".$ptx['bs_decript_alert'].tag('br')."\n";}
      else {
        header('Location: ?&bookstore&admin=plugin_main&action=plugin_text');
        exit;
      }
      $o .= '</div>';
    }

/* end - edit book form */

    $o .=
      '<h3>'.$ptx['bs_section_list'].'</h3>'."\n";

/* SECTION */

    $bs_sectioncount = count($bs_section_array);

    if ($action == 'delete_section') {
      if ($bs_sectioncount == 1) {
        $o .=
            '<div class="bs_bookalert">' // must be at least one section
          . $ptx['bs_removal_impossible']
          . '</div>';
        return;
      }
      else {
        $id_section = $_GET['section_id'];
/* remove section from already read $bs_data */
        array_splice($bs_section_array, $id_section, 1);
/* write $bs_section_array back to file */
        if (($bs_temp = fopen($bs_sectionfile, 'wb')) === false
             || fwrite($bs_temp, implode('', $bs_section_array)) === false) {
          e('cntsave', 'file', $bs_sectionfile);
        }
        if ($bs_temp !== false) {
          fclose($bs_temp);
        }
        header('Location:?&bookstore&admin=plugin_main&action=plugin_text');
      }
    }
/* end - user wants to delete a section */

/* add section */
    $text_length_b = sprintf($ptx['bs_max_text_length'],$pcf['book_maxlength_b']);

    $o .=
        '<div id="add_new_section">'
      . '<div class="bs_section_block">'
      . '<span class="bs_require">*</span><span class="bs_bookinfo">'.$ptx['bs_section_name'].'</span>'."\n"
      . '</div>'
      . '<form action="#add_new_section" name="section_form" method="post" enctype="multipart/form-data">'
      . tag('input id="bs_new_section" name="file_add_section" type="text" maxlength="'.$pcf['book_maxlength_b'].'" value="" placeholder="'.$ptx['bs_section_placeholder'].'"')." ".'<span class="bs_optional">'.$text_length_b.'</span>'."\n"
      . tag('br')
      . tag('input name="input_add_new_section_submit" id="bs_save_new_section" type="submit" value="'.$ptx['bs_addbook_btn'].'"')."\n"
      . '</form>'
      . '</div>';

/* submit */
    if (isset($_POST['input_add_new_section_submit'])) {
//      $bs_section_array = file($bs_sectionfile) or die("wrong file name"); // wrong file name
      if ($bs_sectioncount >= $pcf['section_number_allowed']) {
        $o .= '<div class="bs_bookalert">'.$ptx['bs_max_section_number'].  $pcf['section_number_allowed'].'</div>'; // only NN section allowed
        return;
      }
      else {
        $bs_newsection = $_POST["file_add_section"];
        if (!empty($bs_newsection)) {
          $f=fopen($bs_sectionfile,'a+');
          flock ($f,2);
          fputs($f, "$bs_newsection\n");
          fclose($f);
          header('Location: ?&bookstore&admin=plugin_main&action=plugin_text');
        }
        else {
          $o .= '<div class="bs_bookalert">'.$ptx['bs_section_name'].'</div>';
        }
      }
    }

/* section table */

    $o .=
        '<div id="bs_section_list">'
      . '<table id="section_table">'
      . '<thead class="bs_thead">'
      . '<tr>'
      . '<th>ID</th>'
      . '<th>'.$ptx['bs_section_list'].'</th>'
      . '<th>' .$bs_book_delete. '</th>'
      . '</thead>'
      . '<tbody>';

    $bs_bool = true;
    for($i = 0; $i < $bs_sectioncount; $i++) {
      $o .=
          '<tr class="' . (($bs_bool) ? 'bs_odd' : 'bs_even') . '">'
        . '<td style="text-align:center;" width="20">'
        . ($i+1)
        . '</td>'
        . '<td width="300">'
        . $bs_section_array[$i]
        . '</td>'
        . '<td width="20" align="center" title="'.$ptx['bs_book_delete'].'"><a class="bs_section_delete_link" href="?&amp;bookstore&amp;admin=plugin_main&amp;action=delete_section&amp;section_id=' . $i . '">' .$bs_book_delete.'</a></td>'
        . '</tr>';
        $bs_bool = !$bs_bool;
    }
    $o .=
        '</tbody>'
      . '</table>'
      . '</div>'
      
/* section delete confirm dialog */   
      . '<div class="bs_section_delete_dialog" id="bs_section_delete_question">'.tag('img src="'.$bs_iconpth.'book_help.png" width="48" height="48" alt=""')." ".$ptx['bs_section_delete_confirm'].'</div>'
      . '<div class="bs_section_block">'
      . $bs_book_delete." ".$ptx['bs_section_delete']."\n"
      . '</div>';

    if ($bs_sectioncount == 1) {
      $o .=
          '<div class="bs_bookalert">' // must be at least one section
        . $ptx['bs_removal_impossible']
        . '</div>';
      return;
    }

/* end - section table */

    break;
    default:
      $o .= plugin_admin_common($action, $admin, $plugin);
  }

  $o .=
    '<script type="text/javascript">
      addTableRolloverEffect("book_table","tableRollOverEffect1","tableRowClickEffect1");
      addTableRolloverEffect("section_table","tableRollOverEffect1","tableRowClickEffect1");
    </script>';
}

?>