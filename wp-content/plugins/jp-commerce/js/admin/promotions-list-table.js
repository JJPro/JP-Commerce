/**
 *
 * Manages ajax events in promotions list table
 */

jQuery(function($){
    var ajaxurl = jc_data.ajaxurl;
    var action = 'jp_commerce_toggle_promotion';

    $('span[data-promo]').on('click', function(e, target) {
        var promo = $(this).attr('data-promo'),
            $current_active_promo = $('span[data-promo][class="icon-toggle-filled"]'),
            that = this;

        $.ajax(ajaxurl, {
            data: {
                'promotion': promo,
                'action' : action
            },
            type: 'POST',
            success: function(result, status, xhr) {
                if ( result.success ) {
                    if ( $current_active_promo.attr('data-promo') != promo )
                        $current_active_promo.removeClass('icon-toggle-filled').addClass('icon-toggle');
                    $(that).toggleClass('icon-toggle icon-toggle-filled');
                }
            }
        });
    });
});