$(function () {
    $('#branch').change(function () {
        $('#agent').html(agentSelectLabel);
        var branchId = $(this).val();

        if (branchId != '') {
            $.ajax({
                url: agentRetrievalUrl.replace(':branchId', branchId),
                success: function (data) {
                    var agentData = agentSelectLabel;

                    $.each(data, function (key, value) {
                        agentData += '<option value="' + value.user_id + '">' + value.name + '</option>';
                    });
                    $('#agent').html(agentData);
                }
            });
        }
    });
});
