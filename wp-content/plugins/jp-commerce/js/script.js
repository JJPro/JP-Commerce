/**
 * Created by jjpro on 11/10/15.
 */
jQuery(function($){
    // datepicker
    $('.datepicker').datepicker({maxDate: 0});

    $("#price-input").on("focus", function(){
        $("#profit-calculator").show("slow");
        var commission_rate = $("#commission-rate").val();
        var commission_fee = parseFloat(commission_rate * $(this).val()).toFixed(2);
        var profit = parseFloat(($(this).val() - commission_fee)).toFixed(2);
        $("#commission-fee").html("$ " + commission_fee);
        $("#profit").html("$ " + profit);
    });

    $("#price-input").on("keyup", function() {
        var commission_rate = $("#commission-rate").val();
        var commission_fee = parseFloat(commission_rate * $(this).val()).toFixed(2);
        var profit = parseFloat(($(this).val() - commission_fee)).toFixed(2);
        $("#commission-fee").html("$ " + commission_fee);
        $("#profit").html("$ " + profit);
    })


    var admin_branding = $("<div id='admin-corner-branding'></div>")
        .css({"width": "100%", "height": "160px",
            "background-position": "center center",
            "background-repeat": "no-repeat",
            "background-size": "cover",
            "background-image": "url('http://localhost/~jjpro/wp_clean/wp-content/plugins/jp-commerce/images/logo/small.png')", "position": "absolute", "bottom": "0"});
    $("#adminmenuback").css("height", "100%").append(admin_branding);

    $("#notforsale").on("click", function() {
        $("#set-price-div").toggle(!this.checked);
    })
});