<?php
/**
 * JPCommerce Meta Boxes
 *
 * Sets up the write panels used by artworks and orders (custom post types)
 *
 * @class       JC_Admin_Meta_Boxes
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

require_once( 'meta-boxes/class-jc-meta-box-artwork-media.php' );
require_once( 'meta-boxes/class-jc-meta-box-artwork-data.php' );
require_once( 'meta-boxes/class-jc-meta-box-artwork-tag.php' );
require_once( 'meta-boxes/class-jc-meta-box-artwork-submit.php' );
require_once( 'meta-boxes/class-jc-meta-box-promotion-coupon.php' );


class JC_Admin_Meta_Boxes 
{

    public function __construct()
    {
        add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
        add_action( 'add_meta_boxes', array( $this, 'rename_meta_boxes' ), 20 );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );

        // Save Meta Boxes
        // Preparation for saving:
        add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

        // Save Promotion Meta Boxes
        add_action( 'jc_process_promotion_meta', 'JC_Meta_Box_Promotion_Coupon::save', 10, 2 );

        // Save Artwork Meta Boxes
        add_action( 'jc_process_artwork_meta', 'JC_Meta_Box_Artwork_Data::save', 10, 2 );

        // Save Order Meta Boxes

    }

    public function remove_meta_boxes(){
        remove_meta_box('submitdiv', 'artwork', 'normal');
        remove_meta_box('submitdiv', 'artwork', 'side');
        remove_meta_box('artwork_typediv', 'artwork', 'side');
        remove_meta_box('tagsdiv-artwork_tag', 'artwork', 'side');
    }

    public function rename_meta_boxes(){

    }

    public function add_meta_boxes(){
        // Artworks
        JC_Meta_Box_Artwork_Media::init();
        JC_Meta_Box_Artwork_Type::enqueue_scripts();
        JC_Meta_Box_Artwork_Data::init();
        add_meta_box('artwork_typediv', __('Artwork Type'),
            wp_is_mobile() ? 'post_categories_meta_box' : 'JC_Meta_Box_Artwork_Type::output'
            , 'artwork', 'normal', 'high');
        add_meta_box('artwork-media', 'Pictures', 'JC_Meta_Box_Artwork_Media::output', 'artwork', 'side', 'default');
        add_meta_box('artwork-data', 'Artwork Information', 'JC_Meta_Box_Artwork_Data::output', 'artwork', 'normal', 'default');
        add_meta_box('tagsdiv-artwork_tag', 'Artwork Tags', 'JC_Meta_Box_Artwork_Tag::output', 'artwork', 'normal', 'default', array( 'taxonomy' => 'artwork_tag' ));
        add_meta_box('artwork-submit', 'Submit', 'JC_Meta_Box_Artwork_Submit::output', 'artwork', 'normal', 'default');

        // Promotions
        add_meta_box('coupondiv', 'Set Coupon', 'JC_Meta_Box_Promotion_Coupon::output', 'promotion', 'side', 'high');

        // Orders
    }

    /**
     * Check if we're saving, and trigger an action based on the post type
     *
     * @param int $post_id
     * @param object $post
     */
    public function save_meta_boxes( $post_id, $post ) {
        // $post_id and $post are required
        if ( empty( $post_id ) || empty( $post ) ){
            return;
        }

        // Don't save meta boxes for revisions or autosaves
        if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
            return;
        }

        // Check nonce
        if ( empty( $_POST['jc_meta_nonce'] ) || ! wp_verify_nonce( $_POST['jc_meta_nonce'], 'jc_save_data' ) ) {
            return;
        }

        // Check the post being saved == the $post_id to prevent triggering this call for other save_post events
        if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
            return;
        }

        // Check user has permission to edit
        if ( ! current_user_can( "edit_{$post->post_type}s" ) || ! current_user_can( "manage_{$post->post_type}s" )) {
            return;
        }

        // Check the post type
        if ( in_array( $post->post_type, array('order', 'promotion', 'artwork')) ) {
            do_action( "jc_process_{$post->post_type}_meta", $post_id, $post );
        }
    }
}

new JC_Admin_Meta_Boxes();