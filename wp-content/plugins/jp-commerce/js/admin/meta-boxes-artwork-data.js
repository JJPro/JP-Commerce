/**
 * Handles interaction and validation of the artwork data form.
 */
jQuery(function($){

    // datepicker
    $('#make-date').spinner({max: (new Date()).getFullYear(), numberFormat: 'n'});


    // is for sale?
    $('[name=status]').change(function() {
        $('#for-sale-div').slideToggle(500);
    });


    // price calculator
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
    });







    var $notices_div = $('#artwork-info-notices');

    $('form').on('submit', function() {

        return form_validation();
    });

    function clear_errors() {
        $notices_div.empty();
    }

    function form_validation() {

        var has_error = false;
        clear_errors();

        //debugger;

        // collect fields
        // validate and collect errors
        if ( ! ($('#name').val().trim()) )  // name
        {
            jc_notice('Artwork Name is required!');
            has_error = true;
        }
        if ( ! ($('#description').val().trim()) ) // description
        {
            jc_notice('Description is required!');
            has_error = true;
        }

        if ( $('#artwork-category').val() == -1 ) // category
        {
            jc_notice('Category is required!');
            has_error = true;
        }

        if ( ! $('#artwork-materials-select-box').val() ) // materials
        {
            jc_notice('Materials is required!');
            has_error = true;
        }

        if ( ! $('[name^="dimensions"]').get().reduce( // dimensions
                function(prev, curr, i, array) {
                    if ($(curr).val().trim())
                        return prev && true;
                    else
                        return prev && false;
                },
                true))
        {
            jc_notice('Dimensions are required!');
            has_error = true;
        }


        // for sale fields
        if ( $('input[name="status"]:checked').val() == 1 ) {


            if ( ! $('#stock').val().trim() ) {
                jc_notice('Inventory value is required!');
                has_error = true;
            }
            if ( ! $('[name^="shipping-dimensions"]').get().reduce( // shipping-dimensions
                    function(prev, curr, i, array) {
                        if ($(curr).val().trim())
                            return prev && true;
                        else
                            return prev && false;
                    },
                    true) )
            {
                jc_notice('Shipping Dimensions are required!');
                has_error = true;
            }
            if ( ! $('#shipping-weight').val().trim() ) // shipping-weight
            {
                jc_notice('Shipping Weight is required!');
                has_error = true;
            }
            if ( ! $('#price').val().trim() )
            {
                jc_notice('Price is required!');
                has_error = true;
            }
            if ( ! $('[name^="shipping-from"]:not(#address-2)').get().reduce( // shipping-from
                    function(prev, curr, i, array) {
                        if ($(curr).val().trim())
                            return prev && true;
                        else
                            return prev && false;
                    },
                    true) )
            {
                jc_notice('Shipping From Address is required!');
                has_error = true;
            }
        }


        if (has_error == true)
        {

            // scroll to errors

            $('html, body').animate( {
                scrollTop: $('#artwork-data').offset().top - 40
            }, 500);
            return false;
        }
    }


    function jc_notice(msg) {
        $notices_div.append('<div class="notice error is-dismissible inline">' +
                '<p>' + msg + '</p>' +
                '<button type="button" class="notice-dismiss">' +
                '<span class="screen-reader-text">Dismiss this notice.</span>' +
                '</button>' +
                '</div>')
            .on('click', 'div.is-dismissible button.notice-dismiss', function() {
                $(this).parent().slideUp(function() {
                    $(this).remove();
                });
            });
    }


});
