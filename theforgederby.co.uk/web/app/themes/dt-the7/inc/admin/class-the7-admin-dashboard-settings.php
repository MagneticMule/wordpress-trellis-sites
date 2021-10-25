<?php
/**
 * The7 admin dashboard settings.
 *
 * @package The7\Admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_Admin_Dashboard_Settings
 */
class The7_Admin_Dashboard_Settings {

	const SETTINGS_ID = 'the7_dashboard_settings';

	/**
	 * Return settings definition.
	 *
	 * @return array
	 */
	public static function get_settings_definition() {
		return [
			'db-auto-update'                => [
				'type'       => 'checkbox',
				'std'        => true,
				'exportable' => false,
			],
			'mega-menu'                     => [
				'type' => 'checkbox',
				'std'  => true,
			],
			'critical-alerts'               => [
				'type'       => 'checkbox',
				'std'        => false,
				'exportable' => false,
			],
			'web-fonts-display-swap'        => [
				'type'       => 'checkbox',
				'std'        => false,
				'exportable' => false,
			],
			'elementor-buttons-integration' => [
				'type' => 'checkbox',
				'std'  => true,
			],
			'disable-gutenberg-styles'      => [
				'type'       => 'checkbox',
				'std'        => false,
				'exportable' => false,
			],
			'critical-alerts-email'         => [
				'type'       => 'text',
				'std'        => '',
				'exportable' => false,
			],
			'fontawesome-4-compatibility'   => [
				'type'       => 'checkbox',
				'std'        => true,
				'exportable' => false,
			],
			'options-in-sidebar'            => [
				'type'       => 'checkbox',
				'std'        => false,
				'exportable' => false,
			],
			'rows'                          => [
				'type'       => 'checkbox',
				'std'        => false,
				'exportable' => false,
			],
			'overlapping-headers'           => [
				'type'       => 'checkbox',
				'std'        => false,
				'exportable' => false,
			],
			'portfolio-layout'              => [
				'type'       => 'checkbox',
				'std'        => false,
				'exportable' => false,
			],
			'admin-icons-bar'               => [
				'type'       => 'checkbox',
				'std'        => false,
				'exportable' => false,
			],
			'portfolio'                     => [
				'type' => 'checkbox',
				'std'  => true,
			],
			'portfolio-slug'                => [
				'type'       => 'text',
				'std'        => 'project',
				'exportable' => false,
			],
			'testimonials'                  => [
				'type' => 'checkbox',
				'std'  => true,
			],
			'team'                          => [
				'type' => 'checkbox',
				'std'  => true,
			],
			'team-slug'                     => [
				'type'       => 'text',
				'std'        => 'dt_team',
				'exportable' => false,
			],
			'logos'                         => [
				'type' => 'checkbox',
				'std'  => false,
			],
			'benefits'                      => [
				'type' => 'checkbox',
				'std'  => false,
			],
			'albums'                        => [
				'type' => 'checkbox',
				'std'  => true,
			],
			'albums-slug'                   => [
				'type'       => 'text',
				'std'        => 'dt_gallery',
				'exportable' => false,
			],
			'slideshow'                     => [
				'type' => 'checkbox',
				'std'  => true,
			],
		];
	}

	/**
	 * Setup settings, add hooks.
	 */
	public static function setup() {
		add_action( 'wp_ajax_the7_save_dashboard_settings', array( __CLASS__, 'save_via_ajax' ) );
		add_action( 'the7_dashboard_before_settings_save', array( __CLASS__, 'maybe_reset_notices' ), 10, 2 );

		self::setup_rewrite_filters();
	}

	/**
	 * Setup rewrite rules override and refresh is necessary.
	 */
	protected static function setup_rewrite_filters() {
		$post_types = array(
			array(
				'setting' => 'portfolio-slug',
				'name'    => 'dt_portfolio',
			),
			array(
				'setting' => 'albums-slug',
				'name'    => 'dt_gallery',
			),
			array(
				'setting' => 'team-slug',
				'name'    => 'dt_team',
			),
		);

		foreach ( $post_types as $post_type ) {
			$rewrite_data_provider = new Presscore_Post_Type_Rewrite_Rules_Option_DashboardSettings( $post_type['setting'] );
			$rewrite_filter        = new Presscore_Post_Type_Rewrite_Rules_Filter( $rewrite_data_provider );

			add_filter( "presscore_post_type_{$post_type['name']}_args", array( $rewrite_filter, 'filter_post_type_rewrite' ), 99 );
			add_action( 'the7_dashboard_before_settings_save', array( $rewrite_filter, 'flush_rules_after_slug_change' ) );
		}
	}

	/**
	 * Return all settings value.
	 *
	 * @return array
	 */
	public static function get_all() {
		return get_option( self::SETTINGS_ID, array() );
	}

	/**
	 * Return setting if it's set. Return $default value if it's not null, setting STD value otherwiste.
	 *
	 * @param  string $name  Setting name.
	 * @param  null   $default  Default.
	 *
	 * @return mixed
	 */
	public static function get( $name, $default = null ) {
		$settings_definition = self::get_settings_definition();

		if ( ! array_key_exists( $name, $settings_definition ) ) {
			return $default;
		}

		$settings = self::get_all();

		// Disable db auto update if cron is disabled.
		if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
			$settings['db-auto-update'] = false;
		}

		if ( array_key_exists( $name, $settings ) ) {
			return $settings[ $name ];
		}

		if ( $default === null ) {
			return $settings_definition[ $name ]['std'];
		}

		return $default;
	}

	/**
	 * Return true if setting exists, false otherwise.
	 *
	 * @param string $name Setting name.
	 *
	 * @return bool
	 */
	public static function setting_exists( $name ) {
		return array_key_exists( $name, self::get_all() );
	}

	/**
	 * Set setting.
	 *
	 * @param string $name Setting name.
	 * @param mixed  $value Setting value.
	 *
	 * @return bool
	 */
	public static function set( $name, $value ) {
		$settings = get_option( self::SETTINGS_ID, array() );

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		$settings[ $name ] = $value;

		return update_option( self::SETTINGS_ID, $settings );
	}

	/**
	 * Check existence of settings in db.
	 *
	 * @return bool
	 */
	public static function exists() {
		return ( false !== get_option( self::SETTINGS_ID ) );
	}

	/**
	 * Action. Reset corresponding admin notices on settings change.
	 *
	 * @param array $new_settings New settings.
	 * @param array $old_settings Old settings.
	 *
	 * @return void
	 */
	public static function maybe_reset_notices( $new_settings, $old_settings ) {
		$setting_related_notoces = array(
			'critical-alerts' => 'turn-on-critical-alerts',
		);
		foreach ( $setting_related_notoces as $setting => $notice_id ) {
			if ( ! isset( $new_settings[ $setting ], $old_settings[ $setting ] ) ) {
				continue;
			}

			if ( $new_settings[ $setting ] !== $old_settings[ $setting ] ) {
				the7_admin_notices()->reset( $notice_id );
			}
		}
	}

	/**
	 * Sanitize value according to type.
	 *
	 * @param mixed $value Any value.
	 * @param sting $type Can be `checkbox` or `text`.
	 *
	 * @return string|bool
	 */
	public static function sanitize_setting( $value, $type ) {
		if ( 'checkbox' === $type ) {
			return rest_sanitize_boolean( $value );
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Ajax callback.
	 */
	public static function save_via_ajax() {
		check_ajax_referer( self::SETTINGS_ID . '-save' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( __( 'Current user cannot modify legacy settings', 'the7mk2' ) );
		}

		$new_settings = array();
		if ( isset( $_POST[ self::SETTINGS_ID ] ) && is_array( $_POST[ self::SETTINGS_ID ] ) ) {
			$new_settings = wp_unslash( $_POST[ self::SETTINGS_ID ] );
		}

		$settings = array();
		foreach ( self::get_settings_definition() as $id => $_data ) {
			$new_setting_exists = array_key_exists( $id, $new_settings );

			if ( 'checkbox' === $_data['type'] ) {
				$settings[ $id ] = $new_setting_exists;
				continue;
			}

			if ( $new_setting_exists ) {
				$settings[ $id ] = self::sanitize_setting( $new_settings[ $id ], $_data['type'] );
			}
		}

		$old_settings = get_option( self::SETTINGS_ID, array() );
		do_action( 'the7_dashboard_before_settings_save', $settings, $old_settings );

		update_option( self::SETTINGS_ID, $settings );

		// Regenerate dynamic css after each save.
		presscore_set_force_regenerate_css( true );

		wp_send_json_success( $new_settings );
	}
}
