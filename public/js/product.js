$(function () {
  $('#product_code, #product_sku').mask('0000 0000');

  callFileInput('#photo', 1, 5120, ['jpg', 'jpeg', 'png']);

  $('#form-product').validate();

  // Generate product code
  $('#generate-code').click(function () {
    let code = generateCardNo(8);
    code = code.match(/.{1,4}/g);
    code = code.join(' ');

    $('#product_code').val(code);
    $('#product_sku').val(code);
    $("tbody.variant-row tr").each(function(i, row) {
      let newCode = `${code}-${i+1}`;
      $(row).find('input.variant-sku').val(newCode);
    })
  });

  // add/edit product variantion
  $(document).on('click', '.add-variant-row', function() {
    var url = $(this).data('url');
    var variation_row_index = $(this).closest('.variant-row').find('.row_index').val();
    var variation_value_row_index = $(this).closest('.table-variantion').find('tr:last .variant-row-index').val();
    let productCode = $("#product_code").val();

    if ($(this).closest('.variant-row').find('.row_edit').length >= 1) {
      var row_type = 'edit';
    } 
    else {
      var row_type = 'add';
    }

    if($("#product_code").val() == '') {
      messagePopup("Warning", "Product code cannot be empty!", 'warning');
      return false;
    }

    var table = $(this).closest('.table-variantion');
    $.ajax({
      type: 'GET',
      url: url,
      data: {
        variation_row_index: variation_row_index,
        value_index: variation_value_row_index,
        row_type: row_type,
        product_code: productCode,
      },
      dataType: 'json',
      success: function(result) {
        if (result) {
          table.append(result.data);
        }
      },
    });
  });

  // remove product variantion from list
  $(document).on('click', '.remove-variant-row', function(i) {
    var $this = $(this);
    // console.log('row index : ' + $this.parents('tr').index());
    swal({
      title: sweetAlertTitle,
      text: sweetAlertText,
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes',
      cancelButtonText: 'No',
    }, function(willDelete) {
      if (willDelete) {
        var count = $this.closest('.table-variantion').find('.remove-variant-row').length;
        if(count === 1) {
          $this.closest('.variant-row').remove();
        } 
        else {
          $this.closest('tr').remove();
        }
      }
    });
  });

  $(document).on('click', '.reset-variant-row', function(e) {
    e.preventDefault();

    swal({
      title: LANG.sure,
      icon: 'warning',
      buttons: true,
      dangerMode: true,
    }).then(willDelete => {
      if (willDelete) {
        variantions = {};
        localStorage.setItem('variantions', variantions);
      }
    });
  });

  $(document).on('click', '.submit_product_form', function(e) {
    e.preventDefault();
    var submit_type = $(this).attr('value');
    $('#submit_type').val(submit_type);

    localStorage.clear();

    $('form#form-product').submit();
  });
});
