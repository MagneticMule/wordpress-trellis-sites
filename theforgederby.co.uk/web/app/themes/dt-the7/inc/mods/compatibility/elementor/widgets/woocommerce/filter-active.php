<?php
/*
 * The7 elements product add to cart widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Woocommerce\WC_Widget_Nav;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use WC_Query;

defined( 'ABSPATH' ) || exit;

class Filter_Active extends The7_Elementor_Widget_Base {

	public function get_name() {
		return 'the7-woocommerce-filter-active';
	}

	public function get_categories() {
		return [ 'woocommerce-elements-single', 'woocommerce-elements-archive' ];
	}

	public function get_style_depends() {
		return $this->getDepends();
	}

	private function getDepends() {
		//css and js use the same names
		return [ 'the7-woocommerce-filter-attribute' ];
	}

	public function get_script_depends() {
		the7_register_script( 'the7-woocommerce-filter-attribute', PRESSCORE_THEME_URI . '/js/compatibility/elementor/woocommerce-filter-attribute', [ 'jquery' ], THE7_VERSION, true );

		return $this->getDepends();
	}

	protected function the7_title() {
		return __( 'Active Filter', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-table-of-contents';
	}

	protected function the7_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'cart', 'product', 'filter', 'active' ];
	}

	protected function _register_controls() {
		//Content Tab
		$this->add_title_area_content_controls();
		$this->add_content_content_controls();
		//Styles Tab

		$this->add_title_styles_controls();
		$this->add_filter_indicator_styles_controls();
		$this->add_box_styles_controls( 'active' ,  __( 'Active Attributes Box', 'the7mk2' ) );
		$this->add_box_styles_controls( 'clear_all', __( 'Clear All Box', 'the7mk2' ) );
	}

	protected function add_title_area_content_controls() {
		$this->start_controls_section( 'title_area_section', [
			'label' => __( 'Title Area', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'title', [
			'label'        => __( 'Title', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'On', 'the7mk2' ),
			'label_off'    => __( 'Off', 'the7mk2' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'separator'    => 'before',
		] );

		$this->add_control( 'title_text', [
			'label'   => __( 'Widget Title', 'the7mk2' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Active filters', 'the7mk2' ),
			'condition'    => [
				'title!' => '',
			],
		] );

		$this->add_control( 'toggle', [
			'label'        => __( 'Widget Toggle', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'On', 'the7mk2' ),
			'label_off'    => __( 'Off', 'the7mk2' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'separator'    => 'before',
			'condition'    => [
				'title!' => '',
			],
		] );

		$this->add_control( 'toggle_closed_by_default', [
			'label'        => __( 'Closed By Default', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Yes', 'the7mk2' ),
			'label_off'    => __( 'No', 'the7mk2' ),
			'return_value' => 'closed',
			'default'      => '',
			'condition'    => [
				'toggle!' => '',
				'title!' => '',
			],
		] );

		$this->add_control( 'toggle_icon', [
			'label'            => __( 'Icon', 'the7mk2' ),
			'type'             => Controls_Manager::ICONS,
			'fa4compatibility' => 'icon',
			'default'          => [
				'value'   => 'fas fa-chevron-down',
				'library' => 'fa-solid',
			],
			'recommended'      => [
				'fa-solid'   => [
					'chevron-down',
					'angle-down',
					'angle-double-down',
					'caret-down',
					'caret-square-down',
				],
				'fa-regular' => [
					'caret-square-down',
				],
			],
			'label_block'      => false,
			'skin'             => 'inline',
			'condition'        => [
				'toggle!' => '',
				'title!' => '',
			],
		] );

		$this->add_control( 'toggle_active_icon', [
			'label'            => __( 'Active Icon', 'the7mk2' ),
			'type'             => Controls_Manager::ICONS,
			'fa4compatibility' => 'icon_active',
			'default'          => [
				'value'   => 'fas fa-chevron-up',
				'library' => 'fa-solid',
			],
			'recommended'      => [
				'fa-solid'   => [
					'chevron-up',
					'angle-up',
					'angle-double-up',
					'caret-up',
					'caret-square-up',
				],
				'fa-regular' => [
					'caret-square-up',
				],
			],
			'skin'             => 'inline',
			'label_block'      => false,
			'condition'        => [
				'toggle!'             => '',
				'toggle_icon[value]!' => '',
				'title!' => '',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_content_content_controls() {
		$this->start_controls_section( 'content_section', [
			'label' => __( 'Content', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'indicators_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Indicators', 'the7mk2' ),
		] );

		$this->add_control( 'active_filter_indicator', [
			'label'        => __( 'Active Attributes Indicator', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Yes', 'the7mk2' ),
			'label_off'    => __( 'No', 'the7mk2' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->add_control( 'clear_all_filter_indicator', [
			'label'        => __( '"Clear All" indicator', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Yes', 'the7mk2' ),
			'label_off'    => __( 'No', 'the7mk2' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'condition'    => [
				'clear_all' => 'yes',
			],
		] );


		$this->add_control( 'layout', [
			'label'                => __( 'Layout', 'the7mk2' ),
			'type'                 => Controls_Manager::SELECT,
			'options'              => [
				'grid'   => __( 'Grid', 'the7mk2' ),
				'inline' => __( 'Inline', 'the7mk2' ),
			],
			'separator'            => 'before',
			'default'              => 'grid',
			'prefix_class'         => 'filter-layout-',
			'selectors'            => [
				'{{WRAPPER}} .filter-nav' => '{{VALUE}}',
			],
			'selectors_dictionary' => [
				'grid'   => 'display: grid',
				'inline' => 'display: flex; flex-wrap: wrap; align-items: center;',
			],
		] );

		$this->add_basic_responsive_control(
			'inline_alignment',
			[
				'label' => __( 'Alignment', 'the7mk2' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'condition'      => [
					'layout' => 'inline',
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .filter-nav' => 'justify-content: {{VALUE}};',
				],
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right' => 'flex-end',
				],
			]
		);

		$this->add_basic_responsive_control( 'grid_columns', [
			'label'          => __( 'Number Of Columns', 'the7mk2' ),
			'type'           => Controls_Manager::NUMBER,
			'default'        => 1,
			'mobile_default' => 1,
			'min'            => 1,
			'max'            => 6,
			'condition'      => [
				'layout' => 'grid',
			],
			'selectors'      => [
				'{{WRAPPER}} .filter-nav' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
			],
		] );


		$this->add_basic_responsive_control( 'box_margin', [
			'label'      => __( 'Margins', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 50,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'default'    => [
				'top'      => '0',
				'right'    => '10',
				'bottom'   => '10',
				'left'     => '0',
				'unit'     => 'px',
				'isLinked' => true,
			],
			'selectors'  => [
				'{{WRAPPER}} .filter-nav-item-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
			'condition'  => [
				'layout!' => 'grid',
			],
		] );

		$this->add_basic_responsive_control( 'box_row_space', [
			'label'     => __( 'Row Gap', 'the7mk2' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'default'   => [
				'size' => 10,
			],
			'selectors' => [
				'{{WRAPPER}}  .filter-nav' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
			],
			'condition' => [
				'layout' => 'grid',
			],
		] );

		$this->add_basic_responsive_control( 'box_column_space', [
			'label'     => __( 'Column Gap', 'the7mk2' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors' => [
				'{{WRAPPER}}  .filter-nav' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
			],
			'default'   => [
				'size' => 10,
			],
			'condition' => [
				'layout' => 'grid',
			],
		] );

		$this->add_control( 'clear_all_heading', [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Clear All', 'the7mk2' ),
			'separator' => 'before',
		] );

		$this->add_control( 'clear_all', [
			'label'        => __( 'Clear All', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Yes', 'the7mk2' ),
			'label_off'    => __( 'No', 'the7mk2' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->add_control( 'clear_all_text', [
			'label'     => __( 'Button Name', 'the7mk2' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => __( 'Clear all', 'the7mk2' ),
			'condition' => [
				'clear_all' => 'yes',
			],
		] );

		$this->add_control( 'clear_all_position', [
			'label'     => __( 'Position', 'the7mk2' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'after',
			'options'   => [
				'before' => __( 'Before', 'the7mk2' ),
				'after'  => __( 'After', 'the7mk2' ),
			],
			'condition' => [
				'clear_all' => 'yes',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_title_styles_controls() {
		$this->start_controls_section( 'title_section', [
			'label' => __( 'Title Area', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'condition'    => [
				'title!' => '',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'selector' => '{{WRAPPER}} .filter-title',
		] );

		$this->add_control( 'title_color', [
			'label'     => __( 'Title Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'alpha'     => true,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .filter-title' => 'color: {{VALUE}};',
			],
			'separator' => 'after',
		] );

		$this->add_basic_responsive_control( 'title_arrow_size', [
			'label'     => __( 'Toggle Icon Size', 'the7mk2' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'default'   => [
				'size' => 16,
			],
			'condition' => [
				'toggle!' => '',
			],
			'selectors' => [
				'{{WRAPPER}} .filter-toggle-icon .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->start_controls_tabs( 'title_arrow_tabs_style', [
			'condition' => [
				'toggle!'             => '',
				'toggle_icon[value]!' => '',
			],
		] );

		$this->start_controls_tab( 'normal_title_arrow_style', [
			'label' => __( 'Closed', 'the7mk2' ),
		] );

		$this->add_control( 'title_arrow_color', [
			'label'     => __( 'Icon Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-closed i'   => 'color: {{VALUE}};',
				'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-closed svg' => 'fill: {{VALUE}};',
			],
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover_title_arrow_style', [
			'label' => __( 'Hover', 'the7mk2' ),
		] );

		$this->add_control( 'hover_title_arrow_color', [
			'label'     => __( 'Icon Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .filter-header:hover .filter-toggle-icon .elementor-icon i'   => 'color: {{VALUE}};',
				'{{WRAPPER}} .filter-header:hover .filter-toggle-icon .elementor-icon svg' => 'fill: {{VALUE}};',
			],
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( 'active_title_arrow_style', [
			'label' => __( 'Active', 'the7mk2' ),
		] );

		$this->add_control( 'active_title_arrow_color', [
			'label'     => __( 'Icon Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-active i'   => 'color: {{VALUE}};',
				'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-active svg' => 'fill: {{VALUE}};',
			],
		] );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_basic_responsive_control( 'title_space', [
			'label'     => __( 'Gap', 'the7mk2' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'default'   => [
				'size' => 15,
			],
			'selectors' => [
				'{{WRAPPER}} .filter-container' => 'margin-top: {{SIZE}}{{UNIT}};',
			],
			'separator' => 'before',
		] );

		$this->end_controls_section();
	}

	protected function add_filter_indicator_styles_controls() {
		$this->start_controls_section( 'filter_indicator_section', [
			'label' => __( 'Filter Indicator', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'conditions' => [
				'relation' => 'or',
				'terms' => [
					[
						'name' => 'active_filter_indicator',
						'value' => 'yes',
					],
					[
						'name' => 'clear_all_filter_indicator',
						'value' => 'yes',
					],
				],
			],
		] );

		$icon_selector = '{{WRAPPER}} .filter-nav-item-container .indicator';

		$this->add_control( 'filter_indicator_align', [
			'label'                => __( 'Alignment', 'the7mk2' ),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => [
				'left'  => [
					'title' => __( 'Start', 'the7mk2' ),
					'icon'  => 'eicon-h-align-left',
				],
				'right' => [
					'title' => __( 'End', 'the7mk2' ),
					'icon'  => 'eicon-h-align-right',
				],
			],
			'default'              => is_rtl() ? 'right' : 'left',
			'toggle'               => false,
			'selectors'            => [
				'{{WRAPPER}} .filter-nav-item-container .indicator' => 'order: {{VALUE}}',
			],
			'selectors_dictionary' => [
				'left'  => 0,
				'right' => 1,
			],
			'prefix_class'         => 'filter-indicator-align-',
		] );

		$this->add_basic_responsive_control( 'filter_indicator_icon_size', [
			'label'     => __( 'Icon Size', 'the7mk2' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors' => [
				$icon_selector . ' .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'filter_indicator_padding', [
			'label'      => __( 'Icon Padding', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 50,
				],
			],
			'selectors'  => [
				$icon_selector => 'padding: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_control( 'filter_indicator_border_width', [
			'label'      => __( 'Border Width', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 25,
				],
			],
			'selectors'  => [
				$icon_selector => 'border-width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'filter_indicator_border_radius', [
			'label'      => __( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				$icon_selector => 'border-radius: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_basic_responsive_control( 'filter_indicator_space', [
			'label'     => __( 'Gap', 'the7mk2' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors' => [
				'{{WRAPPER}}.filter-indicator-align-left  .filter-nav-item-container .indicator' => 'margin-right: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}}.filter-indicator-align-right .filter-nav-item-container .indicator' => 'margin-left: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'active_indicator_heading', [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Active Attributes Indicator', 'the7mk2' ),
			'separator' => 'before',
			'condition' => [
				'active_filter_indicator' => 'yes',
			],
		] );

		$this->add_filter_indicator_tabs_controls( 'active' );

		$this->add_control( 'clear_all_indicator_heading', [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Clear All', 'the7mk2' ),
			'separator' => 'before',
			'condition' => [
				'clear_all_filter_indicator' => 'yes',
			],
		] );

		$this->add_filter_indicator_tabs_controls( 'clear_all' );

		$this->end_controls_section();
	}

	protected function add_filter_indicator_tabs_controls( $prefix ) {
		$active_class = '.active';
		if ( $prefix === 'clear_all' ) {
			$active_class = '.clear-all';
		}

		$selector = '{{WRAPPER}} .filter-nav-item' . $active_class . ' .filter-nav-item-container .indicator';


		$this->start_controls_tabs( $prefix . '_indicator_tabs' , [
			'condition' => [
				$prefix . '_filter_indicator' => 'yes',
			],
        ]);

		$this->start_controls_tab( $prefix . '_filter_indicator_tab', [
			'label' => __( 'Normal', 'the7mk2' ),
		] );

		$def_icon = [];
		$def_icon['default'] = [
			'value'   => 'icomoon-the7-font-the7-cross-00',
			'library' => 'the7-icons',
		];

		$this->add_control( $prefix . '_filter_indicator_icon', array_merge( [
			'label'       => __( 'Icon', 'the7mk2' ),
			'type'        => Controls_Manager::ICONS,
			'label_block' => false,
			'skin'        => 'inline',
		], $def_icon ) );

		$this->add_control( $prefix . '_filter_indicator_icon_color', [
			'label'     => __( 'Icon Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector . ' .elementor-icon'     => 'color: {{VALUE}};',
				$selector . ' .elementor-icon svg' => 'fill: {{VALUE}};',
			],
		] );

		$this->add_control( $prefix . '_filter_indicator_background_color', [
			'label'     => __( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'background-color: {{VALUE}};',
			],
		] );

		$this->add_control( $prefix . '_filter_indicator_border_color', [
			'label'     => __( 'Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'border-color: {{VALUE}};',
			],
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( $prefix . '_filter_indicator_hover_tab', [
			'label' => __( 'Hover', 'the7mk2' ),
		] );

		$this->add_control( $prefix . '_filter_indicator_hover_icon', array_merge( [
			'label'       => __( 'Icon', 'the7mk2' ),
			'type'        => Controls_Manager::ICONS,
			'label_block' => false,
			'skin'        => 'inline',
		], $def_icon ) );

		$hov_selector = '{{WRAPPER}} .filter-nav-item' . $active_class . ' .filter-nav-item-container:hover .indicator';
		$this->add_control( $prefix . '_filter_indicator_hover_icon_color', [
			'label'     => __( 'Icon Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector . ' .elementor-icon.indicator-hover'     => 'color: {{VALUE}};',
				$selector . ' .elementor-icon.indicator-hover svg' => 'fill: {{VALUE}};',
				$hov_selector . ' .elementor-icon'                 => 'color: {{VALUE}};',
				$hov_selector . ' .elementor-icon svg'             => 'fill: {{VALUE}};',
			],
		] );

		$selector = '{{WRAPPER}} .filter-nav-item' . $active_class . ' .filter-nav-item-container:hover .indicator';

		$this->add_control( $prefix . '_filter_indicator_hover_background_color', [
			'label'     => __( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'background-color: {{VALUE}};',
			],
		] );

		$this->add_control( $prefix . '_filter_indicator_hover_border_color', [
			'label'     => __( 'Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'border-color: {{VALUE}};',
			],
		] );

		$this->end_controls_tab();
		$this->end_controls_tabs();
	}

	protected function add_box_styles_controls( $prefix, $box_name ) {
	    $condition = [];
	    if ($prefix == 'clear_all'){
		    $condition[$prefix] = 'yes';
	    }
	    $this->start_controls_section( $prefix . '_box_section', [
			'label' => $box_name,
			'tab'   => Controls_Manager::TAB_STYLE,
			'condition'   => $condition,
		] );

		$extra_class = str_replace( "_", "-", $prefix );
		$selector = '{{WRAPPER}} .filter-nav-item.' . $extra_class . ' .filter-nav-item-container';

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => $prefix . '_box_text_typography',
			'selector' => $selector . ' .name',
		] );

		$this->start_controls_tabs( $prefix . '_box_tabs_style' );
		$this->add_box_tab_controls( $prefix, 'normal', __( 'Normal', 'the7mk2' ) );
		$this->add_box_tab_controls( $prefix, 'hover', __( 'Hover', 'the7mk2' ) );
		$this->end_controls_tabs();

		$this->add_control( $prefix . '_box_border_width', [
			'label'      => __( 'Border Width', 'the7mk2' ),
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
				$selector => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
			'separator'  => 'before',
		] );

		$this->add_control( $prefix . '_box_border _radius', [
			'label'      => __( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				$selector => 'border-radius: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_basic_responsive_control( $prefix . '_box_padding', [
			'label'      => __( 'Paddings', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 50,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'default'    => [
				'top'      => '0',
				'right'    => '0',
				'bottom'   => '0',
				'left'     => '0',
				'unit'     => 'px',
				'isLinked' => true,
			],
			'selectors'  => [
				$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		] );
		$this->end_controls_section();
	}

	protected function add_box_tab_controls( $prefix, $state, $box_name ) {
		$extra_class = str_replace( "_", "-", $prefix );

		$isHover = '';
		if ( $state === 'hover' ) {
			$isHover = ':hover';
		}
		$selector = '{{WRAPPER}} .filter-nav-item.' . $extra_class . ' .filter-nav-item-container' . $isHover;

		$full_prefix = $prefix . '_' . $state . '_';

		$this->start_controls_tab( $full_prefix . 'box_style', [
			'label' => $box_name,
		] );
		$this->add_control( $full_prefix . 'box_text_color', [
			'label'     => __( 'Text Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector . ' .name' => 'color: {{VALUE}};',
			],
		] );

		$this->add_control( $full_prefix . 'box_background_color', [
			'label'     => __( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'background-color: {{VALUE}};',
			],
		] );

		$this->add_control( $full_prefix . 'box_border_color', [
			'label'     => __( 'Border color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'border-color: {{VALUE}};',
			],
		] );

		$this->end_controls_tab();
	}

	protected function render() {
		if ( ! $this->isPreview() && ! is_shop() && ! is_product_taxonomy() ) {
			return;
		}
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'product-filter-act', 'class', 'the7-product-act-filter the7-product-filter' );
		$this->add_render_attribute( 'filter-container', 'class', "filter-container" );
		if ( $settings['toggle'] == 'yes' ) {
			$this->add_render_attribute( 'product-filter-act', 'class', 'collapsible' );
			$this->add_render_attribute( 'product-filter-act', 'class', $settings['toggle_closed_by_default'] );
			if ( $settings['toggle_closed_by_default'] ) {
				$this->add_render_attribute( 'filter-container', 'style', "display:none" );
			}
		}
		$this->add_render_attribute( 'filter-title', 'class', 'filter-title' );
		if ( empty( $settings['title_text'] ) ) {
			$this->add_render_attribute( 'filter-title', 'class', 'empty' );
		}
		$this->add_indicator_anim_attribute( $settings, 'active' );
		$this->add_indicator_anim_attribute( $settings, 'clear_all' );

		ob_start();

		?>
        <div <?php echo $this->get_render_attribute_string( 'product-filter-act' ); ?>>
            <div class="filter-header widget-title">
                <div <?php echo $this->get_render_attribute_string( 'filter-title' ); ?>>
					<?php echo esc_html( $settings['title_text'] ); ?>
                </div>
				<?php if ( ! empty( $settings['toggle_icon']['value'] ) ): ?>
                    <div class="filter-toggle-icon">
                        <span class="elementor-icon filter-toggle-closed">
							<?php Icons_Manager::render_icon( $settings['toggle_icon'] ); ?>
                        </span>
						<?php if ( ! empty( $settings['toggle_active_icon']['value'] ) ) : ?>
                            <span class="elementor-icon filter-toggle-active">
                                <?php Icons_Manager::render_icon( $settings['toggle_active_icon'] ); ?>
                            </span>
						<?php endif; ?>
                    </div>
				<?php endif; ?>
            </div>
            <div <?php echo $this->get_render_attribute_string( 'filter-container' ); ?>>
				<?php $found = $this->display_items( $settings ) ?>
            </div>
        </div>
		<?php
		if ( ! $found ) {
			ob_end_clean();
		} else {
			echo ob_get_clean();
		}
	}

	private function isPreview() {
		return $this->is_preview_mode() || Plugin::$instance->editor->is_edit_mode();
	}

	protected function add_indicator_anim_attribute( $settings, $prefix ) {
		if ( $settings[ $prefix . '_filter_indicator' ] !== 'yes' ) {
			return;
		}
		$normal_icon = $settings[ $prefix . '_filter_indicator_icon' ] ['value'];
		$hover_icon = $settings[ $prefix . '_filter_indicator_hover_icon' ] ['value'];
		$add_animate = false;
		$has_animation = false;
		if ( ! empty( $normal_icon ) && ! empty( $hover_icon ) && $normal_icon == $hover_icon ) {
			$this->add_render_attribute( 'product-filter-act', 'class', 'anim-disp-' . $prefix . '-indicator' );
			$has_animation = true;
		} else if ( empty( $normal_icon ) && ! empty( $hover_icon ) ) {
			$add_animate = true;
		} else if ( ! empty( $normal_icon ) && empty( $hover_icon ) ) {
			$add_animate = true;
		}
		if ( $add_animate ) {
			$has_animation = true;
			$this->add_render_attribute( 'product-filter-act', 'class', 'anim-trans-' . $prefix . '-indicator' );
		}
		if ( ! $has_animation ) {
			$this->add_render_attribute( 'product-filter-act', 'class', 'anim-off-' . $prefix . '-indicator' );
		}
	}

	protected function display_items( $settings ) {
		require_once __DIR__ . '/../../pro/modules/woocommerce/class-the7-wc-widget-nav.php';
		$widgetNav = new WC_Widget_Nav();

		$_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
		$min_price = isset( $_GET['min_price'] ) ? wc_clean( wp_unslash( $_GET['min_price'] ) ) : 0;
		$max_price = isset( $_GET['max_price'] ) ? wc_clean( wp_unslash( $_GET['max_price'] ) ) : 0;
		$rating_filter = isset( $_GET['rating_filter'] ) ? array_filter( array_map( 'absint', explode( ',', wp_unslash( $_GET['rating_filter'] ) ) ) ) : array(); // WPCS: sanitization ok, input var ok, CSRF ok.
		$base_link = $widgetNav->get_current_page_url();
		if ( ! $this->isPreview() ) {
			if ( ! ( 0 < count( $_chosen_attributes ) || 0 < $min_price || 0 < $max_price || ! empty( $rating_filter ) ) ) {
				return false;
			}
		}
		$remove_all_filters_link = remove_query_arg( array( 'add-to-cart' ), $base_link );

		if ( $this->isPreview() ) {
			//for preview
			$_chosen_attributes = [
				'preview' => [
					'terms' => [
						'Attribute 1',
						'Attribute 2',
					],
				],
			];
			$max_price = 50;
			$rating_filter = [ 3 ];
		}

		?>
        <ul class="filter-nav">
			<?php
            $items = 0;
			ob_start();
			if ( ! empty( $_chosen_attributes ) ) {

				$link = $base_link;
				foreach ( $_chosen_attributes as $taxonomy => $data ) {
					foreach ( $data['terms'] as $term_slug ) {
						$filter_classes = [ 'active' ];
						if ( $this->isPreview() ) {
							$text = $term_slug;
						} else {
							$term = get_term_by( 'slug', $term_slug, $taxonomy );
							if ( ! $term ) {
								continue;
							}

							$filter_name = 'filter_' . wc_attribute_taxonomy_slug( $taxonomy );
							$current_filter = isset( $_GET[ $filter_name ] ) ? explode( ',', wc_clean( wp_unslash( $_GET[ $filter_name ] ) ) ) : array(); // WPCS: input var ok, CSRF ok.
							$current_filter = array_map( 'sanitize_title', $current_filter );
							$new_filter = array_diff( $current_filter, array( $term_slug ) );

							$link = remove_query_arg( array( 'add-to-cart', $filter_name ), $base_link );
							$remove_all_filters_link = remove_query_arg( $filter_name, $remove_all_filters_link );
							if ( count( $new_filter ) > 0 ) {
								$link = add_query_arg( $filter_name, implode( ',', $new_filter ), $link );
							}

							$filter_classes = $filter_classes + [
									'chosen-' . sanitize_html_class( str_replace( 'pa_', '', $taxonomy ) ),
									'chosen-' . sanitize_html_class( str_replace( 'pa_', '', $taxonomy ) . '-' . $term_slug ),
								];
							$text = esc_html( $term->name );
						}
						$this->display_term_html( $link, $filter_classes, $text, $settings, 'active' );
						$items++;
					}
				}
			}

			if ( $min_price ) {
				$link = remove_query_arg( 'min_price', $base_link );
				$remove_all_filters_link = remove_query_arg( 'min_price', $remove_all_filters_link );
				$this->display_term_html( $link, [ 'active' ], sprintf( __( 'Min %s', 'the7mk2' ), wc_price( $min_price ) ), $settings, 'active' );
				$items++;
			}

			if ( $max_price ) {
				$link = remove_query_arg( 'max_price', $base_link );
				$remove_all_filters_link = remove_query_arg( 'max_price', $remove_all_filters_link );
				$this->display_term_html( $link, [ 'active' ], sprintf( __( 'Max %s', 'the7mk2' ), wc_price( $max_price ) ), $settings, 'active' );
				$items++;
			}

			if ( ! empty( $rating_filter ) ) {
				foreach ( $rating_filter as $rating ) {
					$link_ratings = implode( ',', array_diff( $rating_filter, array( $rating ) ) );
					$link = $link_ratings ? add_query_arg( 'rating_filter', $link_ratings ) : remove_query_arg( 'rating_filter', $base_link );

					$this->display_term_html( $link, [ 'active' ], sprintf( esc_html__( 'Rated %s out of 5', 'the7mk2' ), esc_html( $rating ) ), $settings, 'active' );
					$items++;
				}
				$remove_all_filters_link = remove_query_arg( 'rating_filter', $remove_all_filters_link );
			}
			$filter_html = ob_get_clean();

			$this->display_clear_all_html( $remove_all_filters_link, $settings, 'before', $items );
			echo $filter_html;
			$this->display_clear_all_html( $remove_all_filters_link, $settings, 'after', $items);
			?>
        </ul>
		<?php
		return true;
	}

	protected function display_term_html( $link, $classes, $text, $settings, $icon_prefix ) {
		?>
        <li class="filter-nav-item <?php echo esc_attr( implode( ' ', $classes ) ) ?>">
            <div class="filter-nav-item-container">
                <a aria-label="<?php echo esc_attr__( 'Remove filter', 'the7mk2' ) ?>"
                   href="<?php echo esc_url( $link ) ?>">
					<?php $this->displayFilterIndicator( $settings, $icon_prefix ); ?>
                    <span class="name"><?php echo $text ?></span>
                </a>
        </li>
		<?php
	}

	protected function displayFilterIndicator( $settings, $prefix ) {
		if ( $settings[ $prefix . '_filter_indicator' ] == 'yes' ) {
			?>
            <div class="indicator">
                <span class="elementor-icon indicator-normal">
                    <?php
                    if ( empty( $settings[ $prefix . '_filter_indicator_icon' ] ['value'] ) ) {
	                    ?> <i class="empty-icon"></i><?php
                    } else {
	                    Icons_Manager::render_icon( $settings[ $prefix . '_filter_indicator_icon' ] );
                    }
                    ?>
                </span>
                <span class="elementor-icon indicator-hover">
                    <?php
                    if ( empty( $settings[ $prefix . '_filter_indicator_hover_icon' ] ['value'] ) ) {
	                    ?><i class="empty-icon"></i><?php
                    } else {
	                    Icons_Manager::render_icon( $settings[ $prefix . '_filter_indicator_hover_icon' ] );
                    }
                    ?>
                </span>
            </div>
			<?php
		}
	}

	protected function display_clear_all_html( $link, $settings, $position, $items ) {
		if ( $items > 1 && $settings['clear_all'] && $settings['clear_all_position'] == $position ) {
			$this->display_term_html( $link, [ 'clear-all' ], esc_html( $settings['clear_all_text'] ), $settings, 'clear_all' );
		}
	}
}
