<?php

namespace Dbout\WpMuPlugins;

/**
 * Class DisableComment
 * @package Dbout\WpMuPlugins
 *
 * @see https://github.com/solarissmoke/disable-comments-mu/blob/master/disable-comments-mu.php
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class DisableComment
{

    /**
     * DisableComment constructor.
     */
    public function __construct()
    {
        add_action('widgets_init',[$this, 'disableRcWidget']);
        add_filter('wp_headers', [$this, 'filterWpHeaders']);
        add_action('template_redirect', [$this, 'filterQuery'], 9);

        // Admin bar filtering has to happen here since WP 3.6
        add_action('template_redirect', [$this, 'filterAdminBar']);
        add_action('admin_init', [$this, 'filterAdminBar']);

        // these can happen later
        add_action('wp_loaded', [$this, 'setupFilters']);
    }

    /**
     * @return void
     */
    public function setupFilters(): void
    {
        $types = array_keys(get_post_types(['public' => true], 'objects' ));
        if (!empty($types)) {
            foreach($types as $type) {
                // we need to know what native support was for later
                if( post_type_supports($type, 'comments')) {
                    remove_post_type_support($type, 'comments');
                    remove_post_type_support($type, 'trackbacks');
                }
            }
        }

        // Filters for the admin only
        if (is_admin()) {
            add_action('admin_menu', [$this, 'filterAdminMenu'], PHP_INT_MAX);	// do this as late as possible
            add_action('admin_print_styles-index.php', [$this, 'adminCss']);
            add_action('admin_print_styles-profile.php', [$this, 'adminCss']);
            add_action('wp_dashboard_setup', [$this, 'filterDashboard'] );
            add_filter('pre_option_default_pingback_flag', '__return_zero' );
        }
        // Filters for front end only
        else {
            add_action('template_redirect', [$this, 'checkCommentTemplate']);
            add_filter('comments_open', '__return_false', 20, 2);
            add_filter('pings_open', '__return_false', 20, 2);

            // remove comments links from feed
            add_filter('post_comments_feed_link', '__return_false', 10, 1);
            add_filter('comments_link_feed', '__return_false', 10, 1);
            add_filter('comment_link', '__return_false', 10, 1);

            // remove comment count from feed
            add_filter('get_comments_number', '__return_false', 10, 2);

            // Remove feed link from header
            add_filter( 'feed_links_show_comments_feed', '__return_false');
        }
    }

    /**
     * @return void
     */
    public function checkCommentTemplate(): void
    {
        if( is_singular() ) {
            // Kill the comments template. This will deal with themes that don't check comment stati properly!
            add_filter( 'comments_template', [$this, 'dummyCommentsTemplate'], 20);
            // Remove comment-reply script for themes that include it indiscriminately
            wp_deregister_script('comment-reply');
            // Remove feed action
            remove_action('wp_head', 'feed_links_extra', 3);
        }
    }

    /**
     * @return string
     */
    public function dummyCommentsTemplate(): string
    {
        return dirname( __FILE__ ) . '/templates/comments-template.php';
    }

    /**
     * @param array $headers
     * @return array
     */
    public function filterWpHeaders($headers)
    {
        unset($headers['X-Pingback']);
        return $headers;
    }

    /**
     * @return void
     */
    public function filterQuery()
    {
        if (is_comment_feed()) {
            // we are inside a comment feed
            wp_die(__('Comments are closed.'), '', ['response' => 403]);
        }
    }

    /**
     * @return void
     */
    public function filterAdminBar(): void
    {
        if (is_admin_bar_showing()) {
            // Remove comments links from admin bar
            remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
            if (is_multisite()) {
                add_action('admin_bar_menu', [$this, 'removeNetworkCommentLinks'], 500);
            }
        }
    }

    /**
     * @param $wp_admin_bar
     * @return void
     */
    public function removeNetworkCommentLinks($wp_admin_bar): void
    {
        if (is_user_logged_in()) {
            foreach((array)$wp_admin_bar->user->blogs as $blog ) {
                $wp_admin_bar->remove_menu('blog-' . $blog->userblog_id . '-c');
            }
        }
    }

    /**
     * @return void
     */
    public function filterAdminMenu(): void
    {
        global $pagenow;
        if (in_array($pagenow, ['comment.php', 'edit-comments.php', 'options-discussion.php'])) {
            wp_die(__( 'Comments are closed.'), '', ['response' => 403]);
        }

        remove_menu_page('edit-comments.php');
        remove_submenu_page('options-general.php', 'options-discussion.php');
    }

    /**
     * @return void
     */
    public function filterDashboard(): void
    {
        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal');
    }

    /**
     * @return void
     */
    public function adminCss(): void
    {
        echo '<style>
			#dashboard_right_now .comment-count,
			#dashboard_right_now .comment-mod-count,
			#latest-comments,
			#welcome-panel .welcome-comments,
			.user-comment-shortcuts-wrap {
				display: none !important;
			}
		</style>';
    }

    /**
     * @return void
     */
    public function disableRcWidget(): void
    {
        // This widget has been removed from the Dashboard in WP 3.8 and can be removed in a future version
        unregister_widget( 'WP_Widget_Recent_Comments');
    }
}
