/**
 * Handles interaction and validation of the artwork data form.
 */
jQuery(function($){
    // datepicker
    $('#make-date').spinner({max: (new Date()).getFullYear(), numberFormat: 'n'});

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

    // Does this item have a frame?
    var has_frame = $('input[name="has_frame"]:checked').val();
    if (has_frame === "false") {
        $('.hide-if-no-frame').hide();
    }

    $('input[name="has_frame"]').on('change', function() {
        var has_frame = $(this).val();

        if (has_frame === "false") {
            $('.hide-if-no-frame').hide();
        } else {
            $('.hide-if-no-frame').show();

        }
    })

});
