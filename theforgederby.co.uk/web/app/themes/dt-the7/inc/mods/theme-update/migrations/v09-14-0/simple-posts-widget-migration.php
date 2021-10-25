<?php
/**
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v09_14_0;

use The7\Mods\Compatibility\Elementor\Upgrade\The7_Elementor_Widget_Migrations;

defined( 'ABSPATH' ) || exit;

/**
 * Class Simple_Posts_Widget_Migration
 *
 * @package The7\Inc\Mods\ThemeUpdate\Migrations\v09-14-0
 */
class Simple_Posts_Widget_Migration extends The7_Elementor_Widget_Migrations {

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public static function get_widget_name() {
		return 'the7-elements-simple-posts';
	}

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		$border_radius = $this->get_subkey( 'image_border_radius', 'size' );
		if ( $border_radius !== null ) {
			$this->set(
				'image_border_radius',
				[
					'top'      => $border_radius,
					'right'    => $border_radius,
					'bottom'   => $border_radius,
					'left'     => $border_radius,
					'unit'     => 'px',
					'isLinked' => true,
				]
			);
		}
	}
}
