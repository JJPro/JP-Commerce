<?php
/**
 * Created by PhpStorm.
 * User: jjpro
 * Date: 1/4/16
 * Time: 2:55 AM
 */
include_once( 'inc/cleanup.php' );
include_once( 'inc/scripts.php' );
include_once( 'inc/lib.php' );
include_once( 'inc/template-functions.php' );

include_once( 'inc/pages.php' );



// menus, hide admin bar,
add_action( 'after_setup_theme', 'jc_theme_setup' );