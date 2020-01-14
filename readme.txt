=== Gravity Forms Email Blacklist ===
Contributors: hallme, timbhowe
Donate link: N/A
Tags: gravity forms, gravity form, forms, gravity, form, email blacklist, block email, blacklist
Requires at least: 3.8
Tested up to: 5.3.2
Stable tag: 2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add-on plugin for Gravity Forms allowing you to specify specific emails or email domains that will throw a validation error blocking the form submission.

== Description ==

Email Blacklist Add-on for Gravity Forms was built to help block submissions from users with generic or competitors email addresses. Prevent the user form viewing the form's Confirmation or Notifications and remove non qualified leads from being collected.

This plugin allows site admins to create a list of domains that if used in an email field on a Gravity Form will cause a validation error and block the submission. A default email blacklist and validation message can be created to use across all form email fields. These default settings can be overridden on a per email field basis.

Default settings can be added in the  'Forms' > 'Settings' > 'Email Blacklist'. To add settings to an individual email field just add one to the form and got to the 'Advanced Settings' tab.

This plugin works by blocking either indavidual email addresses (exmple: jsmith@yahoo.com), email address domains (exmple: google.com), and/or, email address top-level domains (exmple: *.com).

== Installation ==

1. Search for and install the 'Gravity Forms Email Blacklist' OR Upload `gravityforms-emailblacklist` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate form the Dashboard to the 'Forms' > 'Settings' > 'Email Blacklist' to make sure it is installed.

== Screenshots ==

1. General Settings
2. Form Settings
3. Form Settings Updated
4. Form Error

== Changelog ==

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
