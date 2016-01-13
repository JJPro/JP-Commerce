<?php
/**
 * JC Frontend
 *
 * Everything specifically on the frontend starts here.
 *
 * @class       JC_Frontend
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JC_Frontend 
{
    public function __construct() {
        add_action( 'init', array( $this, 'includes' ) );

        // Register frontend scripts and styles
        JC_Scripts::register_frontend_scripts();

        // Load the commonly needed scripts/styles on most frontend pages
        $this->enqueue_common_scripts();
    }


    /**
     * Include required frontend files.
     */
    public function includes() {
//        include_once( 'includes/jc-template-hooks.php' );
//        include_once( 'includes/class-jc-frontend-scripts.php' );               // Frontend Scripts
//        include_once( 'includes/class-jc-cart.php' );                           // The main cart class
//        include_once( 'includes/class-jc-https.php' );                          // https Helper
    }

    /**
     * Enqueues commonly used scripts on most frontend pages.
     */
    private function enqueue_common_scripts() {
        add_action('wp_enqueue_scripts', function() {

            wp_enqueue_script( 'tiptip' );
            wp_enqueue_style ( 'tiptip' );
            wp_enqueue_script( 'bootstrap' );
            wp_enqueue_style ( 'bootstrap' );
            wp_enqueue_style ( 'jc-font' );


        });
    }
}

new JC_Frontend();