<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * new posts section of the home page.
 */
?>
<section id="new">
    <div class="section-header text-center">
        <h2>Latest Arts</h2>
        <p class="sub-header">Explore the latest postings.</p>
    </div>
    <div class="row">
    <?php
    /**
     * 1. create the loop
     */
    $new_args = array(
        'post_status' => 'publish',
        'post_type' => 'artwork',
        'posts_per_page' => 5,
        'post__not_in' => array_merge( get_featured_artwork_ids(), get_trending_artwork_ids() ),
    );

    // The featured artworks query.
    $new = new WP_Query( $new_args );
    $new_ids = array();

    /*
     * 2. Loop through the posts and print
     */
    if ( $new->have_posts() ) {
        while ( $new->have_posts() ) {
            $new->the_post();
            $new_ids[] = $new->post->ID;

            get_template_part( 'template-parts/content-artwork' );
        }
    }
    set_new_artwork_ids($new_ids);


    ?>
    </div> <!-- .row -->
</section> <!-- #new -->
