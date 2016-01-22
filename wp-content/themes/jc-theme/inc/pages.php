<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Sets up pages and the loop
 *
 * @author      JJPRO Technologies LLC.
 */

add_action( 'pre_get_posts', 'jc_start_loop' );
function jc_start_loop( $query ) {
    if ( $query->is_home() && $query->is_main_query() ) {
        // start the
        echo "KKKK";
    }
    start_primary_loop($query, 'kk');
}