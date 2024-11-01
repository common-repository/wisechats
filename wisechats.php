<?php
/**
 * Plugin Name: WiseChats
 * Version: 1.0.0
 * Description: Add WiseChats Assistant to your WordPress website.
 * Author URI: https://wisechats.ai
 * Author: WiseChats
 * License: MIT
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Add the Admin Menu and Page Functionality
function wisechats_menu()
{
    add_menu_page(
        'WiseChats',                                      // Page title
        'WiseChats',                                      // Menu title
        'manage_options',                                 // Capability
        'wisechats',                                      // Menu slug
        'wisechats_page',                                 // Function to display the page content
        plugins_url('assets/wisechats-logo.png', __FILE__) // Icon URL
    );
}
add_action('admin_menu', 'wisechats_menu');

// Display the admin page content
function wisechats_page()
{
    ?>
<div class="wrap">
	<h1>WiseChats</h1>
	<form method="post" action="options.php">
		<?php
            settings_fields('wisechats_options_group');
    do_settings_sections('wisechats');
    submit_button();
    ?>
	</form>
</div>
<?php
}

// Add Settings Registration and Fields
function wisechats_id_cb()
{
    $id = esc_attr(get_option('wisechats_id'));
    printf(
        '<input type="text" name="wisechats_id" value="%s" class="regular-text" />',
        esc_attr($id)
    );
}

function wisechats_settings()
{
    register_setting('wisechats_options_group', 'wisechats_id', 'wisechats_sanitize_id');

    add_settings_section(
        'wisechats_section',
        '',
        'wisechats_section_cb',
        'wisechats'
    );

    add_settings_field(
        'wisechats_id',
        'Widget ID or Script',
        'wisechats_id_cb',
        'wisechats',
        'wisechats_section'
    );
}
add_action('admin_init', 'wisechats_settings');

// Sanitize and Extract ID from Input
function wisechats_sanitize_id($input)
{
    if (preg_match('/window\.WiseChats\.init\(\{id:\s?"(.*?)"/', $input, $matches)) {
        return $matches[1];
    }
    return sanitize_text_field($input);
}

function wisechats_section_cb()
{
    echo 'Set up below your WiseChats Assistant ID or paste the entire script.';
}

// Enqueue the script on the frontend
function wisechats_enqueue_script()
{
    $id = esc_attr(get_option('wisechats_id', ''));
    if (!empty($id)) {
        wp_register_script('wisechats-widget', 'https://widget.wisechats.ai/js/widget.js', [], '1.0.0', true);
        wp_enqueue_script('wisechats-widget');

        $inline_script = sprintf("window.WiseChats.init({id: '%s'});", esc_js($id));
        wp_add_inline_script('wisechats-widget', $inline_script);
    }
}
add_action('wp_enqueue_scripts', 'wisechats_enqueue_script');
?>