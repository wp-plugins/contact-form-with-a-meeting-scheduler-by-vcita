<?php
/*
Plugin Name: Next Gen Contact Form by vCita
Plugin URI: http://www.vcita.com
Description: vCita next generation contact form proves to increase the number of contact requests 
Version: 1.4.6
Author: vCita.com
Author URI: http://www.vcita.com
*/

/* --- Static initializer for Wordpress hooks --- */

$other_widget_parms = (array) get_option('vcita_scheduler'); // Check the key of the other plugin

// Check if vCita plugin already installed.
if (isset($other_widget_parms['version']) || isset($other_widget_parms['uid']) || isset($other_widget_parms['email'])) {
	add_action('admin_notices', 'vcita_other_plugin_installed_warning');
} else {
	define('VCITA_WIDGET_VERSION', '1.4.6.2');
	define('VCITA_WIDGET_PLUGIN_NAME', 'Next Gen Contact Form by vCita');
	define('VCITA_WIDGET_KEY', 'vcita_widget');
	define('VCITA_WIDGET_API_KEY', 'wp');
	define('VCITA_WIDGET_MENU_NAME', 'vCita Contact Form');
	define('VCITA_WIDGET_SHORTCODE', 'vCitaContact');
	define('VCITA_WIDGET_UNIQUE_ID', 'contact-form-with-a-meeting-scheduler-by-vcita');
	define('VCITA_WIDGET_UNIQUE_LOCATION', __FILE__);
	define('VCITA_WIDGET_CONTACT_FORM_WIDGET', 'true');

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
function vcita_other_plugin_installed_warning() {
	echo "<div id='vcita-warning' class='error'><p><B>".__("vCita Plugin is already installed")."</B>".__(', please remove "<B>Next Gen Contact Form by vCita</B>" and use the available "<B>Meeting Scheduler by vCita</B>" plugin')."</p></div>";
}
?>