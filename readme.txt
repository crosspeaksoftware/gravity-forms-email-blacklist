==== Gravity Forms Email Blacklist ====
Contributors: hallme, timbhowe
Donate link: N/A
Tags: gravity forms, gravity form, forms, gravity, form, email blacklist, block email, blacklist
Requires at least: 3.8
Tested up to: 6.0.1
Stable tag: 2.5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An add-on plugin for Gravity Forms that allows the Blacklisting of specific emails or email domains that are entered in [Email input fields](https://docs.gravityforms.com/email/) to throw a validation error and blocking the form submission.

=== Description ===

Email Blacklist Add-on for Gravity Forms was built to help block submissions from users with generic or competitors email addresses. Prevent the user form viewing the form's Confirmation or Notifications and remove non qualified leads from being collected.

This plugin allows site admins to create a list of emails, domains and/or top level domains that if used in an [email input field](https://docs.gravityforms.com/email/) on a Gravity Form will cause a validation error and block the submission. A default email blacklist and validation message can be created to use across all forms. These default settings can be overridden on a per email input field basis.

Default settings can be added in the  'Forms' > 'Settings' > 'Email Blacklist'. To add settings to an individual email input field just add one to the form and got to the input fields 'Advanced Settings' tab.

This plugin works by blocking either individual email addresses (exmple: jsmith@yahoo.com), email address domains (exmple: google.com), and/or, email address top-level domains (exmple: *.com).

=== Installation ===

1. Search for and install the 'Gravity Forms Email Blacklist' OR Upload `gravityforms-emailblacklist` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate form the Dashboard to the 'Forms' > 'Settings' > 'Email Blacklist' to make sure it is installed.

=== Screenshots ===

1. General Settings
2. Form Settings
3. Form Settings Updated
4. Form Error

=== Instructions ===

== General Blacklist Settings ==
Once set up these setting will be used on all email input fields across all the Gravity Forms used on the site. They can be overridden by the individual email blacklist settings below.

1. Once Logged into your site navigate to 'Forms' > 'Settings' > 'Email Blacklist'
2. Enter the email addresses (exmple: jsmigh@gmail.com), email address domains (exmple: gmail.com), and/or, email address top-level domains (exmple: *.com) separated by commas into the 'Email Balcklist" field.
3. Enter the validation message you would like displayed to a user that try and use a blacklisted email when submitting a form into the 'Error Message' field.
4. Click the 'Update Settings' button to save the settings.

== Individual Email Input Blacklist Settings ==
Once set up these setting will be used on this form only and in place of the general blacklist settings above.
1. Once Logged into your site navigate to the Gravity Form you would like to update.
2. Add or update an existing Email input filed on the form.
3. Go to the 'Advance Settings' tab for the Email input.
4. Enter the email addresses (exmple: jsmigh@gmail.com), email address domains (exmple: gmail.com), and/or, email address top-level domains (exmple: *.com) separated by commas into the 'Email Balcklist" field. If you enter a space this will override the general blacklist and allow all email address to be submitted.
5. Enter the validation message you would like displayed to a user that try and use a blacklisted email when submitting a form into the 'Error Message' field.
6. Click the 'Update' button to save the settings.

=== Additional Resources ===
* [Gravity Forms Documentation - Getting Started](https://docs.gravityforms.com/category/getting-started/)
* [Gravity Forms Documentation - Email Input Fields](https://docs.gravityforms.com/email/)
* [Gravity Forms Documentation - Fighting Spam](https://docs.gravityforms.com/spam/)

=== Changelog ===

= 2.5.1 =
* Updated static setting strings to allow them to be translated.
* Added Text Domain.
* Added a function to Load Loads a plugin’s translated strings.
* Added filter to allow 3rd party plugins to alter the validation message before it is output.
* Added filter to allow 3rd party plugins to alter the is_valid check.
* Added short circuit filter to allow 3rd party plugins to jump over a check.

= 2.5 =
* Updated documentation, readme and added instructions.
* Updated version number and plugin information on compatibility and stable release.
* Fix: Updated validation function to account for email fields hidden by conditional logic

= 2.4 =
* Updated the plugin to allow the use of wildcards to block whole top-level domains.

= 2.3 =
* Updated the way the AddOn is initiated.
* Updated code to meet PHPCS code standards for WordPress.
* Updated version and compatibility numbers.
* Updated readme.txt.

= 2.2 =
* Added Author URL to GitHub Repo
* Fixed typo in readme.txt

= 2.1 =
* Added plugin icon

= 2.0 =
* Bug Fix: Removed 'var_dump' in validation function.
* Added 'gf_emailblacklist_clean' function to make comparison case insensitive. Thanks to @ractoon, @rscoates.
* Updated function to work with both email domains and specific emails.
* Ran 'class-gfemailblacklist.php' through [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer/wiki) with [WordPress Codeing Standards](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki).
* Moved class to '/inlcudes/class-gfemailblacklist.php' file.
* Added placeholder text to fileds
* Updated code comments.
* Updated plugin file name.
* Updated readme.txt and README.md

= 1.1 =
* Added custom validation message options to the email field under the 'Advanced' field settings
* Added default setting for the email blacklist and validation message to the form settings area to be used on all forms with an email field.

= 1.0 =
* Added email blacklist options to the email field under the 'Advanced' field settings
