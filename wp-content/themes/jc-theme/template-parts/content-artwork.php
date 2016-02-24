<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>
<div class="col-xs-12 col-sm-6 col-md-4">
    <article id="post-<?php the_ID(); ?>" <?php post_class('artwork-archive'); ?>>
        <header class="entry-header text-center">
            <a class="entry-link" href="<?php echo esc_url( get_permalink() ); ?>">
                <img src="<?php echo jc_get_featured_image(); ?>" />
            </a>
        </header>

        <footer class="entry-footer text-left">
            <?php the_title( '<h3 class="entry-title">', '</h3>' ); ?>
            <p>
                by: <strong><?php the_author_posts_link(); ?></strong>
                <a href="<?php the_permalink(); ?>" class="btn btn-default btn-xs pull-right" role="button"><strong>View Details</strong></a>
            </p>
        </footer>
    </article>
</div>