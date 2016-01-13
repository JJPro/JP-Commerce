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

        // register post types and taxonomies
        add_action( 'init', array( __CLASS__, 'register_taxonomies' ) );
        add_action( 'init', array( __CLASS__, 'register_post_types' ) );

        self::artworks_list_table();
    }

    public static function artworks_list_table() {
        // define columns
        add_filter( 'manage_artwork_posts_columns', function( $columns) {
            $columns = array(
                'cover_image' => '<span class="icon-picture-o tiptip description" title="Cover Image"></span>',
                'title' => 'Name',
                'stock' => 'Stock',
                'price' => 'Price',
                'taxonomy-artwork_type' => 'Artwork Type',
                'featured' => '<span class="icon-star-1 tiptip description" title="Featured"></span>',
                'date'  => 'Date',
                'views' => '<span class="icon-eye tiptip description" title="Views"></span>',
                'favorites' => '<span class="icon-heart tiptip description" title="Favorites"></span>',
            );
            return $columns;
        });

        // get column values
        add_action( 'manage_artwork_posts_custom_column', function( $col_name, $post_id ) {

            $artwork = JC_Artwork::instance($post_id);
            switch ($col_name) {
                case 'cover_image' :
                    echo '<a href="'.get_edit_post_link($post_id).'"><img src="' . $artwork->wechat_image . '" /></a>';
                    break;
                case 'price' :
                    if ($artwork->is_for_sale && $artwork->price_of_artwork > 0)
                        echo '$ '. $artwork->price_of_artwork;
                    else
                        echo '0';
                    break;
                case 'featured' :
                    $is_featured = $artwork->is_featured;
                    printf('<span class="%s" data-artwork="%d" data-is_featured="%d"></span>',
                        $is_featured ? 'icon-star-1' : 'icon-star-o',
                        $artwork->id,
                        $is_featured ? 1 : 0
                        );
                    break;
                case 'stock' :
                case 'views' :
                case 'favorites' :
                    $amt = $artwork->$col_name;
                    if ($amt)
                        echo $amt;
                    else
                        echo '0';
                    break;

            }
        }, 10, 2 );
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

        register_post_type('order',
            array(
                'labels'                => array(
                    'name'              => __('Orders', 'jp_commerce'),
                    'singular_name'     => __('Order', 'jp_commerce'),
                    'add_new_item'      => __('Add New Order', 'jp_commerce'),
                    'edit_item'         => __('Edit Order', 'jp_commerce'),
                    'new_item'          => __('New Order', 'jp_commerce'),
                    'all_items'         => __('All Orders', 'jp_commerce'),
                    'view_item'         => __('View Order', 'jp_commerce'),
                    'update_item'       => __('Update Order', 'jp_commerce'),
                    'search_items'      => __('Search Orders', 'jp_commerce'),
                    'not_found'         => __('No orders found', 'jp_commerce'),
                    'not_found_in_trash'=> __('No orders found in Trash', 'jp_commerce'),
                    'menu_name'         => __('Orders', 'jp_commerce')
                ),
                'public'                => false,
                'publicly_queryable'    => false,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'hierarchical'          => false,
                'query_var'             => false,
//                'menu_icon'             => 'dashicons-admin-customizer',
                'has_archive'           => false,
                'rewrite'               => false,
                'capabilities_type'     => 'order',
                'capabilities'          => array(
                    // meta caps ( don't assign to roles )
                    'edit_post'         => 'edit_order',
                    'read_post'         => 'read_order',
                    'delete_post'       => 'delete_order',

                    // primitive/meta caps
                    'create_posts'      => 'manage_orders',

                    // primitive caps used outside of map_meta_cap()
                    'edit_posts'        => 'manage_orders',
                    'edit_others_posts' => 'manage_orders',
                    'publish_posts'     => 'manage_orders',
                    'read_private_posts'=> 'read',

                    // primitive caps used inside of map_meta_cap()
                    'read'                      => 'read',
                    'delete_posts'              => 'manage_orders',
                    'delete_private_posts'      => 'manage_orders',
                    'delete_published_posts'    => 'manage_orders',
                    'delete_others_posts'       => 'manage_orders',
                    'edit_private_posts'        => 'manage_orders',
                    'edit_published_posts'      => 'manage_orders'
                ),
                'map_meta_cap'          => true,
                'supports'              => array('')
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