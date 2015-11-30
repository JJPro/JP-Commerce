<?php
/**
 * Artwork Data
 *
 * Functions for displaying the artwork data meta box.
 *
 * @class       JC_Meta_Box_Artwork_Data
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JC_Meta_Box_Artwork_Data 
{
    private $desc, $date_created, $dimensions, $weight;

    private $not_for_sale = false;
    private $price, $inventory;

    public static function output($post){
        wp_nonce_field( 'jc_save_data', 'jc_meta_nonce' );

        $post_id = $post->ID;

        $make_date      = get_post_meta($post_id, 'make_date', true);
        $dimensions     = get_post_meta($post_id, 'dimensions', true);
        $weight         = get_post_meta($post_id, 'weight', true);
        $not_for_sale   = get_post_meta($post_id, 'not_for_sale', true);
        $price          = get_post_meta($post_id, 'price', true);
        $inventory      = get_post_meta($post_id, 'inventory', true); // -1 for unlimited
        $description    = get_post_meta($post_id, 'description', true);

        ?>
        <p>
            <label for="make-date">When was it made?</label><input type="date" id="make-date" name="make-date" max="<?php echo date_i18n( 'm/d/Y', time() ); ?>" value="<?php echo $make_date; ?>"/>
        </p>
        <p>
            <label for="_length">Dimensions (in)</label>
            <span class="wrap" data-required="true" style="display: inline-block; margin-left:0px;">
                <input type="text" id="_length" name="dimensions['length']" placeholder="Length" value="<?php echo @$dimensions['length']; ?>"/>
                <input type="text" name="dimensions['width']" placeholder="Width" value="<?php echo @$dimensions['width']; ?>"/>
                <input type="text" name="dimensions['height']" placeholder="Height" value="<?php echo @$dimensions['height']; ?>"/>
            </span>
        </p>
        <p>
            <label for="weight">Weight (lbs)</label><span data-required=true><input type="text" id="weight" name="weight" /></span>
        </p>
        <p>
            <label for="not-for-sale">Not for sale</label><span><input type="checkbox" id="not-for-sale" name="not_for_sale" /></span>
        </p>
        <div id="for-sale-wrap">
        <p>
            <label for="price">Price</label><span data-required=true style="position:relative"><span class="currency-symbol" style="position:absolute; top: 0; left: 5px;">$</span><input type="text" id="price" name="price" style="padding-left: 15px; -webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;" /></span>
        </p>
        <p>
            <label for="stock">Inventory <span>(Enter -1 for unlimited)</span></label><span data-required=true><input type="number" id="stock" name="stock" value="1" /></span>
        </p>
        </div>
        <p>
            <label for="description">Description</label><span data-required=true><textarea id="description" name="description" style="width: 65%"></textarea></span>
        </p>
        <style>
            #artwork-data p>label {
                width: 30%;
                float: left;
            }
            #artwork-data span.wrap input {
                width: 30%;
            }
            span[data-required]::before {
                content: "*";
                color: red;
                display: inline-block;
                width: 10px;
                margin-left: -10px;
            }
        </style>
        <script type="text/javascript">
            var not_for_sale = document.getElementById('not-for-sale');
            var wrap = document.getElementById('for-sale-wrap');
            if (not_for_sale.checked) {
                wrap.style.display = 'none';
            } else {
                wrap.style.display = 'block';
            }
            not_for_sale.addEventListener('change', function(e){
                if (this.checked) {
                    wrap.style.display = 'none';
                } else {
                    wrap.style.display = 'block';
                }
            }, true);
        </script>
        <?php
    }

    public static function save($post_id, $post){
    }
}
