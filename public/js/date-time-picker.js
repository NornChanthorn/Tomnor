
$(function () {
    datePicker();
});

function datePicker() {
    $(".date-picker").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        orientation: 'bottom right'
      });
    // $('.date-picker').mask('00-00-0000');
}
