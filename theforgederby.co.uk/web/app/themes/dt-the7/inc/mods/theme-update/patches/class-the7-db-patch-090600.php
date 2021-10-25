<?php
/**
 * @package The7\Migrations
 */

defined( 'ABSPATH' ) || exit;

class The7_DB_Patch_090600 extends The7_DB_Patch {

	/**
	 * Main method. Apply all migrations.
	 */
	protected function do_apply() {
		$this->migrate_fonts();
	}

	protected function migrate_fonts() {
		// Migrate h5 to separate title options.
		$this->copy_option_value( 'fonts-widget-title', 'fonts-h5-typography' );
		$this->copy_option_value( 'fonts-woo-title', 'fonts-h5-typography' );

		$font_family = $this->get_option( 'fonts-font_family' ) ?: [];
		if ( ! is_array( $font_family ) ) {
			$font_family = [ 'font_family' => $font_family ];
		}

		$new_fonts = [
			'fonts-woo-content'    => 'fonts-big_size',
			'fonts-widget-content' => 'fonts-normal_size',
			'header-elements-woocommerce_cart-font-content' => 'fonts-normal_size',
		];
		foreach ( $new_fonts as $new_font => $base_font ) {
			if ( $this->option_exists( $base_font ) ) {
				$font_sizes = The7_Option_Field_Font_Sizes::sanitize(
					$this->get_option( $base_font )
				);
				$this->add_option(
					$new_font,
					array_merge(
						$font_family,
						[
							'responsive_font_size' => [ The7_Option_Field_Responsive_Option::RESPONSIVE_DESKTOP => $font_sizes['font_size'] ],
							'responsive_line_height' => [ The7_Option_Field_Responsive_Option::RESPONSIVE_DESKTOP => $font_sizes['line_height'] ]
						]
					)
				);
			}
		}
	}
}
