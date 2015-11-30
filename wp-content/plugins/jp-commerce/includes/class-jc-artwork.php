<?php
/**
 * JC Artwork
 *
 * Artwork class that handles individual artwork data.
 *
 * @class       JC_Artwork
 * @var         WP_POST|int|JC_Artwork
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 *
 * @property    string $artwork_type
 * @property    string $featured_image
 * @property    string $featured_image_thumbnail
 * @property    array  $other_images
 * @property    array  $other_images_thumbnails
 * @property    string $wechat_image
 * @property    string $artist
 * @property    string $make_date
 * @property    array|false  $dimensions
 * @property    float  $length
 * @property    float  $width
 * @property    float  $height
 * @property    float  $weight
 * @property    bool   $for_sale
 * @property    float  $price
 * @property    int    $stock
 * @property    string $description
 * @property    bool   $visibility
 * @property    float  $commission_rate
 * @property    float  $commission_fee
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JC_Artwork 
{
    private static $__instance = null;

    /**
     * The artwork (post) ID.
     *
     * @var int
     */
    public $id = 0;

    /**
     * Stores post data
     *
     * @var $post WP_Post
     */
    public $post = null;

    /**
     * @var string
     */
    public $artwork_type = null;

    /**
     * @var string url of the featured image
     */
    public $featured_image = null;


    /**
     * JC_Artwork constructor.
     * Gets the post object and sets the ID for the loaded artwork.
     *
     * @param $artwork
     */
    public function __construct( $artwork )
    {

    }

    /**
     * Retrieve similar artworks ids
     * Artworks of the same categories from other artists
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