=== Subscribe2 - Form, Email Subscribers & Newsletters ===
Contributors: tareq1988, nizamuddinbabu, wemail
Donate link: https://getwemail.io
Tags: posts, subscription, email, subscribe, notify, notification, newsletter, post notification, email marketing, optin, form
Requires at least: 4.7
Tested up to: 7.0
Stable tag: 10.50
Requires PHP: 5.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Sends a list of subscribers an email notification when you publish new posts.

== Description ==

= Summary =

Subscribe2 provides a comprehensive subscription management and email notification system for WordPress blogs that sends email notifications to a list of subscribers when you publish new content to your blog.

A full description of features is below.

= Email =

Email notifications can be sent on a per-post basis or periodically in a Digest email. Additionally, certain categories can be excluded from inclusion in the notification and posts can be excluded on an individual basis by setting a custom field.

= Subscriptions =

Subscription requests allows users to publicly subscribe (**Public Subscribers**) by submitting their email address in an easy to use form or to register with your blog (**Registered Users**) which enables greater flexibility over the email content for per-post notifications for the subscriber. Admins are given control over the presentation of the email notifications, can bulk manage subscriptions for users and manually send email notices to subscribers.

The format of the email can also be customised for per-post notifications, Subscribe2 can generate emails for each of the following formats:

* plaintext excerpt.
* plaintext full post (Registered Users only).
* HTML excerpt (Registered Users only).
* HTML full post (Registered Users only).

If you want to grow your subscriber lists, send automated campaigns to huge subscriber lists, you should upgrade to [weMail](https://getwemail.io).


= Privacy Policy =
Subscribe2 uses [Appsero](https://appsero.com) SDK to collect some telemetry data upon user's confirmation. This helps us to troubleshoot problems faster & make product improvements. Learn more about how [Appsero collects and uses this data](https://appsero.com/privacy-policy/).

== Installation ==

= AUTOMATIC INSTALLATION =

1. Log in to your WordPress blog and visit Plugins -> Add New.
2. Search for Subscribe2, click "Install Now" and then Activate the Plugin
3. Visit the "Subscribe2 -> Settings" menu.
4. Configure the options to taste, including the email template and any categories which should be excluded from notification
5. Visit the "Subscribe2 -> Subscribers" menu.
6. Manually subscribe people as you see fit.
7. Create a [WordPress Page](http://codex.wordpress.org/Pages) to display the subscription form.  When creating the page, you may click the "S2" button on the QuickBar to automatically insert the Subscribe2 token.  Or, if you prefer, you may manually insert the Subscribe2 shortcode or token: [subscribe2] or the HTML invisible `<!--subscribe2-->` ***Ensure the token is on a line by itself and that it has a blank line above and below.***
This token will automatically be replaced by dynamic subscription information and will display all forms and messages as necessary.
8. In the WordPress "Settings" area for Subscribe2 select the page name in the "Appearance" section that of the WordPress page created in step 7.

= MANUAL INSTALLATION =

1. Copy the entire /subscribe2/ directory into your /wp-content/plugins/ directory.
2. Activate the plugin.
3. Visit the "Subscribe2 -> Settings" menu.
4. Configure the options to taste, including the email template and any categories which should be excluded from notification
5. Visit the "Subscribe2 -> Subscribers" menu.
6. Manually subscribe people as you see fit.
7. Create a [WordPress Page](http://codex.wordpress.org/Pages) to display the subscription form.  When creating the page, you may click the "S2" button on the QuickBar to automatically insert the Subscribe2 token.  Or, if you prefer, you may manually insert the Subscribe2 shortcode or token: [subscribe2] or the HTML invisible `<!--subscribe2-->` ***Ensure the token is on a line by itself and that it has a blank line above and below.***
This token will automatically be replaced by dynamic subscription information and will display all forms and messages as necessary.
8. In the WordPress "Settings" area for Subscribe2 select the page name in the "Appearance" section that of the WordPress page created in step 7.

== Frequently Asked Questions ==

[Visit FAQ site](https://subscribe2.wordpress.com/support/faqs/)

== Changelog ==

= 10.50 (16th November, 2026) =

* Security: One-click unsubscribe links ({UNSUBLINK}) could be forged, allowing anyone who knew a public subscriber's email address to remove them from the list. Each link now carries a secret key and is only honoured when the key matches. Unsubscribe links in emails sent before this update no longer work; subscribers can still unsubscribe through the subscription form.
* Fix: When notification emails were sent individually, every recipient after the first received the first recipient's unsubscribe link. Each recipient now gets their own link.

= 10.49 (11th November, 2026) =

* Fix: The "Save Emails to CSV File" button on the Subscribers screen exported a file containing only the header row whenever more than one subscriber was listed, because the line breaks separating the addresses were stripped before the list was split. The list is now split and each address sanitized individually, restoring one row per subscriber.
* Fix: The exported CSV file no longer HTML-encodes its data. Subscriber display names and category names containing & or ' appeared as &amp; and &#039; in the downloaded file.

= 10.48 (23rd September, 2026) =

* Fix: Stored Cross-Site Scripting in the Subscribers list table. The signup IP address recorded for public subscribers was rendered unescaped, allowing content stored through the AJAX subscription form (available when the popup style subscription form is enabled) to execute in an administrator's browser. Subscriber email addresses in the list are now escaped as well.
* Fix: The AJAX subscription handler now validates the submitted IP address and stores an empty value when it is not a valid IP address.
* Fix: Submitting array values to the subscription form or the AJAX handler no longer causes a PHP fatal error on public pages.
* Enhance: get_remote_ip() now validates each address header and ignores values that are not valid IP addresses.

= 10.47 (10th September, 2026) =

* Fix: SQL injection in the bulk "change email format" action for registered subscribers. The submitted format value was interpolated into an UPDATE statement instead of being passed through $wpdb->prepare().
* Fix: Broken access control on the block editor REST endpoints. Sending a preview now requires permission to edit the specific post, resending a notification requires the same capability as the Send Email screen (manage_options, filterable through s2_capability) in addition to permission to edit the post, and the settings endpoint requires manage_options. The two defaults the block editor sidebar needs are now served by a separate read-only endpoint limited to those settings and to users who can edit posts.
* Fix: Block editor email preview always used the digest branch because the plugin options were read from an undefined property, which also caused an "Undefined array key" warning during preview.
* Fix: Subscription confirmation links no longer emit PHP warnings when the s2 parameter is missing or malformed (such as an array).
* Fix: PHP 8.2 "creation of dynamic property" deprecation notices by declaring previously undeclared class properties.
* Fix: PHP 8.1+ "passing null" deprecation notices when building notification emails.

= 10.46 (27th July, 2026) =

* Fix: The email request parameter was reflected unescaped into the email field's inline onfocus/onblur JavaScript handlers.
* Enhance: The email field prompt now uses the standard placeholder attribute instead of inline JavaScript handlers.

= 10.45 (29th Dec, 2025) =

* Enhance: Capability checks added for better security.

= 10.44 (20th Jan, 2025) =

* Fix: Security related issues.

= 10.43 (06th June, 2024) =

* Update: Appsero client library.
* Fix: Security related issues.

= 10.42 (12th October, 2023) =

* Fix: Subscribers searching filter can't search by email address.
* Fix: Post notification email is not sending to public subscribers.

= 10.41 (23rd June, 2023) =

* Fix: CSRF, Authorization issues when send an email.
* Fix: Use built-in sanitize_email instead of manual.

= 10.40 (24th January, 2023) =

* Fix: {UNSUBLINK} tag not working properly when send an email.
* Fix: subscribe2 emails no longer sending when publish a post.
* Fix: template entities not working properly in settings page (line breaks, tabs were lost etc.).
* Fix: subscribe2 notification overridden checkbox not working properly.
* Fix: debug error when uninstall plugin & use error suppression.
* Fix: email sending is always selected when multiple choose attachment.

* Enhance: add email attachment removing option from send email.

= 10.39 (23st December, 2022) =

 * Fix classic editor page meta field error.

= 10.38 (21st December, 2022) =

 * Fix security related issues & also make compatible with wp v6.1.1

= 10.37 (23rd November, 2021) =

 * Fix HTML widgets issue
 * Fix logged-in user can see subscription form

= 10.36 (30th September, 2021) =

 * WordPress 5.8 compatibility
 * Fix form preview on widgets
 * Fix pop up form
 * Fix send schedule email options
 * Fix one click subscription
 * Fix some PHP warnings

= 10.35 (15th March, 2021) =

 * Fix {UNSUBLINK} shortcode
 * WordPress 5.7 compatibility

= 10.34 (24th August, 2020) =

 * Fix gutenberg script loading issue

= 10.33 (4th June, 2020) =

 * Bump tested upto version 4.4
 * Minimum PHP version set to 5.4

See complete [changelog](https://github.com/weMail/Subscribe2/blob/develop/changelog.txt).
