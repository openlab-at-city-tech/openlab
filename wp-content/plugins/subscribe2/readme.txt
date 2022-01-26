=== Subscribe2 - Form, Email Subscribers & Newsletters ===
Contributors: tareq1988, nizamuddinbabu, wemail
Donate link: https://getwemail.io
Tags: posts, subscription, email, subscribe, notify, notification, newsletter, post notification, email marketing, optin, form
Requires at least: 4.0
Tested up to: 5.8
Stable tag: 10.37
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
