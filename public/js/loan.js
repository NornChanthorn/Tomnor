$(function () {
    $('#wing_code').mask('0000 0000');

    // Calculate payment schedule automatically when show loan detail
    if (formType == formShowType) {
      calcPaymentSchedules();
    }

    // Validate form fields
    $('#loan-form').validate({
      'branch': { required: true },
      'agent': { required: true },
      'client': { required: true },
      'wing_code': { required: true },
      'client_code': { required: true },
      'product': { required: true },
      'schedule_type': { required: true },
      'loan_amount': { required: true, min: 1 },
      'depreciation_amount': { required: true, min: 0 },
      'interest_rate': { required: true, min: 0 },
      'installment': { required: true, min: 1 },
      'loan_start_date': { required: true },
    });

    // Update loan amount and down payment when change product, product price, or depreciation amount
    $('#product_price, #depreciation_amount').on('change paste keyup', function () {
      // console.log($("#product").find(":selected").val());
      var productPrice = $('#product_price').val();
      var depreciationAmount = $('#depreciation_amount').val();

      calculateTotal(productPrice, depreciationAmount);
    });

    // When change payment schedule type
    $('#schedule_type').change(function () {
      var scheduleType = $(this).val();
      $(this).removeClass('text-danger');

      switch (scheduleType) {
        case '':
        case flatInterestSchedule:
        $('#interest_rate').attr('disabled', true);
        $('#rate_sign').text('');
        break;

        case equalPaymentSchedule:
        case declineInterestSchedule:
        $('#interest_rate').attr('disabled', false);
        $('#rate_sign').text('*');
        $('#rate_text').text(scheduleType == equalPaymentSchedule ? loanRateLabel : interestRateLabel);
        break;

        default:
        $(this).addClass('text-danger').focus();
        return false;
      }
    });

    // When click button to calculate payment schedule
    $('#calculate-payment').click(function () {
      $('#error-msg').text('');

      // If fields are invalid
      if (
        !($('#schedule_type, #loan_amount, #depreciation_amount, #down_payment_amount, #installment, #loan_start_date').attr('required', true).valid())
        || ([equalPaymentSchedule, declineInterestSchedule].includes($('#schedule_type').val()) && !($('#interest_rate').attr({'required': true, 'min': 0}).valid()))
        || ($('#sale-product-table tbody').length == 0)
      ) {
        $('#schedule-table').html('');

        return false;
      }

      calcPaymentSchedules();
      var print = document.getElementById("print");
      print.style.display = "block";
    });


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
      calculateTotalLoan();
    });

    //Update balance
    $('table#sale-product-table tfoot').on('change', 'input.other_service', function() {
      calculate_balance_due();
      calculateTotalLoan();
    });

    calculateTotal();
  });

  /**
   * Calculate and display table of payment schedule
   */
  function calcPaymentSchedules() {
    var scheduleType = $('#schedule_type').val();

    $.ajax({
      url: scheduleRetrievalUrl,
      data: {
        // Payment schedule data
        schedule_type: scheduleType,
        down_payment_amount: $('#down_payment_amount').val(),
        interest_rate: $('#interest_rate').val(),
        installment: $('#installment').val(),
        payment_per_month: $('#payment_per_month').val(),
        loan_start_date: $('#loan_start_date').val(),
        first_payment_date: $('#first_payment_date').val(),
      },
      success: function (data) {
        var isFlatInterestSchedule = (scheduleType == flatInterestSchedule);
        var grandTotalAmount = totalInterest = totalPrinciple= 0;
        var scheduleData = '<thead><tr><th>' + noLabel + '</th><th>' + paymentDateLabel + '</th>';

        if (isFlatInterestSchedule) {
          scheduleData += '<th>' + paymentAmountLabel + '</th>';
        }
        else {
          scheduleData +=
          '<th>' + totalLabel + '</th>' +
          '<th>' + principalLabel + '</th>' +
          '<th>' + interestLabel + '</th>';
        }
        scheduleData += '<th>' + outstandingLabel + '</th></tr></thead><tbody>';

        $.each(data, function (key, value) {
          grandTotalAmount += decimalNumber(value.total,2);
          totalInterest += decimalNumber(value.interest);
          totalPrinciple += decimalNumber(value.principal);

          scheduleData += '<tr><td>' + ++key + '</td><td>' + value.payment_date + '</td>';

          if (isFlatInterestSchedule) {
            scheduleData += '<td>$ ' + value.principal + '</td>';
          }
          else {
            scheduleData +=
            '<td>$ ' + decimalNumber(value.total,2) + '</td>' +
            '<td>$ ' + decimalNumber(value.principal,2) + '</td>' +
            '<td>$ ' + decimalNumber(value.interest,2) + '</td>';
          }

          scheduleData += '<td>$ ' + value.outstanding + '</td></tr>';
        });

        if (!isFlatInterestSchedule) {
          scheduleData += '<tr><td></td> <td></td>' +
          '<td><b>$ ' + $.number(grandTotalAmount) + '</b></td>' +
          '<td><b>$ ' + $.number(totalPrinciple) + '</b></td>' +
          '<td><b>$ ' + $.number(totalInterest) + '</b></td>' +
          '<td></td></tr>';
        }

        scheduleData += '</tbody>';
        $('#schedule-table').html(scheduleData).show();
      },
      error: function (xhr, status, error) {
        $('#error-msg').text(xhr.responseJSON.message);
        $('#schedule-table').html('').hide();
      }
    });
  }

  function addProduct(productElm) {
    let indexId = productElm.product_id+productElm.variantion_id;
    let isProductAdded = ($('#sale-product-table tbody').find('tr[data-id="' + indexId + '"]').length > 0);
    // console.log(productElm);

    // if(productElm.qty_available==null || productElm.qty_available==0) {
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
      '<input type="text" name="products[' + indexId + '][price]" class="form-control form-control-sm decimal-input unit_price" min="1" required value="'+productElm.price+'">' +
      '</td>' +
      '<td width="15%">' +
      '<input type="text" name="products[' + indexId + '][sub_total]" class="form-control form-control-sm integer-input sub_total" min="1" required value="'+productElm.price+'" readonly>' +
      '</td>' +
      '<td><button type="button" class="btn btn-danger btn-sm" onclick="rmProduct(this)"><i class="fa fa-trash-o"></i></button></td>' +
      '</tr>';

      $('#sale-product-table tbody').append(productRow);

      calculateTotal(productElm.price);
    }
  }

  function rmProduct(buttonElm) {
    $(buttonElm).parents('#sale-product-table tbody tr').remove();

    calculateTotal(0);
  }

  function calculate_billing_details(price_total) {
    calculate_balance_due();
  }

  function calculate_balance_due() {
    var total_amount = $('input.total_price').val() || 0;
    var discount = $("input.discount").val() || 0;
    var otherService = $("input.other_service").val() || 0;
    var balance_amount = (parseFloat(total_amount) - parseFloat(discount)) + parseFloat(otherService);
    balance_amount = Number(balance_amount).toFixed(2);

    $('span.shown_balance_amount').html(balance_amount);
    $('input.balance_amount').val(balance_amount);
    $("input.paid_amount").val(balance_amount);
  }

  function calculateTotal() {
    var price_total = 0;

    $('table#sale-product-table tbody tr').each(function() {
      price_total += parseInt($(this).find('input.sub_total').val());
    });

    price_total = Number(price_total).toFixed(2);

    $('span.shown_total_price').html(price_total);
    $('input.total_price').val(price_total);
    calculate_billing_details(price_total);
    calculateTotalLoan();
  }

  function calculateTotalLoan() {
    var price_total = $('input.balance_amount').val();
    var depreciationAmount = $('#depreciation_amount').val();
    var loanAmount = downPaymentAmount = price_total;

    if (depreciationAmount > 0) {
      downPaymentAmount = decimalNumber(downPaymentAmount - depreciationAmount);
    }

    $("#product_price").val(price_total);
    $('#loan_amount').val(loanAmount);
    $('#down_payment_amount').val(downPaymentAmount);
  }
