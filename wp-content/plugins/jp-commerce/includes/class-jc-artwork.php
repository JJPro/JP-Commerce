<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'ARTWORK_DIMENSION_UNIT_CM', 'cm');
define( 'ARTWORK_DIMENSION_UNIT_INCH', 'inches');
define( 'JC_SHIPPING_WEIGHT_UNIT_POUNDS', 'pounds');
define( 'JC_SHIPPING_WEIGHT_UNIT_KG', 'kg');

/**
 * JC Artwork
 *
 * Retrieves all artwork data about an Artwork.
 * Note: This class works both in singleton pattern(JC_Artwork::instance($artwork)) and create normal new object (new JC_Artwork($artwork)).
 * Use singleton when you requesting for properties for the same artwork in different places during a single request.
 * Use a new object when you are creating more than one artworks on one page.
 *
 * @class       JC_Artwork
 * @var         WP_POST|int|JC_Artwork
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 *
 * **** General ****
 * @property-read   int    $id
 * @property-read   int    $artist
 * @property        string $name
 * @property-read   date   $post_date
 * @property        int    $is_featured 1|0
 *
 * **** Artwork Type ****
 * @property        string $artwork_type
 *
 * **** Artwork Details ****
 * @property        date   $date_created
 * @property-read   Object dimensions   Object {width, height, depth, unit}
 * @method'         void   set_dimensions($l, $w, $h, $unit)
 * @property        Array  materials    [str]
 * @property        string $description
 *
 * @property        int    $status. 1-> for sale, 0-> not for sale.
 * @method'         is_for_sale(): null -> bool
 * @property        int    $have_frame  1|0
 * @property        int    $is_frame_optional 1|0
 * @property-read   Object $shipping_dimensions
 * @property        float  $shipping_weight
 * @property        string $shipping_weight_unit
 * @method'         void   set_shipping_dimensions($l, $w, $h, $unit)
 * @property        float  $price_of_artwork
 * @property        float  $price_of_frame
 * @property        int    $stock
 * @property-read   float|Object  $commission_fee
 * @property-read   float|Object  $artist_profit
 * @property-read   Object shipping_from {addr1, addr2, city, state, country, zip, phone}
 * @method'         void   set_shipping_from($addr1, $addr2, $city, $state, $country, $zip, phone)
 * @method'         int    increase_stock($amt=1)
 * @method'         int    reduce_stock($amt=1)
 * @method'         is_required($property) : str -> bool
 * @method'         get_status() : null -> str
 *
 * **** Pictures ****
 * @property         string $cover_image
 * @property-read    string $cover_thumbnail
 * @property-read    array  $other_images
 * @property-read    array  $other_images_thumbnails
 * @property-read    array  $other_images_dropzone_thumbnails
 * @property-read    string $wechat_image
 * @method'          bool   add_other_image($tmp, $order).
 * @method'          bool   update_image_order($uuid, $new_order)
 * @method'          bool   delete_other_image($uuid)
 *
 * **** User Statistics ****
 * @property         int    $views
 * @property         int    $favorites
 *
 *
 *
 * @method' @static  array  featured_artworks($n = 5)
 * @method' @static  array  new_artworks($n = 5)
 * @method' @static  array  trending_artworks($n = 5)
 *
 *
 *
 *  For dealing with images:
 *      Let javascript to determine if featured image and other images needs update,
 *      Let javascript tell us which images to delete.
 *      Assign each image an uuid and sends the id along with image data to the ajax request
 *      When saving images, we test if the image has a uuid bind to it, if not, we know this is a new image
 *      Saving Images to server:
 *          If image doesn't have uuid,
 *              then this is a new image,
 *              generate a unique name for it,
 *              save it to respective directory,
 *              add to artwork meta
 *      Requesting thumbnails from server:
 *
 *      Deleting an image:
 */

class JC_Artwork 
{

    private static $_instance = null;

    private static $wechat_width = 300, $wechat_height = 300;
    private static $thumbnail_width = 300;

    public static $required_properties = ['name', 'description', 'date_created', 'artwork_type', 'materials', 'dimensions', 'stock', 'shipping_dimensions',
                                            'shipping_weight', 'price', 'shipping_from'];


    /**
     * The artwork (post) id.
     */
    public $id = 0;

    /**
     * Stores post data
     *
     * @var $post WP_Post
     */
    public $post = null;

    /**
     * properties from direct access to post meta
     */
    private static $direct_post_meta = ['date_created', 'dimensions', 'description',
                                         'status', 'have_frame', 'is_frame_optional',
                                         'shipping_weight', 'shipping_weight_unit', 'shipping_dimensions', 'price_of_artwork', 'price_of_frame', 'stock', 'shipping_from',
                                         'cover_image', 'is_featured', 'views', 'favorites'];


    private static $through_methods = ['artwork_type', 'materials', 'commission_fee', 'artist_profit', 'wechat_image', 'other_images', 'other_images_thumbnails', 'cover_thumbnail', 'other_images_dropzone_thumbnails'];

    private static $direct_post_property = ['post_date', ];
    private static $indirect_post_property = ['artist', 'name'];


    /**
     * Get the single instance when requesting information about the same artwork during one request
     *
     * @param $artwork int|Post|Artwork
     * @return JC_Artwork
     */
    public static function instance( $artwork ) {
        if ( self::$_instance ) {
            if ( is_numeric($artwork) && self::$_instance->id === $artwork)
                return self::$_instance;
            elseif ( $artwork instanceof WP_Post && self::$_instance->id === $artwork->ID )
                return self::$_instance;
            elseif ( $artwork instanceof JC_Artwork && $artwork->id === self::$_instance->id )
                return self::$_instance;
        }

        self::$_instance = new self($artwork);
        return self::$_instance;

    }

    /**
     * JC_Artwork constructor.
     * Gets the post object and sets the id for the loaded artwork.
     *
     * @param $artwork int|Post|Artwork
     */
    private function __construct( $artwork )
    {
        if ( is_numeric($artwork) ) {
            $this->id = absint($artwork);
            $this->post = get_post($this->id);
            if ( !$this->views ) {
                $this->views = 0;
            }
        }
        elseif ( $artwork instanceof WP_Post ) {
            $this->id = $artwork->ID;
            $this->post = $artwork;

        } elseif ( $artwork instanceof JC_Artwork ) {
            $this->id = $artwork->id;
            $this->post = $artwork->post;
        }
    }

    /**
     * @param $name property name
     * @return mixed
     */
    public function __get($name)
    {
        if ( in_array($name, self::$direct_post_meta) ){
            return get_post_meta($this->id, '_' . $name, true);
        }
        elseif ( in_array($name, self::$through_methods) ) {
            return call_user_func( array($this, 'get_' . $name) );
        }
        elseif ( in_array($name, self::$direct_post_property) ) {
            return $this->post->$name;
        }
        elseif ( in_array($name, self::$indirect_post_property) ) {
            // map Artwork property name to Post property name
            $property_map = array(
                        'name'  => 'post_title',
                        'artist' => 'post_author'
                        );
            return $this->post->$property_map[$name];
        } else {
            return null;
        }
    }

    /**
     * @param $name property name
     * @param $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        if ( in_array($name, self::$direct_post_meta) ){
            if ( in_array($name, ['dimensions', 'shipping_dimensions', 'shipping_from', 'cover_image']) ){
                echo "You are doing it wrong. Please use set_{$name}() method.";
                return;
            } else {
                return update_post_meta($this->id, "_{$name}", $value);
            }
        }
        elseif ( in_array($name, self::$through_methods) ) {
            // readonly properties
            if ( in_array($name, ['commission_fee', 'artist_profit', 'wechat_image', 'other_images_thumbnails', 'cover_thumbnail', 'other_images_dropzone_thumbnails']) ) {
                echo "{$name} is readonly";
                return;
            }
            // use special methods
            elseif ( in_array($name, ['other_images']) ) {
                echo "{$name} is readonly. To update other images please use relative methods.";
                return;
            }
            // call set_ method
            else {
                return call_user_func( array($this, "set_{$name}"), $value );
            }
        }
        elseif ( in_array($name, self::$direct_post_property) ) {
            echo "{$name} is readonly";
            return;
        }
        elseif ( in_array($name, self::$indirect_post_property) ) {
            echo "{$name} is readonly";
            return;
        } else {
            echo "{$name} is not a property of this object.";
            return null;
        }
    }

    /**
     * @param $w
     * @param $h
     * @param $d
     * @param $unit Constant ARTWORK_DIMENSION_UNIT_CM | ARTWORK_DIMENSION_UNIT_INCH
     * @return bool|int
     */
    public function set_dimensions($w, $h, $d, $unit) {
        $dimensions = new stdClass();
        $dimensions->width  = $w;
        $dimensions->height = $h;
        $dimensions->depth  = $d;
        $dimensions->unit = $unit;

        return update_post_meta($this->id, "_dimensions", $dimensions);
    }

    /**
     * @param $w
     * @param $h
     * @param $d
     * @param $unit Constant ARTWORK_DIMENSION_UNIT_CM | ARTWORK_DIMENSION_UNIT_INCH
     * @return bool|int
     */
    public function set_shipping_dimensions($w, $h, $d, $unit) {
        $dimensions = new stdClass();
        $dimensions->width  = $w;
        $dimensions->height = $h;
        $dimensions->depth  = $d;
        $dimensions->unit   = $unit;

        return update_post_meta($this->id, "_shipping_dimensions", $dimensions);
    }

    public function set_shipping_from($addr1, $addr2, $city, $state, $country, $zip, $phone) {
        $addr_array = array(
            'address1' => $addr1,
            'address2' => $addr2,
            'city'     => $city,
            'state'    => $state,
            'country'  => $country,
            'zip'      => $zip,
            'phone'    => $phone,
        );

        $addr = (object)$addr_array;

        return update_post_meta($this->id, "_shipping_from", $addr);
    }

    private function get_artwork_type() {
        return @wp_get_post_terms($this->id, 'artwork_type')[0];
    }

    private function set_artwork_type($type) {
        wp_set_post_terms($this->id, $type, 'artwork_type');
    }

    /**
     * @return float|Object Returns object if artwork has an optional frame. Ojbect {with_frame, without_frame}.
     */
    private function get_commission_fee() {
        $rate = floatval(get_option('commission_rate'));

        if ($this->have_frame === true && $this->is_frame_optional === true) {

            $fees = new stdClass();
            $fees->without_frame = ceil($this->price_of_artwork * $rate * 100) / 100;
            $fees->with_frame    = ceil(($this->price_of_artwork + $this->price_of_frame) * $rate * 100) / 100;
            return $fees;

        } else {
            $price = $this->price_of_artwork;

            $fee = ceil( $price * $rate * 100 ) / 100;

            return $fee;
        }
    }

    /**
     * @return float|Object Returns object if artwork has an optional frame. Ojbect {with_frame, without_frame}.
     */
    private function get_artist_profit() {
        if ($this->have_frame === true && $this->is_frame_optional === true) {
            $commissions = $this->get_commission_fee();
            $profits = new stdClass();
            $profits->without_frame = $this->price_of_artwork - $commissions->without_frame;
            $profits->with_frame    = $this->price_of_artwork + $this->price_of_frame - $commissions->with_frame;

            return $profits;
        } else {
            return $this->price_of_artwork - $this->get_commission_fee();
        }
    }

    /**
     * Increase stock level
     *
     * @param int $amount Amount to increase by. Default 1.
     * @return int new stock level.
     */
    public function increase_stock( $amount = 1 ) {
        $stock = $this->stock;

        // update stock
        $this->stock = $stock + $amount;

        // retrieve new stock level
        return $this->stock;
    }

    /**
     * Reduce stock by 1
     *
     * @param int $amount Amount to reduce by. Default 1.
     * @return int|false new stock level. Return false on error.
     */
    public function reduce_stock( $amount = 1 ) {
        $stock = $this->stock;

        if ($stock < $amount) {
            return false;
        } else {

            // update stock
            $this->stock = $stock - $amount;

            // retrieve new stock level
            return $this->stock;
        }
    }

    /**
     * @param $tmp string Temporary file path for the uploaded file
     * @param $image_type string file extension
     * @param $thumbnail_cropping_data
     * @return bool True on success.
     */
    public function set_cover_image($tmp, $image_type, $thumbnail_cropping_data) {

        // if cover image is already set
        $old_cover_url = $this->cover_image;

        if ($old_cover_url){
            // get cover_image url from meta
            // get path from url
            $path = url_to_path($old_cover_url);

            // delete file
            @wp_delete_file($path);

            // get path for thumbnail
            $thumb_path = _get_image_path($this->id, $this->artist, THUMBNAIL, $path);

            // delete thumbnail
            @wp_delete_file($thumb_path);

            // get path for wechat
            $wechat_path = _get_image_path($this->id, $this->artist, WECHAT, $path);

            // delete wechat
            @wp_delete_file($wechat_path);
        }

        // generate unique file name
        $uuid_filename = uniqid() . ".{$image_type}";

        // move file to the right location
        $path = _get_image_path($this->id, $this->artist, ORIGINAL, $uuid_filename);
        if (!$this->move_file($tmp, $path)) return false;

        // add to artwork meta
        $url = _get_image_url($this->id, $this->artist, ORIGINAL, $uuid_filename);
        if ( update_post_meta($this->id, "_cover_image", $url) === false ) return false;

        // generate thumbnail with cropping data, get thumbnail path and save
        $thumb_path = _get_image_path($this->id, $this->artist, THUMBNAIL, $uuid_filename);
        if (! $this->crop_resize_and_save($path, null, THUMBNAIL, $thumb_path) ) return false;

        // generate wechat image with cropping data, get wechat path and save
        $wechat_path= _get_image_path($this->id, $this->artist, WECHAT, $uuid_filename);
        if (! $this->crop_resize_and_save($path, $thumbnail_cropping_data, WECHAT, $wechat_path) ) return false;

        return true;
    }

    private function get_cover_thumbnail() {
        // get cover_image url from meta
        $cover = $this->cover_image;

        // get thumbnail url from the above url
        // return the value
        if ($cover)
            return _get_image_url($this->id, $this->artist, THUMBNAIL, $cover);
        else
            return '';
    }

    private function get_wechat_image() {

        $cover = $this->cover_image;

        if ($cover)
            return _get_image_url($this->id, $this->artist, WECHAT, $cover);
        else
            return '';
    }


    /**
     * Note: An image in _other_images meta value is represented as an object {url, path, order}
     * @param $tmp
     * @param $image_type string File extension
     * @param $order
     * @return bool
     */
    public function add_other_image($tmp, $image_type, $order) {
        global $logger;

        $filename = uniqid() . ".{$image_type}";

        // get image saving path
        $path = _get_image_path($this->id, $this->artist, ORIGINAL, $filename);
        $url  = _get_image_url ($this->id, $this->artist, ORIGINAL, $filename);

        // move image to location
        if (!$this->move_file($tmp, $path)) return false;

        // create database record
        if ( add_artwork_file($this->id, $path, $url, $order) === false) return false;

        // generate thumbnail
        $thumb_path = _get_image_path($this->id, $this->artist, THUMBNAIL, $filename);
        if (! $this->crop_resize_and_save($path, null, THUMBNAIL, $thumb_path) ) return false;

        // generate dropzone thumbnail
        $dropzone_thumb_path = _get_image_path($this->id, $this->artist, DROPZONE_THUMBNAIL, $filename);
        $logger->log_action('dropzone thumbnail path', $dropzone_thumb_path);
        if (! $this->crop_resize_and_save($path, null, DROPZONE_THUMBNAIL, $dropzone_thumb_path) ) return false;

        return true;
    }

    /**
     * @param $file_id
     * @param $new_order
     * @return bool
     */
    public function update_other_image_order($file_id, $new_order) {

        return update_artwork_file($file_id, array('order' => $new_order));
    }

    /**
     * @param $file_id
     * @return bool
     */
    public function delete_other_image($file_id) {

        // delete file from disk
        $file = get_artwork_file_with_file_id($file_id);
        @wp_delete_file($file->path);

        // delete thumbnail from disk
        $thumbnail_path = _get_image_path($this->id, $this->artist, THUMBNAIL, $file->path);
        @wp_delete_file($thumbnail_path);

        // delete dropzone thumbnail from disk
        $dropzone_thumbnail_path = _get_image_path($this->id, $this->artist, DROPZONE_THUMBNAIL, $file->path);
        @wp_delete_file($dropzone_thumbnail_path);

        // delete file record
        return delete_artwork_file($file_id);
    }

    /**
     * @return array|null Array of Object {id, post_id, path, url, order}
     */
    private function get_other_images() {
        return get_artwork_files($this->id);
    }

    /**
     * @return array|null Array of Object {id, post_id, path, url, order}; id is the record# for the original picture.
     */
    private function get_other_images_thumbnails() {
        $images = $this->get_other_images();

        return array_map(function($image) {
            $path = $image->path;
            $image->path = _get_image_path($this->id, $this->artist, THUMBNAIL, $path);
            $image->url = _get_image_url($this->id, $this->artist, THUMBNAIL, $path);
            return $image;
        }, $images);
    }


    /**
     * @return array|null Array of Object {id, post_id, path, url, order}; id is the record# for the original picture.
     */
    private function get_other_images_dropzone_thumbnails() {
        $images = $this->get_other_images();

        return array_map(function($image) {
            $path = $image->path;
            $image->path = _get_image_path($this->id, $this->artist, DROPZONE_THUMBNAIL, $path);
            $image->url = _get_image_url($this->id, $this->artist, DROPZONE_THUMBNAIL, $path);
            return $image;
        }, $images);
    }



    public function pre_delete_artwork() {
        // locate the artwork file directory
        $dir = dirname( _get_image_path($this->id, $this->artist, ORIGINAL, $this->cover_image) );

        // delete the directory recursively
        include_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
        include_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');
        global $wp_filesystem;
        if (!$wp_filesystem)
            $wp_filesystem = new WP_Filesystem_Direct();
        $wp_filesystem->rmdir($dir, true);

        // cover image post meta will be taken care of automatically.

        // delete database records for other images
        delete_all_artwork_files($this->id);
    }

    /**
     * Move uploaded file
     * Creates the parent directories if directory path doesn't exist.
     * @param $from
     * @param $to
     * @return bool
     */
    private function move_file($from, $to) {

        // recursively create directories if path doesn't exist
        if ( wp_mkdir_p( dirname($to) ) === false ) return false;

        // move file to location
        return move_uploaded_file($from, $to);
    }

    /**
     * @param $image_to_crop
     * @param $cropping_info array|null. array("x" =>, "y"=>, "w"=>, "h"=>)
     * @param $image_size   int     THUMBNAIL|WECHAT
     * @param $save_path
     * @return bool
     */
    private function crop_resize_and_save($image_to_crop, $cropping_info, $image_size, $save_path) {
        $image_editor = wp_get_image_editor($image_to_crop);
        $x = $cropping_info["x"];
        $y = $cropping_info["y"];
        $w = $cropping_info["w"];
        $h = $cropping_info["h"];

        if ($image_size === THUMBNAIL) {
            $max_w = self::$thumbnail_width;
            $max_h = null;
        } elseif ($image_size === DROPZONE_THUMBNAIL) {
            $max_w = 120;
            $max_h = 120;
        } elseif ($image_size === WECHAT) {
            $max_w = self::$wechat_width;
            $max_h = self::$wechat_height;
        }

        // cropping
        if ( $cropping_info && $image_editor->crop($x, $y, $w, $h) === false ) return false;

        // resizing
        if ( $image_editor->resize($max_w, $max_h, true) === false ) return false;

        // save
        if ( is_wp_error($image_editor->save($save_path)) ) return false;

        return true;
    }

    /**
     * Retrieve similar artworks ids
     * Artworks of the same categories or with the same tags (from other artists)
     */
    public function similar_artworks_ids_from_other_artists() {
        echo __METHOD__ . ' is not implemented';
    }

    /**
     * Retrieve other artwork ids from the same artist
     */
    public function other_artwork_ids_from_this_artist() {
        echo __METHOD__ . ' is not implemented';
    }

    /**
     * Is this artwork for sale or display only?
     * @return bool
     */
    public function is_for_sale() {
        return ($this->status === '1');
    }

    /**
     * Is this property a required field?
     * @param $property string. Property Name.
     * @return bool
     */
    public function is_required($property) {
        return in_array($property, self::$required_properties);
    }

    public function get_status() {
        if ($this->is_for_sale()) {
            if ($this->stock > 0) {
                return 'In Stock';
            } else {
                return 'Sold Out';
            }
        } else {
            return 'Not For Sale';
        }
    }

    public function get_materials() {
        $its_materials = get_the_terms($this->id, 'artwork_material');
        if (! $its_materials || is_wp_error($its_materials) )
            $its_materials = [];

        return $its_materials;
    }

    /**
     * @param $materials Array
     */
    public function set_materials($materials) {
        $materials_ids = array_map('intval', (array)$materials);
        wp_set_post_terms($this->id, $materials_ids, 'artwork_material', false);
    }

//    /**
//     * @param int $n
//     * @return array Returns an array of the first $n featured artworks
//     */
//    public static function featured_artworks($n = 5) {
//        if ( false === ( $featured_artwork_ids = get_transient( 'featured_artwork_ids' ) ) ) {
//
//            // find the featured artwork ids and set the transient
//            $featured_args = array(
//                'post_status' => 'publish',
//                'post_type' => 'artwork',
//                'meta_key' => '_is_featured',
//                'meta_value' => 1,
//                'posts_per_page' => $n,
//                'orderby' => 'rand',
//            );
//
//            // The featured artworks query.
//            $featured = new WP_Query( $featured_args );
//
//            if ( $featured->have_posts() ) {
//                while ( $featured->have_posts() ) {
//                    $featured->the_post();
//                    $featured_artwork_ids[] = $featured->post->ID;
//                }
//                set_transient( 'featured_artwork_ids', $featured_artwork_ids );
//            }
//        }
//
//        // Return the post ID's, either from the cache, or from the loop.
//        return $featured_artwork_ids;
//    }
//
//    /**
//     * @param int $n
//     * @return array|Loop Returns an array of the first $n trending artworks
//     *
//     * Trending artworks are artworks of the most views.
//     */
//    public static function trending_artworks($n = 5) {
//
//    }
//
//    /**
//     * @param int $n
//     * @return array|Loop Returns an array of the first $n newly published artworks
//     */
//    public static function new_artworks($n = 5) {
//
//    }

}