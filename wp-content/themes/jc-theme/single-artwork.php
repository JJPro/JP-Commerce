<?php
/**
 * Single page for the artwork
 *
 * Implementation Details:
 *  - increase artwork view count only when the current user is not the author
 *  - "Add to favorites" button
 *      - 1. AJAX to add this artwork to user's favorites list
 *      - 2. AJAX to increase artwork fav count only when the first step is successful
 *  - swap images upon selection on image array.
 */
?>

<?php get_header(); ?>

<?php while ( have_posts() ) : the_post();

    // retrieve the artwork
    $artwork = JC_Artwork::instance( get_the_ID() );
    $author_id = $artwork->artist;

    // increase view count
    if ( get_current_user_id() != $author_id ) {
        $artwork->views++;
    }
    ?>
    <div style="display: none;">
        <img src="<?php echo $artwork->wechat_image; ?>" />
    </div>

    <article id="post-<?php the_ID(); ?>" <?php post_class('artwork-detail'); ?>>
        <div class="row">
            <div class="col-sm-7 col-lg-8">

                <div class="images-container">
                    <div class="current-image ">
                        <img id="current-image" src="<?php echo $artwork->cover_image; ?>" alt="artwork cover image">
                    </div> <!-- .current-image -->

                    <div class="thumbnails-array">
                        <div class="artwork-thumbnail-container"><div class="artwork-thumbnail background-image" style="background-image: url(<?php echo $artwork->cover_thumbnail; ?>);" data-original-image="<?php echo $artwork->cover_image; ?>"></div></div>
                        <?php
                        $other_images_thumbnails = $artwork->other_images_thumbnails;
                        $other_images = $artwork->other_images;
                        $count = count($other_images_thumbnails);
                        for( $i = 0; $i < $count; $i++ ) {
                            echo '<div class="artwork-thumbnail-container"><div class="artwork-thumbnail background-image" style="background-image: url(' . $other_images_thumbnails[$i]->url . ');" data-original-image="' . $other_images[$i]->url . '"></div></div>';
                        }
                        ?>

                    </div> <!-- .images-array -->
                </div> <!-- .images-container -->

            </div>
            <div class="col-sm-5 col-lg-4">
                <div class="artwork-info">

                    <?php the_title( '<h3 class="entry-title text-capitalize">', '</h3>' ); ?>

                    <div class="entry-description">

                        <?php echo $artwork->description; ?>

                    </div> <!-- .entry-description -->

                    <div class="entry-meta">

                        <p>
                            <em>by</em>: <span class="author-name"><?php the_author_posts_link(); ?></span>
                            <br>
                            <?php the_terms( $artwork->id, 'artwork_type', '<em>Category</em>: ' ); ?>
                            <br>
                            <em>Materials</em>: <?php echo implode( ', ', array_map( function($m){return $m->name;}, $artwork->materials ) ); ?>
                            <br>
                            <em>Size</em>: <?php echo get_artwork_size($artwork); ?>
                            <br>
                            <em>Year Made</em>: <?php echo $artwork->date_created; ?>
                        </p>

                    </div> <!-- .entry-meta -->

                    <div class="price-container">
                        <?php if ( $artwork->is_for_sale() ): ?>
                            <span class="price"><?php echo $artwork->price_of_artwork; ?></span>
                            <a href="#" class="jc-btn-flat jc-btn-purchase">Add to Cart</a>
                        <?php else: ?>
                            <strong>NOT FOR SALE</strong>
                        <?php endif; ?>
                    </div>

                    <a href="#" class="jc-btn-flat jc-btn-favorite">Add to Favorites</a>

                    <?php if ( get_current_user_id() == $author_id ) : ?>

                        <ul class="artwork-stats">
                            <li><span class="icon-heart-o"><p><?php echo $artwork->favorites; ?></p></li>
                            <li><span class="icon-eye"><p><?php echo $artwork->views; ?></p></li>
                        </ul>

                    <?php endif; ?>

                </div> <!-- .artwork-info -->
            </div> <!-- .col- -->
        </div> <!-- .row -->
    </article>

<?php endwhile; ?>

<section id="other-artworks-of-author" class="clearfix">
    <h3>Other Artworks by <?php the_author(); ?></h3>
    <?php
    $other_artworks_args = array(
        'post_status' => 'publish',
        'post_type' => 'artwork',
        'posts_per_page' => 5,
        'orderby' => 'rand',
        'author' => $author_id,
        'post__not_in' => array( $artwork->id )
    );

    $other_artworks = new WP_Query( $other_artworks_args );

    if ( $other_artworks->have_posts() ) {
        while ( $other_artworks->have_posts() ) {
            $other_artworks->the_post();

            get_template_part( 'template-parts/content-artwork-small' );

        }
    }
    ?>

<!--    --><?php //if ( $other_artworks->max_num_pages > 1 ): ?>
    <div class="col-xs-12 text-center">
        <a href="<?php echo get_author_posts_url($author_id); ?>" class="jc-btn-flat jc-btn-default">More ...</a>
    </div>
<!--    --><?php //endif; ?>
</section>   <!-- #other-artworks-of-author -->

<?php
/**
 * The Modal Window
 *
 * For showing current image full screen.
 */
?>
<!-- The Modal -->
<div id="modal" class="modal">
    <!-- The Close Button -->
    <span class="jc-close" onclick="document.getElementById('modal').style.display='none'">&times;</span>
    <!-- Modal Content (The Image) -->
    <img class="modal-content" id="modal-image">
</div>

<?php get_footer(); ?>