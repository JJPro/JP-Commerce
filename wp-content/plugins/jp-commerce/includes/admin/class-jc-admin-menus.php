<?php
/**
 * Building the Options page
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'JC_Admin_Menus' ) ) :
class JC_Admin_Menus {
    public function __construct()
    {
        // Remove menu
        add_action( 'admin_menu', array($this, 'remove_menus' ) );

        // Show pending count on artwork menu
        $this->show_pending_count_on_artworks_menu();

        // Add menu
        add_action('admin_menu', array($this, 'jc_create_settings_menu'));

        // Enqueue scripts
        add_action('admin_enqueue_scripts', array($this, 'jc_options_page_scripts'));

    }

    /**
     * Remove some menu pages
     */
    public function remove_menus() {
        remove_menu_page('index.php');
        remove_menu_page('upload.php');

        $current_user = wp_get_current_user();
        if ($current_user
            && current_user_can('manage_options')
            && $current_user->user_login != 'jjpro'
            && !current_user_can('superadmin'))
            remove_menu_page('options-general.php');
    }

    public function jc_options_page_scripts($screen){
        if ('toplevel_page_jp_commerce_settings' != $screen) { return; }

        wp_enqueue_media();
        wp_enqueue_script('options_js');
        wp_enqueue_style('options-style');
    }

    public function jc_create_settings_menu(){
        add_menu_page('JP Commerce Settings', 'JP Commerce', 'manage_options', 'jp_commerce_settings',array($this, 'jp_commerce_settings_page'));
                            /* page title, menu title, cap, menu-slug, callback, icon, position */
                            /* menu-slug: jp_commerce_settings */
        add_action('admin_init', array($this, 'jp_register_settings'));
    }

    function jp_commerce_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo get_admin_page_title(); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('jc_settings_group'); ?>
                <?php do_settings_sections('jc_settings'); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    function jp_register_settings() {

        $this->general_section();

        $this->promotions_section();

        $this->selling_section();

        $this->analytics_section();

    }

    function general_section(){
        register_setting('jc_settings_group', 'blogname');
        register_setting('jc_settings_group', 'blogdescription');
        register_setting('jc_settings_group', 'sitelogo');

        add_settings_section('general-section', 'General', null, 'jc_settings');
        $value = esc_attr(get_option('blogname'));
        add_settings_field('site-title', 'Site Title', 'jc_generic_input_field_html', 'jc_settings', 'general-section', array('name'=>'blogname', 'value'=>$value));
        $value = esc_attr(get_option('blogdescription'));
        add_settings_field('site-tagline', 'Tagline', 'jc_generic_input_field_html', 'jc_settings', 'general-section', array('name'=>'blogdescription', 'value'=>$value));
        add_settings_field('site-logo', 'Site Logo', 'jc_sitelogo_field_html', 'jc_settings', 'general-section');
    }

    function promotions_section(){
        register_setting('jc_settings_group', 'active_promotions');
        add_settings_section('promotions-section', 'Set Promotions', null, 'jc_settings');
        add_settings_field('active-banner-promo', 'Active Banner Promotion', 'jc_promotions_field_html', 'jc_settings', 'promotions-section', array('promo_type'=>'banner'));
        add_settings_field('active-popup-promo', 'Active Pop-up Promotion', 'jc_promotions_field_html', 'jc_settings', 'promotions-section', array('promo_type'=>'popup'));
    }

    function selling_section() {
        register_setting('jc_settings_group', 'commission_rate', 'jc_sanitize_rates');
        register_setting('jc_settings_group', 'tax_rate', 'jc_sanitize_rates');

        add_settings_section('selling-section', 'Selling', null, 'jc_settings');
        $commission_rate = esc_attr(get_option('commission_rate') * 100);
        add_settings_field('commission-rate', 'Commission Rate', 'jc_generic_input_field_html', 'jc_settings', 'selling-section', array('name'=>'commission_rate', 'value'=>$commission_rate, 'after'=>' %', 'class'=>'small-text'));
        $tax_rate = esc_attr(get_option('tax_rate') * 100);
        add_settings_field('tax-rate', 'Tax Rate', 'jc_generic_input_field_html', 'jc_settings', 'selling-section', array('name'=>'tax_rate', 'value'=>$tax_rate, 'after'=>' %', 'class'=>'small-text'));
    }

    function analytics_section(){
        register_setting('jc_settings_group', 'analytics', 'jc_sanitize_analytics');

        add_settings_section('analytics-section', 'Analytics', null, 'jc_settings');
        $value = esc_attr(get_option('analytics')['google_analytics_id']);
        add_settings_field('ga-id', 'Google Analytics ID', 'jc_generic_input_field_html', 'jc_settings', 'analytics-section', array('name'=>'analytics[google_analytics_id]', 'value'=>$value));
        add_settings_field('ga-tracking-code', 'Google Analytics Tracking Code', 'jc_ga_tracking_code_field_html', 'jc_settings', 'analytics-section');
    }

    // sanitize functions
    function jc_sanitize_analytics($input) {
        $input['google_analytics_id'] = sanitize_text_field($input['google_analytics_id']);
    //    $input['ga_tracking_code']    = esc_js($input['ga_tracking_code']);

        return $input;
    }

    function jc_sanitize_rates($input) {
        return doubleval($input)/100;
    }

    /*********
     * HTML *
     *********/
    // promotions section
    function jc_promotions_field_html($args) {

        $promo_type = $args['promo_type'];

        $active_promo_of_this_type = get_option('active_promotions')[$promo_type];

        $all_promos_of_this_type = get_posts(array(
            'posts_per_page'    => -1,
            'post_type'         => 'promotion',
            'post_status'       => 'publish',
            'tax_query'         => array(array(
                'taxonomy'          => 'promotion_type',
                'field'             => 'slug',
                'terms'             => $promo_type,
            ))
        ));
        ?>
        <select name="active_promotions[<?php echo $promo_type; ?>]">
            <option value="0"></option>
            <?php foreach($all_promos_of_this_type as $promo): ?>
                <option value="<?php echo $promo->ID; ?>" <?php echo selected($promo->ID, $active_promo_of_this_type); ?>>
                    <?php echo esc_html($promo->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }
    function jc_ga_tracking_code_field_html() {
        $ga_tracking_code = esc_textarea(get_option('analytics')['ga_tracking_code']);
        echo "<textarea type='text' name='analytics[ga_tracking_code]' rows='5' cols='50'>{$ga_tracking_code}</textarea>";
    }
    function jc_generic_input_field_html($args) {
        $name = $args['name'];
        $value = $args['value'];
        $class = $args['class'] ? $args['class'] : 'regular-text';
        $before = $args['before'];
        $after = $args['after'];
        echo "{$before}<input type='text' name='{$name}' value='{$value}' class='{$class}'>{$after}";
    }
    function jc_sitelogo_field_html() {
        $sitelogo = get_option("sitelogo");
        ?>
        <input type="hidden" id="sitelogo_url" name="sitelogo" value="<?php print $sitelogo;?>">
        <button id="upload-button" class="button-secondary">Update Logo</button>
        <div class="sitelogo-preview image-container" style="background-image: url(<?php print $sitelogo;?>)">
        </div>
        <?php
    }
    private function show_pending_count_on_artworks_menu() {
        add_filter('add_menu_classes', function($menu) {
            if (current_user_can('artist'))
                return $menu;
            $pending_count = wp_count_posts('artwork')->pending;
            foreach ($menu as $menu_key => $menu_data) {

                if ('artworks' == $menu_data[0]) {
                    if ($pending_count > 0)
                        $menu[$menu_key][0] .= ' <span class="update-plugins"><span class="plugin-count">' . $pending_count . '</span></span>'; // utilize the styles of .plugin-count
                    break;
                }
            }
            return $menu;
        });
    }
}

endif;

return new JC_Admin_Menus();