<?php get_header(); ?>

<section id="header">

</section> <!-- #header-->

<div class="row">
    <?php
    if (have_posts()) {
        while ( have_posts() ) {
            the_post();

            get_template_part( 'template-parts/content-artwork' );

        }
    }
    ?>
</div>

<?php get_footer(); ?>