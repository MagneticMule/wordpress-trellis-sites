<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once __DIR__ . '/class-the7-demo-content.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function the7_demo_content() {
	static $instance = null;

	if ( null === $instance ) {
		$instance = new The7_Demo_Content();
	}

	return $instance;
}

the7_demo_content();

/**
 * @return array
 */
function the7_demo_get_quick_search_tags_list() {
	$tags                   = [];
	$demo_has_been_imported = false;
	foreach ( the7_demo_content()->get_demos() as $demo ) {
		$tags[] = $demo->tags;
		if ( $demo->partially_imported() ) {
			$demo_has_been_imported = true;
		}
	}
	$tags = array_unique( array_merge( [ 'elementor', 'wpbakery', 'gutenberg', 'store' ], ...$tags ) );
	if ( $demo_has_been_imported ) {
		$tags[] = 'imported';
	}

	return $tags;
}