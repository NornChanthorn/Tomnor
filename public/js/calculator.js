$('#loan_amount').on('change click', function(){
    var loan_amount = $(this).val();
    var depreciation_amount = $('#depreciation_amount').val();
    var down_payment_amount = loan_amount - depreciation_amount;
    $('#down_payment_amount').val(down_payment_amount);
});
$('#depreciation_amount').on('change click', function(){
    var loan_amount = $("#loan_amount").val();
    var depreciation_amount = $('#depreciation_amount').val();
    var down_payment_amount = loan_amount - depreciation_amount;
    $('#down_payment_amount').val(down_payment_amount);
});
$('#calculate-payment').click(function () {
  $('#error-msg').text('');
  calcPaymentSchedules();
});
function calcPaymentSchedules() {
  var scheduleType = $("#schedule_type").val();
  var  down_payment_amount = $('#down_payment_amount').val();
  var interest_rate = $('#interest_rate').val();
  var installment = $('#installment').val();
  var payment_per_month = $("#payment_per_month").val();
  var loan_start_date = $('#loan_start_date').val();
  var first_payment_date = $('#first_payment_date').val();

  $.ajax({
    url: scheduleRetrievalUrl,
    data: {
      // Payment schedule data
      schedule_type: scheduleType,
      down_payment_amount: down_payment_amount,
      interest_rate:  interest_rate,
      installment: installment,
      payment_per_month: payment_per_month,
      loan_start_date: loan_start_date,
      first_payment_date: first_payment_date,
    },
    success: function (data) {
      var isFlatInterestSchedule = (scheduleType == flatInterestSchedule);
      var grandTotalAmount = totalInterest = 0;
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
        totalInterest += decimalNumber(value.interest,2);
        scheduleData += '<tr><td>' + ++key + '</td><td>' + value.payment_date + '</td>';

        if (isFlatInterestSchedule) {
          scheduleData += '<td>$ ' + value.principal + '</td>';
        }
        else {
          scheduleData +=
          '<td>$ ' + Number(value.total).toFixed(2) + '</td>' +
          '<td>$ ' + Number(value.principal).toFixed(2) + '</td>' +
          '<td>$ ' + Number(value.interest).toFixed(2) + '</td>';
        }

        scheduleData += '<td>$ ' + value.outstanding + '</td></tr>';
      });

      if (!isFlatInterestSchedule) {
        scheduleData += '<tr><td></td> <td></td>' +
        '<td><b>$ ' + $.number(grandTotalAmount) + '</b></td>' +
        '<td></td>' +
        '<td><b>$ ' + $.number(totalInterest) + '</b></td>' +
        '<td></td></tr>';

      }

      scheduleData += '</tbody>';
      $('#schedule-table').html(scheduleData).show();
      var print = document.getElementById("print");
      print.style.display = "block";
    },
    error: function (xhr, status, error) {
      $('#error-msg').text(xhr.responseJSON.message);
      $('#schedule-table').html('').hide();
    }
  });
}
