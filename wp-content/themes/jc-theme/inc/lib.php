<?php
/**
 * Utility functions
 */


function jc_theme_setup() {
    nav_menus();

    hide_admin_bar_from_artists();

}

function nav_menus() {
    add_theme_support('menus');

    register_nav_menu( 'primary', 'Primary Menu' );
    register_nav_menu( 'footer', 'Footer Menu' );
}

function hide_admin_bar_from_artists() {
    if (current_user_can('artist')) {
        show_admin_bar(false);
    }
}



//
///**
// * TODO:
// * This function must be called by action "pre_get_posts"
// *
// * Sets the primary loop for featured, or new, or trending artworks
// *
// * @param $query This is provided by the "pre_get_posts" action
// * @param $for JC_PRIMARY_LOOP_FEATURED|JC_PRIMARY_LOOP_NEW|JC_PRIMARY_LOOP_TRENDING
// */
//if (!defined( 'JC_PRIMARY_LOOP_FEATURED') ) define( 'JC_PRIMARY_LOOP_FEATURED', 'featured' );
//if (!defined( 'JC_PRIMARY_LOOP_NEW') ) define( 'JC_PRIMARY_LOOP_NEW', 'new' );
//if (!defined( 'JC_PRIMARY_LOOP_TRENDING') ) define( 'JC_PRIMARY_LOOP_TRENDING', 'trending' );
//function reset_primary_loop($query, $for) {
//    $query->set( 'post_type', 'artwork' );
//}