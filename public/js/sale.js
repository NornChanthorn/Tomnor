$(function () {
  //show inline client field into form
  // $('#add-client').on('click', function(){
  //   $('.client-addition').toggleClass('hide');
  // });

	//Update line total and check for quantity not greater than max quantity
  $('table#sale-product-table tbody').on('change', 'input.quantity', function() {
    // var max_qty = parseFloat($(this).data('rule-max'));
    var entered_qty = $(this).val();

    var tr = $(this).parents('tr');

    var unit_price_inc_tax = tr.find('input.unit_price').val();
    var line_total = entered_qty * unit_price_inc_tax;
    line_total = Number(line_total).toFixed(2);

    tr.find('input.sub_total').val(line_total);

    calculateTotal();
  });

  $('table#sale-product-table tbody').on('change', 'input.unit_price', function() {
    // var max_qty = parseFloat($(this).data('rule-max'));
    var unit_price_inc_tax = $(this).val();
    var tr = $(this).parents('tr');

    var entered_qty = tr.find('input.quantity').val();
    var line_total = entered_qty * unit_price_inc_tax;
    line_total = Number(line_total).toFixed(2);
    tr.find('input.sub_total').val(line_total);

    calculateTotal();
  });

  //Update balance
  $('table#sale-product-table tfoot').on('change', 'input.paid_amount', function() {
    calculate_balance_due();
  });

  //Update balance
  $('table#sale-product-table tfoot').on('change', 'input.discount', function() {
    calculate_balance_due();
  });

  //Update balance
  $('table#sale-product-table tfoot').on('change', 'input.other_service', function() {
    calculate_balance_due();
  });

  calculateTotal();
});

//Add product row into product cart
//---------------------
function sale_product_row() {
	if($('#product').attr('required', true).valid()) {
		// let productElm = $('#product').find(':selected');
		var pro_ctn = $('#sale-product-table tbody');
    let productId = $('#product').val();

		//check product stock qty
		if(products[productId] == undefined) {
			alert('This product is out of stock.');
			return;
		}

    let isProductAdded = (pro_ctn.find('tr[data-id="' + productId + '"]').length > 0);
    let productElm = products[productId];

    console.log(products[productId]);

    if (!isProductAdded) {
      let productRow =
      '<tr data-id="' + productId + '">' +
      '<input type="hidden" name="products[' + productId + '][id]" value="' + productElm.product_id + '">' +
      '<input type="hidden" name="products[' + productId + '][name]" value="' + productElm.text + '">' +
      '<input type="hidden" name="products[' + productId + '][code]" value="' + productElm.code + '">' +
      '<input type="hidden" name="products[' + productId + '][variantion_id]" value="' + productElm.variantion_id + '">' +
      '<input type="hidden" name="products[' + productId + '][enable_stock]" value="' + productElm.enable_stock + '">' +
      '<td>' + productElm.text + '</td>' +
      '<td>' + (productElm.code || noneLabel) + '</td>' +
      '<td width="15%">' + (parseInt(productElm.qty_available) || noneLabel) + '</td>' +
      '<td width="15%">' +
      '<input type="text" name="products[' + productId + '][quantity]" class="form-control form-control-sm integer-input quantity" min="0.11" required value="1">' +
      '</td>' +
      '<td width="15%">' +
      '<input type="text" name="products[' + productId + '][price]" class="form-control form-control-sm decimal-input unit_price" min="1"  required value="'+productElm.selling_price+'">' +
      '</td>' +
      '<td width="15%">' +
      '<input type="text" name="products[' + productId + '][sub_total]" class="form-control form-control-sm integer-input sub_total" min="0.1" required value="'+productElm.selling_price+'" readonly>' +
      '</td>' +
      '<td><button type="button" class="btn btn-danger btn-sm" onclick="rmProduct(this)"><i class="fa fa-trash-o"></i></button></td>' +
      '</tr>';

      pro_ctn.append(productRow);
      formatNumericFields();
      calculateTotal();
    }

    $('#product').attr('required', false).val('').trigger('change');
  }
}

function rmProduct(buttonElm) {
  $(buttonElm).parents('#sale-product-table tbody tr').remove();

  calculateTotal();
}

function calculateTotal() {
	var total_quantity = 0;
  var price_total = 0;
  var unit_price = 0;

  $('table#sale-product-table tbody tr').each(function() {
    // let quantity = $(this).find('input.quantity').val();
    let cost = $(this).find('input.sub_total').val();
    // total_quantity += parseInt(quantity);
    unit_price  += parseFloat(cost);
    
  });
  price_total = Number(unit_price).toFixed(2);

  //$('span.unit_price_total').html(unit_price_total);
  $('span.shown_total_price').html(price_total);
  $('input.total_price').val(price_total);

  calculate_billing_details(price_total);
}

function calculate_billing_details(price_total) {
  // var discount = pos_discount(price_total);
  // var order_tax = pos_order_tax(price_total, discount);

  //Add shipping charges.
  // var shipping_charges = __read_number($('input#shipping_charges'));

  // var total_payable = price_total + order_tax - discount + shipping_charges;

  // __write_number($('input#final_total_input'), total_payable);
  // var curr_exchange_rate = 1;
  // if ($('#exchange_rate').length > 0 && $('#exchange_rate').val()) {
  //     curr_exchange_rate = __read_number($('#exchange_rate'));
  // }
  // var shown_total = total_payable * curr_exchange_rate;
  // $('span#total_payable').text(__currency_trans_from_en(shown_total, false));

  // $('span.total_payable_span').text(__currency_trans_from_en(total_payable, true));

  //Check if edit form then don't update price.
  // if ($('form#edit_pos_sell_form').length == 0) {
  //     __write_number($('.payment-amount').first(), total_payable);
  // }

  calculate_balance_due();
}

function calculate_balance_due() {
  var total_amount = $('input.total_price').val() || 0;
  var discount = $("input.discount").val() || 0;
  var otherService = $("input.other_service").val() || 0;
  // var paid_amount = $('input.paid_amount').val();
  var balance_amount = (parseFloat(total_amount) - parseFloat(discount)) + parseFloat(otherService);
  balance_amount = Number(balance_amount).toFixed(2);

  $('span.shown_balance_amount').html(balance_amount);
  $('input.balance_amount').val(balance_amount);
  $("input.paid_amount").val(balance_amount);

  // var total_payable = __read_number($('#final_total_input'));
  // var total_paying = 0;
  // $('#payment_rows_div')
  //     .find('.payment-amount')
  //     .each(function() {
  //         if (parseFloat($(this).val())) {
  //             total_paying += __read_number($(this));
  //         }
  //     });
  // var bal_due = total_payable - total_paying;
  // var change_return = 0;

  // //change_return
  // if (bal_due < 0 || Math.abs(bal_due) < 0.05) {
  //     __write_number($('input#change_return'), bal_due * -1);
  //     $('span.change_return_span').text(__currency_trans_from_en(bal_due * -1, true));
  //     change_return = bal_due * -1;
  //     bal_due = 0;
  // } else {
  //     __write_number($('input#change_return'), 0);
  //     $('span.change_return_span').text(__currency_trans_from_en(0, true));
  //     change_return = 0;
  // }

  // __write_number($('input#total_paying_input'), total_paying);
  // $('span.total_paying').text(__currency_trans_from_en(total_paying, true));

  // __write_number($('input#in_balance_due'), bal_due);
  // $('span.balance_due').text(__currency_trans_from_en(bal_due, true));

  // __highlight(bal_due * -1, $('span.balance_due'));
  // __highlight(change_eturn * -1, $('span.change_return_span'));
}

function addProduct(productElm) {
  let indexId = productElm.product_id+productElm.variantion_id;
  let isProductAdded = ($('#sale-product-table tbody').find('tr[data-id="' + indexId + '"]').length > 0);

  // if(productElm.qty_available==null) {
  //   alert('This product is out of stock.');
  //   return;
  // }

  if (!isProductAdded) {
    let productRow =
    '<tr data-id="' + indexId + '">' +
    '<input type="hidden" name="products[' + indexId + '][id]" value="' + productElm.product_id + '">' +
    '<input type="hidden" name="products[' + indexId + '][name]" value="' + productElm.name + '">' +
    '<input type="hidden" name="products[' + indexId + '][code]" value="' + productElm.code + '">' +
    '<input type="hidden" name="products[' + indexId + '][variantion_id]" value="' + productElm.variantion_id + '">' +
    '<input type="hidden" name="products[' + indexId + '][enable_stock]" value="' + productElm.enable_stock + '">' +
    '<td>' + productElm.label +
    '</td>' +
    '<td>' + (productElm.code || noneLabel) + '</td>' +
    '<td width="15%">' + (parseInt(productElm.qty_available) || noneLabel) + '</td>' +
    '<td width="15%">' +
    '<input type="text" name="products[' + indexId + '][quantity]" class="form-control form-control-sm integer-input quantity" min="1" max="'+ productElm.qty_available +'" required value="1">' +
    '</td>' +
    '<td width="15%">' +
    '<input type="text" name="products[' + indexId + '][price]" class="form-control form-control-sm decimal-input unit_price" min="0.01" required value="'+productElm.price+'">' +
    '</td>' +
    '<td width="15%">' +
    '<input type="text" name="products[' + indexId + '][sub_total]" class="form-control form-control-sm integer-input sub_total" min="0.01" required value="'+productElm.price+'" readonly>' +
    '</td>' +
    '<td><button type="button" class="btn btn-danger btn-sm" onclick="rmProduct(this)"><i class="fa fa-trash-o"></i></button></td>' +
    '</tr>';

    $('#sale-product-table tbody').append(productRow);

    // popup modal IMEI
    if(productElm.enable_sr_no) {
      let currentModalDescription = '#row_description_modal_'+indexId;
      $(currentModalDescription).modal('show');
      $(currentModalDescription).on('shown.bs.modal', function () {
        $('#description').trigger('focus');
      })
    }

    calculateTotal();
  }
}
