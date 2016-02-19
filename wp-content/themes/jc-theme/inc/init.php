<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Initilizing work
 *
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */
add_action( 'after_switch_theme', 'insert_pages' );
function insert_pages() {
	
	// the front page
	if ( ! get_page_by_title('home', 'OBJECT', 'page') ) {
		// insert the page only once.
        $args = array(
            'post_title' => 'home',
            'post_name'  => 'home', // slug
            'post_type' => 'page',
            'post_status' => 'publish',
            'comment_status' => 'closed',
            'page_template' => "home.php",
        );
        wp_insert_post($args);
    }
        	
    // other required pages
    $pages = ['cart', 'account', 'orders', 'favorites', 'signin'];

    foreach( $pages as $page ){
        // check if pages exists

        if ( ! get_page_by_title($page, 'OBJECT', 'page') ) {
            // insert them if not
            $args = array(
                'post_title' => $page,
                'post_name'  => $page, // slug
                'post_type' => 'page',
                'post_status' => 'publish',
                'comment_status' => 'closed',
                'page_template' => "templates/$page.php",
            );
            wp_insert_post($args);
        }
    }
}