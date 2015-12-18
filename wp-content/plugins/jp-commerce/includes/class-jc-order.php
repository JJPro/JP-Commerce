<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * JC Order
 *
 * Retrieves information about an order.
 *
 * @class       JC_Order
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 *
 * **** General Details ****
 * @property int    id
 * @property Object order_date. Object {year, month, day, hour, minute}
 * @property string order_status
 * @property int    customer
 * @property string customer_note
 *
 * **** Billing Details ****
 * @property Object billing_info
 * @property Object shipping_info
 *
 * **** Service Notes ****
 * @property array  service_notes. Array of Object: {content, date, author, private}
 *
 * **** Order Items ****
 * @property array  order_items.   Array of item objects: {item_id, quantity}
 *
 *
 *
 * TODO after this class:
 *
 * Action for emailing CS note to customer :
 *  "added_private_note", receives two arguments: note object and JC_Order object.
 *  add_action in notifications class
 *
 * Settings page for email templates - Notifications
 *
 *
 *
 */

class JC_Order 
{
    /**
     * @var $_instance. Caches the JC_Order object for retrieval of different metas from the same session.
     */
    private static $_instance = null;

    /**
     * @var Caches the Post object for easy access to some properties.
     */
    public $post = null;

    /**
     * @var Caches the post id for quick meta access
     */
    private $post_id = null;

    /**
     * @var Caches order id
     */
    public $id = null;

    /**
     * @var Caches query result from the orders table.
     */
    private $order_entry = null;

    private static $direct_order_meta                  = ['customer_note' ];
    private static $indirect_meta                      = ['service_notes', ];
    private static $indirect_post                      = [];
    private static $indirect_order_table_query         = ['order_date', 'order_status', 'customer',
                                                          'billing_info', 'shipping_info', ];
    /**
     * JC_Order creator.
     * @param $order mixed. int | Post | JC_Order.
     * @return JC_Order|null
     */
    public static function instance($order) {
        return self::$_instance ? self::$_instance : new self($order);
    }

    /**
     * Hook up some actions and filters for integration order behaviors with wordpress core.
     */
    public static function init() {

        /** insert new entry into wp_orders when new order is created. */
        add_action('save_post', function($post_id, $post, $update){
            // insert order record if this is a newly created order post
            if(!$update){
                // only create new record when this is not an update to existing order.
                global $wpdb;
                $affect = $wpdb->insert("{$wpdb->prefix}orders", array('post_id' => $post_id), array('%d'));
                global $logger;
                if ($affect !== 1) {
                    $logger->log_action("Warning", "Order entry already exists");
                }
                $logger->log_action("Debug", "Inserted new order entry");
            }

        }, 10, 3);


    }

    /**
     * JC_Order constructor.
     * @param $order mixed. int | Post | JC_Order.
     * Note: int is post# not order#
     */
    private function __construct($order)
    {
        if ( is_numeric($order) ) {
            $this->post_id = $order;
            $this->post = get_post($this->ID);
            $this->id = $this->get_order_id();
        }
        elseif ( $order instanceof WP_Post ) {
            $this->post = $order;
            $this->post_id   = $order->ID;
            $this->id = $this->get_order_id();
        } elseif ( $order instanceof JC_Order ) {
            $this->post = $order->post;
            $this->post_id   = $order->post_id;
            $this->id = $this->get_order_id();
        }
    }

    /**
     * @return int Gets the id of the order entry, aka order id.
     */
    private function get_order_id() {
        global $wpdb;
        $query = "SELECT id FROM {$wpdb->prefix}orders WHERE post_id = {$this->post_id}";
        return absint($wpdb->get_var($query));
    }

    /**
     * @param $name
     * @return mixed.
     */
    public function __get($name)
    {
        if ($name === 'service_notes') {
            return get_order_meta($this->id, '_customer_service_note');
        } elseif (in_array($name, self::$indirect_order_table_query)) {
            if (!$this->order_entry) {
                $this->order_entry = query_order_entry($this->id, self::$indirect_order_table_query);
            }
            if (!$this->order_entry)
                return null;
            else
                return maybe_unserialize($this->order_entry[$name]);
        } elseif ($name === 'order_items') {
            return $this->all_items();
        } elseif (in_array($name, self::$direct_order_meta)) {
            return get_order_meta($this->id, "_{$name}");
        } else {
            return null;
        }
    }

    /**
     * @param $content
     * @param $author
     * @param $private
     * @return mix false|int Returns time of addition on success
     */
    public function add_cs_note($content, $author, $private) {
        $note = new stdClass();
        $note->content = $content;
        $note->date    = time();
        $note->author  = $author;
        $note->private = $private;
        $note_id = add_order_meta($this->id, '_customer_service_note', $note);
        if ($note_id) {
            do_action("added_{$private}_note", $note, $this);
            return $note->date;
        }
        else
            return false;
    }

    /**
     * @param $note_id
     * @return int|false Number of rows affected or false on error.
     */
    public function delete_cs_note($note_id) {
        global $wpdb;
        return $wpdb->delete("{$wpdb->prefix}ordermeta", array('id' => $note_id), array('%d')); // more efficient than delete_order_meta()
    }

    /**
     * Checks whether this order includes given item.
     *
     * @param $artwork int|JC_Artwork
     * @return bool
     */
    public function has_item($artwork) {
        if (is_numeric($artwork))
            ;
        elseif ($artwork instanceof JC_Artwork)
            $artwork = $artwork->id;
        else
            return false;

        global $wpdb;
        $query = 'SELECT * FROM ' . $wpdb->prefix . 'order_details ';
        $query .= 'WHERE order = ' . $this->id . ' AND item = ' . $artwork;
        $affected_rows = $wpdb->query($query);

        return $affected_rows > 0 ? true : false;
    }

    /**
     * Retrieves all items
     *
     * @return array Array of Objects {item_id, quantity}.
     */
    public function all_items() {
        global $wpdb;
        $query = "SELECT item, quantity FROM {$wpdb->prefix}order_details WHERE order = {$this->id}";
        $items = $wpdb->get_results($query, OBJECT);

        return $items;
    }

    /**
     * @param $artwork int|JC_Artwork
     * @return bool|JC_Artwork Returns false on failure or JC_Artwork on success
     */
    public function add_item($artwork) {
        /**
         * check artwork inventory, if stock > 0,
         * then reduce stock
         * and  insert order-item record to order_details
         *
         * return inserted artwork on success, otherwise return false.
         */
        $artwork = new JC_Artwork($artwork);
        if ($artwork->stock > 0){
            $artwork->reduce_stock();

            global $wpdb;
            $rows = $wpdb->insert("{$wpdb->prefix}order_details", array('item' => $artwork->id, 'order' => $this->id, 'quantity' => 1));
            if ($rows > 0)
                return $artwork;
        }
        // return false if any of the above operation fails
        return false;
    }

    /**
     * @param $artwork int|JC_Artwork
     * @return bool  true on success, false on failure (for example, item not found for artwork)
     */
    public function delete_item($artwork) {
        // increase stock amount by released amount from this order
        $artwork = new JC_Artwork($artwork);
        $qty = $this->get_item_qty($artwork);
        if ($qty > 0){
            $artwork->increase_stock($qty);
        }
        // remove record from the order details table.
        global $wpdb;
        $wpdb->delete("{$wpdb->prefix}order_details", array('order' => $this->id, 'item' => $artwork->id));
    }

    public function increase_item_qty($artwork) {
        $artwork = new JC_Artwork($artwork);

        // check stock availability
        if ($artwork->stock > 0){
            // reduce stock amount if available
            $artwork->reduce_stock();
            // update order_details record
            global $wpdb;
            $wpdb->update("{$wpdb->prefix}order_details", array('quantity' => 'quantity + 1'), array('order' => $this->id, 'item' => $artwork->id),
                '%d', '%d');
            global $logger;
            $logger->log_action(__FUNCTION__, 'If item quantity becomes zero in the table record, It might be $wpdb->update() doesn\'t work well in this special situation, alternative, we can use raw query. Same thing for reduce_item_qty() method. ');
        }
    }

    public function reduce_item_qty ($artwork) {
        $artwork = new JC_Artwork($artwork);

        // increase artwork stock
        $artwork->increase_stock();
        // check quantity of this item in the order
        $qty = $this->get_item_qty($artwork);
        // if quantity > 0, update record amt by 1
        if ($qty > 0){
            global $wpdb;
            $wpdb->update("{$wpdb->prefix}order_details", array('quantity' => 'quantity - 1'), array('order' => $this->id, 'item' => $artwork->id),
                '%d', '%d');
        }
        // otherwise, delete item from the order.
        else {
            $this->delete_item($artwork);
        }
    }

    /**
     * @param $artwork int|JC_Artwork
     * @return int Quantity of the asked item in this order.
     */
    public function get_item_qty($artwork) {
        $item_id = is_numeric($artwork) ? $artwork : $artwork->id;

        global $wpdb;
        $query = "SELECT quantity FROM {$wpdb->prefix}order_details WHERE order={$this->id} AND item={$item_id}";
        return absint($wpdb->get_var($query));
    }

    /**
     * Calculates shipping cost on the fly
     */
    public function get_shipping_cost() {
        $destination = $this->shipping_info;
        $items = $this->all_items();
        $total_shipping_cost = 0;

        foreach ($items as $item) {
            $total_shipping_cost += $item->artwork->get_shipping_cost($destination) * $item->qty;
        }

        return $total_shipping_cost;
    }
}