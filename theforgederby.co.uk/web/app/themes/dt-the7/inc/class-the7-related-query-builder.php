<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Related_Query_Builder extends The7_Query_Builder {

	protected $related_post_id;

	public function __construct( $query_args ) {
		parent::__construct( $query_args );

		$post_id = get_queried_object_id();
		$this->related_post_id = is_singular() && ( 0 !== $post_id ) ? $post_id : null;
		$this->query_args['post_type'] = get_post_type( $this->related_post_id );
		if ( $this->related_post_id ) {
			$this->query_args['post__not_in'] = [ $this->related_post_id ];
		}
	}

	public function from_terms( $taxonomy, $terms = array(), $field = 'term_id' ) {
		$this->requested_taxonomy = $taxonomy;

		if ( $taxonomy && $this->related_post_id) {
			$terms = wp_get_post_terms( $this->related_post_id, $taxonomy, [ 'fields' => 'ids' ] );
			if ( ! is_wp_error( $terms ) ) {
				$this->tax_query = compact( 'taxonomy', 'terms', 'field' );
			}
		}

		return $this;
	}

	public function query() {
		if ( ! $this->related_post_id ) {
			return new WP_Query();
		}

		return parent::query();
	}

}
