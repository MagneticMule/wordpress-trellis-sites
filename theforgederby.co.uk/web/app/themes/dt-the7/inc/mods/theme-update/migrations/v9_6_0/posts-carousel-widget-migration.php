<?php
/**
 * @package The7
 */

namespace The7\Inc\Mods\ThemeUpdate\Migrations\v9_6_0;

use The7\Mods\Compatibility\Elementor\Upgrade\The7_Elementor_Widget_Migrations;

defined( 'ABSPATH' ) || exit;

class Posts_Carousel_Widget_Migration extends The7_Elementor_Widget_Migrations {

	public static function get_widget_name() {
		return 'the7_elements_carousel';
	}

	public function do_apply() {
		// Bullets.
		$this->add( 'show_bullets', '' );
		$this->add( 'show_bullets_tablet', '' );
		$this->add( 'show_bullets_mobile', '' );

		// Rename typography to prevent unrelated settings reset.

		$this->rename_typography( 'post_content', 'post_content_typography' );

		// Post category meta.
		$this->rename( 'post_category', 'post_terms' );
		$this->add( 'dis_posts_total', 6 );

		$this->migrate_read_more_button();
		$this->migrate_arrows();
		$this->migrate_icon();
		$this->migrate_content();
		$this->migrate_image();
		$this->migrate_columns();
		$this->migrate_meta_typography();
		$this->migrate_post_title_typography();
	}

	protected function migrate_read_more_button() {
		if ( ! $this->exists( 'read_more_button' ) ) {
			return;
		}

		$read_more_button = $this->get( 'read_more_button' );

		$this->add( 'show_read_more_button', $read_more_button === 'off' ? '' : 'y' );

		if ( $read_more_button === 'default_link' ) {
			$this->add( 'button_background_color', '#00000000' );
			$this->add( 'button_text_color', of_get_option( 'content-headers_color' ) );
			$this->add( 'button_typography_typography', 'custom' );
			$this->add(
				'button_typography_font_size',
				[
					'unit' => 'px',
					'size' => 14,
				]
			);
			$this->add( 'button_typography_font_weight', '700' );
			$this->add(
				'button_typography_line_height',
				[
					'unit' => 'px',
					'size' => 18,
				]
			);
			$this->add( 'button_border_border', 'solid' );
			$this->add( 'button_border_color', '#00000000' );
			$this->add( 'button_hover_border_color', the7_theme_accent_color() );
			$this->add(
				'button_border_width',
				[
					'unit'     => 'px',
					'top'      => '0',
					'bottom'   => '2',
					'left'     => '0',
					'right'    => '0',
					'isLinked' => false,
				]
			);
			$this->add(
				'button_text_padding',
				[
					'unit'     => 'px',
					'top'      => '0',
					'bottom'   => '5',
					'left'     => '0',
					'right'    => '0',
					'isLinked' => false,
				]
			);
		}

		$this->remove( 'read_more_button' );
	}

	protected function migrate_meta_typography() {
		$this->rename_typography( 'post_meta', 'post_meta_typography' );
	}

	protected function migrate_post_title_typography() {
		if ( ! in_array( $this->get( 'post_layout' ), [ 'gradient_rollover', 'gradient_overlay' ], true ) ) {
			$this->add( 'post_title_color_hover', the7_theme_accent_color() );
		}

		$this->add(
			'post_title_bottom_margin',
			[
				'unit' => 'px',
				'size' => 5,
			]
		);

		$this->rename_typography( 'post_title', 'post_title_typography' );

		if ( $this->is_global( 'post_title_typography_typography' ) ) {
			return;
		}

		$is_custom_typography = $this->get( 'post_title_typography_typography' ) === 'custom';

		$title_was_changed = false;

		if ( ! $is_custom_typography || ! $this->get( 'post_title_typography_font_weight' ) ) {
			$this->set( 'post_title_typography_font_weight', 'normal' );
			$title_was_changed = true;
		}

		$h4_typography = of_get_option( 'fonts-h4-typography', [] );

		$title_typography = [
			'post_title_typography_font_size'   => 'responsive_font_size',
			'post_title_typography_line_height' => 'responsive_line_height',
		];

		foreach ( $title_typography as $setting => $option ) {
			$option_value = isset( $h4_typography[ $option ]['desktop'] ) ? $h4_typography[ $option ]['desktop'] : null;

			if ( ! $option_value ) {
				continue;
			}

			if ( ! $is_custom_typography || ! $this->get_subkey( $setting, 'size' ) ) {
				$decoded_option_value = \The7_Option_Field_Slider::decode( $option_value );
				$this->set(
					$setting,
					[
						'unit'  => $decoded_option_value['units'] ?: 'px',
						'size'  => $decoded_option_value['val'],
						'sizes' => [],
					]
				);
				$title_was_changed = true;
			}
		}

		if ( $title_was_changed ) {
			$this->set( 'post_title_typography_typography', 'custom' );
		}
	}

	protected function migrate_columns() {
		$this->rename( 'wide_desk_columns', 'widget_columns_wide_desktop' );
		$this->rename( 'desktop_columns', 'widget_columns' );
		$this->rename( 'tablet_h_columns', 'widget_columns_tablet' );
		$this->rename( 'phone_columns', 'widget_columns_mobile' );
		$this->remove( [ 'laptop_columns', 'tablet_v_columns' ] );
	}

	protected function migrate_image() {
		// Image proportions.
		$image_sizing = $this->get( 'image_sizing' );
		$size         = '';
		$unit         = 'px';
		if ( ! $image_sizing || $image_sizing === 'resize' ) {
			$image_width  = (int) $this->get( 'resize_image_to_width' ) ?: 1;
			$image_height = (int) $this->get( 'resize_image_to_height' ) ?: 1;
			if ( $image_width && $image_height ) {
				$size = the7_get_image_proportion( $image_width, $image_height );
			}
			$this->remove( 'resize_image_to_width' );
			$this->remove( 'resize_image_to_height' );
		}
		$this->add( 'item_ratio', compact( 'size', 'unit' ) );
		$this->remove( 'image_sizing' );

		// Image border radius.
		if ( $this->exists( 'image_border_radius' ) ) {
			$image_border_radius = $this->get( 'image_border_radius' );
			$this->add(
				'img_border_radius',
				[
					'top'      => $image_border_radius['size'],
					'right'    => $image_border_radius['size'],
					'bottom'   => $image_border_radius['size'],
					'left'     => $image_border_radius['size'],
					'unit'     => $image_border_radius['unit'],
					'isLinked' => true,
				]
			);
			$this->remove( 'image_border_radius' );
		}

		// Image shadow.
		if ( $this->exists( 'image_decoration' ) ) {
			$img_shadow_type = '';
			if ( $this->get( 'image_decoration' ) === 'shadow' ) {
				$img_shadow_type = 'yes';
				$this->add(
					'img_shadow_box_shadow',
					[
						'horizontal' => $this->get_subkey( 'shadow_h_length', 'size' ),
						'vertical'   => $this->get_subkey( 'shadow_v_length', 'size' ),
						'blur'       => $this->get_subkey( 'shadow_blur_radius', 'size' ),
						'spread'     => $this->get_subkey( 'shadow_spread', 'size' ),
						'color'      => $this->get( 'shadow_color' ),
					]
				);
			}
			$this->add( 'img_shadow_box_shadow_type', $img_shadow_type );
			$this->remove(
				[
					'image_decoration',
					'shadow_h_length',
					'shadow_v_length',
					'shadow_blur_radius',
					'shadow_spread',
					'shadow_color',
				]
			);
		}

		// Image hover color.
		$image_hover_type = $this->get( 'image_hover_bg_color' );
		if ( in_array( $image_hover_type, [ 'disabled', 'solid_rollover_bg' ], true ) ) {
			$image_hover_color = '#00000000';
			if ( $image_hover_type === 'solid_rollover_bg' ) {
				$image_hover_color = $this->get( 'custom_rollover_bg_color' );
			}
			$this->add( 'overlay_hover_background_background', 'classic' );
			$this->add( 'overlay_hover_background_color', $image_hover_color );
		}
		$this->remove(
			[
				'image_hover_bg_color',
				'custom_rollover_bg_color',
			]
		);
	}

	protected function migrate_content() {
		// Show excerpt.
		if ( $this->get( 'post_content' ) === 'off' ) {
			$this->set( 'post_content', '' );
		}

		// Content width.
		if ( $this->get( 'post_layout' ) === 'bottom_overlap' ) {
			$this->add(
				'bo_content_width',
				[
					'unit' => '%',
					'size' => 75,
				]
			);
			$this->add( 'post_content_box_alignment', 'center' );
		} else {
			$this->remove( 'bo_content_width' );
		}

		// Content background color switcher.
		if ( $this->get( 'content_bg' ) === '' ) {
			$this->set( 'custom_content_bg_color', '#00000000' );
		}
		$this->remove( 'content_bg' );

		$post_layout = $this->get( 'post_layout' );
		if ( ! $post_layout || $post_layout === 'classic' ) {
			$this->rename( 'custom_content_bg_color', 'box_background_color' );
		}

		$this->add( 'box_background_color', '#00000000' );

		$this->add(
			'post_content_bottom_margin',
			[
				'unit' => 'px',
				'size' => 5,
			]
		);
		$post_content_bottom_margin = $this->get( 'post_content_bottom_margin' );
		if ( isset( $post_content_bottom_margin['size'] ) ) {
			$post_content_bottom_margin['size'] = (int) $post_content_bottom_margin['size'] + 10;
			$this->set( 'post_content_bottom_margin', $post_content_bottom_margin );
		}
	}

	protected function migrate_icon() {
		// Icon.
		$this->rename( 'show_details', 'show_details_icon' );

		$icon_border_settings = [
			'project_icon_border_width',
			'project_icon_border_radius',
		];
		foreach ( $icon_border_settings as $icon_border_setting ) {
			if ( isset( $this->settings[ $icon_border_setting ]['size'] ) ) {
				$this->settings[ $icon_border_setting ] = [
					'top'      => $this->settings[ $icon_border_setting ]['size'],
					'right'    => $this->settings[ $icon_border_setting ]['size'],
					'bottom'   => $this->settings[ $icon_border_setting ]['size'],
					'left'     => $this->settings[ $icon_border_setting ]['size'],
					'unit'     => $this->settings[ $icon_border_setting ]['unit'],
					'isLinked' => true,
				];
				unset( $this->settings[ $icon_border_setting ]['size'] );
			}
		}

		// Set icon defasult border radius.
		$this->add(
			'project_icon_border_radius',
			[
				'top'      => 100,
				'right'    => 100,
				'bottom'   => 100,
				'left'     => 100,
				'unit'     => 'px',
				'isLinked' => true,
			]
		);

		if ( isset( $this->settings['project_icon_below_gap']['size'] ) || isset( $this->settings['project_icon_above_gap']['size'] ) ) {
			$this->add(
				'project_icon_margin',
				[
					'top'      => $this->settings['project_icon_above_gap']['size'],
					'right'    => '',
					'bottom'   => $this->settings['project_icon_below_gap']['size'],
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				]
			);
		}
		$this->remove( 'project_icon_below_gap' );
		$this->remove( 'project_icon_above_gap' );

		// Color switches to colors.
		$color_switches = [
			'arrow_icon_border'              => 'arrow_border_color',
			'arrows_bg_show'                 => 'arrow_bg_color',
			'arrow_icon_border_hover'        => 'arrow_border_color_hover',
			'arrows_bg_hover_show'           => 'arrow_bg_color_hover',
			'show_project_icon_border'       => 'project_icon_border_color',
			'project_icon_bg'                => 'project_icon_bg_color',
			'show_project_icon_hover_border' => 'project_icon_border_color_hover',
			'project_icon_bg_hover'          => 'project_icon_bg_color_hover',
		];
		foreach ( $color_switches as $switch => $color ) {
			if ( $this->get( $switch ) === '' ) {
				$this->set( $color, '#00000000' );
			}
			$this->remove( $switch );
		}

		$this->add( 'project_icon_color', the7_theme_accent_color() );
		$this->add( 'project_icon_bg_color', '#FFFFFF4D' );

		// Prevent hover colors transition.
		$icon_colors = [
			'project_icon_color'        => 'project_icon_color_hover',
			'project_icon_bg_color'     => 'project_icon_bg_color_hover',
			'project_icon_border_color' => 'project_icon_border_color_hover',
		];
		foreach ( $icon_colors as $color => $hover_color ) {
			$color_value = $this->get( $color );

			if ( $color_value && $color_value === $this->get( $hover_color ) ) {
				$this->remove( $hover_color );
			}

			$color_global_value = $this->get_global( $color );
			if ( $color_global_value && $color_global_value === $this->get_global( $hover_color ) ) {
				$this->remove_global( $hover_color );
			}

			$dynamic_color_value = $this->get_dynamic_tag_decoded( $color );
			if ( $dynamic_color_value && $dynamic_color_value === $this->get_dynamic_tag_decoded( $hover_color ) ) {
				$this->remove_dynamic_tag( $hover_color );
			}
		}

		// Icon hover color switch.
		if ( $this->get( 'enable_project_icon_hover' ) === '' ) {
			$this->remove( 'project_icon_border_color_hover' );
			$this->remove( 'project_icon_bg_color_hover' );
		}
		$this->remove( 'enable_project_icon_hover' );

		// Set icon default size.
		$this->add(
			'project_icon_bg_size',
			[
				'unit' => 'px',
				'size' => 44,
			]
		);
	}

	protected function migrate_arrows() {
		// Arrows.
		if ( $this->exists( 'arrow_responsiveness' ) ) {
			switch ( $this->get( 'arrow_responsiveness' ) ) {
				case 'hide-arrows':
					$this->add( 'arrows_mobile', '' );
					break;

				case 'reposition-arrows':
					$this->rename( 'l_arrows_mobile_h_position', 'l_arrow_h_offset_mobile' );
					$this->rename( 'r_arrows_mobile_h_position', 'r_arrow_h_offset_mobile' );
					break;
			}

			$this->remove( 'arrow_responsiveness' );
		}

		if ( $this->exists( 'arrows' ) ) {
			$this->add( 'arrows_tablet', $this->get( 'arrows' ) );
			$this->add( 'arrows_mobile', $this->get( 'arrows' ) );
		}

		$this->add(
			'arrow_border_width',
			[
				'unit' => 'px',
				'size' => 0,
			]
		);

		$arrows_icon_color = [
			'arrow_icon_color'       => '#ffffff',
			'arrow_icon_color_hover' => 'rgba(255,255,255,0.75)',
		];
		foreach ( $arrows_icon_color as $arrow_icon_color => $arrow_icon_color_deafult ) {
			if ( $this->is_global( $arrow_icon_color ) ) {
				//				continue;
			}

			if ( $arrow_icon_color === 'arrow_icon_color_hover' && $this->get( $arrow_icon_color ) === '' ) {
				$this->set( $arrow_icon_color, the7_theme_accent_color() );
			}

			$this->add( $arrow_icon_color, $arrow_icon_color_deafult );
		}

		$this->add(
			'arrow_bg_width',
			[
				'unit' => 'px',
				'size' => 36,
			]
		);
		$this->add(
			'arrow_bg_height',
			[
				'unit' => 'px',
				'size' => 36,
			]
		);
		$this->add(
			'r_arrow_h_offset',
			[
				'unit' => 'px',
				'size' => -43,
			]
		);
		$this->add(
			'l_arrow_h_offset',
			[
				'unit' => 'px',
				'size' => -43,
			]
		);

		$accent_color = the7_theme_accent_color();
		if ( $accent_color ) {
			$this->add( 'arrow_bg_color', $accent_color );
			$this->add( 'arrow_bg_color_hover', $accent_color );
		}
	}
}