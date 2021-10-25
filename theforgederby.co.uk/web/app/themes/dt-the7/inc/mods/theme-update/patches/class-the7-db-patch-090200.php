<?php
/**
 * @package The7\Migrations
 */

defined( 'ABSPATH' ) || exit;

class The7_DB_Patch_090200 extends The7_DB_Patch {

	/**
	 * Main method. Apply all migrations.
	 */
	protected function do_apply() {
		$this->copy_option_value( 'header-mobile-floating-bg-color', 'header-mobile-header-bg-color' );
	}
}
