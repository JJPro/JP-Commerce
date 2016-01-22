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
$artwork = JC_Artwork::instance($post);
?>
<img src="<?php echo $artwork->cover_thumbnail;?>"/>
