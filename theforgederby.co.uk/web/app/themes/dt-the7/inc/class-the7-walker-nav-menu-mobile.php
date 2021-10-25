<?php
/**
 * The7 mobile nav menu class.
 *
 * @since 7.6.0
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_Walker_Nav_Menu_Mobile
 */
class The7_Walker_Nav_Menu_Mobile extends The7_Walker_Nav_Menu {

	/**
	 * Display nav menu item.
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing (passed by reference).
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              An array of arguments.
	 * @param string $output            Used to append additional content (passed by reference).
	 */
	function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( ! $element ) {
			return;
		}

		if ( isset( $element->the7_mega_menu['mega-menu-hide-on-mobile'] ) && $element->the7_mega_menu['mega-menu-hide-on-mobile'] === 'on' ) {
			$this->unset_children( $element, $children_elements );
			return;
		}
 
		if ( isset( $element->the7_mega_menu['menu-item-hide-icon-on-mobile'] ) && $element->the7_mega_menu['menu-item-hide-icon-on-mobile'] === 'on' &&
		     isset( $element->the7_mega_menu['menu-item-icon-type']) && in_array($element->the7_mega_menu['menu-item-icon-type'], [ 'image', 'icon' ])) {
			$element->the7_mega_menu['menu-item-icon-type'] = 'none'; 
		}

		if ( isset( $element->the7_mega_menu['mega-menu-start-new-column'] ) ) {
			$element->the7_mega_menu['mega-menu-start-new-column'] = 'off';
		}

		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}
