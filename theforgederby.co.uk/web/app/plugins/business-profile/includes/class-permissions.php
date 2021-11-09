<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'bpfwpPermissions' ) ) {
/**
 * Class to handle plugin permissions for Business Profile
 *
 * @since 2.0.0
 */
class bpfwpPermissions {

	private $plugin_permissions;
	private $permission_level;

	public function __construct() {
		$this->plugin_permissions = array(
			"premium" => 2,
			"locations" => 2,
			"integrations" => 2,
			"api_usage"	=> 2
		);
	}

	public function set_permissions() {
		global $bpfwp_controller;

		$this->permission_level = 1;

		update_option( "bpfwp-permission-level", $this->permission_level );
	}

	public function get_permission_level() {
		$this->permission_level = get_option( "bpfwp-permission-level" );

		if ( ! $this->permission_level ) { $this->set_permissions(); }
	}

	public function check_permission($permission_type = '') {
		if ( ! $this->permission_level ) { $this->get_permission_level(); }

		return ( array_key_exists( $permission_type, $this->plugin_permissions ) ? ( $this->permission_level >= $this->plugin_permissions[$permission_type] ? true : false ) : false );
	}

	public function update_permissions() {
		$this->permission_level = get_option( "bpfwp-permission-level" );
	}
}

}