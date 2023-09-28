<?php
/**
 * Gravity Forms Email Blacklist Handler.
 *
 * @class   GFEmailBlacklist
 * @package GFEmailBlacklist
 */

defined( 'ABSPATH' ) || exit;

GFForms::include_addon_framework();

/**
 * GFEmailBlacklist - extends the GFAddOn class to  allow users to blacklist, block and invalidate submissions to forms
 * using an email input field and based on the email addresses domain.
 */
class GFEmailBlacklist extends GFAddOn {

	protected $_version                  = '2.5.5';
	protected $_min_gravityforms_version = '1.9';
	protected $_slug                     = 'gf_email_blacklist';
	protected $_path                     = 'gravity-forms-email-blacklist/gf_emailblacklist.php';
	protected $_full_path                = __FILE__;
	protected $_title                    = 'Gravity Forms Email Blacklist';
	protected $_short_title              = 'Email Blacklist';

	/**
	 * Defines the capability needed to access the Add-On settings page.
	 *
	 * @since  2.5.4
	 * @access protected
	 * @var    string $_capabilities_settings_page The capability needed to access the Add-On settings page.
	 */
	protected $_capabilities_settings_page = 'gravityforms_email_blacklist';

	/**
	 * Defines the capability needed to access the Add-On form settings page.
	 *
	 * @since  2.5.4
	 * @access protected
	 * @var    string $_capabilities_form_settings The capability needed to access the Add-On form settings page.
	 */
	protected $_capabilities_form_settings = 'gravityforms_email_blacklist';

	/**
	 * Defines the capability needed to uninstall the Add-On.
	 *
	 * @since  2.5.4
	 * @access protected
	 * @var    string $_capabilities_uninstall The capability needed to uninstall the Add-On.
	 */
	protected $_capabilities_uninstall = 'gravityforms_email_blacklist_uninstall';

	/**
	 * Defines the capabilities needed for the Post Creation Add-On
	 *
	 * @since  2.5.4
	 * @access protected
	 * @var    array $_capabilities The capabilities needed for the Add-On
	 */
	protected $_capabilities = array( 'gravityforms_email_blacklist', 'gravityforms_email_blacklist_uninstall' );


	private static $_instance = null;

	/**
	 * Get an instance of this class.
	 *
	 * @return GFEmailBlacklist
	 */
	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new GFEmailBlacklist();
		}
		return self::$_instance;
	}

	/**
	 * Add tasks or filters here that you want to perform only in admin.
	 */
	public function init_admin() {
		parent::init_admin();
		add_action( 'gform_editor_js', array( $this, 'gf_emailblacklist_gform_editor_js' ) );
		add_action( 'gform_field_advanced_settings', array( $this, 'gf_email_blacklist_field_settings' ), 10, 2 );
		add_filter( 'gform_tooltips', array( $this, 'gf_emailblacklist_field_tooltips' ) );
	}

	/**
	 * Add tasks or filters here that you want to perform only in the front end.
	 */
	public function init_frontend() {
		parent::init_frontend();
		add_filter( 'gform_validation', array( $this, 'gf_emailblacklist_validation' ) );
	}

	/**
	 * Add the additional Email Blacklist
	 *
	 * @return array Additional plugin setting fields in the Gravity Forms Settings API.
	 */
	public function plugin_settings_fields() {
		return array(
			array(
				'title'       => __( 'Email Blacklist Global Settings', 'gravity-forms-email-blacklist' ),
				'description' => __( 'Use Email Blacklist to secure your forms. If a blacklisted email is used in any email field, the form will error on submission. You can also globally define a list of blacklisted emails and/or domains and a custom validation message if a blacklisted email is submitted. These settings can be overridden on individual email fields in the advanced settings. <a href="https://www.crosspeaksoftware.com/downloads/gravity-forms-email-blacklist/" target="_blank">View Documentation</a>', 'gravity-forms-email-blacklist' ),
				'fields'      => array(
					array(
						'label'   => __( 'Global Blacklisted Emails', 'gravity-forms-email-blacklist' ),
						'type'    => 'text',
						'name'    => 'default_emailblacklist',
						'tooltip' => __( 'Please enter a comma separated list of blacklisted domains (ex. hotmail.com), email addresses (ex. user@aol.com), and/or include the wildcard notation to block top-level domains (ex. *.com). This setting can be overridden on individual email fields in the advanced settings.', 'gravity-forms-email-blacklist' ),
						'class'   => 'medium',
					),
					array(
						'label'   => __( 'Global Validation Message', 'gravity-forms-email-blacklist' ),
						'type'    => 'text',
						'name'    => 'default_emailblacklist_error_msg',
						'tooltip' => __( 'Please enter a default error message if a blacklisted email is submitted. This setting can be overridden on individual email fields in the advanced settings.', 'gravity-forms-email-blacklist' ),
						'class'   => 'medium',
					),
				),
			),
		);
	}

	/**
	 * Add email blacklist setting to the email fields advanced settings.
	 *
	 * @param integer $position Specifies the position that the settings will be displayed.
	 * @param integer $form_id  The ID of the form from which the entry value was submitted.
	 */
	public function gf_email_blacklist_field_settings( $position, $form_id = null ) {

		// Get settings for placeholder text.
		if ( get_option( 'gravityformsaddon_' . $this->_slug . '_settings' ) ) {
			$validation_message = get_option( 'gravityformsaddon_' . $this->_slug . '_settings' );
			$emailblacklist     = __( 'Global Email Blacklist: ', 'gravity-forms-email-blacklist' ) . $validation_message['default_emailblacklist'];
			$emailblacklist_msg = __( 'Global Error Message: ', 'gravity-forms-email-blacklist' ) . $validation_message['default_emailblacklist_error_msg'];
		} else {
			$emailblacklist     = __( 'Set Blacklist Emails', 'gravity-forms-email-blacklist' );
			$emailblacklist_msg = __( 'Set Error Message', 'gravity-forms-email-blacklist' );
		}

		// Create settings on position 50 (right after Field Label).
		if ( 50 === $position ) {
			?>
		<li class="email_blacklist_setting field_setting">
			<label for="field_email_blacklist">
				<?php esc_html_e( 'Blacklisted Emails', 'gravity-forms-email-blacklist' ); ?>
				<?php gform_tooltip( 'form_field_email_blacklist' ); ?>
			</label>
			<input type="text" id="field_email_blacklist" class="fieldwidth-3" size="35" onkeyup="SetFieldProperty('email_blacklist', this.value);" placeholder="<?php echo esc_attr( $emailblacklist ); ?>">
		</li>

		<li class="email_blacklist_validation field_setting">
			<label for="field_email_blacklist_validation">
				<?php esc_html_e( 'Blacklisted Emails Validation Message', 'gravity-forms-email-blacklist' ); ?>
				<?php gform_tooltip( 'form_field_email_blacklist_validation' ); ?>
			</label>
			<input type="text" id="field_email_blacklist_validation" class="fieldwidth-3" size="35" onkeyup="SetFieldProperty('email_blacklist_validation', this.value);" placeholder="<?php echo esc_attr( $emailblacklist_msg ); ?>">
		</li>
			<?php
		}
	}

	/**
	 * Add the additional tooltips to the new fields.
	 *
	 * @param array $tooltips tooltip associative array.
	 * @return array modified tooltips
	 */
	public function gf_emailblacklist_field_tooltips( $tooltips ) {
		$tooltips['form_field_email_blacklist']            = __( "Please enter a comma separated list of blacklisted domains, (ex. hotmail.com), email addresses (ex. user@aol.com), and/or include the wildcard notation to block top-level domains (ex. *.com). This will override the globally defined blacklisted emails setting. Enter 'none' to bypass the global setting and allow all email addresses.", 'gravity-forms-email-blacklist' );
		$tooltips['form_field_email_blacklist_validation'] = __( 'Please enter an error message if a blacklisted email is submitted. This will override the globally defined error message.', 'gravity-forms-email-blacklist' );
		return $tooltips;
	}

	/**
	 * Inject Javascript into the form editor page for the email blacklist fields.
	 */
	public function gf_emailblacklist_gform_editor_js() {
		?>
	<script type='text/javascript'>
		jQuery(document).ready(function($) {
			// Alter the setting offered for the email input type.
			fieldSettings["email"] = fieldSettings["email"] + ", .email_blacklist_setting, .email_blacklist_validation"; // this will show all fields that Paragraph Text field shows plus my custom setting

			// Binding to the load field settings event to initialize the checkbox.
			$(document).bind("gform_load_field_settings", function(event, field, form){
				$("#field_email_blacklist").val(field["email_blacklist"]);
				$("#field_email_blacklist_validation").val(field["email_blacklist_validation"]);
			});
		});
	</script>
		<?php
	}

	/**
	 * Add email blacklist to gforms validation function.
	 *
	 * @resources: https://docs.gravityforms.com/using-gform-validation-hook/
	 *
	 * @param  array $validation_result Contains the validation result and the current.
	 *
	 * @return array The field validation results.
	 */
	public function gf_emailblacklist_validation( $validation_result ) {

		// Collect global settings.
		$default_blacklist = get_option( 'gravityformsaddon_' . $this->_slug . '_settings' );
		if ( is_array( $default_blacklist ) && ! empty( $default_blacklist['default_emailblacklist'] ) ) {
			$default_blacklist = $default_blacklist['default_emailblacklist'];
		} else {
			$default_blacklist = '';
		}

		// Collect form results.
		$form = $validation_result['form'];

		// Loop through results.
		foreach ( $form['fields'] as &$field ) {

			// If this is not an email field, skip.
			if ( 'email' !== RGFormsModel::get_input_type( $field ) ) {
				continue;
			}

			// If the field is hidden by GF conditional logic, skip.
			if ( RGFormsModel::is_field_hidden( $form, $field, array() ) ) {
				continue;
			}

			// Collect banned domains from backend and clean up.
			$blacklist = $default_blacklist;
			if ( ! empty( $field['email_blacklist'] ) ) { // collect per form settings.
				$blacklist = $field['email_blacklist'];
			}

			// Get the domain from user entered email.
			$email  = $this->gf_emailblacklist_clean( rgpost( "input_{$field['id']}" ) );
			$domain = $this->gf_emailblacklist_clean( rgar( explode( '@', $email ), 1 ) );
			$tld    = strrchr( $domain, '.' );

			/**
			 * Filter to allow third party plugins short circuit blacklist validation.
			 *
			 * @since 2.5.1
			 * @param bool   false      Default value.
			 * @param array  $field     The Field Object.
			 * @param string $email     The email entered in the input.
			 * @param string $domain    The full domain entered in the input.
			 * @param string $tld       The top level domain entered in the input.
			 * @param array  $blacklist List of the blocked emailed/domains.
			 */
			if ( apply_filters( 'gf_blacklist_validation_short_circuit', false, $field, $email, $domain, $tld, $blacklist ) ) {
				continue;
			}

			// Create array of banned domains.
			if ( ! is_array( $blacklist ) ) {
				$blacklist = explode( ',', $blacklist );
			}
			$blacklist = str_replace( '*', '', $blacklist );
			$blacklist = array_map( array( $this, 'gf_emailblacklist_clean' ), $blacklist );
			$blacklist = array_filter( $blacklist );

			// No blacklisted email, skip.
			if ( empty( $blacklist ) ) {
				continue;
			}

			// if the email, domain or top-level domain isn't blacklisted, skip.
			if ( ! in_array( $email, $blacklist, true ) && ! in_array( $domain, $blacklist, true ) && ! in_array( $tld, $blacklist, true ) ) {
				continue;
			}

			/**
			 * Filter to allow third party plugins to set the email blacklist validation.
			 *
			 * @since 2.5.1
			 * @param bool   false      Default value.
			 * @param array  $field     The Field Object.
			 * @param string $email     The email entered in the input.
			 * @param string $domain    The full domain entered in the input.
			 * @param string $tld       The top level domain entered in the input.
			 * @param array  $blacklist List of the blocked emailed/domains.
			 */
			$validation_result['is_valid'] = apply_filters( 'gf_blacklist_is_valid', false, $field, $email, $domain, $tld, $blacklist );
			$field['failed_validation']    = true;

			// Set the validation message or use the default.
			if ( ! empty( $field['email_blacklist_validation'] ) ) {
				$validation_message = $field['email_blacklist_validation'];
			} elseif ( get_option( 'gravityformsaddon_' . $this->_slug . '_settings' ) ) {
				$validation_message = get_option( 'gravityformsaddon_' . $this->_slug . '_settings' );
				$validation_message = $validation_message['default_emailblacklist_error_msg'];
			} else {
				$validation_message = __( 'Sorry, the email address entered is not eligible for this form.', 'gravity-forms-email-blacklist' );
			}

			/**
			 * Filter to allow third party plugins to set the email blacklist validation.
			 *
			 * @since 2.5.1
			 * @param bool   $validation_message The custom validation method.
			 * @param array  $field              The Field Object.
			 * @param string $email              The email entered in the input.
			 * @param string $domain             The full domain entered in the input.
			 * @param string $tld                The top level domain entered in the input.
			 * @param array  $blacklist          List of the blocked emailed/domains.
			 */
			$field['validation_message'] = apply_filters( 'gf_blacklist_validation_message', $validation_message, $field, $email, $domain, $tld, $blacklist );
		}

		$validation_result['form'] = $form;
		return $validation_result;
	}

	/**
	 * Convert a sting to lowercase and remove extra whitespace. Thanks to @ractoon, @rscoates.
	 *
	 * @param string $string A string to sanitize.
	 * @return string Sanitize string
	 */
	protected function gf_emailblacklist_clean( $string ) {
		return strtolower( trim( $string ) );
	}

}
