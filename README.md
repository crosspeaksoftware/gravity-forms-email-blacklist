# Gravity Forms Email Blacklist

## Description

The Email Blacklist Add-on for Gravity Forms was built to help block submissions from users with generic or competitor email addresses. Prevent the user from processing the form and stop non-qualified leads from being collected.

This plugin allows site admins to create a list of domains that, if used in an email field on a Gravity Form, will cause a validation error and block the submission. A default email blacklist and validation message can be created to use across all email fields. These default settings can be overridden on a per-email field basis.

Global settings can be added under **Forms > Settings > Email Blacklist**. To add settings to an individual email field, select the field and navigate to the **Advanced Settings** tab.

This plugin works by blocking either individual email addresses (e.g., `jsmith@gmail.com`), email address domains (e.g., `gmail.com`), and/or email address top-level domains (e.g., `*.com`).

## Installation

1. Search for and install the 'Gravity Forms Email Blacklist' plugin, OR upload `gravity-forms-email-blacklist` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Navigate to **Forms > Settings > Email Blacklist** to configure the plugin.

## Screenshots

1. Global Plugin Settings
2. Email Field Settings
4. Form Validation Error

## Instructions

### Global Blacklist Settings

Once set up, these settings will apply to all email input fields across all Gravity Forms used on the site. They can be overridden by individual email blacklist settings.

1. Log in to your site and navigate to **Forms > Settings > Email Blacklist**.
1. In the **Global Blacklisted Emails** field, enter a comma-separated list of blacklisted domains (e.g., `hotmail.com`), email addresses (e.g., `user@aol.com`), and/or use wildcard notation to block top-level domains (e.g., `*.com`). These settings can be overridden in individual email fields under advanced settings.
1. In the **Global Validation Message** field, enter a default error message that will display if a blacklisted email is submitted. This setting can also be overridden in individual email fields.
1. Select the **Global Invalid Entry Procedure** to determine how the blacklisted submissions should be handled, with validation error or collected and marked as spam.
1. Click **Update Settings** to save your changes.

### Individual Email Input Blacklist Settings

These settings apply only to the selected form and override the global blacklist settings.

1. Log in to your site and navigate to the Gravity Form you want to update.
1. Add or edit an existing email input field on the form.
1. Go to the **Advanced Settings** tab for the field.
1. In the **Blacklisted Emails** field, enter a comma-separated list of blacklisted domains (e.g., `hotmail.com`), email addresses (e.g., `user@aol.com`), and/or use wildcard notation to block top-level domains (e.g., `*.com`). To bypass the global settings and allow all email addresses, enter `none`.
1. In the **Blacklisted Emails Validation Message** field, enter an error message to display if a blacklisted email is submitted. This setting overrides the global validation message.
1. Select the **Invalid Entry Procedure** to determine how the blacklisted submissions should be handled, with validation error or collected and marked as spam.
1. Click **Save Form** to apply your changes.

## Additional Resources

- [Gravity Forms Documentation - Getting Started](https://docs.gravityforms.com/category/getting-started/)
- [Gravity Forms Documentation - Email Input Fields](https://docs.gravityforms.com/email/)
- [Gravity Forms Documentation - Fighting Spam](https://docs.gravityforms.com/spam/)

## Developer Resources

### Filter: `gf_blacklist_validation_message`

This filter lets you customize the error message shown to users if their email is blacklisted based on form or field settings.

#### Parameters

* `$validation_message` (string): The error message to display.
* `$field` (object): The Gravity Forms field object.
* `$email` (string): The full email address being validated.
* `$domain` (string): The domain part of the email address.
* `$tld` (string): The top-level domain (TLD) of the email address.
* `$blacklist` (string): A comma-separated list of blacklisted emails.

#### Returns

(string): The error message displayed when the email is blacklisted.

---

### Filter: `gf_blacklist_is_valid`

This filter lets you determine whether an email passes blacklist validation during form submission.

#### Parameters

* `$is_valid` (bool): Indicates if the email passes validation (*default:* **FALSE**).
* `$field` (object): The Gravity Forms field object.
* `$email` (string): The full email address being validated.
* `$domain` (string): The domain part of the email address.
* `$tld` (string): The top-level domain (TLD) of the email address.
* `$blacklist` (string): A comma-separated list of blacklisted emails.

#### Returns

(bool): **TRUE** if the email is valid and not blacklisted; **FALSE** otherwise.

---

### Filter: `gf_blacklist_is_spam`

This filter lets you determine if a form entry should be flagged as spam based on email validation.

#### Parameters

* `$is_valid` (bool): Indicates if the email passes spam validation (*default:* **FALSE**).
* `$field` (object): The Gravity Forms field object.
* `$email` (string): The full email address being validated.
* `$domain` (string): The domain part of the email address.
* `$tld` (string): The top-level domain (TLD) of the email address.
* `$blacklist` (string): A comma-separated list of blacklisted emails.

#### Returns

(bool): **TRUE** if the email is valid and not flagged as spam; **FALSE** otherwise.

---

### Filter: `gf_blacklist_validation_short_circuit`

This filter lets you bypass the email blacklist validation process entirely for specific cases.

#### Parameters

* `$skip` (bool): Set to **TRUE** to skip blacklist validation (*default:* **FALSE**).
* `$field` (object): The Gravity Forms field object.
* `$email` (string): The full email address being validated.
* `$domain` (string): The domain part of the email address.
* `$tld` (string): The top-level domain (TLD) of the email address.
* `$blacklist` (string): A comma-separated list of blacklisted emails.

#### Returns

(bool): **FALSE** if the validation process was not skipped.

## Changelog

### 2.6.0
- **Enhancement:** Added support to treat submissions with blacklisted emails as spam.
- **Enhancement:** Added the abilty to include * for wildcards anywhere in the blacklisted address or domain.

### 2.5.6
- Fix PHP error under PHP 8+
- Fix with multiple email fields and different blacklists.

### 2.5.5
- Fixed version number issue

### 2.5.4
- Fixed validation function to remove any empty values for the array of blacklisted emails to prevent false positives from empty email fields.
- Added capability declination to the class to allow the plugin to work with role and capabilities plugins.

### 2.5.3
- Updated labels and descriptions throughout the admin settings to improve clarity.

### 2.5.2
- Fix: Updated method for getting the TLD to make sure the domain wildcards work in the case of a sub-domain email.

### 2.5.1
- Updated static setting strings to allow them to be translated.
- Added Text Domain.
- Added a function to Load Loads a plugin’s translated strings.
- Added filter to allow 3rd party plugins to alter the validation message before it is output.
- Added filter to allow 3rd party plugins to alter the is_valid check.
- Added short circuit filter to allow 3rd party plugins to jump over a check.

### 2.5
- Updated documentation, readme and added instructions.
- Updated version number and plugin information on compatibility and stable release.
- Fix: Updated validation function to account for email fields hidden by conditional logic

### 2.4
- Updated the plugin to allow the use of wildcards to block whole top-level domains.

### 2.3
- Updated the way the AddOn is initiated.
- Updated code to meet PHPCS code standards for WordPress.
- Updated version and compatibility numbers.
- Updated readme.txt.

### 2.2
- Added Author URL to GitHub Repo
- Fixed typo in readme.txt

### 2.1
- Added plugin icon

### 2.0
- Bug Fix: Removed 'var_dump' in validation function.
- Added 'gf_emailblacklist_clean' function to make comparison case insensitive. Thanks to @ractoon, @rscoates.
- Updated function to work with both email domains and specific emails.
- Ran 'class-gfemailblacklist.php' through PHPCS with WordPress Coding Standards.
- Moved class to '/includes/class-gfemailblacklist.php' file.
- Added placeholder text to fields
- Updated code comments.
- Updated plugin file name.
- Updated readme.txt and README.md

### 1.1
- Added custom validation message options to the email field under the 'Advanced' field settings
- Added default setting for the email blacklist and validation message to the form settings area to be used on all forms with an email field.

### 1.0
- Added email blacklist options to the email field under the 'Advanced' field settings
