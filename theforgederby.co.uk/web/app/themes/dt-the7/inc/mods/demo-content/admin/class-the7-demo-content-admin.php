<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    DT_Dummy
 * @subpackage DT_Dummy/admin
 * @author     Dream-Theme
 */
class The7_Demo_Content_Admin {

	const REQUIRED_USER_CAPABILITY = 'edit_theme_options';

	/**
	 * Register scripts.
	 *
	 * @since 7.0.0
	 */
	public function register_scripts() {
		the7_register_style( 'the7-import-on-edit-screen', PRESSCORE_ADMIN_URI . '/assets/css/import-on-edit-screen' );
		the7_register_style( 'the7-demo-content', PRESSCORE_ADMIN_URI . '/assets/css/demo-content', array( 'the7-import-on-edit-screen' ) );

		the7_register_script( 'the7-demo-content', PRESSCORE_ADMIN_URI . '/assets/js/demo-content', array( 'jquery', 'jquery-ui-progressbar' ), false, true );
	}

	/**
	 * Enqueue styles for edit screen.
	 *
	 * @since 7.0.0
	 */
	public function enqueue_edit_screen_scripts() {
		if ( ! current_user_can( static::REQUIRED_USER_CAPABILITY ) ) {
			return;
		}

		wp_enqueue_style( 'the7-import-on-edit-screen' );
		$this->enqueue_scripts();
	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'the7-demo-content' );
	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $the7_tgmpa, $typenow;

		wp_enqueue_script( 'the7-demo-content' );

		$plugins = array();
		$plugins_page_url = '';
		if ( class_exists( 'Presscore_Modules_TGMPAModule' ) ) {
			$plugins = Presscore_Modules_TGMPAModule::get_plugins_list_cache();
			$plugins = wp_list_pluck( $plugins, 'name', 'slug' );

			if ( ! $the7_tgmpa->is_tgmpa_complete() ) {
				$plugins_page_url = $the7_tgmpa->get_bulk_action_link();
			}
		}

		$post_type_object = get_post_type_object( $typenow ? $typenow : 'post' );

		$strings = [
			'keep_content_confirm'               => esc_html__(
				'If you choose to keep the demo, you will no longer be able to bulk remove it.  Do you wish to continue?',
				'the7mk2'
			),
			'remove_content_confirm'             => esc_html__(
				'Attention! This action will remove all the demo content, including pages, posts, images, menus, theme options, etc. Do you wish to continue?',
				'the7mk2'
			),
			'btn_import'                         => esc_html__( 'Importing...', 'the7mk2' ),
			'msg_import_success'                 => esc_html__( 'Demo content successfully imported.', 'the7mk2' ),
			'msg_import_fail'                    => esc_html__( 'Import Fail!', 'the7mk2' ),
			'download_package'                   => esc_html__( 'Downloading package.', 'the7mk2' ),
			'import_the7_dashboard_settings'	 => esc_html_x( 'Importing The7 dashboard settings.', 'admin', 'the7mk2' ),
			'add_the7_dashboard_settings'		 => esc_html_x( 'Adding The7 dashboard settings.', 'admin', 'the7mk2' ),
			'import_post_types'                  => esc_html__( 'Importing content.', 'the7mk2' ),
			'import_attachments'                 => esc_html__( 'Importing attachments.', 'the7mk2' ),
			'import_theme_options'               => esc_html__( 'Importing theme options.', 'the7mk2' ),
			'import_rev_sliders'                 => esc_html__( 'Importing slider(s).', 'the7mk2' ),
			'cleanup'                            => esc_html__( 'Final cleanup.', 'the7mk2' ),
			'installing_plugin'                  => esc_html__( 'Installing', 'the7mk2' ),
			'activating_plugin'                  => esc_html__( 'Activating plugin(s)', 'the7mk2' ),
			'plugins_activated'                  => esc_html__( 'Plugin(s) activated successfully.', 'the7mk2' ),
			'import_by_url'						 => esc_html__( 'Importing post.', 'the7mk2' ),
			'import_one_post'					 => esc_html__( 'Importing post.', 'the7mk2' ),
			'plugins_installation_error'         => esc_html__( 'Server error.', 'the7mk2' ),
			'rid_of_redirects'                   => esc_html__( 'Cleanup after plugins installation.', 'the7mk2' ),
			'clear_importer_session'			 => esc_html__( 'Clear importer session.', 'the7mk2' ),
			'get_posts'                          => esc_html__( 'Parsing content.', 'the7mk2' ),
			'loading'                          	 => esc_html__( 'Loading...', 'the7mk2' ),
			'remove_content'					 => esc_html_x( 'Removing content...', 'admin', 'the7mk2' ),
			'keep_content'					 	 => esc_html_x( 'Keeping content...', 'admin', 'the7mk2' ),
			'one_post_importing_msg'             => esc_html__( 'Importing', 'the7mk2' ),
			'one_post_importing_choose_posttype' => esc_html__( 'Choose post type', 'the7mk2' ),
			'one_post_importing_choose_post'     => esc_html__( 'Choose post', 'the7mk2' ),
			'one_post_importing_import'          => esc_html__( 'Import post', 'the7mk2' ),
			'one_post_importing_url_msg'         => esc_html__( 'example', 'the7mk2' ),
			'one_post_importing_success'         => esc_html__( 'Demo page successfully imported.', 'the7mk2' ),
			'cannot_found_page_by_url_error'     => esc_html(
				sprintf(
					__( '%%url%% is not a %s or does not exist.', 'the7mk2' ),
					strtolower( $post_type_object->labels->singular_name )
				)
			),
			'cannot_get_posts_list_error'        => esc_html__( 'Cannot get posts lists from package.', 'the7mk2' ),
			'invalid_url_error'                  => wp_kses(
				sprintf(
					esc_html_x(
						'Provided URL (link) is not valid. Please copy a valid URL (link) from one of %s.',
						'admin',
						'the7mk2'
					),
					'<a href="https://the7.io/#!/demos" target="_blank">The7 pre-made websites</a>'
				),
				[ 'a' => [ 'href' => [], 'target' => [] ] ]
			),
			'action_error'                       => esc_html__( 'Error. Cannot complete following action', 'the7mk2' ),
			'go_back_with_error' => sprintf(
				'<a href="' . esc_url( the7_demo_content()->admin_url() ) . '">' . esc_html_x( 'Back to Pre-made Websites', 'admin', 'the7mk2' ) . '</a>'
			),
		];

		wp_localize_script(
			'the7-demo-content',
			'dtDummy',
			[
				'nonces'            => [
					'keep_demo_content' => wp_create_nonce( 'the7_keep_demo_content' ),
				],
				'import_nonce'      => wp_create_nonce( 'the7_import_demo' ),
				'remove_demo_nonce' => wp_create_nonce( 'the7_remove_demo' ),
				'status_nonce'      => wp_create_nonce( 'the7_php_ini_status' ),
				'plugins'           => $plugins,
				'plugins_page_url'  => $plugins_page_url,
				'strings'           => $strings,
			]
		);
	}

	/**
	 * Add admin notice about successful demo installation.
	 *
	 * @since 6.0.1
	 * @param string $type
	 * @param string $demo
	 */
	public function send_admin_notice( $type, $demo = null ) {
//		set_transient( 'the7_demo_admin_notice', compact( 'type', 'demo' ), 20 );
	}

	/**
	 * Add admin notices.
	 *
	 * @since 6.0.1
	 */
	public function add_admin_notices() {
		global $current_screen;

		if ( $current_screen->base !== 'the7_page_the7-demo-content' ) {
			return;
		}

		if ( get_transient( 'the7_demo_admin_notice' ) ) {
			the7_admin_notices()->add( 'the7_demo_content_installed', [ $this, 'print_admin_notice' ], 'notice-success the7-dashboard-notice' );
		}
	}

	/**
	 * Print admin notice about successful demo installation.
	 *
	 * @since 6.0.1
	 */
	public function print_admin_notice() {
		$notice = get_transient( 'the7_demo_admin_notice' );
		delete_transient( 'the7_demo_admin_notice' );

		$demo = isset( $notice['demo'] ) ? $notice['demo'] : 'Demo';
		$text = "<p>{$demo} has been successfully imported.</p>";
		if ( isset( $notice['type'] ) && $notice['type'] === 'sucessful_remove' ) {
			$text = "<p>{$demo} has been successfully removed.</p>";
		}

		echo $text;
	}

	/**
	 * @since 1.0.0
	 */
	public function ajax_import_demo_content() {
		if ( ! check_ajax_referer(  'the7_import_demo', false, false ) || ! current_user_can( static::REQUIRED_USER_CAPABILITY ) ) {
			wp_send_json_error( [ 'error_msg' => '<p>' . __( 'Insufficient user rights.', 'the7mk2' ) . '</p>' ] );
		}

		if ( empty( $_POST['dummy'] ) ) {
			wp_send_json_error( [ 'error_msg' => '<p>' . __( 'Unable to find dummy content.', 'the7mk2' ) . '</p>' ] );
		}

		wp_raise_memory_limit( 'admin' );
		if ( (int) ini_get( 'max_execution_time' ) < 300 ) {
			the7_set_time_limit( 300 );
		}

		$import_type = isset( $_POST['import_type'] ) ? $_POST['import_type'] : 'full_import';
		$demo_id = isset( $_POST['content_part_id'] ) ? sanitize_key( $_POST['content_part_id'] ) : '';

		if ( $import_type === 'full_import' ) {
			$content_tracker = new The7_Demo_Content_Tracker( $demo_id );
		} else {
			$content_tracker = new The7_Demo_Null_Tracker( $demo_id );
		}

		$wp_uploads = wp_get_upload_dir();
		$import_content_dir = trailingslashit( $wp_uploads['basedir'] ) . "the7-demo-content-tmp/{$demo_id}";
		$import_manager = new The7_Demo_Content_Import_Manager( $import_content_dir, the7_demo_content()->get_raw_demo( $demo_id ), $content_tracker );

		do_action( 'the7_demo_content_before_content_import', $import_manager );

		if ( $import_manager->has_errors() ) {
			wp_send_json_error( array( 'error_msg' => $import_manager->get_errors_string() ) );
		}

		$demo = the7_demo_content()->get_demo( $demo_id );

		$retval = null;

		switch ( $_POST['dummy'] ) {
			case 'download_package':
				$source = isset( $_POST['demo_page_url'] ) ? $_POST['demo_page_url'] : '';
				$import_manager->download_dummy( $source );
				break;

			case 'clear_importer_session':
				\The7_Content_Importer::clear_session();
				break;

			case 'import_the7_dashboard_settings':
				// Must be launched in a separate process to make a difference.
				$import_manager->import_the7_dashboard_settings();
				break;

			case 'add_the7_dashboard_settings':
				$import_manager->add_the7_dashboard_settings();
				break;

			case 'import_post_types':
				$import_manager->import_woocommerce_attributes();
				$import_manager->import_post_types();
				$import_manager->import_wp_settings();
				$import_manager->import_vc_settings();
				$import_manager->import_the7_fontawesome();
				$import_manager->import_woocommerce_settings();
				$import_manager->do_woocommerce_post_import_actions();

				$content_tracker->add( 'post_types', true );
				break;

			case 'import_attachments':
				$content_tracker->track_imported_items();

				// In case it's an one post installation.
				$imported_post_id = isset( $_POST['imported_post_id'] ) ? (int) $_POST['imported_post_id'] : 0;
				if ( $imported_post_id ) {
					$import_manager->importer()->add_filter_by_id( $imported_post_id );
				}

				$retval = $import_manager->import_attachments( $demo->include_attachments, $demo->attachments_batch );

				if ( isset( $retval['imported'] ) ) {
					$content_tracker->add( 'attachments_in_process', $retval['imported'] );
				}

				if ( isset( $retval['left'] ) && $retval['left'] === 0 ) {
					$content_tracker->add( 'attachments', $demo->include_attachments ? 'original' : 'placeholders' );
					$content_tracker->remove( 'attachments_imported' );
				}
				break;

			case 'import_theme_options':
				$import_manager->importer()->read_processed_data_from_cache();
				$import_manager->import_theme_option();
				$import_manager->import_ultimate_addons_settings();

				if ( class_exists( 'Elementor\Plugin' ) ) {
					$import_manager->import_elementor_settings();
				}

				if ( defined( 'TINVWL_FVERSION' ) ) {
					$import_manager->import_tinvwl_settings();
				}
				break;

			case 'import_rev_sliders':
				$imported_sliders = $import_manager->import_rev_sliders();
				if ( $import_type === 'full_import' ) {
					$content_tracker->add( 'rev_sliders', $imported_sliders );
				}
				break;

			case 'get_posts':
				$retval = $import_manager->getPostsList( array(
					'page',
					'post',
					'product',
					'dt_portfolio',
					'dt_testimonials',
					'dt_gallery',
					'dt_team',
					'dt_slideshow',
				) );
				if ( is_array( $retval ) && ! $demo->plugins()->is_plugins_active() ) {
					$retval['plugins_to_install'] = array_keys( $demo->plugins()->get_plugins_to_install() );
					$retval['plugins_to_activate'] = array_keys( $demo->plugins()->get_inactive_plugins() );
				}
				break;

			case 'import_one_post':
				$post_id = 0;
				if ( ! empty( $_POST['post_to_import'] ) ) {
					$post_id = $import_manager->import_one_post( (int) $_POST['post_to_import'] );
				}

				$retval = [
					'postPermalink'     => get_permalink( $post_id ),
					'postEditLink'      => get_edit_post_link( $post_id, 'return' ),
					'step2Links'        => $this->get_step_2_post_links( $post_id ),
					'postImportActions' => $this->determine_post_import_actions( $post_id ),
					'imported_post_id'  => $post_id,
				];
				break;

			case 'import_by_url':
				if ( ! isset( $_POST['provided_url'] ) ) {
					$import_manager->add_error( _x( 'Cannot import because no url provided.', 'admin', 'the7mk2' ) );
				}

				$post_id = $import_manager->import_one_post_by_url( $_POST['provided_url'] );
				if ( ! $post_id ) {
					$import_manager->add_error( esc_html_x( 'Cannot find the post with provided url.', 'admin', 'the7mk2' ) );
				}

				$retval = [
					'postPermalink'     => get_permalink( $post_id ),
					'postEditLink'      => get_edit_post_link( $post_id, 'return' ),
					'step2Links'        => $this->get_step_2_post_links( $post_id ),
					'postImportActions' => $this->determine_post_import_actions( $post_id ),
					'imported_post_id'  => $post_id,
				];
				break;

			case 'cleanup':
				$this->send_admin_notice( 'successfull_import', $demo->title );
				\The7_Content_Importer::clear_session();
				$import_manager->cleanup_temp_dir();
				flush_rewrite_rules();
				$retval = [
					'status' => $demo->get_import_status_text(),
				];
				break;
		}

		do_action( 'the7_demo_content_after_content_import', $import_manager );

		if ( $import_manager->has_errors() ) {
			wp_send_json_error( array( 'error_msg' => $import_manager->get_errors_string() ) );
		}

		wp_send_json_success($retval);
	}

	public function ajax_remove_demo_content() {
		if ( ! check_admin_referer( 'the7_remove_demo' ) ) {
			return wp_send_json_error();
		}

		if ( ! current_user_can( static::REQUIRED_USER_CAPABILITY ) ) {
			return wp_send_json_error();
		}

		$demo = the7_demo_content()->get_demo( isset( $_POST['demo'] ) ? $_POST['demo'] : null );

		if ( ! $demo ) {
			return wp_send_json_error();
		}

		$demo_to_remove         = $demo->id;
		$rollback_site_settings = true;
		$history                = get_option( The7_Demo_Content_Tracker::HISTORY_OPTION_ID, [] );
		if ( count( $history ) > 1 ) {
			$history = array_reverse( $history );
			reset( $history );
			$latest_installed_demo_id = key( $history );

			if ( $latest_installed_demo_id !== $demo_to_remove ) {
				$rollback_site_settings = false;
			}
		}

		$content_tracker = new The7_Demo_Content_Tracker( $demo_to_remove );
		$demo_remover    = new The7_Demo_Remover( $content_tracker );

		if ( $content_tracker->get( 'post_types' ) || $content_tracker->get( 'attachments' ) ) {
			$demo_remover->remove_content();
		}

		if ( $rollback_site_settings ) {
			$demo_remover->revert_site_settings();

			if ( $content_tracker->get( 'theme_options' ) ) {
				$demo_remover->remove_theme_options();
			}
		}

		if ( $content_tracker->get( 'rev_sliders' ) ) {
			$demo_remover->remove_rev_sliders();
		}

		$content_tracker->remove_demo();

		$this->send_admin_notice( 'sucessful_remove', $demo->title );

		// Since we changed content tracker history.
		$demo->refresh_import_status();

		return wp_send_json_success(
			[
				'status' => $demo->get_import_status_text( $content_tracker ),
			]
		);
	}

	public function ajax_keep_demo_content() {
		if ( ! check_admin_referer( 'the7_keep_demo_content' ) ) {
			return wp_send_json_error();
		}

		if ( ! current_user_can( static::REQUIRED_USER_CAPABILITY ) ) {
			return wp_send_json_error();
		}

		$demo = the7_demo_content()->get_demo( isset( $_POST['demo'] ) ? $_POST['demo'] : null );

		if ( ! $demo ) {
			return wp_send_json_error();
		}

		$content_tracker = new The7_Demo_Content_Tracker( $demo->id );
		$content_tracker->keep_demo_content();

		// Since we changed content tracker history.
		$demo->refresh_import_status();

		return wp_send_json_success(
			[
				'status' => $demo->get_import_status_text(),
			]
		);
	}

	/**
	 * Check if php.ini have proper params values. Ajax response.
	 */
	public function ajax_get_php_ini_status() {
		if ( ! check_ajax_referer(  'the7_php_ini_status', false, false ) || ! current_user_can( static::REQUIRED_USER_CAPABILITY ) ) {
			wp_send_json_error();
		}

		ob_start();
		include PRESSCORE_ADMIN_DIR . '/screens/partials/the7-demo-content/notices/status.php';
		$status = ob_get_clean();

		wp_send_json_success( $status );
	}

	/**
	 * Render plugin admin page.
	 *
	 * @since 1.0.0
	 */
	public function display_plugin_page() {
		$args = [];
		$step = isset( $_GET['step'] ) ? 'step' . (int) $_GET['step'] : null;
		if ( $step === 'step2' ) {
			$import_type     = isset( $_POST['import_type'] ) ? $_POST['import_type'] : null;
			$actions_builder = $this->get_actions_builder( $import_type, $_POST );
			$actions_builder->localize_data_to_js();
			$args = [
				'error'         => $actions_builder->get_error(),
				'starting_text' => $actions_builder->get_starting_text(),
			];
		}

		presscore_get_template_part( 'the7_admin', 'partials/the7-demo-content/demos', $step, $args );
	}

	/**
	 * @param string $import_type
	 * @param array  $external_data
	 *
	 * @return The7_Demo_Actions_Builder_Base
	 */
	protected function get_actions_builder( $import_type, $external_data = array() ) {
		$class_name = implode( '_', array_map( 'ucfirst', explode( '_', $import_type ) ) );
		$class_name = "The7_Demo_{$class_name}_Actions_Builder";
		if ( class_exists( $class_name ) ) {
			return new $class_name( $external_data );
		}

		return new The7_Demo_Null_Actions_Builder();
	}

	/**
	 * Add import by url admin pages.
	 *
	 * @since 7.0.0
	 */
	public function add_import_by_url_admin_menu() {
		$menu_items = array(
			array(
				'edit.php',
				_x( 'Import Post', 'admin', 'the7mk2' ),
				_x( 'Import', 'admin', 'the7mk2' ),
				'the7-import-post-by-url',
				'post-new.php',
			),
			array(
				'edit.php?post_type=page',
				_x( 'Import Page', 'admin', 'the7mk2' ),
				_x( 'Import', 'admin', 'the7mk2' ),
				'the7-import-page-by-url',
				'post-new.php?post_type=page',
			),
		);

		$menu_items = apply_filters( 'the7_import_by_url_menu_items', $menu_items );

		foreach ( $menu_items as list( $parent_slug, $page_title, $menu_title, $menu_slug, $insert_after ) ) {
			$hook = the7_add_submenu_page_after(
				$parent_slug,
				$page_title,
				$menu_title,
				static::REQUIRED_USER_CAPABILITY,
				$menu_slug,
				[ $this, 'display_import_by_url_admin_page' ],
				$insert_after
			);
			add_action( "admin_print_scripts-{$hook}", array( $this, 'enqueue_edit_screen_scripts' ) );
		}
	}

	/**
	 * Import by url page callback.
	 *
	 * @since 7.0.0
	 */
	public function display_import_by_url_admin_page() {
		if ( ! current_user_can( static::REQUIRED_USER_CAPABILITY ) ) {
			wp_die( _x( 'You have not sufficient capabilities to see this page.', 'admin', 'the7mk2' ) );
		}
		?>

		<div id="the7-dashboard" class="wrap the7-import-by-url-page">
			<h1><?php echo esc_html( get_admin_page_title() ) ?></h1>
			<div class="wp-header-end"></div>
			<div class="the7-import-page">
				<?php presscore_get_template_part( 'the7_admin', 'partials/the7-demo-content/url-import/page' ) ?>
			</div>
		</div>

		<?php
	}

	/**
	 * Determine post-import actions based on provided post id.
	 *
	 * @since 7.0.0
	 *
	 * @param $post_id
	 *
	 * @return array
	 */
	protected function determine_post_import_actions( $post_id ) {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return array( 'cleanup' );
		}

		$actions = array(
			'import_attachments',
		);

		// import rev sliders?
		// search for shortcodes
		if ( preg_match( '/' . get_shortcode_regex( array( 'rev_slider_vc', 'rev_slider' ) ) . '/', $post->post_content ) ) {
			$actions[] = 'import_rev_sliders';
		}

		// check meta fields
		if (
			get_post_meta( $post_id, '_dt_header_title', true ) === 'slideshow'
			&& get_post_meta( $post_id, '_dt_slideshow_mode', true ) === 'revolution'
		) {
			$actions[] = 'import_rev_sliders';
		}

		$actions[] = 'cleanup';

		return array_unique( $actions );
	}

	protected function get_step_2_post_links( $post_id ) {
		$output = '<a href="' . esc_url( get_permalink( $post_id ) ) . '">' . esc_html_x( 'See post', 'admin', 'the7mk2' ) . '</a>';
		$output .= ' | ';
		$output .= '<a href="' . esc_url( get_edit_post_link( $post_id, 'return' ) ) . '">' . esc_html_x( 'Edit post', 'admin', 'the7mk2' ) . '</a>';

		return $output;
	}
}
