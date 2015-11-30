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
        $this->$this->enqueue_common_scripts();
    }


    /**
     * Include required frontend files.
     */
    private function includes() {
//        include_once( 'includes/jc-template-hooks.php' );
//        include_once( 'includes/class-jc-frontend-scripts.php' );               // Frontend Scripts
//        include_once( 'includes/class-jc-cart.php' );                           // The main cart class
//        include_once( 'includes/class-jc-customer.php' );                       // Customer class
//        include_once( 'includes/class-jc-https.php' );                          // https Helper
    }

    /**
     * Enqueues commonly used scripts on most frontend pages.
     */
    private function enqueue_common_scripts() {
        add_action('wp_enqueue_scripts', function() {

            // tooltip jquery plugin
            wp_enqueue_script( 'tooltip' );
            wp_enqueue_style ( 'tooltip' );


        });
    }
}