<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Demo_Full_Import_Actions_Builder extends The7_Demo_Actions_Builder_Base {

	protected function init() {
		if ( empty( $this->external_data['demo_id'] ) ) {
			$this->add_nothing_to_import_error();

			return;
		}

		$demo = $this->setup_demo( $this->external_data['demo_id'] );

		if ( ! $demo ) {
			$this->add_nothing_to_import_error();

			return;
		}

		$this->setup_starting_text(
			sprintf(
			// translators: %s: demo name
				esc_html_x( 'Importing %s demo...', 'admin', 'the7mk2' ),
				$demo->title
			)
		);
	}

	protected function setup_data() {
		$supported_fields = [
			'import_post_types',
			'import_attachments',
			'import_rev_sliders',
			'import_theme_options',
		];
		$actions          = [];

		if ( isset( $this->external_data['install_plugins'] ) ) {
			$actions[] = 'install_plugins';
		}

		$actions[] = 'download_package';

		if ( isset( $this->external_data['import_post_types'] ) ) {
			$actions[] = 'import_the7_dashboard_settings';
		}

		$demo                      = $this->demo();
		$demo_id                   = $demo->id;

		$demo_history = The7_Demo_Tracker::get_demo_history( $demo_id );
		$required_actions = array_intersect( $supported_fields, array_keys( $this->external_data ) );

		if ( ! isset( $demo_history['attachments_in_process'] ) || ! in_array( 'import_attachments', $required_actions, true ) ) {
			$actions[] = 'clear_importer_session';
		}

		$actions         = array_merge( $actions, $required_actions );
		$actions[]       = 'cleanup';
		$actions         = array_values( $actions );

		$plugins_to_install        = array_keys( $demo->plugins()->get_plugins_to_install() );
		$plugins_to_activate       = array_keys( $demo->plugins()->get_inactive_plugins() );

		$users = [];
		if ( isset( $this->external_data['user'] ) ) {
			$users[] = $this->external_data['user'];
		}

		$this->localize_the7_import_data(
			compact(
				'actions',
				'users',
				'plugins_to_install',
				'plugins_to_activate',
				'demo_id'
			)
		);
	}
}
