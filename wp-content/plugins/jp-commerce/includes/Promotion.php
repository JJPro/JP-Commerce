<?php

/**
 * Created by PhpStorm.
 * User: jjpro
 * Date: 11/5/15
 * Time: 8:48 PM
 */
class Promotion
{
    var $id, $promotionType, $coupon, $message;
        // @param string $promotionType Either 'banner' or 'popup'
        // @param int    $coupon        Coupon rate
        // @param string $message       HTML literal of the promotion message.

    function __construct($id) {

    }

    function isActive() {
        // compare this->id with OPTION active_{$this->promotionType}_promotion
        return ($this->id == get_option("active_{$this->promotionType}_promotion"));
    }

    function activate() {
        update_option("active_{$this->promotionType}_promotion", $this->id);
    }

    static function findAllBannerPromotions() {
        global $wpdb;

    }

    static function findAllPopupPromotions() {

    }

    static function getActiveBannerPromotion() {

    }

    static function getActivePopupPromotion() {

    }
}