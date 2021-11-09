<?php
/**
 * Plugin Name: NS Featured Posts
 * Plugin URI: https://www.nilambar.net/2014/07/ns-featured-posts-wordpress-plugin.html
 * Description: Plugin to make your posts, pages and custom post types Featured
 * Version: 2.0.3
 * Author: Nilambar Sharma
 * Author URI: https://www.nilambar.net
 * Text Domain: ns-featured-posts
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package NS_Featured_Posts
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'NS_FEATURED_POSTS_BASENAME', basename( dirname( __FILE__ ) ) );
define( 'NS_FEATURED_POSTS_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
define( 'NS_FEATURED_POSTS_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );

// Init autoload.
require_once NS_FEATURED_POSTS_DIR . '/vendor/autoload.php';

// Load classes.
require_once NS_FEATURED_POSTS_DIR . '/includes/classes/class-ns-featured-posts.php';
require_once NS_FEATURED_POSTS_DIR . '/includes/classes/class-ns-featured-posts-admin.php';

// Load widget.
require_once NS_FEATURED_POSTS_DIR . '/includes/widgets/nsfp-featured-post-widget.php';

// Register hooks that are fired when the plugin is activated or deactivated.
register_activation_hook( __FILE__, array( 'NS_Featured_Posts', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'NS_Featured_Posts', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'NS_Featured_Posts', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'NS_Featured_Posts_Admin', 'get_instance' ) );
