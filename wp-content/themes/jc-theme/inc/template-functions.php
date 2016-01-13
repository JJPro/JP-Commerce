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
    <form id="searchform" action="<?php echo home_url(); ?>" method="get" role="search">
        <input id="s" type="text" name="s" value="<?php echo $keyword; ?>" />
    </form>
    <?php
}