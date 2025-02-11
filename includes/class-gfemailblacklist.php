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

	protected $_version                  = '2.6.0';
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


	/**
	 * The single instance of the class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    GFEmailBlacklist $_instance The single instance of the class.
	 */
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
		add_filter( 'gform_validation', array( $this, 'email_blacklist_validation' ), 10, 2 );
		add_filter( 'gf_blacklist_is_valid', array( $this, 'is_email_valid' ), 10, 6 );
		add_filter( 'gform_entry_is_spam', array( $this, 'mark_as_spam' ), 10, 3 );
		add_filter( 'gf_blacklist_is_spam', array( $this, 'is_email_valid' ), 10, 6 );
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
						'tooltip' => __( 'Please enter a comma separated list of blacklisted domains (ex. hotmail.com) and/or email addresses (ex. user@aol.com). You may include wildcard notation for either (*.com, jdoe@*, fake*@fakemail.*). This setting can be overridden on individual email fields in the advanced settings.', 'gravity-forms-email-blacklist' ),
						'class'   => 'medium',
					),
					array(
						'label'   => __( 'Global Validation Message', 'gravity-forms-email-blacklist' ),
						'type'    => 'text',
						'name'    => 'default_emailblacklist_error_msg',
						'tooltip' => __( 'Please enter a default error message if a blacklisted email is submitted. This setting can be overridden on individual email fields in the advanced settings.', 'gravity-forms-email-blacklist' ),
						'class'   => 'medium',
					),
					array(
						'label'         => __( 'Global Invalid Entry Procedure', 'gravity-forms-email-blacklist' ),
						'type'          => 'radio',
						'name'          => 'default_emailblacklist_handling',
						'default_value' => 'error',
						'choices'       => array(
							array(
								'label' => 'Validation Error',
								'value' => 'error',
							),
							array(
								'label' => 'Mark as Spam',
								'value' => 'spam',
							),
						),
						'tooltip'       => __( 'Please determine whether blacklisted emails get a validation error or are accepted and marked as spam.', 'gravity-forms-email-blacklist' ),
						'class'         => 'large',
					),
				),
			),
		);
	}

	/**
	 * Collect global settings.
	 *
	 * @return array
	 */
	public function get_global_settings() {
		$global_settings = get_option(
			'gravityformsaddon_' . $this->_slug . '_settings',
			array(
				'default_emailblacklist'           => '',
				'default_emailblacklist_error_msg' => '',
				'default_emailblacklist_handling'  => 'error',
			)
		);
		if ( ! isset( $global_settings['default_emailblacklist'] ) ) {
			$global_settings['default_emailblacklist'] = '';
		}
		if ( ! isset( $global_settings['default_emailblacklist_error_msg'] ) ) {
			$global_settings['default_emailblacklist_error_msg'] = '';
		}
		if ( ! isset( $global_settings['default_emailblacklist_handling'] ) ) {
			$global_settings['default_emailblacklist_handling'] = 'error';
		}
		return $global_settings;
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

		<li class="email_blacklist_handling field_setting">
			<fieldset id="fieldset_email_blacklist_handling">
				<label for="fieldset_email_blacklist_handling">
					<?php esc_html_e( 'Blacklisted Emails Invalid Entry Procedure', 'gravity-forms-email-blacklist' ); ?>
					<?php gform_tooltip( 'form_field_email_blacklist_handling' ); ?>
				</label>
				<div style="display:grid;gap:2px;">
					<input type="radio" name="field_email_blacklist_handling" id="field_email_blacklist_handling_global" value="global" onclick="SetFieldProperty('email_blacklist_handling', this.value);" onkeypress="SetFieldProperty('email_blacklist_handling', this.value);">
					<label for="field_email_blacklist_handling_global" class="inline">Use Global Setting</label>
					<input type="radio" name="field_email_blacklist_handling" id="field_email_blacklist_handling_error" value="error" onclick="SetFieldProperty('email_blacklist_handling', this.value);" onkeypress="SetFieldProperty('email_blacklist_handling', this.value);">
					<label for="field_email_blacklist_handling_error" class="inline">Validation Error</label>
					<input type="radio" name="field_email_blacklist_handling" id="field_email_blacklist_handling_spam" value="spam" onclick="SetFieldProperty('email_blacklist_handling', this.value);" onkeypress="SetFieldProperty('email_blacklist_handling', this.value);">
					<label for="field_email_blacklist_handling_spam" class="inline">Mark as Spam</label>
				</div>
				<br class="clear">
			</fieldset>
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
		$tooltips['form_field_email_blacklist']            = __( "Please enter a comma separated list of blacklisted domains, (ex. hotmail.com) and/or email addresses (ex. user@aol.com). You may include wildcard notation for either (*.com, jdoe@*, fake*@fakemail.*). This will override the globally defined blacklisted emails setting. Enter 'none' to bypass the global setting and allow all email addresses.", 'gravity-forms-email-blacklist' );
		$tooltips['form_field_email_blacklist_validation'] = __( 'Please enter an error message if a blacklisted email is submitted. This will override the globally defined error message.', 'gravity-forms-email-blacklist' );
		$tooltips['form_field_email_blacklist_handling']   = __( 'Please determine whether blacklisted emails get a validation error or are accepted and marked as spam.', 'gravity-forms-email-blacklist' );
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
			fieldSettings["email"] = fieldSettings["email"] + ", .email_blacklist_setting, .email_blacklist_validation, .email_blacklist_handling"; // this will show all fields that Paragraph Text field shows plus my custom setting

			// Binding to the load field settings event to initialize the checkbox.
			$(document).bind("gform_load_field_settings", function(event, field, form){
				$("#field_email_blacklist").val(field["email_blacklist"]);
				$("#field_email_blacklist_validation").val(field["email_blacklist_validation"]);

				if( "error" == field["email_blacklist_handling"] ) {
					$("#field_email_blacklist_handling_error").prop("checked", true).trigger("click");
				} else if ( "spam" == field["email_blacklist_handling"] ) {
					$("#field_email_blacklist_handling_spam").prop("checked", true).trigger("click");
				} else {
					$("#field_email_blacklist_handling_global").prop("checked", true).trigger("click");
				}

			});
		});
	</script>
		<?php
	}

	/**
	 * Add email blacklist to Gravity Forms validation function.
	 *
	 * @link https://docs.gravityforms.com/using-gform-validation-hook/
	 *
	 * @param array  $validation_result The validation result array.
	 * @param string $context           The context for the current submission.
	 *
	 * @return array The modified validation result.
	 */
	public function email_blacklist_validation( $validation_result, $context ) {
		// Get the global settings.
		$global_settings            = $this->get_global_settings();
		$default_blacklist          = $global_settings['default_emailblacklist'];
		$default_validation_message = $global_settings['default_emailblacklist_error_msg'];
		$default_handling           = $global_settings['default_emailblacklist_handling'];

		// Get the form object.
		$form = $validation_result['form'];

		foreach ( $form['fields'] as &$field ) {

			// Skip if not an email field or if field is hidden by GF conditional logic.
			if ( 'email' !== RGFormsModel::get_input_type( $field ) || RGFormsModel::is_field_hidden( $form, $field, array() ) ) {
				continue;
			}

			// Determine the blacklist handling for the field. If we are going to treat it as spam, skip.
			$blacklist_handling = ! empty( $field['email_blacklist_handling'] ) && 'global' !== $field['email_blacklist_handling'] ? $field['email_blacklist_handling'] : $default_handling;
			if ( 'spam' === $blacklist_handling ) {
				continue;
			}

			// Determine the blacklist for the field.
			$blacklist = ! empty( $field['email_blacklist'] ) ? $field['email_blacklist'] : $default_blacklist;
			// Extract the email input and parse email components.
			$email  = $this->gf_emailblacklist_clean( rgpost( "input_{$field['id']}" ) );
			$domain = $this->gf_emailblacklist_clean( rgar( explode( '@', $email ), 1 ) );
			$tld    = strrchr( $domain, '.' );

			// Apply the blacklist validation filter.
			$is_valid = apply_filters( 'gf_blacklist_is_valid', false, $field, $email, $domain, $tld, $blacklist );

			// If the email is not valid, set the field as failed and add the validation error message.
			if ( ! $is_valid ) {
				$field['failed_validation'] = true;
				// Retrieve or set a default validation message.
				$validation_message = ! empty( $field['email_blacklist_validation'] ) ? $field['email_blacklist_validation'] : $default_validation_message;
				// Apply a filter to customize the validation message.
				$field['validation_message']   = apply_filters( 'gf_blacklist_validation_message', $validation_message, $field, $email, $domain, $tld, $blacklist );
				$validation_result['is_valid'] = false;
			}
		}

		// Update the form object in the validation result and return.
		$validation_result['form'] = $form;
		return $validation_result;
	}

	/**
	 * Add email blacklist to gforms spam function.
	 *
	 * @resources: https://docs.gravityforms.com/gform_entry_is_spam/
	 *
	 * @param bool  $is_spam Indicates if the submission has been flagged as spam.
	 * @param array $form    The form currently being processed.
	 * @param array $entry   The entry currently being processed.
	 *
	 * @return bool Is the entry spam?
	 */
	public function mark_as_spam( $is_spam, $form, $entry ) {
		// If the entry is already spam, skip.
		if ( $is_spam ) {
			return $is_spam;
		}

		// Get the global settings.
		$global_settings   = $this->get_global_settings();
		$default_blacklist = $global_settings['default_emailblacklist'];
		$default_handling  = $global_settings['default_emailblacklist_handling'];

		foreach ( $form['fields'] as &$field ) {

			// Skip if not an email field or if field is hidden by GF conditional logic.
			if ( 'email' !== RGFormsModel::get_input_type( $field ) || RGFormsModel::is_field_hidden( $form, $field, array() ) ) {
				continue;
			}

			// If we are not going to treat it as spam, skip.
			$blacklist_handling = ! empty( $field['email_blacklist_handling'] ) && 'global' !== $field['email_blacklist_handling'] ? $field['email_blacklist_handling'] : $default_handling;
			if ( 'spam' !== $blacklist_handling ) {
				continue;
			}

			// Determine the blacklist for the field.
			$blacklist = ! empty( $field['email_blacklist'] ) ? $field['email_blacklist'] : $default_blacklist;
			// Extract the email input and parse email components.
			$email  = $this->gf_emailblacklist_clean( rgar( $entry, $field->id ) );
			$domain = $this->gf_emailblacklist_clean( rgar( explode( '@', $email ), 1 ) );
			$tld    = strrchr( $domain, '.' );

			$is_spam = ! apply_filters( 'gf_blacklist_is_spam', false, $field, $email, $domain, $tld, $blacklist );

			if ( $is_spam && method_exists( 'GFCommon', 'set_spam_filter' ) ) {
				GFCommon::set_spam_filter(
					rgar( $form, 'id' ),
					__( 'Gravity Forms Email Blacklist', 'gravity-forms-email-blacklist' ),
					/* translators: The placeholder is the email address. */
					sprintf( __( 'The email address %s is blacklisted.', 'gravity-forms-email-blacklist' ), $email )
				);
			}
		}

		return $is_spam;
	}

	/**
	 * Check if the email is valid given the blacklist.
	 *
	 * @param bool   $is_valid Whether the field passes validation.
	 * @param object $field    The field object.
	 * @param string $email    The email address.
	 * @param string $domain   The email domain.
	 * @param string $tld      The email TLD.
	 * @param string $blacklist The email blacklist.
	 *
	 * @return bool Whether the field passes blacklist validation.
	 */
	public function is_email_valid( $is_valid, $field, $email, $domain, $tld, $blacklist ) {

		/**
		 * Short-circuit filter for third-party plugins.
		 *
		 * @since  1.0.0
		 *
		 * @param bool   $short_circuit Whether to short-circuit the field validation.
		 * @param object $field    The field object.
		 * @param string $email    The email address.
		 * @param string $domain   The email domain.
		 * @param string $tld      The email TLD.
		 * @param string $blacklist The email blacklist.
		 *
		 * @return bool Whether the field passes blacklist validation.
		 */
		if ( apply_filters( 'gf_blacklist_validation_short_circuit', false, $field, $email, $domain, $tld, $blacklist ) ) {
			return true;
		}

		// Skip if 'none' is set.
		if ( 'none' === $this->gf_emailblacklist_clean( $blacklist ) ) {
			return true;
		}

		// Create the blacklist array from string if needed and clean it.
		$blacklist = is_array( $blacklist ) ? $blacklist : explode( ',', $blacklist );
		$blacklist = array_map( array( $this, 'gf_emailblacklist_clean' ), $blacklist );
		$blacklist = array_filter( $blacklist );

		// Skip if the blacklist is empty.
		if ( empty( $blacklist ) ) {
			return true;
		}

		// Create patterns from the blacklist and clean up the email username.
		$blacklist_patterns = array_map( array( $this, 'gf_emailblacklist_pattern' ), $blacklist );

		// Remove periods from the username part of the email since they all point to the same email address.
		$user  = $this->gf_emailblacklist_clean( rgar( explode( '@', $email ), 0 ) );
		$user  = str_replace( '.', '', $user );
		$email = $user . '@' . $domain;

		// Check the email against the blacklist patterns.
		foreach ( $blacklist_patterns as $pattern ) {
			if ( preg_match( $pattern, $email ) ) {
				return false; // Match found, return false to indicate blacklist.
			}
		}

		// No match found, pass the validation.
		return true;
	}

	/**
	 * Convert a sting to lowercase and remove extra whitespace. Thanks to @ractoon, @rscoates.
	 *
	 * @param string $text A string to sanitize.
	 * @return string Sanitize string
	 */
	public function gf_emailblacklist_clean( $text ) {
		return strtolower( trim( $text ) );
	}

	/**
	 * Convert each blacklist email entry into a regular expression search pattern.
	 *
	 * @param string $email An email to convert.
	 * @return string A regex pattern.
	 */
	protected function gf_emailblacklist_pattern( $email ) {

		if ( str_contains( $email, '@' ) ) {
			$array  = explode( '@', $email );
			$user   = $array[0];
			$domain = $array[1];
		} else {
			$user   = '*';
			$domain = $email;
		}

		// Avoid special characters in the email.
		if ( preg_match( '/[^a-zA-ZÀ-ÖØ-öø-ÿ0-9_\+\-\.\*]/', $user ) ) {
			return false;
		}
		if ( preg_match( '/[^a-zA-ZÀ-ÖØ-öø-ÿ0-9_\-\.\*]/', $domain ) ) {
			return false;
		}

		// Remove periods from username.
		$user = str_replace( '.', '', $user );

		// Create the regex pattern.
		$pattern = '/^' . $user . '(?:+*)?@' . $domain . '$/';

		$pattern = str_replace( '+', '\+', $pattern );
		$pattern = str_replace( '-', '\-', $pattern );
		$pattern = str_replace( '.', '\.', $pattern );
		$pattern = str_replace( '*', '.*?', $pattern );

		return $pattern;
	}

	/**
	 * Return the plugin's icon for the plugin/form settings menu.
	 *
	 * @return string
	 */
	public function get_menu_icon() {
		return file_get_contents( plugin_dir_url( __FILE__ ) . '/assets/blacklist-icon.svg' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}
}
