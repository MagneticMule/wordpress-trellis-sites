<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Style;

use The7\Mods\Compatibility\Elementor\Widgets\The7_Elementor_Elements_Widget;

defined( 'ABSPATH' ) || exit;

trait Pagination_Style {

	/**
	 * @param string    $loading_mode
	 * @param \WP_Query $query
	 */
	protected function display_pagination( $loading_mode, \WP_Query $query ) {
		if ( 'standard' === $loading_mode ) {
			$this->display_standard_pagination( $query->max_num_pages, $this->get_pagination_wrap_class() );
		} elseif ( in_array( $loading_mode, [ 'js_more', 'js_lazy_loading' ], true ) ) {
			$this->display_load_more_button( $this->get_pagination_wrap_class( 'paginator-more-button' ) );
		} elseif ( 'js_pagination' === $loading_mode ) {
			echo '<div class="' . esc_attr( $this->get_pagination_wrap_class() ) . '" role="navigation"></div>';
		}
	}

	/**
	 * @param int    $max_num_pages
	 * @param string $class
	 */
	protected function display_standard_pagination( $max_num_pages, $class = 'paginator' ) {
		$add_pagination_filter = has_filter( 'dt_paginator_args', 'presscore_paginator_show_all_pages_filter' );
		remove_filter( 'dt_paginator_args', 'presscore_paginator_show_all_pages_filter' );

		$num_pages = $this->get_settings_for_display( 'show_all_pages' ) ? 9999 : 5;
		$item_class = 'page-numbers filter-item';
		$no_next = '';
		$no_prev = '';
		$prev_text = '<i class="dt-icon-the7-arrow-35-1" aria-hidden="true"></i>';
		$next_text = '<i class="dt-icon-the7-arrow-35-2" aria-hidden="true"></i>';

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
	 * @param string $class
	 *
	 * @return string
	 */
	protected function get_pagination_wrap_class( $class = '' ) {
		$settings = $this->get_settings_for_display();

		$wrap_class = [ 'paginator', 'filter-decorations', $class ];
		if ( $settings['pagination_style'] ) {
			$wrap_class[] = 'filter-pointer-' . $settings['pagination_style'];

			foreach ( $settings as $key => $value ) {
				if ( 0 === strpos( $key, 'pagination_animation' ) && $value ) {
					$wrap_class[] = 'filter-animation-' . $value;
					break;
				}
			}
		}

		return implode( ' ', array_filter( $wrap_class ) );
	}

	protected function display_load_more_button( $class = 'paginator-more-button' ) {
		echo dt_get_next_page_button(
			2,
			$class,
			$cur_page = 1,
			'highlighted filter-item',
			$this->get_settings_for_display( 'pagination_load_more_text' ),
			$this->get_elementor_icon_html( $this->get_settings_for_display( 'pagination_load_more_icon' ) ),
			$this->get_settings_for_display( 'pagination_load_more_icon_position' )
		);
	}
}
