<?php

/**
 * TODO:
 * 1. Register Notifications & Emails section on options page
 *      1.1. jp_commerce_email_from_name
 *      1.2. jp_commerce_email_from_address
 */


/**
 * JC Emails
 *
 * Handles transactional emails/notifications
 *
 * @class       JC_Emails
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class JC_Emails
 *
 * Handles transactional notifications and emails
 */
class JC_Emails 
{
    /** @var JC_Emails The single instance of the class */
    private static $_instance = null;

    /** @var array Array of email notification classes */
    public $emails;

    /**
     * Main JC_Emails Instance
     *
     * Ensures only one instance of JC_Emails is loaded or can be loaded.
     *
     * @static
     * @return JC_Emails Main instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Hook in all transactional emails
     */
    public static function init_transactional_emails() {
        $email_actions = apply_filters( 'jp_commerce_email_actions', array(
            'jp_commerce_artwork_sold_out',
            'jp_commerce_order_status_pending_to_processing',
            'jp_commerce_order_status_pending_to_completed',
            'jp_commerce_order_status_pending_to_cancelled',
            'jp_commerce_order_status_pending_to_on-hold',
            'jp_commerce_order_status_failed_to_processing',
            'jp_commerce_order_status_failed_to_completed',
            'jp_commerce_order_status_on-hold_to_processing',
            'jp_commerce_order_status_on-hold_to_cancelled',
            'jp_commerce_order_status_completed',
            'jp_commerce_order_fully_refunded',
            'jp_commerce_order_partially_refunded',
            'jp_commerce_new_customer_note',
            'jp_commerce_created_customer'
        ) );

        foreach ( $email_actions as $action ) {
            add_action( $action, array( __CLASS__, 'send_transactional_email' ), 10, 10 );
        }
    }

    /**
     * Init the mailer instance and call the notifications for the current filter.
     * @internal param array $args (default: array())
     */
    public static function send_transactional_email() {
        self::instance();
        $args = func_get_args();
        do_action_ref_array( current_filter() . '_notification', $args );
    }

    /**
     * Constructor for the email class hooks in all emails that can be sent.
     */
    public function __construct()
    {
        $this->init();

        // Email Header, Footer and content hooks
        add_action( 'jp_commerce_email_header', array( $this, 'email_header' ) );
        add_action( 'jp_commerce_email_footer', array( $this, 'email_footer' ) );
        add_action( 'jp_commerce_email_order_meta', array( $this, 'order_meta' ), 10, 3 );
        add_action( 'jp_commerce_email_customer_details', array( $this, 'customer_details' ), 10, 3 );
        add_action( 'jp_commerce_email_customer_details', array( $this, 'email_addresses' ), 20, 3 );

        // Hooks for sending emails during store events
        add_action( 'jp_commerce_no_stock_notification', array( $this, 'no_stock' ) );
        add_action( 'jp_commerce_created_customer_notification', array( $this, 'customer_new_account' ), 10, 3 );
    }

    /**
     * Init email classes
     */
    public function init() {
        // Include email classes
        include_once( 'emails/class-wc-email.php' );

        $this->emails['WC_Email_New_Order']                 		= include( 'emails/class-wc-email-new-order.php' );
        $this->emails['WC_Email_Cancelled_Order']           		= include( 'emails/class-wc-email-cancelled-order.php' );
        $this->emails['WC_Email_Customer_Processing_Order'] 		= include( 'emails/class-wc-email-customer-processing-order.php' );
        $this->emails['WC_Email_Customer_Completed_Order']  		= include( 'emails/class-wc-email-customer-completed-order.php' );
        $this->emails['WC_Email_Customer_Refunded_Order']   		= include( 'emails/class-wc-email-customer-refunded-order.php' );
        $this->emails['WC_Email_Customer_Invoice']          		= include( 'emails/class-wc-email-customer-invoice.php' );
        $this->emails['WC_Email_Customer_Note']             		= include( 'emails/class-wc-email-customer-note.php' );
        $this->emails['WC_Email_Customer_Reset_Password']   		= include( 'emails/class-wc-email-customer-reset-password.php' );
        $this->emails['WC_Email_Customer_New_Account']      		= include( 'emails/class-wc-email-customer-new-account.php' );

        $this->emails = apply_filters( 'jp_commerce_email_classes', $this->emails );
    }

    /**
     * Return the email classes - used in admin to load settings.
     *
     * @return array
     */
    public function get_emails() {
        return $this->emails;
    }

    /**
     * Get from name for email.
     *
     * @return string
     */
    public function get_from_name() {
        return wp_specialchars_decode( get_option( 'jc_commerce_email_from_name' ) );
    }

    /**
     * Get from email address.
     *
     * @return string
     */
    public function get_from_address() {
        return get_option( 'jc_commerce_email_from_address' );
    }

    /**
     * Get the email header.
     *
     * @param mixed $email_heading heading for the email
     */
    public function email_header( $email_heading ) {
//        wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
    }

    /**
     * Get the email footer.
     */
    public function email_footer() {
//        wc_get_template( 'emails/email-footer.php' );
    }
}