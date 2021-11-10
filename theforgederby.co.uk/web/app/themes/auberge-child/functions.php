<?php

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {

wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );

add_theme_support( 'custom-header' );

add_theme_support( 'custom-header', array(
  'video' => true
));

}

?>