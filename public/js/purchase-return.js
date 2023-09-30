$(function () {
  // When add product to purchase list
  $('#add-product').click(function () {
      if ($('#product').attr('required', true).valid()) {
          let productId = $('#product').val();
          let isProductAdded = ($('#purchase_return_table tbody').find('tr[data-id="' + productId + '"]').length > 0);

          if (!isProductAdded) {
              let productElm = $('#product').find(':selected');
              let productRow =
                  '<tr data-id="' + productId + '">' +
                      '<input type="hidden" name="products[' + productId + '][id]" value="' + productId + '">' +
                      '<input type="hidden" name="products[' + productId + '][name]" value="' + productElm.data('name') + '">' +
                      '<input type="hidden" name="products[' + productId + '][code]" value="' + productElm.data('code') + '">' +
                      '<td>' + productElm.data('name') + '</td>' +
                      '<td>' + (productElm.data('code') || noneLabel) + '</td>' +
                      '<td width="25%">' +
                          '<input type="text" name="products[' + productId + '][quantity]" class="form-control integer-input" min="1" max="10000" required>' +
                      '</td>' +
                      '<td><button type="button" class="btn btn-danger btn-sm" onclick="removeProduct(this)"><i class="fa fa-trash-o"></i></button></td>' +
                  '</tr>';

              $('#purchase_return_table tbody').append(productRow);
              formatNumericFields();
          }

          $('#product').attr('required', false).val('').trigger('change');
      }
  });

  //Update line total and check for quantity not greater than max quantity
  $('table#purchase_return_table tbody').on('change', 'input.return_quantity', function() {
    // var max_qty = parseFloat($(this).data('rule-max'));
    var entered_qty = $(this).val();
    var tr = $(this).parents('tr');

    var unit_price_inc_tax = tr.find('input.purchase_price').val();
    var line_total = entered_qty * unit_price_inc_tax;
    line_total = Number(line_total).toFixed(2);

    tr.find('span.sub-total').html(line_total)

    calculateTotal();
  });

  //Update line total and check for quantity not greater than max quantity
  $('table#purchase_return_table tbody').on('change', 'input.purchase-price', function() {
    // var max_qty = parseFloat($(this).data('rule-max'));
    var unit_price_inc_tax = $(this).val();

    var tr = $(this).parents('tr');

    var entered_qty = tr.find('input.return_quantity').val();
    var line_total = entered_qty * unit_price_inc_tax;
    line_total = Number(line_total).toFixed(2);

    tr.find('span.sub-total').html(line_total);
    calculateTotal();
  });

  //Update discount
  $('table#purchase_return_table tfoot').on('change', 'input.discount', function() {
    calculateDueBalance();
  });

  //Update shipping charges
  $('table#purchase_return_table tfoot').on('change', 'input.shipping-cost', function() {
    calculateDueBalance();
  });
});

// $(document).on('change', ".quantity, .purchase-price", function() {
//   calculateTotal();
// });

/**
 * Remove product from purchase list.
 *
 * @param buttonElm Button element that has been clicked
 */
function removeProduct(buttonElm) {
  $(buttonElm).parents('#purchase_return_table tbody tr').remove();

  calculateTotal();
}

function addProduct(productElm) {
  let indexId = productElm.id+productElm.variantion_id;
  let isProductAdded = ($('#purchase_return_table tbody').find('tr[data-id="' + indexId + '"]').length > 0);

  if (!isProductAdded) {
    let productRow =
      '<tr data-id="' + indexId + '">' +
        '<input type="hidden" name="products[' + indexId + '][id]" value="' + productElm.id + '">' +
        '<input type="hidden" name="products[' + indexId + '][name]" value="' + productElm.label + '">' +
        '<input type="hidden" name="products[' + indexId + '][code]" value="' + productElm.code + '">' +
        '<input type="hidden" name="products[' + indexId + '][variantion_id]" value="' + productElm.variantion_id + '">' +
        '<td>' + productElm.label + '</td>' +
        '<td width="15%" class="text-right">' +
          '<input type="text" name="products[' + indexId + '][purchase_price]" value="'+ productElm.cost+'" class="form-control form-control-sm decimal-input purchase-price">' +
        '</td>' +
        '<td width="15%">' +
          '<input type="hidden" name="products[' + indexId + '][quantity]" value="1" class="form-control form-control-sm integer-input quantity" min="1" max="10000" required>' +
        '</td>' +
        '<td width="15%">' +
          '<input type="hidden" name="products[' + indexId + '][quantity]" value="1" class="form-control form-control-sm integer-input quantity" min="1" max="10000" required>' +
        '</td>' +
       
        '<td width="15%" class="text-right">' + 
          '<input type="text" name="sub_total" value="'+ productElm.cost +'" class="form-control form-control-sm decimal-input sub-total" readonly placeholder="">' +
        '</td>' +
        '<td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="removeProduct(this)"><i class="fa fa-trash-o"></i></button></td>' +
      '</tr>';

    $('#purchase_return_table tbody').append(productRow);
    calculateTotal();
  }
}

function removeProduct(buttonElm) {
  $(buttonElm).parents('#purchase_return_table tbody tr').remove();

  calculateTotal();
}

function calculateTotal() {
  var total_quantity = 0;
  var total_cost = 0;
  var total_balance = 0;

  $('table#purchase_return_table tbody tr').each(function() {
    let quantity = $(this).find('input.return_quantity').val();
    let cost = $(this).find('input.purchase_price').val();

    total_quantity += parseInt(quantity);
    total_cost += parseFloat(cost);
    total_balance += parseFloat(cost * quantity);
    $(this).find('td input.sub-total').val(Number(parseFloat(cost*quantity)).toFixed(2));
  });

  $('span#total-quantity').html(total_quantity);
  $('span#total-cost').html(Number(total_cost).toFixed(2));

  $("span.shown_total_price").html(Number(total_balance).toFixed(2));
  $("input[name='total_price']").val(total_balance);

  calculateDueBalance();
}

function calculateDueBalance() {
  var total_amount = $('input.total_price').val() || 0;
  var discount = $("input.discount").val() || 0;
  var otherService = $("input.shipping-cost").val() || 0;
  // var otherService = $("input.other_service").val();
  var balance_amount = (parseFloat(total_amount) - parseFloat(discount)) + parseFloat(otherService);
  balance_amount = Number(balance_amount).toFixed(2);

  $('span.shown_balance_amount').html(balance_amount);
  $('input.balance_amount').val(balance_amount);
  $("input#total_payable_amount").val(balance_amount);
}