Gravity Forms Email Blacklist
================
### Description

Email Blacklist Add-on for Gravity Forms was built to help block submissions from users with generic or competitors email addresses. Prevent the user form viewing the form's Confirmation or Notifications and remove non qualified leads from being collected.

This plugin allows site admins to create a list of domains that if used in an email field on a Gravity Form will cause a validation error and block the submission. A default email blacklist and validation message can be created to use across all form email fields. These default settings can be overridden on a per email field basis.

Default settings can be added in the  'Forms' > 'Settings' > 'Email Blacklist'. To add settings to an individual email field just add one to the form and got to the 'Advanced Settings' tab.

This plugin works by blocking either indavidual email addresses (example: jsmith@yahoo.com), email address domains (example: google.com), and/or, email address top-level domains (example: *.com).

### Installation

1. Search for and install the 'Gravity Forms Email Blacklist' OR Upload `gravity-forms-email-blacklist` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate form the Dashboard to the 'Forms' > 'Settings' > 'Email Blacklist' to make sure it is installed.

### Screenshots

1. General Settings
2. Form Settings
3. Form Settings Updated
4. Form Error

### Instructions

## General Blacklist Settings
Once set up these setting will be used on all email input fields across all the Gravity Forms used on the site. They can be overridden by the individual email blacklist settings below.

1. Once Logged into your site navigate to 'Forms' > 'Settings' > 'Email Blacklist'
2. Enter the email addresses (example: jsmith@gmail.com), email address domains (example: gmail.com), and/or, email address top-level domains (example: *.com) separated by commas into the 'Email Blacklist" field.
3. Enter the validation message you would like displayed to a user that try and use a blacklisted email when submitting a form into the 'Error Message' field.
4. Click the 'Update Settings' button to save the settings.

## Individual Email Input Blacklist Settings
Once set up these setting will be used on this form only and in place of the general blacklist settings above.
1. Once Logged into your site navigate to the Gravity Form you would like to update.
2. Add or update an existing Email input filed on the form.
3. Go to the 'Advance Settings' tab for the Email input.
4. Enter the email addresses (example: jsmith@gmail.com), email address domains (example: gmail.com), and/or, email address top-level domains (example: *.com) separated by commas into the 'Email Blacklist" field. If you enter a space this will override the general blacklist and allow all email address to be submitted.
5. Enter the validation message you would like displayed to a user that try and use a blacklisted email when submitting a form into the 'Error Message' field.
6. Click the 'Update' button to save the settings.

### Additional Resources
* [Gravity Forms Documentation - Getting Started](https://docs.gravityforms.com/category/getting-started/)
* [Gravity Forms Documentation - Email Input Fields](https://docs.gravityforms.com/email/)
* [Gravity Forms Documentation - Fighting Spam](https://docs.gravityforms.com/spam/)

### Changelog

# 2.5.2 #
* Fix: Updated method for getting the TLD to make sure the domain wildcards work in the case of a sub-domain email.

# 2.5.1 #
* Updated static setting strings to allow them to be translated.
* Added Text Domain.
* Added a function to Load Loads a plugin’s translated strings.
* Added filter to allow 3rd party plugins to alter the validation message before it is output.
* Added filter to allow 3rd party plugins to alter the is_valid check.
* Added short circuit filter to allow 3rd party plugins to jump over a check.

# 2.5 #
* Updated documentation, readme and added instructions.
* Updated version number and plugin information on compatibility and stable release.
* Fix: Updated validation function to account for email fields hidden by conditional logic

# 2.4
* Updated the plugin to allow the use of wildcards to block whole top-level domains.

# 2.3
* Updated the way the AddOn is initiated.
* Updated code to meet PHPCS code standards for WordPress.
* Updated version and compatibility numbers.
* Updated readme.txt.

# 2.2
* Added Author URL to GitHub Repo
* Fixed typo in readme.txt

# 2.1
* Added plugin icon

# 2.0
* Bug Fix: Removed 'var_dump' in validation function.
* Added 'gf_emailblacklist_clean' function to make comparison case insensitive. Thanks to @ractoon, @rscoates.
* Updated function to work with both email domains and specific emails.
* Ran 'class-gfemailblacklist.php' through PHPCS with WordPress Coding Standards.
* Moved class to '/includes/class-gfemailblacklist.php' file.
* Added placeholder text to fields
* Updated code comments.
* Updated plugin file name.
* Updated readme.txt and README.md

# 1.1
* Added custom validation message options to the email field under the 'Advanced' field settings
* Added default setting for the email blacklist and validation message to the form settings area to be used on all forms with an email field.

# 1.0
* Added email blacklist options to the email field under the 'Advanced' field settings
