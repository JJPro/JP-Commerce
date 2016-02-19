<?php
/**
 * The template for displaying search results pages.
 *
 * @author: Lu Ji
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <?php if ( have_posts() ) : ?>
            <header class="page-header">
                <h2 class="page-title"><?php printf( 'Search Results for: %s', get_search_query() ); ?></h2>
            </header><!-- .page-header -->
            <div class="row">
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php get_template_part( 'content', 'search' ); ?>
                <?php endwhile; ?>
            </div><!-- .row -->
            <?php
            // page navigation.
            the_posts_pagination( array(
                'prev_text'          => 'Previous page',
                'next_text'          => 'Next page',
                'before_page_number' => '<span class="meta-nav screen-reader-text">' . 'Page' . ' </span>',
            ) );

        // If no content, include the "No posts found" template.
        else :
            get_template_part( 'content', 'none' );
        endif;
        ?>

    </main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_footer(); ?>
