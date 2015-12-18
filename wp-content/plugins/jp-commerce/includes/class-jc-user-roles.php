<?php
/**
 * User Roles
 *
 * Registers user roles and capabilities
 *
 * @class       JC_User_Roles
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JC_User_Roles
{
    public static function init(){
        add_action( 'init', array( __CLASS__, 'register_user_roles' ) );
        add_action( 'init', array( __CLASS__, 'modify_role_caps' ) );
    }

    public static function register_user_roles(){
        remove_role('contributor');

        add_role('superadmin', 'Super Admin',
            get_role('administrator')->capabilities);


        add_role('artist', 'Artist',
            array (
                'read'              => true,
                'create_artworks'   => true,
                'edit_artworks'     => true,
            )
        );

        add_role('customer', 'Customer', false);
    }

    public static function modify_role_caps(){
        $superadmin = get_role('superadmin');
        $superadmin->add_cap('manage_promotion_types');
        $superadmin->add_cap('manage_promotions');
        $superadmin->add_cap('manage_artworks');
        $superadmin->add_cap('manage_artwork_types');
        $superadmin->add_cap('manage_options');
        $superadmin->add_cap('manage_orders');

        $admin = get_role('administrator');
        $admin->remove_cap('edit_posts');
        $admin->remove_cap('manage_categories');
        $admin->remove_cap('update_core');
        $admin->remove_cap('export');           // hide tools menu
        $admin->remove_cap('edit_plugins');
        $admin->remove_cap('install_plugins');
        $admin->remove_cap('activate_plugins');
        $admin->remove_cap('update_plugins');
        $admin->remove_cap('update_themes');
        $admin->add_cap('edit_promotions');
        $admin->add_cap('manage_promotions');
        $admin->add_cap('manage_promotion_types');
        $admin->add_cap('create_artworks');
        $admin->add_cap('edit_artworks');
        $admin->add_cap('manage_artworks');
        $admin->add_cap('manage_artwork_types');
        $admin->add_cap('manage_orders');

        $editor = get_role('editor');
        $editor->remove_cap('edit_posts');
        $editor->remove_cap('manage_categories');
        $editor->add_cap('manage_artworks');
        $editor->add_cap('manage_artwork_types');
        $editor->add_cap('edit_promotions');
        $editor->add_cap('manage_promotions');
        $editor->add_cap('manage_promotion_types');
        $editor->add_cap('edit_theme_options');
        $editor->add_cap('manage_options'); // so that it can submit the jc options form
        $admin->add_cap('manage_orders');
    }
}