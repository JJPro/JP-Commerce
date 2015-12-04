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
    public static function init() {
        self::enqueue_scripts();
    }

    private static function enqueue_scripts() {
        add_action("admin_enqueue_scripts",
            function()
            {
                wp_enqueue_script('meta-boxes-artwork-data', JC_PLUGIN_DIR_URL . 'js/admin/meta-boxes-artwork-data.min.js', ['tiptip', 'jquery-ui-datepicker']);
                wp_enqueue_style ('jquery-ui');
            }
        );
    }

    public static function output($post){
        wp_nonce_field( 'jc_save_data', 'jc_meta_nonce' );

        $artwork = new JC_Artwork($post);

        ?>
        <section id="artwork-detail">
            <h4>Artwork Detail:</h4>
            <p>
                <label for="make-date">When was it made?</label><input id="make-date" name="make-date" max="<?php echo date_i18n( 'm/d/Y', time() ); ?>" value="<?php echo $artwork->make_date; ?>"/>
            </p>
            <p>
                <label>Does this item have a frame?</label>
                <span>
                    <label for="with-frame">YES </label><input type="radio" id="with-frame" name="has-frame" value="true" />
                    <label for="no-frame">NO </label><input type="radio" id="no-frame" name="has-frame" value="false" />
                </span>
            </p>
            <p>
                <label>Is the frame an optional add-on?</label>
                <span>
                    <label for="frame-optional-true">YES </label><input type="radio" id="frame-optional-true" name="frame-optional" value="true" />
                    <label for="frame-optional-false">NO </label><input type="radio" id="frame-optional-false" name="frame-optional" value="false" />
                </span>
            </p>
            <p id="dimensions-with-frame">
                <label>Dimensions with frame</label>
                <span class="wrap" style="display: inline-block; margin: 0; width: 60%">
                    <input type="text" id="_length" name="dimensions_with_frame['length']" placeholder="Length" />
                    <input type="text" name="$dimensions_with_frame['width']" placeholder="Width" />
                    <input type="text" name="$dimensions_with_frame['height']" placeholder="Height" />
                </span>
            </p>
            <p>
                <label for="weight-with-frame">Weight with frame (lbs)</label><span><input type="text" id="weight-with-frame" name="weight_with_frame" /></span>
            </p>
            <p id="dimensions-without-frame">
                <label>Dimensions without frame</label>
                <span class="wrap" style="display: inline-block; margin: 0; width: 60%">
                    <input type="text" id="_length" name="dimensions_without_frame['length']" placeholder="Length" />
                    <input type="text" name="dimensions_without_frame['width']" placeholder="Width" />
                    <input type="text" name="dimensions_without_frame['height']" placeholder="Height" />
                </span>
            </p>
            <p>
                <label for="weight-without-frame">Weight without frame (lbs)</label><span><input type="text" id="weight-without-frame" name="weight_without_frame" /></span>
            </p>
            <p>
                <label for="not-for-sale">Not for sale</label><span><input type="checkbox" id="not-for-sale" name="not_for_sale" /></span>
            </p>
            <div id="for-sale-wrap">
                <p>
                    <label for="price">Price</label>
                    <span style="position:relative"><span class="currency-symbol">$</span><input type="text" id="price" class="price" name="artwork_price" placeholder="Price of artwork" /></span>
                    <span style="position:relative"><span class="currency-symbol">$</span><input type="text" class="price" name="frame_price" placeholder="Price of frame" /></span>
                    <div id="profit-calculator" style="display: none; margin-top: 15px; padding-left: 35%; padding-right: 5%">
                        <input type="hidden" id="commission-rate" value="<?php echo esc_attr(get_option("jc_commission_rate")); ?>" >
                        <table style="text-align: left; border-top: dashed gray 1px; width: 100%; ">
                            <tr>
                                <th>Commission Fee (<?php echo get_option("commission_rate")*100; ?> %): </th><td id="commission-fee" style="text-align: left; width: 30%"></td>
                            </tr>
                            <tr>
                                <th>You Will Get: </th><td id="profit" style="text-decoration: underline; color: red; text-align: left"></td>
                            </tr>
                        </table>

                    </div>
                </p>
                <p>
                    <label for="stock">Inventory <span>(Enter -1 for unlimited)</span></label><span data-required=true><input type="number" id="stock" name="stock" value="1" /></span>
                </p>
            </div>
            <p>
                <label for="description">Description</label><span data-required=true style="display: inline-block; width: 60%"><textarea id="description" name="description" style="width: 100%; min-height: 100px"></textarea></span>
            </p>
        </section>
        <section id="ship-from">
            <h4>Will Ship From: </h4>
            <p class="description">This address will be used together with the dimensions and weight to estimate shipping cost.</p>
            <div style="max-width: 400px">
                <div>
                    <label for="address-1" class="col-half">Address 1</label> <label for="address-2" class="col-half">Address 2</label>
                    <input type="text" class="col-half" id="address-1" name="address_1" />
                    <input type="text" class="col-half" id="address-2" name="address_2" />
                </div>

                <div>
                    <label for="city" class="col-full">City</label>
                    <input type="text" class="col-full" id="city" name="city" />
                </div>

                <div>
                    <label for="state" class="col-half">State</label>
                    <label for="postcode" class="col-half">Postcode</label>
                    <input type="text" class="col-half" id="state" name="state" />
                    <input type="text" class="col-half" id="postcode" name="postcode" />
                </div>
            </div>
        </section>

        <style>
            #artwork-data p>label {
                display: inline-block;
                width: 30%;
                padding-right: 5%;
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
            .currency-symbol {
                color: gray;
                font-size: larger;
                position: absolute;
                top: -4px;
                left: 5px;
            }
            #for-sale-wrap input.price {
                padding-left: 14px;
            }
            section:not(:last-of-type) {
                margin-bottom: 50px;
            }
            .col-full {
                display: block;
                width: 100%;
            }
            .col-half {
                display: inline-block;
                width: 48%;
                margin: 0;
            }
            .col-half:first-of-type {
                margin-right: 3%;
            }
        </style>
        <script type="text/javascript">
            var not_for_sale = document.getElementById('not-for-sale');
            var wrap = document.getElementById('for-sale-wrap');
            var ship_from = document.getElementById('ship-from');
            if (not_for_sale.checked) {
                wrap.style.display = 'none';
                ship_from.style.display = 'none';
            } else {
                wrap.style.display = 'block';
                ship_from.style.display = 'block';
            }
            not_for_sale.addEventListener('change', function(e){
                if (this.checked) {
                    wrap.style.display = 'none';
                    ship_from.style.display = 'none';
                } else {
                    wrap.style.display = 'block';
                    ship_from.style.display = 'block';
                }
            }, true);
        </script>
        <?php
    }

    public static function save($post_id, $post){
    }
}
