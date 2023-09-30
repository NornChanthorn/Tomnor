(function () {
	"use strict";

	var treeviewMenu = $('.app-menu');

	// Toggle Sidebar
	$('[data-toggle="sidebar"]').click(function(event) {
		event.preventDefault();
		$('.app').toggleClass('sidenav-toggled');
	});

	// Activate sidebar treeview toggle
	$("[data-toggle='treeview']").click(function(event) {
		event.preventDefault();
		if(!$(this).parent().hasClass('is-expanded')) {
			treeviewMenu.find("[data-toggle='treeview']").parent().removeClass('is-expanded');
		}
		$(this).parent().toggleClass('is-expanded');
	});

	// Set initial active toggle
	$("[data-toggle='treeview.'].is-expanded").parent().toggleClass('is-expanded');

	//Activate bootstrip tooltips
	$("[data-toggle='tooltip']").tooltip();

	// Disabled form submission when click Enter key
  $('.no-auto-submit').keypress(function (e) {
      return e.which !== 13;
  });

})();

$(document).on('click', '.btn-modal', function(e) {
  e.preventDefault();
  var container = $(this).data('container');

  $.ajax({
    url: $(this).data('href'),
    dataType: 'html',
    type: "GET",
    success: function(result) {
      $(container).html(result).modal('show');
    },
  });
});

/**
 * Return default options for SweetAlert.
 *
 * @param boxType SweetAlert box type: success, info, warning, ...
 *
 * @return object
 */
function defaultSwalOptions(boxType) {
  boxType = boxType || 'success';
  return {
    title: sweetAlertTitle,
    text: sweetAlertText,
    type: boxType,
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
  };
}

/**
 * Display a confirmation box (SweetAlert) to perform action on seerver via AJAX.
 *
 * @param {string} url URL to perform the action
 * @param {string} boxType SweetAlert box type: success, info, warning, ...
 * @param {string} httpMethod
 * @param {string|null} redirectUrl URL to redirect after success
 *
 * @return void
 */
function confirmPopup(url, boxType, httpMethod, redirectUrl) {
  boxType = boxType || 'warning';
  httpMethod = httpMethod || 'POST';

  swal(defaultSwalOptions(boxType), function(isConfirmed) {
    if (isConfirmed) {
      $.ajax({
        url: url,
        type: httpMethod,
        success: function (result) {
          // console.log(result);
          if (redirectUrl === undefined) {
            window.location.reload();
          } 
          else {
            window.location.href = redirectUrl;
          }
        }
      });
    }
  });
}

/**
 * Return decimal number.
 *
 * @param value
 * @param decimalLength
 *
 * @return number
 */
function decimalNumber(value, decimalLength) {
    decimalLength = decimalLength || 2;
    return Number(parseFloat(value).toFixed(decimalLength));
}

/**
 * Display a confirmation box to submit form when click on a specific submit button.
 * Note: jQuery validation plugin is required.
 *
 * @param formElm jQuery selector of form element to submit
 */
function confirmFormSubmission(formElm) {
  event.preventDefault();
  if (formElm.valid()) {
    swal(defaultSwalOptions(), function (isConfirmed) {
      if (isConfirmed) {
        formElm.submit();
      }
    });
  }
}


function generateCardNo(x) {
    if(!x) { x = 16; }
    chars = "1234567890";
    no = "";
    for (var i=0; i<x; i++) {
       var rnum = Math.floor(Math.random() * chars.length);
       no += chars.substring(rnum,rnum+1);
   }
   return no;
}

$(".btn-delete").on('click', function() {
  confirmPopup($(this).data('url'), 'error', 'DELETE');
});