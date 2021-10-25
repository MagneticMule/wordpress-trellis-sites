<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Demo_Content {

	/**
	 * @var The7_Demo_Content_Admin
	 */
	public $admin;

	/**
	 * @var The7_Demo_Content_Remote_Content
	 */
	public $remote;

	/**
	 * @var array
	 */
	protected $demos = [];

	public function __construct() {
		$this->load_dependencies();
		$this->define_admin_hooks();
	}

	public function setup_admin_page_hooks( $page_hook ) {
		add_action( 'load-' . $page_hook, [ $this->remote, 'update_check' ] );
		add_action( 'admin_print_styles-' . $page_hook, [ $this->admin, 'enqueue_styles' ] );
		add_action( 'admin_print_scripts-' . $page_hook, [ $this->admin, 'enqueue_scripts' ] );
	}

	/**
	 * @return string
	 */
	public function admin_url() {
		return admin_url( 'admin.php?page=the7-demo-content' );
	}

	/**
	 * @return array
	 */
	public function get_demos() {
		if ( ! $this->demos ) {
			$this->demos = array_map(
				function ( $demo ) {
					return new The7_Demo( $demo );
				},
				$this->get_raw_demos()
			);
		}

		return $this->demos;
	}

	/**
	 * @return array
	 */
	public function get_raw_demos() {
		return apply_filters( 'the7_demo_content_list', [] );
	}

	/**
	 * @param string $demo_id
	 *
	 * @return The7_Demo|null
	 */
	public function get_demo( $demo_id ) {
		$demos = $this->get_demos();

		if ( ! isset( $demos[ $demo_id ] ) ) {
			return null;
		}

		return $demos[ $demo_id ];
	}

	/**
	 * @param string $demo_id
	 *
	 * @return array
	 */
	public function get_raw_demo( $demo_id ) {
		$demos = $this->get_raw_demos();

		if ( ! isset( $demos[ $demo_id ] ) ) {
			return [];
		}

		return $demos[ $demo_id ];
	}

	private function load_dependencies() {
		require_once __DIR__ . '/class-the7-demo-content-tgmpa.php';
		require_once __DIR__ . '/class-the7-demo-content-import-manager.php';
		require_once __DIR__ . '/class-the7-demo-content-phpstatus.php';
		require_once __DIR__ . '/class-the7-demo-content-remote-content.php';

		require_once __DIR__ . '/admin/class-the7-demo-content-admin.php';
		require_once __DIR__ . '/admin/class-the7-demo-content-meta-box.php';
		require_once __DIR__ . '/actions-builders/class-the7-demo-actions-builder-base.php';
		require_once __DIR__ . '/actions-builders/class-the7-demo-null-actions-builder.php';
		require_once __DIR__ . '/actions-builders/class-the7-demo-full-import-actions-builder.php';
		require_once __DIR__ . '/actions-builders/class-the7-demo-url-import-actions-builder.php';
		require_once __DIR__ . '/actions-builders/class-the7-demo-post-import-actions-builder.php';
		require_once __DIR__ . '/actions-builders/class-the7-demo-remove-actions-builder.php';
		require_once __DIR__ . '/actions-builders/class-the7-demo-keep-actions-builder.php';

		require_once __DIR__ . '/trackers/class-the7-demo-tracker.php';
		require_once __DIR__ . '/trackers/class-the7-demo-null-tracker.php';
		require_once __DIR__ . '/trackers/class-the7-demo-content-tracker.php';
		require_once __DIR__ . '/class-the7-demo-remover.php';

		require_once __DIR__ . '/importers/class-the7-wp-settings-importer.php';
		require_once __DIR__ . '/importers/class-the7-ultimate-addons-importer.php';
		require_once __DIR__ . '/importers/class-the7-elementor-importer.php';
		require_once __DIR__ . '/importers/class-the7-theme-options-importer.php';
		require_once __DIR__ . '/importers/class-the7-dashboard-settings-importer.php';
		require_once __DIR__ . '/class-the7-demo.php';
	}

	private function define_admin_hooks() {
		$this->admin  = new The7_Demo_Content_Admin();
		$this->remote = new The7_Demo_Content_Remote_Content();

		add_action( 'admin_enqueue_scripts', [ $this->admin, 'register_scripts' ] );

		add_action( 'admin_notices', [ $this->admin, 'add_admin_notices' ] );

		add_action( 'wp_ajax_the7_import_demo_content', [ $this->admin, 'ajax_import_demo_content' ] );
		add_action( 'wp_ajax_the7_remove_demo_content', [ $this->admin, 'ajax_remove_demo_content' ] );
		add_action( 'wp_ajax_the7_demo_content_php_status', [ $this->admin, 'ajax_get_php_ini_status' ] );
		add_action( 'wp_ajax_the7_keep_demo_content', [ $this->admin, 'ajax_keep_demo_content' ] );

		add_action( 'admin_menu', [ $this->admin, 'add_import_by_url_admin_menu' ] );

		add_action( 'add_meta_boxes', [ 'The7_Demo_Content_Meta_Box', 'add' ] );
		add_action( 'wp_ajax_the7_demo_keep_the_post', [ 'The7_Demo_Content_Meta_Box', 'save' ] );
	}
}
