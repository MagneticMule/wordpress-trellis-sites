<?php
/**
 * Required plugins view.
 *
 * @package The7
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * @var The7_Demo $demo
 */
?>

<div class="dt-dummy-controls-block">
    <div class="dt-dummy-required-plugins">
        <p>
            <strong><?php esc_html_e( 'In order to import this demo, you need to install/activate the following plugins:', 'the7mk2' ); ?></strong>
        </p>
        <ol>
			<?php
			$required_plugins = $demo->plugins()->get_required_plugins_list();
			foreach ( $required_plugins as $plugin_name ) {
				echo "<li>{$plugin_name}</li>";
			}
			?>
        </ol>
    </div>
</div>