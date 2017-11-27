/* jQuery PLUGIN SETTINGS */

/*<![CDATA[*/

$(document).ready(function()
{


//  $("#bs_remember").prop("checked", true);

/* Expandable textarea SCRIPT */
  jQuery(function($) {
    $("#bs_description_area,#bs_edit_description_area").expandable();
  });

/*  MaskEdInput SCRIPT */
  $("#book_add_year_input,#book_edit_year_input").mask ("9999", {placeholder: "-"});
  jQuery('input[placeholder]').placeholder();

/* Easy-confirm-dialog SCRIPT */
  $(".bs_book_delete_link").easyconfirm({dialog: $("#bs_book_delete_question")});
  $(".bs_book_approve_link").easyconfirm({dialog: $("#bs_book_approve_question")});
  $(".bs_section_delete_link").easyconfirm({dialog: $("#bs_section_delete_question")});

/* symbols limit */
  $('#bs_book_add_genre,#bs_book_edit_genre,#bs_book_custom_field_name,#bs_book_custom_field_text,#bs_book_edit_customfieldname,#bs_book_edit_customfieldtext').charCount({ allowed: 20, warning: 10 });
  $('#bs_book_add_user_limit,#bs_book_add_sitename,#bs_new_section,#bs_book_edit_sitename').charCount({ allowed: 30, warning: 10 });  
  $('#bs_book_add_email,#bs_book_add_author,#bs_book_add_editor,#bs_book_edit_author,#bs_book_edit_editor').charCount({ allowed: 50, warning: 20 });
  $('#bs_book_add_title,#bs_book_edit_title').charCount({ allowed: 100, warning: 30 });
  $('#bs_book_add_site,#bs_book_add_external,#bs_book_read_online_link,#bs_book_edit_site,#bs_book_edit_external,#bs_book_edit_read_online_link').charCount({ allowed: 150, warning: 30 });
  $('#bs_description_area,#bs_edit_description_area').charCount({ allowed: 500, warning: 50 });

/* my-tooltips */
  // change to ".class[title]" for element with .class and title attribute only
	$("[title]").style_my_tooltips({
	  tip_follows_cursor: "on", // on/off bind to the mouse cursor
	  tip_delay_time: 500 // ms
  });

/* tablePagination */
  var options = {
    currPage : 1,
//    ignoreRows : $('tbody tr:odd', $('#book_table')),
    optionsForRows : [2,5,10,20,50,75,100],
    rowsPerPage : 10,
    firstArrow : (new Image()).src="plugins/bookstore/js/jtp/images/goFirst.png",
    prevArrow : (new Image()).src="plugins/bookstore/js/jtp/images/goPrev.png",
    lastArrow : (new Image()).src="plugins/bookstore/js/jtp/images/goLast.png",
    nextArrow : (new Image()).src="plugins/bookstore/js/jtp/images/goNext.png",
    topNav : false
  }
  $("#book_table").tablePagination(options);

/* show/hide form only */
/*
  $.fn.slideFadeToggle = function(speed, easing, callback){
    return this.animate({opacity: 'toggle', height: 'toggle'}, speed, easing, callback);
  };
  $("#show_book_form").hide();
  $("#add_new_book_btn").click(function() {
    $("#show_book_form").slideFadeToggle("slow");
  });
*/

/* show form as dialog window */
  $("#add_new_book_btn").button().click(function() {
    var create_dialog = $("#show_book_form");
    var create_button = $(this);
    // if isOpen - close
    if( create_dialog.dialog("isOpen") ) {
      create_dialog.dialog("close");
    } else {
      create_dialog.dialog("open");
    }
  });
  $("#show_book_form").dialog({
    autoOpen : false,
    minWidth : 610,
    position : ["center","top"],
    title: "Book Store",
    resizable: true,
    show: 'slide',
    modal: true
  });
});
/*]]>*/