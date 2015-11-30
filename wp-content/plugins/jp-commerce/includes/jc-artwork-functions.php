<?php
/**
 * Artwork Functions
 *
 * Functions to get and save information about artworks
 *
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Deletes all files related to the artwork post
 * This function is triggered by "before_delete_post" action.
 *
 * @param $post_id int
 */
function __delete_artwork_files( $post_id ) {
    // only delete files when this is an artwork post type
    global $post_type, $logger;

    if ( $post_type != 'artwork' )
        return;

    $author_id = get_post_field("post_author", $post_id);
    $files_dir = wp_upload_dir()["basedir"] . "/artworks/{$author_id}/{$post_id}";

    WP_Filesystem_Direct::delete( $files_dir, true);
}


/**
 * Retrieve original size media files associated to the artwork
 *
 * @param $post_id int
 * @return array. URLs to original sized media files.
 */
function get_images( $post_id ) {
    $images = get_post_meta($post_id, "_images", true);
    $images = is_array($images) ? $images : array();

    return $images;
}

/**
 * Retrieve thumbnails associated to the artwork
 *
 * Regenerates missing thumbnails on the fly if they haven't been created yet or missing some.
 *
 * @param $post_id int
 * @return array. URLs to thumbnail images.
 */
function get_thumbnails( $post_id ) {

    // Don't save meta boxes for revisions or autosaves
    if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post_id ) ) || is_int( wp_is_post_autosave( $post_id ) ) ) {
        return;
    }

    global $logger;


    $thumbnails = get_post_meta( $post_id, "_thumbnails", true );
    $thumbnails = is_array($thumbnails) ? $thumbnails : array();
    $missing_thumbnails = get_post_meta( $post_id, "_missing_thumbnails", true );

    if (
        $missing_thumbnails &&

        // create directory if needed
        wp_mkdir_p( dirname(_get_image_path( $post_id, null, THUMBNAIL, pathinfo($missing_thumbnails[0], PATHINFO_BASENAME)) ) )
        )
    {
        $logger->log_action(__FUNCTION__ . " Missing Thumbnails",$missing_thumbnails);

        foreach($missing_thumbnails as $key => $image_path) {
            // thumbnail url
            $url  = _get_image_url ($post_id, null, THUMBNAIL, pathinfo($image_path, PATHINFO_BASENAME));
            // thumbnail path
            $path = _get_image_path ($post_id, null, THUMBNAIL, pathinfo($image_path, PATHINFO_BASENAME));
            $image_editor = wp_get_image_editor($image_path);
            if ( ! is_wp_error($image_editor) ) {
                $image_editor->resize(120, 120, ["center", "center"]);

                if (JC_DEBUG)
                    $logger->log_action("Thumbnail path", $path);

                if ( ! is_wp_error( $image_editor->save( $path ) ) ){
                    $thumbnails[] = $url;
                }
            }
        }

        if ( is_numeric( update_post_meta($post_id, "_thumbnails", $thumbnails) ) )
            update_post_meta($post_id, "_thumbnails", $thumbnails);
        update_post_meta($post_id, "_missing_thumbnails", false);
    }

    return $thumbnails;
}

/**
 * Retrieves the featured image for the artwork
 *
 * Return the first image if not set
 *
 * @param $post_id int
 * @return string. URL to the featured image.
 */
function get_featured_image( $post_id ) {

    $featured = get_post_meta($post_id, "_featured_image", true);
    $images = get_images($post_id);
    if (! $featured ) {
        if (!$images){
            return '';
        }
        return $images[0];
    } else {
        return $featured;
    }
}

/**
 * Retrieves wechat sized image (featured image) for the artwork
 *
 * Recreates with featured image if not set yet.
 *
 * @param $post_id int
 * @return string. URL to wechat image.
 */
function get_wechat_image($post_id) {

    // Don't save meta boxes for revisions or autosaves
    if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post_id ) ) || is_int( wp_is_post_autosave( $post_id ) ) ) {
        return;
    }

    $wechat = get_post_meta($post_id, "_wechat", true); // path string

    if (!$wechat) {
        $featured = get_featured_image($post_id);
        if (!$featured){
            return '';
        }

        $path = _get_image_path($post_id, null, WECHAT, pathinfo($featured, PATHINFO_BASENAME));

        // create the directory if necessary
        if (! wp_mkdir_p( dirname( $path ) ) ){
            return '';
        }

        $image_editor = wp_get_image_editor($featured);
        $image_editor->resize(300, 300, ["center", "center"]);
        $url = _get_image_url($post_id, null, WECHAT, pathinfo($featured, PATHINFO_BASENAME));
        if (! is_wp_error( $image_editor->save($path) ) ){
            if ( is_numeric( update_post_meta($post_id, "_wechat", $url) ) )
                update_post_meta($post_id, "_wechat", $url);
            return $url;
        } else {
            return '';
        }
    }
    return $wechat;
}


/**
 * Gets path to image
 *
 * @param $post_id int
 * @param $author_id int
 * @param $image_size int ORIGINAL|THUMBNAIL|WECHAT
 * @param $image_name string The image you are getting path for. e.g. profile.png
 * @return string. path to image.
 */
if (!defined("ORIGINAL")) {define("ORIGINAL", "ORIGINAL");}
if (!defined("THUMBNAIL")) {define("THUMBNAIL", "THUMBNAIL");}
if (!defined("WECHAT")) {define("WECHAT", "WECHAT");}
function _get_image_path( $post_id, $author_id = null, $image_size, $image_name) {
    return __get_image__($post_id, $author_id, $image_size, $image_name, PATH);
}

/**
 * Gets URL to image
 *
 * @param $post_id int
 * @param $author_id int
 * @param $image_size int ORIGINAL|THUMBNAIL|WECHAT
 * @param $image_name string The image you are getting path for. e.g. profile.png
 * @return string. path to image.
 */
function _get_image_url( $post_id, $author_id = null, $image_size, $image_name) {
    return __get_image__($post_id, $author_id, $image_size, $image_name, URL);
}


/**
 * Gets URL to image
 * @internal @private
 * @param $post_id int
 * @param $author_id int
 * @param $image_size int ORIGINAL|THUMBNAIL|WECHAT
 * @param $image_name string The image you are getting path for. e.g. profile.png
 * @param $link_type int PATH|URL
 * @return string. path or url to image.
 */
if (!defined("PATH")) {define("PATH", "PATH");}
if (!defined("URL")) {define("URL", "URL");}
function __get_image__( $post_id, $author_id = null, $image_size, $image_name, $link_type) {

    $filename   = pathinfo($image_name, PATHINFO_FILENAME);
    $extension  = pathinfo($image_name, PATHINFO_EXTENSION);

    $author_id = $author_id ? $author_id : get_post_field('post_author', $post_id);
    $arg = (PATH == $link_type) ? "basedir" : "baseurl";
    $path = wp_upload_dir()[$arg] . "/artworks/{$author_id}/{$post_id}/";
    $foldername = '';
    $sizeinfo = '';
    if ($image_size == THUMBNAIL){
        $foldername = 'thumbnails/';
        $sizeinfo   = '-120x120';
    }
    elseif ($image_size == WECHAT){
        $foldername = 'wechat/';
        $sizeinfo   = '-300x300';
    }
    $path .= $foldername . $filename . $sizeinfo . '.' . $extension;

    return $path;
}

/**
 * Retrieve artwork object
 *
 * @param $post int|Object
 * @return JC_Artwork|bool False if artwork not found.
 */
function get_artwork( $post ) {
//    $product = new JC_Artwork($post);
}