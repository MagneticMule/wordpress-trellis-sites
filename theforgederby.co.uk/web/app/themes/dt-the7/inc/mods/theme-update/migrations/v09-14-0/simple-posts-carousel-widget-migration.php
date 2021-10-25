<?php
/**
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v09_14_0;

defined( 'ABSPATH' ) || exit;

/**
 * Class Simple_Posts_Carousel_Widget_Migration
 */
class Simple_Posts_Carousel_Widget_Migration extends Simple_Posts_Widget_Migration {

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public static function get_widget_name() {
		return 'the7-elements-simple-posts-carousel';
	}
}
