<?php

namespace The7\Mods\Compatibility\Elementor\Pro\Modules\Woocommerce;

use WC_Widget_Layered_Nav;

class WC_Widget_Nav extends WC_Widget_Layered_Nav {

	public function get_filtered_term_product_counts( $term_ids, $taxonomy, $query_type ) {
		return parent::get_filtered_term_product_counts( $term_ids, $taxonomy, $query_type );
	}

	public function get_instance_taxonomy( $instance ) {
		return parent::get_instance_taxonomy( $instance );
	}

	public function get_current_page_url() {
		return parent::get_current_page_url(  );
	}

	/**
	 * Return the currently viewed taxonomy name.
	 *
	 * @return string
	 */
	public function get_current_taxonomy() {
		return parent::get_current_taxonomy();
	}

	/**
	 * Return the currently viewed term ID.
	 *
	 * @return int
	 */
	public function get_current_term_id() {
		return parent::get_current_term_id();
	}

	/**
	 * Return the currently viewed term slug.
	 *
	 * @return int
	 */
	public function get_current_term_slug() {
		return parent::get_current_term_slug();
	}
}