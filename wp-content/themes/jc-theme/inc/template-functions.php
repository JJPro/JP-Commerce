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
        <label class="form-control-icon"><i class="icon-search"></i></label>
        <input type="text" id="s" class="form-control input-search-box" name="s" value="<?php echo $keyword; ?>" placeholder="Search for arts or artists">
    </form>
    <?php
}

/**
 * TODO:
 * This function must be called by action "pre_get_posts"
 *
 * Starts the primary loop for featured, or new, or trending artworks
 *
 * @param $query This is provided by the "pre_get_posts" action
 * @param $for JC_PRIMARY_LOOP_FEATURED|JC_PRIMARY_LOOP_NEW|JC_PRIMARY_LOOP_TRENDING
 */
if (!defined( 'JC_PRIMARY_LOOP_FEATURED') ) define( 'JC_PRIMARY_LOOP_FEATURED', 'featured' );
if (!defined( 'JC_PRIMARY_LOOP_NEW') ) define( 'JC_PRIMARY_LOOP_NEW', 'new' );
if (!defined( 'JC_PRIMARY_LOOP_TRENDING') ) define( 'JC_PRIMARY_LOOP_TRENDING', 'trending' );
function start_primary_loop($query, $for) {
    $query->set( 'post_type', 'artwork' );
}