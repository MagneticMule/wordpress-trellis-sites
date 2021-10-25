<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Demo_Null_Actions_Builder extends The7_Demo_Actions_Builder_Base {

	protected function init() {
		$this->add_nothing_to_import_error();
	}

	protected function setup_data() {
	}

}
