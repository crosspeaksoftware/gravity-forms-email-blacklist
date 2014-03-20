<?php
/**
 * Gravity_Forms_Email_Blacklist
 *
 * @package   Gravity_Forms_Email_Blacklist_Admin
 * @author    Tim Howe <timbhowe@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.hallme.com
 * @copyright 2014 Tim Howe
 */

/**
 * Gravity_Forms_Email_Blacklist_Admin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-plugin-name.php`
 *
 * @package Gravity_Forms_Email_Blacklist_Admin
 * @author  Tim Howe <timbhowe@gmail.com>
 */
class Gravity_Forms_Email_Blacklist_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$plugin = Gravity_Forms_Email_Blacklist::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		//Actions
		add_action( 'gform_editor_js', array( $this, 'gf_email_field_blacklist_js' ) );
		add_action( 'gform_field_advanced_settings', array( $this, 'gf_email_field_blacklist_settings' ) );

		//Filters
		add_filter( 'gform_tooltips', array( $this, 'gf_email_field_blacklist_tooltip' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Gravity_Forms_Email_Blacklist::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Gravity_Forms_Email_Blacklist::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * @TODO:
		 *
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Gravity Forms Email Blacklist Settings', $this->plugin_slug ),
			__( 'Gravity Forms Email Blacklist', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * Execute some javascript technicalities for the field to load correctly
	 *
	 * @since    1.0.0
	 */
	public function gf_email_field_blacklist_js(){
	?>
	<script type='text/javascript'>
		jQuery(document).ready(function($) {
	        //Alter the setting offered for the email input type
	        fieldSettings["email"] = fieldSettings["email"] + ", .email_blacklist_setting, .email_blacklist_validation"; // this will show all fields that Paragraph Text field shows plus my custom setting

			//binding to the load field settings event to initialize the checkbox
			$(document).bind("gform_load_field_settings", function(event, field, form){
				$("#field_email_blacklist").val(field["email_blacklist"]);
				$("#field_email_blacklist_validation").val(field["email_blacklist_validation"]);
			});
	    });
	</script>
	<?php
	}

	/**
	 * Alter the Email Blacklist settings on a per email field level
	 *
	 * @since    1.0.0
	 */
	public function gf_email_field_blacklist_settings( $position, $form_id=null ){

	    // Create settings on position 50 (right after Field Label)
	    if( $position == 50 ){ ?>

	    <li class="email_blacklist_setting field_setting">
			<label for="field_email_blacklist">
	            <?php _e("Blacklisted Emails", "gravityforms"); ?>
	            <?php gform_tooltip("form_field_email_blacklist"); ?>
	        </label>
			<input type="text" id="field_email_blacklist" class="fieldwidth-3" size="35" onkeyup="SetFieldProperty('email_blacklist', this.value);">
	    </li>
	    <li class="email_blacklist_validation field_setting">
			<label for="field_email_blacklist_validation">
	            <?php _e("Blacklisted Emails Validation Message", "gravityforms"); ?>
	            <?php gform_tooltip("form_field_email_blacklist_validation"); ?>
	        </label>
			<input type="text" id="field_email_blacklist_validation" class="fieldwidth-3" size="35" onkeyup="SetFieldProperty('email_blacklist_validation', this.value);">
	    </li>

	    <?php
	    }
	}

	/**
	 * Add a tool tip to the Email Blacklist setting field on the form edit page in the back end
	 *
	 * @since    1.0.0
	 */
	public function gf_email_field_blacklist_tooltip($tooltips){
	   $tooltips["form_field_email_blacklist"] = "<h6>Email Blacklist</h6> Please enter a comma separated list of domains you would like to block from submitting their email.";
	   $tooltips["form_field_email_blacklist_validation"] = "<h6>Validation Message</h6> Please enter the validation message you would like to appear if a blacklisted email is entered.";
	   return $tooltips;
	}

}
