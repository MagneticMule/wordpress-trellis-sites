<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Less_Vars_Storage extends Presscore_Lib_SimpleBag {

	protected $css_vars = [];

	protected $exclude = false;

	public function set( $key, $value ) {
		parent::set( $key, $value );
		if ( ! $this->exclude ) {
			$this->set_value( $this->css_vars, $key, $value );
		}
	}

	public function map( $items ) {
		parent::map( $items );

		if ( ! $this->exclude ) {
			foreach ( (array) $items as $key => $value ) {
				$this->css_vars[ $key ] = $value;
			}
		}
	}

	public function remove( $key ) {
		parent::remove( $key );
		$this->remove_value( $this->css_vars, $key );
	}

	public function start_excluding_css_vars() {
		$this->exclude = true;
	}

	public function end_excluding_css_vars() {
		$this->exclude = false;
	}

	public function get_css_vars() {
		return $this->css_vars;
	}
}