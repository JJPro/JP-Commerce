<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies, and registers a few default tax terms for
 *
 * @class       JC_Post_Types
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}



class JC_Post_Types
{
    public static function init() {
        add_action( 'init', array( __CLASS__, 'register_taxonomies' ) );
        add_action( 'init', array( __CLASS__, 'register_post_types' ) );
    }

    public static function register_post_types(){
        if ( post_type_exists('promotion'))
            return;

        register_post_type('promotion',
            array(
                'labels'                => array(
                    'name'              => __('Promotions', 'jp_commerce'),
                    'singular_name'     => __('Promotion', 'jp_commerce'),
                    'add_new_item'      => __('Add New Promotion', 'jp_commerce'),
                    'edit_item'         => __('Edit Promotion', 'jp_commerce'),
                    'new_item'          => __('New Promotion', 'jp_commerce'),
                    'all_items'         => __('All Promotions', 'jp_commerce'),
                    'view_item'         => __('View Promotion', 'jp_commerce'),
                    'search_items'      => __('Search Promotions', 'jp_commerce'),
                    'not_found'         => __('No promotions found', 'jp_commerce'),
                    'not_found_in_trash'=> __('No promotions found in Trash', 'jp_commerce'),
                    'menu_name'         => __('Promotions', 'jp_commerce')
                ),
                'public'                => false,
                'publicly_queryable'    => false,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'hierarchical'          => false,
                'query_var'             => false,
                'menu_icon'             => 'dashicons-megaphone',
                'has_archive'           => false,
                'rewrite'               => false,
                'capabilities_type'     => 'promotion',
                'capabilities'          => array(
                        // meta caps ( don't assign to roles )
                        'edit_post'         => 'edit_promotion',
                        'read_post'         => 'read_promotion',
                        'delete_post'       => 'delete_promotion',

                        // primitive/meta caps
                        'create_posts'      => 'manage_promotions',

                        // primitive caps used outside of map_meta_cap()
                        'edit_posts'        => 'manage_promotions',
                        'edit_others_posts' => 'manage_promotions',
                        'publish_posts'     => 'manage_promotions',
                        'read_private_posts'=> 'read',

                        // primitive caps used inside of map_meta_cap()
                        'read'                      => 'read',
                        'delete_posts'              => 'manage_promotions',
                        'delete_private_posts'      => 'manage_promotions',
                        'delete_published_posts'    => 'manage_promotions',
                        'delete_others_posts'       => 'manage_promotions',
                        'edit_private_posts'        => 'manage_promotions',
                        'edit_published_posts'      => 'manage_promotions'
                    ),
                'map_meta_cap'          => true,
                'supports'              => array('title', 'editor')
            )
        );

        register_post_type('artwork',
            array(
                'labels'                => array(
                    'name'              => __('Artworks', 'jp_commerce'),
                    'singular_name'     => __('Artwork', 'jp_commerce'),
                    'add_new_item'      => __('Add New Artwork', 'jp_commerce'),
                    'edit_item'         => __('Edit Artwork', 'jp_commerce'),
                    'new_item'          => __('New Artwork', 'jp_commerce'),
                    'all_items'         => __('All Artworks', 'jp_commerce'),
                    'view_item'         => __('View Artwork', 'jp_commerce'),
                    'update_item'       => __('Update Artwork', 'jp_commerce'),
                    'search_items'      => __('Search Artworks', 'jp_commerce'),
                    'not_found'         => __('No artworks found', 'jp_commerce'),
                    'not_found_in_trash'=> __('No artworks found in Trash', 'jp_commerce'),
                    'menu_name'         => __('Artworks', 'jp_commerce')
                ),
                'public'                => true,
                'publicly_queryable'    => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'hierarchical'          => false,
                'query_var'             => true,
                'menu_icon'             => 'dashicons-admin-customizer',
                'has_archive'           => true,
                'rewrite'               => true,
                'map_meta_cap'          => true,
                'capabilities_type'     => 'artwork',
                'capabilities'          => array(
                    // meta caps ( don't assign to roles )
                    'edit_post'         => 'edit_artwork',
                    'read_post'         => 'read_artwork',
                    'delete_post'       => 'delete_artwork',

                    // primitive/meta caps
                    'create_posts'      => 'create_artworks',

                    // primitive caps used outside of map_meta_cap()
                    'edit_posts'        => 'edit_artworks',
                    'edit_others_posts' => 'manage_artworks',
                    'publish_posts'     => 'manage_artworks',
                    'read_private_posts'=> 'read',

                    // primitive caps used inside of map_meta_cap()
                    'read'                      => 'read',
                    'delete_posts'              => 'edit_artworks',
                    'delete_private_posts'      => 'edit_artworks',
                    'delete_published_posts'    => 'manage_artworks',
                    'delete_others_posts'       => 'manage_artworks',
                    'edit_private_posts'        => 'edit_artworks',
                    'edit_published_posts'      => 'edit_artworks'
                ),
                'supports'              => array('title')
            )
        );
    }

    public static function register_taxonomies() {

        if ( taxonomy_exists( 'artwork_type' ) ) {
            return;
        }

        include_once( 'admin/meta-boxes/class-jc-meta-box-artwork-type.php' );
        register_taxonomy('artwork_type',
            'artwork',
            array(
                'labels'            => array(
                    'name'          => __('Artwork Types', 'jp_commerce'),
                    'singular_name' => __('Artwork Type', 'jp_commerce'),
                    'search_items'  => __('Search Artwork Types', 'jp_commerce'),
                    'popular_items' => __('Popular Artwork Types', 'jp_commerce'),
                    'all_items'     => __('All Artwork Types', 'jp_commerce'),
                    'edit_item'     => __('Edit Artwork Type', 'jp_commerce'),
                    'view_item'     => __('View Artwork Type', 'jp_commerce'),
                    'update_item'   => __('Update Artwork Type', 'jp_commerce'),
                    'add_new_item'  => __('Add New Artwork Type', 'jp_commerce'),
                    'new_item_name' => __('New Artwork Type Name', 'jp_commerce'),
                    'not_found'     => __('No artwork types found', 'jp_commerce'),
                    'no_terms'      => __('No artwork types', 'jp_commerce')
                ),
                'description'       => 'Taxonomy to classify artworks',
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => true,
                'show_tagcloud'     => false,
                'show_admin_column' => true,
                'meta_box_cb'       => array('JC_Meta_Box_Artwork_Type', 'output'),
                'capabilities'      => array(
                    'manage_terms'  => 'manage_artwork_types',
                    'edit_terms'    => 'manage_artwork_types',
                    'delete_terms'  => 'manage_artwork_types',
                    'assign_terms'  => 'edit_artworks',
                )
            )
        );

        register_taxonomy('artwork_tag',
            'artwork',
            array(
                'labels'            => array(
                    'name'          => __('Artwork Tags', 'jp_commerce'),
                    'singular_name' => __('Artwork Tag', 'jp_commerce'),
                    'search_items'  => __('Search Artwork Tags', 'jp_commerce'),
                    'popular_items' => __('Popular Artwork Tags', 'jp_commerce'),
                    'all_items'     => __('All Artwork Tags', 'jp_commerce'),
                    'edit_item'     => __('Edit Artwork Tag', 'jp_commerce'),
                    'view_item'     => __('View Artwork Tag', 'jp_commerce'),
                    'update_item'   => __('Update Artwork Tag', 'jp_commerce'),
                    'add_new_item'  => __('Add New Artwork Tag', 'jp_commerce'),
                    'new_item_name' => __('New Artwork Tag Name', 'jp_commerce'),
                    'not_found'     => __('No artwork tags found', 'jp_commerce'),
                    'no_terms'      => __('No artwork tags', 'jp_commerce')
                ),
                'description'       => 'Tags will help find artworks while searching',
                'public'            => true,
                'hierarchical'      => false,
                'show_ui'           => true,
                'show_in_menu'      => false,
                'show_in_nav_menus' => false,
                'show_tagcloud'     => true,
                'show_admin_column' => true,
                'meta_box_cb'       => array('JC_Meta_Box_Artwork_Tag', 'output'),
                'capabilities'      => array(
                    'manage_terms'  => 'edit_artworks',
                    'edit_terms'    => 'edit_artworks',
                    'delete_terms'  => 'edit_artworks',
                    'assign_terms'  => 'edit_artworks',
                )
            )
        );

        include_once( 'admin/meta-boxes/class-jc-meta-box-tax-select-style.php');
        register_taxonomy('promotion_type',
            'promotion',
            array(
                'labels'            => array(
                    'name'          => __('Promotion Types', 'jp_commerce'),
                    'singular_name' => __('Promotion Type', 'jp_commerce'),
                    'search_items'  => __('Search Promotion Types', 'jp_commerce'),
                    'popular_items' => __('Popular Promotion Types', 'jp_commerce'),
                    'all_items'     => __('All Promotion Types', 'jp_commerce'),
                    'edit_item'     => __('Edit Promotion Type', 'jp_commerce'),
                    'view_item'     => __('View Promotion Type', 'jp_commerce'),
                    'update_item'   => __('Update Promotion Type', 'jp_commerce'),
                    'add_new_item'  => __('Add New Promotion Type', 'jp_commerce'),
                    'new_item_name' => __('New Promotion Type Name', 'jp_commerce'),
                    'not_found'     => __('No promotion types found', 'jp_commerce'),
                    'no_terms'      => __('No promotion types', 'jp_commerce')
                ),
                'description'       => 'Taxonomy to classify promotions',
                'public'            => false,
                'hierarchical'      => false,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => false,
                'show_tagcloud'     => false,
                'show_admin_column' => true,
                'meta_box_cb'       => array('JC_Meta_Box_Tax_Select_Style', 'output'),
                'capabilities'      => array(
                    'manage_terms'  => 'manage_promotion_types',
                    'edit_terms'    => 'manage_promotion_types',
                    'delete_terms'  => 'manage_promotion_types',
                    'assign_terms'  => 'edit_promotions',
                )
            )
        );
    }
}

JC_Post_Types::init();