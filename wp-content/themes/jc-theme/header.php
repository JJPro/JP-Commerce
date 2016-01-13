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
        <title><?php wp_title( '|', true, 'right' ); ?></title>
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

        <header class="container">
            <div class="row">
                <div class="col-xs-3 text-left">
                    <?php
                        $logo_url = logo_url();
                        if ($logo_url) {
                            echo "<img src='$logo_url'/>";
                        }
                    ?>
                </div>
                <div class="col-xs-6 text-center">
                    <?php jc_search_form(); ?>
                </div>
                <div class="col-xs-3 text-right">
                    <span class="dashicons-before dashicons-admin-users"></span>
                    <?php if (current_user_can('artist')): ?>
                        <li><a href="<?php echo admin_url('post-new.php?post_type=artwork'); ?>">Upload New Art</a> </li>
                        <li><a href="<?php echo admin_url('edit.php?post_type=artwork'); ?>">All Arts</a> </li>
                    <?php endif; ?>
                </div>
                <div class="row">

                    <nav class="navbar navbar-default navbar-jc">
                        <?php
                            wp_nav_menu( array(
                                'theme_location' => 'header',
                                'container' => false,
                                'menu_class' => 'nav navbar-nav',
                            ) );
                        ?>

                    </nav>
                </div>
            </div>
        </header>
        <main class="container">