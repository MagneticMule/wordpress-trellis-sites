<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Demo_Null_Tracker extends The7_Demo_Tracker {

	public function get( $key ) {
		// Do nothing.
	}

	public function set( $key, $value ) {
		// Do nothing.
	}

	public function add( $key, $value ) {
		// Do nothing.
	}

	public function remove( $key ) {
		// Do nothing.
	}

	public function get_demo_type() {
		// Do nothing.
	}

	public function track_imported_items() {
		// Do nothing.
	}

	public function keep_demo_content() {
		// Do nothing.
	}

	public function remove_demo() {
		// Do nothing.
	}

}
