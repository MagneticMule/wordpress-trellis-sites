<?php
/**
 * Vertical Menu widget migration.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v09_17_0;

use The7\Mods\Compatibility\Elementor\Upgrade\The7_Elementor_Widget_Migrations;

defined( 'ABSPATH' ) || exit;

/**
 * Vertical_Menu_Widget_Migration class.
 */
class Vertical_Menu_Widget_Migration extends The7_Elementor_Widget_Migrations {

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public static function get_widget_name() {
		return 'the7_nav-menu';
	}

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		$this->copy( 'align_items', 'align_sub_items' );
		$this->copy( 'icon_align', 'sub_icon_align' );
		$this->copy( 'menu_typography_font_size', 'icon_size' );
		$this->copy( 'sub_menu_typography_font_size', 'sub_icon_size' );
		$this->copy( 'icon_space', 'sub_icon_space' );

		foreach ( self::get_responsive_devices() as $device ) {
			$this->change_control_type_from_slider_to_dimensions( 'menu_border_radius', $device );
			$this->change_control_type_from_slider_to_dimensions( 'menu_sub_border_radius', $device );
			$this->migrate_margin_menu_item( 'margin_menu_item', $device );
			$this->migrate_margin_sub_menu_item( 'margin_sub_menu_item', $device );

			// It's important to remove these settings AFTER migration migration methods.
			// `migrate_margin_sub_menu_item` uses `margin_menu_item`.
			$this->remove( 'margin_sub_menu_item' . $device );
			$this->remove( 'margin_menu_item' . $device );
		}
	}

	/**
	 * Change control type from slider to dimensions.
	 *
	 * @param string $control Control id.
	 */
	protected function change_control_type_from_slider_to_dimensions( $control, $device ) {
		$device_control = $control . $device;
		$value          = $this->get( $device_control );

		if ( isset( $value['unit'], $value['size'] ) ) {
			$this->set(
				$device_control,
				[
					'unit'     => $value['unit'],
					'top'      => $value['size'],
					'bottom'   => $value['size'],
					'left'     => $value['size'],
					'right'    => $value['size'],
					'isLinked' => true,
				]
			);
		}
	}

	/**
	 * Migrate `margin_menu_item`.
	 */
	protected function migrate_margin_menu_item( $control, $device ) {
		$responsive_control = $control . $device;

		if ( ! $this->exists( $responsive_control ) ) {
			return;
		}

		$margin_menu_item = $this->get( $responsive_control );

		$this->add(
			'rows_gap' . $device,
			[
				'unit' => 'px',
				'size' => (int) $margin_menu_item['top'] + (int) $margin_menu_item['bottom'],
			]
		);

		$advanced_padding = $this->get( '_padding' . $device );
		if ( ! $advanced_padding ) {
			$advanced_padding = [
				'top'      => '0',
				'right'    => '0',
				'bottom'   => '0',
				'left'     => '0',
				'unit'     => 'px',
				'isLinked' => false,
			];
		}

		// Original `_padding` can be in `em`, `rem` and other units. Add `left` and `right` only in `_padding` in `px`.
		if ( $advanced_padding['unit'] === 'px' ) {
			$advanced_padding['isLinked'] = false;
			$advanced_padding['right']    = (string) ( (int) $advanced_padding['right'] + (int) $margin_menu_item['right'] );
			$advanced_padding['left']     = (string) ( (int) $advanced_padding['left'] + (int) $margin_menu_item['left'] );
			$this->set( '_padding' . $device, $advanced_padding );
		}
	}

	/**
	 * Migrate `margin_sub_menu_item`.
	 */
	protected function migrate_margin_sub_menu_item( $control, $device ) {
		$responsive_control = $control . $device;

		if ( ! $this->exists( $responsive_control ) ) {
			return;
		}

		$margin_sub_menu_item = $this->get( $responsive_control );

		$this->add(
			'sub_rows_gap' . $device,
			[
				'unit' => 'px',
				'size' => (int) $margin_sub_menu_item['top'] + (int) $margin_sub_menu_item['bottom'],
			]
		);

		$margin_menu_item_bottom = (int) $this->get_subkey( 'margin_menu_item' . $device, 'bottom' );

		$this->add(
			'padding_sub_menu' . $device,
			[
				'top'      => (string) ( (int) $margin_sub_menu_item['top'] + $margin_menu_item_bottom ),
				'right'    => $margin_sub_menu_item['right'],
				'bottom'   => '0',
				'left'     => $margin_sub_menu_item['left'],
				'unit'     => 'px',
				'isLinked' => false,
			]
		);
	}
}
