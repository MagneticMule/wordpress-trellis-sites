<?php
/**
 * Buttons migration base class. Contain common logic.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v09_16_0;

use The7\Mods\Compatibility\Elementor\Upgrade\The7_Elementor_Widget_Migrations;
use The7_Admin_Dashboard_Settings;
use The7_Option_Field_Number;
use The7_Option_Field_Spacing;

defined( 'ABSPATH' ) || exit;

/**
 * Button_Migration_Base class.
 */
class Button_Migration_Base extends The7_Elementor_Widget_Migrations {

	/**
	 * Override elementor XS button style by values from theme options.
	 */
	protected function override_xs_elementor_button_style() {

		// Run this method only if button integration is off.
		if ( The7_Admin_Dashboard_Settings::get( 'elementor-buttons-integration' ) !== false ) {
			return;
		}

		$border_radius = of_get_option( 'buttons-s_border_radius' );
		if ( $border_radius ) {
			$border_radius_decoded = The7_Option_Field_Number::sanitize( $border_radius, 'px|%|em' );
			$this->add(
				'button_border_radius',
				[
					'unit'     => $border_radius_decoded['units'],
					'top'      => (string) $border_radius_decoded['val'],
					'left'     => (string) $border_radius_decoded['val'],
					'bottom'   => (string) $border_radius_decoded['val'],
					'right'    => (string) $border_radius_decoded['val'],
					'isLinked' => true,
				]
			);
		}

		$padding = of_get_option( 'buttons-s_padding' );
		if ( $padding ) {
			$padding_decoded = The7_Option_Field_Spacing::sanitize( $padding, 'px|%|em' );
			$this->add(
				'button_text_padding',
				[
					'unit'     => $padding_decoded[0]['units'],
					'top'      => (string) $padding_decoded[0]['val'],
					'left'     => (string) $padding_decoded[1]['val'],
					'bottom'   => (string) $padding_decoded[2]['val'],
					'right'    => (string) $padding_decoded[3]['val'],
					'isLinked' => false,
				]
			);
		}

		$font = of_get_option( 'buttons-s-typography' );
		if ( isset( $font['font_size'] ) && ! $this->is_global( 'button_typography_typography' ) ) {

			// Cleanup typography if it's not custom.
			if ( $this->get( 'button_typography_typography' ) !== 'custom' ) {
				$this->remove_typography( 'button_typography' );
			}

			$this->add( 'button_typography_typography', 'custom' );
			$this->add(
				'button_typography_font_size',
				[
					'unit' => 'px',
					'size' => (int) $font['font_size'],
				]
			);
			$this->add(
				'button_typography_line_height',
				[
					'unit' => 'px',
					'size' => 2 + (int) $font['font_size'],
				]
			);
		}
	}
}
