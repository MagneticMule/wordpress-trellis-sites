<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;
?>

<h2 class="nav-tab-wrapper hide-if-js" style="display: block;">
	<?php
	$tabs      = array(
		'full-site'   => _x( 'Full site', 'admin', 'the7mk2' ),
		'single-page' => _x( 'Single page', 'admin', 'the7mk2' ),
	);
	$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'full-site-tab';
	foreach ( $tabs as $id => $title ) {
	    $tab_query_arg = "{$id}-tab";
		$act_class = $tab_query_arg === $active_tab ? 'nav-tab-active' : '';
		$tab_url = add_query_arg( array( 'tab' => $tab_query_arg ) );
		printf( '<a id="%1$s-tab" class="nav-tab %2$s" title="%3$s" href="%4$s">%3$s</a>', $id, $act_class, $title, esc_url( $tab_url ) );
	}
	?>
</h2>

<div class="tabs-holder hidden">
    <div id="full-site-group" class="group">
		<?php presscore_get_template_part( 'the7_admin', 'partials/the7-demo-content/full-import/page' ) ?>
    </div>
    <div id="single-page-group" class="group the7-import-by-url-page" data-post-type="page">
		<div class="the7-import-page">
			<?php presscore_get_template_part( 'the7_admin', 'partials/the7-demo-content/url-import/page', null, [ 'the7_import_post_type' => 'Page' ] ) ?>
		</div>
    </div>
</div>
