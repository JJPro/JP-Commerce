<?php
/**
 * JJProCommerce Meta Box Functions
 *
 * @author  JJPro Technology LLC.
 * @version 1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Output a text input box.
 *
 * @param array $field
 */
function jc_wp_text_input( $field ) {
    global $thepostid, $post;
    $thepostid              = $thepostid ? $thepostid : $post->ID;
    $field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
    $field['class']   = isset( $field['class'] ) ? $field['class'] : 'short';
    $field['style']   = isset( $field['style'] ) ? $field['style'] : '';
    $field['value']   = isset( $field['value'] ) ? $field['value'] : '';
    $field['name']    = isset( $field['name'] ) ? $field['name'] : '';
    $field['type']    = isset( $field['type'] ) ? $field['type'] : '';
    $data_type        = empty( $field['data_type'] ) ? '' : $field['data_type'];

    switch ( $data_type ) {
        case 'price' :
            $field['class'] .= ' jc-input-price';
            $field['value']  = 2;
    }
}