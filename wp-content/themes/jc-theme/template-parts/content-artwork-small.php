<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('artwork-archive-small clearfix'); ?>>
    <header class="entry-header text-center">
        <a class="entry-link" href="<?php echo esc_url( get_permalink() ); ?>">
            <img src="<?php echo jc_get_featured_image(); ?>" />
        </a>
    </header>

    <footer class="entry-footer text-center">
        <?php the_title( '<span class="entry-title">', '</span>' ); ?>

        <a href="<?php the_permalink(); ?>" class="btn btn-default btn-xs" role="button">View Details</a>
    </footer>
</article>