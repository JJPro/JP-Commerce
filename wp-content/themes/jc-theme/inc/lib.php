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

    register_nav_menu( 'header', 'Header Menu' );
    register_nav_menu( 'footer', 'Footer Menu' );
}

function hide_admin_bar_from_artists() {
    if (current_user_can('artist')) {
        show_admin_bar(false);
    }
}

function shopping_cart_items_count() {

}