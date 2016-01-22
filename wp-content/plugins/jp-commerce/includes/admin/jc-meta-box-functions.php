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

/*
    ====================
        ARTWORK
    ====================
 */
/**
 * Prints the artwork category input field as select menu
 * @param $artwork JC_Artwork
 */
function artwork_category_field( $artwork ) {

    $cat = get_the_terms( $artwork->id, 'artwork_type' );
    if ($cat && !is_wp_error($cat)) {
        $cat = $cat[0]->term_id;
    } else {
        $cat = -1;
    }

    wp_dropdown_categories( "hide_empty=0&taxonomy=artwork_type&hierarchical=1&orderby=NAME&name=artwork-category&selected={$cat}&hierarchical=1&show_option_none= ");
}

/**
 * Prints 'required' if this field is required.
 * @param $artwork JC_Artwork
 * @param $field str
 */
function artwork_field_required( $artwork, $field ) {
    if ($artwork->is_required($field)) {
        echo 'required';
    }
}

function artwork_materials_field( $artwork ) {
    JC_Artwork_Materials_Field::output($artwork);
}