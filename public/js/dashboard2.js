

$('#today-tab').click(function() {
    var location = $('#location').val();
    $.ajax({ 
        type: 'GET',
        url: "/dashboard-suggestion",
        dataType: 'json',
        data:{
            type:'today',
            location:location,
        },
        success: function(result) {
            if (result) {
                $('#totalSellAmount').html('$ ' + result.totalSellAmount);
                $('#totalPurchaseAmount').html('$ ' + result.totalPurchaseAmount);
                $('#totalDuePurchaseAmount').html('$ ' + result.totalDuePurchaseAmount);
                $('#totalDuePurchaseAmount').html('$ ' + result.totalDuePurchaseAmount);
                $('#totalNetRevenueAmount').html('$ ' + result.totalNetRevenueAmount);
                $('#totalDueSaleAmount').html('$ ' + result.totalDueSaleAmount);
                $('#totalClient').html(result.totalClient);
                $('#totalProfit').html('$ ' + result.totalProfit);
                $('#totalProductQTY').html(result.totalProductQTY + " PCs");
                $('#totalExpenseAmount').html('$ ' + result.totalExpenseAmount);
                // console.log(result);
            }
        },
    });
});
$('#weekly-tab').click(function() {
    var location = $('#location').val();
    $.ajax({ 
        type: 'GET',
        url: "/dashboard-suggestion",
        dataType: 'json',
        data:{
            type:'weekly',
            location:location,
        },
        success: function(result) {
            if (result) {
                $('#totalSellAmount').html('$ ' + result.totalSellAmount);
                $('#totalPurchaseAmount').html('$ ' + result.totalPurchaseAmount);
                $('#totalDuePurchaseAmount').html('$ ' + result.totalDuePurchaseAmount);
                $('#totalDuePurchaseAmount').html('$ ' + result.totalDuePurchaseAmount);
                $('#totalNetRevenueAmount').html('$ ' + result.totalNetRevenueAmount);
                $('#totalDueSaleAmount').html('$ ' + result.totalDueSaleAmount);
                $('#totalProfit').html('$ ' + result.totalProfit);
                $('#totalClient').html(result.totalClient);
                $('#totalProductQTY').html(result.totalProductQTY + " PCs");
                $('#totalExpenseAmount').html('$ ' + result.totalExpenseAmount);
                // console.log(result.totalSellAmount);
            }
        },
    });
});
$('#month-tab').click(function() {
    var location = $('#location').val();
    $.ajax({ 
        type: 'GET',
        url: "/dashboard-suggestion",
        dataType: 'json',
        data:{
            type:'monthly',
            location:location,
        },
        success: function(result) {
            if (result) {
                $('#totalSellAmount').html('$ ' + result.totalSellAmount);
                $('#totalPurchaseAmount').html('$ ' + result.totalPurchaseAmount);
                $('#totalDuePurchaseAmount').html('$ ' + result.totalDuePurchaseAmount);
                $('#totalDuePurchaseAmount').html('$ ' + result.totalDuePurchaseAmount);
                $('#totalNetRevenueAmount').html('$ ' + result.totalNetRevenueAmount);
                $('#totalDueSaleAmount').html('$ ' + result.totalDueSaleAmount);
                $('#totalProfit').html('$ ' + result.totalProfit);
                $('#totalClient').html(result.totalClient);
                $('#totalProductQTY').html(result.totalProductQTY + " PCs");
                $('#totalExpenseAmount').html('$ ' + result.totalExpenseAmount);
                // console.log(result.totalSellAmount);
            }
        },
    });
});
$('#year-tab').click(function() {
        var location = $('#location').val();
        $.ajax({ 
            type: 'GET',
            url: "/dashboard-suggestion",
            dataType: 'json',
            data:{
                type:'yearly',
                location:location,
            },
            success: function(result) {
                if (result) {
                    $('#totalSellAmount').html('$ ' + result.totalSellAmount);
                    $('#totalPurchaseAmount').html('$ ' + result.totalPurchaseAmount);
                    $('#totalDuePurchaseAmount').html('$ ' + result.totalDuePurchaseAmount);
                    $('#totalDuePurchaseAmount').html('$ ' + result.totalDuePurchaseAmount);
                    $('#totalNetRevenueAmount').html('$ ' + result.totalNetRevenueAmount);
                    $('#totalDueSaleAmount').html('$ ' + result.totalDueSaleAmount);
                    $('#totalProfit').html('$ ' + result.totalProfit);
                    $('#totalClient').html(result.totalClient);
                    $('#totalProductQTY').html(result.totalProductQTY + " PCs");
                    $('#totalExpenseAmount').html('$ ' + result.totalExpenseAmount);

                }
            },
        });
    });
 
window.onload = (event) => {

    var location = $('#location').val();
    $.ajax({ 
        type: 'GET',
        url: "/dashboard-suggestion",
        dataType: 'json',
        data:{
            type:'today',
            location:location,
        },
        success: function(result) {
            if (result) {
                $('#totalSellAmount').html('$ ' + result.totalSellAmount);
                $('#totalPurchaseAmount').html('$ ' + result.totalPurchaseAmount);
                $('#totalDuePurchaseAmount').html('$ ' + result.totalDuePurchaseAmount);
                $('#totalDuePurchaseAmount').html('$ ' + result.totalDuePurchaseAmount);
                $('#totalNetRevenueAmount').html('$ ' + result.totalNetRevenueAmount);
                $('#totalDueSaleAmount').html('$ ' + result.totalDueSaleAmount);
                $('#totalProfit').html('$ ' + result.totalProfit);
                $('#totalClient').html(result.totalClient);
                $('#totalProductQTY').html(result.totalProductQTY + " PCs");
                $('#totalExpenseAmount').html('$ ' + result.totalExpenseAmount);
                // console.log(result);
            }
        },
    });
};
