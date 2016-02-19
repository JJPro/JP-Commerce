<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * 
 *
 * Template for each search entry
 *
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( get_post_type() === 'artwork' ) {
    get_template_part( 'template-parts/content-artwork' );
}