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
        add_action('current_screen', array($this, 'conditional_includes'));
        add_action('current_screen', array($this, 'hide_contextual_help'));
        add_filter('screen_options_show_screen', '__return_false');   // hide screen options
        add_action('current_screen', array($this, 'limit_artists_visibility_of_posts'));
        $this->remove_footer_text();
        JC_Scripts::register_admin_scripts();
    }

    /**
     * Includes any classes we need within admin.
     */
    public function includes()
    {
        include_once('jc-meta-box-functions.php');
        include_once('class-jc-admin-menus.php');
        include_once('class-jc-admin-meta-boxes.php');
    }

    /**
     * Include admin files depends on various admin screens
     *
     */
    public function conditional_includes()
    {
        $screen = get_current_screen();

        switch ($screen->id) {
            case 'dashboard' :
//                include_once('class-jc-admin-dashboard.php');
                /* 
                    TODO - Create & Fill out the dashboard file
                    @author - Jason
                    @date   - 11/22/15
                    @time   - 3:42 AM
                */
                break;
            case 'artwork' :
                $this->one_column_layout();
                add_action('before_delete_post', '__delete_artwork_files');
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