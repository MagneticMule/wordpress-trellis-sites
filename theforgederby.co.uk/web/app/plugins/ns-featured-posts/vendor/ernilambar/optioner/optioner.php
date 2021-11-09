<?php
/**
 * Plugin Name: Optioner
 * Description: Minimal option framework.
 * Version: 1.0.9
 * Author: Nilambar Sharma
 * Author URI: https://www.nilambar.net
 * Text Domain: optioner
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package Optioner
 */

namespace Nilambar\Optioner;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'OPTIONER_BASENAME', basename( dirname( __FILE__ ) ) );
define( 'OPTIONER_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
define( 'OPTIONER_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );

require_once OPTIONER_DIR . '/src/Optioner.php';

$obj = new Optioner();

$obj->set_page();

$obj->add_tab(
	array(
		'id'    => 'basic_tab',
		'title' => esc_html__( 'Basic', 'ns-featured-posts' ),
	)
);

// Field: sample_heading.
$obj->add_field(
	'basic_tab',
	array(
		'id'          => 'sample_heading',
		'type'        => 'heading',
		'title'       => esc_html__( 'Sample Heading', 'ns-featured-posts' ),
		'description' => esc_html__( 'This is description.', 'ns-featured-posts' ),
	)
);

// Field: sample_text.
$obj->add_field(
	'basic_tab',
	array(
		'id'          => 'sample_text',
		'type'        => 'text',
		'title'       => esc_html__( 'Sample Text', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description for sample text.', 'ns-featured-posts' ),
		'placeholder' => esc_html__( 'Enter text', 'ns-featured-posts' ),
	)
);

// Field: sample_checkbox.
$obj->add_field(
	'basic_tab',
	array(
		'id'          => 'sample_checkbox',
		'type'        => 'checkbox',
		'default'     => true,
		'title'       => esc_html__( 'Sample Checkbox', 'ns-featured-posts' ),
		'side_text'   => esc_html__( 'Enable sample checkbox', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description for sample checkbox field.', 'ns-featured-posts' ),
	)
);

// Field: sample_select.
$obj->add_field(
	'basic_tab',
	array(
		'id'          => 'sample_select',
		'type'        => 'select',
		'title'       => esc_html__( 'Sample Select', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description of sample select.', 'ns-featured-posts' ),
		'choices'     => array(
			'1' => esc_html__( 'First', 'ns-featured-posts' ),
			'2' => esc_html__( 'Second', 'ns-featured-posts' ),
			'3' => esc_html__( 'Third', 'ns-featured-posts' ),
		),
	)
);

// Field: sample_radio.
$obj->add_field(
	'basic_tab',
	array(
		'id'          => 'sample_radio',
		'type'        => 'radio',
		'title'       => esc_html__( 'Sample Radio', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description of sample radio.', 'ns-featured-posts' ),
		'default'     => '1',
		'choices'     => array(
			'1' => esc_html__( 'First', 'ns-featured-posts' ),
			'2' => esc_html__( 'Second', 'ns-featured-posts' ),
		),
	)
);

// Field: sample_image.
$obj->add_field(
	'basic_tab',
	array(
		'id'          => 'sample_image',
		'type'        => 'image',
		'title'       => esc_html__( 'Sample Image', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description for sample image.', 'ns-featured-posts' ),
	)
);

// Field: sample_color.
$obj->add_field(
	'basic_tab',
	array(
		'id'          => 'sample_color',
		'type'        => 'color',
		'title'       => esc_html__( 'Sample Color', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description of sample color.', 'ns-featured-posts' ),
		'default'     => '#8224e3',
	)
);

$obj->add_tab(
	array(
		'id'    => 'text_tab',
		'title' => esc_html__( 'Text', 'ns-featured-posts' ),
	)
);

// Field: text_regular.
$obj->add_field(
	'text_tab',
	array(
		'id'          => 'text_regular',
		'type'        => 'text',
		'title'       => esc_html__( 'Text Regular', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description of text regular.', 'ns-featured-posts' ),
	)
);

// Field: text_small.
$obj->add_field(
	'text_tab',
	array(
		'id'          => 'text_small',
		'type'        => 'text',
		'title'       => esc_html__( 'Text Small', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description of text small.', 'ns-featured-posts' ),
		'class'       => 'small-text',
	)
);

// Field: text_tiny.
$obj->add_field(
	'text_tab',
	array(
		'id'          => 'text_tiny',
		'type'        => 'text',
		'title'       => esc_html__( 'Text Tiny', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description of text tiny.', 'ns-featured-posts' ),
		'class'       => 'tiny-text',
	)
);

// Field: text_large.
$obj->add_field(
	'text_tab',
	array(
		'id'          => 'text_large',
		'type'        => 'text',
		'title'       => esc_html__( 'Text Large', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description of text large.', 'ns-featured-posts' ),
		'class'       => 'large-text',
	)
);

// Field: text_password.
$obj->add_field(
	'text_tab',
	array(
		'id'          => 'text_password',
		'type'        => 'password',
		'title'       => esc_html__( 'Text Password', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description of text password.', 'ns-featured-posts' ),
	)
);

// Field: sample_url.
$obj->add_field(
	'text_tab',
	array(
		'id'          => 'sample_url',
		'type'        => 'url',
		'title'       => esc_html__( 'Sample URL', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description of sample URL.', 'ns-featured-posts' ),
		'placeholder' => esc_html__( 'Enter full URL.', 'ns-featured-posts' ),
	)
);

// Field: sample_number.
$obj->add_field(
	'text_tab',
	array(
		'id'          => 'sample_number',
		'type'        => 'number',
		'title'       => esc_html__( 'Sample Number', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description of sample number.', 'ns-featured-posts' ),
	)
);

// Field: sample_email.
$obj->add_field(
	'text_tab',
	array(
		'id'          => 'sample_email',
		'type'        => 'email',
		'title'       => esc_html__( 'Sample Email', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description of sample email.', 'ns-featured-posts' ),
	)
);

// Tab: textarea_tab.
$obj->add_tab(
	array(
		'id'    => 'textarea_tab',
		'title' => esc_html__( 'Textarea', 'ns-featured-posts' ),
	)
);

// Field: textarea_regular.
$obj->add_field(
	'textarea_tab',
	array(
		'id'          => 'textarea_regular',
		'type'        => 'textarea',
		'title'       => esc_html__( 'Textarea Regular', 'ns-featured-posts' ),
		'description' => esc_html__( 'This is regular textarea.', 'ns-featured-posts' ),
		'placeholder' => esc_html__( 'Enter content.', 'ns-featured-posts' ),
	)
);

// Field: textarea_large.
$obj->add_field(
	'textarea_tab',
	array(
		'id'          => 'textarea_large',
		'type'        => 'textarea',
		'title'       => esc_html__( 'Textarea Large', 'ns-featured-posts' ),
		'description' => esc_html__( 'This is large textarea.', 'ns-featured-posts' ),
		'placeholder' => esc_html__( 'Enter content.', 'ns-featured-posts' ),
		'class'       => 'large-text',
	)
);

// Tab: checkbox_tab.
$obj->add_tab(
	array(
		'id'    => 'checkbox_tab',
		'title' => esc_html__( 'Checkbox', 'ns-featured-posts' ),
	)
);

// Field: checkbox_single.
$obj->add_field(
	'checkbox_tab',
	array(
		'id'          => 'checkbox_single',
		'type'        => 'checkbox',
		'default'     => true,
		'title'       => esc_html__( 'Checkbox Simple', 'ns-featured-posts' ),
		'side_text'   => esc_html__( 'Enable simple checkbox', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description for simple checkbox field.', 'ns-featured-posts' ),
	)
);

// Field: checkbox_multi.
$obj->add_field(
	'checkbox_tab',
	array(
		'id'          => 'checkbox_multi',
		'type'        => 'multicheck',
		'title'       => esc_html__( 'Checkbox Multiple', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description for checkbox multiple.', 'ns-featured-posts' ),
		'choices'     => array(
			'1' => esc_html__( 'First', 'ns-featured-posts' ),
			'2' => esc_html__( 'Second', 'ns-featured-posts' ),
			'3' => esc_html__( 'Third', 'ns-featured-posts' ),
			'4' => esc_html__( 'Fourth', 'ns-featured-posts' ),
		),
	)
);

// Tab: selection_tab.
$obj->add_tab(
	array(
		'id'    => 'selection_tab',
		'title' => esc_html__( 'Selection', 'ns-featured-posts' ),
	)
);

// Field: select_simple.
$obj->add_field(
	'selection_tab',
	array(
		'id'          => 'select_simple',
		'type'        => 'select',
		'title'       => esc_html__( 'Select Simple', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description of select simple.', 'ns-featured-posts' ),
		'choices'     => array(
			'1' => esc_html__( 'First', 'ns-featured-posts' ),
			'2' => esc_html__( 'Second', 'ns-featured-posts' ),
			'3' => esc_html__( 'Third', 'ns-featured-posts' ),
		),
	)
);

// Field: select_null_allowed.
$obj->add_field(
	'selection_tab',
	array(
		'id'          => 'select_null_allowed',
		'type'        => 'select',
		'title'       => esc_html__( 'Select Null Allowed', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description of select null allowed.', 'ns-featured-posts' ),
		'allow_null'  => true,
		'choices'     => array(
			'1' => esc_html__( 'First', 'ns-featured-posts' ),
			'2' => esc_html__( 'Second', 'ns-featured-posts' ),
			'3' => esc_html__( 'Third', 'ns-featured-posts' ),
		),
	)
);

// Field: radio_horizontal.
$obj->add_field(
	'selection_tab',
	array(
		'id'          => 'radio_horizontal',
		'type'        => 'radio',
		'layout'      => 'horizontal',
		'title'       => esc_html__( 'Radio Horizontal', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description of radio horizontal.', 'ns-featured-posts' ),
		'default'     => '1',
		'choices'     => array(
			'1' => esc_html__( 'First', 'ns-featured-posts' ),
			'2' => esc_html__( 'Second', 'ns-featured-posts' ),
			'3' => esc_html__( 'Third', 'ns-featured-posts' ),
		),
	)
);

// Field: radio_vertical.
$obj->add_field(
	'selection_tab',
	array(
		'id'          => 'radio_vertical',
		'type'        => 'radio',
		'title'       => esc_html__( 'Radio Vertical', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description of radio vertical.', 'ns-featured-posts' ),
		'default'     => '1',
		'choices'     => array(
			'1' => esc_html__( 'First', 'ns-featured-posts' ),
			'2' => esc_html__( 'Second', 'ns-featured-posts' ),
			'3' => esc_html__( 'Third', 'ns-featured-posts' ),
		),
	)
);

// Tab: editor_tab.
$obj->add_tab(
	array(
		'id'    => 'editor_tab',
		'title' => esc_html__( 'Editor', 'ns-featured-posts' ),
	)
);

// Field: editor_visual_only.
$obj->add_field(
	'editor_tab',
	array(
		'id'          => 'editor_visual_only',
		'type'        => 'editor',
		'title'       => esc_html__( 'Editor Visual Mode Only', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description for editor visual mode only.', 'ns-featured-posts' ),
		'size'        => 460, // Max width, in px.
		'settings'    => array(
			'textarea_rows' => 5,
			'media_buttons' => false,
			'quicktags'     => false,
		),
	)
);

// Field: editor_text_only.
$obj->add_field(
	'editor_tab',
	array(
		'id'          => 'editor_text_only',
		'type'        => 'editor',
		'title'       => esc_html__( 'Editor Text Mode Only', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description for editor text mode only.', 'ns-featured-posts' ),
		'size'        => 460, // Max width, in px.
		'settings'    => array(
			'textarea_rows' => 5,
			'media_buttons' => false,
			'tinymce'       => false,
		),
	)
);

// Field: editor_small.
$obj->add_field(
	'editor_tab',
	array(
		'id'          => 'editor_small',
		'type'        => 'editor',
		'title'       => esc_html__( 'Editor Small', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description for editor small.', 'ns-featured-posts' ),
		'size'        => 460, // Max width, in px.
		'settings'    => array(
			'textarea_rows' => 5,
			'media_buttons' => false,
		),
	)
);

// Field: editor_large.
$obj->add_field(
	'editor_tab',
	array(
		'id'          => 'editor_large',
		'type'        => 'editor',
		'title'       => esc_html__( 'Editor Large', 'ns-featured-posts' ),
		'description' => esc_html__( 'Description for editor large.', 'ns-featured-posts' ),
	)
);

// Tab: features_tab.
$obj->add_tab(
	array(
		'id'              => 'features_tab',
		'title'           => esc_html__( 'Features', 'ns-featured-posts' ),
		'render_callback' => __NAMESPACE__ . '\optioner_render_features_tab',
	)
);

// Set sidebar.
$obj->set_sidebar(
	array(
		'render_callback' => __NAMESPACE__ . '\optioner_render_sidebar',
	)
);

// Render now.
$obj->run();

/**
 * Render features tab.
 *
 * @since 1.0.0
 */
function optioner_render_features_tab() {
	?>
	<p>This is a demonstration of custom tab. You can add anything here. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eos quia eveniet fugiat nihil voluptatum, laborum alias reprehenderit, modi dicta repudiandae, officia repellat fuga cum optio sit culpa nemo quibusdam. Perspiciatis.</p>
	<p>Ipsum dolor sit amet, consectetur adipisicing elit. Eos quia eveniet fugiat nihil voluptatum, laborum alias reprehenderit, modi dicta repudiandae, officia repellat fuga cum optio sit culpa nemo quibusdam. Perspiciatis.</p>
	<?php
}

/**
 * Render features tab.
 *
 * @since 1.0.0
 */
function optioner_render_sidebar() {
	?>
	<div class="sidebox">
		<h3 class="box-heading">Help &amp; Support</h3>
		<div class="box-content">
			<ul>
				<li><strong>Questions, bugs, or great ideas?</strong></li>
				<li><a href="https://github.com/ernilambar/optioner/issues" target="_blank">Create issue in the repo</a></li>
			</ul>
		</div>
	</div>
	<div class="sidebox">
		<h3 class="box-heading">Sample Links</h3>
		<div class="box-content">
			<p>Lorem ipsum dolor sit amet, conse ctetur adipiscing elit.</p>
			<ul>
				<li><strong>Important links</strong></li>
				<li><a href="#">Sample Link One</a></li>
				<li><a href="#">Sample Link Two</a></li>
				<li><a href="#">Sample Link Three</a></li>
				<li><a href="#">Sample Link Four</a></li>
				<li><a href="#">Sample Link Five</a></li>
			</ul>
		</div>
	</div>
	<?php
}
