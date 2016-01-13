<?php
/**
 * Created by PhpStorm.
 * User: jjpro
 * Date: 1/4/16
 * Time: 2:55 AM
 */
include_once( 'inc/cleanup.php' );
include_once( 'inc/lib.php' );
include_once( 'inc/template-functions.php' );

// enqueue scripts
add_action('wp_enqueue_scripts', 'jc_enqueue_scripts');

// menus, hide admin bar,
add_action( 'after_setup_theme', 'jc_theme_setup' );