<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * trending section of the home page.
 */
?>
<section id="trending">
    <div class="section-header text-center">
        <h2>Trending Arts</h2>
        <p class="sub-header">Discover shopper's top picks.</p>
    </div>
    <div class="row">
    <?php
    /**
     * 1. create the loop
     */
    $trending_args = array(
        'post_status' => 'publish',
        'post_type' => 'artwork',
        'meta_key' => '_views',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'posts_per_page' => 5,
        'post__not_in' => get_featured_artwork_ids(),
    );

    // The featured artworks query.
    $trending = new WP_Query( $trending_args );
    $trending_ids = array();

    /*
     * 2. Loop through the posts and print
     */
    if ( $trending->have_posts() ) {
        while ( $trending->have_posts() ) {
            $trending->the_post();
            $trending_ids[] = $trending->post->ID;

            get_template_part( 'template-parts/content-artwork' );
        }
    }
    set_trending_artwork_ids($trending_ids);

    ?>
    </div> <!-- .row -->
</section> <!-- #trending -->
