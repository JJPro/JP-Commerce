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
 * insert new record for uploaded file
 * @param $post_id int post_id | artwork_id
 * @param $path string
 * @param $url string
 * @param $order int
 * @return bool|int file_id on success
 */
function add_artwork_file($post_id, $path, $url, $order) {
    global $wpdb;
    $table = "{$wpdb->prefix}artwork_files";

    return $wpdb->insert($table, array('post_id' => $post_id, 'path' => $path, 'url' => $url, 'order' => $order),
        array('%d', '%s', '%s', '%d'));
}

/**
 * @param $file_id int Entry id for file that requires update
 * @param $changes array array('column' => new_val)
 * @return bool
 */
function update_artwork_file($file_id, $changes) {
    global $wpdb;
    $table = "{$wpdb->prefix}artwork_files";

    $success = $wpdb->update($table, $changes, array('id' => $file_id));

    if ($success === false)
        return false;
    else
        return true;
}

/**
 * @param $file_id int
 * @return Object {id, post_id, path, url, order}
 */
function get_artwork_file_with_file_id($file_id) {
    global $wpdb;
    $table = "{$wpdb->prefix}artwork_files";

    $query = "SELECT id, post_id, path, url, `order` FROM {$table} WHERE id = {$file_id}";

    return $wpdb->get_row($query);
}

/**
 * Retrieve the file object at a given order.
 * @param $post_id
 * @param $order
 * @return Object|null {id, post_id, path, url, order}
 */
function get_artwork_file_at_order($post_id, $order) {
    global $wpdb;
    $table = "{$wpdb->prefix}artwork_files";

    $query = "SELECT id, post_id, path, url, `order` FROM {$table} WHERE post_id = {$post_id} AND `order` = {$order}";

    return $wpdb->get_row($query);
}

/**
 * Get all the artwork files related to this artwork/post
 * @param $post_id
 * @return array(Object)|null Object {id, post_id, path, url, order}
 */
function get_artwork_files($post_id) {
    global $wpdb;
    $table = "{$wpdb->prefix}artwork_files";

    $query = "SELECT id, post_id, path, url, `order` FROM {$table} WHERE post_id = {$post_id} ORDER BY `order`";

    return $wpdb->get_results($query);
}

/**
 * @param $file_id
 * @return bool
 */
function delete_artwork_file($file_id) {
    global $wpdb;
    $table = "{$wpdb->prefix}artwork_files";

    $success = $wpdb->delete($table, array('id' => $file_id), array('%d'));

    if ($success === false)
        return false;
    else
        return true;
}

/**
 * This function deletes the database record of the files, it doesn't delete the files on disk.
 * @param $post_id
 * @return bool
 */
function delete_all_artwork_files($post_id) {

    global $logger;
    $logger->log_action('Delete function is called');

    global $wpdb;
    $table = "{$wpdb->prefix}artwork_files";

    $success = $wpdb->delete($table, array('post_id' => $post_id), array('%d'));

    if ($success === false)
        return false;
    else
        return true;
}



/**
 * Locate image with its name and post info
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
if (!defined("DROPZONE_THUMBNAIL")) {define("DROPZONE_THUMBNAIL", "DROPZONE_THUMBNAIL");}
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
 * @param $image_name string The image you are getting path for, can also be a path if you are getting the destination path for moving files. e.g. profile.png
 * @param $link_type int PATH|URL
 * @return string. path or url to image.
 */
if (!defined("PATH")) {define("PATH", "PATH");}
if (!defined("URL")) {define("URL", "URL");}
function __get_image__( $post_id, $author_id = null, $image_size, $image_name, $link_type) {

    global $logger;

    $filename   = pathinfo($image_name, PATHINFO_FILENAME);
    $extension  = pathinfo($image_name, PATHINFO_EXTENSION);

    $author_id = $author_id ? $author_id : get_post_field('post_author', $post_id);
    $arg = (PATH == $link_type) ? "basedir" : "baseurl";
    $path = wp_upload_dir()[$arg] . "/artworks/{$author_id}/{$post_id}/";
    $foldername = '';
    $sizeinfo = '';
    if ($image_size == THUMBNAIL){
        $foldername = 'thumbnails/';
        $sizeinfo   = '-300xAUTO';
    }
    elseif ($image_size == DROPZONE_THUMBNAIL) {
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
 * Get all artwork_materials as an array.
 * @return array
 */
function get_artwork_materials() {
    $taxonomies = 'artwork_material';
    $args = array(
        'hide_empty' => false,
    );

    $materials = get_terms($taxonomies, $args);
    if (! is_wp_error($materials))
        return $materials;
    else
        return array();
}
