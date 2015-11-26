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
        global $post, $thepostid;

        wp_nonce_field( 'jc_save_data', 'jc_meta_nonce' );

        $thepostid = $post->ID;

        ?>

        <?php
    }

    public static function save($post_id, $post){
    }
}
