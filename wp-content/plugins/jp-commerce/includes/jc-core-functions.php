<?php
/**
 * JP Commerce Core Functions
 *
 * JP Commerce core functions for both admin and frontend
 *
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


// Include core functions (available in both admin and frontend)
include_once( 'jc-page-functions.php' );
include_once( 'jc-tax-functions.php' );
include_once( 'jc-artwork-functions.php' );


/**
 * Manually Send HTML emails from JP Commerce
 *
 * @param mixed $to
 * @param mixed $subject
 * @param mixed $message
 * @param string $headers (default: "Content-Type: text/html\r\n")
 * @param string $attachments (default: "")
 */
function jc_mail( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = "" ) {
    $mailer = JC()->mailer();

    $mailer->send( $to, $subject, $message, $headers, $attachments );
}

/**
 * Generates respective Path from an URL
 *
 * @param $url string
 * @return string
 */
function url_to_path($url) {
    $url = str_replace(rtrim(get_site_url(), '/') . '/', ABSPATH, $url);
    return $url;
}