<?php
/**
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v09_16_0;

defined( 'ABSPATH' ) || exit;

/**
 * Class Products_Widget_Button_Migration
 *
 * @package The7\Mods\Theme_Update\Migrations\v09_16_0
 */
class Products_Widget_Button_Migration extends Button_Migration_Base {

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		$this->rename( 'show_add_to_cart_icon', 'button_icon' );
		$this->rename( 'button_bottom_margin', 'gap_above_button' );

		$this->override_xs_elementor_button_style();
	}
}
