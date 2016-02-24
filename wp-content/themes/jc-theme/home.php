<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Template Name: Home page
 *
 * The front page template. 
 *
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

get_header();

get_template_part( 'layout/section', 'header' );

get_template_part( 'layout/section', 'featured' );

get_template_part( 'layout/section', 'trending' );

get_template_part( 'layout/section', 'new' );

get_footer();