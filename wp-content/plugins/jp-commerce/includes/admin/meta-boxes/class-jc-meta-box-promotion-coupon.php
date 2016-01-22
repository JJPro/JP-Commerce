<?php
/**
 * Promotion Coupon
 *
 * Displays promotion coupon meta boxes
 *
 * @class       JC_Meta_Box_Promotion_Coupon
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JC_Meta_Box_Promotion_Coupon 
{
    public static function output($post){

        wp_nonce_field( 'jc_save_data', 'jc_meta_nonce' );

        $promo = JC_Promotion::instance($post);
        $coupon_code = $promo->coupon_code;
        $coupon_rate = $promo->coupon_rate;

        ?>
        <table>
            <tr>
                <th>Coupon Rate:</th>
                <td>
                    <input type="number" name="coupon-rate" value="<?php echo esc_attr($coupon_rate * 100); ?>" maxlength="5" size="5"> <span>% OFF</span>
                </td>
            </tr>
            <tr>
                <th>Coupon Code:</th>
                <td>
                    <input type="text" name="coupon-code" value="<?php echo esc_attr($coupon_code); ?>" maxlength="20" size="22" style="text-transform: uppercase">
                </td>
            </tr>
        </table>

        <style type="text/css">
            input[name='coupon-rate'] {
                width: 70%;
            }
            input[name='coupon-rate']+span {
                width: 25%;
                display: inline-block;
            }
        </style>
        <?php
    }

    public static function save($post_id, $post){
        $nonce = $_POST['jc_meta_nonce'];
        if ( ! wp_verify_nonce($nonce, 'jc_save_data') )
            return;

        $code = isset($_POST['coupon-code']) ? $_POST['coupon-code'] : '';
        $rate = isset($_POST['coupon-rate']) ? $_POST['coupon-rate'] : 0;

        $promo = JC_Promotion::instance($post_id);
        $promo->coupon_code = $code;
        $promo->coupon_rate = $rate / 100;
    }
}