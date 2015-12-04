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

        // general shared script for both frontend and admin areas
        wp_register_script('jc-shared', JC_PLUGIN_DIR_URL . 'js/jc-shared.min.js', ['tiptip']);
    }

    /**
     * @private use DO NOT CALL
     */
    private static function shared_css() {
        // tipTip
        wp_register_style('tiptip', JC_PLUGIN_DIR_URL . 'css/libs/tipTip.min.css');
        // css for jquery-ui
        wp_register_style('jquery-ui', 'https://code.jquery.com/ui/1.11.4/themes/black-tie/jquery-ui.css');
        // bootstrap-grid-system
        wp_register_style('grid-system', JC_PLUGIN_DIR_URL . 'css/libs/bootstrap-grid-system.min.css');
        // generic css for both frontend and admin areas
        wp_register_style('jc-shared', JC_PLUGIN_DIR_URL . 'css/jc-shared.min.css');
    }

    /**
     * @private_use_DO_NOT_CALL
     */
    private static function admin_js() {


    }

    /**
     * @private_use_DO_NOT_CALL
     */
    private static function admin_css() {
        // font awesome
        wp_register_style('font-awesome', JC_PLUGIN_DIR_URL . 'fonts/font-awesome/css/font-awesome.min.css');
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

/*
// admin script
wp_register_script('jc-admin-script', plugins_url('js/script.js', dirname(__FILE__)), ['jquery-ui-datepicker']);
wp_register_script('dropzone', plugins_url('js/dropzone.js', dirname(__FILE__)));
wp_register_script('upload_meta_box', plugins_url('js/upload_meta_box.js', dirname(__FILE__)), ['dropzone']);
wp_register_script('options_js', plugins_url('js/options.js', dirname(__FILE__)));

// date picker style
wp_register_style('datepicker-style', '///code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css');
wp_register_style('dropzone-style', plugins_url('css/dropzone.css', dirname(__FILE__)));
wp_register_style('jc-generic-style', plugins_url('css/style.css', dirname(__FILE__)));
wp_register_style('options-style', plugins_url('css/options.css', dirname(__FILE__)));

add_action('admin_enqueue_scripts', 'jc_enqueue_style_generic');
add_action('admin_enqueue_scripts', 'jc_enqueue_script_meta_box_artwork_date_created');
add_action('admin_enqueue_scripts', 'jc_enqueue_script_meta_box_artwork_upload');

function jc_enqueue_script_meta_box_artwork_date_created(){
    $screen = get_current_screen();
    if ($screen->post_type == 'artwork'){
        wp_enqueue_script('jc-admin-script');
        wp_enqueue_style('datepicker-style');
    }
}

function jc_enqueue_script_meta_box_artwork_upload() {
    $screen = get_current_screen();
    if ($screen->post_type == 'artwork'){
        wp_enqueue_script('upload_meta_box');
        wp_enqueue_style('dropzone-style');
    }
}

function jc_enqueue_style_generic() {
    wp_enqueue_style('jc-generic-style');
    wp_enqueue_script('jc-admin-script');
}
*/