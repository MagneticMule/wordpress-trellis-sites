<?php
/**
 * Plugin Name: Five Star Business Profile and Schema
 * Plugin URI:  https://www.fivestarplugins.com/plugins/business-profile/
 * Description: Add schema structured data to any page or post type. Create an SEO friendly contact card with your business info and associated schema. Supports Google Map, opening hours and more.
 * Version:     2.1.5
 * Author:      Five Star Plugins
 * Author URI:  https://www.fivestarplugins.com
 * License: GPLv3
 * License URI:http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Text Domain: business-profile
 * Domain Path: /languages/
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpInit', false ) ) :

	class bpfwpInit {

		/**
		 * Settings for displaying the contact card currently being handled.
		 *
		 * @since  0.0.1
		 * @access public
		 * @var    array
		 */
		public $display_settings = array();

		/**
		 * Placeholder for the main settings class instance.
		 *
		 * @since  0.0.1
		 * @access public
		 * @var    object bpfwpSettings
		 */
		public $settings;

		/**
		 * Placeholder for the main CPTs class instance.
		 *
		 * @since  0.0.1
		 * @access public
		 * @var    object bpfwpCustomPostTypes
		 */
		public $cpts;

		/**
		 * Initialize the plugin and register hooks.
		 *
		 * @since  0.0.1
		 * @access public
		 * @return void
		 */
		public function __construct() {
			self::constants();
			self::includes();
			self::instantiate();
			self::wp_hooks();
			if ( $this->settings->get_setting( 'multiple-locations' ) ) {
				register_activation_hook( __FILE__, array( $this->cpts, 'flush_rewrite_rules' ) );
			}

			// Add the admin menu
			add_action( 'admin_menu', array( $this, 'add_menu_page' ) );

			// Add a link to the Google Rich Results Test Page for front-end pages
			add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_link' ), 100 );

			// Load permissions and handle combination
			$this->permissions = new bpfwpPermissions();

			// Load plugin dashboard
			require_once( BPFWP_PLUGIN_DIR . '/includes/class-dashboard.php' );
			new bpfwpDashboard();

		}

		public function add_menu_page() {
			add_menu_page(
				__( 'Business Profile', 'business-profile' ),
				__( 'Business Profile', 'business-profile' ),
				'manage_options',
				'bpfwp-business-profile',
				'',
				'dashicons-businessperson',
				51
			);
		}

		/**
		 * Add a link to the Google Rich Results Test Page to front-end pages
		 *
		 * @since 2.1.0
		 * @return void
		 */
		public function add_admin_bar_link( $admin_bar ) {

			if ( is_admin() ) { return; }

			$admin_bar->add_node(
				array(
					'id'	=> 'business_profile_test_link',
					'title'	=> 'Test Schema',
					'href'	=> 'https://search.google.com/test/rich-results?url=' . get_the_permalink(),
					'meta'	=> array(
						'target'	=> 'blank',
						'title'		=> 'View Google Schema Test Results'
					)
				)
			);
		}

		/**
		 * Define plugin constants.
		 *
		 * @since  1.1.0
		 * @access protected
		 * @return void
		 */
		protected function constants() {
			define( 'BPFWP_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			define( 'BPFWP_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
			define( 'BPFWP_PLUGIN_FNAME', plugin_basename( __FILE__ ) );
			define( 'BPFWP_VERSION', '2.0.5' );
		}

		/**
		 * Include all plugin files.
		 *
		 * @since  1.1.0
		 * @access protected
		 * @return void
		 */
		protected function includes() {
			require_once BPFWP_PLUGIN_DIR . '/includes/class-blocks.php';
			require_once BPFWP_PLUGIN_DIR . '/includes/class-compatibility.php';
			require_once BPFWP_PLUGIN_DIR . '/includes/class-custom-post-types.php';
			require_once BPFWP_PLUGIN_DIR . '/includes/class-deactivation-survey.php';
			require_once BPFWP_PLUGIN_DIR . '/includes/class-installation-walkthrough.php';
			require_once BPFWP_PLUGIN_DIR . '/includes/class-integrations.php';
			require_once BPFWP_PLUGIN_DIR . '/includes/class-permissions.php';
			require_once BPFWP_PLUGIN_DIR . '/includes/class-review-ask.php';
			require_once BPFWP_PLUGIN_DIR . '/includes/class-schemas-manager.php';
			require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema.php';
			require_once BPFWP_PLUGIN_DIR . '/includes/class-settings.php';
			require_once BPFWP_PLUGIN_DIR . '/includes/class-template-loader.php';
			require_once BPFWP_PLUGIN_DIR . '/includes/template-functions.php';
			require_once BPFWP_PLUGIN_DIR . '/includes/helper-functions.php';
		}

		/**
		 * Spin up instances of our plugin classes.
		 *
		 * @since  1.1.0
		 * @access protected
		 * @return void
		 */
		protected function instantiate() {
			
			new bpfwpCompatibility();
			new bpfwpIntegrations(); // Deprecated in v1.1.
			new bpfwpDeactivationSurvey();
			new bpfwpReviewAsk();
			new bpfwpInstallationWalkthrough();
			
			$this->permissions = new bpfwpPermissions();
			$this->schemas = new bpfwpSchemasManager();
			$this->settings = new bpfwpSettings();
			$this->cpts = new bpfwpCustomPostTypes();
			$this->blocks = new bpfwpBlocks();

			$this->blocks->run();
			$this->cpts->run( $this->settings->get_setting('multiple-locations') );
		}

		/**
		 * Hook into WordPress.
		 *
		 * @since  1.1.0
		 * @access protected
		 * @return void
		 */
		protected function wp_hooks() {
			add_action( 'plugins_loaded',        array( $this, 'load_textdomain' ) );
			add_action( 'admin_notices',		 array( $this, 'display_header_area') );
			add_action( 'wp_enqueue_scripts',    array( $this, 'register_assets' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
			add_action( 'widgets_init',          array( $this, 'register_widgets' ) );
			add_filter( 'the_content', 			 array( $this, 'append_to_content' ) );
			add_filter( 'plugin_action_links',   array( $this, 'plugin_action_links' ), 10, 2 );
		}

		/**
		 * Load the plugin textdomain for localistion.
		 *
		 * @since  0.0.1
		 * @access public
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain(
				'business-profile',
				false,
				plugin_basename( dirname( __FILE__ ) ) . '/languages'
			);
		}

		/**
		 * Register the front-end CSS styles
		 *
		 * @since  0.0.1
		 * @access public
		 * @return void
		 */
		function register_assets() {
			wp_register_style(
				'bpfwp-default',
				BPFWP_PLUGIN_URL . '/assets/css/contact-card.css',
				null,
				BPFWP_VERSION
			);
			wp_register_script(
				'bpfwp-map',
				BPFWP_PLUGIN_URL . '/assets/js/map.js',
				array( 'jquery' ),
				BPFWP_VERSION,
				true
			);
		}

		/**
		 * Register the widgets
		 *
		 * @since  0.0.1
		 * @access public
		 * @return void
		 */
		public function register_widgets() {
			require_once BPFWP_PLUGIN_DIR . '/includes/class-contact-card-widget.php';
			register_widget( 'bpfwpContactCardWidget' );
		}


	public function display_header_area() {
		global $bpfwp_controller, $post;

		$screen = get_current_screen();
		$screenID = $screen->id;
		$screenPostType = $screen->post_type;
		$settings = get_option( 'bpfwp-settings', [] );

		if ( $screenID != 'business-profile_page_bpfwp-settings' && $screenID != 'business-profile_page_bpfwp-dashboard' && $screenPostType != 'location' && $screenPostType != 'schema' ) {return;}

		if ( ! $bpfwp_controller->permissions->check_permission( 'premium' ) || get_option("BPFWP_Trial_Happening") == "Yes" ) {
			?>
			<div class="bpfwp-dashboard-new-upgrade-banner">
				<div class="bpfwp-dashboard-banner-icon"></div>
				<div class="bpfwp-dashboard-banner-buttons">
					<a class="bpfwp-dashboard-new-upgrade-button" href="https://www.fivestarplugins.com/license-payment/?Selected=BPFWP&Quantity=1" target="_blank">UPGRADE NOW</a>
				</div>
				<div class="bpfwp-dashboard-banner-text">
					<div class="bpfwp-dashboard-banner-title">
						GET FULL ACCESS WITH OUR PREMIUM VERSION
					</div>
					<div class="bpfwp-dashboard-banner-brief">
						Automatic schema integration into posts and with other plugins, multiple locations and more!
					</div>
				</div>
			</div>
			<?php
		}
		
		?>
		<div class="bpfwp-admin-header-menu">
			<h2 class="nav-tab-wrapper">
			<a id="bpfwp-dash-mobile-menu-open" href="#" class="menu-tab nav-tab"><?php _e("MENU", 'business-profile'); ?><span id="bpfwp-dash-mobile-menu-down-caret">&nbsp;&nbsp;&#9660;</span><span id="bpfwp-dash-mobile-menu-up-caret">&nbsp;&nbsp;&#9650;</span></a>
			<a id="dashboard-menu" href='admin.php?page=bpfwp-dashboard' class="menu-tab nav-tab <?php if ($screenID == 'profile_page_bpfwp-dashboard') {echo 'nav-tab-active';}?>"><?php _e("Dashboard", 'business-profile'); ?></a>
			<?php if($settings['multiple-locations'] == 1){ ?><a id="locations-menu" href='edit.php?post_type=location' class="menu-tab nav-tab <?php if ($screenID == 'post-type-location') {echo 'nav-tab-active';}?>"><?php _e("Locations", 'business-profile'); ?></a><?php } ?>
			<a id="schemas-menu" href='edit.php?post_type=schema' class="menu-tab nav-tab <?php if ($screenID == 'post-type-schema') {echo 'nav-tab-active';}?>"><?php _e("Schemas", 'business-profile'); ?></a>
			<a id="options-menu" href='admin.php?page=bpfwp-settings' class="menu-tab nav-tab <?php if ($screenID == 'profile_page_bpfwp-settings') {echo 'nav-tab-active';}?>"><?php _e("Settings", 'business-profile'); ?></a>
			</h2>
		</div>
		<?php
	}

		/**
		 * Enqueue the admin CSS for locations
		 *
		 * @since  1.1
		 * @access public
		 * @global WP_Post $post The current WordPress post object.
		 * @param  string $hook_suffix The current admin screen slug.
		 * @return void
		 */
		public function enqueue_admin_assets( $hook_suffix ) {

			global $post;
			$screen = get_current_screen();
			$screenPostType = $screen->post_type;

			if (
				'post-new.php' === $hook_suffix 
				|| 'post.php' === $hook_suffix 
				|| $screenPostType == 'location' 
				|| $screenPostType == 'schema'
				|| $screen->id == 'business-profile_page_bpfwp-settings'
			) {
				//if ( $this->settings->get_setting( 'multiple-locations' ) && $this->cpts->location_cpt_slug === $post->post_type ) {
					wp_enqueue_style( 'bpfwp-admin-location', BPFWP_PLUGIN_URL . '/assets/css/admin.css', array(), BPFWP_VERSION );
					wp_enqueue_script( 'bpfwp-admin-js', BPFWP_PLUGIN_URL . '/assets/js/admin.js', array( 'jquery' ), BPFWP_VERSION, true );
				//}
			}
		}

		/**
		 * Add links to the plugin listing on the installed plugins page
		 *
		 * @since  0.0.1
		 * @access public
		 * @param  array  $links The current plugin action links.
		 * @param  string $plugin The current plugin slug.
		 * @return array $links Modified action links.
		 */
		public function plugin_action_links( $links, $plugin ) {
			if ( BPFWP_PLUGIN_FNAME === $plugin ) {
				$links['help'] = sprintf( '<a href="http://doc.fivestarplugins.com/plugins/business-profile/" title="%s">%s</a>',
					__( 'View the help documentation for Business Profile', 'business-profile' ),
					__( 'Help', 'business-profile' )
				);
			}

			return $links;
		}

		/**
		 * Retrieve the get_theme_supports() value for a feature
		 *
		 * @since  1.1
		 * @access public
		 * @param  string $feature A theme support feature to get.
		 * @return bool Whether or not a feature is supported.
		 */
		public function get_theme_support( $feature ) {

			$theme_support = get_theme_support( 'business-profile' );

			if ( true === $theme_support ) {
				return true;
			} elseif ( false === $theme_support ) {
				return false;
			} else {
				$theme_support = (array) $theme_support;
				$theme_support = array_shift( $theme_support );
				return isset( $theme_support[ $feature ] ) && true === $theme_support[ $feature ];
			}
		}

		/**
		 * Append contact card to a post's $content variable
		 * @since 2.0.8
		 */
		function append_to_content( $content ) {
			global $post;
	
			if ( !is_main_query() || !in_the_loop() || post_password_required() ) {
				return $content;
			}
	
			if ( $post->ID == $this->settings->get_setting( 'contact-page' ) and ! $this->settings->get_setting( 'disable-contact-page-card' ) ) {
				return $content . bpwfwp_print_contact_card();
			}
	
			return $content;
		}

		/**
		 * Return a single instance of the main plugin class.
		 *
		 * Developers and tests may still create multiple instances by spinning
		 * them up directly, but for most uses, this method is preferred.
		 *
		 * @since 1.1.0
		 * @access public
		 * @static
		 * @return object bpfwpInit A single instance of the main plugin class.
		 */
		public static function instance() {
			static $instance;
			if ( null === $instance ) {
				$instance = new self;
			}
			return $instance;
		}
	}
endif;

$bpfwp_controller = bpfwpInit::instance();

