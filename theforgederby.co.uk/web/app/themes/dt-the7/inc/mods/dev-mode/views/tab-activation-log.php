<?php
/**
 * Activation log tab.
 *
 * @package The7/Dev/Templates
 */

defined( 'ABSPATH' ) || exit;

echo '<h2>The last 7 activated The7\'s</h2>';

$activationn_log = array_reverse( get_option( 'the7_theme_activation_log', [] ) );

echo '<ul>';
foreach ( $activationn_log as $entry ) {
	echo '<li>';
	echo '<strong>v.' . esc_html( $entry['version'] ) . '</strong>  -->  ' . date( 'Y-m-d H:i:s', $entry['activated_at'] );
	echo '</li>';
}
echo '</ul>';
