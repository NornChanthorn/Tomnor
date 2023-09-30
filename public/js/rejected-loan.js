$(function () {
    $('.btn-revert').click(function () {
        confirmPopup($(this).data('revert-url'), 'warning', 'POST', $(this).data('redirect-url'));
    });
});
