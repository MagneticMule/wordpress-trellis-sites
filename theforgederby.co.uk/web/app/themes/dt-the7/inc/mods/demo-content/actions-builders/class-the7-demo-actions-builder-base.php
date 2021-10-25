<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

abstract class The7_Demo_Actions_Builder_Base {

	/**
	 * @var string
	 */
	protected $starting_text = '';

	/**
	 * @var string
	 */
	protected $error;

	/**
	 * @var array
	 */
	protected $external_data;

	/**
	 * @var The7_Demo
	 */
	protected $demo;

	abstract protected function setup_data();

	public function __construct( $external_data = [] ) {
		$this->external_data = $external_data;

		$this->init();
	}

	public function localize_data_to_js() {
		if (!empty($this->error)) {
			return;
		}

		$this->setup_data();
	}

	/**
	 * @return string
	 */
	public function get_starting_text() {
		return $this->starting_text;
	}

	public function get_error() {
		return $this->error;
	}

	protected function init() {
		// Do nothing by default.
	}

	protected function setup_starting_text( $text ) {
		$this->starting_text = $text;
	}

	protected function add_error( $error_text ) {
		$this->error = $error_text;
	}

	protected function setup_demo( $demo_id ) {
		$this->demo = the7_demo_content()->get_demo( $demo_id );

		return $this->demo;
	}

	protected function demo() {
		return $this->demo;
	}

	protected function localize_the7_import_data( $data = [] ) {
		wp_localize_script( 'the7-demo-content', 'the7ImportData', $data );
	}

	protected function add_nothing_to_import_error() {
		$this->add_error(
			sprintf(
				// translators: %s: url to the7 pre-dame websites page
				'<p>' . _x( 'Nothing to import. <a href="%s">Import a demo from here.</a>', 'admin', 'the7mk2' ) . '</p>',
				esc_url( the7_demo_content()->admin_url() )
			)
		);
	}
}
