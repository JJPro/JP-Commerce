<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * 
 *
 * Functions for set and get order info
 *
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

/**
 * Retrieves the order record with specified fields.
 *
 * @param $order_id int.
 * @param $args array. The columns to retrieve.
 * @return array|null. Associative array ['col' => value], null on failure.
 */
function query_order_entry($order_id, $args) {
    global $wpdb;
    $fields = implode(', ', $args);
    $query = "SELECT $fields FROM {$wpdb->prefix}orders WHERE id = {$order_id}";
    return $wpdb->get_row($query, ARRAY_A);
}


/**
 * @param $order_id int
 * @param $meta_key string
 * @param $single   bool
 * @return mixed. Returns array if $single is false, or false if meta doesn't exist.
 */
function get_order_meta($order_id, $meta_key, $single=true) {
    global $wpdb;

    $query = "SELECT meta_value FROM {$wpdb->prefix}ordermeta WHERE order_id = {$order_id} AND meta_key = {$meta_key}";
    $results = $wpdb->get_results($query); // array of objects

    if ($results){
        if ($single){
            return maybe_unserialize($results[0]->meta_value);
        } else {
            return array_map(function($e){
                return maybe_unserialize($e->meta_value);
            }, $results);
        }
    } else {
        return false;
    }
}

/**
 * serialize meta_value and save to db
 * @param $order_id     int
 * @param $meta_key     string
 * @param $meta_value   mix
 * @param $unique       bool Default is false.
 * @return mix bool|int Returns id of inserted row on success, returns false on error.
 */
function add_order_meta($order_id, $meta_key, $meta_value, $unique = true) {
    global $wpdb;
    $meta_value = maybe_serialize($meta_value);

    if ($unique) {
        $query = "SELECT meta_value FROM {$wpdb->prefix}ordermeta WHERE order_id = {$order_id} AND meta_key = {$meta_key}";
        $existing_rows = $wpdb->query($query);
        if ($existing_rows > 0)
            return false;
    }
    return $wpdb->insert('ordermeta', array('meta_key' => $meta_key, 'meta_value' => $meta_value));
}

function update_order_meta($order_id, $meta_key, $meta_value) {
    global $wpdb;

    $meta_value = maybe_serialize($meta_value);
    $wpdb->update('ordermeta', array('meta_value' => $meta_value), array('order_id' => $order_id, 'meta_key' => $meta_key), '%s', array('%d', '%s'));
}

/**
 * @param $order_id
 * @param $meta_key
 * @param $meta_value
 * @return false|int  Number of rows deleted, false on error.
 */
function delete_order_meta($order_id, $meta_key, $meta_value) {
    global $wpdb;
    return $wpdb->delete('ordermeta', array('order_id' => $order_id, 'meta_key' => $meta_key, 'meta_value' => $meta_value), array('%d', '%s', '%s'));
}
