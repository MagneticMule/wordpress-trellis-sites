<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Demo_Content_TGMPA {

	/**
	 * array( 'slug' => 'name' )
	 * 
	 * @var array
	 */
	protected $inactive_plugins = [];

	/**
	 * array( 'slug' => 'name' )
	 *
	 * @var array
	 */
	protected $plugins_to_install = [];

	/**
	 * @var array
	 */
	protected $required_plugins = [];

	/**
	 * @var The7_TGM_Plugin_Activation
	 */
	protected $tgmpa;

	/**
	 * The7_Demo_Content_TGMPA constructor.
	 *
	 * @param array $required_plugins
	 */
	public function __construct( array $required_plugins ) {
		if ( empty( $GLOBALS['the7_tgmpa'] ) && class_exists( 'Presscore_Modules_TGMPAModule' ) ) {
			Presscore_Modules_TGMPAModule::init_the7_tgmpa();
			Presscore_Modules_TGMPAModule::register_plugins_action();
		}

		$this->tgmpa            = $GLOBALS['the7_tgmpa'];
		$this->required_plugins = $required_plugins;

		$this->check_required_plugins( $this->required_plugins );
	}

	/**
	 * @return array
	 */
	public function get_required_plugins_list() {
		$inactive_plugins   = $this->get_inactive_plugins();
		$plugins_to_install = $this->get_plugins_to_install();
		$required_plugins   = [];
		if ( ! $this->is_installed( 'pro-elements' ) ) {
			$required_plugins['pro-elements'] = esc_html(
				sprintf(
					_x(
						// translators: 1: elementor pro plugin name, 2: pro elements plugin name
						'%1$s or %2$s',
						'admin',
						'the7mk2'
					),
					'Elementor Pro (premium)',
					'PRO Elements (free)'
				)
			);
		}

		return array_merge( $required_plugins, $inactive_plugins, $plugins_to_install );
	}

	/**
	 * @param string $slug
	 *
	 * @return bool
	 */
	public function is_required( $slug ) {
		return in_array( $slug, $this->required_plugins, true );
	}

	/**
	 * @param string $slug
	 *
	 * @return bool
	 */
	public function is_installed( $slug ) {
		if ( ! $this->is_required( $slug ) ) {
			return true;
		}

		if ( $this->tgmpa->is_plugin_installed( $slug ) ) {
			return true;
		}

		$aliases = (array) $this->get_plugin_aliases( $slug );

		foreach ( $aliases as $alias ) {
			if ( $this->tgmpa->is_plugin_installed( $alias ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $slug
	 *
	 * @return bool
	 */
	public function is_active( $slug ) {
		if ( ! $this->is_required( $slug ) ) {
			return true;
		}

		if ( $this->tgmpa->is_plugin_active( $slug ) ) {
			return true;
		}

		$aliases = (array) $this->get_plugin_aliases( $slug );

		foreach ( $aliases as $alias ) {
			if ( $this->tgmpa->is_plugin_active( $alias ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return array
	 */
	public function get_inactive_plugins() {
		return array_filter( $this->inactive_plugins );
	}

	/**
	 * @return array
	 */
	public function get_plugins_to_install() {
		return array_filter( $this->plugins_to_install );
	}

	/**
	 * @return bool
	 */
	public function is_plugins_active() {
		return empty( $this->get_inactive_plugins() ) && empty( $this->get_plugins_to_install() );
	}

	/**
	 * @param string $slug
	 *
	 * @return array
	 */
	public function get_plugin_aliases( $slug ) {
		if ( isset( $this->tgmpa->plugins[ $slug ]['aliases'] ) ) {
			return (array) $this->tgmpa->plugins[ $slug ]['aliases'];
		}

		return [];
	}

	/**
	 * If all plugins installed and active - returns empty string. In other cases returns url to tgmpa plugins page.
	 * 
	 * @return string
	 */
	public function get_install_plugins_page_link() {
		if ( $this->tgmpa->is_tgmpa_complete() ) {
			return '';
		}

		return $this->tgmpa->get_bulk_action_link();
	}

	/**
	 * Returns $slug plugin name if it is registered, in other cases returns $slug.
	 * 
	 * @param  string $slug
	 * @return string
	 */
	public function get_plugin_name( $slug ) {
		if ( isset( $this->tgmpa->plugins[ $slug ] ) ) {
			return $this->tgmpa->plugins[ $slug ]['name'];
		}

		return $slug;
	}

	/**
	 * Returns false if any of $plugins is not active, in other cases returns true.
	 *
	 * @param  array   $plugins
	 */
	protected function check_required_plugins( $plugins = [] ) {
		if ( ! $plugins ) {
			return;
		}

		foreach ( $plugins as $slug ) {
			if ( $this->maybe_plugin_is_active_or_can_be_activated( $slug ) ) {
				continue;
			}

			$aliases = $this->get_plugin_aliases( $slug );
			foreach ( $aliases as $alias ) {
				if ( $this->maybe_plugin_is_active_or_can_be_activated( $slug ) ) {
					break;
				}
			}
		}
	}

	/**
	 * Populates $plugins_to_install and $inactive_plugins properties.
	 *
	 * @param string $slug
	 *
	 * @return bool
	 */
	protected function maybe_plugin_is_active_or_can_be_activated( $slug ) {
		if ( $this->tgmpa->is_plugin_active( $slug ) ) {
			return true;
		}

		if ( $this->tgmpa->is_plugin_installable( $slug ) ) {
			$this->plugins_to_install[ $slug ] = $this->get_plugin_name( $slug );

			return true;
		}

		if (
			$this->tgmpa->is_plugin_installed( $slug )
			&& ! $this->tgmpa->is_plugin_active( $slug )
		) {
			$this->inactive_plugins[ $slug ] = $this->get_plugin_name( $slug );

			return true;
		}

		return false;
	}

}
