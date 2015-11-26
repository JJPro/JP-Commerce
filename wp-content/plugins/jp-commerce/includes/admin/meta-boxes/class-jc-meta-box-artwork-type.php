<?php
/**
 * Artwork Type
 *
 * Displays the artwork type meta box.
 *
 * @class       JC_Meta_Box_Artwork_Type
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JC_Meta_Box_Artwork_Type
{
    public static function init() {
        self::enqueue_scripts();
    }

    private static function enqueue_scripts() {
        add_action("admin_enqueue_scripts", function() {wp_enqueue_script('meta-boxes-artwork-type', JC_PLUGIN_DIR_URL . 'js/admin/meta-boxes-artwork-type.min.js', ['jquery-ui-selectable']);});
    }

    public static function output( $post ) {
        $term = wp_get_object_terms($post->ID, 'artwork_type');
        $term = $term ? $term[0]->term_id : '';
        $ui_selected = [];
        if ($term) {
            $ui_selected = get_all_parents_ids($term);
            $ui_selected[] = $term;
        }

        ?>
        <style>
            #artwork_typediv .ui-selectee {
                border: solid orange 1px;
                padding: 3px;
            }

            #artwork_typediv .ui-selectee.ui-selected {
                background-color: darkorange;
            }

            #artwork_typediv .ui-selectee.ui-selecting {
                background-color: orange;
            }

            #artwork_typediv .selectable {
                display: inline-block;
                width: 150px;
                margin-right: 10px;
                vertical-align: top;
            }

            #artwork_typediv .has-children::after {
                content: '\276F';
                float: right;
            }
        </style>
        <script type="text/javascript">
            var nodes = <?php echo json_encode(jc_get_all_artwork_types()); ?>;
            var end_nodes = <?php echo json_encode(jc_get_childless_artwork_types()); ?>;
            var ui_selected = <?php echo json_encode($ui_selected); ?>;
        </script>
        <div id="selectables-wrap">
            <input type="hidden" id="artwork_type" name="tax_input[artwork_type][]" value="<?php echo $term; ?>">
        </div>
        <?php

    }

}