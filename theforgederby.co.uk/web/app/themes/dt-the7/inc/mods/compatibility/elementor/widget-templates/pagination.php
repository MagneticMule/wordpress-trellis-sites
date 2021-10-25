<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates;

use The7\Mods\Compatibility\Elementor\Widget_Templates\Abstract_Template;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Class Pagination
 *
 * @package The7\Mods\Compatibility\Elementor\Widget_Templates
 */
class Pagination extends Abstract_Template {

	protected $loading_mode;

	/**
	 * Render pagination.
	 *
	 * @param int $max_num_pages Max num pages.
	 */
	public function render( $max_num_pages ) {
		$loading_mode = $this->get_loading_mode();

		if ( 'standard' === $loading_mode ) {
			$this->render_standard_pagination( $max_num_pages, $this->get_pagination_wrap_class() );
		} elseif ( in_array( $loading_mode, [ 'js_more', 'js_lazy_loading' ], true ) ) {
			$this->render_load_more_button( $this->get_pagination_wrap_class( 'paginator-more-button' ) );
		} elseif ( 'js_pagination' === $loading_mode ) {
			echo '<div class="' . esc_attr( $this->get_pagination_wrap_class() ) . '" role="navigation"></div>';
		}
	}

	/**
	 * Render standard pagination.
	 *
	 * @param int    $max_num_pages Max num pages.
	 * @param string $class         Paginator class.
	 */
	public function render_standard_pagination( $max_num_pages, $class = 'paginator' ) {
		$add_pagination_filter = has_filter( 'dt_paginator_args', 'presscore_paginator_show_all_pages_filter' );
		remove_filter( 'dt_paginator_args', 'presscore_paginator_show_all_pages_filter' );

		$num_pages  = $this->get_settings( 'show_all_pages' ) ? 9999 : 5;
		$item_class = 'page-numbers filter-item';
		$no_next    = '';
		$no_prev    = '';
		$prev_text  = '<i class="dt-icon-the7-arrow-35-1" aria-hidden="true"></i>';
		$next_text  = '<i class="dt-icon-the7-arrow-35-2" aria-hidden="true"></i>';

		dt_paginator(
			null,
			compact(
				'max_num_pages',
				'class',
				'num_pages',
				'item_class',
				'no_next',
				'no_prev',
				'prev_text',
				'next_text'
			)
		);

		$add_pagination_filter && add_filter( 'dt_paginator_args', 'presscore_paginator_show_all_pages_filter' );
	}

	/**
	 * Render load more button.
	 *
	 * @param string $class Paginator class.
	 */
	public function render_load_more_button( $class = 'paginator-more-button' ) {
		ob_start();
		Icons_Manager::render_icon( $this->get_settings( 'pagination_load_more_icon' ), [ 'aria-hidden' => 'true' ] );
		$icon = ob_get_clean();

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo dt_get_next_page_button(
			2,
			$class,
			$cur_page = 1,
			'highlighted filter-item',
			$this->get_settings( 'pagination_load_more_text' ),
			$icon,
			$this->get_settings( 'pagination_load_more_icon_position' )
		);
	}

	/**
	 * Add container data attributes.
	 *
	 * @param array $data Data attributes array.
	 *
	 * @return array
	 */
	public function add_containter_data( $data ) {
		$loading_mode         = $this->get_loading_mode();
		$data_pagination_mode = 'none';
		if ( in_array( $loading_mode, [ 'js_more', 'js_lazy_loading' ], true ) ) {
			$data_pagination_mode = 'load-more';
		} elseif ( $loading_mode === 'js_pagination' ) {
			$data_pagination_mode = 'pages';
		} elseif ( $loading_mode === 'standard' ) {
			$data_pagination_mode = 'standard';
		}

		$data['data-post-limit']      = (int) ( $this->get_post_limit() );
		$data['data-pagination-mode'] = $data_pagination_mode;

		return $data;
	}

	/**
	 * Returns post limit based on loading mode.
	 *
	 * @return string|int
	 */
	public function get_post_limit() {
		$post_limit = '-1';
		switch ( $this->get_loading_mode() ) {
			case 'js_pagination':
				$post_limit = $this->get_settings( 'jsp_posts_per_page' ) ?: get_option( 'posts_per_page' );
				break;
			case 'js_more':
				$post_limit = $this->get_settings( 'jsm_posts_per_page' ) ?: get_option( 'posts_per_page' );
				break;
			case 'js_lazy_loading':
				$post_limit = $this->get_settings( 'jsl_posts_per_page' ) ?: get_option( 'posts_per_page' );
				break;
		}

		return $post_limit;
	}

	/**
	 * Return pagination wrapper common classes.
	 *
	 * @param string $class Custom class.
	 *
	 * @return string
	 */
	public function get_pagination_wrap_class( $class = '' ) {
		$wrap_class = [ 'paginator', 'filter-decorations', $class ];
		if ( $this->get_settings( 'pagination_style' ) ) {
			$wrap_class[] = 'filter-pointer-' . $this->get_settings( 'pagination_style' );

			foreach ( $this->get_settings() as $key => $value ) {
				if ( $value && 0 === strpos( $key, 'pagination_animation' ) ) {
					$wrap_class[] = 'filter-animation-' . $value;
					break;
				}
			}
		}

		return implode( ' ', array_filter( $wrap_class ) );
	}

	/**
	 * Register pagination content controls.
	 *
	 * @param string $query_control_name Query control name.
	 */
	public function add_content_controls( $query_control_name ) {
		$this->widget->start_controls_section(
			'pagination',
			[
				'label' => __( 'Pagination', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->widget->add_control(
			'loading_mode',
			[
				'label'     => __( 'Pagination mode', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'disabled',
				'options'   => [
					'disabled'        => 'Disabled',
					'standard'        => 'Standard',
					'js_pagination'   => 'JavaScript pages',
					'js_more'         => '"Load more" button',
					'js_lazy_loading' => 'Infinite scroll',
				],
				'condition' => [
					"{$query_control_name}!" => [ 'current_query', 'related' ],
				],
			]
		);

		$this->widget->add_control(
			'pagination_load_more_text',
			[
				'label'       => __( 'Button Text', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Load more', 'the7mk2' ),
				'placeholder' => '',
				'condition'   => [
					'loading_mode'           => 'js_more',
					"{$query_control_name}!" => 'current_query',
				],
			]
		);

		$this->widget->add_control(
			'pagination_show_load_more_icon',
			[
				'label'        => __( 'Icon', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'loading_mode'           => 'js_more',
					"{$query_control_name}!" => 'current_query',
				],
			]
		);

		$this->widget->add_control(
			'pagination_load_more_icon',
			[
				'label'     => '',
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-arrow-circle-down',
					'library' => 'fa-solid',
				],
				'condition' => [
					'loading_mode'                   => 'js_more',
					'pagination_show_load_more_icon' => 'y',
					"{$query_control_name}!"         => 'current_query',
				],
			]
		);

		$this->widget->add_control(
			'pagination_load_more_icon_position',
			[
				'label'     => __( 'Icon Position', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'toggle'    => false,
				'default'   => 'before',
				'options'   => [
					'before' => __( 'Before', 'the7mk2' ),
					'after'  => __( 'After', 'the7mk2' ),
				],
				'condition' => [
					'loading_mode'                   => 'js_more',
					'pagination_show_load_more_icon' => 'y',
					"{$query_control_name}!"         => 'current_query',
				],
			]
		);

		$this->widget->add_control(
			'pagination_load_more_icon_spacing',
			[
				'label'      => __( 'Icon Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -200,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .paginator a.button-load-more i:first-child' => 'margin: 0 {{SIZE}}{{UNIT}} 0 0;',
					'{{WRAPPER}} .paginator a.button-load-more i:last-child'  => 'margin: 0 0 0 {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'loading_mode'                   => 'js_more',
					'pagination_show_load_more_icon' => 'y',
					"{$query_control_name}!"         => 'current_query',
				],
			]
		);

		// Disabled pagination.
		$this->widget->add_control(
			'dis_posts_total',
			[
				'label'       => __( 'Total Number Of Posts', 'the7mk2' ),
				'description' => __( 'Leave empty to display all posts.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'conditions'  => [
					'relation' => 'or',
					'terms'    => [
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'loading_mode',
									'operator' => '==',
									'value'    => 'disabled',
								],
								[
									'name'     => $query_control_name,
									'operator' => '!==',
									'value'    => 'current_query',
								],
							],
						],
						[
							'name'     => $query_control_name,
							'operator' => '==',
							'value'    => 'related',
						],
					],
				],
			]
		);

		// Standard pagination.
		$this->widget->add_control(
			'st_posts_per_page',
			[
				'label'       => __( 'Posts Per Page', 'the7mk2' ),
				'description' => __(
					'Leave empty to use value from the WP Reading settings. Set "-1" to show all posts.',
					'the7mk2'
				),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'loading_mode'           => 'standard',
					"{$query_control_name}!" => [ 'current_query', 'related' ],
				],
			]
		);

		// JS pagination.
		$this->widget->add_control(
			'jsp_posts_total',
			[
				'label'       => __( 'Total Number Of Posts', 'the7mk2' ),
				'description' => __( 'Leave empty to display all posts.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'loading_mode'           => 'js_pagination',
					"{$query_control_name}!" => [ 'current_query', 'related' ],
				],
			]
		);

		$this->widget->add_control(
			'jsp_posts_per_page',
			[
				'label'       => __( 'Posts Per Page', 'the7mk2' ),
				'description' => __( 'Leave empty to use value from the WP Reading settings.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'loading_mode'           => 'js_pagination',
					"{$query_control_name}!" => [ 'current_query', 'related' ],
				],
			]
		);

		// JS load more.
		$this->widget->add_control(
			'jsm_posts_total',
			[
				'label'       => __( 'Total Number Of Posts', 'the7mk2' ),
				'description' => __( 'Leave empty to display all posts.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'loading_mode'           => 'js_more',
					"{$query_control_name}!" => [ 'current_query', 'related' ],
				],
			]
		);

		$this->widget->add_control(
			'jsm_posts_per_page',
			[
				'label'       => __( 'Posts Per Page', 'the7mk2' ),
				'description' => __( 'Leave empty to use value from the WP Reading settings.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'loading_mode'           => 'js_more',
					"{$query_control_name}!" => [ 'current_query', 'related' ],
				],
			]
		);

		// JS infinite scroll.
		$this->widget->add_control(
			'jsl_posts_total',
			[
				'label'       => __( 'Total Number Of Posts', 'the7mk2' ),
				'description' => __( 'Leave empty to display all posts.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'loading_mode'           => 'js_lazy_loading',
					"{$query_control_name}!" => [ 'current_query', 'related' ],
				],
			]
		);

		$this->widget->add_control(
			'jsl_posts_per_page',
			[
				'label'       => __( 'Posts Per Page', 'the7mk2' ),
				'description' => __( 'Leave empty to use value from the WP Reading settings.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'loading_mode'           => 'js_lazy_loading',
					"{$query_control_name}!" => [ 'current_query', 'related' ],
				],
			]
		);

		// Posts offset.
		$this->widget->add_control(
			'posts_offset',
			[
				'label'       => __( 'Posts Offset', 'the7mk2' ),
				'description' => __(
					'Offset for posts query (i.e. 2 means, posts will be displayed starting from the third post).',
					'the7mk2'
				),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'condition'   => [
					"{$query_control_name}!" => [ 'current_query', 'related' ],
				],
			]
		);

		$this->widget->add_control(
			'show_all_pages',
			[
				'label'        => __( 'Show All Pages In Paginator', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => '',
				'conditions'   => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'loading_mode',
							'operator' => 'in',
							'value'    => [ 'standard', 'js_pagination' ],
						],
						[
							'name'     => $query_control_name,
							'operator' => 'in',
							'value'    => [ 'current_query' ],
						],
					],
				],
			]
		);

		$this->widget->end_controls_section();
	}

	/**
	 * Register pagination style controls.
	 */
	public function add_style_controls( $query_control_name ) {
		$this->widget->start_controls_section(
			'pagination_style_tab',
			[
				'label'      => __( 'Pagination', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'loading_mode',
							'operator' => 'in',
							'value'    => [ 'standard', 'js_pagination', 'js_more' ],
						],
						[
							'name'     => $query_control_name,
							'operator' => 'in',
							'value'    => [ 'current_query' ],
						],
					],
				],
			]
		);

		$this->widget->add_control(
			'pagination_position',
			[
				'label'                => __( 'Align', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'toggle'               => false,
				'default'              => 'center',
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
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
				'selectors'            => [
					'{{WRAPPER}} .paginator' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->widget->add_control(
			'pagination_style',
			[
				'label'          => __( 'Pointer', 'the7mk2' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => 'underline',
				'options'        => [
					'none'        => __( 'None', 'the7mk2' ),
					'underline'   => __( 'Underline', 'the7mk2' ),
					'overline'    => __( 'Overline', 'the7mk2' ),
					'double-line' => __( 'Double Line', 'the7mk2' ),
					'framed'      => __( 'Framed', 'the7mk2' ),
					'background'  => __( 'Background', 'the7mk2' ),
					'text'        => __( 'Text', 'the7mk2' ),
				],
				'style_transfer' => true,
			]
		);

		$this->widget->add_control(
			'pagination_animation_line',
			[
				'label'     => __( 'Animation', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => [
					'fade'     => 'Fade',
					'slide'    => 'Slide',
					'grow'     => 'Grow',
					'drop-in'  => 'Drop In',
					'drop-out' => 'Drop Out',
					'none'     => 'None',
				],
				'condition' => [
					'pagination_style' => [ 'underline', 'overline', 'double-line' ],
					'loading_mode!'    => 'js_more',
				],
			]
		);

		$this->widget->add_control(
			'pagination_animation_framed',
			[
				'label'     => __( 'Animation', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => [
					'fade'    => 'Fade',
					'grow'    => 'Grow',
					'shrink'  => 'Shrink',
					'draw'    => 'Draw',
					'corners' => 'Corners',
					'none'    => 'None',
				],
				'condition' => [
					'pagination_style' => 'framed',
					'loading_mode!'    => 'js_more',
				],
			]
		);

		$this->widget->add_control(
			'pagination_animation_background',
			[
				'label'     => __( 'Animation', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => [
					'fade'                   => 'Fade',
					'grow'                   => 'Grow',
					'shrink'                 => 'Shrink',
					'sweep-left'             => 'Sweep Left',
					'sweep-right'            => 'Sweep Right',
					'sweep-up'               => 'Sweep Up',
					'sweep-down'             => 'Sweep Down',
					'shutter-in-vertical'    => 'Shutter In Vertical',
					'shutter-out-vertical'   => 'Shutter Out Vertical',
					'shutter-in-horizontal'  => 'Shutter In Horizontal',
					'shutter-out-horizontal' => 'Shutter Out Horizontal',
					'none'                   => 'None',
				],
				'condition' => [
					'pagination_style' => 'background',
					'loading_mode!'    => 'js_more',
				],
			]
		);

		$this->widget->add_control(
			'pagination_animation_text',
			[
				'label'     => __( 'Animation', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'grow',
				'options'   => [
					'grow'   => 'Grow',
					'shrink' => 'Shrink',
					'sink'   => 'Sink',
					'float'  => 'Float',
					'skew'   => 'Skew',
					'rotate' => 'Rotate',
					'none'   => 'None',
				],
				'condition' => [
					'pagination_style' => 'text',
					'loading_mode!'    => 'js_more',
				],
			]
		);

		$this->widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'pagination_typography',
				'label'    => __( 'Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .paginator a, {{WRAPPER}} .paginator .button-load-more',
				'exclude'  => [
					'text_decoration',
				],
			]
		);

		$this->widget->add_control(
			'pagination_underline_height',
			[
				'label'      => __( 'Pointer Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 20,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .paginator' => '--filter-pointer-border-width: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'pagination_style!' => [ 'background', 'none' ],
				],
			]
		);

		$this->widget->start_controls_tabs( 'pagination_elements_style' );

		$this->widget->start_controls_tab(
			'pagination_normal_style',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->widget->add_control(
			'pagination_text_color',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .paginator' => '--filter-title-color-normal: {{VALUE}}',
				],
			]
		);

		$this->widget->add_control(
			'pagination_pointer_normal_color',
			[
				'label'     => __( 'Pointer', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .paginator' => '--filter-pointer-bg-color-normal: {{VALUE}};',
				],
				'condition' => [
					'loading_mode' => 'js_more',
				],
			]
		);

		$this->widget->end_controls_tab();

		$this->widget->start_controls_tab(
			'pagination_hover_style',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->widget->add_control(
			'pagination_text_hover_color',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .paginator a' => '--filter-title-color-hover: {{VALUE}}',
				],
			]
		);

		$this->widget->add_control(
			'pagination_pointer_hover_color',
			[
				'label'     => __( 'Pointer', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .paginator a' => '--filter-pointer-bg-color-hover: {{VALUE}};',
				],
			]
		);

		$this->widget->end_controls_tab();

		$this->widget->start_controls_tab(
			'pagination_active_style',
			[
				'label'     => __( 'Active', 'the7mk2' ),
				'condition' => [
					'loading_mode!' => 'js_more',
				],
			]
		);

		$this->widget->add_control(
			'pagination_text_active_color',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .paginator a' => '--filter-title-color-active: {{VALUE}}',
				],
			]
		);

		$this->widget->add_control(
			'pagination_pointer_active_color',
			[
				'label'     => __( 'Pointer', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .paginator a'                  => '--filter-pointer-bg-color-active: {{VALUE}};',
					'{{WRAPPER}} .paginator a.button-load-more' => '--filter-pointer-bg-color-active: {{VALUE}};',
				],
			]
		);

		$this->widget->end_controls_tab();

		$this->widget->end_controls_tabs();

		$this->widget->add_control(
			'pagination_bg_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
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
					'{{WRAPPER}} .paginator' => '--filter-pointer-bg-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition'  => [
					'pagination_style' => 'background',
				],
			]
		);

		$this->widget->add_control(
			'pagination_element_padding',
			[
				'label'      => __( 'Padding', 'the7mk2' ),
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
					'{{WRAPPER}} .paginator a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->widget->add_control(
			'pagination_element_margin',
			[
				'label'      => __( 'Margin', 'the7mk2' ),
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
					'{{WRAPPER}} .paginator a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->widget->add_control(
			'gap_before_pagination',
			[
				'label'      => __( 'Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
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
					'{{WRAPPER}} .paginator' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'loading_mode' => [ 'standard', 'js_pagination', 'js_more' ],
				],
			]
		);

		$this->widget->end_controls_section();
	}

	/**
	 * @return mixed
	 */
	public function get_loading_mode() {
		return $this->loading_mode ?: $this->get_settings( 'loading_mode' );
	}

	public function set_loading_mode( $loading_mode ) {
		$this->loading_mode = $loading_mode;
	}

}
