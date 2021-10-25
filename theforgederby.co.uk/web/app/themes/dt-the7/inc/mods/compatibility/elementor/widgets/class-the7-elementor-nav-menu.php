<?php
/**
 * The7 'Vertical Menu' widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * The7_Elementor_Nav_Menu class.
 */
class The7_Elementor_Nav_Menu extends The7_Elementor_Widget_Base {

	/**
	 * Get element name.
	 */
	public function get_name() {
		return 'the7_nav-menu';
	}

	/**
	 * Get element title.
	 */
	protected function the7_title() {
		return __( 'Vertical Menu', 'the7mk2' );
	}

	/**
	 * Get element icon.
	 */
	protected function the7_icon() {
		return 'eicon-nav-menu';
	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the element requires.
	 *
	 * @return array Element scripts dependencies.
	 */
	public function get_script_depends() {
		if ( $this->is_preview_mode() ) {
			return [ 'the7-vertical-menu-widget-preview' ];
		}

		return [];
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the element requires.
	 *
	 * @return array Element styles dependencies.
	 */
	public function get_style_depends() {
		return [ 'the7-vertical-menu-widget' ];
	}

	/**
	 * Get element keywords.
	 *
	 * @return string[] Element keywords.
	 */
	protected function the7_keywords() {
		return [ 'nav', 'menu' ];
	}

	/**
	 * Define what element data to export.
	 *
	 * @param array $element Element data.
	 *
	 * @return array Element data.
	 */
	public function on_export( $element ) {
		unset( $element['settings']['menu'] );

		return $element;
	}

	/**
	 * Get available menus list.
	 *
	 * @return array List of menus.
	 */
	private function get_available_menus() {
		$menus = wp_get_nav_menus();

		$options = [];

		foreach ( $menus as $menu ) {
			$options[ $menu->slug ] = $menu->name;
		}

		return $options;
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __( 'Layout', 'the7mk2' ),
			]
		);

		$menus = $this->get_available_menus();

		if ( ! empty( $menus ) ) {
			$this->add_control(
				'menu',
				[
					'label'          => __( 'Menu', 'the7mk2' ),
					'type'           => Controls_Manager::SELECT,
					'options'        => $menus,
					'default'        => array_keys( $menus )[0],
					'save_default'   => true,
					'description'    => sprintf(
						// translators: %s - edit menu admin page.
						__( 'Go to the <a href="%s" target="_blank">Menus screen</a> to manage your menus.', 'the7mk2' ),
						admin_url( 'nav-menus.php' )
					),
					'style_transfer' => true,
				]
			);
		} else {
			$this->add_control(
				'menu',
				[
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => '<strong>' . __( 'There are no menus in your site.', 'the7mk2' ) . '</strong><br>' . sprintf(
						// translators: %s - edit menu admin page.
						__( 'Go to the <a href="%s" target="_blank">Menus screen</a> to create one.', 'the7mk2' ),
						admin_url( 'nav-menus.php?action=edit&menu=0' )
					),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);
		}

		$this->add_control(
			'submenu_display',
			[
				'label'              => __( 'Display the submenu', 'the7mk2' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'on_click',
				'options'            => [
					'always'        => __( 'Always', 'the7mk2' ),
					'on_click'      => __( 'On icon click (parent clickable)', 'the7mk2' ),
					'on_item_click' => __( 'On item click (parent unclickable)', 'the7mk2' ),
				],
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout_icon',
			[
				'label'     => __( 'Submenu Indicator Icons', 'the7mk2' ),
				'condition' => [
					'submenu_display!' => 'always',
				],
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label'       => __( 'Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-caret-right',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'selected_active_icon',
			[
				'label'       => __( 'Active icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-caret-down',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
				'condition'   => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_main-menu',
			[
				'label' => __( 'Main Menu', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,

			]
		);

		$this->add_control(
			'list_heading',
			[
				'label' => __( 'List', 'the7mk2' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			]
		);

		$this->add_basic_responsive_control(
			'rows_gap',
			[
				'label'      => __( 'Rows Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '0',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-nav-menu > li:not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}); margin-bottom: 0;',
					'{{WRAPPER}}.widget-divider-yes .dt-nav-menu > li:first-child' => 'padding-top: calc({{SIZE}}{{UNIT}}/2);',

					'{{WRAPPER}}.widget-divider-yes .dt-nav-menu > li:last-child' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .dt-nav-menu' => ' --grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'divider',
			[
				'label'        => __( 'Dividers', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => __( 'Off', 'elementor' ),
				'label_on'     => __( 'On', 'elementor' ),
				'prefix_class' => 'widget-divider-',
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label'     => __( 'Style', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'solid'  => __( 'Solid', 'the7mk2' ),
					'double' => __( 'Double', 'the7mk2' ),
					'dotted' => __( 'Dotted', 'the7mk2' ),
					'dashed' => __( 'Dashed', 'the7mk2' ),
				],
				'default'   => 'solid',
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.widget-divider-yes .dt-nav-menu > li:after' => 'border-bottom-style: {{VALUE}}',
					'{{WRAPPER}}.widget-divider-yes .dt-nav-menu > li:first-child:before' => 'border-top-style: {{VALUE}};',
					'{{WRAPPER}} .first-item-border-hide .dt-nav-menu > li:first-child:before' => ' border-top-style: none;',
					'{{WRAPPER}}.widget-divider-yes .first-item-border-hide .dt-nav-menu > li:first-child' => 'padding-top: 0;',
					'{{WRAPPER}}.widget-divider-yes .last-item-border-hide .dt-nav-menu > li:last-child:after' => 'border-bottom-style: none;',
					'{{WRAPPER}}.widget-divider-yes .last-item-border-hide .dt-nav-menu > li:last-child' => 'padding-bottom: 0;',
				],
			]
		);

		$this->add_control(
			'divider_weight',
			[
				'label'     => __( 'Width', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.widget-divider-yes' => '--divider-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.widget-divider-yes .dt-nav-menu > li:after, {{WRAPPER}}.widget-divider-yes .dt-nav-menu > li:before' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'show_first_border',
			[
				'label'        => __( 'First Divider', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'condition'    => [
					'divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_last_border',
			[
				'label'        => __( 'Last Divider', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'items_heading',
			[
				'label'     => __( 'Item', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_basic_responsive_control(
			'align_items',
			[
				'label'                => __( 'Text alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'              => is_rtl() ? 'right' : 'left',
				'prefix_class'         => 'dt-nav-menu_align%s-',
				'selectors_dictionary' => [
					'left'   => 'justify-content: flex-start; align-items: flex-start; text-align: left;',
					'center' => 'justify-content: center; align-items: center; text-align: center;',
					'right'  => 'justify-content: flex-end;  align-items: flex-end; text-align: right',
				],
				'selectors'            => [
					'{{WRAPPER}} .dt-nav-menu > li > a' => ' {{VALUE}};',
					'{{WRAPPER}}.dt-nav-menu_align-center .dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'padding: 0 var(--icon-size);',
					'(desktop) {{WRAPPER}}.dt-nav-menu_align-left .dt-icon-position-left.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 0 0 var(--icon-spacing); padding: 0 0 0 var(--icon-size)',
					'(desktop) {{WRAPPER}}.dt-nav-menu_align-right .dt-icon-position-left.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 0 0 var(--icon-spacing); padding: 0 0 0 var(--icon-size)',

					'(desktop) {{WRAPPER}}.dt-nav-menu_align-left .dt-icon-position-right.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 var(--icon-spacing) 0 0; padding: 0 var(--icon-size) 0 0',
					'(desktop) {{WRAPPER}}.dt-nav-menu_align-right .dt-icon-position-right.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 var(--icon-spacing) 0 0; padding: 0 var(--icon-size) 0 0',

					'(tablet) {{WRAPPER}}.dt-nav-menu_align-tablet-left .dt-icon-position-left.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 0 0 var(--icon-spacing); padding: 0 0 0 var(--icon-size)',
					'(tablet) {{WRAPPER}}.dt-nav-menu_align-tablet-right .dt-icon-position-left.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 0 0 var(--icon-spacing); padding: 0 0 0 var(--icon-size)',

					'(tablet) {{WRAPPER}}.dt-nav-menu_align-tablet-left .dt-icon-position-right.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 var(--icon-spacing) 0 0; padding: 0 var(--icon-size) 0 0',
					'(tablet) {{WRAPPER}}.dt-nav-menu_align-tablet-right .dt-icon-position-right.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 var(--icon-spacing) 0 0; padding: 0 var(--icon-size) 0 0',

					'(tablet) {{WRAPPER}}.dt-nav-menu_align-tablet-center .dt-icon-align-side .dt-nav-menu > li > a .item-content ' => 'margin: 0 var(--icon-spacing); padding: 0 var(--icon-size)',

					'(mobile) {{WRAPPER}}.dt-nav-menu_align-mobile-left .dt-icon-position-left.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 0 0 var(--icon-spacing); padding: 0 0 0 var(--icon-size)',
					'(mobile) {{WRAPPER}}.dt-nav-menu_align-mobile-right .dt-icon-position-left.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 0 0 var(--icon-spacing); padding: 0 0 0 var(--icon-size)',

					'(mobile) {{WRAPPER}}.dt-nav-menu_align-mobile-left .dt-icon-position-right.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 var(--icon-spacing) 0 0; padding: 0 var(--icon-size) 0 0',
					'(mobile) {{WRAPPER}}.dt-nav-menu_align-mobile-right .dt-icon-position-right.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 var(--icon-spacing) 0 0; padding: 0 var(--icon-size) 0 0',

					'(mobile) {{WRAPPER}}.dt-nav-menu_align-mobile-center .dt-icon-align-side.dt-icon-position-right .dt-nav-menu > li > a .item-content ' => 'margin: 0 var(--icon-spacing); padding: 0 var(--icon-size)',
					'(mobile) {{WRAPPER}}.dt-nav-menu_align-mobile-center .dt-icon-align-side.dt-icon-position-left .dt-nav-menu > li > a .item-content ' => 'margin: 0 var(--icon-spacing); padding: 0 var(--icon-size)',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'menu_typography',
				'scheme'    => Schemes\Typography::TYPOGRAPHY_1,
				'separator' => 'before',
				'selector'  => ' {{WRAPPER}} .dt-nav-menu > li > a',
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'     => __( 'Indicator Position', 'the7mk2' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'  => [
						'title' => __( 'Start', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'End', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'   => is_rtl() ? 'left' : 'right',
				'toggle'    => false,
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_alignment',
			[
				'label'                => __( 'Indicator Align', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => [
					'with_text' => __( 'With text', 'the7mk2' ),
					'side'      => __( 'Side', 'the7mk2' ),
				],
				'default'              => 'with_text',
				'selectors_dictionary' => [
					'with_text' => '',
					'side'      => 'justify-content: space-between;',
				],
				'condition'            => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_basic_responsive_control(
			'icon_size',
			[
				'label'      => __( 'Indicator size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'vw' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-nav-menu' => '--icon-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .dt-nav-menu > li > a .next-level-button i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-nav-menu > li > a .next-level-button, {{WRAPPER}} .dt-nav-menu > li > a .next-level-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_basic_responsive_control(
			'icon_space',
			[
				'label'     => __( 'Indicator Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu' => '--icon-spacing: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .dt-icon-position-left .dt-nav-menu > li > a .next-level-button' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-icon-position-right .dt-nav-menu > li > a  .next-level-button' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-icon-position-left.dt-icon-align-side .dt-nav-menu > li > a .item-content ' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-icon-position-right.dt-icon-align-side .dt-nav-menu > li > a .item-content ' => 'margin-right: {{SIZE}}{{UNIT}};',
					'(desktop) {{WRAPPER}}.dt-nav-menu_align-center .dt-icon-align-side .dt-nav-menu > li > a  .item-content ' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_menu_item_style' );

		$this->start_controls_tab(
			'tab_menu_item_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_menu_item',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_3,
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu > li > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => __( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu > li > a .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .dt-nav-menu > li > a svg'                => 'fill: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'bg_menu_item',
			[
				'label'     => __( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu > li > a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_menu_item',
			[
				'label'     => __( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu > li > a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_menu_item_hover',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_4,
				],
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu > li > a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_hover_color',
			[
				'label'     => __( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-sub-menu-display-on_click .dt-nav-menu > li > a .next-level-button:hover ' => 'color: {{VALUE}};',
					'
					{{WRAPPER}} .dt-sub-menu-display-on_item_click .dt-nav-menu > li > a:hover .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .dt-sub-menu-display-on_click .dt-nav-menu > li > a svg:hover, {{WRAPPER}} .dt-sub-menu-display-on_item_click .dt-nav-menu > li > a:hover svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'bg_menu_item_hover',
			[
				'label'     => __( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu > li > a:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_menu_item_hover',
			[
				'label'     => __( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu > li > a:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_active',
			[
				'label' => __( 'Active', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_menu_item_active',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu > li > a.active-item' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_active_color',
			[
				'label'     => __( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}  .dt-nav-menu > li > .active-item .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .dt-nav-menu > li > .active-item svg'                 => 'fill: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'bg_menu_item_active',
			[
				'label'     => __( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu > li > a.active-item' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_menu_item_active',
			[
				'label'     => __( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu > li > a.active-item' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		/* This control is required to handle with complicated conditions */
		$this->add_control(
			'hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_basic_responsive_control(
			'border_menu_item_width',
			[
				'label'      => __( 'Border width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-nav-menu > li > a' => 'border-top-width: {{TOP}}{{UNIT}};
					border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'padding_menu_item',
			[
				'label'      => __( 'Item paddings', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-nav-menu > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .dt-icon-position-left.dt-icon-align-side .dt-nav-menu > li > a .next-level-button ' => 'left: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .dt-icon-position-right.dt-icon-align-side .dt-nav-menu > li > a .next-level-button ' => 'right: {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'menu_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .dt-nav-menu > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_sub-menu',
			[
				'label' => __( 'Sub Menu', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'sub_list_heading',
			[
				'label' => __( 'List', 'the7mk2' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			]
		);

		$this->add_basic_responsive_control(
			'padding_sub_menu',
			[
				'label'      => __( '2 menu level Paddings', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-nav-menu > li > .vertical-sub-nav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'padding_sub_sub_menu',
			[
				'label'      => __( '3+ menu level Paddings', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .vertical-sub-nav .vertical-sub-nav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'sub_rows_gap',
			[
				'label'      => __( 'Rows Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '0',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .vertical-sub-nav > li:not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}); margin-bottom: 0; --sub-grid-row-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.sub-widget-divider-yes .vertical-sub-nav > li:first-child' => 'padding-top: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .vertical-sub-nav .vertical-sub-nav > li:first-child' => 'margin-top: calc({{SIZE}}{{UNIT}}/2); padding-top: calc({{SIZE}}{{UNIT}}/2);',

					'{{WRAPPER}} .first-sub-item-border-hide .dt-nav-menu > li > .vertical-sub-nav > li:first-child' => 'padding-top: 0;',

					'{{WRAPPER}}.sub-widget-divider-yes .vertical-sub-nav > li:last-child' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .vertical-sub-nav .vertical-sub-nav > li:last-child' => 'margin-bottom: calc({{SIZE}}{{UNIT}}/2); padding-bottom: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}}.sub-widget-divider-yes .last-sub-item-border-hide .dt-nav-menu > li > .vertical-sub-nav > li:last-child' => 'padding-bottom: 0;',
					'{{WRAPPER}} .dt-nav-menu > li > .vertical-sub-nav .vertical-sub-nav' => 'margin-bottom: calc(-{{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'sub_divider',
			[
				'label'        => __( 'Dividers', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => __( 'Off', 'elementor' ),
				'label_on'     => __( 'On', 'elementor' ),
				'prefix_class' => 'sub-widget-divider-',
			]
		);

		$this->add_control(
			'sub_divider_style',
			[
				'label'     => __( 'Style', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'solid'  => __( 'Solid', 'the7mk2' ),
					'double' => __( 'Double', 'the7mk2' ),
					'dotted' => __( 'Dotted', 'the7mk2' ),
					'dashed' => __( 'Dashed', 'the7mk2' ),
				],
				'default'   => 'solid',
				'condition' => [
					'sub_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.sub-widget-divider-yes .vertical-sub-nav > li:after' => 'border-bottom-style: {{VALUE}}',
					'{{WRAPPER}}.sub-widget-divider-yes .vertical-sub-nav > li:first-child:before' => 'border-top-style: {{VALUE}};',

					'{{WRAPPER}} .first-sub-item-border-hide .dt-nav-menu > li > .vertical-sub-nav > li:first-child:before' => ' border-top-style: none;',

					'{{WRAPPER}} .last-sub-item-border-hide .vertical-sub-nav > li:last-child:after, {{WRAPPER}} .vertical-sub-nav .vertical-sub-nav > li:last-child:after' => ' border-bottom-style: none;',
				],

			]
		);

		$this->add_control(
			'sub_divider_weight',
			[
				'label'     => __( 'Width', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'condition' => [
					'sub_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.sub-widget-divider-yes' => '--divider-sub-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'sub_divider_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'sub_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.sub-widget-divider-yes .vertical-sub-nav > li:after, {{WRAPPER}}.sub-widget-divider-yes .vertical-sub-nav > li:before' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'show_sub_first_border',
			[
				'label'        => __( 'First Divider', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'condition'    => [
					'sub_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_sub_last_border',
			[
				'label'        => __( 'Last Divider', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'sub_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'sub_item_heading',
			[
				'label'     => __( 'Item', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_basic_responsive_control(
			'align_sub_items',
			[
				'label'                => __( 'Text alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'              => is_rtl() ? 'right' : 'left',
				'prefix_class'         => 'dt-sub-menu_align%s-',
				'selectors_dictionary' => [
					'left'   => 'justify-content: flex-start; align-items: flex-start; text-align: left;',
					'center' => 'justify-content: center; align-items: center; text-align: center;',
					'right'  => 'justify-content: flex-end;  align-items: flex-end; text-align: right',
				],
				'selectors'            => [
					'{{WRAPPER}} .vertical-sub-nav > li a' => ' {{VALUE}};',

					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-left .dt-sub-icon-position-left.dt-sub-icon-align-side .vertical-sub-nav > li .item-content' => 'margin: 0 0 0 var(--sub-icon-spacing); padding: 0 0 0 var(--sub-icon-size)',
					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-right .dt-sub-icon-position-left.dt-sub-icon-align-side .vertical-sub-nav > li .item-content' => 'margin: 0 0 0 var(--sub-icon-spacing); padding: 0 0 0 var(--sub-icon-size)',

					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-left .dt-sub-icon-position-right.dt-sub-icon-align-side .vertical-sub-nav > li .item-content' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',
					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-right .dt-sub-icon-position-right.dt-sub-icon-align-side .vertical-sub-nav > li .item-content' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',

					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-center .dt-sub-icon-align-side .vertical-sub-nav > li .item-content ' => 'margin: 0 var(--icon-spacing); padding: 0 var(--sub-icon-size)',

					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-left .dt-sub-icon-position-left.dt-sub-icon-align-side .vertical-sub-nav > li .item-content' => 'margin: 0 0 0 var(--sub-icon-spacing); padding: 0 0 0 var(--sub-icon-size)',
					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-right .dt-sub-icon-position-left.dt-sub-icon-align-side .vertical-sub-nav > li .item-content' => 'margin: 0 0 0 var(--sub-icon-spacing); padding: 0 0 0 var(--sub-icon-size)',

					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-left .dt-sub-icon-position-right.dt-sub-icon-align-side .vertical-sub-nav > li .item-content' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',
					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-right .dt-sub-icon-position-right.dt-sub-icon-align-side .vertical-sub-nav > li .item-content' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',

					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-center .dt-sub-icon-align-side.dt-sub-icon-position-right .vertical-sub-nav > li .item-content ' => 'margin: 0 var(--sub-icon-spacing); padding: 0 var(--sub-icon-size)',
					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-center .dt-sub-icon-align-side.dt-sub-icon-position-left .vertical-sub-nav > li .item-content ' => 'margin: 0 var(--sub-icon-spacing); padding: 0 var(--sub-icon-size)',
				],
			]
		);

		$this->add_control(
			'sub_icon_align',
			[
				'label'     => __( 'Indicator Position', 'the7mk2' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'  => [
						'title' => __( 'Start', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'End', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
				'default'   => is_rtl() ? 'left' : 'right',
				'toggle'    => false,
			]
		);

		$this->add_control(
			'sub_icon_alignment',
			[
				'label'                => __( 'Indicator Align', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => [
					'with_text' => __( 'With text', 'the7mk2' ),
					'side'      => __( 'Side', 'the7mk2' ),
				],
				'default'              => 'with_text',
				'selectors_dictionary' => [
					'with_text' => '',
					'side'      => 'justify-content: space-between;',
				],
				'condition'            => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_basic_responsive_control(
			'sub_icon_size',
			[
				'label'      => __( 'Indicator size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'vw' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .vertical-sub-nav' => '--sub-icon-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .vertical-sub-nav > li > a .next-level-button i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .vertical-sub-nav > li > a .next-level-button, {{WRAPPER}} .vertical-sub-nav > li > a .next-level-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_basic_responsive_control(
			'sub_icon_space',
			[
				'label'     => __( 'Indicator Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .vertical-sub-nav' => '--sub-icon-spacing: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .dt-sub-icon-position-left .vertical-sub-nav > li > a .next-level-button' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-sub-icon-position-right .vertical-sub-nav > li > a  .next-level-button' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-sub-icon-position-left.dt-sub-icon-align-side .vertical-sub-nav > li > a .item-content ' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-sub-icon-position-right.dt-sub-icon-align-side .dt-nav-menu > li > a .item-content ' => 'margin-right: {{SIZE}}{{UNIT}};',
					'(desktop) {{WRAPPER}}.dt-sub-menu_align-center .dt-sub-icon-align-side .vertical-sub-nav > li > a  .item-content ' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'sub_menu_typography',
				'scheme'    => Schemes\Typography::TYPOGRAPHY_1,
				'selector'  => '{{WRAPPER}} .vertical-sub-nav > li, {{WRAPPER}} .vertical-sub-nav > li a',
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_sub_menu_item_style' );

		$this->start_controls_tab(
			'tab_sub_menu_item_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_sub_menu_item',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_3,
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .vertical-sub-nav li a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'sub_menu_icon_color',
			[
				'label'     => __( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .vertical-sub-nav > li > a .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .vertical-sub-nav > li > a svg'                => 'fill: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'bg_sub_menu_item',
			[
				'label'     => __( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .vertical-sub-nav a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_sub_menu_item',
			[
				'label'     => __( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .vertical-sub-nav a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_sub_menu_item_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_sub_menu_item_hover',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_4,
				],
				'selectors' => [
					'{{WRAPPER}} .vertical-sub-nav li a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'sub_menu_icon_hover_color',
			[
				'label'     => __( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-sub-menu-display-on_click .vertical-sub-nav > li > a .next-level-button:hover, {{WRAPPER}} .dt-sub-menu-display-on_item_click .vertical-sub-nav > li > a:hover .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .dt-sub-menu-display-on_click .vertical-sub-nav > li > a svg:hover,  {{WRAPPER}} .dt-sub-menu-display-on_item_click .vertical-sub-nav > li > a:hover svg'                                    => 'fill: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'bg_sub_menu_item_hover',
			[
				'label'     => __( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .vertical-sub-nav li a:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_sub_menu_item_hover',
			[
				'label'     => __( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .vertical-sub-nav li a:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_sub_menu_item_active',
			[
				'label' => __( 'Active', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_sub_menu_item_active',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .vertical-sub-nav li > a.active-item' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'sub_menu_icon_active_color',
			[
				'label'     => __( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .vertical-sub-nav a.active-item .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .vertical-sub-nav a.active-item svg'                => 'fill: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'bg_sub_menu_item_active',
			[
				'label'     => __( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .vertical-sub-nav li > a.active-item' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_sub_menu_item_active',
			[
				'label'     => __( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .vertical-sub-nav li > a.active-item' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		/* This control is required to handle with complicated conditions */
		$this->add_control(
			'sub_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_basic_responsive_control(
			'border_sub_menu_item_width',
			[
				'label'      => __( 'Border width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .vertical-sub-nav li a' => 'border-top-width: {{TOP}}{{UNIT}};
					border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'padding_sub_menu_item',
			[
				'label'      => __( 'Item paddings', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .vertical-sub-nav li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .dt-sub-icon-position-left.dt-sub-icon-align-side .vertical-sub-nav li a .next-level-button ' => 'left: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .dt-sub-icon-position-right.dt-sub-icon-align-side .vertical-sub-nav li a .next-level-button ' => 'right: {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'menu_sub_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .vertical-sub-nav li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render element.
	 *
	 * Generates the final HTML on the frontend.
	 */
	protected function render() {
		if ( ! $this->get_available_menus() ) {
			return;
		}

		$settings = $this->get_active_settings();

		$this->add_render_attribute(
			[
				'main-menu' => [
					'role' => 'navigation',
				],
			]
		);

		$class     = [
			'dt-nav-menu--main',
			'dt-nav-menu__container',
			'dt-sub-menu-display-' . $settings['submenu_display'],
			'dt-icon-align-' . $settings['icon_alignment'],
			'dt-icon-position-' . $settings['icon_align'],
			'dt-sub-icon-position-' . $settings['sub_icon_align'],
			'dt-sub-icon-align-' . $settings['sub_icon_alignment'],
		];
		$switchers = [
			'show_first_border'     => 'first-item-border-hide',
			'show_last_border'      => 'last-item-border-hide',
			'show_sub_first_border' => 'first-sub-item-border-hide',
			'show_sub_last_border'  => 'last-sub-item-border-hide',

		];
		foreach ( $switchers as $control => $class_to_add ) {
			if ( isset( $settings[ $control ] ) && $settings[ $control ] !== 'y' ) {
				$class[] = $class_to_add;
			}
		}
		$this->add_render_attribute( 'main-menu', 'class', $class );

		$sub_menu_act_icon = '';
		if ( $settings['selected_active_icon'] ) {
			$sub_menu_act_icon = $this->get_elementor_icon_html(
				$settings['selected_active_icon'],
				'i',
				[
					'class' => 'icon-active',
				]
			);
		}

		$sub_menu_icon = '';
		if ( $settings['selected_icon'] ) {
			$sub_menu_icon = $this->get_elementor_icon_html(
				$settings['selected_icon'],
				'i',
				[
					'class' => 'open-button',
				]
			);
		}

		if ( $settings['selected_icon'] && $settings['selected_icon']['value'] === '' ) {
			$this->add_render_attribute( 'main-menu', 'class', 'indicator-off' );
		}

		$link_after = sprintf(
			'</span><span class="%s" data-icon = "%s">%s %s</span>',
			esc_attr( $settings['icon_align'] ? $settings['icon_align'] . ' next-level-button' : '' ),
			esc_attr( ! empty( $settings['selected_active_icon']['value'] ) && is_string( $settings['selected_active_icon']['value'] ) ? $settings['selected_active_icon']['value'] : '' ),
			$sub_menu_icon,
			$sub_menu_act_icon
		);

		do_action( 'presscore_primary_nav_menu_before' );

		presscore_nav_menu(
			[
				'menu'                => $settings['menu'],
				'theme_location'      => 'the7_nav-menu',
				'items_wrap'          => '<nav ' . $this->get_render_attribute_string( 'main-menu' ) . '><ul class="dt-nav-menu">%3$s</ul></nav>',
				'submenu_class'       => implode( ' ', presscore_get_primary_submenu_class( 'vertical-sub-nav' ) ),
				'link_before'         => '<span class="item-content">',
				'link_after'          => $link_after,
				'parent_is_clickable' => $settings['submenu_display'] !== 'on_item_click',
			]
		);

		do_action( 'presscore_primary_nav_menu_after' );
	}

	/**
	 * Render widget plain content.
	 *
	 * No plain content here.
	 */
	public function render_plain_content() {
	}
}
