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

		$plugin = Gravity_Forms_Email_Blacklist::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		add_filter("gform_addon_navigation", array( $this, 'gravity_forms_menu'));
		add_action( 'gform_editor_js', array( $this, 'gf_email_field_blacklist_js' ) );
		add_action( 'gform_field_advanced_settings', array( $this, 'gf_email_field_blacklist_settings' ) );
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

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Add a page for this plugin to the Gravity Forms menu.
	 *
	 * @since     1.0.0
	 *
	 */
	public function gravity_forms_menu($menus) {

		$permission = current_user_can("gform_full_access");
		if(!empty($permission))
			$menus[] = array('name' => 'gf_emailblacklist', 'label' => __('Email Blacklist', $this->plugin_slug), "callback" =>  array( $this, 'display_plugin_gravityforms_page'), 'permission' => $has_full_access ? "gform_full_access" : "gravityforms_edit_forms", "gf_edit_forms" );

		return $menus;

	}

	public function display_plugin_gravityforms_page() {
		include_once( 'views/admin.php' );
	}

	public function form_settings_fields($form) {
	    return array(
	        array(
	            "title"  => "Simple Form Settings",
	            "fields" => array(
	                array(
	                    "label"   => "My checkbox",
	                    "type"    => "checkbox",
	                    "name"    => "enabled",
	                    "tooltip" => "This is the tooltip",
	                    "choices" => array(
	                        array(
	                            "label" => "Enabled",
	                            "name"  => "enabled"
	                        )
	                    )
	                ),
	                array(
	                    "label"   => "My checkboxes",
	                    "type"    => "checkbox",
	                    "name"    => "checkboxgroup",
	                    "tooltip" => "This is the tooltip",
	                    "choices" => array(
	                        array(
	                            "label" => "First Choice",
	                            "name"  => "first"
	                        ),
	                        array(
	                            "label" => "Second Choice",
	                            "name"  => "second"
	                        ),
	                        array(
	                            "label" => "Third Choice",
	                            "name"  => "third"
	                        )
	                    )
	                ),
	                array(
	                    "label"   => "My Radio Buttons",
	                    "type"    => "radio",
	                    "name"    => "myradiogroup",
	                    "tooltip" => "This is the tooltip",
	                    "choices" => array(
	                        array(
	                            "label" => "First Choice"
	                        ),
	                        array(
	                            "label" => "Second Choice"
	                        ),
	                        array(
	                            "label" => "Third Choice"
	                        )
	                    )
	                ),
	                array(
	                    "label"   => "My Horizontal Radio Buttons",
	                    "type"    => "radio",
	                    "horizontal" => true,
	                    "name"    => "myradiogrouph",
	                    "tooltip" => "This is the tooltip",
	                    "choices" => array(
	                        array(
	                            "label" => "First Choice"
	                        ),
	                        array(
	                            "label" => "Second Choice"
	                        ),
	                        array(
	                            "label" => "Third Choice"
	                        )
	                    )
	                ),
	                array(
	                    "label"   => "My Dropdown",
	                    "type"    => "select",
	                    "name"    => "mydropdown",
	                    "tooltip" => "This is the tooltip",
	                    "choices" => array(
	                        array(
	                            "label" => "First Choice",
	                            "value" => "first"
	                        ),
	                        array(
	                            "label" => "Second Choice",
	                            "value" => "second"
	                        ),
	                        array(
	                            "label" => "Third Choice",
	                            "value" => "third"
	                        )
	                    )
	                ),
	                array(
	                    "label"   => "My Text Box",
	                    "type"    => "text",
	                    "name"    => "mytext",
	                    "tooltip" => "This is the tooltip",
	                    "class"   => "medium",
	                    "feedback_callback" => array($this, "is_valid_setting")
	                ),
	                array(
	                    "label"   => "My Text Area",
	                    "type"    => "textarea",
	                    "name"    => "mytextarea",
	                    "tooltip" => "This is the tooltip",
	                    "class"   => "medium merge-tag-support mt-position-right"
	                ),
	                array(
	                    "label"   => "My Hidden Field",
	                    "type"    => "hidden",
	                    "name"    => "myhidden"
	                ),
	                array(
	                    "label"   => "My Custom Field",
	                    "type"    => "my_custom_field_type",
	                    "name"    => "my_custom_field"
	                )
	            )
	        )
	    );
	}

	public function settings_my_custom_field_type(){
	    ?>
	    <div>
	        My custom field contains a few settings:
	    </div>
	    <?php
	        $this->settings_text(
	            array(
	                "label" => "A textbox sub-field",
	                "name" => "subtext",
	                "default_value" => "change me"
	            )
	        );
	        $this->settings_checkbox(
	            array(
	                "label" => "A checkbox sub-field",
	                "choices" => array(
	                    array(
	                        "label" => "Activate",
	                        "name" => "subcheck",
	                        "default_value" => true
	                    )

	                )
	            )
	        );
	}

	public function is_valid_setting($value){
	    return strlen($value) < 10;
	}

//--Individual Email Field Settings---------------------------------------------------

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
