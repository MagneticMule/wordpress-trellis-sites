<?php

/**
 * Will disable elementor kit manager (theme styles).
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor;

use Elementor\Plugin as Elementor;
use The7\Mods\Compatibility\Elementor\Modules\Kits\The7_Kit;
use The7_Less_Vars_Value_Font;
use The7_Less_Vars_Value_Number;
use The7_Option_Field_Font_Sizes;
use The7_Option_Field_Typography;

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_Kit_Manager_Control
 */
class The7_Kit_Manager_Control {


	public function bootstrap() {
		add_action( 'elementor/init', [ $this, 'disable_elementor_kit_manager' ], 1 );
		add_action( 'after_switch_theme', [ $this, 'update_kit_css' ] );
		add_action( 'optionsframework_options_saved', [ $this, 'update_kit_css' ] );
		add_action( 'the7_maybe_regenerate_dynamic_css_done', [ $this, 'update_kit_css' ] );
	}

	public function disable_elementor_kit_manager() {
		$kits_manager = Elementor::instance()->kits_manager;
		if ( the7_is_elementor2() ) {
			remove_action( 'elementor/documents/register', [ $kits_manager, 'register_document' ] );
			remove_filter( 'elementor/editor/localize_settings', [ $kits_manager, 'localize_settings' ] );
			remove_filter( 'elementor/editor/footer', [ $kits_manager, 'render_panel_html' ] );
			remove_action( 'elementor/frontend/after_enqueue_global', [
				$kits_manager,
				'frontend_before_enqueue_styles',
			], 0 );
			remove_action( 'elementor/preview/enqueue_styles', [ $kits_manager, 'preview_enqueue_styles' ], 0 );
		} else {
			remove_action( 'elementor/documents/register', [ $kits_manager, 'register_document' ] );
			add_action( 'elementor/documents/register', [ $this, 'register_document' ] );
			add_filter( 'elementor/editor/localize_settings', [ $this, 'localize_settings' ], 50 );
			//handle global colors
			add_filter( 'rest_request_after_callbacks', [ $this, 'handle_kit_globals' ], 10, 3 );
		}
	}

	public function register_document( $documents_manager ) {
		require_once __DIR__ . '/modules/kits/class-the7-kit.php';
		$documents_manager->register_document_type( 'kit', The7_Kit::get_class_full_name() );
	}

	public function localize_settings( $settings ) {
		$settings = array_replace_recursive( $settings, [
			'i18n' => [
				'theme_style' => "",
			],
		] );

		return $settings;
	}

	public function handle_kit_globals( $response, $handler, $request ) {
		$route = $request->get_route();
		if ( $request->get_method() === 'GET' && strpos( $route, '/elementor/v1/globals' ) !== false ) {
			if ( $route === '/elementor/v1/globals' ) {
				$the7_colors     = self::get_the7_kit_colors();
				$the7_typography = self::get_the7_kit_typography();

				if ( isset( $response->data['colors'] ) ) {
					$response->data['colors'] = array_merge( $response->data['colors'], $the7_colors );
				}

				if ( isset( $response->data['typography'] ) ) {
					$response->data['typography'] = array_merge( $response->data['typography'], $the7_typography );
				}
			} elseif ( strpos( $route, '/elementor/v1/globals/colors/' ) === 0 ) {
				$the7_colors = self::get_the7_kit_colors();
				$param_id    = $request->get_param( 'id' );
				if ( array_key_exists( $param_id, $the7_colors ) ) {
					$response = rest_ensure_response( $the7_colors[ $param_id ] );
				}
			} elseif ( strpos( $route, '/elementor/v1/globals/typography/' ) === 0 ) {
				$the7_typography = self::get_the7_kit_typography();
				$param_id        = $request->get_param( 'id' );
				if ( array_key_exists( $param_id, $the7_typography ) ) {
					$response = rest_ensure_response( $the7_typography[ $param_id ] );
				}
			}
		}

		return $response;
	}

	private static function get_the7_kit_colors() {
		$colors = [
			'the7-content-headers_color'           => __( 'Headings', 'the7mk2' ),
			'the7-content-primary_text_color'      => __( 'Primary text', 'the7mk2' ),
			'the7-content-secondary_text_color'    => __( 'Secondary text', 'the7mk2' ),
			'the7-content-links_color'             => __( 'Links color', 'the7mk2' ),
			'the7-accent'                          => __( 'Accent', 'the7mk2' ),
			'the7-buttons-color_mode'              => __( 'Button background normal', 'the7mk2' ),
			'the7-buttons-hover_color_mode'        => __( 'Button background hover', 'the7mk2' ),
			'the7-buttons-text_color_mode'         => __( 'Button text normal', 'the7mk2' ),
			'the7-buttons-text_hover_color_mode'   => __( 'Button text hover', 'the7mk2' ),
			'the7-buttons-border-color_mode'       => __( 'Button border normal', 'the7mk2' ),
			'the7-buttons-hover-border-color_mode' => __( 'Button border hover', 'the7mk2' ),
			'the7-dividers-color'                  => __( 'Dividers', 'the7mk2' ),
			'the7-general-content_boxes_bg_color'  => __( 'Content boxes background', 'the7mk2' ),
		];

		$result = [];
		foreach ( $colors as $key => $title ) {
			$key_filtered = str_replace( "-", "_", $key );
			$result[ $key_filtered ] = [
				'id'    => $key_filtered,
				'title' => 'The7 ' . $title,
				'value' => the7_theme_get_color( str_replace( "the7-", "", $key ) ),
			];
		}

		return $result;
	}

	private static function get_the7_kit_typography() {
		$typographys = [];

		for ( $id = 1; $id <= 6; $id ++ ) {
			$typographys["the7-fonts-h{$id}-typography"] = [
				'title' => __( "Headings {$id}", 'the7mk2' ),
				'id'    => "the7-h{$id}",
			];
		}
		$font_fields = array(
			'fonts-widget-title'    => array(
				'font_desc' => __( 'Widget title', 'the7mk2' ),
			),
			'fonts-widget-content'    => array(
				'font_desc' => __( 'Widget content', 'the7mk2' ),
			),
			'fonts-woo-title'    => array(
				'font_desc' => __( 'Product title', 'the7mk2' ),
			),
			'fonts-woo-content'    => array(
				'font_desc' => __( 'Product content', 'the7mk2' ),
			),
		);
		foreach ( $font_fields as $id => $data ) {
			$typographys["the7-{$id}"] = [
				'title'       => $data['font_desc'],
				'id'          => "the7-{$id}",
			];
		}

		//combine font sizes and main font
		$font_sizes = array(
			'big_size'    => array(
				'font_desc' => __( 'Large font', 'the7mk2' ),
			),
			'normal_size' => array(
				'font_desc' => __( 'Medium font', 'the7mk2' ),
			),
			'small_size'  => array(
				'font_desc' => __( 'Small font', 'the7mk2' ),
			),
		);

		foreach ( $font_sizes as $id => $data ) {
			$typographys["the7-fonts-{$id}"] = [
				'title'       => $data['font_desc'],
				'id'          => "the7-{$id}",
				'font-family' => "fonts-font_family",
				'sizes-option-name'  => "fonts-{$id}",
			];
		}
		$result = [];
		foreach ( $typographys as $key => $typography_val ) {
			$key_filtered = str_replace( "-", "_", $typography_val['id'] );

			$result[ $key_filtered ] = [
				'id'    => $key_filtered,
				'title' => 'The7 ' . $typography_val['title'],
				'value' => [ 'typography_typography' => 'custom' ],
			];

			$arr_val = &$result[ $key_filtered ]['value'];

			$option_name = '';
			if ( isset( $typography_val['font-family'] ) ) {
				$option_name = $typography_val['font-family'];
			} else {
				$option_name = str_replace( "the7-", "", $key );
			}

			$option = of_get_option( $option_name );

			if ( ! is_array( $option ) ) {
				$option = [ 'font_family' => $option ];
			}
			if ( isset( $typography_val['sizes-option-name'] ) ) {
				$font_sizes = The7_Option_Field_Font_Sizes::sanitize( of_get_option( $typography_val['sizes-option-name'] ) );
				$option['responsive_font_size'] = [ 'desktop' => $font_sizes['font_size'] ];
				$option['responsive_line_height'] = [ 'desktop' => $font_sizes['line_height'] ];
			}

			$typography = The7_Option_Field_Typography::sanitize( $option );

			$the7_web_font = new The7_Less_Vars_Value_Font( $typography['font_family'] );

			$arr_val['typography_font_family'] = $the7_web_font->get_family();

			if ( $the7_web_font->get_weight() != '~""' ) {
				$arr_val['typography_font_weight'] = $the7_web_font->get_weight();
			}

			if ( $the7_web_font->get_style() != '~""' ) {
				$arr_val['typography_font_style'] = $the7_web_font->get_style();
			}

			if ( isset( $typography['text_transform'] ) && ! empty( $typography['text_transform'] )) {
				$arr_val["typography_text_transform"] = $typography['text_transform'];
			}

			foreach ( $typography['responsive_font_size'] as $device => $val ) {
				if ( $device === 'desktop' ) {
					$device = '';
				} else {
					$device = "_{$device}";
				}
				$var = new The7_Less_Vars_Value_Number( $val );
				$data = [
					'unit'  => $var->get_units(),
					'size'  => $var->get_val(),
					'sizes' => [],
				];

				$arr_val["typography_font_size{$device}"] = $data;
			}
			foreach ( $typography['responsive_line_height'] as $device => $val ) {
				if ( $device === 'desktop' ) {
					$device = '';
				} else {
					$device = "_{$device}";
				}
				$var = new The7_Less_Vars_Value_Number( $val );
				$data = [
					'unit'  => $var->get_units(),
					'size'  => $var->get_val(),
					'sizes' => [],
				];

				$arr_val["typography_line_height{$device}"] = $data;
			}
		}

		return $result;
	}

	public function update_kit_css() {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
}