<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


/**
 * JC Promotion
 *
 * @class       JC_Promotion
 * @var         int|WP_Post|JC_Promotion
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 *
 *
 * @property string coupon_code
 * @property float coupon_rate
 *
 * @property string content
 * @property string title
 */
class JC_Promotion
{
    private static $_instance = null;

    /**
     * Name in the options table for the active promotion.
     *
     * @var string $option_name
     */
    public static $option_name = '_active_promotion';

    /**
     * The promotion (post) id.
     *
     * @var int $id.
     */
    public $id = null;   // Cache id for fast access

    /**
     * Stores post data
     *
     * @var WP_Post $post
     */
    public $post = null; // For direct reference to post meta


    /**
     * Properties from direct access to post meta
     */
    private static $direct_post_meta = ['coupon_code', 'coupon_rate', ];

    /**
     * Propreties from post object
     */
    private static $direct_post_properties = ['content' => 'post_content', 'title' => 'post_title'];


    /**
     * Get the single instance when requesting information about the same promotion during one request
     *
     * @param int|Post|Promotion $promo
     * @return JC_Promotion
     */
    public static function instance( $promo ) {
        if ( self::$_instance ) {
            if ( is_numeric($promo) && (self::$_instance->id === $promo) )
                return self::$_instance;
            elseif ( $promo instanceof WP_Post && self::$_instance->id === $promo->ID )
                return self::$_instance;
            elseif ( $promo instanceof JC_Artwork && $promo->id === self::$_instance->id )
                return self::$_instance;

        }

        self::$_instance = new self( $promo );
        return self::$_instance;

    }

    /**
     * JC_Promotion constructor.
     * Gets the promotion object and sets the id for the loaded promotion.
     *
     * @param int|Post|Promotion $promo
     */
    private function __construct( $promo )
    {
        if ( is_numeric($promo) ) {
            $this->id = absint($promo);
            $this->post = get_post($this->id);
        }
        elseif ( $promo instanceof WP_Post ) {
            $this->id = $promo->ID;
            $this->post = $promo;

        } elseif ( $promo instanceof JC_Artwork ) {
            $this->id = $promo->id;
            $this->post = $promo->post;
        }
    }


    public function __get( $name )
    {
        if ( in_array($name, self::$direct_post_meta ) ) {
            return get_post_meta($this->id, '_' . $name, true);
        }
        elseif ( array_key_exists($name, self::$direct_post_properties) ) {
            return $this->post->{"post_$name"};
        }
        else {
            return null;
        }
    }

    public function __set( $name, $value )
    {
        if ( in_array( $name, self::$direct_post_meta ) ) {
            return update_post_meta($this->id, "_{$name}", $value);
        }
        elseif ( array_key_exists( $name, self::$direct_post_properties ) ) {
            echo "{$name} is readonly";
            return;
        }
        else {
            echo "{$name} is not a property of this object.";
            return null;
        }
    }



    /**
     * @return JC_Promotion|null The active promotion if exists
     */
    public static function the_active_promotion() {
        $active_promo_id = get_option( self::$option_name );

        if ( $active_promo_id === false )
            return null;
        else
            return self::instance( $active_promo_id );
    }

    /**
     * @return bool Is this promotion active?
     */
    public function is_active() {
        $active_promo_id = get_option( self::$option_name );

        return $active_promo_id == $this->id;
    }

    /**
     * Makes this promotion active if it is not yet.
     * Unsets active promotion if it is already active.
     *
     * @return bool True when the operation is successful.
     */
    public function toggle_active() {
        $active_promo_id = get_option( self::$option_name );

        if ( $active_promo_id === false ) {
            // make this promo active
            return update_option( self::$option_name, $this->id );
        } else {
            // delete option
            return delete_option( self::$option_name );
        }
    }

    /**
     * @return string. 20%
     */
    public function coupon_rate_for_display() {
        $str = sprintf( "<span class='coupon-digit'>%d</span> <span class='coupon-percentage-symbol'>%%</span>", $this->coupon_rate * 100 );
        return $str;
    }
}