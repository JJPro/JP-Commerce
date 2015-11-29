<?php
/**
 * JPCommerce JC_AJAX
 *
 * AJAX Event Handler
 *
 * @class       JC_AJAX
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JC_AJAX 
{
    public static function init() {

        self::add_ajax_events();
    }


    /**
     * Hook in methods - uses WordPress ajax handlers (admin-ajax)
     */
    public static function add_ajax_events() {

        // array( event_name => nopriv? )
        $ajax_events = array(
            'upload_artwork_media'              => false,
            'remove_artwork_media'              => false,
            'set_artwork_featured_image'        => false,
        );

        foreach ( $ajax_events as $ajax_event => $nopriv ) {
            add_action( 'wp_ajax_jp_commerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );

            if ( $nopriv ) {
                add_action( 'wp_ajax_nopriv_jp_commerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
            }
        }
    }

    /**
     * AJAX upload artwork pictures
     */
    public static function upload_artwork_media() {
        global $logger;

        $post_id = $_POST["post_id"];
        $author_id = $_POST["author_id"];
        $nonce   = $_POST["nonce"];

        // save newly uploaded images to meta so that get_thumbnails() knows to when to update thumbnails.
        $missing_thumbnails = get_post_meta($post_id, "_missing_thumbnails", true);
        $missing_thumbnails = is_array($missing_thumbnails) ? $missing_thumbnails : array();

        if (! wp_verify_nonce($nonce, "jc_upload_media") )
            wp_die("Operation is forbidden.");

        if ( is_array($_FILES) && count($_FILES) > 0 )
        {
            $saved_files = get_post_meta($post_id, "_images", true);
            $saved_files = is_array($saved_files) ? $saved_files : array();

            $file = $_FILES['file']; // uploadMultiple is disabled.

            $tmp_path = $file["tmp_name"];
            $errn = $file["error"];

            $filename = $file['name'];

            $dest_file_path = _get_image_path($post_id, $author_id, ORIGINAL, $filename);
            $dest_dir       = dirname($dest_file_path);

            switch ($errn) {
                case 1:
                    wp_die( "Error: File is too large" );
                    break;
                case 3:
                    wp_die( "Error: File is not complete." );
                    break;
                case 4:
                    wp_die( "Error: No file is provided." );
                    break;
                case 6:
                    wp_die( "Error: No tmp dir exists on the server. Please contact your server's administrator." );
                    break;
                case 7:
                    wp_die( "Error: Permission denied to write to tmp dir." );
                    break;
            }

            if ( wp_mkdir_p( $dest_dir ) ) { // create dir if not already exists


                if (!move_uploaded_file($tmp_path, $dest_file_path)) {
                    wp_die("Failed to save file on the server.");
                }
                $saved_files[]          = _get_image_url($post_id, $author_id, ORIGINAL, $filename);
                $missing_thumbnails[]   = $dest_file_path;


                if (is_numeric( update_post_meta($post_id, "_images", $saved_files) ))
                    update_post_meta($post_id, "_images", $saved_files);
                if (is_numeric( update_post_meta($post_id, "_missing_thumbnails", $missing_thumbnails) ))
                    update_post_meta($post_id, "_missing_thumbnails", $missing_thumbnails);
            }


            wp_send_json_success( array(
                'filename'      => $filename
            ) );
                // The sent message will be a json object of array( 'success' => true, 'data' => array( 'filename' => xxx ) ).


        } else {
            wp_send_json_error("Error: No files are provided");
                // The sent message will be a json object of array( 'success'   => false, 'data' => 'Error: No files are provided').
        }
    }

    /**
     * Deletes artwork file
     *
     * remove thumbnail file
     * update thumbnail meta
     * if this image happen to be _featured:
     *      unset the _featured meta
     *      remove wechat file
     *      unset _wechat meta
     */
    public static function remove_artwork_media() {
        global $logger;

        $post_id    = $_POST["post_id"];
        $author_id  = $_POST["author_id"];
        $nonce      = $_POST["nonce"];
        $file       = $_POST["name"];

        $image_path      = _get_image_path($post_id, $author_id, ORIGINAL, $file);
        $thumbnail_path  = _get_image_path($post_id, $author_id, THUMBNAIL, $file);

        if (! wp_verify_nonce($nonce, "jc_upload_media") )
            wp_die("Operation is forbidden.");

        if (! file_exists($image_path))
            wp_die(); // we don't do anything if file doesn't exists, because this might be called from "removedfile" emit from Dropzone JS.

        wp_delete_file($image_path);
        wp_delete_file($thumbnail_path);
        $images = get_images($post_id);


        // unset it from _images meta
        $image_url_for_file = _get_image_url($post_id, $author_id, ORIGINAL, $file);
        $key = array_search($image_url_for_file, $images);
        unset( $images[$key] );
        $images = array_values($images);
        update_post_meta($post_id, "_images", $images);



        // unset and delete from _thumbnails
        $processed_thumbnails = get_post_meta($post_id, "_thumbnails", true); // Can't use get_thumbnails(), because it will mess up the missing_thumbnails variable we are getting next.
        $missing_thumbnails = get_post_meta($post_id, "_missing_thumbnails", true);

        if ($missing_thumbnails && in_array($image_path, $missing_thumbnails)){

            if (JC_DEBUG)
                $logger->log_action("The thumbnail for the deleted file has not been created yet");

            $key = array_search($image_path, $missing_thumbnails);

            if (JC_DEBUG)
                $logger->log_action("Will unset from missing_thumbnails", sprintf("deleting %s from %s", $thumbnail_path, $missing_thumbnails));

            unset($missing_thumbnails[$key]);
            $missing_thumbnails = array_values($missing_thumbnails);
            update_post_meta($post_id, "_missing_thumbnails", $missing_thumbnails);

            if (JC_DEBUG)
                $logger->log_action("Did unset thumbnail from missing thumbnails: ", sprintf("rest missing thumbnails", json_encode(get_post_meta($post_id, "_missing_thumbnails", true))));

        } else {

            $thumbnail_url_for_file = _get_image_url($post_id, $author_id, THUMBNAIL, $file);

            if (JC_DEBUG)
                $logger->log_action("Will unset thumbnail: ", sprintf("deleting %s from %s", $thumbnail_url_for_file, json_encode($processed_thumbnails)));


            $key = array_search($thumbnail_url_for_file, $processed_thumbnails);

            unset($processed_thumbnails[$key]);
            $processed_thumbnails = array_values($processed_thumbnails);
            update_post_meta($post_id, "_thumbnails", $processed_thumbnails);

            if (JC_DEBUG)
                $logger->log_action("Did unset thumbnail: ", sprintf("rest thumbnails", json_encode(get_post_meta($post_id, "_thumbnails", true))));

        }

        // unset _featured if matched
            // unset and delete wechat
        $featured = get_featured_image($post_id);
        if ( $image_url_for_file == $featured ){
            update_post_meta($post_id, "_featured_image", false);
            update_post_meta($post_id, "_wechat", false);
            $wechat_file_path = _get_image_path($post_id, $author_id, WECHAT, $file);
            wp_delete_file($wechat_file_path);
        }



        $logger->log_action("The file {$file} has been deleted from post meta for post {$post_id}");

        wp_die();
    }

    /**
     * Sets featured image of artwork
     */
    public static function set_artwork_featured_image() {
        global $logger;
        if (JC_DEBUG)
            $logger->log_action("AJAX", "setting featured image");
        if (JC_DEBUG)
            $logger->log_action("Featured Image Setting message", $_POST);

        $post_id    = $_POST["post_id"];
        $author_id  = $_POST["author_id"];
        $nonce      = $_POST["nonce"];
        $file       = $_POST["name"];

        // verify nonce.
        if (! wp_verify_nonce($nonce, "jc_upload_media") )
            wp_die("Operation is forbidden.");

        /**
         * update the meta value
         * (Note: don't have to do anything with _wechat meta, get_wechat() will auto-generate the wechat image and meta from featured image)
         */
        $new_featured = _get_image_url($post_id, $author_id, ORIGINAL, $file);

        if ( $new_featured != get_featured_image($post_id) ) {
            if (is_numeric( update_post_meta($post_id, "_featured_image", $new_featured) ))
                update_post_meta($post_id, "_featured_image", $new_featured);
        }

        wp_die();
    }
}

JC_AJAX::init();