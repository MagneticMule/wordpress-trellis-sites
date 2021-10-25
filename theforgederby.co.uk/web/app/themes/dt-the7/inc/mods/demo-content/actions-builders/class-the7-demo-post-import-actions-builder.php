<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Demo_Post_Import_Actions_Builder extends The7_Demo_Actions_Builder_Base {

	protected function init() {
		if ( empty( $this->external_data['the7_import_post_id'] ) || empty( $this->external_data['demo_id'] ) ) {
			$this->add_nothing_to_import_error();

			return;
		}

		$demo = $this->setup_demo( $this->external_data['demo_id'] );

		if ( empty( $demo ) ) {
			$this->add_nothing_to_import_error();

			return;
		}

		$this->setup_starting_text(
			esc_html_x( 'Importing post ...', 'admin', 'the7mk2' )
		);
	}

	protected function setup_data() {
		$demo    = $this->demo();
		$actions = [];
		if ( ! $demo->plugins()->is_plugins_active() ) {
			$actions[] = 'install_plugins';
		}
		$actions[]           = 'download_package';
		$actions[]           = 'add_the7_dashboard_settings';
		$actions[]           = 'clear_importer_session';
		$actions[]           = 'import_one_post';
		$users               = [];
		$plugins_to_install  = array_keys( $demo->plugins()->get_plugins_to_install() );
		$plugins_to_activate = array_keys( $demo->plugins()->get_inactive_plugins() );
		$demo_id             = $demo->id;
		$import_type         = 'post_import';
		$post_to_import      = (int) $this->external_data['the7_import_post_id'];

		$this->localize_the7_import_data(
			compact(
				'actions',
				'users',
				'plugins_to_install',
				'plugins_to_activate',
				'demo_id',
				'import_type',
				'post_to_import'
			)
		);
	}
}
