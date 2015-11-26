<?php
/**
 * Installation related functions and actions.
 *
 * @author   JJPRO Technologies LLC.
 * @version  1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JC_Install Class
 */
class JC_Install {

    public static function init() {
        add_filter( 'cron_schedules', array( __CLASS__, 'cron_schedules' ) );
    }

    public static function install() {
        global $wpdb;

        if ( ! defined( 'JC_INSTALLING' ) ) {
            define( 'JC_INSTALLING', true );
        }

        global $logger;
        $logger->log_action("install method is called");


        self::create_options();
        self::create_tables();
        self::create_roles();

        // register post types and taxonomies before calling create_terms()
        JC_Post_Types::register_post_types();
        JC_Post_Types::register_taxonomies();

        self::create_terms();
        self::create_cron_jobs();
        self::create_files();

        do_action( 'jp_commerce_installed' );
    }

    /**
     * Default options
     *
     * Sets up the default options used on the settings page
     */
    private static function create_options() {

    }

    /**
     * Default Terms
     *
     * Sets up the default terms for artwork types and promotion types
     */
    private static function create_terms() {
        // initialize with 3 artwork types
        wp_insert_term('Paintings', 'artwork_type', array('slug' => 'painting'));
        wp_insert_term('Sculptures', 'artwork_type', array('slug' => 'sculpture'));
        wp_insert_term('Photographs', 'artwork_type', array('slug' => 'photograph'));

        // There are only two kinds of promotion types and are not editable: (actually only editable by superadmin )
        wp_insert_term('Banner Promotion', 'promotion_type', array('slug' => 'banner'));
        wp_insert_term('Pop-up Promotion', 'promotion_type', array('slug' => 'popup'));
    }

    /**
     * Create Files
     *
     *
     */
    private static function create_files() {

    }

    /**
     * Set up the database tables
     *
     * Tables:
     *      orders
     *      order_details
     *      shipment
     */
    private static function create_tables() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'orders';
        $sqls = array("CREATE TABLE {$table_name } (
            id INT NOT NULL AUTO_INCREMENT,
            customer INT NOT NULL,
            billing_address VARCHAR(5000),
            shipping_address VARCHAR(5000),
            order_datetime TIMESTAMP NOT NULL,
            order_status VARCHAR(10),
            PRIMARY KEY (id),
            INDEX (customer),
            INDEX (order_status));");
        $table_name = $wpdb->prefix . 'order_details';
        $sqls[] = "CREATE TABLE {$table_name} (
            id INT NOT NULL AUTO_INCREMENT,
            item INT NOT NULL,
            `order` INT NOT NULL,
            quantity INT NOT NULL,
            PRIMARY KEY (id),
            INDEX(`order`),
            INDEX(item));";
        $table_name = $wpdb->prefix . 'shipments';
        $sqls[] = "CREATE TABLE {$table_name} (
            id INT NOT NULL AUTO_INCREMENT,
            `order` INT NOT NULL,
            ship_datetime DATETIME NOT NULL,
            shipping_status VARCHAR(10),
            tracking_number VARCHAR(50),
            PRIMARY KEY (id),
            INDEX(`order`));";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sqls);
        // save the db version, helpful in determining versions and upgrading table structures in the future.
        $jc_db_version = '1.0';
        add_option('jp_commerce_db_version', $jc_db_version);
    }

    /**
     * Modify User Roles and Capabilities
     */
    public static function create_roles() {
        JC_User_Roles::init();
    }

    /**
     * Add more cron schedules
     * @param  array $schedules
     * @return array
     */
    public static function cron_schedules( $schedules ) {

        return $schedules;
    }

    /**
     * Create cron jobs (clear them first)
     */
    private static function create_cron_jobs() {

    }

}

JC_Install::init();