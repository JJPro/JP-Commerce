/**
 * Created by jjpro on 12/22/15.
 *
 * Manages ajax events in artworks list table
 */

// toggle-featured-button, data-is_featured (1, 0)
jQuery(function($){
    var ajaxurl = jc_data.ajaxurl;
    var action_set_as_featured = 'jp_commerce_set_artwork_as_featured';
    var action_cancel_featured = 'jp_commerce_cancel_artwork_featured';

    $('span[data-is_featured]').on('click', function(e, target) {
        var artwork = $(this).attr('data-artwork');
        var is_featured = this.hasAttribute('featured');
        var that = this;
        var action;

        if (is_featured)
            action = action_cancel_featured;
        else
            action = action_set_as_featured;

        $.ajax(ajaxurl, {
            data: {
                'artwork': artwork,
                'action' : action
            },
            type: 'POST',
            success: function(result, status, xhr) {
                if ( result.success ) {
                    $(that).toggleClass('featured');
                }
            }
        });
    });
});