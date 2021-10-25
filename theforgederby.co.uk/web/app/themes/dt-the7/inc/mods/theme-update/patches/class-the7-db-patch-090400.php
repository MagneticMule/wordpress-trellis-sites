<?php
/**
 * @package The7\Migrations
 */

defined( 'ABSPATH' ) || exit;

class The7_DB_Patch_090400 extends The7_DB_Patch {

	/**
	 * Main method. Apply all migrations.
	 */
	protected function do_apply() {
		$this->migrate_heading_fonts();
		$this->migrate_base_font_sizes();
		$this->migrate_h1_responsive_data();
	}

	protected function migrate_heading_fonts() {
		$fields_to_modify = [ 'font_size', 'line_height' ];
		for ( $id = 1; $id <= 6; $id ++ ) {
			$option_name = "fonts-h{$id}-typography";
			if ( ! $this->option_exists( $option_name ) ) {
				continue;
			}
			$header_typography = $this->get_option( $option_name );

			foreach ( $fields_to_modify as $field_name ) {
				if ( isset( $header_typography[ $field_name ] ) ) {
					$header_typography["responsive_{$field_name}"] = array( The7_Option_Field_Responsive_Option::RESPONSIVE_DESKTOP => $header_typography[ $field_name ] . 'px' );
					unset( $header_typography[ $field_name ] );
				}
			}
			$this->set_option( $option_name, $header_typography );
		}
	}

	protected function migrate_base_font_sizes() {
		$fields_to_modify = [
			'fonts-big_size',
			'fonts-small_size',
			'fonts-normal_size',
		];
		foreach ( $fields_to_modify as $field_name ) {
			if ( ! $this->option_exists( $field_name ) ) {
				continue;
			}
			$font_size = $this->get_option( $field_name );

			// Wrong format.
			if ( is_array( $font_size ) ) {
				continue;
			}

			$line_height_field_name = "{$field_name}_line_height";
			$line_height            = $this->get_option( $line_height_field_name );
			$this->remove_option( $line_height_field_name );

			$new_option = [];
			if ( ! empty( $font_size ) ) {
				$new_option['font_size'] = $font_size . 'px';
			}
			if ( ! empty( $line_height ) ) {
				$new_option['line_height'] = $line_height . 'px';
			}
			$this->set_option( $field_name, $new_option );
		}
	}

	protected function migrate_h1_responsive_data() {
		$option_h1_name = 'fonts-h1-typography';
		$option_h2_name = 'fonts-h2-typography';
		if ( ! $this->option_exists( $option_h1_name ) || ! $this->option_exists( $option_h2_name ) ) {
			return;
		}

		$option_h1 = $this->get_option( $option_h1_name );
		$option_h2 = $this->get_option( $option_h2_name );

		$option_h1['responsive_font_size'][ The7_Option_Field_Responsive_Option::RESPONSIVE_TABLET ] = $option_h2['responsive_font_size'][ The7_Option_Field_Responsive_Option::RESPONSIVE_DESKTOP ];
		$option_h1['responsive_font_size'][ The7_Option_Field_Responsive_Option::RESPONSIVE_MOBILE ] = $option_h2['responsive_font_size'][ The7_Option_Field_Responsive_Option::RESPONSIVE_DESKTOP ];
		$this->set_option( $option_h1_name, $option_h1 );
	}
}
