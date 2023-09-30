$(function () {
    $('#warehouse').change(function () {
        $('#product').val('').change();
    });

    // Update product in-stock quantity
    $('#product').change(function() {
        if ($('#warehouse').valid() && $(this).val() != '') {
            $.ajax({
                url: stockQtyRetrievalUrl.replace(':warehouseId', $('#warehouse').val()).replace(':productId', $('#product').val()),
                success: function (stockQty) {
                    $('#stock-qty').html(stockQty);
                }
            })
        } else {
            $('#stock-qty').html(NALabel);
        }
    });
});
