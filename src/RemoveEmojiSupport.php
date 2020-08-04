<?php

namespace Dbout\WpMuPlugins;

/**
 * Class RemoveEmojiSupport
 * @package Dbout\WpMuPlugins
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class RemoveEmojiSupport
{

    /**
     * @return void
     */
    public static function remove()
    {

        // Front
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );

        // Admin
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');

        // Feeds
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');

        // Embeds
        remove_filter('embed_head', 'print_emoji_detection_script');

        // Disable in database
        if ((int) get_option('use_smilies') === 1) {
            update_option('use_smilies', 0);
        }
    }
}
