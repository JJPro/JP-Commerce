<?php
/**
 * Artwork Tag Meta Box
 *
 * Displays the artwork tags meta box.
 *
 * @class       JC_Meta_Box_Artwork_Tag
 * @version     1.0
 * @author      JJPRO Technologies LLC.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JC_Meta_Box_Artwork_Tag 
{

    public static function init() {
        self::enqueue_scripts();
    }

    public static function enqueue_scripts() {
        add_action('admin_enqueue_scripts',
            function()
            {
                wp_enqueue_script('tags-box');
                wp_enqueue_script('quicktags');
                wp_enqueue_script('admin-tags');
            }
        );
    }

    public static function output($post, $box) {

        ?>
        <p class="description">Tags are keywords that emphasises characters of your artwork. </p>
            <span class="tiptip description"
            title="<p>Artwork Tags helps customers to discover your work on search;</p><p>It also increases the chances of being displayed in the up-selling and cross-selling promotions section at customer checkout.</p>
            <p>However, too many tags will confuse the search engine. <br />
            So, please <strong>be brief</strong> and <strong>accurate!</strong></p>">
                Show More
            </span>

        </p>
        <p class="description">
            Please be brief and accurate.
        </p>
        <?php
        post_tags_meta_box($post, $box);
    }
}