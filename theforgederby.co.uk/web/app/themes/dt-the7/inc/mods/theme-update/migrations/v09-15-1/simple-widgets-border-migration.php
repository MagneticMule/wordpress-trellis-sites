<?php
/**
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v09_15_1;

use The7\Mods\Compatibility\Elementor\Upgrade\The7_Elementor_Widget_Migrations;

defined( 'ABSPATH' ) || exit;

/**
 * Class Carousel_Widget_Width_Migration
 *
 * @package The7\Mods\Theme_Update\Migrations\v09_15_1
 */
class Simple_Widgets_Border_Migration extends The7_Elementor_Widget_Migrations {

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		if ( $this->exists( 'box_border_width' ) ) {
			$this->add( 'box_border_border', 'solid' );
		}
	}
}
