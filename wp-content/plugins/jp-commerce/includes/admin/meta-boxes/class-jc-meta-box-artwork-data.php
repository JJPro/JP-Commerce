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
//    private static $errors = array();

    public static function init() {
//        session_start();
        self::enqueue_scripts();
        JC_Artwork_Materials_Field::init();
//        self::print_errors();
    }

    private static function enqueue_scripts() {
        add_action("admin_enqueue_scripts",
            function()
            {
                wp_enqueue_script('meta-boxes-artwork-data', JC_PLUGIN_DIR_URL . 'js/admin/meta-boxes-artwork-data.min.js', ['tiptip', 'jquery-ui-spinner', 'jc-shared']);
                wp_enqueue_style ('jquery-ui');
            }
        );
    }

    public static function output($post){
        wp_nonce_field( 'jc_save_data', 'jc_meta_nonce' );




        $artwork = JC_Artwork::instance($post);

        $dateCreated = $artwork->date_created;
        $dateCreated = empty($dateCreated) ? date_i18n( 'Y', time() ) : $dateCreated;

        ?>
        <div id="artwork-info-notices"></div>
        <section id="artwork-detail">
            <div id="general-detail">

                <h4>Artwork Detail:</h4>
                <p>
                    <label <?php artwork_field_required($artwork, 'name'); ?> for="name">Name</label>
                    <input <?php artwork_field_required($artwork, 'name'); ?> id="name" name="post_title" value="<?php echo $artwork->name; ?>" placeholder="Enter Name Here"/>

                </p>
                <p>
                    <label <?php artwork_field_required($artwork, 'description'); ?> for="description">Description</label>
                    <textarea <?php artwork_field_required($artwork, 'description'); ?> id="description" name="description" style="width: 65%; min-height: 100px"><?php echo esc_textarea($artwork->description); ?></textarea>
                </p>
                <p>
                    <label <?php artwork_field_required($artwork, 'date_created'); ?> for="make-date">Year Created</label>
                    <input <?php artwork_field_required($artwork, 'date_created'); ?> id="make-date" name="make-date" max="<?php echo date_i18n( 'Y', time() ); ?>" value="<?php echo $dateCreated; ?>"/>
                </p>
                <p>
                    <label <?php artwork_field_required($artwork, 'artwork_type'); ?> for="artwork-category">Category</label>
                    <?php artwork_category_field($artwork); ?>
                </p>
                <p>
                    <label <?php artwork_field_required($artwork, 'materials'); ?> for="materails">Materials</label>
                    <?php artwork_materials_field($artwork); ?>
                </p>
                <p>
                    <label <?php artwork_field_required($artwork, 'dimensions'); ?> for="dimensions-width">Dimensions</label>
                    <span class="dimensions">
                        <input type="number" name="dimensions[width]" id="dimensions-width" placeholder="Width" value="<?php echo $artwork->dimensions->width; ?>"/>
                        <input type="number" name="dimensions[height]" id="dimensions-height" placeholder="Height" value="<?php echo $artwork->dimensions->height; ?>"/>
                        <input type="number" name="dimensions[depth]" id="dimensions-depth" placeholder="Depth" value="<?php echo $artwork->dimensions->depth; ?>"/>
                        <select name="dimensions[unit]">
                            <option value="<?php echo ARTWORK_DIMENSION_UNIT_INCH; ?>" <?php selected($artwork->dimensions->unit, ARTWORK_DIMENSION_UNIT_INCH); ?>>Inches</option>
                            <option value="<?php echo ARTWORK_DIMENSION_UNIT_CM; ?>" <?php selected($artwork->dimensions->unit, ARTWORK_DIMENSION_UNIT_CM); ?>>cm</option>
                        </select>
                    </span>
                </p>
            </div> <!-- #general-detail -->

            <hr>

            <div id="for-sale-detail">
                <h4>Artwork Status</h4>

                <div class="status-div">
                    <p><input type="radio" name="status" id='status-1' value="1" <?php checked($artwork->is_for_sale(), true); ?> /> <label for="status-1"><span><span></span></span>For Sale</label></p>
                    <p><input type="radio" name="status" id='status-2' value="0" <?php checked($artwork->is_for_sale(), false); ?> /> <label for="status-2"><span><span></span></span>Not For Sale</label></p>
                </div>
                <div id="for-sale-div">
                    <p>
                        <label for="stock" <?php artwork_field_required($artwork, 'stock'); ?>>Inventory <span>(Enter -1 for unlimited)</span></label><input type="number" id="stock" name="stock" value="<?php echo $artwork->stock; ?>" placeholder="1" />
                    </p>
                    <p>
                        <label for="shipping-dimensions[width]" <?php artwork_field_required($artwork, 'shipping_dimensions'); ?>>Shipping Dimensions</label>
                        <span class="dimensions">
                            <input type="number" name="shipping-dimensions[width]" id="shipping-dimensions-width" placeholder="Width" value="<?php echo $artwork->shipping_dimensions->width; ?>"/>
                            <input type="number" name="shipping-dimensions[height]" id="shipping-dimensions-height" placeholder="Height" value="<?php echo $artwork->shipping_dimensions->height; ?>"/>
                            <input type="number" name="shipping-dimensions[depth]" id="shipping-dimensions-depth" placeholder="Depth" value="<?php echo $artwork->shipping_dimensions->depth; ?>"/>
                            <select name="shipping-dimensions[unit]">
                                <option value="<?php echo ARTWORK_DIMENSION_UNIT_INCH; ?>" <?php selected($artwork->shipping_dimensions->unit, ARTWORK_DIMENSION_UNIT_INCH); ?>>Inches</option>
                                <option value="<?php echo ARTWORK_DIMENSION_UNIT_CM; ?>" <?php selected($artwork->shipping_dimensions->unit, ARTWORK_DIMENSION_UNIT_CM); ?>>cm</option>
                            </select>
                        </span>
                    </p>
                    <p>
                        <label for="shipping-weight" <?php artwork_field_required($artwork, 'shipping_weight'); ?>>Shipping Weight</label>
                        <span>
                            <input type="number" name="shipping-weight" id="shipping-weight" value="<?php echo $artwork->shipping_weight; ?>">
                            <select name="shipping-weight-unit">
                                <option value="<?php echo JC_SHIPPING_WEIGHT_UNIT_POUNDS; ?>" <?php selected(JC_SHIPPING_WEIGHT_UNIT_POUNDS, $artwork->shipping_weight_unit); ?>>Pounds</option>
                                <option value="<?php echo JC_SHIPPING_WEIGHT_UNIT_KG; ?>" <?php selected(JC_SHIPPING_WEIGHT_UNIT_KG, $artwork->shipping_weight_unit); ?>>Kg</option>
                            </select>
                        </span>
                    </p>
                    <p>
                        <label for="price" <?php artwork_field_required($artwork, 'price'); ?>>Price</label>
                        <span style="display: inline-block; margin-left: -4px;width: 60%;">
                            <span style="position:relative; display: inline-block"><span class="currency-symbol">$</span><input type="number" id="price" class="price" name="artwork_price" placeholder="Price of artwork" value="<?php echo $artwork->price_of_artwork; ?>"/></span>
                        </span>
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

                    <section id="ship-from">
                        <h4>Will Ship From: <span style="color: darkgrey">*</span></h4>
                        <?php $shipping_from = $artwork->shipping_from; ?>
                        <p class="description">This address will be used together with the dimensions and weight to estimate shipping cost for the buyer.</p>
                        <div style="max-width: 400px">
                            <div>
                                <label for="address-1" class="col-half">Address 1 *</label>
                                <label for="address-2" class="col-half">Address 2</label>
                                <input type="text" class="col-half" id="address-1" name="shipping_from[address_1]" value="<?php if ($shipping_from) echo $shipping_from->address1; ?>"/>
                                <input type="text" class="col-half" id="address-2" name="shipping_from[address_2]" value="<?php if ($shipping_from) echo $shipping_from->address2; ?>"/>
                            </div>

                            <div>
                                <label for="city" class="col-half">City *</label>
                                <label for="state" class="col-half">State *</label>
                                <input type="text" class="col-half" id="city" name="shipping_from[city]" value="<?php if ($shipping_from) echo $shipping_from->city; ?>"/>
                                <input type="text" class="col-half" id="state" name="shipping_from[state]" value="<?php if ($shipping_from) echo $shipping_from->state; ?>"/>
                            </div>

                            <div>
                                <label for="country" class="col-half">Country *</label>
                                <label for="postcode" class="col-half">Postcode *</label>
                                <input type="text" class="col-half" id="country" name="shipping_from[country]" value="<?php if ($shipping_from) echo $shipping_from->country; ?>"/>
                                <input type="text" class="col-half" id="postcode" name="shipping_from[postcode]" value="<?php if ($shipping_from) echo $shipping_from->zip; ?>"/>
                            </div>

                            <div>
                                <label for="phone" class="col-full">Phone Number *</label>
                                <input type="text" class="col-full" id="phone" name="shipping_from[phone]" value="<?php if ($shipping_from) echo $shipping_from->phone; ?>" />
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>

        <style>
            <?php // overwrite to close gap above artwork data div ?>
            #post-body-content {
                display: none;
            }
            #artwork-data p>label {
                display: inline-block;
                width: 30%;
                padding-right: 5%;
                vertical-align: top;
            }

            .currency-symbol {
                color: gray;
                font-size: larger;
                position: absolute;
                /*top: -4px;*/
                left: 5px;
                top: 50%;
                -webkit-transform: translateY(-50%);
                -moz-transform: translateY(-50%);
                -ms-transform: translateY(-50%);
                -o-transform: translateY(-50%);
                transform: translateY(-50%);
            }
            #for-sale-div {
                margin: auto 20px;
                padding-left: 10px;
                display: <?php echo $artwork->is_for_sale() ? 'block' : 'none'; ?>;
                border-top: 1px solid lightblue;
            }
            #for-sale-div input.price {
                padding-left: 14px;
            }
            section:not(:last-of-type) {
                margin-bottom: 50px;
            }
            .col-full {
                display: block;
                width: 98.5%;
            }
            .col-half {
                display: inline-block;
                width: 48%;
                margin: 0;
            }
            .col-half:first-of-type {
                margin-right: 2%;
            }
            span.dimensions {
                width: 69%;
                display: inline-block;
            }
            span.dimensions [name^='dimensions'],
            span.dimensions [name^='shipping-dimensions'] {
                width: 24%;
            }
            span.dimensions [name^='dimensions']:last-child,
            span.dimensions [name^='shipping-dimensions'],
            select[name='shipping-weight-unit'] {
                width: 85px;
                margin-bottom: 5px;
            }

            .status-div {
                margin-left: 20px;
            }
            .status-div p label{
                vertical-align: middle !important;
            }
            .status-div p {
                margin-top: 0;
                margin-bottom: 5px;
            }
            .status-div p:last-of-type {
                margin-bottom: 13px;
            }

            /* style the radio box */
            input[type=checkbox]:not(old),
            input[type=radio   ]:not(old){
                width     : 2em;
                margin    : 0;
                padding   : 0;
                font-size : 1em;
                opacity   : 0;
            }
            input[type=checkbox]:not(old) + label,
            input[type=radio   ]:not(old) + label{
                display      : inline-block;
                margin-left  : -2em;
                line-height  : 1.5em;
            }
            input[type=checkbox]:not(old) + label > span,
            input[type=radio   ]:not(old) + label > span{
                display          : inline-block;
                width            : 0.875em;
                height           : 0.875em;
                margin           : 0.25em 0.5em 0.25em 0.25em;
                border           : 0.0625em solid rgb(192,192,192);
                border-radius    : 0.25em;
                background       : rgb(224,224,224);
                background-image :    -moz-linear-gradient(rgb(240,240,240),rgb(224,224,224));
                background-image :     -ms-linear-gradient(rgb(240,240,240),rgb(224,224,224));
                background-image :      -o-linear-gradient(rgb(240,240,240),rgb(224,224,224));
                background-image : -webkit-linear-gradient(rgb(240,240,240),rgb(224,224,224));
                background-image :         linear-gradient(rgb(240,240,240),rgb(224,224,224));
                vertical-align   : bottom;
            }
            input[type=checkbox]:not(old):checked + label > span,
            input[type=radio   ]:not(old):checked + label > span{
                background-image :    -moz-linear-gradient(rgb(224,224,224),rgb(240,240,240));
                background-image :     -ms-linear-gradient(rgb(224,224,224),rgb(240,240,240));
                background-image :      -o-linear-gradient(rgb(224,224,224),rgb(240,240,240));
                background-image : -webkit-linear-gradient(rgb(224,224,224),rgb(240,240,240));
                background-image :         linear-gradient(rgb(224,224,224),rgb(240,240,240));
            }
            input[type=checkbox]:not(old):checked + label > span:before{
                content     : '✓';
                display     : block;
                width       : 1em;
                color       : rgb(153,204,102);
                font-size   : 0.875em;
                line-height : 1em;
                text-align  : center;
                text-shadow : 0 0 0.0714em rgb(115,153,77);
                font-weight : bold;
            }
            input[type=radio]:not(old):checked + label > span > span{
                display          : block;
                width            : 0.5em;
                height           : 0.5em;
                margin           : 0.125em;
                border           : 0.0625em solid rgb(115,153,77);
                border-radius    : 0.125em;
                background       : rgb(153,204,102);
                background-image :    -moz-linear-gradient(rgb(179,217,140),rgb(153,204,102));
                background-image :     -ms-linear-gradient(rgb(179,217,140),rgb(153,204,102));
                background-image :      -o-linear-gradient(rgb(179,217,140),rgb(153,204,102));
                background-image : -webkit-linear-gradient(rgb(179,217,140),rgb(153,204,102));
                background-image :         linear-gradient(rgb(179,217,140),rgb(153,204,102));
            }
            label[required]:after {
                content: '*';
                color: darkgrey;
                vertical-align: bottom;
                margin-left: 3px;
            }
        </style>
        <?php

    }

    public static function save($post_id, $post){
        $nonce = $_POST['jc_meta_nonce'];
        if ( ! wp_verify_nonce($nonce, 'jc_save_data') )
            return;


        $desc = $_POST['description'];
        $date_created = $_POST['make-date'];
        $artwork_type = $_POST['artwork-category'];
        $dimensions = $_POST['dimensions'];
        $status = $_POST['status'];

//        self::verify_general_required_fields();

        $artwork = JC_Artwork::instance($post_id);
        $artwork->description = $desc;
        $artwork->date_created = $date_created;
        $artwork->artwork_type = $artwork_type;
        JC_Artwork_Materials_Field::save($artwork);
        $artwork->set_dimensions($dimensions['width'], $dimensions['height'], $dimensions['depth'], $dimensions['unit']);
        $artwork->status = $status;


        if ($artwork->is_for_sale()) {

            $stock = $_POST['stock'];
            $shipping_dimensions = $_POST['shipping-dimensions'];
            $shipping_weight = $_POST['shipping-weight'];
            $shipping_weight_unit = $_POST['shipping-weight-unit'];
            $price = $_POST['artwork_price'];
            $from = $_POST['shipping_from'];

//            self::verify_for_sale_fields();


            $artwork->stock = $stock;
            $artwork->set_shipping_dimensions($shipping_dimensions['width'], $shipping_dimensions['height'],
                $shipping_dimensions['depth'], $shipping_dimensions['unit']);
            $artwork->shipping_weight = $shipping_weight;
            $artwork->shipping_weight_unit = $shipping_weight_unit;
            $artwork->price_of_artwork = $price;
            $artwork->set_shipping_from($from['address_1'], $from['address_2'],
                $from['city'], $from['state'], $from['country'], $from['postcode'], $from['phone']);
        }
    }

    private static function checked($val1, $val2) {
        if ($val1 === $val2)
            echo 'checked="checked"';
    }

//    private static function print_errors() {
//        session_start();
//        $errors = isset($_SESSION['artwork_data_errors']) ? $_SESSION['artwork_data_errors'] : array();
//
//        foreach ($errors as $error) {
//            jc_admin_error_notice($error, true);
//
//        }
//    }

//    private static function verify_general_required_fields() {
//
//        $required = ['description', 'date_created', 'artwork_type', 'materials', 'dimensions',];
//
//        $_SESSION['artwork_data_errors'] = array('kkkkkk');
//
//    }

//    private static function verify_for_sale_fields() {
//        $required = ['stock', 'shipping_dimensions', 'shipping_weight', 'price', 'shipping_from'];
//        $_SESSION['artwork_data_errors'] = array('ppppp');
//    }
}
