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
            // Artworks
            'add_artwork_file'                  => false,
            'update_artwork_file_order'         => false,
            'delete_artwork_file'               => false,
            'set_artwork_as_featured'           => false,
            'cancel_artwork_featured'           => false,

            // Orders
            'add_cs_note'                       => false,
            'delete_cs_note'                    => false,
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
    public static function add_artwork_file() {
        $artwork = $_POST["artwork"];
        $order   = $_POST["file_order"];
        $nonce   = $_POST["nonce"];

        if (! wp_verify_nonce($nonce, "jc_upload_media") )
            wp_die("Operation is forbidden.");

        if ( is_array($_FILES) && count($_FILES) > 0 )
        {
            $file = $_FILES['file']; // uploadMultiple is disabled.

            $filename = $file["name"];
            $tmp_path = $file["tmp_name"];
            $ext      = pathinfo($filename, PATHINFO_EXTENSION);
            $errn     = $file["error"];

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

            $artwork = JC_Artwork::instance($artwork);
            if ($artwork->add_other_image($tmp_path, $ext, $order)) {
                wp_send_json_success();
            } else {
                wp_send_json_error(['filename' => $filename]);
                // The sent message will be a json object of array( 'success' => true, 'data' => array( 'filename' => xxx ) ).
            }

        } else {
            wp_send_json_error("Error: No files are provided");
                // The sent message will be a json object of array( 'success'   => false, 'data' => 'Error: No files are provided').
        }
    }

    public static function update_artwork_file_order() {
        $artwork = $_POST['artwork'];
        $file_id = $_POST['file_id'];
        $order   = $_POST['file_order'];

        $artwork = JC_Artwork::instance($artwork);
        if ($artwork->update_other_image_order($file_id, $order)){
            wp_send_json_success();
        } else {
            wp_send_json_error("Error: Failed to update file order on the server.");
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
    public static function delete_artwork_file() {
        global $logger;

        $artwork = $_POST['artwork'];
        $file_id = $_POST["file_id"];
        $nonce   = $_POST["nonce"];

        if (! wp_verify_nonce($nonce, "jc_upload_media") )
            wp_die("Operation is forbidden.");

        $artwork = JC_Artwork::instance($artwork);
        if ($artwork->delete_other_image($file_id)) {
            wp_send_json_success();
        } else {
            wp_send_json_error("Error: Failed to delete file on the server.");
        }
    }

    public static function set_artwork_as_featured() {
        $artwork = $_POST['artwork'];

        $artwork = JC_Artwork::instance($artwork);

        $artwork->is_featured = 1;

        global $logger;
        $logger->log_action(__FUNCTION__, $artwork->is_featured);

        if ($artwork->is_featured) {
            wp_send_json_success();
        } else {
            wp_send_json_error("Error: Failed to set artwork as featured.");
        }
    }

    public static function cancel_artwork_featured () {
        $artwork = $_POST['artwork'];

        $artwork = JC_Artwork::instance($artwork);

        $artwork->is_featured = 0;

        global $logger;
        $logger->log_action(__FUNCTION__, $artwork->is_featured);

        if (!$artwork->is_featured) {
            wp_send_json_success();
        } else {
            wp_send_json_error("Error: Failed to set artwork as featured.");
        }
    }


    /****************************
     **** ORDER's ADMIN AJAX ****
    /****************************/

    /**
     * Adds a customer service note
     * Sends back time of addition upon success.
     */
    public static function add_cs_note() {
        $order_id = $_POST["order_id"];
        $content = $_POST["note"];
        $author = $_POST["note_author"];
        $private = $_POST["private"];
        $private = $private === '0' ? false : true;

        $order = JC_Order::instance($order_id);
        $date = $order->add_cs_note($content, $author, $private);
        if ($date)
            wp_send_json_success($date);
        else
            wp_send_json_error();
    }

    /**
     * Expects @order_id and @note_id
     */
    public static function delete_cs_note() {
        $order_id = $_POST["order_id"];
        $note_id = $_POST["note_id"];

        $order = JC_Order::instance($order_id);
        $success = $order->delete_cs_note($note_id);
        if ($success)
            wp_send_json_success();
        else
            wp_send_json_error();
    }

    /**
     * Adds item to the order and returns the added item.
     *
     * Add item to the order details table.
     *
     * Expects @order_id and @artwork_id
     */
    public static function add_order_item() {
        $order = JC_Order::instance($_POST['order_id']);
        $artwork = $_POST['artwork_id'];
        $success = null;

        if ($order->has_item($artwork))
            $success = $order->increase_item_qty($artwork);
        else
            $success = $order->add_item($artwork);

        if ($success)
            wp_send_json_success();
        else
            wp_send_json_error();
    }

    /**
     * Delete this artwork all together from the order.
     *
     * Expects @order_id and @artwork_id
     */
    public static function delete_order_item() {
        $order = JC_Order::instance($_POST['order_id']);
        $artwork = $_POST['artwork_id'];
        $success = $order->delete_item($artwork);

        if ($success)
            wp_send_json_success();
        else
            wp_send_json_error();
    }


    public static function increase_order_item_qty() {
        $order = JC_Order::instance($_POST['order_id']);
        $artwork = $_POST['artwork_id'];

        $success = $order->increase_item_qty($artwork);
        if ($success)
            wp_send_json_success();
        else
            wp_send_json_error();
    }

    public static function reduce_order_item_qty() {
        $order = JC_Order::instance($_POST['order_id']);
        $artwork = $_POST['artwork_id'];

        $success = $order->reduce_item_qty($artwork);
        if ($success)
            wp_send_json_success();
        else
            wp_send_json_error();
    }

    /**
     * Retrives the updated shipping cost
     *
     * Expects @order_id
     */
    public static function update_shipping_cost() {
        $order = JC_Order::instance($_POST['order_id']);
        $new_shipping_cost = $order->get_shipping_cost();

        wp_send_json(array("updated_shipping_cost" => $new_shipping_cost));
    }


    /**
     * Called when looking for items to add for an order.
     * Search artworks by id or title
     *
     * Sends back array of Object {artwork_id, title}
     */
    public static function search_items() {
        $term = $_POST['term'];
        global $wpdb;

        $query = 'SELECT ID AS artwork_id, post_title AS title ';
        $query .= " FROM {$wpdb->prefix}posts JOIN {$wpdb->prefix}postmeta ON ID = post_id ";
        $query .= " WHERE post_type = 'artwork' AND post_status = 'publish' AND meta_key = '_stock' AND meta_value > 0 ";
        $query .= " AND ID LIKE '%%%d%%' OR post_title LIKE '%%%s%%'";
        $query = wpdb::prepare( $query, absint($term), $term);

        $items = $wpdb->get_results($query);
        wp_send_json(array("items" => $items));

    }

}

JC_AJAX::init();