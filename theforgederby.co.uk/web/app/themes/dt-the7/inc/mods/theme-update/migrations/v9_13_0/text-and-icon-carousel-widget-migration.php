<?php
/**
 * @package The7
 */

namespace The7\Inc\Mods\ThemeUpdate\Migrations\v9_13_0;

use The7\Mods\Compatibility\Elementor\Upgrade\The7_Elementor_Widget_Migrations;

defined( 'ABSPATH' ) || exit;

// Not included automatically.
require_once PRESSCORE_MODS_DIR . '/theme-update/migrations/v9_13_0/posts-carousel-widget-migration.php';

/**
 * Class Text_And_Icon_Carousel_Widget_Migration
 *
 * @package The7\Inc\Mods\ThemeUpdate\Migrations\v9_13_0
 */
class Text_And_Icon_Carousel_Widget_Migration extends Posts_Carousel_Widget_Migration {

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public static function get_widget_name() {
		return 'the7_content_carousel';
	}
}
