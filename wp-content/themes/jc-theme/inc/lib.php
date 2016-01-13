<?php
/**
 * User: jjpro
 * Date: 1/5/16
 * Time: 4:08 PM
 */
function jc_enqueue_scripts() {
    wp_enqueue_style( 'dashicons' );
    wp_enqueue_style( 'template_style', get_template_directory_uri() . '/style.css' );
}

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