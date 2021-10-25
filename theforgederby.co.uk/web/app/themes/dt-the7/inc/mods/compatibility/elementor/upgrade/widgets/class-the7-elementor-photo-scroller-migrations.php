<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Upgrade\Widgets;

use The7\Mods\Compatibility\Elementor\Upgrade\The7_Elementor_Widget_Migrations;

defined( 'ABSPATH' ) || exit;

class The7_Elementor_Photo_Scroller_Migrations extends The7_Elementor_Widget_Migrations {

	public static function get_widget_name() {
		return 'the7_photo-scroller';
	}

	public static function _9_3_1_migration( $element, $args ) {
		if ( empty( $element['widgetType'] ) || $element['widgetType'] !== self::get_widget_name() ) {
			return $element;
		}

		$settings = $element['settings'];

		// Migrate arrows visibility settings.
		if ( isset( $settings['arrows'] ) ) {

			// Upgrade the old value.
			if ( $settings['arrows'] === 'yes' ) {
				$settings['arrows'] = 'y';
			}

			// Turn off arrows on mobile if they were diabled altogether.
			if ( $settings['arrows'] === '' ) {
				$settings['arrows_mobile'] = '';
				$settings['arrows_tablet'] = '';
			}
		}

		// Maybe disable arrows on mobile.
		if ( isset( $settings['arrow_responsiveness'] ) && $settings['arrow_responsiveness'] === 'hide-arrows' ) {
			$settings['arrows_mobile'] = '';
			$settings['arrows_tablet'] = '';
		}
		unset( $settings['arrow_responsiveness'] );

		// Migrate normal arrows colors after switches removal.
		$switches_to_colors = [
			'arrow_icon_border'       => 'arrow_border_color',
			'arrows_bg_show'          => 'arrow_bg_color',
			'arrow_icon_border_hover' => 'arrow_border_color_hover',
			'arrows_bg_hover_show'    => 'arrow_bg_color_hover',
		];
		foreach ( $switches_to_colors as $switch => $color ) {
			if ( ! isset( $settings[ $color ] ) ) {
				if ( empty( $settings[ $switch ] ) || $settings[ $switch ] === 'n' ) {
					$settings[ $color ] = '#00000000';
				} else {
					$settings[ $color ] = the7_theme_accent_color();
				}
			}

			unset( $settings[ $switch ] );
		}

		$element['settings'] = $settings;
		$args['do_update']   = true;

		return $element;
	}

}