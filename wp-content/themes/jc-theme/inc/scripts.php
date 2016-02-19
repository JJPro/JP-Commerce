<?php
/**
 * Managing scripts
 */

add_action('wp_enqueue_scripts', 'jc_enqueue_scripts');
function jc_enqueue_scripts() {
    // wordpress dashicons
    wp_enqueue_style( 'dashicons' );

    // font-awesome
    wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/fonts/font-awesome-4.5.0/css/font-awesome.min.css');

    // application style
    wp_enqueue_style( 'application_style', get_template_directory_uri() . '/style.css' );

    // bootstrap
    wp_enqueue_script( 'bootstrap' );
    
    // theme js
    wp_enqueue_script( 'jc-theme', get_template_directory_uri() . '/js/script.min.js', array('jquery-core', 'bootstrap'), '1.0', true );
}