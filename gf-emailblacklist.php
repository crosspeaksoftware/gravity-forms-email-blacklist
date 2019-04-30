<?php
/**
 * Plugin Name: Gravity Forms Email Blacklist
 * Plugin URI: https://wordpress.org/plugins/gravity-forms-email-blacklist/
 * Description: This plugin adds the ability to set a blacklist of domains on the email field in gravity forms.
 * Version: 2.3
 * Author: hallme
 * Author URI: https://github.com/hallme/gravityforms-emailblacklist
 *
 * @package GFEmailBlacklist
 */

defined( 'ABSPATH' ) || exit;

add_action( 'gform_loaded', array( 'GF_Simple_AddOn_Bootstrap', 'load' ), 5 );

class GFEmailBlacklist_Bootstrap {

	public static function load() {
		if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
			return;
		}

		require_once 'includes/class-gfemailblacklist.php';
		GFAddOn::register( 'GFEmailBlacklist' );
	}
}

function gf_email_blacklist_addon() {
	return GFEmailBlacklist::get_instance();
}
