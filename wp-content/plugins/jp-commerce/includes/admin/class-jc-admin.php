<?php
/**
 * JC Admin
 *
 * Handles Admin Screen Access and Functions
 *
 * @class       JC_Admin
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class JC_Admin
{
    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('init', array($this, 'includes'));
        add_action('admin_init', array($this, 'prevent_admin_access_for_customers'));
        add_action('admin_init', array($this, 'hide_update_notices_from_non_admins'), 1);
        add_action('current_screen', array($this, 'conditional_includes'));
        add_action('current_screen', array($this, 'hide_contextual_help'));
//        add_filter('screen_options_show_screen', '__return_false');   // hide screen options
        add_action('current_screen', array($this, 'limit_artists_visibility_of_posts'));
        $this->remove_footer_text();
        JC_Scripts::register_admin_scripts();

        JC_Scripts::enqueue_common_admin_scripts();
    }

    /**
     * Includes any classes we need within admin.
     */
    public function includes()
    {
        include_once('jc-meta-box-functions.php');
        include_once('class-jc-admin-menus.php');
        include_once('class-jc-admin-meta-boxes.php');

        // fixing some admin styles
        add_action( 'admin_head', function() {
            ?>
            <style type="text/css">
                .notice-dismiss {
                    top: 50%;
                    transform: translateY(-50%);
                    height: 100%;
                }
            </style>
            <?php
        });
    }

    /**
     * Include admin files depends on various admin screens
     *
     */
    public function conditional_includes()
    {
        $screen = get_current_screen();

        switch ($screen->id) {

            case 'promotion' :
                add_action( 'admin_head', function() {
                    ?>
                    <style type="text/css">
                        #wp-word-count { display: none; }
                    </style>
                    <?php
                });
                break;
            case 'edit-promotion' :
                add_action('admin_enqueue_scripts', function () {
                    wp_enqueue_script( 'jc-font' );
                    wp_enqueue_style( 'jc-font' );
                    wp_enqueue_script( 'tiptip' );
                    wp_enqueue_style( 'tiptip' );
                    wp_enqueue_script( 'jc-shared' );
                    wp_enqueue_style( 'jc-shared' );
                    wp_enqueue_script('promotions-list-table');
                    wp_enqueue_style('promotions-list-table');
                    wp_localize_script('promotions-list-table', 'jc_data', array(
                        'ajaxurl'   => admin_url('admin-ajax.php'),
                    ));
                });
                break;
            case 'artwork' :
                add_action('before_delete_post', function($post_id) {
                    $artwork = JC_Artwork::instance($post_id);
                    $artwork->pre_delete_artwork();
                });
                // remove edit-slug-box beneath title
                // vertical center notice dismiss button
                add_action( 'admin_head', function() {
                    ?>
                    <style type="text/css">
                        #edit-slug-box {
                            display: none;
                        }

                    </style>
                    <?php
                });
                break;
            case 'edit-artwork' :
                add_action('admin_enqueue_scripts', function () {
                    wp_enqueue_script('artworks-list-table');
                    wp_enqueue_style('artworks-list-table');
                    wp_localize_script('artworks-list-table', 'jc_data', array(
                        'ajaxurl'   => admin_url('admin-ajax.php'),
                    ));
                });
                // remove quick edit link
                add_filter('post_row_actions',
                    function($actions, $post) {
                        unset($actions['inline hide-if-no-js']);
                        return $actions;
                    },
                    10, 2
                );
                break;
            case 'order' :
                JC_Order::init(); // initializes actions and filters for orders.
                break;
            default :
                break;

        }
    }

    /**
     * Prevent customers from accessing admin
     */
    public function prevent_admin_access_for_customers()
    {
        if (current_user_can('customer')) {
            wp_safe_redirect(jc_get_page_permalink('myaccount'));
            exit;
        }
    }

    public function hide_update_notices_from_non_admins()
    {
        if (!current_user_can('update_core')){
            remove_action( 'admin_notices', 'update_nag', 3 );
        }
    }

    /**
     * Hide contextual help
     */
    public function hide_contextual_help()
    {
        add_filter('contextual_help', 'remove_help_tabs', 999, 3);
        function remove_help_tabs($old_help, $screen_id, $screen)
        {
            $screen->remove_help_tabs();
            return $old_help;
        }
    }

    /**
     * Remove footer text
     */
    private function remove_footer_text()
    {
        add_filter('admin_footer_text', '__return_false', 999);
        add_filter('update_footer', '__return_false', 999);
    }

    // limit artists to see only their posts in the admin screen
    public function limit_artists_visibility_of_posts()
    {
        $screen = get_current_screen();
        if (!$screen->id == 'artwork') {
            return;
        }

        add_filter('request', '__limit_artists_visibility_of_posts');
        function __limit_artists_visibility_of_posts($request)
        {
            global $current_screen;
            if ($current_screen->post_type == 'artwork') {

                if (current_user_can('artist')) {
                    $request['author'] = get_current_user_id();

                    add_filter('views_' . get_current_screen()->id, 'remove_posts_counts');

                    function remove_posts_counts($posts_count_li)
                    {
                        unset($posts_count_li['mine']);

                        // recalculate pending and drafts
                        foreach (['pending', 'draft', 'all', 'publish'] as $status) {
                            if (isset($posts_count_li[$status])) {
                                global $wpdb;
                                $query = "SELECT COUNT(*) FROM " . $wpdb->posts . " WHERE post_author=" . get_current_user_id();
                                $query .= " AND post_type='artwork'";
                                $query .= ($status == 'all') ? " AND NOT post_status='auto-draft'" : " AND post_status='{$status}'";
                                $new_count = $wpdb->get_var($query);
                                if ($new_count == 0)
                                    unset($posts_count_li[$status]);
                                else
                                    $posts_count_li[$status] = preg_replace('/\(([0-9]+)\)/', "({$new_count})", $posts_count_li[$status]);
                            }
                        }
                        return $posts_count_li;
                    }
                }
            }

            return $request;
        }

    }

    /**
     * Force one-column layout
     */
    private function one_column_layout()
    {
        // one column layout
        add_filter('get_user_option_screen_layout_artwork', function () {
            return 1;
        });
        // order of meta boxes
        add_filter('get_user_option_meta-box-order_artwork', 'artwork_layout_order');
        function artwork_layout_order($order)
        {
            return array(
                'normal' => join(",", array(
                    'uploaddiv',
                    'datecreateddiv',
                    'dimensionsdiv',
                    'postimagediv',
                    'pricediv',
                    'submitdiv',
                )),
                'side' => '',
                'advanced' => '',
            );
        }

        add_filter('get_user_option_meta-box-order_promotion', 'promotion_layout_order');
        function promotion_layout_order($order)
        {
            return array(
                'side' => join(',', array(
                    'tagsdiv-promotion_type',
                    'coupondiv',
                    'submitdiv',
                )),
                'advanced' => '',
            );
        }
    }
}

return new JC_Admin();