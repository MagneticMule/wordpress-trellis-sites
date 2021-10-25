<?php
/**
 * Class that handles theme options backup routine.
 *
 * @since   7.6.0
 *
 * @package The7\Options
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_Options_Backup
 */
class The7_Options_Backup {

	const RECORD_NAME_BASE = 'the7-theme-options-backup';

	/**
	 * Store current theme options in database.
	 */
	public static function store_options( $options = null, $version = null, $date = null ) {
		$date = $date ?: date( 'Y-m-d-H:i:s' );
		$version = $version ?: get_option( 'the7_db_version', 'latest' );
		$options = $options ?: optionsframework_get_options();
		set_transient( self::RECORD_NAME_BASE . "-{$version}-{$date}", $options, 7 * DAY_IN_SECONDS );
	}

	/**
	 * Restore theme options from the record.
	 *
	 * @param string $record_name Record name.
	 *
	 * @return bool Return true on success, false otherwise.
	 */
	public static function restore( $record_name ) {
		$options_backup = static::get_record_value( $record_name );
		if ( is_array( $options_backup ) ) {
			of_save_unsanitized_options( $options_backup );
			_optionsframework_delete_defaults_cache();
			presscore_refresh_dynamic_css();

			return true;
		}

		return false;
	}

	/**
	 * @param string $record_name
	 *
	 * @return mixed
	 */
	public static function get_record_value( $record_name ) {
		return get_transient( $record_name );
	}

	/**
	 * Delete all records.
	 *
	 * @return int Return number of deleted records.
	 */
	public static function delete_all_records() {
		$records_deleted = 0;
		foreach ( self::get_records() as $record_name ) {
			if ( delete_transient( $record_name ) ) {
				$records_deleted ++;
			}
		}

		return $records_deleted;
	}

	/**
	 * Return all stored records of them options.
	 *
	 * @return array Array of record names.
	 */
	public static function get_records() {
		global $wpdb;

		$record_name_like = '_transient_' . self::RECORD_NAME_BASE . '%';
		$transients       = $wpdb->get_results(
			$wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s", $record_name_like ),
			ARRAY_A
		);

		$records = array();
		foreach ( $transients as $transient ) {
			$records[] = str_replace( '_transient_', '', $transient['option_name'] );
		}

		return $records;
	}
}
