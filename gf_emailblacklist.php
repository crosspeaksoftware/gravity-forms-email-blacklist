<?php
/**
 * Gravity Forms Email Blacklist
 *
 * This plugin allows you to add a list of domains that will give validation errors if submited in an email field.
 *
 * @package   Gravity_Forms_Email_Blacklist
 * @author    Tim Howe <timbhowe@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.hallme.com
 * @copyright 2014 Tim Howe
 *
 * @wordpress-plugin
 * Plugin Name:       Gravity Forms Email Blacklist
 * Plugin URI:        http://www.hallme.com
 * Description:       This plugin allows you to add a list of domains that will give validation errors if submited in an email field.
 * Version:           0.0.1
 * Author:            Tim Howe
 * Author URI:        
 * Text Domain:       gf_emailblacklist
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: git@github.com:hallme/gravityforms-emailblacklist.git
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-gf_emailblacklist.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Gravity_Forms_Email_Blacklist', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Gravity_Forms_Email_Blacklist', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Gravity_Forms_Email_Blacklist', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-gf_emailblacklist-admin.php' );
	add_action( 'plugins_loaded', array( 'Gravity_Forms_Email_Blacklist_Admin', 'get_instance' ) );

}
