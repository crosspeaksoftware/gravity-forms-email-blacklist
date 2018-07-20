<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'GFForms' ) ) {
	GFForms::include_addon_framework();

	class GFEmailBlacklist extends GFAddOn {

		protected $_version                  = '1.2';
		protected $_min_gravityforms_version = '1.8';
		protected $_slug                     = 'gf_email_blacklist';
		protected $_path                     = 'gravityformsemailblacklist/gf_email_blacklist.php';
		protected $_full_path                = __FILE__;
		protected $_title                    = 'This plugin adds the ability to set a blacklist of domains on the email field in gravity forms.';
		protected $_short_title              = 'Email Blacklist';

		/**
		 * Add tasks or filters here that you want to perform both in the backend and frontend and for ajax requests.
		 */
		public function init() {
			parent::init();
			add_filter( 'gform_submit_button', array( $this, 'form_submit_button' ), 10, 2 );
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
		 * Add the text in the plugin settings to the bottom of the form if enabled for this form.
		 */
		public function form_submit_button( $button, $form ) {
			$settings = $this->get_form_settings( $form );
			if ( isset( $settings['enabled'] ) && true === $settings['enabled'] ) {
				$text   = $this->get_plugin_setting( 'mytextbox' );
				$button = "<div>{$text}</div>" . $button;
			}
			return $button;
		}


		/**
		 * Add the additional Email Blacklist
		 *
		 * @return array Additional plugin setting fields in the Gravity Forms Settings API.
		 */
		public function plugin_settings_fields() {
			return array(
				array(
					'title'  => 'Default Settings',
					'fields' => array(
						array(
							'label'   => 'Email Blacklist',
							'type'    => 'text',
							'name'    => 'default_emailblacklist',
							'tooltip' => '<h6>Email Blacklist</h6> Please enter a comma separated list of domains you would like to block from submitting their email. These setting can be overwritten on a per field basis',
							'class'   => 'medium',
						),
						array(
							'label'   => 'Error Message',
							'type'    => 'text',
							'name'    => 'default_emailblacklist_error_msg',
							'tooltip' => '<h6>Validation Message</h6> Please enter the validation message you would like to appear if a blacklisted email is entered. These setting can be overwritten on a per field basis',
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
				$emailblacklist     = __( 'Global Email Blacklist: ' ) . $validation_message['default_emailblacklist'];
				$emailblacklist_msg = __( 'Global Error Message: ' ) . $validation_message['default_emailblacklist_error_msg'];
			} else {
				$emailblacklist     = __( 'Set Blacklist Emails' );
				$emailblacklist_msg = __( 'Set Error Message' );
			}

			// Create settings on position 50 (right after Field Label)
			if ( 50 === $position ) { ?>

			<li class="email_blacklist_setting field_setting">
				<label for="field_email_blacklist">
					<?php esc_html_e( 'Blacklisted Emails' ); ?>
					<?php gform_tooltip( 'form_field_email_blacklist' ); ?>
				</label>
				<input type="text" id="field_email_blacklist" class="fieldwidth-3" size="35" onkeyup="SetFieldProperty('email_blacklist', this.value);" placeholder="<?php echo esc_attr( $emailblacklist ); ?>">
			</li>

			<li class="email_blacklist_validation field_setting">
				<label for="field_email_blacklist_validation">
					<?php esc_html_e( 'Blacklisted Emails Validation Message' ); ?>
					<?php gform_tooltip( 'form_field_email_blacklist_validation' ); ?>
				</label>
				<input type="text" id="field_email_blacklist_validation" class="fieldwidth-3" size="35" onkeyup="SetFieldProperty('email_blacklist_validation', this.value);" placeholder="<?php echo esc_attr( $emailblacklist_msg ); ?>">
			</li>
			<?php
			}
		}

		// Filter to add a new tooltip.
		public function gf_emailblacklist_field_tooltips( $tooltips ) {
			$tooltips['form_field_email_blacklist']            = "<h6>Email Blacklist</h6> Please enter a comma separated list of domains you would like to block from submitting. Enter 'none' if you would like to override the default blacklist settings.";
			$tooltips['form_field_email_blacklist_validation'] = '<h6>Validation Message</h6> Please enter the validation message you would like to appear if a blacklisted email is entered.';
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
		 * @param  array $validation_result Contains the validation result and the current.
		 *
		 * @return array The field validation results.
		 */
		public function gf_emailblacklist_validation( $validation_result ) {
			// Collect form results.
			$form = $validation_result['form'];
			// Loop through results.
			foreach ( $form['fields'] as &$field ) {

				// If this is not an email field, skip.
				if ( 'email' !== RGFormsModel::get_input_type( $field ) ) {
					continue;
				}

				// Get the domain from user enterd email.
				$email  = gf_emailblacklist_clean( rgpost( "input_{$field['id']}" ) );
				$domain = gf_emailblacklist_clean( rgar( explode( '@', $email ), 1 ) );

				// Collect banned domains from backend and clean up.
				if ( ! empty( $field['email_blacklist'] ) ) { // collect per form settings
					$blacklist = $field['email_blacklist'];
				} else { // Collect default settings.
					$blacklist = get_option( 'gravityformsaddon_' . $this->_slug . '_settings' );
					$blacklist = $blacklist['default_emailblacklist'];
				}

				// Create array of banned domains.
				$blacklist = explode( ',', $blacklist );
				$blacklist = array_map( array( $this, 'gf_emailblacklist_clean' ), $blacklist );

				// if domain is valid OR if the email field is empty, skip
				if ( ! in_array( $email, $blacklist, true ) && ! in_array( $domain, $blacklist, true ) ) {
					continue;
				}

				$validation_result['is_valid'] = false;
				$field['failed_validation']    = true;

				// Set the validation message or use the default.
				if ( ! empty( $field['email_blacklist_validation'] ) ) {
					$validation_message = $field['email_blacklist_validation'];
				} elseif ( get_option( 'gravityformsaddon_' . $this->_slug . '_settings' ) ) {
					$validation_message = get_option( 'gravityformsaddon_' . $this->_slug . '_settings' );
					$validation_message = $validation_message['default_emailblacklist_error_msg'];
				} else {
					$validation_message = __( 'Sorry, the email address entered is not eligible for this form.' );
				}
				$field['validation_message'] = $validation_message;
			}

			$validation_result['form'] = $form;
			return $validation_result;
		}

		/**
		 * Convert a sting to lowercase and remove extra whitespace. Thanks to @ractoon, @rscoates.
		 *
		 * @param string $string A string to sanitize
		 *
		 * @return string Sanitize string
		 */
		protected function gf_emailblacklist_clean( $string ) {
			return strtolower( trim( $string ) );
		}

	}

	new GFEmailBlacklist();
}
