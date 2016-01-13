<?php
/**
 * Template for page
 * User: jjpro
 * Date: 1/5/16
 * Time: 8:16 PM
 */
?>
<?php get_header(); ?>

<?php
while ( have_posts() ) : the_post();
    get_template_part( 'content', 'page' );
endwhile;
?>

<?php get_footer(); ?>