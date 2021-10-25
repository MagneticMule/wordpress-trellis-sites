<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * @var string $follow_link
 * @var string $target
 * @var string $caption
 * @var string $icon
 * @var string $icon_position
 * @var string $aria_label
 */

$atts = [
	'class' => 'post-details details-type-btn dt-btn-s dt-btn',
];

$atts['href']       = esc_url( $follow_link );
$atts['target']     = $target;
$atts['aria-label'] = $aria_label;
?>

<a <?php echo the7_get_html_attributes_string( $atts ) ?>><?php
	if ( $icon_position === 'before' ) {
		echo $icon;
	}

	echo '<span>' . esc_html( $caption ) . '</span>';

	if ( $icon_position === 'after' ) {
		echo $icon;
	}
	?></a>