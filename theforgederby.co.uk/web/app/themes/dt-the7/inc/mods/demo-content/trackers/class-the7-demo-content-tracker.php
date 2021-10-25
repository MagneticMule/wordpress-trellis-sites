<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Demo_Content_Tracker extends The7_Demo_Tracker {

	public function get( $key ) {
		if ( empty( $this->demo_history[ $this->demo_type ][ $key ] ) ) {
			return false;
		}

		return $this->demo_history[ $this->demo_type ][ $key ];
	}

	public function set( $key, $value ) {
		$this->demo_history[ $this->demo_type ][ $key ] = $value;
		$this->save_demo_history();
	}

	public function add( $key, $value ) {
		if ( ! isset( $this->demo_history[ $this->demo_type ][ $key ] ) ) {
			$this->demo_history[ $this->demo_type ][ $key ] = $value;
			$this->save_demo_history();
		}
	}

	public function remove( $key ) {
		unset( $this->demo_history[ $this->demo_type ][ $key ] );
		$this->save_demo_history();
	}

	public function get_demo_type() {
		return $this->demo_type;
	}

	public function track_imported_items() {
		add_filter( 'wp_import_post_meta', [ $this, 'add_the7_imported_item_meta_filter' ], 10, 3 );
		add_filter( 'wp_import_term_meta', [ $this, 'add_the7_imported_item_meta_filter' ], 10, 3 );
	}

	public function keep_demo_content() {
		delete_metadata( 'post', null, '_the7_imported_item', $this->demo_type, true );
		delete_metadata( 'term', null, '_the7_imported_item', $this->demo_type, true );

		$this->remove_demo();
	}

	public function add_the7_imported_item_meta_filter( $meta, $post_id = 0, $post = [] ) {
		if ( isset( $post['post_type'], $post['post_title'] ) && $post['post_type'] === 'elementor_library' && $post['post_title'] === 'Default Kit' ) {
			return $meta;
		}

		$meta[] = [
			'key' => '_the7_imported_item',
			'value' => $this->demo_type,
		];

		return $meta;
	}

	public function remove_demo() {
		if ( isset( $this->demo_history[ $this->demo_type ] ) ) {
			unset( $this->demo_history[ $this->demo_type ] );
		}
		$this->save_demo_history();
	}

	protected function save_demo_history() {
		update_option( static::HISTORY_OPTION_ID, $this->demo_history, false );
	}
}
