<?php
/**
 * @package The7\Migrations
 */

defined( 'ABSPATH' ) || exit;

class The7_DB_Patch_091402 extends The7_DB_Patch {

	/**
	 * Main method. Apply all migrations.
	 */
	protected function do_apply() {
		if ( ! The7_Admin_Dashboard_Settings::setting_exists( 'elementor-buttons-integration' ) ) {
			The7_Admin_Dashboard_Settings::set( 'elementor-buttons-integration', false );
		}
		//enable custom icon size option `
		$buttons = [
			'buttons-s',
			'buttons-m',
			'buttons-l',
			'buttons-lg',
			'buttons-xl',
		];

		foreach ( $buttons as $button ) {
			// Prevent multiple apply.
			if ( ! $this->option_exists( "{$button}-custom-icon-size" ) ) {
				$this->set_option( "{$button}-custom-icon-size", 1 );
			}
		}

		//apply current material effect shadow analog
		if ( $this->option_exists( 'buttons-style' ) ) {
			$buttons_decoration = $this->get_option( 'buttons-style' );
			$shadow = [];
			$shadow_hover = [];
			switch ( $buttons_decoration ) {
				case '3d':
					include_once PRESSCORE_DIR . '/less-vars.php';
					$less_vars = presscore_compile_less_vars();
					if ( ! class_exists( 'the7_lessc' ) ) {
						require PRESSCORE_DIR . '/vendor/lessphp/the7_lessc.inc.php';
					}
					$lessc = new the7_lessc();
					$lessc->injectVariables( $less_vars );

					$dt_btn_bg_color = [
						'function',
						'desaturate',
						[
							'list',
							'',
							[
								[
									'function',
									'darken',
									[
										'list',
										'',
										[
											[ 'variable', '@dt-btn-bg-color' ],
											[ "number", '12', '%' ],
										],
									],
								],
								[ "number", '20', '%' ],
							],
						],
					];

					$dt_btn_hover_bg_color = [
						'function',
						'desaturate',
						[
							'list',
							'',
							[
								[
									'function',
									'darken',
									[
										'list',
										'',
										[
											[ 'variable', '@dt-btn-hover-bg-color' ],
											[ "number", '12', '%' ],
										],
									],
								],
								[ "number", '20', '%' ],
							],
						],
					];

					$color_raw = $lessc->reduce( $dt_btn_bg_color );
					$shadow['color'] = $lessc->compileValue( $color_raw );
					$shadow['horizontal'] = '0';
					$shadow['vertical'] = '2';
					$shadow['blur'] = '0';
					$shadow['spread'] = '0';
					$shadow['position'] = 'outline';

					$color_raw = $lessc->reduce( $dt_btn_hover_bg_color );
					$shadow_hover['color'] = $lessc->compileValue( $color_raw );
					$shadow_hover['horizontal'] = '0';
					$shadow_hover['vertical'] = '2';
					$shadow_hover['blur'] = '0';
					$shadow_hover['spread'] = '0';
					$shadow_hover['position'] = 'outline';
					break;
				case 'shadow':
					$shadow['color'] = 'rgba(0, 0, 0, 0.12)';
					$shadow['horizontal'] = '0';
					$shadow['vertical'] = '1';
					$shadow['blur'] = '6';
					$shadow['spread'] = '0';
					$shadow['position'] = 'outline';

					$shadow_hover['color'] = 'rgba(0, 0, 0, 0.25)';
					$shadow_hover['horizontal'] = '0';
					$shadow_hover['vertical'] = '4';
					$shadow_hover['blur'] = '13';
					$shadow_hover['spread'] = '2';
					$shadow_hover['position'] = 'outline';
					break;
				default:
					$shadow['color'] = 'rgba(0, 0, 0, 0)';
					$shadow['horizontal'] = '0';
					$shadow['vertical'] = '0';
					$shadow['blur'] = '10';
					$shadow['spread'] = '0';
					$shadow['position'] = 'outline';

					$shadow_hover['color'] = 'rgba(0, 0, 0, 0)';
					$shadow_hover['horizontal'] = '0';
					$shadow_hover['vertical'] = '0';
					$shadow_hover['blur'] = '10';
					$shadow_hover['spread'] = '0';
					$shadow_hover['position'] = 'outline';
					break;
			}

			$this->set_option( 'button-shadow', $shadow );
			$this->set_option( 'button-shadow-hover', $shadow_hover );

			$this->remove_option( 'buttons-style' );
		}
		//apply font family for lg and xl from l buttons
		if ( $this->option_exists( 'buttons-l-typography' ) ) {
			$typography = $this->get_option( 'buttons-l-typography' );
			unset( $typography['font_size'] );
			if ( ! $this->option_exists( 'buttons-lg-typography' ) ) {
				$this->set_option( 'buttons-lg-typography', $typography );
			}
			if ( ! $this->option_exists( 'buttons-xl-typography' ) ) {
				$this->set_option( 'buttons-xl-typography', $typography );
			}
		}
	}
}
