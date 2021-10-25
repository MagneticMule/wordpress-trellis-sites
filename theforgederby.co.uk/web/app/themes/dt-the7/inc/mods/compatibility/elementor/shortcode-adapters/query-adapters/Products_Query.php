<?php

namespace The7\Mods\Compatibility\Elementor\Shortcode_Adapters\Query_Adapters;

use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\Query_Interface;
use The7\Mods\Compatibility\Elementor\With_Pagination;
use The7\Inc\Mods\Compatibility\WooCommerce\Front\Recently_Viewed_Products;

class Products_Query extends Query_Interface {

	use With_Pagination;

	/**
	 * Create a new WP_Qury instance.
	 *
	 * @return mixed|\WP_Query
	 */
	public function create() {
		if ( 'current_query' === $this->get_att( 'query_post_type' ) ) {
			return $GLOBALS['wp_query'];
		}

		add_action( 'pre_get_posts', array( $this, 'add_offset' ), 1 );
		add_filter( 'found_posts', array( $this, 'fix_pagination' ), 1, 2 );

		$query = new \WP_Query( $this->parse_query_args() );

		remove_action( 'pre_get_posts', array( $this, 'add_offset' ), 1 );
		remove_filter( 'found_posts', array( $this, 'fix_pagination' ), 1 );
		remove_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );

		return $query;
	}

	public function parse_query_args() {
		// Get order + orderby args from string.
		$orderby_value = explode( '-', $this->get_att( $this->query_prefix . 'orderby' ) );
		$orderby       = esc_attr( $orderby_value[0] );
		$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : strtoupper( $this->get_att( $this->query_prefix . 'order' ) );

		$query_args = [
			'post_type'           => 'product',
			'ignore_sticky_posts' => true,
			'orderby'             => $orderby,
			'order'               => $order,
		];

		$query_args['meta_query'] = WC()->query->get_meta_query();
		$query_args['tax_query']  = [];

		$loading_mode                 = $this->get_att( 'loading_mode', 'disabled' );
		$query_args['posts_per_page'] = (int) $this->get_posts_per_page( $loading_mode, $this->atts );
		if ( 'standard' === $loading_mode ) {
			$query_args['paged'] = the7_get_paged_var();
		}

		// Visibility.
		$this->set_visibility_query_args( $query_args );

		// Featured.
		$this->set_featured_query_args( $query_args );

		// Sale.
		$this->set_sale_products_query_args( $query_args );

		// Best sellings.
		$this->set_best_sellings_products_query_args( $query_args );

		// Top rated.
		$this->set_top_rated_products_query_args( $query_args );

		// IDs.
		$this->set_ids_query_args( $query_args );

		// Categories & Tags.
		$this->set_terms_query_args( $query_args );

		// Exclude.
		$this->set_exclude_query_args( $query_args );

		// Related.
		$this->set_related_products_query_args( $query_args );

		// Recently viewed.
		$this->set_recently_viewed_query_args( $query_args );

		$ordering_args         = WC()->query->get_catalog_ordering_args( $query_args['orderby'], $query_args['order'] );

		$query_args['orderby'] = $ordering_args['orderby'];
		$query_args['order']   = $ordering_args['order'];
		if ( $ordering_args['meta_key'] ) {
			$query_args['meta_key'] = $ordering_args['meta_key']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		}

		$query_args = apply_filters( 'the7_woocommerce_widget_products_query', $query_args );

		// Load only id fileds.
		$query_args['fields'] = [ 'ids' ];

		return $query_args;
	}

	protected function set_visibility_query_args( &$query_args ) {
		$query_args['tax_query'] = array_merge( $query_args['tax_query'], WC()->query->get_tax_query() ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}

	protected function set_featured_query_args( &$query_args ) {
		if ( 'featured' === $this->get_att( $this->query_prefix . 'post_type' ) ) {
			$product_visibility_term_ids = wc_get_product_visibility_term_ids();

			$query_args['tax_query'][] = [
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => [ $product_visibility_term_ids['featured'] ],
			];
		}
	}

	protected function set_sale_products_query_args( &$query_args ) {
		if ( 'sale' === $this->get_att( $this->query_prefix . 'post_type' ) ) {
			$query_args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
		}
	}

	protected function set_ids_query_args( &$query_args ) {

		switch ( $this->get_att( $this->query_prefix . 'post_type' ) ) {
			case 'by_id':
				$post__in = $this->get_att( $this->query_prefix . 'posts_ids' );
				break;
			case 'sale':
				$post__in = wc_get_product_ids_on_sale();
				break;
		}

		if ( ! empty( $post__in ) ) {
			$query_args['post__in'] = $post__in;
		}
	}

	private function set_terms_query_args( &$query_args ) {

		$query_type = $this->get_att( $this->query_prefix . 'post_type' );

		if ( in_array( $query_type, [ 'current_query', 'by_id', 'related' ], true ) ) {
			return;
		}

		if ( empty( $this->get_att( $this->query_prefix . 'include' ) ) || empty( $this->get_att( $this->query_prefix . 'include_term_ids' ) ) || ! in_array( 'terms', $this->get_att( $this->query_prefix . 'include' ), true ) ) {
			return;
		}

		$terms = [];
		foreach ( $this->get_att( $this->query_prefix . 'include_term_ids' ) as $id ) {
			$term_data = get_term_by( 'term_taxonomy_id', $id );
			if ( isset( $term_data->taxonomy ) ) {
				$terms[ $term_data->taxonomy ][] = $id;
			}
		}
		$tax_query = [];
		foreach ( $terms as $taxonomy => $ids ) {
			$tax_query[] = [
				'taxonomy' => $taxonomy,
				'field'    => 'term_taxonomy_id',
				'terms'    => $ids,
			];
		}

		if ( ! empty( $tax_query ) ) {
			$query_args['tax_query'] = array_merge( $query_args['tax_query'], $tax_query );
		}
	}

	protected function set_exclude_query_args( &$query_args ) {
		if ( empty( $this->get_att( $this->query_prefix . 'exclude' ) ) ) {
			return;
		}
		$post__not_in = [];
		if ( in_array( 'current_post', $this->get_att( $this->query_prefix . 'exclude' ) ) ) {
			if ( is_singular() ) {
				$post__not_in[] = get_queried_object_id();
			}
		}

		if ( in_array( 'manual_selection', $this->get_att( $this->query_prefix . 'exclude' ) ) && ! empty( $this->get_att( $this->query_prefix . 'exclude_ids' ) ) ) {
			$post__not_in = array_merge( $post__not_in, $this->get_att( $this->query_prefix . 'exclude_ids' ) );
		}

		$query_args['post__not_in'] = empty( $query_args['post__not_in'] ) ? $post__not_in : array_merge( $query_args['post__not_in'], $post__not_in );

		/**
		 * WC populates `post__in` with the ids of the products that are on sale.
		 * Since WP_Query ignores `post__not_in` once `post__in` exists, the ids are filtered manually, using `array_diff`.
		 */
		if ( 'sale' === $this->get_att( $this->query_prefix . 'post_type' ) ) {
			$query_args['post__in'] = array_diff( $query_args['post__in'], $query_args['post__not_in'] );
		}

		if ( in_array( 'terms', $this->get_att( $this->query_prefix . 'exclude' ) ) && ! empty( $this->get_att( $this->query_prefix . 'exclude_term_ids' ) ) ) {
			$terms = [];
			foreach ( $this->get_att( $this->query_prefix . 'exclude_term_ids' ) as $to_exclude ) {
				$term_data                       = get_term_by( 'term_taxonomy_id', $to_exclude );
				$terms[ $term_data->taxonomy ][] = $to_exclude;
			}
			$tax_query = [];
			foreach ( $terms as $taxonomy => $ids ) {
				$tax_query[] = [
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $ids,
					'operator' => 'NOT IN',
				];
			}
			if ( empty( $query_args['tax_query'] ) ) {
				$query_args['tax_query'] = $tax_query;
			} else {
				$query_args['tax_query']['relation'] = 'AND';
				$query_args['tax_query'][]           = $tax_query;
			}
		}
	}

	protected function set_best_sellings_products_query_args( &$query_args ) {
		if ( 'best_selling' === $this->get_att( $this->query_prefix . 'post_type' ) ) {
			$query_args['meta_key'] = 'total_sales';
			$query_args['orderby']  = 'meta_value_num';
		}
	}

	protected function set_top_rated_products_query_args( &$query_args ) {
		if ( 'top' === $this->get_att( $this->query_prefix . 'post_type' ) ) {
			add_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );
			$query_args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$query_args['orderby']  = 'meta_value_num';
		}
	}

	protected function set_related_products_query_args( &$query_args ) {
		global $product;

		if ( 'related' !== $this->get_att( $this->query_prefix . 'post_type' ) ) {
			return;
		}

		$product = wc_get_product();

		if ( ! $product ) {
			return;
		}

		$posts_per_page = ! empty( $query_args['posts_per_page'] ) ? $query_args['posts_per_page'] : 9999;

		// Get visible related products then sort them at random.
		$products = array_filter(
			array_map(
				'wc_get_product',
				wc_get_related_products( $product->get_id(), $posts_per_page, $product->get_upsell_ids() )
			),
			'wc_products_array_filter_visible'
		);

		// Handle orderby.
		$products = wc_products_array_orderby( $products, $query_args['orderby'], $query_args['order'] );

		if ( $products ) {
			$query_args['post__in']   = array_map(
				function ( $p ) {
					return $p->get_id();
				},
				$products
			);
			$query_args['meta_query'] = [];
			$query_args['tax_query']  = [];
			$query_args['orderby']    = 'post__in';
		} else {
			$query_args['orderby'] = 'rand';
		}
	}

	/**
	 * Set the query args for the recently viewed products.
	 *
	 * @see woocommerce/includes/widgets/class-wc-widget-recently-viewed.php
	 *
	 * @param array $query_args WP_Query args.
	 */
	protected function set_recently_viewed_query_args( &$query_args ) {
		if ( 'recently_viewed' !== $this->get_att( $this->query_prefix . 'post_type' ) ) {
			return;
		}

		$viewed_products = array_reverse( Recently_Viewed_Products::get() );

		if ( empty( $viewed_products ) ) {
			$viewed_products[] = 0;
		}

		$query_args = array_merge(
			$query_args,
			[
				'post__in' => $viewed_products,
				'orderby'  => 'post__in',
			]
		);

		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$query_args['tax_query']['relation'] = 'AND';
			$query_args['tax_query'][]           = [
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'outofstock',
				'operator' => 'NOT IN',
			]; // WPCS: slow query ok.
		}
	}

	protected function get_posts_offset() {
		return (int) $this->get_att( 'posts_offset', 0 );
	}

	/**
	 * Add offset to the posts query.
	 *
	 * @param WP_Query $query
	 *
	 * @since 1.15.0
	 */
	public function add_offset( $query ) {
		$offset  = $this->get_posts_offset();
		$ppp     = (int) $query->query_vars['posts_per_page'];
		$current = (int) $query->query_vars['paged'];

		if ( $query->is_paged ) {
			$page_offset = $offset + ( $ppp * ( $current - 1 ) );
			$query->set( 'offset', $page_offset );
		} else {
			$query->set( 'offset', $offset );
		}
	}

	/**
	 * Fix pagination accordingly with posts offset.
	 *
	 * @param int $found_posts
	 *
	 * @return int
	 */
	public function fix_pagination( $found_posts ) {
		return $found_posts - $this->get_posts_offset();
	}
}
