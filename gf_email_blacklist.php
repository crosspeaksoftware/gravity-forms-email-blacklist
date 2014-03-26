<?php
/*
Plugin Name: Gravity Forms Email Blacklist
Plugin URI: http://www.hallme.com
Description: This plugin adds the ability to set a blacklist of domains on the email field in gravity forms.
Version: 1.0
Author: Hall Internet Marketing (Tim Howe)
Author URI: http://www.hallme.com

------------------------------------------------------------------------
Copyright 2012-2013 Rocketgenius Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/


//------------------------------------------
if (class_exists("GFForms")) {
    GFForms::include_addon_framework();

    class GFEmailBlacklist extends GFAddOn {

        protected $_version = "1.0";
        protected $_min_gravityforms_version = "1.8";
        protected $_slug = "gf_email_blacklist";
        protected $_path = "gravityformsemailblacklist/gf_email_blacklist.php";
        protected $_full_path = __FILE__;
        protected $_title = "This plugin adds the ability to set a blacklist of domains on the email field in gravity forms.";
        protected $_short_title = "Email Blacklist";

		public function pre_init(){
            parent::pre_init();
            // add tasks or filters here that you want to perform during the class constructor - before WordPress has been completely initialized
        }

        public function init(){
            parent::init();
            add_filter( 'gform_submit_button', array($this, 'form_submit_button'), 10, 2 );
        }

        public function init_admin(){
            parent::init_admin();
			add_action( 'gform_editor_js', array($this, 'gf_emailblacklist_gform_editor_js') );
			add_action( 'gform_field_advanced_settings', array($this, 'gf_email_blacklist_field_settings'), 10, 2 );
			add_filter( 'gform_tooltips', array($this,'gf_emailblacklist_field_tooltips') );
        }

        public function init_frontend(){
            parent::init_frontend();
			add_filter( 'gform_validation', array($this,'gf_emailblacklist_validation') );
        }

        public function init_ajax(){
            parent::init_ajax();
            // add tasks or filters here that you want to perform only during ajax requests
        }

        // Add the text in the plugin settings to the bottom of the form if enabled for this form
        function form_submit_button($button, $form){
            $settings = $this->get_form_settings($form);
            if(isset($settings["enabled"]) && true == $settings["enabled"]){
                $text = $this->get_plugin_setting("mytextbox");
                $button = "<div>{$text}</div>" . $button;
            }
            return $button;
        }


		// Add a custom setting to the tos advanced field
		function gf_email_blacklist_field_settings( $position, $form_id=null ){

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

		//Filter to add a new tooltip
		function gf_emailblacklist_field_tooltips($tooltips){
		   $tooltips["form_field_email_blacklist"] = "<h6>Email Blacklist</h6> Please enter a comma separated list of domains you would like to block from submitting. Enter 'none' if you would like to override the default blacklist settings.";
		   $tooltips["form_field_email_blacklist_validation"] = "<h6>Validation Message</h6> Please enter the validation message you would like to appear if a blacklisted email is entered.";
		   return $tooltips;
		}

		// Now we execute some javascript technicalitites for the field to load correctly
		function gf_emailblacklist_gform_editor_js(){ ?>
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

		//Add email blacklist to gforms validation function
		function gf_emailblacklist_validation($validation_result) {
			//collect form results
			$form = $validation_result['form'];
			//loop through results
			foreach($form['fields'] as &$field) {

				// if this is not an email field, skip
				if(RGFormsModel::get_input_type($field) != 'email')
					continue;

				//get the domain from user enterd email
				$email = explode('@', rgpost("input_{$field['id']}"));
				$domain = rgar($email, 1);

				//collect banned domains from backend and clean up
				if( !empty($field["email_blacklist"]) ){ //collect per form settings
					$ban_domains = $field["email_blacklist"];
								var_dump($ban_domains);
				}elseif( get_option('gravityformsaddon_'.$this->_slug.'_settings') ){ //collect default settings
					$ban_domains = get_option('gravityformsaddon_'.$this->_slug.'_settings');
					$ban_domains = $ban_domains['default_emailblacklist'];
				}else{
					//no else value
				}

				//create array of banned domains
				$ban_domains = explode(',',$ban_domains);
				$ban_domains = array_map('trim',$ban_domains);

				// if domain is valid OR if the email field is empty, skip
				if(!in_array($domain, $ban_domains) || empty($domain))
					continue;

				$validation_result['is_valid'] = false;
				$field['failed_validation'] = true;

				//set the validation message or use the default
				if( !empty($field["email_blacklist_validation"]) ) {
					$validation_message = $field["email_blacklist_validation"];
				}elseif( get_option('gravityformsaddon_'.$this->_slug.'_settings')  ){
					$validation_message = get_option('gravityformsaddon_'.$this->_slug.'_settings');
					$validation_message = $validation_message['default_emailblacklist_error_msg'];
				}else{
					$validation_message = sprintf(__('Sorry, <strong>%s</strong> email accounts are not eligible for this form.'), $domain);
				}
				$field['validation_message'] = $validation_message;
			}

			$validation_result['form'] = $form;
			return $validation_result;
		}

        public function plugin_settings_fields() {
            return array(
                array(
                    "title"  => "Default Settings",
                    "fields" => array(
						array(
                            "label"   => "Email Blacklist",
                            "type"    => "text",
                            "name"    => "default_emailblacklist",
                            "tooltip" => "<h6>Email Blacklist</h6> Please enter a comma separated list of domains you would like to block from submitting their email. These setting can be overwritten on a per field basis",
                            "class"   => "medium"
                        ),
						array(
                            "label"   => "Error Message",
                            "type"    => "text",
                            "name"    => "default_emailblacklist_error_msg",
                            "tooltip" => "<h6>Validation Message</h6> Please enter the validation message you would like to appear if a blacklisted email is entered. These setting can be overwritten on a per field basis",
                            "class"   => "medium"
                        )
                    )
                )
            );
        }
    }

    new GFEmailBlacklist();
}