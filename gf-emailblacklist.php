<?php
/**
 * Plugin Name: Gravity Forms Email Blacklist
 * Plugin URI: https://wordpress.org/plugins/gravity-forms-email-blacklist/
 * Description: This plugin adds the ability to set a blacklist of domains on the email field in gravity forms.
 * Version: 2.6.2
 * Author: CrossPeak Software
 * Author URI: https://www.crosspeaksoftware.com/
 * Text Domain: gravity-forms-email-blacklist
 * Domain Path: /languages
 *
 * @package GFEmailBlacklist
 */

defined( 'ABSPATH' ) || exit;

add_action( 'gform_loaded', array( 'GFEmailBlacklist_Bootstrap', 'load' ), 5 );

/**
 * Gravity Forms Bootstrap class to load the Add-On library and new class.
 */
class GFEmailBlacklist_Bootstrap {

	/**
	 * Load the Add-On class after checking for the frame work.
	 */
	public static function load() {
		if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
			return;
		}

		require_once 'includes/class-gfemailblacklist.php';
		GFAddOn::register( 'GFEmailBlacklist' );
	}
}

/**
 * Init the class.
 *
 * @return object Return the instance of the Add-On class.
 */
function gf_email_blacklist_addon() {
	return GFEmailBlacklist::get_instance();
}

/**
 * Load plugin textdomain for localization.
 *
 * @return void
 */
function gf_email_blacklist_plugin_textdomain() {
	load_plugin_textdomain( 'gravity-forms-email-blacklist', false, basename( __DIR__ ) . '/languages/' );
}
add_action( 'init', 'gf_email_blacklist_plugin_textdomain' );
