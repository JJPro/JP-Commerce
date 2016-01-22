<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * JC Meta Box Artwork Media
 *
 * Displays artwork media upload meta box and handles Ajax request and responds
 *
 * @class       JC_Meta_Box_Artwork_Media
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 *
 * TODO:
 * show the progress while uploading images to the server upon submitting of post
 */
class JC_Meta_Box_Artwork_Media
{
    public static function init() {
        self::enqueue_scripts();
        self::register_actions();
    }

    private static function register_actions() {

        // action to delete media files
        add_action( 'before_delete_post', function($post_id){
            /**
             * remove all media files associated with it.
             *   meta value will be taken care of automatically.
             */
            $artwork = JC_Artwork::instance($post_id);
            $artwork->pre_delete_artwork();

            global $logger;
            $logger->log_action(__CLASS__, "all media files associated with post {$post_id} is deleted." );

        });

        // change form tag
        add_action( 'post_edit_form_tag', function() {
            echo ' enctype="multipart/form-data"';
        });
    }

    private static function enqueue_scripts() {
        add_action("admin_enqueue_scripts",
            function()
            {

                // jcrop
                wp_enqueue_script('jcrop');
                wp_enqueue_style('jcrop');

                // dropzone
                wp_register_script('dropzone-core', JC_PLUGIN_DIR_URL . 'js/libs/dropzone.min.js');

                // js
                wp_enqueue_script('meta-boxes-artwork-media', JC_PLUGIN_DIR_URL . 'js/admin/meta-boxes-artwork-media.min.js', ['tiptip', 'jquery-ui-sortable', 'jquery-effects-shake', 'dropzone-core', 'jquery-ui-dialog', 'jquery-effects-bounce']);
                wp_localize_script('meta-boxes-artwork-media', 'jc_data', array(
                        'ajaxurl'   => admin_url('admin-ajax.php'),
                    )
                );

                // css
                wp_enqueue_style('meta-boxes-artwork-media', JC_PLUGIN_DIR_URL . 'css/meta-boxes/artwork-media.css');
            }
        );
    }

    public static function output( $post ) {
        global $logger;

        $artwork = JC_Artwork::instance($post);
        $id = $artwork->id;
        $nonce = wp_create_nonce("jc_upload_media");

        $cover_thumbnail = $artwork->cover_thumbnail; // url
        $wechat_image = $artwork->wechat_image; // url
        $other_thumbnails = $artwork->other_images_dropzone_thumbnails; // objects {id, post_id, path, url, order}
        $logger->log_action($other_thumbnails);
        ?>
        <?php /** pass some data **/ ?>
        <script type="text/javascript">
            var artwork = <?php echo $id; ?>;
            var cover_thumb = <?php echo $cover_thumbnail ? wp_json_encode($cover_thumbnail) : "''"; ?>;
            var other_thumbnails = <?php echo wp_json_encode($other_thumbnails); ?>;
        </script>

        <?php /** HTML  **/ ?>
        <div id="cover-image">
            <h3>Cover Image</h3>
            <div id="cover-image-notices"></div>
            <p class="description">Cover image will show on the main page and the will be used as the icon image when shared. </p>

            <div id="cover-image-upload-container">
            <div id="cover-image-upload">
                <img id="cover-preview" style="display: <?php if (!$cover_thumbnail) echo 'none'; ?>" src="<?php echo $cover_thumbnail; ?>" width="100%"/>
                <input id="cover-input" type="file" name="cover_image" style="visibility: hidden; display: none;">
                <input class="hidden-input" type="text" name="coord[x]" />
                <input class="hidden-input" type="text" name="coord[y]" />
                <input class="hidden-input" type="text" name="coord[w]" />
                <input class="hidden-input" type="text" name="coord[h]" />
            </div>
            </div>
            <p style="text-align: center;">
                <button id="btn-update-cover" class="button">Update Cover Image</button>
            </p>


            <h4>Wechat Thumbnail</h4>
            <p class="description">Used as icon image when the artwork is shared via social network, such as <span class="tiptip description" title="http://www.facebook.com" >Facebook</span> and <span class="tiptip description" title="<p>Wechat is a extremely popular social App from Tencent China. It has more than 1 billion daily active users worldwide. </p><p>http://www.wechat.com</p>" >Wechat</span>.</p>
            <div id="wechat-preview-wrap-wrap">

                <div id="wechat-preview-container">
                    <img id="wechat-preview" style="display: <?php if (!$wechat_image) echo 'none'; ?>;" src="<?php echo $wechat_image; ?>" />
                </div>
            </div>
        </div>
        <hr>
        <div id="other-images">
            <h3>Other Images
            </h3>
            <p class="description">These images will be shown on artwork detail page only. <br />Click <strong>Preview</strong> at the bottom of the page to see how your artwork will look like.</p>
            <div id="media-upload-wrap" class="dz-clickable clearfix" data-nonce="<?php echo $nonce; ?>">
                <span class="dz-message">Drop files here or click</span>
                <div id="upload-indicator-wrap">
                    <div id="upload-indicator" class="dz-clickable">&#43;</div>
                </div>
            </div>
        </div>
        <div id="dropzone-local-errors">
            <ul></ul>
        </div>
        <div id="file-processing-modal">
            <p>Please wait while your files are being processed. </p>

            <div id="total-progress">
                <div id="progress-bar">
                    <span id="progress-text"></span>
                </div>
            </div>
            <div id="media-upload-errors">
                <ul></ul>
            </div>
        </div>
        <?php
    }


    public static function save($post_id, $post) {

        /********* Save the cover image ***********/
        $coord = $_POST["coord"];
        $cover = $_FILES["cover_image"];

        if ($cover['error'] === 0) {
            $artwork = JC_Artwork::instance($post);
            $tmp_name = $cover["tmp_name"];
            $img_type = pathinfo($cover["name"], PATHINFO_EXTENSION);

            $artwork->set_cover_image($tmp_name, $img_type, $coord);
        }
    }
}