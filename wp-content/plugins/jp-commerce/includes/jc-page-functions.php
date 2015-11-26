<?php
/**
 * Page Functions
 *
 * Functions related to pages and menus
 *
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Retrieve page permalink
 *
 * @param string $page
 * @return string
 */
function jc_get_page_permalink( $page ) {
    $page_id = jc_get_page_id($page);
    $permalink = $page_id ? get_permalink( $page_id ) : '';
    return $permalink;
}

/**
 * Retreive page ids - used for myaccount, edit_address, shop, cart, checkout, orders.
 *                      returns -1 if no page found
 * @param string $page
 * @return int
 */
function jc_get_page_id( $page ) {
    $page_id = get_option( "jc_{$page}_page_id" );

    return $page_id ? absint( $page_id ) : -1;
}