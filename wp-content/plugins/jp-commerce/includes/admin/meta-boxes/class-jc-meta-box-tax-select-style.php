<?php
/**
 * Select Style Meta Box for Taxonomies
 *
 * Displays the select style meta box for taxonomies
 *
 * @class       JC_Meta_Box_Tax_Select_Style
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JC_Meta_Box_Tax_Select_Style 
{
    public static function output ( $post, $box ) {
        $defaults = array('taxonomy' => 'category');

        if ( ! isset( $box['args'] ) || ! is_array( $box['args'] ) ) {
            $args = array();
        }
        else {
            $args = $box['args'];
        }

        extract( wp_parse_args($args, $defaults), EXTR_SKIP );

        $tax = get_taxonomy($taxonomy);
        $selected = wp_get_object_terms($post->ID, $taxonomy, array('fields' => 'ids'));
        $hierarchical = $tax->hierarchical;
        ?>
        <div id="taxonomy-<?php echo $taxonomy; ?>" class="selectdiv">
            <?php if (current_user_can($tax->cap->assign_terms)): ?>
                <?php
                if ($hierarchical) {
                    wp_dropdown_categories(array(
                        'taxonomy' => $taxonomy,
                        'class' => 'widefat',
                        'hide_empty' => 0,
                        'name' => "tax_input[$taxonomy][]",
                        'selected' => count($selected) >= 1 ? $selected[0] : '',
                        'orderby' => 'name',
                        'hierarchical' => 1,
                        'show_option_all' => " "
                    ));
                } else {
                    ?>
                    <fieldset class="widefat">
                        <?php foreach (get_terms($taxonomy, array('hide_empty' => false)) as $term): ?>

                            <input type="radio" name="<?php echo "tax_input[$taxonomy][]"; ?>" class="<?php echo $tax->name; ?>" id="<?php echo $tax->name; ?>-<?php echo esc_attr($term->slug); ?>"
                                   value="<?php echo esc_attr($term->slug); ?>" <?php checked($term->term_id, count($selected) >= 1 ? $selected[0] : ''); ?> />
                            <label for="<?php echo $tax->name; ?>-<?php echo esc_attr($term->slug); ?>" class="<?php echo $tax->name; ?>-icon <?php echo $tax->name; ?>-<?php echo esc_attr($term->slug); ?>">
                                <?php echo esc_html($term->name); ?>
                            </label>
                            <br />

                        <?php endforeach; ?>
                    </fieldset>
                    <?php
                }
                ?>
            <?php endif; ?>
        </div>

        <?php
    }
}