<?php
/*
Plugin Name: Gravity Forms Email Black List
Plugin URI:
Description: Validate submitted emails againts a list of blacklisted emails.
Version: 1.0
Author: Hall Internet Marketing
Author URI: http://hallme.com
License: GPL2
*/

// Now we execute some javascript technicalitites for the field to load correctly
add_action( "gform_editor_js", "blacklist_gform_editor_js" );
function blacklist_gform_editor_js(){
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

// Add a custom setting to the tos advanced field
add_action( "gform_field_advanced_settings" , "blacklist_email_blacklist_settings" , 10, 2 );
function blacklist_email_blacklist_settings( $position, $form_id ){

    // Create settings on position 50 (right after Field Label)
    if( $position == 50 ){
    ?>

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
add_filter('gform_tooltips', 'blacklist_add_tos_tooltips');
function blacklist_add_tos_tooltips($tooltips){
   $tooltips["form_field_email_blacklist"] = "<h6>Email Blacklist</h6> Please enter a comma separated list of domains you would like to block from submitting their email.";
   $tooltips["form_field_email_blacklist"] = "<h6>Validation Message</h6> Please enter the validation message you would like to appear if a blacklisted email is entered.";
   return $tooltips;
}

//Add email blacklist to gforms validation function
add_filter("gform_validation", "email_blacklist_validation");
function email_blacklist_validation($validation_result) {
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
		$ban_domains = explode(',',$field["email_blacklist"]);
		$ban_domains = array_map('trim',$ban_domains);

		// if domain is valid OR if the email field is empty, skip
		if(!in_array($domain, $ban_domains) || empty($domain))
			continue;

		$validation_result['is_valid'] = false;
		$field['failed_validation'] = true;

		//set the validation message or use the default
		if( empty($field["email_blacklist_validation"]) ) {
			$field['validation_message'] = sprintf(__('Sorry, <strong>%s</strong> email accounts are not eligible for this form.'), $domain);
		}else{
			$field['validation_message'] = $field["email_blacklist_validation"];
		}
	}

	$validation_result['form'] = $form;
	return $validation_result;
}
