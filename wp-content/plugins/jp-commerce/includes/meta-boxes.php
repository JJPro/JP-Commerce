<?php
/**
 * Defines all the meta boxes
 */
if ( !defined('ABSPATH') ) exit;

function artwork_date_created_meta_box($post, $box) {
    // retrieve meta values
    $date_created = get_post_meta($post->ID, 'date_created', true);

    // nonce for security
    wp_nonce_field(plugin_basename(__FILE__), 'date_created_meta_box');

    // HTML
    ?>
    <label for="date_created">Date:</label>
    <input id="date_created" class="datepicker" name="date_created" value="<?php echo $date_created; ?>" size="10">
    <?php
}
function save_artwork_date_created_meta_box($post_id){
    if (isset($_POST['date_created_meta_box']))
    {
        // skip auto-saving
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        // check nonce for security
        wp_verify_nonce(plugin_basename(__FILE__), 'date_created_meta_box');

        // now save the meta data
        $date = preg_replace("([^0-9/])", "", $_POST['date_created']);
        update_post_meta($post_id, 'date_created', $date);
    }
}


// price meta box
function artwork_price_meta_box($post, $box) {
    global $logger;
    $logger->log_action("box", json_encode($box));
    $price = get_post_meta($post->ID, 'price', true);
    $notforsale = get_post_meta($post->ID, 'notforsale', true);
    ?>

    <?php wp_nonce_field(plugin_basename(__FILE__), 'artwork_price_meta_box'); ?>
    <input type="checkbox" id="notforsale" name="notforsale" value="true" <?php checked($notforsale); ?>><label for="notforsale"><strong>Not For Sale</strong></label>
    <div id="set-price-div" style="margin-top: 15px; <?php if ($notforsale) print "display:none;"; ?>">
        <label class="currency_symbol" for="price-input">$ </label>
        <input type="text" id="price-input" name="price" value="<?php echo esc_attr($price); ?>" size="10" >
        <input type="hidden" id="commission-rate" value="<?php echo esc_attr(get_option("commission_rate")); ?>" >
        <div id="profit-calculator" style="display: none; margin-top: 15px;border-top: dashed grey 1px; ">
            <table style="text-align: left; ">
                <tr>
                    <th>Commission Fee (<?php echo get_option("commission_rate")*100; ?> %): </th><td id="commission-fee"></td>
                </tr>
                <tr>
                    <th>You Will Get: </th><td id="profit" style="text-decoration: underline; color: red;"></td>
                </tr>
            </table>

        </div>

    </div>
    <?php
}
function save_artwork_price_meta_box($post_id) {
    // process form only if $_POST is set
    if (isset($_POST['artwork_price_meta_box']))
    {
        // skip auto-saving
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        // check nonce for security
        wp_verify_nonce(plugin_basename(__FILE__), 'artwork_price_meta_box');

        // now save the meta data
        update_post_meta($post_id, 'price', floatval($_POST['price']));
        update_post_meta($post_id, 'notforsale', isset($_POST['notforsale'])? 1: 0);
    }
}

// TODO: upload media: photos and videos
function artwork_upload_media_meta_box($post, $box) {
    $images_index_combo = get_post_meta($post->ID, 'images', false);
        /** array( 'index' => int, 'images' => array() ) */
    $images = $images_index_combo['images'];
    $featured_image_index = $images_index_combo['index'];
    ?>
    <?php wp_nonce_field(plugin_basename(__FILE__), 'artwork_upload_meta_box'); ?>
    <div class="dropzone">
        <div class="fallback">
            <input name="file" type="file" multiple />
        </div>
    </div>
    <?php
}
function save_artwork_upload_media_meta_box($post_id) {
// TODO
}


