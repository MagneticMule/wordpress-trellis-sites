<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

$terms        = isset( $terms ) ? $terms : [];
$current_term = isset( $current_term ) ? (string) $current_term : '';
$filter_class = isset( $filter_class ) ? $filter_class : [];

?>

<div class="<?php echo esc_attr( implode( ' ', $filter_class ) ); ?>">

	<?php
	if ( ! empty( $show_catalog_ordering ) ) {
		echo '<div class="the7-wc-catalog-ordering">';
		woocommerce_catalog_ordering();
		echo '</div>';
	}

	if ( $terms ) {
		echo '<div class="filter-categories">';

		foreach ( $terms as $term_obj ) {
			$class = 'filter-item';
			if ( in_array( $current_term, [ (string) $term_obj->term_id, (string) $term_obj->slug ], true ) ) {
				$class .= ' act';
			}

			printf(
				'<a href="%s" class="%s" data-filter="%s">%s</a>',
				$term_obj->filter_url,
				$class,
				".category-{$term_obj->term_id}",
				$term_obj->name
			);
		}

		echo '</div>';
	}

	if ( ! empty( $show_result_count ) ) {
		echo '<div class="the7-wc-results-count">';
		woocommerce_result_count();
		echo '</div>';
	}
	?>

</div>
