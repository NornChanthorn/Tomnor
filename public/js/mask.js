$(function () {
   formatFields();
});

function formatFields() {
    $('.phone, .id-card').mask('000 000 0000');
    $('#date-picker').mask('00-00-0000');
}
