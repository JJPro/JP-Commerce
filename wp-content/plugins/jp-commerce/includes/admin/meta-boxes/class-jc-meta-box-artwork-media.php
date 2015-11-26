<?php
/**
 * JC Meta Box Artwork Media
 *
 * Displays artwork media upload meta box and handles Ajax request and responds
 *
 * @class       JC_Meta_Box_Artwork_Media
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JC_Meta_Box_Artwork_Media 
{
    public static function init(){
        self::enqueue_scripts();
        self::register_ajax_functions();
    }

    private static function enqueue_scripts() {
        add_action("admin_enqueue_scripts",
            function()
            {
                wp_enqueue_script('meta-boxes-artwork-media', JC_PLUGIN_DIR_URL . 'js/admin/meta-boxes-artwork-media.min.js', ['jquery-core', 'dropzone-core']);
                wp_localize_script('meta-boxes-artwork-media', 'myAjax', array(
//                        'ajaxurl'   => admin_url('admin-ajax.php'),
                        'ajaxurl'   => JC_PLUGIN_DIR_URL . 'test_ajax.php'
                    )
                );
            }
        );
    }

    private static function register_ajax_functions() {
        global $logger;
        $logger->log_action('registering ajax functions');
        $logger->log_action($_FILES);

        add_action("wp_ajax_meta_box_artwork_media_upload", function() {

            /**
             * Move uploaded files to artists own directory
             *      directory structure: uploads/artwork-images/artistusername/post_id/*.*
             */
            $name = $_FILES["name"];
            $type = $_FILES["type"];
            $size = $_FILES["size"];
            $tmp_path = $_FILES["tmp_name"];
            $errn = $_FILES["error"];

            global $logger;
            $logger->log_action($_FILES);
            $logger->log_action("AJax is triggerred");
        });
        add_action("wp_ajax_nopriv_meta_box_artwork_media_upload", function(){
            wp_die("You must log in to upload files.");
        });
    }

    public static function output( $post ) {
        $thumbnails = [];
        $post_id = $post->ID;
        $nonce = wp_create_nonce("jc_upload_media");



        ?>
        <div id="media-upload-wrap" class="dz-clickable clearfix" data-post_id="<?php echo $post_id; ?>" data-nonce="<?php echo $nonce; ?>">
<!--            <span class="dz-message">Drop files here or click to upload.</span>-->
            <div id="upload-indicator-wrap">
                <div id="upload-indicator" class="dz-clickable">&#43;</div>
            </div>
        </div>
        <style>
            .clearfix::after {
                content: "";
                display: table;
                clear: both;
            }

            #media-upload-wrap {
                min-height: 120px;
                margin: 10px 3px 3px;
                padding: 10px;
                border: 2px dashed lightblue;
                background-color: #f7f7f7;
                text-align: center;
                vertical-align: top;
            }

            #media-upload-wrap:hover,
            #media-upload-wrap.dz-drag-hover {
                cursor: pointer;
                background-color: #f3f3f3;
            }
            #media-upload-wrap span.dz-message {
                display: block;
                text-align: center;
                line-height: 120px;
                font-weight: bold;
                font-size: large;
                color: rgba(18, 161, 224, 0.65);
            }
            #media-upload-wrap.dz-started span.dz-message {
                display: none;
            }
            #upload-indicator-wrap {
                display: inline-block;
                padding: 15px;
                width: calc(120px - 15*2px);
                float: left;
                margin-right:5px;
            }
            #upload-indicator {
                font-size: 60px;
                line-height: calc(120px - 15*2px - 3*2px);
                text-align: center;
                color: lightgrey;
                background-color: white;
                border: dashed 3px lightgray;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
            }
            #media-upload-wrap.dz-max-files-reached #upload-indicator-wrap {
                display: none;
            }

            .dz-preview {
                position: relative;
                display: inline-block;
                margin-right: 5px;
                margin-bottom:5px;
                cursor: default;
                float: left;
            }
            .dz-preview img{
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;

                opacity: 1;
                -webkit-transition-property: opacity;
                -moz-transition-property: opacity;
                -ms-transition-property: opacity;
                -o-transition-property: opacity;
                transition-property: opacity;
                -webkit-transition-duration: 400ms;
                -moz-transition-duration: 400ms;
                -ms-transition-duration: 400ms;
                -o-transition-duration: 400ms;
                transition-duration: 400ms;
            }
            .dz-process,
            .dz-error-message {
                position: absolute;
                top: 50%;
                -webkit-transform: translateY(-50%);
                -moz-transform: translateY(-50%);
                -ms-transform: translateY(-50%);
                -o-transform: translateY(-50%);
                transform: translateY(-50%);
                padding: 3px;
                display: none;
            }
            .dz-preview.dz-processing .dz-progress{
                position: absolute;
                bottom: 10%;

                height: 10px;
                width: 95%;
                left: 50%;
                -webkit-transform: translateX(-50%);
                -moz-transform: translateX(-50%);
                -ms-transform: translateX(-50%);
                -o-transform: translateX(-50%);
                transform: translateX(-50%);
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
                overflow: hidden;
            }
            .dz-preview.dz-processing .dz-progress .dz-upload {
                display: block;
                height: 100%;
                background-color: #74c9ee;

            }
            .dz-preview.dz-error .dz-error-message{
                display: block;

                background-color: rgba(0, 0, 0, 0.7);
                color: #FFF;
            }

            .dz-preview .dz-feature,
            .dz-preview .dz-remove {
                position: absolute;
                right: -20px;
                font-size: 30px;
                color: brown;
                text-decoration: none;
                opacity: 0;
                -webkit-transition-property: opacity, right;
                -moz-transition-property: opacity, right;
                -ms-transition-property: opacity, right;
                -o-transition-property: opacity, right;
                transition-property: opacity, right;
                -webkit-transition-duration: 400ms;
                -moz-transition-duration: 400ms;
                -ms-transition-duration: 400ms;
                -o-transition-duration: 400ms;
                transition-duration: 400ms;
            }
            .dz-feature {
                top: 50%;
                -webkit-transform: translateY(-50%);
                -moz-transform: translateY(-50%);
                -ms-transform: translateY(-50%);
                -o-transform: translateY(-50%);
                transform: translateY(-50%);
            }
            .dz-remove {
                bottom: 12px;
            }
            .dz-preview:hover .dz-feature,
            .dz-preview:hover .dz-remove {
                opacity: 1;
                right: 10px;
            }
            .dz-preview:hover img {
                opacity: 0.7;
            }


        </style>
        <?php
    }

    private static function thumbnails( $post ) {

    }
}