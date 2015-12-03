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
    public static function init() {
        self::enqueue_scripts();
        self::register_actions();
    }

    private static function register_actions() {
        add_action( 'before_delete_post', function($post_id){
                /**
                 * remove all media files associated with it.
                 *   meta value will be taken care of automatically.
                 */
//                $files = get_post_meta($post_id, "_images", true);
//                $files = array_merge($files, get_post_meta($post_id, "_thumbnails", true));
//                $files[] = get_post_meta($post_id, "_wechat", true);
//                foreach( $files as $key=>$file) {
//                    wp_delete_file( $file );
//                }
                global $logger;
                $logger->log_action(__CLASS__, "all media files associated with post {$post_id} is deleted." );

            }
        );
    }

    private static function enqueue_scripts() {
        add_action("admin_enqueue_scripts",
            function()
            {
                wp_register_script('dropzone-core', JC_PLUGIN_DIR_URL . 'js/libs/dropzone.min.js');
                wp_enqueue_script('meta-boxes-artwork-media', JC_PLUGIN_DIR_URL . 'js/admin/meta-boxes-artwork-media.js', ['tiptip', 'jquery-ui-sortable', 'dropzone-core']);
                wp_localize_script('meta-boxes-artwork-media', 'jc_data', array(
                        'ajaxurl'   => admin_url('admin-ajax.php'),
                    )
                );

                wp_enqueue_style("font-awesome");
            }
        );
    }

    public static function output( $post ) {
        global $logger;

        $post_id = $post->ID;
        $author_id = $post->post_author;
        $nonce = wp_create_nonce("jc_upload_media");

        $existing_thumbnails = wp_json_encode( self::get_existing_thumbnails($post_id) );
        ?>
        <div id="cover-image">
            <h3>Cover Image
                <span class="description">Cover image will show on the main page. You may crop and resize it to show customers exactly the way you like it to be.</span>
            </h3>

        </div>
        <div id="other-images">
            <h3>Other Images
                <span class="description">These images will be shown on artwork detail page only. <br />Click <strong>Preview</strong> at the bottom of the page to see how your artwork will look like.</span>
            </h3>
            <div id="media-upload-wrap" class="dz-clickable clearfix" data-post_id="<?php echo $post_id; ?>" data-author_id="<?php echo $author_id; ?>" data-nonce="<?php echo $nonce; ?>">
                <span class="dz-message">Drop files here or click</span>
                <div id="upload-indicator-wrap">
                    <div id="upload-indicator" class="dz-clickable">&#43;</div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            var existing_thumbnails = <?php echo $existing_thumbnails; ?>;
        </script>
        <style>
            #artwork-media .inside h3 {
                padding-left: 0;
            }
            #artwork-media .inside h3 span {
                font-weight: lighter;
                font-size: smaller;
                display: block;
                color: rgb(145, 145, 145);
                padding-top: 5px;

            }

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
            }

            #media-upload-wrap:hover,
            #media-upload-wrap.dz-drag-hover {
                cursor: pointer;
                background-color: #f3f3f3;
            }
            #media-upload-wrap span.dz-message {
                line-height: 120px;
                font-weight: bold;
                font-size: large;
                color: rgba(18, 161, 224, 0.65);
            }
            #media-upload-wrap.dz-started span.dz-message {
                display: none;
            }
            #media-upload-wrap #upload-indicator-wrap {
                display: none;
                padding: 15px;
                width: calc(120px - 15*2px);
                float: left;
                margin-right:5px;
            }
            #media-upload-wrap.dz-started #upload-indicator-wrap {
                display: inline-block;
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
                -webkit-transition-property: opacity, blur;
                -moz-transition-property: opacity, blur;
                -ms-transition-property: opacity, blur;
                -o-transition-property: opacity, blur;
                transition-property: opacity, blur;
                -webkit-transition-duration: 400ms;
                -moz-transition-duration: 400ms;
                -ms-transition-duration: 400ms;
                -o-transition-duration: 400ms;
                transition-duration: 400ms;
            }
            .dz-preview:hover img {
                cursor: move;
                -webkit-filter: blur(3px);
                -moz-filter: blur(3px);
                -o-filter: blur(3px);
                -ms-filter: blur(3px);
                filter: blur(3px);
            }
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
            .dz-preview.dz-error .dz-error-message{
                display: block;

                background-color: rgba(0, 0, 0, 0.7);
                color: #FFF;
            }



            /********* PROGRESS BAR *********/
            .dz-preview .dz-progress{
                position: absolute;
                bottom: 10%;

                height: 10px;
                width: 95%;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
                overflow: hidden;

                left: 50%;
                -webkit-transform: translateX(-50%);
                -moz-transform: translateX(-50%);
                -ms-transform: translateX(-50%);
                -o-transform: translateX(-50%);
                transform: translateX(-50%);
            }

            .dz-preview .dz-upload {
                height: 100%;
                background-color: #74c9ee;

                display: block;

                /*opacity: 0;*/
                -webkit-transition-property: opacity;
                -moz-transition-property: opacity;
                -ms-transition-property: opacity;
                -o-transition-property: opacity;
                transition-property: opacity;
            }
            .dz-preview.dz-processing .dz-upload {
                opacity: 1;
            }
            .dz-preview.dz-complete .dz-upload {
                opacity: 0;
            }


            .dz-preview .dz-remove {
                cursor: pointer;
                position: absolute;
                display: block;
                width: 26px;
                height: 26px;
                top: 8px;
                right: 8px;
                font-size: 2em;
                /*color: brown;*/
                text-decoration: none;
                opacity: 0;
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

            .dz-preview:hover .dz-remove {
                opacity: 1;
            }


        </style>
        <?php
    }


    /**
     * Gets the file NAMES of the existing thumbnails
     *
     * @param $post_id int
     * @return array. [name => url]
     */
    private static function get_existing_thumbnails($post_id) {
        $existing_thumbnails_urls = get_thumbnails($post_id);
        $existing_images_urls = get_images($post_id);

        $result = [];

        for ($i=0, $len = count($existing_thumbnails_urls); $i<$len; $i++){
            $result[] = ["name" => pathinfo($existing_images_urls[$i], PATHINFO_BASENAME),
                         "url"  => $existing_thumbnails_urls[$i]];
        }

//        foreach ($existing_thumbnails_urls as $key => $url) {
//            $result[] = ["name" => pathinfo($existing_images_urls[$key], PATHINFO_BASENAME),
//                "url"  => $url];
//        }

        return $result;
    }

}