<?php
/**
 * The7 elements scroller widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Plugin;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Border;
use Elementor\Core\Responsive\Responsive;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use Elementor\Group_Control_Image_Size;
use The7_Less_Vars_Value_Font;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;

defined( 'ABSPATH' ) || exit;

class The7_Elementor_Icon_Box_Grid_Widget extends The7_Elementor_Widget_Base {

	/**
	 * Get element name.
	 *
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7_icon_box_grid_widget';
	}

	protected function the7_title() {
		return __( 'Icon Box Grid', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-icon-box';
	}

	protected function get_less_file_name() {
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-icon-box-grid-widget.less';
	}

	public function get_style_depends() {
		return [ 'the7-icon-box-grid-widget' ];
	}

	protected function register_controls() {
		// Content.
		$this->add_content_controls();
		$this->add_layout_content_controls();

		// Style.
		$this->add_box_content_style_controls();
		$this->add_divider_style_controls();
		$this->add_icon_style_controls();
		$this->add_title_style_controls();
		$this->add_description_style_controls();
		$this->template( Button::class )->add_style_controls();
	}

	protected function add_content_controls() {

		$this->start_controls_section(
			'section_icon',
			[
				'label' => __( 'Items', 'the7mk2' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'selected_icon',
			[
				'label' => __( 'Icon', 'the7mk2' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
			]
		);

		$repeater->add_control(
			'title_text',
			[
				'label' => __( 'Title & Description', 'the7mk2' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'This is the heading', 'the7mk2' ),
				'placeholder' => __( 'Enter your title', 'the7mk2' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'description_text',
			[
				'label' => '',
				'type' => Controls_Manager::WYSIWYG,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the7mk2' ),
				'placeholder' => __( 'Enter your description', 'the7mk2' ),
				'rows' => 10,
				'separator' => 'none',
				'show_label' => false,
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label'       => __( 'Button Text', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'default'	  => esc_html__( 'Click Here', 'the7mk2' )
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __( 'Link', 'the7mk2' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'the7mk2' ),
			]
		);

		$tab_default_content = __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the7mk2' );

		$this->add_control(
			'icon_boxes_items',
			[
				'label' => __( 'Items', 'the7mk2' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title_text' 		=> __( 'Item Title #1', 'the7mk2' ),
						'description_text' 	=> $tab_default_content,
						'button_text'		=> __('Click Here', 'the7mk2' ),
						'link'				=> '#',
					],
					[
						'title_text' 		=> __( 'Item Title #2', 'the7mk2' ),
						'description_text' 	=> $tab_default_content,
						'button_text'		=> __('Click Here', 'the7mk2' ),
						'link'				=> '#',
					],
				],
				'title_field' => '{{{ title_text }}}',
			]
		);

		$this->add_control(
			'content_heading',
			[
				'label'     => __( 'Content', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label' => __( 'Title HTML Tag', 'the7mk2' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
				],
				'default' => 'h4',
			]
		);

		$this->add_basic_responsive_control(
			'text_align',
			[
				'label' => __( 'Alignment', 'the7mk2' ),
				'type' => Controls_Manager::CHOOSE,
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
					'justify' => [
						'title' => __( 'Justified', 'the7mk2' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'prefix_class'         => 'content-align%s-',
				'default' 			=> 'left',
				'selectors_dictionary' => [
					'left'   => 'align-items: flex-start; text-align: left;',
					'center' => 'align-items: center; text-align: center;',
					'right'  => 'align-items: flex-end; text-align: right;',
					'justify' => 'align-items: stretch; text-align: justify;',
 				],
				'selectors' => [
					'{{WRAPPER}} .box-content' => ' {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_layout_content_controls() {

		$this->start_controls_section(
			'layout_content_section',
			[
				'label' => __( 'Layout', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_widget_title',
			[
				'label'        => __( 'Widget Title', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => '',
			]
		);

		$this->add_control(
			'widget_title_text',
			[
				'label'     => __( 'Title', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Widget title',
				'condition' => [
					'show_widget_title' => 'y',
				],
			]
		);

		$this->add_control(
			'widget_title_tag',
			[
				'label'     => __( 'Title HTML Tag', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'default'   => 'h3',
				'condition' => [
					'show_widget_title' => 'y',
				],
			]
		);

		$this->add_control(
			'widget_columns_wide_desktop',
			[
				'label'       => __( 'Columns On A Wide Desktop', 'the7mk2' ),
				'description' => sprintf(
				// translators: %s: elementor content width.
					__( 'Apply when browser width is bigger than %s ("Content Width" Elementor setting).', 'the7mk2' ),
					the7_elementor_get_content_width_string()
				),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 1,
				'max'         => 12,
				'separator'   => 'before',
				'selectors'   => [
					'{{WRAPPER}} .dt-css-grid' => '--wide-desktop-columns: {{SIZE}}',
				],
				'render_type'    => 'template',
			]
		);

		$this->add_basic_responsive_control(
			'widget_columns',
			[
				'label'          => __( 'Columns', 'the7mk2' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 1,
				'tablet_default' => 1,
				'mobile_default' => 1,
				'min'            => 1,
				'max'            => 12,
				'selectors'      => [
					'{{WRAPPER}} .dt-css-grid' => 'grid-template-columns: repeat({{SIZE}},1fr)',
					'{{WRAPPER}}'              => '--wide-desktop-columns: {{SIZE}}',
				],
				'render_type'    => 'template',
			]
		);

		$this->add_control(
			'gap_between_posts',
			[
				'label'      => __( 'Columns Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '40',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-css-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'rows_gap',
			[
				'label'      => __( 'Rows Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '20',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-css-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}}; --grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider',
			[
				'label'     => __( 'Dividers', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'elementor' ),
				'label_on'  => __( 'On', 'elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'link_click',
			[
				'label'     => __( 'Apply Link & Hover', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'button',
				'separator' => 'before',
				'options'   => [
					'box'  => __( 'Whole box', 'the7mk2' ),
					'button' => __( "Separate element's", 'the7mk2' ),
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_box_content_style_controls() {
		$this->start_controls_section(
			'section_design_box',
			[
				'label' => __( 'Box', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_basic_responsive_control(
			'box_height',
			[
				'label'      => __( 'Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
					'vh' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .wf-cell .the7-icon-box-grid' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'content_position',
			[
				'label'                => __( 'Content Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => __( 'Top', 'the7mk2' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __( 'Middle', 'the7mk2' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'the7mk2' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default'              => 'top',
				'prefix_class'         => 'icon-box-vertical-align%s-',
				'selectors_dictionary' => [
					'top'   => 'align-items: flex-start;align-content: flex-start;',
					'center' => 'align-items: center;align-content: center;',
					'bottom'  => 'align-items: flex-end;align-content: flex-end;',
				],
				'selectors'    => [
					'{{WRAPPER}} .wf-cell .the7-icon-box-grid' => '{{VALUE}}',
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'box_border',
				'label' => __( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .wf-cell .the7-icon-box-grid',
				'exclude'	=> [
					'color'
				]
			]
		);

	    $this->add_basic_responsive_control(
	     	'box_border_radius',
	     	[
	     		'label' => __('Border Radius', 'the7mk2'),
	     		'type' => Controls_Manager::DIMENSIONS,
	     		'size_units' => ['px', '%'],
	     		'selectors' =>  [
	     			'{{WRAPPER}} .wf-cell .the7-icon-box-grid' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
	     		]
	     	]
	    );

		$this->add_basic_responsive_control(
			'box_padding',
			[
				'label'      => __( 'Padding', 'the7mk2' ),
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
				'selectors'  => [
					'{{WRAPPER}} .wf-cell .the7-icon-box-grid' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_icon_box_style' );

		$this->start_controls_tab(
			'tab_color_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'box_bg_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wf-cell .the7-icon-box-grid' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
            'box_border_color',
            [
                'label'     => __( 'Border Color', 'the7mk2' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wf-cell .the7-icon-box-grid' => 'border-color: {{VALUE}}',
                ]
            ]
        );

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'label' => __( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .wf-cell .the7-icon-box-grid',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_color_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'bg_hover_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-icon-box-grid { transition: all 0.3s ease; } {{WRAPPER}} .wf-cell .the7-icon-box-grid:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
            'box_hover_border_color',
            [
                'label'     => __( 'Border Color', 'the7mk2' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .the7-icon-box-grid { transition: all 0.3s ease; } {{WRAPPER}} .wf-cell .the7-icon-box-grid:hover' => 'border-color: {{VALUE}}',
                ]
            ]
        );

        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_hover_shadow',
				'label' => __( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-icon-box-grid { transition: all 0.3s ease; } {{WRAPPER}} .wf-cell .the7-icon-box-grid:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function add_divider_style_controls() {
		$this->start_controls_section(
			'widget_divider_section',
			[
				'label'     => __( 'Dividers', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'divider' => 'yes',
				],
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
					'{{WRAPPER}} .widget-divider-on .wf-cell:before' => 'border-bottom-style: {{VALUE}}',
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
					'{{WRAPPER}} .widget-divider-on' => '--divider-width: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .widget-divider-on .wf-cell:before' => 'border-bottom-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_icon_style_controls() {
		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => __( 'Icon', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_basic_responsive_control(
			'position',
			[
				'label'                => __( 'Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'  => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'top'   => [
						'title' => __( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'right' => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'              => 'left',
				'toggle'               => false,
				'device_args'          => [
					'tablet' => [
						'toggle' => true,
					],
					'mobile' => [
						'toggle' => true,
					],
				],
				'selectors_dictionary' => [
					'top'   => 'flex-flow: column wrap;',
					'left'  => 'flex-flow: row nowrap;',
					'right' => 'flex-flow: row nowrap;',
				],
				'selectors'            => [
					'{{WRAPPER}} .box-content-wrapper' => '{{VALUE}}',
				],
				'prefix_class'         => 'icon-position%s-',
			]
		);

		$icon_position_options            = [
			'start'  => __( 'Start', 'the7mk2' ),
			'center' => __( 'Center', 'the7mk2' ),
			'end'    => __( 'End', 'the7mk2' ),
		];
		$icon_position_options_on_devices = [ '' => __( 'Default', 'the7mk2' ) ] + $icon_position_options;

		$this->add_basic_responsive_control(
			'icon_position',
			[
				'label'                => __( 'Align', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'start',
				'options'              => $icon_position_options,
				'device_args'          => [
					'tablet' => [
						'default' => '',
						'options' => $icon_position_options_on_devices,
					],
					'mobile' => [
						'default' => '',
						'options' => $icon_position_options_on_devices,
					],
				],
				'prefix_class'         => 'icon-vertical-align%s-',
				'selectors_dictionary' => [
					'start'  => 'align-self: flex-start;',
					'center' => 'align-self: center;',
					'end'    => 'align-self: flex-end;',
				],
				'selectors'            => [
 					'{{WRAPPER}} .elementor-icon-div' => '{{VALUE}}',
 				],
			]
		);

		$this->add_basic_responsive_control(
			'size',
			[
				'label' => __( 'Size', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_basic_responsive_control(
			'icon_padding',
			[
				'label'      => __( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'selectors'  => [
					'{{WRAPPER}} .elementor-icon-div .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min'  => 0.1,
						'max'  => 5,
						'step' => 0.01,
						],
				],
			]
		);

		$this->add_basic_responsive_control(
			'border_width',
			[
				'label'      => __( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-icon-div .elementor-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style:solid;',
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-icon-div .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'icon_colors' );

		$this->start_controls_tab(
			'icon_colors_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'primary_color',
			[
				'label'     => __( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-div i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-icon-div svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-div .elementor-icon' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-div .elementor-icon' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_colors_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'hover_primary_color',
			[
				'label'     => __( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon i { transition: color 0.3s ease; } {{WRAPPER}} .elementor-icon svg { transition: fill 0.3s ease; } {{WRAPPER}} .elementor-icon-div:hover i, {{WRAPPER}} a.the7-icon-box-grid:hover .elementor-icon-div i'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-icon-div .elementor-icon:hover svg, {{WRAPPER}} a.the7-icon-box-grid:hover .elementor-icon-div .elementor-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-div .elementor-icon:hover, {{WRAPPER}} a.the7-icon-box-grid:hover .elementor-icon' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_hover_bg_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-div .elementor-icon:hover, {{WRAPPER}} a.the7-icon-box-grid:hover .elementor-icon-div .elementor-icon' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_basic_responsive_control(
			'icon_spacing',
			[
				'label'      => __( 'Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 15,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}'                                                         => '--icon-spacing: {{SIZE}}{{UNIT}}',
					'(tablet) {{WRAPPER}}.icon-position-tablet-left .elementor-icon-div'  => 'margin: 0 var(--icon-spacing) 0 0',
					'(tablet) {{WRAPPER}}.icon-position-tablet-right .elementor-icon-div' => 'margin: 0 0 0 var(--icon-spacing)',
					'(mobile) {{WRAPPER}}.icon-position-mobile-left .elementor-icon-div'  => ' margin: 0 var(--icon-spacing) 0 0',
					'(mobile) {{WRAPPER}}.icon-position-mobile-right .elementor-icon-div' => 'margin: 0 0 0 var(--icon-spacing)',
					'(tablet) {{WRAPPER}}.icon-position-tablet-top .elementor-icon-div'   => 'margin: 0 0 var(--icon-spacing) 0',
					'(mobile) {{WRAPPER}}.icon-position-mobile-top .elementor-icon-div'   => 'margin: 0 0 var(--icon-spacing) 0',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_title_style_controls() {
		// Title Style.
		$this->start_controls_section(
			'title_style',
			[
				'label'     => __( 'Title', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .box-content-wrapper .box-heading, {{WRAPPER}} .box-content-wrapper .box-heading a',
			]
		);

		$this->start_controls_tabs( 'tabs_title_style' );

		$this->start_controls_tab(
			'tab_title_color_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'tab_title_text_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .box-content-wrapper .box-heading, {{WRAPPER}} .box-content-wrapper .box-heading a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_color_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'tab_title_hover_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .box-heading, {{WRAPPER}} .box-heading a { transition: color 0.3s ease; } {{WRAPPER}} .the7-icon-box-grid .box-heading:hover, {{WRAPPER}} .the7-icon-box-grid .box-heading:hover a' => 'color: {{VALUE}};',
					'{{WRAPPER}} a.the7-icon-box-grid:hover .box-heading, {{WRAPPER}} a.the7-icon-box-grid:hover .box-heading a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function add_description_style_controls() {
		$this->start_controls_section(
			'section_style_desc',
			[
				'label' => __( 'Description', 'the7mk2' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .box-description',
			]
		);

		$this->start_controls_tabs( 'tabs_description_style' );

		$this->start_controls_tab(
			'tab_desc_color_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'short_desc_color',
			[
				'label'     => __( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .box-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_desc_color_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'short_desc_color_hover',
			[
				'label'     => __( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .box-description { transition: color 0.3s ease; } {{WRAPPER}} .box-description:hover, {{WRAPPER}} a.the7-icon-box-grid:hover .box-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'gap_above_description',
			[
				'label'      => __( 'Description Top Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors'  => [
					'{{WRAPPER}} .box-description' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_main_wrapper_class_render_attribute_for( $element ) {

		$class = [
			'the7-box-grid-wrapper',
			'the7-elementor-widget',
			'loading-effect-none'
		];

		// Unique class.
		$class[] = $this->get_unique_class();

		$settings = $this->get_settings_for_display();

		if ( $settings['divider'] ) {
			$class[] = 'widget-divider-on';
		}

		$this->add_render_attribute( $element, 'class', $class );
	}

	protected function display_widget_title( $text, $tag = 'h3' ) {

		$tag = esc_html( $tag );

		$output  = '<' . $tag . ' class="rp-heading">';
		$output .= esc_html( $text );
		$output .= '</' . $tag . '>';

		return $output;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->print_inline_css();

		$this->add_main_wrapper_class_render_attribute_for( 'wrapper' );

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';

		if ( $settings['show_widget_title'] === 'y' && $settings['widget_title_text'] ) {
			echo $this->display_widget_title( $settings['widget_title_text'], $settings['widget_title_tag'] );
		}

		if ( '' !== $settings['icon_boxes_items'] ) : ?>
			<div class="dt-css-grid">
				<?php
				foreach ( $settings['icon_boxes_items'] as $index => $item ) :
					$repeater_setting_key = $this->get_repeater_setting_key( 'text', 'icon_list', $index );

					$this->add_render_attribute( $repeater_setting_key, 'class', 'wf-cell shown' );
					
					$tab_content_setting_key = $this->get_repeater_setting_key( 'description_text', 'tabs', $index );
					$this->add_render_attribute( $tab_content_setting_key, 'class', 'box-description' );
					$this->add_inline_editing_attributes( $tab_content_setting_key );

					$link_key = 'link_' . $index;

					$this->add_link_attributes( $link_key, $item['link'] );

					$btn_attributes = $this->get_render_attribute_string( $link_key );
					$btn_attributes_list = [];

					if ( 'button' === $settings['link_click'] ) {
						$title_link       		= '<a ' . $btn_attributes . '>';
						$title_link_close 		= '</a>';
						$btn_element         = 'a';
						$btn_attributes_list = $this->get_render_attributes( $link_key );
						$parent_wrapper       	= '<div class="the7-icon-box-grid">';
						$parent_wrapper_close 	= '</div>';
						$icon_wrapper       	= '<a class="elementor-icon-div" '. $btn_attributes .'>';
						$icon_wrapper_close 	= '</a>';
					} else {
						$title_link       		= '';
						$title_link_close 		= '';
						$btn_element      = 'div';
						$parent_wrapper       	= '<a class="the7-icon-box-grid box-hover" '. $btn_attributes .'>';
						$parent_wrapper_close 	= '</a>';						
						$icon_wrapper       	= '<div class="elementor-icon-div">';
						$icon_wrapper_close 	= '</div>';
					}
					?>
					<div <?php echo $this->get_render_attribute_string( $repeater_setting_key ); ?>>
						<?php echo $parent_wrapper; ?>
							<div class="box-content-wrapper">
								<?php if (  $item['selected_icon']["value"]!== ''  ) : ?>
									<?php echo $icon_wrapper; ?>
										<div class="elementor-icon">
											<?php Icons_Manager::render_icon( $item['selected_icon'], [ 'aria-hidden' => 'true' ] );?>
										</div>
									<?php echo $icon_wrapper_close; ?>
								<?php endif; ?>
								<div class="box-content">
									<?php if ( $item['title_text'] ) : ?>
										<?php $title_html_tag = Utils::validate_html_tag( $settings['title_html_tag'] ); ?>
										<<?php echo $title_html_tag; ?> class="box-heading">
											<?php echo $title_link; ?>
												<?php echo wp_kses_post( $item['title_text'] ); ?>
											<?php echo $title_link_close;?>
										</<?php echo $title_html_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
									<?php endif; ?>
									<?php if ( ! Utils::is_empty( $item['description_text'] ) ) : ?>
										<div <?php echo $this->get_render_attribute_string( $tab_content_setting_key ); ?>><?php echo $item['description_text']; ?></div>
									<?php endif; ?>
									<?php
									if ( $item['button_text'] || $this->template( Button::class )->is_icon_visible() ) {
										// Cleanup button render attributes.
										$this->remove_render_attribute( 'box-button' );

										$this->add_render_attribute( 'box-button', $btn_attributes_list ?: [] );

										$this->template( Button::class )->render_button(
											'box-button',
											esc_html( $item['button_text'] ),
											$btn_element
										);
									}
									?>
								</div>
							</div>
						<?php echo $parent_wrapper_close; ?>
					</div>
				<?php
				endforeach;
				?>
			</div>
		<?php
		endif;
		echo '</div>';
	}

	protected function get_content_btn( $item, $link_key ) {
		$settings = $this->get_settings_for_display();

		$icon = $link = '';
		$title_link       = '<div class="box-button">';
		$title_link_close = '</div>';
		
		$btn_attributes = $this->get_render_attribute_string( $link_key );

		if ( 'button' === $settings['link_click'] ) {
			$title_link       = '<a class="box-button" '. $btn_attributes .'>';
			$title_link_close = '</a>';
		}

		if ( $settings['button_icon'] ) {
			$icon = $this->get_elementor_icon_html( $settings['button_icon'] );
		}

		ob_start();
		?>

		<?php echo $title_link; ?>
			<?php
			echo '<span>' . $icon . esc_html( $item['button_text'] ) . '</span>';

		echo $title_link_close;

		return ob_get_clean();
	}

	
	protected function less_vars( The7_Elementor_Less_Vars_Decorator_Interface $less_vars ) {
		$settings = $this->get_settings_for_display();

		$less_vars->add_keyword(
			'unique-shortcode-class-name',
			$this->get_unique_class() . '.the7-box-grid-wrapper',
			'~"%s"'
		);
		foreach ( $this->get_supported_devices() as $device => $dep ) {
			$less_vars->start_device_section( $device );
			$less_vars->add_keyword(
				'grid-columns',
				$this->get_responsive_setting( 'widget_columns' ) ?: 3
			);
			$less_vars->close_device_section();
		}
		$less_vars->add_keyword('grid-wide-columns', $settings['widget_columns_wide_desktop']);

		foreach ( Responsive::get_breakpoints() as $size => $value ) {
			$less_vars->add_pixel_number( "elementor-{$size}-breakpoint", $value );
		}
	}
}