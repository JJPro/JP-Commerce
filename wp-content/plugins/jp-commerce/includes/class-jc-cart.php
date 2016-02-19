<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * JC Cart
 *
 * Retrieves information about an cart.
 *
 * @class       JC_Cart
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
 */