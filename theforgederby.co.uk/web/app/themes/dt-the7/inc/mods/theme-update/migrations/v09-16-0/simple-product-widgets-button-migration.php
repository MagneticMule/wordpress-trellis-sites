<?php
/**
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v09_16_0;

defined( 'ABSPATH' ) || exit;

/**
 * Class Simple_Product_Widgets_Button_Migration
 *
 * @package The7\Mods\Theme_Update\Migrations\v09_16_0
 */
class Simple_Product_Widgets_Button_Migration extends Button_Migration_Base {

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		if ( $this->get( 'show_add_to_cart' ) === '' ) {
			return;
		}

		if ( $this->exists( 'button_background_color' ) ) {
			$this->add( 'button_background_background', 'classic' );
		}
		if ( $this->exists( 'button_background_hover_color' ) ) {
			$this->add( 'button_background_hover_background', 'classic' );
		}

		$this->override_xs_elementor_button_style();
	}
}
