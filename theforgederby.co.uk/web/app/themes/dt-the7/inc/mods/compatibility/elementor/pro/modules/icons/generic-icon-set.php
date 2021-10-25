<?php

namespace The7\Mods\Compatibility\Elementor\Pro\Modules\Icons;

use ElementorPro\Modules\AssetsManager\AssetTypes\Icons\Custom_Icons;
use ElementorPro\Modules\AssetsManager\AssetTypes\Icons\IconSets\Icon_Set_Base;

defined( 'ABSPATH' ) || exit;

class Generic_Icon_Set extends Icon_Set_Base {

	public function __construct( $directory ) {
	}

	public function generate_e_icons_js( $dir, $url, $icons ) {
		$wp_filesystem = Custom_Icons::get_wp_filesystem();
		$json_file     = $this->get_ensure_upload_dir( $dir ) . '/e_icons.js';
		$wp_filesystem->put_contents( $json_file, wp_json_encode( [ 'icons' => $icons ] ) );
		return $url . '/e_icons.js';
	}

	protected function get_url( $filename = '' ) {
		return '';
	}

	protected function extract_icon_list() {
		return [];
	}

	protected function prepare() {
		return [];
	}

	protected function get_type() {
		return '';
	}

	public function get_name() {
		return '';
	}
}
