<?php
/**
 * Manage all import behaviour.
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Demo_Content_Import_Manager {

	/**
	 * @var string
	 */
	public $content_dir;

	/**
	 * @var string
	 */
	public $xml_file_to_import;

	/**
	 * @var array
	 */
	protected $demo;

	/**
	 * @var array
	 */
	protected $errors = array();

	/**
	 * @var The7_Content_Importer
	 */
	protected $importer;

	/**
	 * @var mixed
	 */
	public $site_meta = null;

	/**
	 * @var The7_Demo_Content_Tracker
	 */
	private $tracker;

	/**
	 * DT_Dummy_Import_Manager constructor.
	 *
	 * @param string $content_dir
	 * @param array  $demo
	 */
	public function __construct( $content_dir, $demo, $tracker ) {
		$this->content_dir        = trailingslashit( $content_dir );
		$this->xml_file_to_import = $this->content_dir . 'full-content.xml';
		$this->demo               = $demo;
		$this->tracker            = $tracker;

		if ( $this->importer_bootstrap() ) {
			register_shutdown_function( [ $this, 'fatal_errors_log_handler' ] );
		} else {
			$this->add_error( __( 'The auto importing script could not be loaded.', 'the7mk2' ) );
		}
	}

	public function tracker() {
		return $this->tracker;
	}

	public function importer() {
		return $this->importer;
	}

	/**
	 * Downloads demo content package.
	 */
	public function download_dummy( $source ) {
		$item              = basename( $this->content_dir );
		$download_dir      = dirname( $this->content_dir );
		$the7_remote_api   = new The7_Remote_API( presscore_get_purchase_code() );
		$download_response = $the7_remote_api->download_demo( $item, $download_dir, $source );

		if ( is_wp_error( $download_response ) ) {
			$error = $download_response->get_error_message();

			$code = presscore_get_purchase_code();
			if ( $code && strpos( $error, $code ) !== false ) {
				$error = str_replace( $code, presscore_get_censored_purchase_code(), $error );
			}

			if ( 'the7_auto_deactivated' !== $download_response->get_error_code() ) {
				$error .= ' ' . sprintf(
						__(
							'Please don\'t hesitate to contact our <a href="%s" target="_blank" rel="noopener">support</a>.',
							'the7mk2'
						),
						'https://support.dream-theme.com/'
					);
			}

			$this->add_error( $error );

			return false;
		}

		return trailingslashit( $download_response );
	}

	/**
	 * Remove temp dir.
	 */
	public function cleanup_temp_dir() {
		if ( ! $this->content_dir ) {
			return false;
		}

		$wp_uploads = wp_get_upload_dir();

		$dir_to_delete = dirname( $this->content_dir );
		if ( untrailingslashit( $wp_uploads['basedir'] ) === untrailingslashit( $dir_to_delete ) ) {
			return false;
		}

		if ( false === strpos( $dir_to_delete, $wp_uploads['basedir'] ) ) {
			return false;
		}

		global $wp_filesystem;

		if ( ! $wp_filesystem && ! WP_Filesystem() ) {
			return false;
		}

		$wp_filesystem->delete( $dir_to_delete, true );

		return true;
	}

	/**
	 * Import post types dummy.
	 */
	public function import_post_types() {
		$this->rename_existing_menus();

		$this->tracker->track_imported_items();
		$this->import_file( $this->xml_file_to_import );
		$this->importer->cache_processed_data();
	}

	/**
	 * Import one post types dummy.
	 */
	public function import_one_post_by_url( $post_url ) {
		$this->importer->add_filter_by_url( $post_url );

		$this->import_file( $this->xml_file_to_import );
		$this->importer->cache_processed_data();

		return $this->importer->get_processed_filtered_post();
	}

	public function import_one_post( $post_id ) {
		$this->importer->add_filter_by_id( $post_id );

		$this->import_file( $this->xml_file_to_import );
		$this->importer->cache_processed_data();

		return $this->importer->get_processed_filtered_post();
	}

	public function import_attachments( $include_attachments = false, $batch = 27 ) {
		if ( ! $this->importer ) {
			return false;
		}

		if ( ! $this->file_exists( $this->xml_file_to_import ) ) {
			return false;
		}

		if ( ! $include_attachments ) {
			add_filter( 'wp_import_post_data_raw', [ $this, 'replace_attachment_url' ] );
		}

		add_filter( 'wp_import_tags', '__return_empty_array' );
		add_filter( 'wp_import_categories', '__return_empty_array' );
		add_filter( 'wp_import_terms', '__return_empty_array' );

		$status = $this->importer->import_batch( $this->xml_file_to_import, (int) $batch );

		$widgets = get_option( 'widget_text', [] );
		if ( $widgets ) {
			$widgets_str = wp_json_encode( $widgets );

			$url_remap = $this->importer->url_remap;
			uksort( $url_remap, [ $this->importer, 'cmpr_strlen' ] );

			foreach ( $url_remap as $old_url => $new_url ) {
				$old_url     = str_replace( '"', '', wp_json_encode( $old_url ) );
				$new_url     = str_replace( '"', '', wp_json_encode( $new_url ) );
				$widgets_str = str_replace( $old_url, $new_url, $widgets_str );
			}

			update_option( 'widget_text', json_decode( $widgets_str, true ) );
		}

		return $status;
	}

	/**
	 * Rename existing menus.
	 */
	public function rename_existing_menus() {
		$menus = wp_get_nav_menus();

		if ( ! empty( $menus ) ) {
			foreach ( $menus as $menu ) {
				$updated = false;
				$i       = 0;

				while ( ! is_numeric( $updated ) ) {
					$i++;
					$args['menu-name']   = __( 'Previously used menu', 'the7mk2' ) . ' ' . $i;
					$args['description'] = $menu->description;
					$args['parent']      = $menu->parent;

					$updated = wp_update_nav_menu_object( $menu->term_id, $args );

					if ( $i > 100 ) {
						$updated = 1;
					}
				}
			}
		}
	}

	private function importer_bootstrap() {
		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
			define( 'WP_LOAD_IMPORTERS', true );
		}

		// Load WP_Import.
		if ( ! class_exists( 'WP_Import' ) ) {
			require_once PRESSCORE_DIR . '/vendor/wordpress-importer/wordpress-importer.php';
		}

		if ( ! class_exists( 'WP_Import' ) ) {
			return false;
		}

		// Load custom importer.
		if ( ! class_exists( 'The7_Content_Importer', false ) ) {
			require __DIR__ . '/importers/class-the7-content-importer.php';
		}

		$this->importer    = new The7_Content_Importer();

		return true;
	}

	/**
	 * Import xml file.
	 *
	 * @param string $file_name File to import.
	 * @param array  $options   Options array.
	 *
	 * @return bool
	 */
	public function import_file( $file_name, $options = [] ) {
		if ( ! $this->file_exists( $file_name ) ) {
			return false;
		}

		/**
		 * Fix Fatal Error while process orphaned variations.
		 */
		remove_filter( 'post_type_link', [ 'WC_Post_Data', 'variation_post_link' ] );
		add_filter( 'post_type_link', [ $this, 'variation_post_link' ], 10, 2 );

		// Fix elementor data import alongside with installed wordpress-importer plugin.
		if ( class_exists( 'Elementor\Compatibility' ) ) {
			remove_filter( 'wp_import_post_meta', [ 'Elementor\Compatibility', 'on_wp_import_post_meta' ] );
		}

		add_filter( 'wp_import_post_meta', [ $this, 'fix_menus_for_microsite' ] );
		add_filter( 'wp_import_post_meta', [ $this, 'remove_wc_product_reviews_meta' ] );
		add_filter( 'wxr_menu_item_args', [ $this, 'menu_item_args_filter' ] );

		$this->importer->log_reset();

		$demo_title = isset( $this->demo['title'] ) ? $this->demo['title'] : 'demo';
		$this->importer->log_add( 'Importing ' . $demo_title );

		$start = microtime( true );

		$elementor_importer = new \The7_Elementor_Importer( $this->importer, $this->tracker );
		$elementor_importer->do_before_importing_content();

		$this->importer->fetch_attachments = ! empty( $options['fetch_attachments'] );
		$this->importer->import( $file_name );

		$elementor_importer->do_after_importing_content();

		$this->importer->log_add( 'Content was imported in: ' . ( microtime( true ) - $start ) );

		return true;
	}

	public function getPostsList( $post_types ) {
		if ( ! $this->file_exists( $this->xml_file_to_import ) ) {
			return false;
		}

		$parser      = new WXR_Parser();
		$import_data = $parser->parse( $this->xml_file_to_import );

		$available_posts = [];

		if ( ! empty( $import_data['posts'] ) ) {
			foreach ( $import_data['posts'] as $post ) {
				if ( ! isset( $post['status'] ) || $post['status'] !== 'publish' ) {
					continue;
				}

				if ( isset( $post['post_type'] ) && in_array( $post['post_type'], $post_types, true ) ) {
					$available_posts[ $post['post_type'] ][ $post['post_id'] ] = [
						'post_title' => $post['post_title'],
						'url'        => $post['link'],
					];
				}
			}
		}

		return [
			'response' => 'getPostsList',
			'data'     => $available_posts,
		];
	}

	/**
	 * Filter menu item args.
	 *
	 * Replace demo-relative urls with site-relative.
	 *
	 * @since 7.4.1
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function menu_item_args_filter( $args ) {
		$home_url  = home_url( '/' );
		$demo_path = $this->get_demo_url_path();
		if ( $demo_path !== '/' ) {
			$args['menu-item-url'] = preg_replace( "#^{$demo_path}(.*)#", "{$home_url}$1", $args['menu-item-url'] );
		}

		return $args;
	}

	/**
	 * Alter post meta to be imported properly.
	 *
	 * Update microsite custom menu fields with new nav_menu term ids.
	 *
	 * @since 6.7.0
	 *
	 * @param array $post_meta Imported post meta.
	 *
	 * @return array
	 */
	public function fix_menus_for_microsite( $post_meta ) {
		$keys_to_migrate = array(
			'_dt_microsite_primary_menu',
			'_dt_microsite_split_left_menu',
			'_dt_microsite_split_right_menu',
			'_dt_microsite_mobile_menu',
		);

		$processed_terms = array();
		if ( isset( $this->importer->processed_terms ) ) {
			$processed_terms = $this->importer->processed_terms;
		}

		foreach ( $post_meta as $meta_index => $meta ) {
			if ( array_key_exists( $meta['value'], $processed_terms ) && in_array( $meta['key'], $keys_to_migrate, true ) ) {
				$post_meta[ $meta_index ]['value'] = $processed_terms[ $meta['value'] ];
			}
		}

		return $post_meta;
	}

	/**
	 * We should take care of product reviews meta since we do not import any comments.
	 *
	 * @param array $post_meta Imported post meta.
	 *
	 * @return array
	 */
	public function remove_wc_product_reviews_meta( $post_meta ) {
		$reviews_meta = [
			'_wc_average_rating',
			'_wc_rating_count',
			'_wc_review_count',
		];

		foreach ( $post_meta as $meta_index => $meta ) {
			if ( in_array( $meta['key'], $reviews_meta, true ) ) {
				unset( $post_meta[ $meta_index ] );
			}
		}

		return $post_meta;
	}

	public function add_the7_imported_item_meta_action( $attachment_id ) {
		add_post_meta( $attachment_id, '_the7_imported_item', $this->demo['id'] );
	}

	/**
	 * Link to parent products when getting permalink for variation. Fail safe.
	 *
	 * @see WC_Post_Data::variation_post_link()
	 *
	 * @param $permalink
	 * @param $post
	 *
	 * @return string
	 */
	public function variation_post_link( $permalink, $post ) {
		if ( 'product_variation' === $post->post_type && function_exists( 'wc_get_product' ) ) {
			$variation = wc_get_product( $post->ID );
			if ( is_object( $variation ) ) {
				return $variation->get_permalink();
			}
		}

		return $permalink;
	}

	public function import_the7_dashboard_settings() {
		$dashboard_settings = $this->get_site_meta( 'the7_dashboard_settings' );
		if ( $dashboard_settings ) {
			$importer = new The7_Dashboard_Settings_Importer( $this->tracker );
			$importer->import( $dashboard_settings );
		}
	}

	public function add_the7_dashboard_settings() {
		$dashboard_settings = $this->get_site_meta( 'the7_dashboard_settings' );
		if ( $dashboard_settings ) {
			$importer = new The7_Dashboard_Settings_Importer( $this->tracker );
			$importer->add( $dashboard_settings );
		}
	}

	/**
	 * Import theme options.
	 */
	public function import_theme_option() {
		$site_meta = $this->get_site_meta();

		if ( isset( $site_meta['theme_options'] ) ) {
			$options_importer = new The7_Theme_Options_Importer( $this->importer, $this->tracker );
			$options_importer->import( $site_meta['theme_options'] );
		}
	}

	/**
	 * Import wp settings.
	 */
	public function import_wp_settings() {
		$site_meta = $this->get_site_meta();

		$wp_settings_importer = new The7_WP_Settings_Importer( $this->importer, $this->tracker );

		if ( ! empty( $site_meta['wp_settings'] ) ) {
			$wp_settings_importer->import_settings( $site_meta['wp_settings'] );
		}

		if ( ! empty( $site_meta['nav_menu_locations'] ) ) {
			$wp_settings_importer->import_menu_locations( $site_meta['nav_menu_locations'] );
		}

		if ( ! empty( $site_meta['widgets_settings'] ) ) {
			$wp_settings_importer->import_widgets( $site_meta['widgets_settings'] );
		}
	}

	/**
	 * Import ultimate addons settings.
	 */
	public function import_ultimate_addons_settings() {
		$site_meta         = $this->get_site_meta();
		$ultimate_importer = new \The7_Ultimate_Addons_Importer( $this->tracker );

		if ( ! empty( $site_meta['ultimate_selected_google_fonts'] ) ) {
			$ultimate_importer->import_google_fonts( $site_meta['ultimate_selected_google_fonts'] );
		}

		if ( isset( $site_meta['schema']['folders']['ultimate_icon_fonts'] ) && ! empty( $site_meta['ultimate_icon_fonts'] ) ) {
			$demo_icons  = (array) $site_meta['ultimate_icon_fonts'];
			$from_folder = trailingslashit( $this->content_dir ) . $site_meta['schema']['folders']['ultimate_icon_fonts'];

			$ultimate_importer->import_icon_fonts( $demo_icons, $from_folder );
		}
	}

	/**
	 * Import The7 Font Awesome.
	 *
	 * @return bool
	 */
	public function import_the7_fontawesome() {
		$site_meta = $this->get_site_meta();

		if ( empty( $site_meta['the7_fontawesome_version'] ) ) {
			return false;
		}

		if ( $site_meta['the7_fontawesome_version'] === 'fa5' ) {
			The7_Icon_Manager::enable_fontawesome5();
		} else {
			The7_Icon_Manager::enable_fontawesome4();
		}

		return true;
	}

	/**
	 * Import revoluton slider sliders.
	 */
	public function import_rev_sliders() {
		$site_meta = $this->get_site_meta();

		if ( empty( $site_meta['revolution_sliders'] ) ) {
			return;
		}

		require_once __DIR__ . '/importers/class-the7-revslider-importer.php';
		$rev_slider_importer = new The7_Revslider_Importer();

		add_action( 'add_attachment', [ $this, 'add_the7_imported_item_meta_action' ] );

		$imported_sliders = [];
		foreach ( (array) $site_meta['revolution_sliders'] as $rev_slider ) {
			$status = $rev_slider_importer->import_slider( $rev_slider, $this->content_dir . "{$rev_slider}.zip" );
			if ( ! empty( $status['success'] ) ) {
				$imported_sliders[] = $status['sliderID'];
			}
		}

		remove_action( 'add_attachment', [ $this, 'add_the7_imported_item_meta_action' ] );

		return $imported_sliders;
	}

	public function import_elementor_settings() {
		$site_meta = $this->get_site_meta();

		$elementor_importer = new \The7_Elementor_Importer( $this->importer, $this->tracker );
		if ( ! empty( $site_meta['elementor'] ) ) {
			$elementor_importer->import_options( $site_meta['elementor'] );
		}

		if ( ! empty( $site_meta['elementor_kit_settings'] ) && the7_is_elementor3() ) {
			$elementor_importer->import_kit_settings( $site_meta['elementor_kit_settings'] );
		}
	}

	public function import_tinvwl_settings() {
		if ( ! defined( 'TINVWL_PREFIX' ) || ! class_exists( 'TInvWL_Admin_Settings_General' ) ) {
			return;
		}

		$site_meta = $this->get_site_meta();

		if ( empty( $site_meta['ti_wish_list_settings'] ) ) {
			return;
		}

		$ti_settings_object = TInvWL_Admin_Settings_General::instance();
		$ti_settings_declaration = (array) $ti_settings_object->constructor_data();
		$settings_to_import      = $site_meta['ti_wish_list_settings'];
		foreach ( $ti_settings_declaration as $settings_group ) {
			$option_id = TINVWL_PREFIX . '-' . $settings_group['id'];
			if ( array_key_exists( $option_id, $settings_to_import ) ) {
				update_option( $option_id, $settings_to_import[ $option_id ] );
			}
		}
	}

	public function import_woocommerce_settings() {
		if ( ! the7_is_woocommerce_enabled() ) {
			return;
		}

		$global_settings = $this->get_site_meta( 'woocommerce' );

		if ( ! $global_settings ) {
			return;
		}

		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		$global_settings = (array) $global_settings;

		$woocommerce_page_settings = wp_parse_args(
			$global_settings,
			[
				'woocommerce_shop_page_id'      => false,
				'woocommerce_cart_page_id'      => false,
				'woocommerce_checkout_page_id'  => false,
				'woocommerce_myaccount_page_id' => false,
				'woocommerce_terms_page_id'     => false,
			]
		);
		foreach ( $woocommerce_page_settings as $opt_id => $post_id ) {
			if ( ! $post_id ) {
				continue;
			}

			$imported_post_id = $this->importer->get_processed_post( $post_id );
			if ( $imported_post_id ) {
				$post_id = $imported_post_id;
			}

			update_option( $opt_id, $post_id );
		}

		$wc_image_settings = [
			'woocommerce_single_image_width',
			'woocommerce_thumbnail_image_width',
			'woocommerce_thumbnail_cropping',
			'woocommerce_thumbnail_cropping_custom_width',
			'woocommerce_thumbnail_cropping_custom_height',
		];

		$options_to_export = [];
		foreach ( $wc_image_settings as $wc_option ) {
			if ( isset( $woocommerce_meta[ $wc_option ] ) ) {
				update_option( $wc_option, $woocommerce_meta[ $wc_option ] );
			}
		}

		// Clear any unwanted data and flush rules.
		update_option( 'woocommerce_queue_flush_rewrite_rules', 'yes' );
		WC()->query->init_query_vars();
		WC()->query->add_endpoints();
	}

	/**
	 * Import WooCommerce attributes.
	 *
	 * @since 9.6.1
	 */
	public function import_woocommerce_attributes() {
		if ( ! the7_is_woocommerce_enabled() ) {
			return;
		}

		$attributes = $this->get_site_meta( 'wc_attributes' );

		if ( ! $attributes ) {
			return;
		}

		foreach ( $attributes as $attribute ) {
			wc_create_attribute(
				[
					'name'         => $attribute['attribute_label'],
					'slug'         => $attribute['attribute_name'],
					'type'         => $attribute['attribute_type'],
					'order_by'     => $attribute['attribute_orderby'],
					'has_archives' => (bool) $attribute['attribute_public'],
				]
			);
		}

		the7_wc_flush_attributes_cache();
	}

	/**
	 * Recount WC terms and regenerate product lookup tables. Has to be launched after WC content import.
	 */
	public function do_woocommerce_post_import_actions() {
		if ( ! the7_is_woocommerce_enabled() ) {
			return;
		}

		if ( ! class_exists( 'WC_REST_System_Status_Tools_Controller' ) ) {
			return;
		}

		$tools_controller = new \WC_REST_System_Status_Tools_Controller();
		$tools            = $tools_controller->get_tools();
		$actions          = [
			'recount_terms',
			'regenerate_product_lookup_tables',
			'clear_transients',
			'clear_template_cache',
		];

		foreach ( $actions as $action ) {
			if ( ! array_key_exists( $action, $tools ) ) {
				return;
			}

			$tools_controller->execute_tool( $action );
		}
	}

	/**
	 * @return bool
	 */
	public function import_vc_settings() {
		$site_meta = $this->get_site_meta();

		if ( empty( $site_meta['vc_settings'] ) || ! is_array( $site_meta['vc_settings'] ) ) {
			return false;
		}

		require_once __DIR__ . '/importers/class-the7-vc-importer.php';
		$vc_importer = new The7_VC_Importer();
		if ( $vc_importer->import_settings( $site_meta['vc_settings'] ) ) {
			$vc_importer->show_notification();

			return true;
		}

		return false;
	}

	/**
	 * Return site meta - decoded site-meta.json file content.
	 *
	 * @param string $meta
	 *
	 * @return mixed
	 */
	public function get_site_meta( $meta = null ) {
		if ( $this->site_meta === null ) {
			$this->site_meta = json_decode( file_get_contents( $this->content_dir . 'site-meta.json' ), true );
		}

		if ( $meta === null ) {
			return $this->site_meta;
		}

		if ( isset( $this->site_meta[ $meta ] ) ) {
			return $this->site_meta[ $meta ];
		}

		return null;
	}

	/**
	 * Add error.
	 *
	 * @param string $msg
	 */
	public function add_error( $msg ) {
		$this->errors[] = wp_kses_post( $msg );
	}

	/**
	 * Returns errors string.
	 *
	 * @return string
	 */
	public function get_errors_string() {
		return implode( '', $this->errors );
	}

	/**
	 * @return bool
	 */
	public function has_errors() {
		return ( ! empty( $this->errors ) );
	}

	/**
	 * Replace attachments with noimage dummies.
	 *
	 * @param $raw_post
	 *
	 * @return mixed
	 */
	public function replace_attachment_url( $raw_post ) {
		if ( isset( $raw_post['post_type'] ) && 'attachment' === $raw_post['post_type'] ) {
			$raw_post['attachment_url'] = $raw_post['guid'] = $this->get_noimage_url( $raw_post['attachment_url'] );
		}

		return $raw_post;
	}

	/**
	 * Log fatal errors.
	 */
	public function fatal_errors_log_handler() {
		$error = error_get_last();

		if ( $error && in_array( $error['type'], array( E_ERROR, E_PARSE, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR ), true ) ) {
			$this->importer->log_add( 'Error: ' . $error['message'] );
		}
	}

	/**
	 * @param string $file_name
	 *
	 * @return bool
	 */
	protected function file_exists( $file_name ) {
		if ( ! is_file( $file_name ) ) {
			$this->add_error(
				esc_html__(
					"The XML file containing the dummy content is not available or could not be read in {$file_name}",
					'the7mk2'
				)
			);

			return false;
		}

		return true;
	}

	/**
	 * Returns dummy image src.
	 *
	 * @param string $origin_img_url
	 *
	 * @return string
	 */
	protected function get_noimage_url( $origin_img_url ) {
		switch ( pathinfo( $origin_img_url, PATHINFO_EXTENSION ) ) {
			case 'jpg':
			case 'jpeg':
				$ext = 'jpg';
				break;

			case 'png':
				$ext = 'png';
				break;

			case 'gif':
			default:
				$ext = 'gif';
				break;
		}

		return PRESSCORE_ADMIN_URI . "/assets/images/noimage.{$ext}";
	}

	/**
	 * @return string
	 */
	protected function get_demo_url_path() {
		return parse_url( $this->demo['link'], PHP_URL_PATH );
	}
}
