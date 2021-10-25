<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Dashboard_Settings_Importer {

	/**
	 * @var The7_Demo_Tracker
	 */
	private $content_tracker;

	/**
	 * The7_Dashboard_Settings_Importer constructor.
	 *
	 * @param The7_Demo_Tracker $content_tracker
	 */
	public function __construct( $content_tracker ) {
		$this->content_tracker = $content_tracker;
	}

	/**
	 * @param array $settings
	 */
	public function import( $settings ) {
		$origin_setting = array_intersect_key( The7_Admin_Dashboard_Settings::get_all(), $settings );
		$this->content_tracker->set( 'the7_dashboard_settings', $origin_setting );

		foreach ( $settings as $name => $value ) {
			The7_Admin_Dashboard_Settings::set( $name, $value );
		}
	}

	/**
	 * @param array $settings
	 */
	public function add( $settings ) {
		foreach ( $settings as $name => $value ) {
			if ( $value ) {
				The7_Admin_Dashboard_Settings::set( $name, $value );
			}
		}
	}

}
