<?php
/**
 * Registers frequently used scripts
 */

if ( !defined('ABSPATH') ) exit;

class JC_Scripts {
    public static function register_admin_scripts() {
        // admin js
        add_action("admin_enqueue_scripts", function(){ self::admin_js(); });
        // admin css
        add_action("admin_enqueue_scripts", function(){ self::admin_css(); });

        // shared js
        add_action("admin_enqueue_scripts", function(){ self::shared_js(); });
        // shared css
        add_action("admin_enqueue_scripts", function(){ self::shared_css(); });
    }

    public static function register_frontend_scripts() {
        add_action("wp_enqueue_scripts", function(){ self::frontend_js(); });
        add_action("wp_enqueue_scripts", function(){ self::frontend_css(); });

        // shared js
        add_action("wp_enqueue_scripts", function(){ self::shared_js(); });
        // shared css
        add_action("wp_enqueue_scripts", function(){ self::shared_css(); });
    }

    /**
     * @private use DO NOT CALL
     */
    private static function shared_js() {
        // tipTip js
        wp_register_script('tiptip', JC_PLUGIN_DIR_URL . 'js/libs/jquery.tipTip.min.js', ['jquery-core']);

        // bootstrap
        wp_register_script('bootstrap', JC_PLUGIN_DIR_URL . 'js/libs/bootstrap.min.js', ['jquery-core']);

        // general shared script for both frontend and admin areas
        wp_register_script('jc-shared', JC_PLUGIN_DIR_URL . 'js/jc-shared.min.js', ['tiptip']);

        // jcrop
        wp_register_script('jcrop', JC_PLUGIN_DIR_URL . 'js/libs/jquery.Jcrop.min.js', ['jquery-core']);
    }

    /**
     * @private use DO NOT CALL
     */
    private static function shared_css() {
        // tipTip
        wp_register_style('tiptip', JC_PLUGIN_DIR_URL . 'css/libs/tipTip.min.css');
        // css for jquery-ui
        wp_register_style('jquery-ui', 'https://code.jquery.com/ui/1.11.4/themes/black-tie/jquery-ui.css');
        // bootstrap
        wp_register_style('bootstrap', JC_PLUGIN_DIR_URL . 'css/libs/bootstrap.min.css');
        // generic css for both frontend and admin areas
        wp_register_style('jc-shared', JC_PLUGIN_DIR_URL . 'css/jc-shared.min.css');

        // fonts
        wp_register_style('jc-font', JC_PLUGIN_DIR_URL . 'fonts/jc-font/styles.css');

        // jcrop
        wp_register_style('jcrop', JC_PLUGIN_DIR_URL . 'css/libs/jcrop.min.css');
    }

    /**
     * @private_use_DO_NOT_CALL
     */
    private static function admin_js() {
        wp_register_script('artworks-list-table', JC_PLUGIN_DIR_URL . 'js/admin/artworks-list-table.js');

    }

    /**
     * @private_use_DO_NOT_CALL
     */
    private static function admin_css() {
        wp_register_style('artworks-list-table', JC_PLUGIN_DIR_URL . 'css/artworks-list-table.min.css');
    }

    /**
     * @private_use_DO_NOT_CALL
     */
    private static function frontend_js() {

    }

    /**
     * @private_use_DO_NOT_CALL
     */
    private static function frontend_css() {

    }

}
