<?php
/**
 * @package The7
 */

namespace The7\Inc\Mods\ThemeUpdate\Migrations\v9_13_0;

use The7\Mods\Compatibility\Elementor\Upgrade\The7_Elementor_Widget_Migrations;

defined( 'ABSPATH' ) || exit;

/**
 * Class Posts_Carousel_Widget_Migration
 *
 * Some classes extends this class!
 *
 * @see     \The7\Inc\Mods\ThemeUpdate\Migrations\v9_13_0\Testimonials_Carousel_Widget_Migration
 * @see     \The7\Inc\Mods\ThemeUpdate\Migrations\v9_13_0\Text_And_Icon_Carousel_Widget_Migration
 *
 * @package The7\Inc\Mods\ThemeUpdate\Migrations\v9_13_0
 */
class Posts_Carousel_Widget_Migration extends The7_Elementor_Widget_Migrations {

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public static function get_widget_name() {
		return 'the7_elements_carousel';
	}

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		$controls = [
			'l_arrow_v_offset',
			'l_arrow_h_offset',
			'r_arrow_v_offset',
			'r_arrow_h_offset',
		];
		$devices  = [
			'',
			'_tablet',
			'_mobile',
		];

		foreach ( $controls as $control ) {
			foreach ( $devices as $device ) {
				$control_name = $control . $device;
				$offset       = $this->get( $control_name );
				if ( ! empty( $offset['unit'] ) && $offset['unit'] === '%' ) {
					$this->set(
						$control_name,
						[
							'unit' => 'px',
							'size' => -30,
						]
					);
				}
			}
		}
	}
}
