<?php

defined( 'ABSPATH' ) || exit;

class The7_Option_Field_Web_Fonts_Preview extends The7_Option_Field_Abstract {
 	public function html() {
		$output = '<div class="dt-web-fonts-preview"><span>The quick brown fox</span><span>jumps over a lazy dog.</span></div>';
		return $output;
	}
}
