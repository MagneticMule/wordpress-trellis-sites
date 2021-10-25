<?php
/**
 * The7 elements scroller widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use The7\Inc\Mods\Compatibility\WooCommerce\Front\Recently_Viewed_Products;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Query_Control\The7_Group_Control_Query;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\DT_Shortcode_Products_Carousel_Adapter;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\The7_Shortcode_Adapter_Interface;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Shortcode_Adaptor_Widget_Base;

defined( 'ABSPATH' ) || exit;

class Old_Products_Carousel extends The7_Elementor_Shortcode_Adaptor_Widget_Base {

	/**
	 * Register widget assets.
	 *
	 * @see The7_Elementor_Widget_Base::__construct()
	 */
	protected function register_assets() {
		// Setup shortcode adapter.
		require_once __DIR__ . '/../../shortcode-adapters/class-the7-elementor-products-carousel-adapter.php';
		$this->setup_shortcode_adapter( new DT_Shortcode_Products_Carousel_Adapter() );
	}

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-elements-woo-carousel';
	}

	protected function the7_title() {
		return __( 'Old Product Carousel', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-posts-carousel';
	}

	/**
	 * Get the7 widget categories.
	 *
	 * @return string[]
	 */
	protected function the7_categories() {
		return [ 'woocommerce-elements' ];
	}

	public function get_script_depends() {
		if ( $this->is_preview_mode() ) {
			return [ 'the7-elements-carousel-widget-preview' ];
		}

		if ( $this->get_settings_for_display( 'query_post_type' ) === 'recently_viewed' ) {
			return [ 'the7-cookies' ];
		}

		return [];
	}

	/**
	 * Render widget.
	 */
	protected function render() {
		parent::render();

		$post_type  = $this->get_settings_for_display( 'query_post_type' );
		$is_preview = $this->is_preview_mode();

		if ( ! $is_preview && $post_type === 'recently_viewed' ) {
			Recently_Viewed_Products::track_via_js();
		}
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		$this->add_layout_content_controls();
		$this->add_content_controlls();
		$this->register_query_controls();
		$this->add_arrows_controll();
	}

	protected function get_adapted_settings() {
		$settings = $this->get_settings_for_display();

		$adopted_settings = [
			'item_space'       => 'item_space_adaptor:size',
			'stage_padding'    => 'stage_padding_adapter:size',
			'next_icon'        => 'next_icon_adapter:value',
			'prev_icon'        => 'prev_icon_adapter:value',
			'slides_on_desk'   => 'widget_columns',
			'slides_on_h_tabs' => 'widget_columns_tablet',
			'slides_on_mob'    => 'widget_columns_mobile',
		];

		foreach ( $adopted_settings as $setting => $adopted_setting ) {
			$parts  = explode( ':', $adopted_setting );
			$key    = $parts[0];
			$subkey = isset( $parts[1] ) ? $parts[1] : null;

			if ( $subkey ) {
				$adopted_value = isset( $settings[ $key ][ $subkey ] ) ? $settings[ $key ][ $subkey ] : null;
			} else {
				$adopted_value = isset( $settings[ $key ] ) ? $settings[ $key ] : null;
			}

			if ( $adopted_value !== null ) {
				$settings[ $setting ] = $adopted_value;
			}
		}

		return $settings;
	}

	protected function add_layout_content_controls() {
		$this->start_controls_section(
			'layout_section',
			[
				'label' => __( 'Layout', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => __( 'Text & button position:', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'content_below_img',
				'options' => [
					'content_below_img' => 'Text & button below image',
					'btn_on_img'        => 'Text below image, button on image',
					'btn_on_img_hover'  => 'Text below image, button on image hover',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_content_controlls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'dis_posts_total',
			[
				'label'       => __( 'Total number of products', 'the7mk2' ),
				'description' => __(
					'Leave empty to use value from the WP Reading settings. Set "-1" to show all posts.',
					'the7mk2'
				),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 6,
			]
		);
		/**
		 * Responsiveness.
		 */
		$this->add_control(
			'responsiveness_settings',
			[
				'label'     => __( 'Columns & Responsiveness', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_basic_responsive_control(
			'widget_columns',
			[
				'label'          => __( 'Columns', 'the7mk2' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
			]
		);
		$this->add_control(
			'item_space_adaptor',
			[
				'label'      => __( 'Gap between columns', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 15,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
			]
		);

		$this->add_control(
			'stage_padding_adapter',
			[
				'label'      => __( 'Stage padding', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
			]
		);

		$this->add_control(
			'adaptive_height',
			[
				'label'        => __( 'Adaptive height', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => '',
			]
		);

		$this->end_controls_section();
	}

	protected function add_arrows_controll() {
		/**
		 * Arrows section.
		 */
		$this->start_controls_section(
			'arrows_section',
			[
				'label' => __( 'Arrows', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'arrows',
			[
				'label'        => __( 'Show arrows', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
			]
		);

		$this->add_control(
			'arrows_heading',
			[
				'label'     => __( 'Arrow Icon', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'next_icon_adapter',
			[
				'label'     => __( 'Choose next arrow icon', 'the7mk2' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'icomoon-the7-font-the7-arrow-09',
					'library' => 'the7-icons',
				],
				'classes'   => [ 'elementor-control-icons-svg-uploader-hidden' ],
				'condition' => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'prev_icon_adapter',
			[
				'label'     => __( 'Choose previous arrow icon', 'the7mk2' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'icomoon-the7-font-the7-arrow-08',
					'library' => 'the7-icons',
				],
				'classes'   => [ 'elementor-control-icons-svg-uploader-hidden' ],
				'condition' => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'arrow_icon_size',
			[
				'label'      => __( 'Arrow icon size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 18,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'condition'  => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'arrows_background_heading',
			[
				'label'     => __( 'Arrow Background', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'arrow_bg_width',
			[
				'label'      => __( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 36,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'condition'  => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'arrow_bg_height',
			[
				'label'      => __( 'Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 36,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'condition'  => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'arrow_border_radius',
			[
				'label'      => __( 'Arrow border radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 500,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'condition'  => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'arrow_border_width',
			[
				'label'      => __( 'Arrow border width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 25,
						'step' => 1,
					],
				],
				'condition'  => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'arrows_color_heading',
			[
				'label'     => __( 'Color Setting', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'arrows' => 'y',
				],
				'global'    => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'arrow_icon_color',
			[
				'label'       => __( 'Arrow icon color', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'default'     => '#ffffff',
				'condition'   => [
					'arrows' => 'y',
				],
				'global'      => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'arrow_icon_border',
			[
				'label'        => __( 'Show arrow border color', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'arrows' => 'y',
				],
				'global'       => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'arrow_border_color',
			[
				'label'       => __( 'Arrow border color', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'default'     => '',
				'condition'   => [
					'arrow_icon_border' => 'y',
					'arrows'            => 'y',
				],
				'global'      => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'arrows_bg_show',
			[
				'label'        => __( 'Show arrow background', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'arrow_bg_color',
			[
				'label'       => __( 'Arrow background color', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'default'     => '',
				'condition'   => [
					'arrows_bg_show' => 'y',
					'arrows'         => 'y',
				],
				'global'      => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'arrows_hover_color_heading',
			[
				'label'     => __( 'Hover Color Setting', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'arrows' => 'y',
				],
				'global'    => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'arrow_icon_color_hover',
			[
				'label'       => __( 'Arrow icon color hover', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'default'     => 'rgba(255,255,255,0.75)',
				'condition'   => [
					'arrows' => 'y',
				],
				'global'      => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'arrow_icon_border_hover',
			[
				'label'        => __( 'Show arrow border color hover', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'arrow_border_color_hover',
			[
				'label'       => __( 'Arrow border color hover', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'default'     => '',
				'condition'   => [
					'arrow_icon_border_hover' => 'y',
					'arrows'                  => 'y',
				],
				'global'      => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'arrows_bg_hover_show',
			[
				'label'        => __( 'Show arrow background hover', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'arrow_bg_color_hover',
			[
				'label'       => __( 'Arrow background hover color', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'default'     => '',
				'condition'   => [
					'arrows_bg_hover_show' => 'y',
					'arrows'               => 'y',
				],
				'global'      => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'right_arrow_position_heading',
			[
				'label'     => __( 'Right Arrow Position', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'r_arrow_v_position',
			[
				'label'     => __( 'Vertical position', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => [
					'top'    => 'Top',
					'center' => 'Center',
					'bottom' => 'Bottom',
				],
				'condition' => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'r_arrow_h_position',
			[
				'label'     => __( 'Horizontal position', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'right',
				'options'   => [
					'left'   => 'Left',
					'center' => 'Center',
					'right'  => 'Right',
				],
				'condition' => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'r_arrow_v_offset',
			[
				'label'      => __( 'Vertical offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -10000,
						'max'  => 10000,
						'step' => 1,
					],
				],
				'condition'  => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'r_arrow_h_offset',
			[
				'label'      => __( 'Horizontal offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => -43,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -10000,
						'max'  => 10000,
						'step' => 1,
					],
				],
				'condition'  => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'left_arrow_position_heading',
			[
				'label'     => __( 'Left Arrow Position', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'l_arrow_v_position',
			[
				'label'     => __( 'Vertical position', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => [
					'top'    => 'Top',
					'center' => 'Center',
					'bottom' => 'Bottom',
				],
				'condition' => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'l_arrow_h_position',
			[
				'label'     => __( 'Horizontal position', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => [
					'left'   => 'Left',
					'center' => 'Center',
					'right'  => 'Right',
				],
				'condition' => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'l_arrow_v_offset',
			[
				'label'      => __( 'Vertical offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -10000,
						'max'  => 10000,
						'step' => 1,
					],
				],
				'condition'  => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'l_arrow_h_offset',
			[
				'label'      => __( 'Horizontal offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => -43,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -10000,
						'max'  => 10000,
						'step' => 1,
					],
				],
				'condition'  => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'arrows_responsiveness_heading',
			[
				'label'     => __( 'Arrows responsiveness', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'arrow_responsiveness',
			[
				'label'     => __( 'Responsive behaviour', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'reposition-arrows',
				'options'   => [
					'reposition-arrows' => 'Reposition arrows',
					'no-changes'        => 'Leave as is',
					'hide-arrows'       => 'Hide arrows',
				],
				'condition' => [
					'arrows' => 'y',
				],
			]
		);

		$this->add_control(
			'hide_arrows_mobile_switch_width',
			[
				'label'     => __( 'Hide arrows if browser width is less then', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 778,
				'condition' => [
					'arrow_responsiveness' => 'hide-arrows',
					'arrows'               => 'y',
				],
			]
		);

		$this->add_control(
			'reposition_arrows_mobile_switch_width',
			[
				'label'     => __( 'Reposition arrows after browser width', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 778,
				'condition' => [
					'arrow_responsiveness' => 'reposition-arrows',
					'arrows'               => 'y',
				],
			]
		);

		$this->add_control(
			'l_arrows_mobile_h_position',
			[
				'label'      => __( 'Left arrow horizontal offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 10,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -10000,
						'max'  => 10000,
						'step' => 1,
					],
				],
				'condition'  => [
					'arrow_responsiveness' => 'reposition-arrows',
					'arrows'               => 'y',
				],
			]
		);

		$this->add_control(
			'r_arrows_mobile_h_position',
			[
				'label'      => __( 'Right arrow horizontal offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 10,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -10000,
						'max'  => 10000,
						'step' => 1,
					],
				],
				'condition'  => [
					'arrow_responsiveness' => 'reposition-arrows',
					'arrows'               => 'y',
				],
			]
		);

		$this->end_controls_section();
	}


	protected function register_query_controls() {
		$this->start_controls_section(
			'section_query',
			[
				'label' => __( 'Query', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'current_query_info',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __(
					'Note that the amount of posts per page is the product of "Products per row" and "Rows per page" settings from "Appearance"->"Customize"->"WooCommerce"->"Products Catalog".',
					'the7mk2'
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'query_post_type' => 'current_query',
				],
			]
		);

		$this->add_group_control(
			The7_Group_Control_Query::get_type(),
			[
				'name'            => The7_Shortcode_Adapter_Interface::QUERY_CONTROL_NAME,
				'query_post_type' => 'product',
				'presets'         => [ 'include', 'exclude', 'order' ],
				'fields_options'  => [
					'post_type' => [
						'default' => 'product',
						'options' => [
							'current_query'   => __( 'Current Query', 'the7mk2' ),
							'product'         => __( 'Latest Products', 'the7mk2' ),
							'sale'            => __( 'Sale', 'the7mk2' ),
							'top'             => __( 'Top rated products', 'the7mk2' ),
							'best_selling'    => __( 'Best selling', 'the7mk2' ),
							'featured'        => __( 'Featured', 'the7mk2' ),
							'by_id'           => _x( 'Manual Selection', 'Posts Query Control', 'the7mk2' ),
							'related'         => __( 'Related Products', 'the7mk2' ),
							'recently_viewed' => __( 'Recently Viewed', 'the7mk2' ),
						],
					],
					'orderby'   => [
						'default' => 'date',
						'options' => [
							'date'       => __( 'Date', 'the7mk2' ),
							'title'      => __( 'Title', 'the7mk2' ),
							'price'      => __( 'Price', 'the7mk2' ),
							'popularity' => __( 'Popularity', 'the7mk2' ),
							'rating'     => __( 'Rating', 'the7mk2' ),
							'rand'       => __( 'Random', 'the7mk2' ),
							'menu_order' => __( 'Menu Order', 'the7mk2' ),
						],
					],
					'exclude'   => [
						'options' => [
							'current_post'     => __( 'Current Post', 'the7mk2' ),
							'manual_selection' => __( 'Manual Selection', 'the7mk2' ),
							'terms'            => __( 'Term', 'the7mk2' ),
						],
					],
					'include'   => [
						'options' => [
							'terms' => __( 'Term', 'the7mk2' ),
						],
					],
				],
				'exclude'         => [
					'posts_per_page',
					'exclude_authors',
					'authors',
					'offset',
					'related_fallback',
					'related_ids',
					'query_id',
					'avoid_duplicates',
					'ignore_sticky_posts',
				],
			]
		);

		$this->end_controls_section();
	}

}
