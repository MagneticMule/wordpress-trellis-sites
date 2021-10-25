<?php
/**
 * Import log tab.
 *
 * @package The7/Dev/Templates
 */

defined( 'ABSPATH' ) || exit;

echo '<h2>Latest import log</h2>';
echo '<pre>';
echo esc_html( (string) get_transient( 'the7_import_log' ) );
echo '</pre>';
