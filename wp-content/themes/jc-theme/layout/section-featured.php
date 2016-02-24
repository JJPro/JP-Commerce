<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * featured section of the home page.
 */
?>
<section id="featured">
    <div class="section-header text-center">
        <h2>Featured Arts</h2>
        <p class="sub-header">Check out our editors' choices.</p>
    </div>
    <div class="row">

    <?php
    /**
     * TODO:
     * Plan A:
     * 1. create the new loop
     * 2. loop through the posts and call content-artwork.php to print the artwork
     * 3. reset the loop
     *
     */

    /**
     * 1. create the loop
     * args : taxonomy => artwork,
     *          num_of_posts => 5,
     *
     *
     */
    $featured_args = array(
        'post_status' => 'publish',
        'post_type' => 'artwork',
        'meta_key' => '_is_featured',
        'meta_value' => 1,
        'posts_per_page' => 5,
        'orderby' => 'rand',
    );

    // The featured artworks query.
    $featured = new WP_Query( $featured_args );
    $featured_ids = array();

    /*
     * 2. Loop through the posts and print
     */
    if ( $featured->have_posts() ) {
        while ( $featured->have_posts() ) {
            $featured->the_post();
            $featured_ids[] = $featured->post->ID;

            get_template_part( 'template-parts/content-artwork' );

        }
    }
    set_featured_artwork_ids($featured_ids);
    ?>
    </div> <!-- .row -->
</section> <!-- #featured-->