<?php
/*
Plugin Name: WP Tripadvisor Review Widgets
Plugin Title: WP Tripadvisor Review Widgets Plugin
Plugin URI: https://wordpress.org/plugins/review-widgets-for-tripadvisor/
Description: Embed Tripadvisor reviews fast and easily into your WordPress site. Increase SEO, trust and sales using Tripadvisor reviews.
Tags: tripadvisor, reviews, hotels, restaurant, accommodation, bar, reviews, ratings, recommendations, testimonials, widget, slider, review, rating, recommendation, testimonial, customer review
Author: Trustindex.io <support@trustindex.io>
Author URI: https://www.trustindex.io/
Contributors: trustindex
License: GPLv2 or later
Version: 7.3
Text Domain: review-widgets-for-tripadvisor
Domain Path: /languages/
Donate link: https://www.trustindex.io/prices/
*/
/*
Copyright 2019 Trustindex Kft (email: support@trustindex.io)
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
require(ABSPATH . 'wp-includes/version.php');
$page_slug = isset($_GET['page']) ? explode('/', sanitize_text_field($_GET['page']))[0] : '';
$tmp = explode('/', plugin_dir_path( __FILE__ ));
$plugin_slug = $tmp[ count($tmp) - 2 ];
if(1)
{
require_once plugin_dir_path( __FILE__ ) . 'plugin-load.php';
$trustindex_pm_tripadvisor = new TrustindexPlugin("tripadvisor", __FILE__, "7.3", "WP Tripadvisor Review Widgets", "Tripadvisor");
}
register_activation_hook(__FILE__, array($trustindex_pm_tripadvisor, 'activate'));
register_deactivation_hook(__FILE__, array($trustindex_pm_tripadvisor, 'deactivate'));
add_action('admin_menu', array($trustindex_pm_tripadvisor, 'add_setting_menu'), 10);
add_filter('plugin_action_links', array($trustindex_pm_tripadvisor, 'add_plugin_action_links'), 10, 2);
add_filter('plugin_row_meta', array($trustindex_pm_tripadvisor, 'add_plugin_meta_links'), 10, 2);
if(!function_exists('register_block_type'))
{
add_action('widgets_init', array($trustindex_pm_tripadvisor, 'init_widget'));
add_action('widgets_init', array($trustindex_pm_tripadvisor, 'register_widget'));
}
add_action('wp_head', array($trustindex_pm_tripadvisor, 'add_noreg_css_head'));
add_action('admin_head', array($trustindex_pm_tripadvisor, 'add_noreg_css_head_admin'));
$widget_css_tripadvisor = get_option($trustindex_pm_tripadvisor->get_option_name('css-content'));
if($trustindex_pm_tripadvisor->is_noreg_linked() && $widget_css_tripadvisor)
{
add_action('wp_enqueue_scripts', function() {
global $trustindex_pm_tripadvisor;
wp_enqueue_script('trustindex-frontend-js-tripadvisor', $trustindex_pm_tripadvisor->get_plugin_file_url('static/js/frontend.js'), [ 'jquery' ], false, true );
$data = [
'ajaxurl' => admin_url('admin-ajax.php'),
'action' => 'widget_css_tripadvisor',
'selector' => '.ti-widget.ti-' . substr($trustindex_pm_tripadvisor->shortname, 0, 4)
];
if(get_option($trustindex_pm_tripadvisor->get_option_name('widget-nonce'), 1))
{
$data['security'] = wp_create_nonce('frontend-nonce-$platform_type');
}
wp_localize_script('trustindex-frontend-js-tripadvisor', 'WidgetCsstripadvisor', $data);
});
add_action('wp_ajax_nopriv_widget_css_tripadvisor', 'trustindex_widget_css_tripadvisor');
add_action('wp_ajax_widget_css_tripadvisor', 'trustindex_widget_css_tripadvisor');
function trustindex_widget_css_tripadvisor() {
global $trustindex_pm_tripadvisor;
global $widget_css_tripadvisor;
if(get_option($trustindex_pm_tripadvisor->get_option_name('widget-nonce'), 1))
{
check_ajax_referer('frontend-nonce-$platform_type', 'security');
}
echo $widget_css_tripadvisor;
exit;
}
}
add_action('init', array($trustindex_pm_tripadvisor, 'init_shortcode'));
add_filter('script_loader_tag', function($tag, $handle) {
if(strpos($tag, 'trustindex.io/loader.js') !== false && strpos($tag, 'defer async') === false) {
$tag = str_replace(' src', ' defer async src', $tag );
}
return $tag;
}, 10, 2);
add_action('init', array($trustindex_pm_tripadvisor, 'register_tinymce_features'));
add_action('init', array($trustindex_pm_tripadvisor, 'output_buffer'));
add_action('wp_ajax_list_trustindex_widgets', array($trustindex_pm_tripadvisor, 'list_trustindex_widgets_ajax'));
add_action('admin_enqueue_scripts', array($trustindex_pm_tripadvisor, 'trustindex_add_scripts'));
add_action('rest_api_init', array($trustindex_pm_tripadvisor, 'init_restapi'));
add_action('admin_notices', function() {
$rate_us = get_option('trustindex-tripadvisor-rate-us', time() - 1);
if($rate_us == 'hide' || (int)$rate_us > time())
{
return;
}
$dir = plugin_dir_path( __FILE__ );
$usage_time = time() + 10;
if(is_dir($dir))
{
$usage_time = filemtime($dir) + (1 * 86400);
}
if($usage_time > time())
{
return;
}
?>
<div class="notice notice-warning is-dismissible trustindex-popup" style="position: fixed; top: 50px; right: 20px; padding-right: 30px; z-index: 1">
<p>
<?php echo TrustindexPlugin::___("Hello, I am happy to see that you've been using our <strong>%s</strong> plugin for a while now!", ["WP Tripadvisor Review Widgets"]); ?><br>
<?php echo TrustindexPlugin::___("Could you please help us and give it a 5-star rating on WordPress?"); ?><br><br>
<?php echo TrustindexPlugin::___("-- Thanks, Gabor M."); ?>
</p>
<p>
<a href="<?php echo admin_url("admin.php?page=review-widgets-for-tripadvisor/settings.php&rate_us=open"); ?>" class="trustindex-rateus" style="text-decoration: none" target="_blank">
<button class="button button-primary"><?php echo TrustindexPlugin::___("Sure, you deserve it"); ?></button>
</a>
<a href="<?php echo admin_url("admin.php?page=review-widgets-for-tripadvisor/settings.php&rate_us=later"); ?>" class="trustindex-rateus" style="text-decoration: none">
<button class="button button-secondary"><?php echo TrustindexPlugin::___("Maybe later"); ?></button>
</a>
<a href="<?php echo admin_url("admin.php?page=review-widgets-for-tripadvisor/settings.php&rate_us=hide"); ?>" class="trustindex-rateus" style="text-decoration: none">
<button class="button button-secondary" style="float: right"><?php echo TrustindexPlugin::___("Do not remind me again"); ?></button>
</a>
</p>
</div>
<?php
});
if(class_exists('Woocommerce') && !class_exists('TrustindexWoocommercePlugin') && !function_exists('ti_woocommerce_notice'))
{
function ti_woocommerce_notice() {
$rate_us = get_option('trustindex-wc-notification', time() - 1);
if($rate_us == 'hide' || (int)$rate_us > time())
{
return;
}
?>
<div class="notice notice-warning is-dismissible" style="margin: 5px 0 15px">
<p><strong><?php echo TrustindexPlugin::___("Download our new <a href='%s' target='_blank'>%s</a> plugin and get features for free!", [ 'https://wordpress.org/plugins/customer-reviews-for-woocommerce/', TrustindexPlugin::___('Customer Reviews for WooCommerce') ]); ?></strong></p>
<ul style="list-style-type: disc; margin-left: 10px; padding-left: 15px">
<li><?php echo TrustindexPlugin::___('set up Trustindex company profile and get high quality backlink'); ?></li>
<li><?php echo TrustindexPlugin::___('set up review-collector campaigns to get new ratings / reviews / recommendations automatically'); ?></li>
<li><?php echo TrustindexPlugin::___('show customer reviews in fancy widgets'); ?></li>
</ul>
<p>
<a href="<?php echo admin_url("admin.php?page=review-widgets-for-tripadvisor/settings.php&wc_notification=open"); ?>" target="_blank" class="trustindex-rateus" style="text-decoration: none">
<button class="button button-primary"><?php echo TrustindexPlugin::___("Download plugin"); ?></button>
</a>
<a href="<?php echo admin_url("admin.php?page=review-widgets-for-tripadvisor/settings.php&wc_notification=hide"); ?>" class="trustindex-rateus" style="text-decoration: none">
<button class="button button-secondary"><?php echo TrustindexPlugin::___("Do not remind me again"); ?></button>
</a>
</p>
</div>
<?php
}
add_action('admin_notices', 'ti_woocommerce_notice');
}
add_action('plugins_loaded', array($trustindex_pm_tripadvisor, 'plugin_loaded'));
?>