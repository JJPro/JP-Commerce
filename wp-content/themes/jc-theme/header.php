<?php
/**
 * Header Template
 * Author: jjpro
 * Date: 1/4/16
 * Time: 2:55 AM
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
        <link ref="profile" href="http://gmpg.org/xfn/11">
        <?php if (is_singular() && pings_open( get_queried_object() ) ): ?>
            <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
        <?php endif; ?>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>

        <div id="promotion-top" class="container">
            <?php
            $preview_promo_id = $_GET['preview_promo'];
            $active_promo = $preview_promo_id ? JC_Promotion::instance($preview_promo_id) : JC_Promotion::the_active_promotion();

            if ($active_promo) {
                echo $active_promo->content;
            }
            ?>
        </div> <!-- Promotion -->

        <header class="container">
            <div class="row">
                <div class="col-xs-3 text-left">
                    <?php $logo_url = logo_url(); ?>
                    <?php if ($logo_url) : ?>
                    <a href="<?php home_url(); ?>">
                        <img class="logo-image" src="<?php echo $logo_url; ?>" />
                    </a>
                    <?php endif; ?>
                </div>
                <div class="col-xs-6 text-center">
                    <?php jc_search_form(); ?>
                </div>
                <div class="col-xs-3 text-right right-side-controls">
                    <?php get_template_part('layout/menu', 'account'); ?>
                </div>

            </div> <!-- .row -->
            <div class="row">
                <?php if (has_nav_menu('primary')): ?>
                    <div class="nav-container">

                        <nav class="navbar navbar-tabs navbar-jc">
                            <?php
                            wp_nav_menu( array(
                                'theme_location' => 'primary',
                                'container' => false,
                                'menu_class' => 'nav nav-tabs',
                                'walker' => new JC_Walker_Nav_Primary()
                            ) );
                            ?>

                        </nav>
                    </div> <!-- .nav-container -->
                <?php endif; ?>
            </div>
        </header>
        <main class="container">