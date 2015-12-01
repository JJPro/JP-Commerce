/**
 * Handles interaction and validation of the artwork data form.
 */
jQuery(function($){
    // datepicker
    $('#make-date').datepicker({maxDate: 0});

    $("#price").on("focus", function(){
        $("#profit-calculator").slideDown();
        var commission_rate = $("#commission-rate").val();
        var commission_fee = parseFloat(commission_rate * $(this).val()).toFixed(2);
        var profit = parseFloat(($(this).val() - commission_fee)).toFixed(2);
        $("#commission-fee").html("$ " + commission_fee);
        $("#profit").html("$ " + profit);
    });

    $("#price").on("keyup", function() {
        var commission_rate = $("#commission-rate").val();
        var commission_fee = parseFloat(commission_rate * $(this).val()).toFixed(2);
        var profit = parseFloat(($(this).val() - commission_fee)).toFixed(2);
        $("#commission-fee").html("$ " + commission_fee);
        $("#profit").html("$ " + profit);
    })

});
