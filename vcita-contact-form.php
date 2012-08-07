<?php
/*
Plugin Name: Contact Form by vCita
Plugin URI: http://www.vcita.com
Description: Contact form by vCita proves to increase the number of contact form requests 
Version: 2.1.0
Author: vCita.com
Author URI: http://www.vcita.com
*/

/* --- Static initializer for Wordpress hooks --- */

// Check if vCita plugin already installed.
if (vcita_contact_check_plugin_available('vcita_scheduler') || vcita_contact_check_plugin_available('vcita_support')) {
	add_action('admin_notices', 'vcita_contact_other_plugin_installed_warning');
} else {
	define('VCITA_SERVER_BASE', "www.vcita.com"); /* Don't include the protocol, added dynamically */
	define('VCITA_WIDGET_VERSION', '2.1.0');
	define('VCITA_WIDGET_PLUGIN_NAME', 'Contact Form by vCita');
	define('VCITA_WIDGET_KEY', 'vcita_widget');
	define('VCITA_WIDGET_API_KEY', 'wp');
	define('VCITA_WIDGET_MENU_NAME', 'vCita Contact Form');
	define('VCITA_WIDGET_SHORTCODE', 'vCitaContact');
	define('VCITA_WIDGET_UNIQUE_ID', 'contact-form-with-a-meeting-scheduler-by-vcita');
	define('VCITA_WIDGET_UNIQUE_LOCATION', __FILE__);
	define('VCITA_WIDGET_CONTACT_FORM_WIDGET', 'true');
	define('VCITA_WIDGET_SHOW_EMAIL_PRIVACY', 'true');

	require_once(WP_PLUGIN_DIR."/".VCITA_WIDGET_UNIQUE_ID."/vcita-functions.php");
	
	
	/* --- Static initializer for Wordpress hooks --- */

	add_action('plugins_loaded', 'vcita_init');
	add_shortcode(VCITA_WIDGET_SHORTCODE,'vcita_add_contact');
	add_action('admin_menu', 'vcita_admin_actions');
	add_action('wp_head', 'vcita_add_active_engage');
}

/** 
 * Notify about other vCita plugin already available
 */ 
function vcita_contact_other_plugin_installed_warning() {
	echo "<div id='vcita-warning' class='error'><p><B>".__("vCita Plugin is already installed")."</B>".__(', please remove "<B>Contact Form by vCita</B>" and use the available "<B>Meeting Scheduler by vCita</B>" plugin')."</p></div>";
}

/**
 * Check if the requested plugin is already available
 */
function vcita_contact_check_plugin_available($plugin_key) {
	$other_widget_parms = (array) get_option($plugin_key); // Check the key of the other plugin

	// Check if vCita plugin already installed.
	return (isset($other_widget_parms['version']) || 
		    isset($other_widget_parms['uid']) || 
		    isset($other_widget_parms['email']));
}
?>