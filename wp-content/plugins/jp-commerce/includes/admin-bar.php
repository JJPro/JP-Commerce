<?php
/**
 * Customize the admin bar
 */
hide_admin_bar_from_customers();
add_action('wp_before_admin_bar_render', 'jp_customize_admin_bar_items');
// admin bar style in admin screen
add_action('admin_enqueue_scripts', function() {
    wp_enqueue_style('jp_commerce_admin_style', plugins_url('css/admin-bar.css', dirname(__FILE__)));
});
// admin bar style in public screen.
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('jp_commerce_admin_style', plugins_url('css/admin-bar.css', dirname(__FILE__)));
});



function hide_admin_bar_from_customers() {
    add_action('init', '__hide_admin_bar_from_customers');
    function __hide_admin_bar_from_customers(){
        if (current_user_can('customer'))
            show_admin_bar(false);
    }
}

function jp_customize_admin_bar_items() {
    global $wp_admin_bar;
    // To remove WordPress logo and related submenu items
    $wp_admin_bar->remove_menu('wp-logo');
    $wp_admin_bar->remove_menu('about');
    $wp_admin_bar->remove_menu('wporg');
    $wp_admin_bar->remove_menu('documentation');
    $wp_admin_bar->remove_menu('support-forums');
    $wp_admin_bar->remove_menu('feedback');
    $wp_admin_bar->remove_menu('new-media');
    // Add Pending Reviews button when there are artworks waiting to be reviewed.
    $pending_count = wp_count_posts('artwork')->pending;
    $node = array(
        'id'        => 'pending-reviews',
        'title'     => '<span class="ab-icon"></span><span class="ab-label">Pending Reviews</span><span class="pending-count">'.$pending_count.'</span>',
        'href'      => site_url().'/wp-admin/edit.php?post_type=artwork&post_status=pending',
        'meta'      => array (
            'class'     => 'admin-bar-item-pending-reviews'
        )
    );
    if (current_user_can('manage_artworks') && !current_user_can('artist') && $pending_count > 0)
        $wp_admin_bar->add_menu($node);


}