<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * TODO:
 *
 * Sets up pages and the loop
 *
 * @author      JJPRO Technologies LLC.
 */

add_action( 'pre_get_posts', 'jc_update_query' );
function jc_update_query( $query ) {
    // the main query on blog page
    if ( $query->is_home() && $query->is_main_query() ) {
        // reset the primary loop depends on the current page.
        echo 'haha';
    }
}