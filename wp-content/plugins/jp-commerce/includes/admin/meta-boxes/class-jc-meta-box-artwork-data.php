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
                wp_enqueue_style ('bootstrap');
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
                <label for="_length">Dimensions (in)</label>
            <span class="wrap" data-required="true" style="display: inline-block; margin: 0; width: 60%">
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
                    <label>Does this item have a frame?</label>
                    <span>
                        <label for="with-frame">YES</label><input type="radio" id="with-frame" name="has-frame" value="true" />
                        <label for="no-frame">NO</label><input type="radio" id="no-frame" name="has-frame" value="false" />
                    </span>
                </p>
                <p>
                    <label>Is the frame an optional add-on?</label>
                    <span>
                        <label for="frame-optional-true">YES</label><input type="radio" id="frame-optional-true" name="frame-optional" value="true" />
                        <label for="frame-optional-false">NO</label><input type="radio" id="frame-optional-false" name="frame-optional" value="false" />
                    </span>
                </p>
                <p>
                    <label for="price">Price</label><span data-required=true style="position:relative"><span class="currency-symbol" style="color: gray; font-size: larger; position:absolute; top: -4px; left: 5px;">$</span><input type="text" id="price" name="price" style="padding-left: 14px; -webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;" /></span>
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

            #for-sale-wrap p>label {
                padding-left: 2%;

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
