<?php
/**
 * The7 elementor importer.
 *
 * @package The7
 */

use ElementorPro\Modules\AssetsManager\AssetTypes\Icons\Custom_Icons;
use ElementorPro\Modules\AssetsManager\AssetTypes\Icons_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_Elementor_Importer
 */
class The7_Elementor_Importer {

	/**
	 * Demo content tracker.
	 *
	 * @var The7_Demo_Content_Tracker
	 */
	private $content_tracker;

	/**
	 * General demo content importer.
	 *
	 * @var The7_Content_Importer
	 */
	private $importer;

	/**
	 * The7_Elementor_Importer constructor.
	 *
	 * @param The7_Content_Importer     $importer        General demo content importer.
	 * @param The7_Demo_Content_Tracker $content_tracker Demo content tracker.
	 */
	public function __construct( $importer, $content_tracker ) {
		$this->content_tracker = $content_tracker;
		$this->importer        = $importer;
	}

	/**
	 * Import Elementor options.
	 *
	 * @param array $options Options array.
	 */
	public function import_options( $options ) {
		$origin_options = [];

		foreach ( $options as $key => $option ) {
			$origin_options[ $key ] = get_option( $key, null );

			if ( isset( $option ) ) {
				update_option( $key, $option );
			} else {
				delete_option( $key );
			}
		}

		$this->content_tracker->add( 'origin_elementor_options', $origin_options );

		the7_elementor_flush_css_cache();

		if ( class_exists( 'ElementorPro\Modules\ThemeBuilder\Module' ) ) {
			\ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_cache()->regenerate();
		}
	}

	/**
	 * Import Elementor Kit settings.
	 *
	 * @param array $kit_settings Kit settings.
	 */
	public function import_kit_settings( $kit_settings ) {
		$kit_id = \Elementor\Plugin::$instance->kits_manager->get_active_id();
		$kit    = \Elementor\Plugin::$instance->documents->get( $kit_id );

		if ( ! $kit ) {
			return;
		}

		$current_settings = (array) $kit->get_meta( \Elementor\Core\Settings\Page\Manager::META_KEY ) ?: [];

		foreach ( $kit_settings as $key => $setting ) {
			if ( isset( $setting ) ) {
				$current_settings[ $key ] = $setting;
			} else {
				unset( $current_settings[ $key ] );
			}
		}

		$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
		$page_settings_manager->save_settings( $current_settings, $kit_id );
	}

	/**
	 * Do some important suff before content importing.
	 */
	public function do_before_importing_content() {
		/**
		 * Replace demo base url with the local site url in Elementor data.
		 *
		 * This should fix issues with the links and image urls.
		 */
		add_filter(
			'wp_import_post_meta_value',
			function( $value, $key ) {
				$value = $this->fix_elementor_data_urls( $key, $value );
				$value = $this->fix_elementor_icon_set_path( $key, $value );
				$value = $this->fix_elementor_custom_icon_set_config( $key, $value );

				return $value;
			},
			10,
			2
		);
	}

	/**
	 * Do some stuff after content imprting.
	 */
	public function do_after_importing_content() {
		$this->fix_term_ids_in_elementor_data();
		$this->generate_icons_e_icons_js();
	}

	/**
	 * Correct the Elementor data to match the current content.
	 */
	protected function fix_term_ids_in_elementor_data() {
		global $wpdb;

		$ids = $wpdb->get_col( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_elementor_data'" );

		foreach ( $ids as $post_id ) {
			$elementor_data = json_decode( get_post_meta( $post_id, '_elementor_data', true ), true );

			if ( ! $elementor_data ) {
				continue;
			}

			static::apply_elementor_data_patch( $elementor_data, [ $this, 'fix_the7_widgets_terms' ] );
			update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $elementor_data ) ) );
		}
	}

	/**
	 * Utility method that runs a transformation callback on elementor data.
	 *
	 * @param array $elementor_data Elementor data.
	 * @param mixed $callback Callback.
	 */
	public static function apply_elementor_data_patch( &$elementor_data, $callback ) {
		foreach ( $elementor_data as &$element ) {
			if ( isset( $element['elType'] ) && $element['elType'] === 'widget' ) {
				if ( is_callable( $callback ) ) {
					$element = $callback( $element );
				}
			}

			if ( ! empty( $element['elements'] ) ) {
				static::apply_elementor_data_patch( $element['elements'], $callback );
			}
		}
	}

	/**
	 * Correct terms id's in the widgets.
	 *
	 * @param array $widget Elementor widget data.
	 *
	 * @return array
	 */
	protected function fix_the7_widgets_terms( $widget ) {
		if ( isset( $widget['settings']['terms'] ) && is_array( $widget['settings']['terms'] ) ) {
			foreach ( $widget['settings']['terms'] as &$term ) {
				$term = (string) $this->importer->get_processed_term( $term );
			}
		}
		unset( $term );

		$query_controls = [
			'query_exclude_term_ids',
			'query_include_term_ids',
		];
		foreach ( $query_controls as $term_ids_control ) {
			if ( empty( $widget['settings'][ $term_ids_control ] ) || ! is_array( $widget['settings'][ $term_ids_control ] ) ) {
				continue;
			}

			foreach ( $widget['settings'][ $term_ids_control ] as &$tax_id ) {
				// Yeah, they've used term_taxonomy_id ...
				$tax_id = (string) $this->importer->get_processed_taxonomy_id( $tax_id );
			}
		}
		unset( $tax_id );

		return $widget;
	}

	public static function sanitize_url_for_replacement( $url ) {
		return str_replace( '/', '\/', $url );
	}

	/**
	 * Generate `e_icons.js` file for the Elementor editor custom icons interface.
	 */
	protected function generate_icons_e_icons_js() {
		// Do not run if Elementor Pro is not active.
		if ( ! class_exists( Custom_Icons::class ) ) {
			return;
		}

		$icons            = new \WP_Query(
			[
				'post_type'      => Icons_Manager::CPT,
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			]
		);
		$generic_icon_set = new \The7\Mods\Compatibility\Elementor\Pro\Modules\Icons\Generic_Icon_Set( '' );
		foreach ( $icons->posts as $icon_set ) {
			$set_config = json_decode( Custom_Icons::get_icon_set_config( $icon_set->ID ), true );
			$generic_icon_set->generate_e_icons_js( $set_config['name'], dirname( $set_config['fetchJson'] ), $set_config['icons'] );
		}
	}

	/**
	 * Fix urls in _elementor_data.
	 *
	 * @param string $key Meta key.
	 * @param string $value Meta value.
	 *
	 * @return string
	 */
	protected function fix_elementor_data_urls( $key, $value ) {
		if ( $key !== '_elementor_data' ) {
			return $value;
		}

		// The base_url (basically the demo home url) is known only after parsing the content, so it should be used here.
		$base_url                              = self::sanitize_url_for_replacement( untrailingslashit( $this->importer->base_url ) );
		$escaped_local_url                     = self::sanitize_url_for_replacement( get_home_url() );
		$escaped_local_url_with_trailing_slash = self::sanitize_url_for_replacement( trailingslashit( get_home_url() ) );

		/**
		 * Serach for urls like `"https://the7.io`, `"https://the7.io/main`. Without trailig slash.
		 * `"` is used to determine the start of the field in json-encoded string.
		 */
		$search        = [ '"' . $base_url ];
		$replace       = [ '"' . $escaped_local_url ];
		$base_url_path = wp_parse_url( $this->importer->base_url, PHP_URL_PATH );
		if ( $base_url_path ) {
			// Search for substring like `"main/` and `"/main/`.
			$search[]  = '"' . self::sanitize_url_for_replacement( trailingslashit( $base_url_path ) );
			$replace[] = '"' . $escaped_local_url_with_trailing_slash;
			$search[]  = '"' . self::sanitize_url_for_replacement( trailingslashit( ltrim( $base_url_path, '/\\' ) ) );
			$replace[] = '"' . $escaped_local_url_with_trailing_slash;

			// Search for substrings like `"main#anchor` and `"/main#anchor`.
			$search[]  = '"' . self::sanitize_url_for_replacement( $base_url_path . '#' );
			$replace[] = '"' . $escaped_local_url . '#';
			$search[]  = '"' . self::sanitize_url_for_replacement( ltrim( $base_url_path, '/\\' ) . '#' );
			$replace[] = '"' . $escaped_local_url . '#';
		}

		$wp_uploads          = wp_get_upload_dir();
		$wp_uploads_base_url = self::sanitize_url_for_replacement( trailingslashit( $wp_uploads['baseurl'] ) );

		// Fix uploads url. With trailing slash.
		$regexp = '|' . preg_quote( $base_url, '|' ) . '\\\/wp-content\\\/uploads\\\/(sites\\\/\d*\\\/)?|';
		$value  = preg_replace( $regexp, $wp_uploads_base_url, $value );

		// Fix site url.
		return str_replace( $search, $replace, $value );
	}

	/**
	 * Fix urls in _elementor_icon_set_path.
	 *
	 * @param string $key Meta key.
	 * @param string $value Meta value.
	 *
	 * @return string
	 */
	protected function fix_elementor_icon_set_path( $key, $value ) {
		if ( $key !== '_elementor_icon_set_path' ) {
			return $value;
		}

		$wp_uploads = wp_get_upload_dir();
		$value      = preg_replace( '|.*/wp-content/uploads/(sites/\d*/)?|', trailingslashit( $wp_uploads['basedir'] ), $value );

		return $value;
	}

	/**
	 * Fix urls in elementor_custom_icon_set_config.
	 *
	 * @param string $key Meta key.
	 * @param string $value Meta value.
	 *
	 * @return string
	 */
	protected function fix_elementor_custom_icon_set_config( $key, $value ) {
		if ( $key !== 'elementor_custom_icon_set_config' ) {
			return $value;
		}

		$wp_uploads = wp_get_upload_dir();
		$value      = preg_replace( '|' . preg_quote( untrailingslashit( $this->importer->base_url ), '|' ) . '/wp-content/uploads/(sites/\d*/)?|', trailingslashit( $wp_uploads['baseurl'] ), $value );

		return $value;
	}

}
