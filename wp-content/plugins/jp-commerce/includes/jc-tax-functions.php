<?php
/**
 * 
 *
 * Functions related to taxonomies and categories.
 *
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


/**
 * Retrieve all artwork types in hierarchical structure
 *
 * @return array. Array of object (@var int $term_id, @var string $name, @var array $children)
 */
function jc_get_all_artwork_types() {
    $base_types = jc_get_children_artwork_types(0);
    foreach ($base_types as $type) {
        integrate_children_into_parent($type);
    }
    return $base_types;
}

/**
 * @private_function
 *
 * @param Object $parent
 * @return array. Array of object (@var int $term_id, @var string $name, @var array $children)
 */
function integrate_children_into_parent( $parent ){
    $parent->children = jc_get_children_artwork_types( $parent->term_id );
    foreach( $parent->children as $child ) {
        integrate_children_into_parent( $child );
    }
}

/**
 * Retrieve children artwork types of a artwork type
 *
 * @param int $parent_id
 * @return array. Array of object (@var int $term_id, @var string $name)
 */
function jc_get_children_artwork_types( $parent_id ) {
    $artwork_types = get_terms('artwork_type', array(
        'hide_empty'    => false,
        'parent'      => $parent_id,
    ));
    return $artwork_types;
}

/**
 * Retrieve endpoint artwork types
 * @return array.
 */
function jc_get_childless_artwork_types() {
    $leaf_types = get_terms('artwork_type', array(
        'hide_empty'    => false,
        'childless'     => true
    ));
    return $leaf_types;
}

/**
 * Retrieve all parents' ids
 *
 * @param int $term_id
 * @return array.
 */
function get_all_parents_ids($term_id) {
    $parents = [];
    $parent = wp_get_term_taxonomy_parent_id($term_id, 'artwork_type');
    while ($parent != 0) {
        $parents[] = $parent;
        $parent = wp_get_term_taxonomy_parent_id($parent, 'artwork_type');
    }
    return array_reverse($parents);
}