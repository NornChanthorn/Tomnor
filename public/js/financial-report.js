$(function () {
    $('#report_type').change(function () {
        // Disabled month select box when view type is yearly
        $('#month').attr('disabled', ($(this).val() != monthlyDuration));
    });
});
