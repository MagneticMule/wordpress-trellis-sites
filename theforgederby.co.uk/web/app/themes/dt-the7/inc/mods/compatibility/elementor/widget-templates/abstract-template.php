<?php
/**
 * Abstract widget template.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates;

use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Class Abstract_Template
 */
abstract class Abstract_Template {

	const CONTROL_TYPE_CONTROL = 'control';
	const CONTROL_TYPE_RESPONSIVE = 'responsive';
	const CONTROL_TYPE_GROUP = 'group';
	const CONTROL_TYPE_TABS = 'tabs';

	/**
	 * Widget object.
	 *
	 * @var Widget_Base
	 */
	public $widget;

	/**
	 * Template constructor.
	 *
	 * @param Widget_Base $widget Widget objects.
	 */
	public function __construct( $widget ) {
		$this->widget = $widget;
	}

	/**
	 * Proxy for `Elementor\Controls_Stack::get_settings_for_display()`.
	 *
	 * @param null|string $setting Find this setting.
	 *
	 * @return mixed
	 */
	public function get_settings( $setting = null ) {
		return $this->widget->get_settings_for_display( $setting );
	}

	/**
	 * Add widget controls based on $fields array.
	 *
	 * @param array $fields    Widget controls array.
	 * @param array $overrides Widget controls overrides.
	 */
	protected function setup_controls( array $fields, array $overrides = [] ) {
		foreach ( $fields as $id => $args ) {
			$control_type = isset( $args['control_type'] ) ? $args['control_type'] : 'control';
			unset( $args['control_type'] );

			if ( array_key_exists( $id, $overrides ) ) {

				// Allow to exclude controls.
				if ( $overrides[ $id ] === null ) {
					continue;
				}

				$args = array_merge( $args, $overrides[ $id ] );
			}

			if ( $control_type === self::CONTROL_TYPE_RESPONSIVE ) {
				$this->widget->add_basic_responsive_control( $id, $args );
			} elseif ( $control_type === self::CONTROL_TYPE_GROUP ) {
				$group_type = $args['type'];
				unset( $args['type'] );
				$this->widget->add_group_control( $group_type, $args );
			} elseif ( $control_type === self::CONTROL_TYPE_TABS ) {
				$this->widget->start_controls_tabs( $id );
				foreach ( $args['fields'] as $tab_id => $tab_args ) {

					// Allow to override tab.
					if ( array_key_exists( $tab_id, $overrides ) ) {
						$tab_args = array_merge( $tab_args, $overrides[ $tab_id ] );
					}

					$tab_fields = $tab_args['fields'];
					unset( $tab_args['fields'] );
					$this->widget->start_controls_tab( $tab_id, $tab_args );
					$this->setup_controls( $tab_fields );
					$this->widget->end_controls_tab();
				}
				$this->widget->end_controls_tabs();
			} else {
				$this->widget->add_control( $id, $args );
			}
		}
	}
}
