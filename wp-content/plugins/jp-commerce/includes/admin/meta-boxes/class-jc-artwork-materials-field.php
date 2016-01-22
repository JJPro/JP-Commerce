<?php

/**
 * Created by PhpStorm.
 * User: jjpro
 * Date: 1/13/16
 * Time: 2:16 AM
 */
class JC_Artwork_Materials_Field
{

    public static function init() {
        self::scripts();

    }

    private static function scripts() {
        add_action( 'admin_enqueue_scripts',
            function()
            {
                // chosen js and css
                wp_enqueue_script('chosen', JC_PLUGIN_DIR_URL . 'js/libs/chosen_v1.4.2/chosen.jquery.min.js', array('jquery'), '1.4.2', true);
                wp_enqueue_style('chosen', JC_PLUGIN_DIR_URL . 'js/libs/chosen_v1.4.2/chosen.min.css');

                // my js and style
                wp_enqueue_script('my-chosen', JC_PLUGIN_DIR_URL . 'js/admin/artwork-materials-field.min.js', array('chosen', 'jquery'), '1.0', true);
                wp_enqueue_style('my-chosen', JC_PLUGIN_DIR_URL . 'css/meta-boxes/artwork-materials-field.css');

            }
        );
    }


    public static function output($artwork) {

        $all_materials = get_terms('artwork_material', 'hide_empty=0');
        if (! $all_materials || is_wp_error($all_materials) )
            $all_materials = [];

        $its_materials = $artwork->materials;

        echo '<select id="artwork-materials-select-box" name="artwork-materials[]" multiple="multiple" data-placeholder="Select 1-5 Materials">';

        foreach ($all_materials as $material) {
            echo "<option value='{$material->term_id}' " . self::selected($material, $its_materials) . ">{$material->name}</option>";
        }

        echo '</select>';
        echo '<span id="artwork-materials-select-box-alert">You can only add 5 materials.</span>';

    }

    public static function save($artwork) {
        $nonce = $_POST['jc_meta_nonce'];
        if ( ! wp_verify_nonce($nonce, 'jc_save_data') )
            return;

        $materials = $_POST['artwork-materials'];
        $artwork->materials = $materials;
    }

    private static function selected($needle, $array) {
        foreach($array as $material) {
            if ($needle->term_id === $material->term_id)
            {
                return 'selected';
            }
        }
    }


}