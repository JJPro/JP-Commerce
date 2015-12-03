<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * JC Artwork
 *
 * Retrieves all artwork data about an Artwork.
 *
 * @class       JC_Artwork
 * @var         WP_POST|int|JC_Artwork
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 *
 * @property    string artwork_type
 * @property    string $featured_image
 * @property    string $featured_image_thumbnail
 * @property    array  $other_images
 * @property    array  $other_images_thumbnails
 * @property    string $wechat_image
 * @property    string $artist
 * @property    string $artist_id
 * @property    string $make_date
 * @property    Object $dimensions
 * @property    Object $dimensions_with_frame
 * @property    float  $shipping_weight
 * @property    bool   $showoff_only
 * @property    float  $price
 * @property    float  $price_with_frame
 * @property    int    $stock
 * @property    string $description
 * @property    float  $commission_rate
 * @property    float  $commission_fee
 * @property    float  $artist_profit
 * @property    Object $ship_from
 */


class JC_Artwork 
{

    private $_instance = null;


    /**
     * The artwork (post) ID.
     *
     * @var int
     */
    public $ID = 0;

    /**
     * Stores post data
     *
     * @var $post WP_Post
     */
    public $post = null;

    /**
     * Caches Dimensions array
     *
     * @var $dimensions array
     */
    private $dimensions = null;

    /**
     * Caches dimensions_with_frame array
     */

    /**
     * properties from direct access to post meta
     */
    private static $direct_post_metas = ['make_date', 'description', 'weight', 'price', 'stock'];

    /**
     * properties getting through methods
     */
    private static $through_methods = ['length', 'width', 'height', 'type', 'showoff_only', 'artist'];

    /**
     * Properties getting from the WP_Post object
     */
    private static $through_post = ['post_date', 'name', 'artist_id'];


    /**
     * JC_Artwork constructor.
     * Gets the post object and sets the ID for the loaded artwork.
     *
     * @param $artwork int|Post|Artwork
     */
    public function __construct( $artwork )
    {
        if ( is_numeric($artwork) ) {
            $this->ID = absint($artwork);
            $this->post = get_post($this->ID);
        }
        elseif ( $artwork instanceof WP_Post ) {
            $this->ID = $artwork->ID;
            $this->post = $artwork;

        } elseif ( $artwork instanceof JC_Artwork ) {
            $this->ID = $artwork->ID;
            $this->post = $artwork->post;
        }
    }

    /**
     * @param $name property name
     * @return mixed
     */
    public function __get($name)
    {
        if ( in_array($name, self::$direct_post_metas) ){
            return get_post_meta($this->ID, '_' . $name, true);
        }
        if ( in_array($name, self::$through_methods) ) {
            return call_user_func( array($this, 'get_' . $name) );
        }
        if ( in_array($name, self::$through_post) ) {
            // map Artwork property name to Post property name
            $property_map = array(
                        'name'  => 'post_title',
                        'artist_id' => 'post_author'
                        );
            return $this->post->$property_map[$name];
        }
    }

    /**
     * Retrieves length of artwork
     * @return int
     */
    private function get_length() {
        if (!$this->dimensions){
            $this->dimensions = get_post_meta($this->ID, '_dimensions', true);
        }
        return absint(apply_filters('jp_commerce_artwork_length', @$this->dimensions['length']));
    }

    /**
     * Retrieves width of artwork
     * @return int
     */
    private function get_width() {
        if (!$this->dimensions){
            $this->dimensions = get_post_meta($this->ID, '_dimensions', true);
        }
        return absint(apply_filters('jp_commerce_artwork_width', @$this->dimensions['width']));
    }

    /**
     * Retrieves height of artwork
     * @return int
     */
    private function get_height() {
        if (!$this->dimensions){
            $this->dimensions = get_post_meta($this->ID, '_dimensions', true);
        }
        return absint(apply_filters('jp_commerce_artwork_height', @$this->dimensions['height']));
    }

    private function get_type() {
        return wp_get_object_terms($this->ID, 'artwork_type')[0];
    }

    private function get_showoff_only() {
        $showoff_only = get_post_meta($this->ID, '_showoff_only', true); // 1 or 0
        return $showoff_only === 1;
    }

    /**
     * Gets artist (name)
     */
    private function get_artist() {
        $author_id = $this->artist_id;
        return get_the_author_meta('display_name', $author_id);
    }

    /**
     * Retrieve similar artworks ids
     * Artworks of the same categories or with the same tags (from other artists)
     */
    public function get_similar_artworks_ids_from_other_artists() {

    }

    /**
     * Retrieve other artwork ids from the same artist
     */
    public function get_other_artwork_ids_from_this_artist() {

    }

    /**
     * Increase stock level
     *
     * @param int $amount Amount to increase by. Default 1.
     * @return int new stock level.
     */
    public function increase_stock( $amount = 1 ) {

    }

    /**
     * Reduce stock by 1
     *
     * @param int $amount Amount to reduce by. Default 1.
     * @return int new stock level.
     */
    public function reduce_stock( $amount = 1 ) {

    }
}