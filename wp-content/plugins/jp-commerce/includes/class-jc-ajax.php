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
        $missing_thumbnails = $missing_thumbnails ? $missing_thumbnails : array();

        if (! wp_verify_nonce($nonce, "jc_upload_media") )
            wp_die("Operation is forbidden.");

//        if (JC_DEBUG)
//            $logger->log_action("FILES", $_FILES);

        if ( is_array($_FILES) && count($_FILES) > 0 )
        {
            $saved_files = get_post_meta($post_id, "_images", true);
            $saved_files = $saved_files ? $saved_files : array();

            if (JC_DEBUG)
                $logger->log_action("LAST SAVED FILES", $saved_files);

            foreach( $_FILES as $key=>$file ){

                $filename = $file["name"];
                $tmp_path = $file["tmp_name"];
                $errn = $file["error"];


                $dest_dir = wp_upload_dir()["basedir"] . "/artworks/{$author_id}/{$post_id}";
                $dest_file_path = "{$dest_dir}/{$filename}";

                if (JC_DEBUG) $logger->log_action("Error", $errn);

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

                    if (JC_DEBUG) $logger->log_action("Directory created or exists", $dest_dir);

                    if (!move_uploaded_file($tmp_path, $dest_file_path)) {
                        if (JC_DEBUG) $logger->log_action("Error", "Cannot move file to location $dest_file_path");
                        wp_die("Failed to save file on the server.");
                    }
                    if (JC_DEBUG) $logger->log_action("Success", "Moved file to location $dest_file_path");
                    $saved_files[] = _get_image_url($post_id, $author_id, ORIGINAL, $filename);
                    $missing_thumbnails[] = $dest_file_path;

                    if (JC_DEBUG) {
                        $__images = get_post_meta($post_id, "_images", true);
                        $__missing = get_post_meta($post_id, "_missing_thumbnails", true);
                        $__thumbnails = get_post_meta($post_id, "_thumbnails", true);
                        $logger->log_action("BEFORE UPDATING META", sprintf("_images: %s\n_missing: %s\n_thumbnails: %s\n",
                            wp_json_encode($__images), wp_json_encode($__missing), wp_json_encode($__thumbnails)));

                        $logger->log_action("WILL UPDATE TO", sprintf("_images: %s\n_missing: %s\n_thumbnails: %s\n",
                            wp_json_encode($saved_files), wp_json_encode($missing_thumbnails), "Not applicable"));
                    }

                    if (is_numeric( update_post_meta($post_id, "_images", $saved_files) ))
                        update_post_meta($post_id, "_images", $saved_files);
                    if (is_numeric( update_post_meta($post_id, "_missing_thumbnails", $missing_thumbnails) ))
                        update_post_meta($post_id, "_missing_thumbnails", $missing_thumbnails);

                    if (JC_DEBUG) $logger->log_action("Success", "Finished Updating _images and _missing_thumbnails");
                }
            }
            wp_die( "Your file is uploaded!" );
        } else {
            wp_die("Error: No files are provided");
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
        $processed_thumbnails = get_thumbnails($post_id);
        $missing_thumbnails = get_post_meta($post_id, "_missing_thumbnails", true);

        if ($missing_thumbnails && in_array($image_path, $missing_thumbnails)){

            $key = array_search($image_path, $missing_thumbnails);

            if (JC_DEBUG)
                $logger->log_action("Unsetting in Missing_Thumbnails: ", $missing_thumbnails[$key]);

            unset($missing_thumbnails[$key]);
            $missing_thumbnails = array_values($missing_thumbnails);
            update_post_meta($post_id, "_missing_thumbnails", $missing_thumbnails);
        } else {

            $thumbnail_url_for_file = _get_image_url($post_id, $author_id, THUMBNAIL, $file);

            if (JC_DEBUG):
                $logger->log_action("Deleting thumbnail_url_for_file: ", $thumbnail_url_for_file);
                $logger->log_action("From processed_thumbnails: ", $processed_thumbnails);
            endif;


            $key = array_search($thumbnail_url_for_file, $processed_thumbnails);

            if (JC_DEBUG)
                $logger->log_action("Unsetting in _Thumbnails: ", $processed_thumbnails[$key]);

            unset($processed_thumbnails[$key]);
            $processed_thumbnails = array_values($processed_thumbnails);
            update_post_meta($post_id, "_thumbnails", $processed_thumbnails);

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

        if (JC_DEBUG)
            $logger->log_action("URL of the new featured image", $new_featured);

        if ( $new_featured != get_featured_image($post_id) ) {
            if (is_numeric( update_post_meta($post_id, "_featured_image", $new_featured) ))
                update_post_meta($post_id, "_featured_image", $new_featured);
        }

        wp_die();
    }
}

JC_AJAX::init();