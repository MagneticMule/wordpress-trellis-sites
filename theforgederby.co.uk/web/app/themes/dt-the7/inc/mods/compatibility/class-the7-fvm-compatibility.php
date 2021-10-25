<?php
/**
 * Optimization module.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'The7_FVM_Compatibility' ) ) {

	class The7_FVM_Compatibility {

		/**
		 * Bootstrap adapter.
		 */
		public function bootstrap() {
			# actions for frontend only
			if ( ! is_admin() && (
				(function_exists( "fvm_can_minify" ) &&  fvm_can_minify()) ||
				(function_exists("fvm_can_minify_js") &&  fvm_can_minify_js())
				)) {
				//remove cloudflare preloads
				add_filter( 'http2_link_preload_src', array( 'The7_FVM_Compatibility', 'disablePreload' ) );
				// FVM use PHP_INT_MIN too so it's valid.
				// phpcs:ignore PHPCompatibility.Constants.NewConstants.php_int_minFound
				add_action( 'template_redirect', array( 'The7_FVM_Compatibility', 'startBuffer' ), PHP_INT_MIN );
			}
		}


		public static function disablePreload( $src ) {
			$src = [];

			return $src;
		}

		# start buffering before template
		public static function startBuffer() {
			ob_start( array( 'The7_FVM_Compatibility', 'processPage' ), 0, PHP_OUTPUT_HANDLER_REMOVABLE );
		}

		# process html from fvm_end_buffer
		public static function processPage( $html ) {
			# return early if not html
			if ( function_exists( "fvm_is_html" ) && fvm_is_html( $html ) !== true ) {
				return $html;
			}
			if ( !function_exists( "str_get_html" )  ) {
				return $html;
			}
			# get html into an object
			# https://simplehtmldom.sourceforge.io/manual.htm
			$html_object = str_get_html( $html, true, true, 'UTF-8', false, PHP_EOL, ' ' );

			# return early if html is not an object, or overwrite html into an object for processing
			if ( ! is_object( $html_object ) ) {
				return $html;
			} else {
				$html = $html_object;
			}
			$the7_loader_script = '';
			$foundScripts = array();
			foreach ( $html->find( 'script' ) as $tag ) {
				if ( isset( $tag->src ) ) {
					if ( preg_match( '/.*-[A-Za-z0-9]{40}\.(footer|header)\.min\.js/', $tag->src ) ) {
						$foundScripts[] = $tag;
					}
					else if( preg_match( '/.*[0-9]{10}-js[A-Za-z0-9]{62}\.js/', $tag->src )){
						$foundScripts[] = $tag;
					}
				}
				//remove loader
				if ( isset( $tag->id ) && $tag->id == "the7-loader-script" ) {
					$the7_loader_script = $tag;
				}
			}

			if ( count( $foundScripts ) > 0 ) {
				$script_srs = [];
				$lastElement = end($foundScripts);
				foreach ( $foundScripts as $tag ) {
					$script_srs[] = $tag->src;
					$is_inject_loader = $lastElement == $tag && $the7_loader_script;
					$tag->outertext = self::getScript( $tag->src, $is_inject_loader );
				}

				$header = self::addHeaderScript();
				if ( $the7_loader_script ) {
					$the7_loader_script->outertext = "";
				}
				# append header, if available
				if ( ! is_null( $html->find( 'head', 0 ) ) && ! is_null( $html->find( 'body', - 1 ) ) ) {
					if ( ! is_null( $html->find( 'head', 0 )->first_child() ) && ! is_null( $html->find( 'body', - 1 )->last_child() ) ) {
						$html->find( 'head', 0 )->first_child()->outertext = $header . $html->find( 'head', 0 )->first_child()->outertext;
					}
				}

				//remove preloads for handled scripts
				foreach ( $html->find( 'link[rel=preload]' ) as $tag ) {
					if ( isset( $tag->href ) && in_array( $tag->href, $script_srs ) ) {
						$tag->outertext = '';
					}
				}
				# convert html object to string
				$html = trim( $html->save() );
			}

			return $html;
		}

		public static function addHeaderScript() {
			$script_timeout = of_get_option( 'advanced-fvm_script_timeout' );
			if ( empty ( $script_timeout ) ) {
				$script_timeout = 500;
			}
			$script_timeout = (int) $script_timeout;
			# create function
			$lst = array( 'x11.*ox\/54', 'id\s4.*us.*ome\/62', 'oobo', 'ight', 'tmet', 'eadl', 'ngdo', 'PTST' );

			return '<script data-cfasync="false">function dtmuag(){var e=navigator.userAgent;if(e.match(/' . implode( '|', $lst ) . '/i))return!1;if(e.match(/x11.*me\/86\.0/i)){var r=screen.width;if("number"==typeof r&&1367==r)return!1}return!0}var dtmuag_t=' . $script_timeout . ';  var dtmuag_events = ["mouseover", "keydown", "touchmove", "touchstart"];</script>';
		}

		public static function getScript( $url, $is_inject_loader) {
			$rem = 'b.async=false;';
			if ($is_inject_loader) {
				$rem .= 'b.onload = function () 
						{
							var load = document.getElementById("load"); 
							if(!load.classList.contains("loader-removed")){ 
								setTimeout(function() {
									load.className += " loader-removed";
								}, 100);
							}
						};';
			}
			# generate and set delayed script tag
			return "<script data-cfasync='false'>" . 'if(dtmuag()){window.addEventListener("load",function(){var c=setTimeout(b,dtmuag_t);dtmuag_events.forEach(function(a){window.addEventListener(a,e,{passive:!0})});function e(){b();clearTimeout(c);dtmuag_events.forEach(function(a){window.removeEventListener(a,e,{passive:!0})})}function b(){' . "(function(a){dtmuag_events.forEach(function(a){window.removeEventListener(a,e,{passive:!0})});var b=a.createElement('script'),c=a.scripts[0];b.src='" . trim( $url ) . "';" . $rem . "a.body.appendChild(b);}(document)); " . '}});}' . "</script>";
		}
	}
}