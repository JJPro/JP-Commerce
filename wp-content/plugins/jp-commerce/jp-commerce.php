<?php
/**
 * Plugin Name: JP Commerce
 * Plugin URI: http://jjpro.net
 * Description: To enable e-commerce capabilities specifically designed for gallery commerce. It adds artists and customers, artworks and promotions; It adds tables to handle orders such as orders, order_details, and shipment tables.
 * Version: 1.0
 * Author: Lu Ji
 * Author URI: http://jjpro.net
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'JJProCommerce' ) ):

/**
 * Main JJProCommerce Class
 *
 * @class JJProCommerce
 * @version 1.0
 */
final class JJProCommerce {

    /**
     * @var string
     */
    public $version = '1.0';

    /**
     * @var JJProCommerce The single instance of the class
     */
    protected static $_instance = null;

    /**
     * @var JC_Session session.
     */
    public $session = null;

    /**
     * @var JC_Query query.
     */
    public $query = null;

    /**
     * @var JC_Cart cart.
     */
    public $cart = null;

    /**
     * @var JC_Customer customer
     */
    public $customer = null;

    /**
     * @var Logger logger
     */
    public $logger = null;

    /**
     * Main JJProCommerce Instance
     *
     * Ensures only one instance of JJProCommerce is loaded
     *
     * @static
     * @see WC()
     * @return JJProCommerce - Main instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Lazy-load some properties on demand - mailer, checkout.
     * @param mixed $key
     * @return mixed
     */
    public function __get( $key ) {
        if ( in_array( $key, array( 'mailer', 'checkout' ) ) ) {
            return $this->$key();
        }
    }

    /**
     * JJProCommerce Constructor
     */
    public function __construct()
    {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();

        do_action( 'jp_commerce loaded' );
    }

    /**
     * Hook into actions and filters
     */
    private function init_hooks() {
        register_activation_hook( __FILE__, array( 'JC_Install', 'install' ) );
        add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
        add_action( 'init', array( $this, 'init' ), 0 );

//        add_action( 'init', array( 'JC_Emails', 'init_transactional_emails' ) );
        /* 
            TODO - build JC_Emails class file
            @author - Jason
            @date   - 11/22/15
            @time   - 12:01 AM
        */
    }

    /**
     * Define JC Constants
     */
    private function define_constants() {
        $upload_dir = wp_upload_dir();

        $this->define( 'JC_VERSION', $this->version );
        $this->define( 'JC_LOG_DIR', $upload_dir['basedir'] . '/jc-logs/' );
        $this->define('JC_ADMIN', 'includes/admin');
        $this->define('JC_META_BOXES', JC_ADMIN . '/meta-boxes');
        $this->define( 'JC_PLUGIN_DIR_URL', plugin_dir_url(__FILE__) );
        $this->define( 'JC_DEBUG', true );
    }

    /**
     * Define constant if not already set
     * @param string $name
     * @param string|bool $value
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    /**
     * What type of request is this?
     * string $type ajax, frontend, cron or admin
     * @return bool
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
                return is_admin();
            case 'ajax' :
                return defined( 'DOING_AJAX' );
            case 'cron' :
                return defined( 'DOING_CRON' );
            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {
        // SHARED MODULES
        include_once( 'includes/libs/Logger.php' );
        include_once( 'includes/jc-core-functions.php' );
        include_once( 'includes/class-jc-user-roles.php' );
        include_once( 'includes/class-jc-post-types.php' );                     // Registers post types
        include_once( 'includes/class-jc-order.php' );
        include_once( 'includes/class-jc-install.php' );
        include_once( 'includes/class-jc-ajax.php' );
        include_once( 'includes/admin-bar.php' );
        // Registration functions for all scripts in JP Commerce.
        // You have to call the respective class functions to register scripts in either admin or frontend.
        include_once( 'includes/class-jc-scripts.php' );
        include_once( 'includes/class-jc-artwork.php' );



        // ADMIN MODULES
        if ( $this->is_request( 'admin' ) ) {
            include_once( 'includes/admin/class-jc-admin.php' );

        }

        // FRONTEND MODULES
        if ( $this->is_request( 'frontend' ) ) {
            include_once( 'includes/class-jc-frontend.php' );
        }

        // CRON MODULES
        if ( $this->is_request( 'frontend' ) || $this->is_request( 'cron' ) ) {

        }

//        $this->query = include( 'includes/class-jc-query.php' );                // The main query class

    }



    /**
     * Get Checkout Class.
     * @return JC_Checkout
     */
    public function checkout() {
//        return JC_Checkout::instance();
    }

    /**
     * Email Class.
     * @return JC_Emails
     */
    public function mailer() {
//        include_once( 'includes/class-jc-emails.php' );
//        return JC_Emails::instance();
    }

    /**
     * Function used to Init JJProCommerce Template Functions - This makes them pluggable by plugins and themes.
     */
    public function include_template_functions() {
        include_once( 'includes/jc-template-functions.php' );
    }
    
    /**
     * Init JPCommerce when WordPress Initialises.
     */
    public function init() {
        /* 
            TODO - Finish this method
            @author - Jason
            @date   - 11/22/15
            @time   - 4:44 PM
        */
        // Before init action
        do_action( 'before_jp_commerce_init' );

        // register user roles
        $this->create_roles();
        
        // Load class instances


        // Classes/actions loaded for the frontend and for ajax requests
        if ( $this->is_request('frontend')) {
//            $this->cart     = new JC_Cart();
//            $this->customer = new JC_Customer();
        }
        
        // Init action
        do_action( 'jp_commerce_init' );
    }

    /**
     * Get Ajax URL.
     * @return string
     */
    public function ajax_url() {
        return admin_url( 'admin-ajax.php' );
    }

    /**
     * Modify User Roles and Capabilities
     */
    public function create_roles() {
        global $logger;
        $logger->log_action("creating roles");
        JC_User_Roles::init();
    }
}

endif;

new JJProCommerce();

/**
 * Returns the main instance of JC to prevent the need to use globals.
 *
 * @since 1.0
 * @return JJProCommerce
 */
function JC() {
    return JJProCommerce::instance();
}

