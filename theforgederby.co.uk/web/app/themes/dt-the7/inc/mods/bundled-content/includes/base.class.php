<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

abstract class BundledContent {

	protected static $wpdb;
	protected static $filedir;

	public function __construct() {
		global $wpdb;
		self::$wpdb = $wpdb;
		self::$filedir = trailingslashit( dirname( __FILE__ ) );
	}

	public function isBundledPlugin($plugin_slug){
		global $the7_tgmpa;
		if ( ! $the7_tgmpa && class_exists( 'Presscore_Modules_TGMPAModule' ) ) {
			Presscore_Modules_TGMPAModule::init_the7_tgmpa();
			Presscore_Modules_TGMPAModule::register_plugins_action();
		}
		if ( empty( $the7_tgmpa->plugins ) ) {
			Presscore_Modules_TGMPAModule::register_plugins_action();
		}
		return $the7_tgmpa->is_the7_plugin( $plugin_slug );
	}

	abstract protected function activatePlugin();

	abstract protected function deactivatePlugin();

	abstract protected function isActivatedPlugin();

	abstract protected function getBundledPluginCode();

	public function isActivatedByTheme() {
		$bundledPluginCode = $this->getBundledPluginCode();
		if ( empty( $bundledPluginCode ) ) {
			return false;
		}
		$themeCode = get_site_option( 'the7_purchase_code', '' );
		if ( empty( $themeCode ) ) {
			return true;
		}
		$val = ( $themeCode === $bundledPluginCode );

		return $val;
	}
}