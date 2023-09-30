$(function () {
    $('.btn-delete').click(function () {
        confirmPopup($(this).data('url'), 'warning', 'DELETE');
    });
});
