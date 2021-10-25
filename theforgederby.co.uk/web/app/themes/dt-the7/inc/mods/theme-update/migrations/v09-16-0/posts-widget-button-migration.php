<?php
/**
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v09_16_0;

defined( 'ABSPATH' ) || exit;

/**
 * Class Posts_Masonry_Widget_Button_Migration
 *
 * @package The7\Mods\Theme_Update\Migrations\v09_16_0
 */
class Posts_Widget_Button_Migration extends Button_Migration_Base {

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		if ( $this->get( 'show_read_more_button' ) === '' ) {
			return;
		}

		// Hide icon only if it was explicilty disabled. In other cases rename or use default.
		if ( $this->get( 'show_read_more_button_icon' ) === '' ) {
			$this->add(
				'button_icon',
				[
					'value'   => '',
					'library' => '',
				]
			);
		} else {
			$this->rename( 'read_more_button_icon', 'button_icon' );
		}
		$this->remove( 'show_read_more_button_icon' );

		$this->rename( 'read_more_button_icon_position', 'button_icon_position' );
		$this->rename( 'read_more_button_icon_spacing', 'button_icon_spacing' );
		$this->rename( 'button_bottom_margin', 'gap_above_button' );

		if ( $this->exists( 'button_background_color' ) ) {
			$this->add( 'button_background_background', 'classic' );
		}

		if ( $this->exists( 'button_background_hover_color' ) ) {
			$this->add( 'button_background_hover_background', 'classic' );
		}

		$this->override_xs_elementor_button_style();
	}

}
