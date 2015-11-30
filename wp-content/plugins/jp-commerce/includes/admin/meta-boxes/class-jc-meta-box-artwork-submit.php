<?php
/**
 * Artwork Form Submit
 *
 * Displays submit buttons for the form.
 *
 * @class       JC_Meta_Box_Artwork_Submit
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JC_Meta_Box_Artwork_Submit 
{
    static public function output($post, $args=array()) {
        $post_type = $post->post_type;
        $post_type_object = get_post_type_object($post_type);
        $can_publish = current_user_can($post_type_object->cap->publish_posts);
        ?>
        <?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key ?>
        <div style="display:none;">
            <?php submit_button( __( 'Save' ), 'button', 'save' ); ?>
        </div>

        <div id="action-submit">
            <span class="spinner"></span>
            <?php
            if ( $can_publish ) {
                submit_button( __( 'Publish' ), 'primary button-large', 'publish', false );
            } else {
                submit_button(__('Submit for Review'), 'primary button-large', 'publish', false);
            }
            ?>
        </div>
        <div id="action-save">
            <input type="submit" name="save" value="<?php esc_attr_e('Save as Draft'); ?>" class="button" />
            <span class="spinner"></span>
        </div>
        <div id="action-preview">
            <?php
            if ( 'publish' == $post->post_status ) {
                $preview_link = esc_url( get_permalink( $post->ID ) );
                $preview_button = __( 'Preview Changes' );
            } else {
                $preview_link = set_url_scheme( get_permalink( $post->ID ) );

                /**
                 * Filter the URI of a post preview in the post submit box.
                 *
                 * @since 2.0.5
                 * @since 4.0.0 $post parameter was added.
                 *
                 * @param string  $preview_link URI the user will be directed to for a post preview.
                 * @param WP_Post $post         Post object.
                 */
                $preview_link = esc_url( apply_filters( 'preview_post_link', add_query_arg( 'preview', 'true', $preview_link ), $post ) );
                $preview_button = __( 'Preview' );
            }
            ?>
            <a class="button" href="<?php echo $preview_link; ?>" target="wp-preview-<?php echo (int) $post->ID; ?>"><?php echo $preview_button; ?></a>
            <input type="hidden" name="wp-preview" id="wp-preview" value="" />
        </div>
        <style>
            #artwork-submit .hndle,
            #artwork-submit .handlediv {
                display: none;
            }
            #artwork-submit .inside {
                padding-top: 10px;
                padding-bottom: 15px;
                text-align: center;
            }
            #action-submit,
            #action-save,
            #action-preview {
                display: inline-block;
            }
            #action-submit {
                border-right: dotted 2px #98a3a7;
                margin-right: 10px;
                padding-right: 15px;
            }
            .spinner {
                float: left;
            }
        </style>
        <?php
    }
}