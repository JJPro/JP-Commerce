<?php
/**
 * functions to be used in template files
 * User: jjpro
 * Date: 1/5/16
 * Time: 4:38 PM
 */
/**
 * @return string|false
 */
function logo_url() {
    return get_option('_logo_url');
}

function jc_search_form() {
    $keyword = $_GET['s'];
    ?>
    <form id="searchform" class="form-group form-group-has-icon" action="<?php echo home_url(); ?>" method="get" role="search">
        <i class="icon-search"></i>
        <input type="text" id="s" class="form-control input-search-box" name="s" value="<?php echo $keyword; ?>" placeholder="Search for arts or artists">
    </form>
    <?php
}

function shopping_cart_items_count() {
    /**
     * TODO:
     * Rely on class JC_Cart
     * 1. get current user id
     * 2. retrieve entry count for user's shopping cart
     */


}

function set_featured_artwork_ids( $ids ) {
	$GLOBALS['featured_artwork_ids'] = $ids;
}

function get_featured_artwork_ids() {
	return $GLOBALS['featured_artwork_ids'];
}

function set_trending_artwork_ids( $ids ) {
	$GLOBALS['trending_artwork_ids'] = $ids;
}

function get_trending_artwork_ids() {
	return $GLOBALS['trending_artwork_ids'];
}

function set_new_artwork_ids( $ids ) {
	$GLOBALS['new_artwork_ids'] = $ids;
}

function get_new_artwork_ids() {
	return $GLOBALS['new_artwork_ids'];
}



/*
	===================
		Inside the Loop functions 
	===================
*/
function jc_get_featured_image() {
	return JC_Artwork::instance( get_the_ID() )->cover_thumbnail;
}

