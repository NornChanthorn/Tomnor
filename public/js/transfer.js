$(function () {
  // $('#original_warehouse').change(function () {
  //   $('#product').html(emptyOptionElm);
  //   $('#product-table tbody').html('');
  //   let originalWarehouseId = $(this).val();

  //   if (originalWarehouseId != '') {
  //     $.ajax({
  //       url: productRetrievalUrl.replace(':warehouseId', originalWarehouseId),
  //       success: function (data) {
  //         let productData = emptyOptionElm;

  //         $.each(data.products, function (key, product) {
  //           productData += '<option value="' + product.id + '" data-name="' + product.name + '"' +
  //           ' data-code="' + product.code + '" data-stock-qty="' + product.stock_qty + '">' +
  //           product.name + ' (' + codeLabel + ' : ' + (product.code || noneLabel) + ')</option>';
  //         });

  //         $('#product').html(productData);
  //       }
  //     });
  //   }
  // });

    // When add product to transfer list
    $('#add-product').click(function () {
      if ($('#product').attr('required', true).valid()) {
        let productId = $('#product').val();
        let isProductAdded = ($('#product-table tbody').find('tr[data-id="' + productId + '"]').length > 0);

        if (!isProductAdded) {
          let productElm = $('#product').find(':selected');
          let productRow =
          '<tr data-id="' + productId + '">' +
          '<input type="hidden" name="products[' + productId + '][id]" value="' + productId + '">' +
          '<input type="hidden" name="products[' + productId + '][name]" value="' + productElm.data('name') + '">' +
          '<input type="hidden" name="products[' + productId + '][code]" value="' + productElm.data('code') + '">' +
          '<input type="hidden" name="products[' + productId + '][stock_qty]" value="' + productElm.data('stock-qty') + '">' +
          '<td>' + productElm.data('name') + '</td>' +
          '<td>' + (productElm.data('code') || noneLabel) + '</td>' +
          '<td>' + productElm.data('stock-qty') + '</td>' +
          '<td width="25%">' +
          '<input type="text" name="products[' + productId + '][quantity]" class="form-control integer-input" min="1" max="10000" required>' +
          '</td>' +
          '<td><button type="button" class="btn btn-danger btn-sm" onclick="removeProduct(this)"><i class="fa fa-trash-o"></i></button></td>' +
          '</tr>';

          $('#product-table tbody').append(productRow);
          formatNumericFields();
        }

        $('#product').attr('required', false).val('').trigger('change');
      }
    });

});

/**
 * Remove product from transfer list.
 *
 * @param buttonElm Button element that has been clicked
*/
function removeProduct(buttonElm) {
  $(buttonElm).parents('#product-table tbody tr').remove();
}

function addProduct(productElm) {
  let indexId = productElm.product_id+productElm.variantion_id;
  let isProductAdded = ($('#product-table tbody').find('tr[data-id="' + indexId + '"]').length > 0);
  console.log(indexId);

  if (!isProductAdded) {
    let productRow =
      '<tr data-id="' + indexId + '">' +
        '<input type="hidden" name="products[' + indexId + '][id]" value="' + productElm.product_id + '">' +
        '<input type="hidden" name="products[' + indexId + '][name]" value="' + productElm.label + '">' +
        '<input type="hidden" name="products[' + indexId + '][code]" value="' + productElm.code + '">' +
        '<input type="hidden" name="products[' + indexId + '][price]" value="' + productElm.price + '">' +
        '<input type="hidden" name="products[' + indexId + '][stock_qty]" value="' + productElm.qty_available + '">' +
        '<input type="hidden" name="products[' + indexId + '][enable_stock]" value="' + productElm.enable_stock + '">' +
        '<input type="hidden" name="products[' + indexId + '][variantion_id]" value="' + productElm.variantion_id + '">' +
        '<td>' + productElm.label + '</td>' +
        '<td>' + (productElm.code || noneLabel) + '</td>' +
        '<td width="15%">' + (parseInt(productElm.qty_available) || noneLabel) + '</td>' +
        '<td width="15%">' +
          '<input type="text" name="products[' + indexId + '][quantity]" value="1" class="form-control form-control-sm integer-input quantity" min="1" max="'+ productElm.qty_available +'" required>' +
        '</td>' +
        '<td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="removeProduct(this)"><i class="fa fa-trash-o"></i></button></td>' +
      '</tr>';

    $('#product-table tbody').append(productRow);
    formatNumericFields();
  }
}