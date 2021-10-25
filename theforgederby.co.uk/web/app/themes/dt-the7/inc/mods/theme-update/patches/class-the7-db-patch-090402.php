<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_DB_Patch_090402 extends The7_DB_Patch {

	/**
	 * Main method. Apply all migrations.
	 */
	protected function do_apply() {
		$font_sizes = [
			'fonts-big_size'    => 16,
			'fonts-small_size'  => 13,
			'fonts-normal_size' => 15,
		];

		$backups = \The7_Options_Backup::get_records();
		array_reverse( $backups );
		foreach ( $backups as $record_name ) {
			$options_record = \The7_Options_Backup::get_record_value( $record_name );
			foreach ( $font_sizes as $option_name => $default ) {
				if ( ! empty( $options_record[ $option_name ] ) ) {
					$font_sizes[ $option_name ] = $options_record[ $option_name ];
				}
			}
		}

		foreach ( $font_sizes as $option_name => $default ) {
			$font_size = $this->get_option( $option_name );
			if ( $font_size === [] ) {
				$new_font_size           = [
					'font_size' => ( (float) $default ) . 'px',
				];
				$line_height_option_name = "{$option_name}_line_height";
				$line_height             = $this->get_option( $line_height_option_name );
				if ( $line_height ) {
					$new_font_size['line_height'] = ( (float) $line_height ) . 'px';
					$this->remove_option( $line_height_option_name );
				}
				$this->set_option( $option_name, $new_font_size );
			}
		}
	}

}