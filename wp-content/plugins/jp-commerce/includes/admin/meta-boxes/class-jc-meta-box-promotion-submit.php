<?php
/**
 * Promotion Form Submit
 *
 * Displays submit buttons for the form.
 *
 * @class       JC_Meta_Box_Promotion_Submit
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JC_Meta_Box_Promotion_Submit
{

    static public function init() {
        // enqueue stylesheet
        add_action("admin_enqueue_scripts",
            function()
            {

                // css
                wp_enqueue_style( 'promo-submit', JC_PLUGIN_DIR_URL . 'css/meta-boxes/promotion-submit.css' );

            }
        );
    }


    static public function output($post, $args=array()) {
        $post_type = $post->post_type;
        ?>
        <?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key ?>
        <div class="submitbox" id="submitpost">
            <div style="display:none;">
                <?php submit_button( __( 'Save' ), 'button', 'save' ); ?>
            </div>
            <div class="row">
                <div id="action-submit" class="col-xs-12">
                    <span class="spinner"></span>
                    <input type="submit" name="publish" id="publish" class="btn btn-lg btn-primary btn-block" value="Publish" />
                </div>
            </div> <!-- .row -->
            <p></p>
            <div class="row">
                <div id="action-save" class="col-xs-6">
                    <input type="submit" name="save" id="save-post" value="<?php esc_attr_e('Save Draft'); ?>" class="btn btn-default btn-block" />
                    <span class="spinner"></span>
                </div>
                <div id="action-preview" class="col-xs-6">
                    <?php
                    $preview_link = esc_url( add_query_arg( array( 'preview' => 'true', 'preview_promo' => $post->ID ), home_url() ) );

                    $preview_button = __( 'Preview' );
                    ?>
                    <a class="preview btn btn-info btn-block" href="javascript:;" id="promo-preview" onclick="preview('<?php echo $preview_link; ?>')"><?php echo $preview_button; ?></a>
                    <input type="hidden" name="wp-preview" id="wp-preview" value="" />
                </div>
            </div> <!-- .row -->
        </div> <!-- .submitbox -->
        <script type="text/javascript">
            function preview(url) {
                document.getElementById('save-post').click();
                setTimeout( window.open(url, '_blank'), 3000);
            }
        </script>
        <?php
    }
}