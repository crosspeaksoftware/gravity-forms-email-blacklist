==== Gravity Forms Email Blacklist ====
Contributors: crosspeak, hallme, timbhowe, matt-h-1
Donate link: https://www.crosspeaksoftware.com/process-payment/
Tags: gravity forms, email blacklist, block email, blacklist
Requires at least: 3.8
Tested up to: 6.7.2
Stable tag: 2.6.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An add-on plugin for Gravity Forms that allows the Blacklisting of specific emails or email domains that are entered in [Email input fields](https://docs.gravityforms.com/email/) to throw a validation error and blocking the form submission.

=== Description ===

The Email Blacklist Add-on for Gravity Forms was built to help block submissions from users with generic or competitors email addresses. Prevent the user from processing the form and stop non-qualified leads from being collected.

This plugin allows site admins to create a list of domains that if used in an email field on a Gravity Form it will cause a validation error and block the submission. A default email blacklist and validation message can be created to use across all email fields. These default settings can be overridden on a per email field basis.

Global settings can be added on 'Forms' > 'Settings' > 'Email Blacklist'. To add settings to an individual email field, select the field and navigate to the 'Advanced Settings' tab.

This plugin works by blocking either individual email addresses (ex. jsmith@gmail.com), email address domains (ex. gmail.com), and/or email address top-level domains (ex. *.com).

Feel free to contribute on [github](https://github.com/crosspeaksoftware/gravity-forms-email-blacklist).

=== Installation ===

1. Search for and install the 'Gravity Forms Email Blacklist' OR Upload `gravity-forms-email-blacklist` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate from the Dashboard to the 'Forms' > 'Settings' > 'Email Blacklist' to make sure it is installed.

=== Screenshots ===

1. Global Plugin Settings
2. Email Field Settings
3. Form Validation Error

=== Instructions ===

== Global Blacklist Settings ==
Once set up, these settings will be used on all email input fields across all the Gravity Forms used on the site. They can be overridden by the individual email blacklist settings below.

1. Once Logged into your site navigate to 'Forms' > 'Settings' > 'Email Blacklist'
2. In the 'Global Blacklisted Emails' input enter a comma separated list of blacklisted domains (ie. hotmail.com), email addresses (ie. user@aol.com), and/or include the wildcard notation to block top-level domains (ie. *.com). This setting can be overridden on individual email fields in the advanced settings.
3. In the 'Global Validation Message' input enter a default error message if a blacklisted email is submitted. This setting can be overridden on individual email fields in the advanced settings.
4. Select the 'Global Invalid Entry Procedure' to determine how the blacklisted submissions should be handled, with validation error or collected and marked as spam.
5. Click the 'Update Settings' button to save the settings.

== Individual Email Input Blacklist Settings ==
Once set up these settings will be used on this form only and in place of the global blacklist settings above.

1. Once Logged into your site navigate to the Gravity Form you would like to update.
2. Add or update an existing email input field on the form.
3. Go to the 'Advanced Settings' tab for the 'Blacklisted Emails' input.
4. In the 'Blacklisted Emails' input enter a comma separated list of blacklisted domains (ie. hotmail.com), email addresses (ie. user@aol.com), and/or include the wildcard notation to block top-level domains (ie. *.com). This will override the globally defined blacklisted emails setting. Enter 'none' to bypass the global setting and allow all email addresses.
5. In the 'Blacklisted Emails Validation Message' input enter an error message if a blacklisted email is submitted. This will override the globally defined error message.
6. Select the 'Invalid Entry Procedure' to determine how the blacklisted submissions should be handled, with validation error or collected and marked as spam.
7. Click the 'Save Form' button to save the settings.

=== Additional Resources ===
* [Gravity Forms Documentation - Getting Started](https://docs.gravityforms.com/category/getting-started/)
* [Gravity Forms Documentation - Email Input Fields](https://docs.gravityforms.com/email/)
* [Gravity Forms Documentation - Fighting Spam](https://docs.gravityforms.com/spam/)

=== Changelog ===

= 2.6.0 =
* Enhancement: Added support to treat submissions with blacklisted emails as spam.
* Enhancement: Added the ability to include * for wildcards anywhere in the blacklisted address or domain.

= 2.5.6 =
* Fix PHP error under PHP 8+
* Fix with multiple email fields and different blacklists.

= 2.5.5 =
* Fixed version number issue

= 2.5.4 =
* Fixed validation function to remove any empty values for the array of blacklisted emails to prevent false positives from empty email fields.
* Added capability declination to the class to allow the plugin to work with role and capabilities plugins.

= 2.5.3 =
* Updated labels and descriptions throughout the admin settings to improve clarity.

= 2.5.2 =
* Fix: Updated method for getting the TLD to make sure the domain wildcards work in the case of a sub-domain email.

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
* Ran 'class-gfemailblacklist.php' through [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer/wiki) with [WordPress Coding Standards](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki).
* Moved class to '/includes/class-gfemailblacklist.php' file.
* Added placeholder text to fields
* Updated code comments.
* Updated plugin file name.
* Updated readme.txt and README.md

= 1.1 =
* Added custom validation message options to the email field under the 'Advanced' field settings
* Added default setting for the email blacklist and validation message to the form settings area to be used on all forms with an email field.

= 1.0 =
* Added email blacklist options to the email field under the 'Advanced' field settings
