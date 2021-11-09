<?php
/**
 * NS Featured Posts
 *
 * @package NS_Featured_Posts
 */

/**
 * NS Featured Posts Plugin class.
 *
 * @since 1.0.0
 */
class NS_Featured_Posts {

	/**
	 * Plugin version
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const VERSION = '2.0.3';

	/**
	 * Plugin slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $plugin_slug = 'ns-featured-posts';

	/**
	 * Plugin default options.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected static $default_options = null;

	/**
	 * Plugin options.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * Instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		// Load plugin text domain.
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added.
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		self::$default_options = array(
			'nsfp_posttypes'  => array( 'post' ),
			'nsfp_radio_mode' => array(),
			'nsfp_max_posts'  => 3,
			'nsfp_max_types'  => array(),
		);

		$this->set_default_options();

		// Get current options.
		$this->get_current_options();

		// Migrate options.
		add_action( 'init', array( $this, 'migrate_options' ) );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since 1.0.0
	 *
	 * @return Plugin slug.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $network_wide Whether network wide.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids.
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();
			} else {
				self::single_activate();
			}
		} else {
			self::single_activate();
		}
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $network_wide Whether network wide.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids.
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();
				}

				restore_current_blog();
			} else {
				self::single_deactivate();
			}
		} else {
			self::single_deactivate();
		}
	}

	/**
	 * Fired when a new site is activated with a multisite environment.
	 *
	 * @since 1.0.0
	 *
	 * @param int $blog_id ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {
		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}

	/**
	 * Get all active blog ids.
	 *
	 * @since 1.0.0
	 *
	 * @return array|false The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {
		global $wpdb;

		$ids = array();

		$output = $wpdb->get_results( "SELECT blog_id FROM $wpdb->blogs WHERE archived = '0' AND spam = '0' AND deleted = '0'", ARRAY_A );

		if ( $output ) {
			$ids = wp_list_pluck( $output, 'blog_id' );
		}

		return $ids;
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		$option_name = 'nsfp_plugin_options';
		update_option( $option_name, self::$default_options );
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since 1.0.0
	 */
	private static function single_deactivate() {
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( $this->plugin_slug );
	}

	/**
	 * Fetch plugin options.
	 *
	 * @since 1.0.0
	 */
	private function get_current_options() {
		$nsfp_options  = array_merge( self::$default_options, (array) get_option( 'nsfp_plugin_options', array() ) );
		$this->options = $nsfp_options;
	}

	/**
	 * Set default plugin options.
	 *
	 * @since 1.0.0
	 */
	private function set_default_options() {
		if ( ! get_option( 'nsfp_plugin_options' ) ) {
			update_option( 'nsfp_plugin_options', self::$default_options );
		}
	}

	/**
	 * Get plugin options details.
	 *
	 * @since 1.0.0
	 *
	 * @return array Options array.
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Migrate options.
	 *
	 * @since 1.4.3
	 */
	public function migrate_options() {
		if ( 'yes' === get_option( 'nsfp_option_migration_complete' ) ) {
			return;
		}

		$opt = get_option( 'nsfp_plugin_options' );

		if ( $opt ) {
			if ( isset( $opt['nsfp_posttypes'] ) && ! empty( $opt['nsfp_posttypes'] ) ) {

				$values = array_keys( $opt['nsfp_posttypes'] );

				$values = array_filter( $values );

				if ( ! empty( $values ) ) {
					$opt['nsfp_posttypes'] = $values;
				}

				update_option( 'nsfp_plugin_options', $opt );
			}
		}

		update_option( 'nsfp_option_migration_complete', 'yes' );
	}
}
