<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

abstract class The7_Demo_Tracker {

	const HISTORY_OPTION_ID = 'the7_demo_history';

	protected $demo_type;

	protected $demo_history;

	public function __construct( $demo_type ) {
		$this->demo_type    = $demo_type;
		$this->demo_history = get_option( static::HISTORY_OPTION_ID, [] );
	}

	public static function get_demo_history( $demo_id ) {
		$demo_history = get_option( static::HISTORY_OPTION_ID, [] );

		if ( ! array_key_exists( $demo_id, $demo_history ) ) {
			return [];
		}

		return $demo_history[ $demo_id ];
	}

	abstract public function get( $key );

	abstract public function set( $key, $value );

	abstract public function add( $key, $value );

	abstract public function remove( $key );

	abstract public function get_demo_type();

	abstract public function track_imported_items();

	abstract public function keep_demo_content();

	abstract public function remove_demo();
}
