<?php
/**
 * The7 product navigation Elementor widget.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Plugin;
use Elementor\Controls_Stack;
use Elementor\Core\Responsive\Responsive;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use Elementor\Icons_Manager;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;

defined( 'ABSPATH' ) || exit;

/**
 * Class Product_Navigation
 *
 * @package The7\Mods\Compatibility\Elementor\Widgets\Woocommerce
 */
class Product_Navigation extends The7_Elementor_Widget_Base {

	public function get_name() {
		return 'the7-woocommerce-product-navigation';
	}

	protected function the7_title() {
		return __( 'Product Navigation', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-post-navigation';
	}

	protected function the7_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'navigation', 'links', 'menu', 'product' ];
	}

	public function get_categories() {
		return [ 'woocommerce-elements-single' ];
	}

	protected function get_less_file_name() {
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-woocommerce-product-navigation.less';
	}

	public function get_style_depends() {
	   return [ 'the7-woocommerce-product-navigation-widget' ];
	}

	public function get_script_depends() {
		if ( Plugin::$instance->preview->is_preview_mode() ) {
			return [ 'the7-woocommerce-product-images-widget-preview' ];
		}

		return [ 'the7-gallery-scroller' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_post_navigation_skin',
			[
				'label' => __( 'General', 'the7mk2' ),
			]
		);

		$this->add_control(
			'navigation_skin',
			[
				'label'        => __( 'Skin', 'the7mk2' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => [
					'normal' => __( 'Normal', 'the7mk2' ),
					'popup'  => __( 'Popup', 'the7mk2' ),
				],
				'default'      => 'normal',
				'prefix_class' => 'nav--skin-',
			]
		);

		$this->add_control(
			'popup_position',
			[
				'label'     => __( 'Popup Position', 'the7mk2' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'top'    => [
						'title' => __( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'   => 'bottom',
				'condition' => [
					'navigation_skin' => 'popup',
				],
			]
		);

		$this->add_control(
			'display_products',
			[
				'label'   => __( 'Navigate Through', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					''            => esc_html__( 'All Products', 'the7mk2' ),
					'product_cat' => esc_html__( 'Product Categories', 'the7mk2' ),
					'product_tag' => esc_html__( 'Product Tags', 'the7mk2' ),
				],
				'default' => '',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_post_navigation_content',
			[
				'label' => __( 'Content', 'the7mk2' ),
			]
		);

		$this->add_control(
			'show_content',
			[
				'label'        => __( 'Show Content Box On Desktop', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'prefix_class' => 'nav--box-',
			]
		);

		$this->add_control(
			'show_content_tablet',
			[
				'label'        => __( 'Show Content Box On Tablet', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'prefix_class' => 'nav--box-tablet-',
			]
		);

		$this->add_control(
			'show_content_mobile',
			[
				'label'        => __( 'Show Content Box On Mobile', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => '',
				'prefix_class' => 'nav--box-mobile-',
			]
		);

		$this->add_control(
			'content_position',
			[
				'label'     => __( 'Content Alignment', 'the7mk2' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'        => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'inner-sides' => [
						'title' => __( 'Inner', 'the7mk2' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'       => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
					'sides'       => [
						'title' => __( 'Outer', 'the7mk2' ),
						'icon'  => 'eicon-h-align-stretch',
					],
				],
				'default'   => 'sides',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'     => __( 'Title', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'the7mk2' ),
				'label_off' => __( 'Hide', 'the7mk2' ),
				'default'   => 'yes',
			]
		);

		$this->add_control(
			'title_width',
			[
				'label'        => __( 'Title Width', 'the7mk2' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => [
					'normal'      => __( 'Normal', 'the7mk2' ),
					'crp-to-line' => __( 'Crop to one line', 'the7mk2' ),
				],
				'default'      => 'normal',
				'condition'    => [
					'show_title' => 'yes',
				],
				'prefix_class' => 'the7-navigation-title-width-',
			]
		);

		$this->add_control(
			'excerpt_words_limit',
			[
				'label'       => __( 'Maximum Number Of Words', 'the7mk2' ),
				'description' => __( 'Leave empty to show the entire title.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 1,
				'max'         => 20,
				'condition'   => [
					'show_title'  => 'yes',
					'title_width' => 'normal',
				],
			]
		);

		$this->add_control(
			'show_price',
			[
				'label'     => __( 'Price', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'the7mk2' ),
				'label_off' => __( 'Hide', 'the7mk2' ),
				'default'   => 'yes',
			]
		);

		$this->add_control(
			'show_featured_image',
			[
				'label'     => __( 'Featured Image', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'the7mk2' ),
				'label_off' => __( 'Hide', 'the7mk2' ),
				'default'   => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_post_navigation_position',
			[
				'label' => __( 'Navigation', 'the7mk2' ),
			]
		);

		$this->add_basic_responsive_control(
			'alignment',
			[
				'label'                => __( 'Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Inner', 'the7mk2' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
					'outer'  => [
						'title' => __( 'Outer', 'the7mk2' ),
						'icon'  => 'eicon-h-align-stretch',
					],
				],
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
					'outer'  => 'space-between',
				],
				'prefix_class'         => 'nav%s--align-',
				'default'              => 'outer',
				'toggle'               => true,
				'device_args'          => [
					'desktop' => [
						'toggle' => false,
					],
				],
				'selectors'            => [
					'{{WRAPPER}} .the7-widget' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .post-nav'    => 'justify-content: {{VALUE}};',

					'(mobile) {{WRAPPER}}:not(.nav--box-mobile-y) .post-nav .empty-product'  => 'width: calc( var(--arrow-spacing, 0px) + var(--navigation-arrow-size, 0px) );',
					'(mobile) {{WRAPPER}}.nav-mobile--align-center .post-nav .empty-product' => 'display: flex;',
					'(mobile) {{WRAPPER}}.nav-mobile--align-outer .post-nav .empty-product'  => 'display: flex;',
					'(mobile) {{WRAPPER}}.nav-mobile--align-right .post-nav .empty-product'  => 'display: none;',
					'(mobile) {{WRAPPER}}.nav-mobile--align-left .post-nav .empty-product'   => 'display: none;',

					'(tablet) {{WRAPPER}}:not(.nav--box-tablet-y) .post-nav .empty-product'  => 'width: calc( var(--arrow-spacing, 0px) + var(--navigation-arrow-size, 0px) );',
					'(tablet) {{WRAPPER}}.nav-tablet--align-center .post-nav .empty-product' => 'display: flex;',
					'(tablet) {{WRAPPER}}.nav-tablet--align-outer .post-nav .empty-product'  => 'display: flex;',
					'(tablet) {{WRAPPER}}.nav-tablet--align-right .post-nav .empty-product'  => 'display: none;',
					'(tablet) {{WRAPPER}}.nav-tablet--align-left .post-nav .empty-product'   => 'display: none;',

					'(desktop+) {{WRAPPER}}.nav--align-right .post-nav .empty-product' => 'display: none;',
					'(desktop+) {{WRAPPER}}.nav--align-left .post-nav .empty-product'  => 'display: none;',
				],
			]
		);

		$this->add_control(
			'show_arrow',
			[
				'label'     => __( 'Arrows', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'the7mk2' ),
				'label_off' => __( 'Hide', 'the7mk2' ),
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'next_icon',
			[
				'label'       => __( 'Next Arrow Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
				'default'     => [
					'value'   => 'icomoon-the7-font-the7-arrow-09',
					'library' => 'the7-icons',
				],
				'classes'     => [ 'elementor-control-icons-none-label-hidden' ],
				'condition'   => [
					'show_arrow' => 'yes',
				],
			]
		);

		$this->add_control(
			'prev_icon',
			[
				'label'       => __( 'Previous Arrow Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
				'default'     => [
					'value'   => 'icomoon-the7-font-the7-arrow-08',
					'library' => 'the7-icons',
				],
				'classes'     => [ 'elementor-control-icons-none-label-hidden' ],
				'condition'   => [
					'show_arrow' => 'yes',
				],
			]
		);

		// Back Icon.
		$this->add_control(
			'show_back_arrow',
			[
				'label'     => __( 'Back Icon', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'the7mk2' ),
				'label_off' => __( 'Hide', 'the7mk2' ),
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'back_icon',
			[
				'label'       => __( 'Back Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
				'default'     => [
					'value'   => 'fas fa-border-all',
					'library' => 'fa-solid',
				],
				'classes'     => [ 'elementor-control-icons-none-label-hidden' ],
				'condition'   => [
					'show_back_arrow' => 'yes',
				],
			]
		);

		$this->add_control(
			'back_link',
			[
				'label'       => __( 'Link', 'the7mk2' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'the7mk2' ),
				'condition'   => [
					'show_back_arrow' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		// Title Style.
		$this->start_controls_section(
			'title_style',
			[
				'label'     => __( 'Title', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .widget-product-info .product-title',
			]
		);

		$this->start_controls_tabs( 'tabs_post_navigation_style' );

		$this->start_controls_tab(
			'tab_color_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .widget-product-info .product-title' => 'color: {{VALUE}};',
				],
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
			'hover_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-product-navigation .product-title { transition: color 0.3s ease; }
					{{WRAPPER}} .the7-product-navigation .the7-nav-product:hover .widget-product-info .product-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// Price Style.
		$this->start_controls_section(
			'price_style',
			[
				'label'     => __( 'Price', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_price' => 'yes',
				],
			]
		);

		$this->add_control(
			'normal_price_heading',
			[
				'type'  => \Elementor\Controls_Manager::HEADING,
				'label' => __( 'Normal price', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'price_typography',
				'label'    => __( 'Normal Price Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .widget-product-info .price *',
			]
		);

		$this->add_control(
			'normal_price_text_color',
			[
				'label'     => __( 'Normal Price Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .widget-product-info .price > span.woocommerce-Price-amount.amount, {{WRAPPER}} .widget-product-info .price > span.woocommerce-Price-amount span, {{WRAPPER}} .widget-product-info span.price, {{WRAPPER}} .widget-product-info .price ins span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sale_price_heading',
			[
				'type'      => \Elementor\Controls_Manager::HEADING,
				'label'     => __( 'Sale Price', 'the7mk2' ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sale_price_typography',
				'label'    => __( 'Old Price Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .widget-product-info .price del span',
			]
		);

		$this->add_control(
			'sale_price_text_color',
			[
				'label'     => __( 'Old Price Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .widget-product-info .price del span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'old_price_line_color',
			[
				'label'     => __( 'Old Price Line Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .widget-product-info .price del' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sale_new_price_typography',
				'label'    => __( 'New Price Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .widget-product-info .price ins span',
			]
		);

		$this->add_control(
			'sale_new_price_text_color',
			[
				'label'     => __( 'New Price Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .widget-product-info .price ins span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'price_space',
			[
				'label'     => __( 'Gap Above Price', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .widget-product-info span.price' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		// Featured Image.
		$this->start_controls_section(
			'fetatured_image_style',
			[
				'label'     => __( 'Featured Image', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_featured_image' => 'yes',
				],
			]
		);

		$this->add_basic_responsive_control(
			'image_size',
			[
				'label'      => __( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 60,
					'unit' => 'px',
				],
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 5,
						'max' => 130,
					],
					'%'  => [
						'min' => 5,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-product-navigation-thumb' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_preserve_ratio',
			[
				'label'        => __( 'Preserve Image Proportions', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'y',
				'return_value' => 'y',
				'prefix_class' => 'preserve-img-ratio-',
			]
		);

		$this->add_basic_responsive_control(
			'item_ratio',
			[
				'label'      => __( 'Image Ratio', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 0.66,
				],
				'range'      => [
					'px' => [
						'min'  => 0.1,
						'max'  => 2,
						'step' => 0.01,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'     => 'item_preserve_ratio',
							'operator' => '!=',
							'value'    => 'y',
						],
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .img-ratio-wrapper' => 'padding-bottom:  calc( {{SIZE}} * 100% )',
				],
			]
		);


		$this->add_basic_responsive_control(
			'image_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-product-navigation-thumb img' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'image_space',
			[
				'label'     => __( 'Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 10,
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .content--align-right .the7-product-navigation-thumb'                       => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .content--align-sides .post-nav__next .the7-product-navigation-thumb'       => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .content--align-inner-sides .post-nav__prev .the7-product-navigation-thumb' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .content--align-inner-sides .post-nav__next .the7-product-navigation-thumb' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .content--align-left .the7-product-navigation-thumb'                        => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .content--align-sides .post-nav__prev .the7-product-navigation-thumb'       => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Arrows.
		$this->start_controls_section(
			'arrow_style',
			[
				'label'     => __( 'Arrows', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_arrow' => 'yes',
				],
			]
		);

		$this->add_basic_responsive_control(
			'arrow_size',
			[
				'label'     => __( 'Size', 'the7mk2' ),
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
				'selectors' => [
					'{{WRAPPER}} .post-navigation__arrow-wrapper'     => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .post-navigation__arrow-wrapper svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}'                                     => '--navigation-arrow-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_post_navigation_arrow_style' );

		$this->start_controls_tab(
			'arrow_color_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'arrow_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post-navigation__arrow-wrapper'          => 'color: {{VALUE}};',
					'{{WRAPPER}} .post-navigation__arrow-wrapper svg path' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'arrow_color_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'arrow_hover_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post-navigation__arrow-wrapper { transition: all 0.3s ease; }
					{{WRAPPER}} .post-navigation__arrow-wrapper:hover'          => 'color: {{VALUE}};',
					'{{WRAPPER}} .post-navigation__arrow-wrapper:hover svg path' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_basic_responsive_control(
			'arrow_space',
			[
				'label'     => __( 'Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}} .nav-arrow-prev' => 'margin-right: {{SIZE}}{{UNIT}};',
					'body:not(.rtl) {{WRAPPER}} .nav-arrow-next' => 'margin-left: {{SIZE}}{{UNIT}};',
					'body.rtl {{WRAPPER}} .nav-arrow-prev'       => 'margin-left: {{SIZE}}{{UNIT}};',
					'body.rtl {{WRAPPER}} .nav-arrow-next'       => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'                                => '--arrow-spacing: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		// Back Icon.
		$this->start_controls_section(
			'back_icon_style',
			[
				'label'     => __( 'Back Icon', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_back_arrow' => 'yes',
				],
			]
		);

		$this->add_basic_responsive_control(
			'back_icon_size',
			[
				'label'      => __( 'Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 16,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .post-nav__back-wrapper' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .post-nav__back-wrapper svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_post_navigation_back_icon_style' );

		$this->start_controls_tab(
			'back_icon_color_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'back_icon_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post-nav__back-wrapper a'        => 'color: {{VALUE}};',
					'{{WRAPPER}} .post-nav__back-wrapper svg path' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'back_icon_color_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'back_icon_hover_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post-nav__back-wrapper a { transition: color 0.3s ease; } 
					{{WRAPPER}} .post-nav__back-wrapper:hover a'        => 'color: {{VALUE}};',
					'{{WRAPPER}} .post-nav__back-wrapper:hover svg path' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// Box Style.
		$this->start_controls_section(
			'box_style',
			[
				'label' => __( 'Box', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_basic_responsive_control(
			'box_padding',
			[
				'label'      => __( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-nav-product' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'min-height',
			[
				'label'      => __( 'Min Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 100,
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
					'{{WRAPPER}} .the7-nav-product' => 'min-height: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'max_width',
			[
				'label'      => __( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 250,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-nav-product' => 'max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'                   => '--navigation-max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'box_bg_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-nav-product' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'box_border',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-nav-product',
			]
		);

		$this->add_control(
			'box_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 0,
				],
				'range'     => [
					'px' => [
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .the7-nav-product' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'label'    => __( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-nav-product',
			]
		);

		$this->add_basic_responsive_control(
			'box_space',
			[
				'label'     => __( 'Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--navigation-spacing: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function container_class( $class = [] ) {
		$class[] = 'the7-widget';

		// Unique class.
		$class[] = $this->get_unique_class();

		$settings = $this->get_settings_for_display();

		$class[] = 'nav--content-'. ( $settings['show_content'] ? $settings['show_content'] : 'n' );
		$class[] = 'nav-tablet--content-'. ( $settings['show_content_tablet'] ? $settings['show_content_tablet'] : 'n' );
		$class[] = 'nav-mobile--content-'. ( $settings['show_content_mobile'] ? $settings['show_content_mobile'] : 'n' );

		return sprintf( ' class="%s" ', esc_attr( implode( ' ', $class ) ) );
	}

	protected function render() {
		$settings = $this->get_active_settings();

		$this->print_inline_css();

		echo '<div ' . $this->container_class() . '>';

		$prev_arrow = '';
		$next_arrow = '';

		if ( 'yes' === $settings['show_arrow'] ) {
			$next_icon_class = empty( $settings['next_icon']['value'] ) ? '' : $settings['next_icon'];
			$prev_icon_class = empty( $settings['prev_icon']['value'] ) ? '' : $settings['prev_icon'];

			$prev_arrow = '<span class="post-navigation__arrow-wrapper nav-arrow-prev elementor-icon">' . $this->get_elementor_icon_html( $prev_icon_class ) . '<span class="elementor-screen-only">' . esc_html__( 'Prev', 'the7mk2' ) . '</span></span>';
			$next_arrow = '<span class="post-navigation__arrow-wrapper nav-arrow-next elementor-icon">' . $this->get_elementor_icon_html( $next_icon_class ) . '<span class="elementor-screen-only">' . esc_html__( 'Next', 'the7mk2' ) . '</span></span>';
		}

		$in_same_term = false;
		$taxonomy     = 'product_cat';
		if ( ! empty( $settings['display_products'] ) && in_array( $settings['display_products'], [ 'product_cat', 'product_tag' ] ) ) {
			$in_same_term = true;
			$taxonomy     = $settings['display_products'];
		}

		$prev_post = $this->get_product_object( $in_same_term, '', true, $taxonomy );
		$next_post = $this->get_product_object( $in_same_term, '', false, $taxonomy );

		$this->add_render_attribute(
			'wrapper',
			[
				'class' => 'the7-product-navigation post-nav',
			]
		);

		if ( $settings['navigation_skin'] === 'popup' ) {
			$this->add_render_attribute(
				'wrapper',
				'class',
				'the7-nav-popup-align-' . ( $settings['popup_position'] ? $settings['popup_position'] : 'bottom' )
			);
		}

		if ( $settings['navigation_skin'] === 'popup' && ! empty( $settings['popup_content_position'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'content-align-' . $settings['popup_content_position'] );
		}

		$this->add_render_attribute(
			'content-wrapper',
			[
				'class' => 'the7-nav-product',
			]
		);

		if ( ! empty( $settings['content_position'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'content--align-' . $settings['content_position'] );
		}

		?>
			<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
				<div class="post-nav__prev nav-el the7-navigation-nav <?php echo $prev_post ? '' : 'empty-product'; ?>">
					<?php if ( $prev_post ) : ?>
						<?php previous_post_link( '%link', $prev_arrow, $in_same_term, '', $taxonomy ); ?>
						<?php if ( $settings['show_title'] || $settings['show_price'] || $settings['show_featured_image'] ) : ?>
							<div <?php echo $this->get_render_attribute_string( 'content-wrapper' ); ?>>
								<?php if ( $settings['show_featured_image'] ) : ?>
									<div class="the7-product-navigation-thumb">
										<?php echo $this->get_product_image( $prev_post );?>
									</div>
								<?php endif; ?>
								<?php if ( $settings['show_title'] || $settings['show_price'] ) : ?>
									<div class="widget-product-info">
										<?php if ( $settings['show_title'] ) : ?>
											<a href="<?php echo esc_url( $prev_post->get_permalink() ); ?>" class="product-title">
												<?php if ( $settings['excerpt_words_limit'] && $settings['title_width'] == 'normal' ) : ?>
													<?php echo esc_html__( wp_trim_words( $prev_post->get_title(), $settings['excerpt_words_limit'] ) ); ?>
												<?php else : ?>
													<?php echo esc_html__( $prev_post->get_title() ); ?>
												<?php endif; ?>
											</a>
										<?php endif; ?>
										<?php if ( $settings['show_price'] ) : ?>
											<span class="price">
												<?php echo wp_kses_post( $prev_post->get_price_html() ); ?>
											</span>
										<?php endif; ?>
									</div>
								<?php endif; ?>
								<a href="<?php echo esc_url( $prev_post->get_permalink() ); ?>" class="box-layer"></a>
							</div><!--the7-nav-product end-->
						<?php endif; ?>
					<?php elseif ( $next_post ) : ?>
						<div class="empty-product"></div>
					<?php endif; ?>
				</div>
				<?php if ( 'yes' === $settings['show_back_arrow'] ) : ?>
					<?php if ( $next_post || $prev_post ) : ?>
						<div class="post-nav__back-wrapper elementor-icon">
							<?php
							if ( empty( $settings['back_link']['url'] ) ) {
								$settings['back_link']['url'] = home_url( '/' );
							}

							$this->add_link_attributes( 'url', $settings['back_link'] );

							echo '<a ' . $this->get_render_attribute_string( 'url' ) . '>';

							Icons_Manager::render_icon( $settings['back_icon'], [ 'aria-hidden' => 'true' ] );

							echo '</a>';
							?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
				<div class="post-nav__next nav-el the7-navigation-nav <?php echo $next_post ? '' : 'empty-product'; ?>">
					<?php if ( $next_post ) : ?>
						<?php next_post_link( '%link', $next_arrow, $in_same_term, '', $taxonomy ); ?>
						<?php if ( $settings['show_title'] || $settings['show_price'] || $settings['show_featured_image'] ) : ?>
							<div <?php echo $this->get_render_attribute_string( 'content-wrapper' ); ?>>
								<?php if ( $settings['show_featured_image'] ) : ?>
									<div class="the7-product-navigation-thumb">
										<?php echo $this->get_product_image( $next_post );?>
									</div>
								<?php endif; ?>
								<?php if ( $settings['show_title'] || $settings['show_price'] ) : ?>
									<div class="widget-product-info">
										<?php if ( $settings['show_title'] ) : ?>
											<a href="<?php echo esc_url( $next_post->get_permalink() ); ?>" class="product-title">
												<?php if ( $settings['excerpt_words_limit'] && $settings['title_width'] == 'normal' ) : ?>
													<?php echo esc_html__( wp_trim_words( $next_post->get_title(), $settings['excerpt_words_limit'] ) ); ?>
												<?php else : ?>
													<?php echo esc_html__( $next_post->get_title() ); ?>
												<?php endif; ?>
											</a>
										<?php endif; ?>
										<?php if ( $settings['show_price'] ) : ?>
											<span class="price">
												<?php echo wp_kses_post( $next_post->get_price_html() ); ?>
											</span>
										<?php endif; ?>
									</div>
								<?php endif; ?>
								<a href="<?php echo esc_url( $next_post->get_permalink() ); ?>" class="box-layer"></a>
							</div><!--the7-nav-product end-->
						<?php endif; ?>
					<?php elseif ( $prev_post ) : ?>
						<div class="empty-product"></div>
					<?php endif; ?>
				</div>
			</div>
		<?php
		echo '</div>';
	}

	public function get_product_object( $in_same_term, $excluded_terms = '', $previous = true, $taxonomy = 'category' ) {
		$post = get_adjacent_post( $in_same_term, $excluded_terms, $previous, $taxonomy );

		if ( ! $post ) {
			return false;
		}

		$product = wc_get_product( $post->ID );

		if ( $product && $product->is_visible() ) {
			return $product;
		}

		return false;
	}

	protected function get_product_image( $product ) {
		$post_media = '';

		if ( $product->get_image_id() ) {
			$link_class = [ 'the7-nav-product-thumb', 'img-ratio-wrapper' ];

			$thumb_args = [
				'img_id' => $product->get_image_id(),
				'class'  => implode( ' ', $link_class ),
				'href'   => $product->get_permalink(),
				'custom' => the7_get_html_attributes_string(
					[
						'aria-label' => __( 'Product image', 'the7mk2' ),
					]
				),
				'wrap'   => '<a %HREF% %CLASS% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% /></a>',
				'echo'   => false,
			];

			if ( presscore_lazy_loading_enabled() ) {
				$thumb_args['lazy_loading'] = true;
			}

			$post_media = dt_get_thumb_img( $thumb_args );
		} else {
			$image = sprintf(
				'<img class="%s" src="%s" width="%s" height="%s">',
				'preload-me',
				get_template_directory_uri() . '/images/gray-square.svg',
				1500,
				1500
			);

			$post_media = sprintf(
				'<a %s>%s</a>',
				the7_get_html_attributes_string(
					[
						'aria-label' => __( 'Product image', 'the7mk2' ),
					]
				),
				$image
			);
		}

		return $post_media;
	}

	protected function less_vars( The7_Elementor_Less_Vars_Decorator_Interface $less_vars ) {
		$settings = $this->get_settings_for_display();

		foreach ( Responsive::get_breakpoints() as $size => $value ) {
			$less_vars->add_pixel_number( "elementor-{$size}-breakpoint", $value );
		}
	}

	protected function _add_render_attributes() {
		parent::_add_render_attributes();

		$settings = $this->get_active_settings();

		$is_desktop = $settings['alignment'];
		$is_tablet  = $settings['alignment_tablet'];
		$is_mobile  = $settings['alignment_mobile'];

		$tablet_type = ( ! empty( $is_tablet ) ? $is_tablet : $is_desktop );
		$mobile_type = ( ! empty( $is_mobile ) ? $is_mobile : $is_desktop );

		$this->add_render_attribute(
			'_wrapper',
			'class',
			[
				'nav-tablet--align-' . $tablet_type,
				'nav-mobile--align-' . $mobile_type,
			]
		);

		$this->add_render_attribute( '_wrapper', 'data-element_type', $this->get_name() );
	}

}
