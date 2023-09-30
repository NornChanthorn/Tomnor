$(function () {
// Validate form elements when submit
    callFileInput('#photo', 1, 5120, ['jpg', 'jpeg', 'png']);
    $('#payment-form').validate({
        payment_date: { required: true, dateISO: false },
        payment_amount: { required: true, min: 1},
        payment_method: { required: true },
    });

    // For advanced payment
    $('.schedule').on('change', function () {
        var paymentAmountElm = $('#payment_amount');
        var paymentAmount = parseFloat(paymentAmountElm.val());
        if (isNaN(paymentAmount)) {
            paymentAmount = 0;
        }

        var checkStatus = $(this).prop('checked');
        var principal = parseFloat($(this).data('principal'));
        var scheduleId = $(this).data('schedule-id');

        if (checkStatus) {
            paymentAmount += principal;
            $('#payment-form').append('<input type="hidden" name="selected_schedules[]" id="' + scheduleId + '" value="' + scheduleId + '">');
        } else {
            paymentAmount -= principal;
            $('#' + scheduleId).remove();
        }

        paymentAmountElm.val(paymentAmount);
    });
    if(repayment_type=2){
        var principal = $('#principal').val();
        var interest = 0;
        var discount =  0;
        var interest_discount = 0;
        var penalty = 0;
        var total_amount = 0;
        var wave = 0;
     
        $('#discount_interest').on('keyup change', function(){
            discount = $('#discount_interest').val();
            penalty =$('#penalty_amount').val();
            interest =$('#interest').val();
            interest_discount = interest * discount / 100;
            wave = $('#wave').val();
            wave = penalty - wave;
            $('#interest_after_discount').val(interest-interest_discount);
            total_amount = Number(principal) + Number(wave) + Number(interest) - interest_discount;
            $('#payment_amount').val(total_amount);
        });
        $('#penalty_amount').on('keyup change', function(){
            discount = $('#discount_interest').val();
            penalty =$('#penalty_amount').val();
            interest =$('#interest').val();
            interest_discount = interest * discount / 100;
            wave = $('#wave').val();
            wave = penalty - wave;
            $('#interest_after_discount').val(interest-interest_discount);
            total_amount = Number(principal) + Number(wave) + Number(interest) - interest_discount;
            $('#payment_amount').val(total_amount);
        });
        $('#interest').on('keyup change', function(){
            discount = $('#discount_interest').val();
            penalty =$('#penalty_amount').val();
            interest =$('#interest').val();
            interest_discount = interest * discount / 100;
            wave = $('#wave').val();
            wave = penalty - wave;
            $('#interest_after_discount').val(interest-interest_discount);
            total_amount = Number(principal) + Number(wave) + Number(interest) - interest_discount;
            $('#payment_amount').val(total_amount);
        });
        $('#wave').on('keyup change', function(){
            discount = $('#discount_interest').val();
            penalty =$('#penalty_amount').val();
            interest =$('#interest').val();
            interest_discount = interest * discount / 100;
            wave = $('#wave').val();
            wave = penalty - wave;
            $('#interest_after_discount').val(interest-interest_discount);
            total_amount = Number(principal) + Number(wave) + Number(interest) - interest_discount;
            $('#payment_amount').val(total_amount);
        });
     }

});
