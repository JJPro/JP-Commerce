<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * 
 *
 * Retriving information from the settings page
 *
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */


/**
 * @return string n%
 */
function get_commission_rate() {
    $rate = get_option('commission_rate');
    $rate = floatval($rate) * 100;
    $rate = "{$rate}%";
    return $rate;
}