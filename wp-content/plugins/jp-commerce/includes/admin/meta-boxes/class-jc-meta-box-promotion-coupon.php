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
        global $post, $thepostid;

        wp_nonce_field( 'jc_save_data', 'jc_meta_nonce' );

        $thepostid = $post->ID;

        // retrieve meta data
        $promo_coupon_code = get_post_meta($thepostid, '_promo_coupon_code', true);
        $promo_coupon_rate = get_post_meta($thepostid, '_promo_coupon_rate', true) * 100;

        ?>
        <table>
            <tr>
                <th>Coupon Rate:</th>
                <td>
                    <input type="text" name="_promo_coupon_rate" value="<?php echo esc_attr($promo_coupon_rate); ?>" maxlength="5" size="5"> <span>% OFF</span>
                </td>
            </tr>
            <tr>
                <th>Coupon Code:</th>
                <td>
                    <input type="text" name="_promo_coupon_code" value="<?php echo esc_attr($promo_coupon_code); ?>" maxlength="20" size="22" style="text-transform: uppercase">
                </td>
            </tr>
        </table>
        <?php
    }

    public static function save($post_id, $post){
        if (isset($_POST['_promo_coupon_code']))
        {
            update_post_meta($post_id, '_promo_coupon_code', sanitize_text_field($_POST['_promo_coupon_code']));
            update_post_meta($post_id, '_promo_coupon_rate', floatval($_POST['_promo_coupon_rate'] / 100));
        }
    }
}