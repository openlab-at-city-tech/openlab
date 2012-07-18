=== Subscribe2 ===
Contributors: MattyRob, Skippy, RavanH
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=2387904
Tags: posts, subscription, email, subscribe, notify, notification
Requires at least: 3.1
Tested up to: 3.4
Stable tag: 8.4
License: GPL3

Sends a list of subscribers an email notification when new posts are published to your blog

== Description ==

Subscribe2 provides a comprehensive subscription management and email notification system for WordPress blogs that sends email notifications to a list of subscribers when you publish new content to your blog.

Email Notifications can be sent on a per-post basis or periodically in a Digest email. Additionally, certain categories can be excluded from inclusion in the notification and posts can be excluded on an individual basis by setting a custom field.

The plugin also handles subscription requests allowing users to publicly subscribe (**Public Subscribers**) by submitting their email address in an easy to use form or to register with your blog (**Registered Users**) which enables greater flexibility over the email content for per-post notifications for the subscriber. Admins are given control over the presentation of the email notifications, can bulk manage subscriptions for users and manually send email notices to subscribers.

The format of the email can also be customised for per-post notifications, subscribe2 can generate emails for each of the following formats:

* plaintext excerpt
* plaintext full post (Registered Users only)
* HTML excerpt (Registered Users only)
* HTML full post (Registered Users only)

If you want to send full content HTML emails to Public Subscribers too then upgrade to [Subscribe2 HTML](http://wpplugins.com/plugin/46/subscribe2-html).

== Installation ==

AUTOMATIC INSTALLATION

1. Log in to your WordPress blog and visit Plugins->Add New.
2. Search for Subscribe2, click "Install Now" and then Activate the Plugin
3. Click the "Settings" admin menu link, and select "Subscribe2".
4. Configure the options to taste, including the email template and any categories which should be excluded from notification
5. Click the "Tools" admin menu link, and select "Subscribers".
6. Manually subscribe people as you see fit.
7. Create a [WordPress Page](http://codex.wordpress.org/Pages) to display the subscription form.  When creating the page, you may click the "S2" button on the QuickBar to automatically insert the subscribe2 token.  Or, if you prefer, you may manually insert the subscribe2 shortcode or token: [subscribe2] or the HTML invisible `<!--subscribe2-->` ***Ensure the token is on a line by itself and that it has a blank line above and below.***
This token will automatically be replaced by dynamic subscription information and will display all forms and messages as necessary.
8. In the WordPress "Settings" area for Subscribe2 select the page name in the "Appearance" section that of the WordPress page created in step 7.

MANUAL INSTALLATION

1. Copy the entire /subscribe2/ directory into your /wp-content/plugins/ directory.
2. Activate the plugin.
3. Click the "Settings" admin menu link, and select "Subscribe2".
4. Configure the options to taste, including the email template and any categories which should be excluded from notification
5. Click the "Tools" admin menu link, and select "Subscribers".
6. Manually subscribe people as you see fit.
7. Create a [WordPress Page](http://codex.wordpress.org/Pages) to display the subscription form.  When creating the page, you may click the "S2" button on the QuickBar to automatically insert the subscribe2 token.  Or, if you prefer, you may manually insert the subscribe2 shortcode or token: [subscribe2] or the HTML invisible `<!--subscribe2-->` ***Ensure the token is on a line by itself and that it has a blank line above and below.***
This token will automatically be replaced by dynamic subscription information and will display all forms and messages as necessary.
8. In the WordPress "Settings" area for Subscribe2 select the page name in the "Appearance" section that of the WordPress page created in step 7.

== Frequently Asked Questions ==

= I want HTML email to be the default email type =

You need to pay for the [Subscribe2 HTML version](http://wpplugins.com/plugin/46/subscribe2-html).

= Where can I get help? =
So, you've downloaded the plugin an it isn't doing what you expect. First you should read the included documentation. There is a ReadMe.txt file and a PDF startup guide installed with the plugin.

Next you could search in the [WordPress forums](http://wordpress.org/support/), the old [Subscribe2 Forum](http://getsatisfaction.com/subscribe2/), or the [Subscribe2 blog FAQs](http://subscribe2.wordpress.com/category/faq/).

If you can't find an answer then post a new topic at the [WordPress forums](http://wordpress.org/support/) or make a [donation](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2387904) to get my attention!

= Where can I get more information about the plugin features? =

A comprehensive guide that covers many, if not all, of the Subscribe2 features is available to purchase from the [My WP Works](http://mywpworks.com/store/subscribe2-ebook/)

= Sending post notifications or email with Subscribe2 =

Subscribe2 sends an email at the very moment the post is published. Since Subscribe2 sends live mail with no un-do, it's important to use the Preview function in WordPress to make sure the post has been edited to perfection *before* moving it from Draft to Published mode.

Mail is sent when a post is published - it will not be re-sent if you Update the post later. If you need to send a mailing a second time (e.g. during testing), switch the post to Draft mode, then re-publish it.

You can also manually send emails to groups of your subscribers using the Send Email page that the plugin creates in the WordPress administration area.

= Where can I find the HTML and CSS templates? =

While the template field in Settings | Subscribe2 does not display HTML by default, feel free to add HTML to it as needed. You can insert references to static images for use as banners, wrap sections of the template in divs or other elements, or do whatever you like.

There is no need to include HTML header data or body tags - just focus on the HTML content, in conjunction with the template tags documented on the settings page.

Subscribe2 does not maintain a separate stylesheet for the emails it generates. Instead, it uses the CSS of your currently active WordPress theme. If you need new/custom styles specific to your newsletter that aren't included in your theme stylesheet, try adding elements such as div id="newsletter_sidebar" to your HTML, with corresponding #newsletter_sidebar rules in your stylesheet.

Note that if you ever change your site theme, you'll need to copy these additions over to the new theme's stylesheet. To avoid this problem, consider placing a custom CSS file on your server outside of your theme directory, and link to it from the template, thus overriding the active theme styles permanently.

= Some or all email notifications fail to send, why?  =
In the first instance **check this with your hosting provider**, they have access to your server logs and will be able to tell you where and why emails are being blocked.

This is by far the most common question I am asked and the most frequent issue that arises. Without fail it is always down to a server side limitation or restriction.

These restrictions broadly fall into one of three areas. These are the sender details, the header details and restrictions on the number of messages sent.

**Sender Details**. You may need to ensure that the email notification is being sent from an email address on the same domain as your blog. So, if your blog is http://www.example.com the email should be something like admin@example.com. To do this go to Subscribe2->Settings and carefully select from the dropdown list where is says "Send Email From". Here you will see "Post Author", then the name of your blog and then the names of your administrator level users. It may be wise to set up a dummy user account specifically to send the emails from and make sure you give that account an on domain email address.

**Header Details**. Some hosting providers place a restriction on the maximum number of recipients in any one email message.  Some hosts simply block all emails on certain low-cost hosting plans.

Subscribe2 provides a facility to work around a restriction of the maximum number of recipients per email by sending batches of emails.  To enable this feature, go to Settings->Subscribe2 and located the setting to restrict the number of recipients per email. If this is set to 30 then each outgoing email notification will only contain addresses for 30 recipients.

Reminder: because subscribe2 places all recipients in BCC fields, and places the blog admin in the TO field, the blog admin will receive one email per batched delivery.  So if you have 90 subscribers, the blog admin should receive three post notification emails, one for each set of 30 BCC recipients.

Batches will occur for each group of message as described above.  A site like this with many public and registered subscribers could conceivably generate a lot of email for your own inbox.

**Restrictions on the number of messages sent**. In order to combat spam many hosts are now implementing time based limitations. This means you are only allowed to send a certain number of messages per unit time, 500 per hour for example. Subscribe2 does not have a work around for this inbuilt but see the next question.

= My host has a limit of X emails per hour / day, can I limit the way Subscribe2 sends emails? =

This is the second most common question I get asked (the first being about emails not being sent which quote often ends up here anyway!). This is more commonly called 'throttling' or 'choking'. PHP is a scripting language and while it is technically possible to throttle emails using script it is not very efficient. It is much better in terms of speed and server overhead (CPU cycles and RAM) to throttle using a server side application.

In the first instance you should try to solve the problem by speaking to your hosting provider about changing the restrictions, move to a less restricting hosting package or change hosting providers.

If the above has not put you off then I spent some time writing a Mail Queue script for Subscribe2 that adds the mails to a database table and sends then in periodic batches. It is available, at a price, [here](http://wpplugins.com/plugin/76/wordpress-mail-queue-wpmq).

= My Digest emails fail to send, why? =

If you have already worked through all of the above email trouble shooting tips, and you are still not seeing your periodic digest emails send there may be an issue with the WordPress pseudo-cron functions on your server.

The pseudo-cron is WordPress is named after the cron jobs on servers. These are tasks that are run periodically to automate certain functions. In WordPress these tasks include checking for core and plugin updates, publishing scheduled posts and in the case of Subscribe2 sending the digest email. so, if the psuedo-cron is not working the email won't send.

some reasons why your pseudo-cron may not be working are explained [here](http://wordpress.org/support/topic/296236#post-1175405). You can also try overcoming these by calling the wp-cron.php file directly and there are even [instructions](http://www.satollo.net/how-to-make-the-wordpress-cron-work) about how to set up a server cron job to do this periodically to restore WordPress pseudo-cron to a working state.

= When I click on Send Preview in Settings->Susbcribe2 I get 4 emails, why =

Subscribe2 supports 4 potential email formats for Susbcribers so you will get a preview for each of the different possibilities.

= Why do I need to create a WordPress Page =

Subscribe2 uses a filter system to display dynamic output to your readers. The token may result in the display of the subscription form, a subscription message, confirmation that an email has been sent, a prompt to log in. This information needs a static location for the output of the filter and a WordPress page is the ideal place for this to happen.

If you decide to use Subscribe2 only using the widget you must still have at least one WordPress page on your site for Subscribe2 to work correctly.

= Why is my admin address getting emails from Subscribe2? =

This plugin sends emails to your subscribers using the BCC (Blind Carbon Copy) header in email messages. Each email is sent TO: the admin address. There may be emails for a plain text excerpt notification, plain text full text and HTML format emails and additionally if the number of recipients per email has been set due to hosting restrictions duplicate copies of these emails will be sent to the admin address.

= I can't find my subscribers / the options / something else =

Subscribe2 creates four (4) new admin menus in the back end of WordPress. These are all under the top level menu header **Subscribe2**.

* Your Subscriptions : Allows the currently logged in user to manage their own subscriptions
* Subscribers : Allows you to manually (un)subscribe users by email address, displays lists of currently subscribed users and allows you to bulk subscribe Registered Users
* Settings : Allows administrator level users to control many aspects of the plugins operation. It should be pretty self explanatory from the notes on the screen
* Send Mail : Allows users with Publish capabilities to send emails to your current subscribers

**Note:** In versions of the plugin prior to version 7.0 the menus are under the WordPress system at Posts -> Mail Subscribers, Tools -> Subscribers, Users -> Subscriptions and Settings -> Subscribe2.

= I'm confused, what are all the different types of subscriber? =

There are basically only 2 types of subscriber. Public Subscribers and Registered Subscribers.

Public subscribers have provided their email address for email notification of your new posts. When they enter there address on your site they are sent an email asking them to confirm their request and added to a list of Unconfirmed Subscribers. Once they complete their request by clicking on the link in their email they will become Confirmed Subscribers. They will receive a limited email notification when new post is made or periodically (unless that post is assigned to one of the excluded categories you defined).  The general public will receive a plaintext email with an excerpt of the post: either the excerpt you created when making the post, the portion of text before a <!--more--> tag (if present), or the first 50 words or so of the post.

Registered Users have registered with your WordPress blog (provided you have enabled this in the core WordPress settings). Registered users of the blog can elect to receive email notifications for specific categories (unless Digest email are select, then it is an opt in or out decision).  The Users->Subscription menu item will also allow them greater control to select the delivery format (plaintext or HTML), amount of message (excerpt or full post), and the categories to which they want to subscribe.  You, the blog owner, have the option (Options->Subscribe2) to allow registered users to subscribe to your excluded categories or not.

**Note** You can send HTML emails to Public Subscribers with the paid [Subscribe2 HTML version](http://wpplugins.com/plugin/46/subscribe2-html) of the plugin.

= Can I put the form elsewhere? (header, footer, sidebar without the widget) =

The simple answer is yes you can but this is not supported so you need to figure out any problems that are caused by doing this on your own. Read <a href="http://subscribe2.wordpress.com/2006/09/19/sidebar-without-a-widget/">here</a> for the basic approach.

= I'd like to be able to collect more information from users when they subscribe, can I? =

Get them to register with your blog rather than using the Subscribe2 form. Additional fields would require much more intensive form processing, checking and entry into the database and since you won't then be able to easily use this information to personalise emails there really isn't any point in collecting this data.

= How do I use the Subscribe2 shortcode? =

In version 6.1 of Subscribe2 the new standard WordPress shortcode [subscribe2] was introduced. By default, it behaves same as old Subscribe2 token, `<--subscribe2-->`, which means that it will show the same Subscribe2 output in your chosen page in WordPress or in the Widget.

But it also has advanced options, which are related to form. The default form contains two buttons for subscribing and unsubscribing. You may, for example, only want form that handles unsubscribing, so the shortcode accepts a **hide** parameter to hide one of the buttons.

If you use the shortcode [subscribe2 hide="subscribe"] then the button for subscribing will be hidden and similarly if you use [subscribe2 hide="unsubscribe"], only button for subscribing will be shown.

The new shortcode also accepts two further attributes, these are **id** and **url**. To understand these parameters you need to understand that Subscribe2 returns a user to the default WordPress Page on your site where you use the shortcode or token however in some circumstances you may ant to override this behaviour. If you specify a WordPress page id using the id parameter or a full URL using the url parameter then the user would be returned to the alternative page.

There are many scenarios in which to use new options, but here is an example:

* Two separate WordPress pages, "Subscribe" that shows only Subscribe button, and "Unsubscribe", that shows only Unsubscribe button. Both pages also have text that should help users in use of form.
* In the widget, show only Subscribe button and post form content to page "Subscribe"
* In the Subscribe2 email template for new post, add text "You can unsubscribe on a following page:" which is followed with link to "Unsubscribe" page

= I can't find or insert the Subscribe2 token or shortcode, help! =

If, for some reason the Subscribe2 button does not appear in your browser window try refreshing your browser and cache (Shift and Reload in Firefox). If this still fails then insert the token manually. In the Rich Text Editor (TinyMCE) make sure you switch to the "code" view and type in [subscribe2] or <!--subscribe2-->.

= My digest email didn't send, how can I resend it? =

In order to force sending you'd need to change the date of publication on the posts from last week or amend the date stamp in the database regarding when the posts should be included from.

If you opt for the latter way look in the options table for the subscribe2_options settings (it's an array) and you'll need to change the 'last_s2cron' value to a timestamp for last week. Then force the cron event to run again with [WP-Crontrol](http://wordpress.org/extend/plugins/wp-crontrol/).

= I would really like Registered users to have the Subscription page themed like my site, is this possible? =

Yes, it is. There is a small extension to Subscribe2 that delivers exactly this functionality. It is available from [Theme Tailors(http://stiofan.themetailors.com/store/products/tt-subscribe2-front-end-plugin/) for just $2.

= How do I make use of the support for Custom Post Types =

In a plugin file for your site or perhaps functions.php in your theme add the following code where 'my_post_type' is change to the name of your custom post type.

`function my_post_types($types) {
	$types[] = 'my_post_type';
	return $types;
}
add_filter('s2_post_types', 'my_post_types');`

= How can I make use of the support for Custom Taxonomies =

In a plugin file for your site or perhaps functions.php in your theme add the following code where 'my_
taxonomy_type' is change to the name of your custom taxonomy type.

`function my_taxonomy_types($taxonomies) {
	$taxonomies[] = 'my_taxonomy_type';
	return $taxonomies;
}
add_filter('s2_taxonomies', 'my_taxonomy_types');`

= How do I make use of the new option to AJAXify the form? =

The first thing you will need to do is visit the options page and enable the AJAX setting where it says "Enable AJAX style subscription form?", this will load the necessary javascript onto your WordPress site.

Next you need to decide if you want the link to be on a WordPress page or in your Sidebar with the Widget.

For a WordPress page you use the normal Subscribe2 token but add a 'link' parameter with the text you'd like your users to click, so something like:

`[subscribe2 link="Click Here to Subscribe"]`

For Sidebar users, visit the Widgets page and look in the Subscribe2 Widget, there is a new option at the bottom called "Show as link". If you choose this a link will be placed in your sidebar that displays the form when clicked.

In either case, if your end users have javascript disabled in their browser the link will sinply take them through to the subscription page you are recommended to create at step 7 of the install instructions.

The final thing to mention is the styling of the form. The CSS taken from the jQuery-UI libraries and there are several to choose from. I quite link darkness-ui and that is the styling used by default. But what if you want to change this?

Well, you need to write a little code and provide a link to the Google API or Microsoft CDN hosted CSS theme you prefer. The example below changes the theme from ui-darkness to ui-lightness. More choice are detailed on the [jQuery release blog](http://blog.jqueryui.com/2011/08/jquery-ui-1-8-16/) where the them names are listed and linked to the address you'll need.

`function custom_ajax_css() {
	return "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/ui-lightness/jquery-ui.css";
}
add_filter('s2_jqueryui_css', 'custom_ajax_css');`

= I want to change the kinds of users who can access the Subscribe2 menus. Is that possible? =

Yes, it is possible with a little bit for code either in a custom plugin or your functions.php file in your theme. You use the add_filter() command that is part of WordPress to change the [capability](http://codex.wordpress.org/Roles_and_Capabilities#Capabilities) that allows access to each of the Subscribe2 menus.

`function s2_admin_changes( $capability, $menu ) {
	// $capability is the core WordPress capability to allow admin page access
	// $menu is the title of the page:
	//	'user' for access to personal subscription settings
	//	'manage' to allow access to the user management screen
	//	'settings' to allow access to the plugin settings
	//	'send' for access to the Send Email page

	// identify the menu you are changing capability for first
	// then return a new capability
	if ( $menu == 'send' ) {
		return 'read';
	}

	return $capability;
}

add_filter('s2_capability', 's2_admin_changes', 10, 2);`

= I want to change the email subject, how do I do that? =

You can change the email subject with the 's2_email_subject' filter. Something like this:

`function s2_subject_changes($subject) {
	return "This is my preferred email subject";
}

add_filter('s2_email_subject', 's2_subject_changes');`

= Can I suggest you add X as a feature =

I'm open to suggestions but since the software is written by me for use on my site and then shared for free because others may find it useful as it comes don't expect your suggestion to be implemented unless I'll find it useful.

= I'd like to be able to send my subscribers notifications in HTML =

By default Public Subscribers get plain text emails and only Registered Subscribers can opt to receive email in HTML format. If you really want HTML for all you need to pay for the [Subscribe2 HTML version](http://wpplugins.com/plugin/46/subscribe2-html).

= Which version should I be using, I'm on WordPress x.x.x? =

WordPress 3.1 and up requires Subscribe2 from the 7.x stable branch. The most recent version is hosted via [Wordpress.org](http://wordpress.org/extend/plugins/subscribe2/).

WordPress 2.8 and up requires Subscribe2 from the 6.x stable branch. The most q version is [6.5](http://downloads.wordpress.org/plugin/subscribe2.6.5.zip).

WordPress 2.3.x through to 2.7.x require Subscribe2 from the 4.x or 5.x stable branch. The most recent version is [5.9](http://downloads.wordpress.org/plugin/subscribe2.5.9.zip).

WordPress 2.1.x and 2.2.x require Subscribe2 from the 3.x stable branch. The most recent version is [3.8](http://downloads.wordpress.org/plugin/subscribe2.3.8.zip).

WordPress 2.0.x requires Subscribe2 from the 2.x stable branch. The most recent version is [2.22](http://downloads.wordpress.org/plugin/subscribe2.2.22.zip).

= Why doesn't the form appear in my WordPress page? =
This is usually caused by one of two things. Firstly, it is possible that the form is there but because you haven't logged out of WordPress yourself you are seeing a message about managing your profile instead. Log out of WordPress and it will appear as the subscription form you are probably expecting.

Secondly, make sure that the token ([subscribe2] or <!--subscribe2-->) is correctly entered in your page with a blank line above and below. The easiest way to do this is to deactivate the plugin, visit your WordPress page and view the source. The token should be contained in the source code of the page. If it is not there you either have not correctly entered the token or you have another plugin that is stripping the token from the page code.

== Screenshots ==

1. The Posts->Mail Subscribers admin page generated by the plugin.
2. The Tools->Subscribers admin page generated by the plugin.
3. The Users->Subscriptions admin page generated by the plugin.
4. The Options->Subscribe2 admin page generated by the plugin.

== Changelog ==

= Version 8.4 by Matthew Robinson =

* Fixed an error in the 'Send Mail' page that blocked emails from being sent
* Added tracking features to Digest title links in HTML version - thanks to Jeremy Schultz
* Provide clearer user feedback for Send and Preview buttons in "Send Email" window
* Add new option to the Widget to return users to the home page on submission
* Added warning to Settings page if selected sender email address is "off-domain"
* Fixed numerous minor SQL injections vectors - thanks to Tom Adams (holizz)
* Removed auto-embed iframes from HTML emails - thanks to Doug Lerner
* Add more intuitive user messages for Public Subscribers when Unsubscribing - thanks to Doug Lerner

= Version 8.3 by Matthew Robinson =

* Fixed a bunch of PHP messages - should not show anyway unless WP_DEBUG is true or PHP error reporting is on a high setting
* Fixed bulk category management so it applies to all users and not just the first record
* Update 'autosub' to no on one-click unsubscribe instead of erasing the value
* Return empty string is get_tracking_link() is passed and empty variable
* Fixed some typographical errors
* Fixed profile page to obey 'one click' display setting
* Fixed implode error seen when sending preview emails on some configurations
* Fixed a error in the admin user dropdown on installs (particularly Multisite) where there may be no administrator level users
* Added a button text filter for the Subscribe and Unsubscribe buttons - proposed by casben79
* Use wp_hash in place of MD5 to further obfuscate confirmation links - thanks to Charlie Eriksen, Otto and Ipstenu

= Version 8.2 by Matthew Robinson =

* Implemented use of Farbtastic as colour chooser in the Counter Widget because ColorPicker has been deprecated in WordPress
* Fixed one hook call in WordPress to pass $this variable by reference to save a little more RAM
* Fixed Subscribe2 implementation of custom taxonomies
* Fixed Bulk Management Format Change code to apply for all users
* Fix for low impact security vulnerability

= Version 8.1 by Matthew Robinson =

* Fixed redirect errors and crash affecting multisite installs on upgrade to 8.0 - thanks in particular to Ed Cooper
* Fixed several other multisite bugs affecting links and styling - thanks to Mark Olbert
* Fixed empty CSV exports - thanks to Gil Namur

= Version 8.0 by Matthew Robinson =

* Split the main plugin file into separate classes for more efficient (60% less RAM) server resource usage - huge thanks to Milan Petrovic
* Ensure notifications for posts are sent to all active registered users
* Fixed bug in Bulk Management code to include all filtered Registered Subscribers, not just those on current screen - thanks to samandiriel
* Fixed some PHP messages regarding use of deprecated clean_url() function and WordPress version checking - thanks to anmari
* Improved white space trimming in the code so it respects tabs and line breaks - thanks to belg4mit
* Updated screenshots to Subscribe2 8.0 and WordPress 3.3.1 screen layouts
* Implemented one-click buttons to subscribe and unsubscribe - thanks to dimadin for code patch
* Changed some default settings, sender to BLOGNAME and number of recipients per email to 1
* Implemented a change so that categories are not displayed in the filter dropdowns when using Subscribe2 in digest mode
* Ensure an action is specified for HTML5 validity - thanks to Franco Carinato
* Introduced 4 new hooks to filter per-post subscribers for each of the 4 email types - thanks to Nicolas Noé

= Version 7.2 by Matthew Robinson =

* Fix for non-sending Preview emails when sender details match recipient details exactly
* Remove some HTML tags (DEL, S and STRIKE) from plain text content to avoid confusing reading - props cpo
* Improved removal of excess white space within the content of the plain text emails
* Introduced 'size' parameter to the shortcode to allow sizing of the email text input box
* Fix for non-sending emails to newly Registered Subscribers - thanks to Gengar003 and WebEndev
* Fix for commenter subscriptions on systems without moderation in place - thanks to elarson
* Improved the TinyMCE plugin code that handles placement of the Subscribe2 shortcode in the rich text editor
* Tidied up some public subscribe functions removing unnecessary code

= Version 7.1 by Matthew Robinson =

* Fix for Opt-out by Author for Registered Users that resulted in posts being sent even though a user had opted out
* Workaround implemented for core WordPress glitch that reults in blank sender details in digest emails when using "Post Author" as sender
* Introduced DIV HTML tags to the administrion menu screens to allow jQuery custom hiding by plugins - proposed by madtownlems
* Reduced code overhead when collecting admin level users
* Fix for non-sending emails when published via XML RPC interface (like the iOS app and Windows Live Writer) - thanks to bellarush & Marc Williams
* Removed the X-Mailer header ready for core PHPMailer update
* Removed single use of split() function which is deprecated in PHP 5.3.0
* Added additional parameters to the 's2_html_email' hook - props Milan Dinić
* Added a check on wp-cron.php to warn digest email users - props Dave Bergschneider

= Version 7.0.1 by Matthew Robinson =

* Fixed a typo in the include/options file that caused failed installs and upgrades
* Fix for get_registered() function for fringe situations

= Version 7.0 by Matthew Robinson =

* NOTE Subscribe2 now requires WordPress 3.1 or higher
* Implemented top level menu to make Subscribe2 menu access easier
* Introduced the ability to add UTM Tracking parameters to email links - Thanks to Sander de Boer
* Introduced Opt-out by Author for Registered Users (on sites where there is more than one author)
* Improved table layout code on user management page
* Make use of core checked() function to save code space
* Show user name for Registered Users when mouse is hovered over email address in Subscribe2->Subscribers
* Move IP address display on hover from date of signup to email address for Public Subscribers so consistent with above change
* Introduced the 's2_capability' filter to allow API amendments to page access for different user capabilities
* Added support to exclude 'post formats' from generating emails provided 'post formats' are supported by the current active theme
* Implemented use of core WordPress functions to place the authoring button shortcuts, removed reliance on buttonsnap.php so this library is now dropped
* Implement use of wp_register_scripts() function
* Display custom post types added using the API in the Settings page as confirmation things are working
* Added two missed strings to the translation domain and corrected a typo - thanks to Stig Ulfsby
* Added a filter for the outgoing email subject line
* Added an option to AJAXify the form, read the FAQs for how to use this new feature

= Version 6.5 by Matthew Robinson =

* Fix for Multisite / MU detection for WordPress versions prior to 3.0
* Fix in the upgrade() function to insert database entires for Registered Users on first time install
* Fix for the uninstaller script to allow use on regular and Multisite WordPress
* Updated uninstaller to remove any postmeta entries on uninstall
* Fix for blank post detail on HTML Excerpt notifications when the post content is brief
* Fix for some PHP notices
* Fix to remove all cron settings when options are reverted to per-post
* Minor code layout changes and comment updates
* Dropped use of WP_CONTENT_DIR to fix issues on site where wp-content/ folder has been moved

= Version 6.4 by Matthew Robinson =

* Wrapped all KEYWORDS in curly brackets {} so capitalised keywords can be used in content without being replaced
* Added support for Custom Taxonomies - thanks to Ian Dunn
* Added feature to allow commenters on your blog to subscribe when commenting (requires WordPress 2.9+)
* Improved and updated some of the jQuery ready for WordPress 3.2
* Introduced 's2_registered_subscribers' filter to allow other plugins to dynamically add or remove email addresses to the 'registered' array - thanks to Allan Tan
* Improved handling of [gallery] shortcode where no post id is defined for HTML emails - thanks to Chris Grady
* Removed trailing semi colons from the maybe_add_column function calls
* Fixed a type on the Settings page - thanks to Deborah Hanchey
* Updated editor buttons to insert shortcode instead of token
* Fixed some PHP notices about undeclared variables
* Compressed some of the javascript code includes to reduce download time
* Fixes to the Counter Widget ColorPicker jQuery code
* Fixes to the Counter Widget label tags
* Fixed a bug in the new_category function that was introduced in version 6.3 - thanks to crashtest
* Improved the user experience when clicking Subscribe and Unsubscribe links in the WordPress MultiSite interface - thanks to Huyz
* Improved the Bulk Manage section to take into account the digest notification setting
* Updated the uninstall script
* Updated some of the code comments to aid reviewing
* Other minor improvements and fixes

= Version 6.3 by Matthew Robinson =

* Stopped using deprecated get_usermeta(), update_usermeta() and delete_usermeta() functions in WordPress 3.1
* Use WP_User_Query class and functions in WordPress 3.1
* Stop using buggy is_blog_user() multisite function
* Fixed a bug where the creating of a new category would re-subscribed digest email users incorrectly

= Version 6.2 by Matthew Robinson =

* Pass email address to add() function explicitly
* Fix typos in the ReadMe
* Added warning on the Options page to avoid using the Subscribe2 KEYWORDS in posts as they will get substituted
* Added HTML LABEL tags to the Subscribe2 form to support WCAG
* SSL friendly with WordPress admin areas
* Added filter to allow on-the-fly manipulation of the digest email
* Added option choice to have emails send from the global admin email and addressed from the name of the blog
* Change sending details for Preview emails so they can be more easily identified by type
* Fixed a typo in a screen message to WordPressMU users

= Version 6.1 by Matthew Robinson =

* Fixed a glitch in the HTML tags in the Subscribe2 Widget that affected drag and drop functions in WordPress - thanks to Marty McOmber
* Improved detection of Multisite installs - thanks to Nada Oneal
* Fixed precontent and postcontent in the Widget to retain entered HTML tags - reported by Rob Saxe
* Fixed a few small typos in the inline code comments and email subjects
* Fixed a bug where Bulk Management changes to move all users to Plain Text Full content would result in blank settings - reported by Sean @ GetSatisfaction
* Fixed issued with TIME and AUTHORNAME keywords in digest emails - thanks to Robert @ GetSatisfaction
* Introduced a more flexible Subscribe2 shortcode - thanks to Milan for the patch code

= Version 6.0 by Matthew Robinson =

* Improved case sensitive SQL queries to avoid issues on some server configurations
* Introduced option to check the notification override button by default at Settings->Subscribe2 - Appearance
* Extend support for for multiuser sites to include WordPress 3.0
* Converted both Widgets to the WordPress 2.8 API - check your widgets are installation
* Improved the Colour Picker in the Counter Widget
* Integrated the Counter Widget into the Settings page
* Removed several functions from the code for WordPress versions prior to 2.8 (2.8 is now a minimum requirement for Subscribe2)
* Removed legacy tinymce files that are no longer required
* Added code to prevent duplicate Public Subscriber entries
* Introduced TABLELINKS keyword for digest type notifications

= Version 5.9 by Matthew Robinson =

* Added support for WordPress 3.0 Custom Post Types
* Fix for failed save of "entries per page" setting
* Correct Digest display issue associated with use of AUTHORNAME keyword
* Fixed issue where posts were not included in Digest notifications despite settings
* Fixed possible issue where a page is not included in Digest notifications if a certain category is excluded
* Fixed issue where successful emailing from Post->Mail Subscribers would report as failed if Subscribe2 is set to email one user per email - Thanks to Meini from Utech Computer Solutions (www.utechworld.com)
* Added a preview button in the Email Subscribers screen that will send current content of the window to the logged in user
* Avoid duplicating the MIME-Version header in the emails
* Removed direct links to Support forum
* Ensure that Subscribe2 sanitises email addresses to RFC5322 and RFC5321 - Thanks to Vital
* Improved the 'Save Emails to CSV' function to include additional information - Thanks to John Griffiths (www.luadesign.co.uk)
* Report that there is no digest notification to resend rather than success if there are no posts in the email

= Version 5.8 by Matthew Robinson =

* Reverted erroneous use of a WordPress 3.0 function

= Version 5.7 by Matthew Robinson =

* Corrected some missed strings to allow i18n translation
* Added AUTHORNAME to digest notification in the same was a TAGS and CATS (i.e. a fixed location in email independent of location in template)
* Ensure digest email sending times are not updated when resend is invoked
* Error check options for number of users displayed per page and if not an integer use default setting of 25
* Better name for cron 'define' and improved 'define' and 'defined' statements
* Revert to using WordPress capability when adding menus rather than user_level, latter is deprecated
* Added DATE and TIME keywords for per-post notifications

= Version 5.6 by Matthew Robinson =

* Fixed a critical bug in the digest function - a typo resulting failed notifications
* Added ability to resend the last digest email again
* Improved Bulk Management to apply to filtered users only (provided they are still Registered and not Public Subscribers)
* Fix for HTML Excerpt mails not using email template

= Version 5.5 by Matthew Robinson =

* Made the email header function pluggable to allow custom changes that persist through versions
* Fix for failed upgrades
* Ensure HTML entities in blogname are decoded for emails
* Add safety checking for options at install to protect against randomly resetting options

= Version 5.4 by Matthew Robinson =

* Fixed a bug introduced in 5.3 that produced in malformed email headers resulting in failed emails
* Fixed an upgrade error that could result in an incomplete table update which blocked new subscriptions

= Version 5.3 by Matthew Robinson =

* Added an HTML Excerpt notification type which sends an HTML formatted excerpt of the post
* Issue an error message in Settings->Subscribe2 if there is no WordPress page published on the blog site
* Improved usage of time dropdown in Settings->Subscribe2 so it works for periodic emails that are on a less than daily frequency
* Sorted categories by slugname for better presentation in the category selection area
* Make sure Preview emails skip the mail queue if WPMQ is used
* Fixed issue where blog posts made by email generated duplicate notifications
* Added fixes with the hope of stopping the random settings reset glitch many thanks to Barbara Wiebel
* Fixed AJAX bugs caused by deprecated jQuery function
* Fixed an issue where Registered Users who have requests a password reset disappear from Subscribe2
* Amended code for IP address collection to work around IIS servers
* Added COUNT keyword
* Ensure that BLOGNAME is not used in digest emails if it is empty

= Version 5.2 by Matthew Robinson =

* Added screen_icon() to each Subscribe2 admin page
* Improved addition of links to the Plugins admin page
* Improved XHTML validity of admin pages
* Improved display of category hierarchy display in the category form
* Added ability to use TAGS & CATS keywords in digest mails (position is static irrespective of keyword location)
* Use PHP variable for Subscribe2 folder to allow for easier renaming (if needed)
* Fixed a bug in TinyURL encoding introduced when links were click enabled
* Removed BurnURL from the plugin as it appears to be no longer operational
* Added urlencode to email addresses in Tools->Subscribers when editing other user preferences
* Restored several FAQs to the ReadMe file and the [WordPress.org FAQ section](http://wordpress.org/extend/plugins/subscribe2/faq/)

= Version 5.1 by Matthew Robinson =

* Add widget options to add custom text before and a after the dynamic Subscribe2 output - thank to Lee Willis
* Add protection against SQL injection attacks to the data entered into the Subscribe2 table
* Applied a fix for WP_User_Search on PHP4 installations
* Collect IP address of subscribers either at initial submission or at confirmation as required by some hosts to allow relaxation of email restrictions. IP details are in the database or available when the mouse pointer is held over the sign up date in Tools->Subscribers
* Fix for script execution time limit code for sites that have safe mode on or that have disable ini_set()
* Display category slugs when mouse pointer is held over the name in the category form area
* Fixed display of HTML entities in the subject of emails by using html_entity_decode()
* Fixed substitution of the MYNAME keyword in notification emails
* Added option to use BurnURL as an alternative to TinyURL to create shorter link URLs

= Version 5.0 by Matthew Robinson =

* Change version number to reflect change in the on going support of the plugin which is now a searchable forum or a paid service
* Added links to online Subscribe2 resources into the Options->Subsribe2 page
* Fixed Digest Time Dropdown to recall Cron Task scheduled time
* Fixed code using updated [Admin Menu Plugin](http://wordpress.org/extend/plugins/ozh-admin-drop-down-menu/) API
* Fixed foreach() error in widget rename function
* Improved layout of widget control boxes
* Improved identification of Administrator level users on blogs where usermeta table entries for user_level are low or missing
* Removed avatar plugin support on WPMU due to processing overhead
* Improved the layout of the digest email with respect to inclusion of unnecessary white space
* Extended maximum script runtime for servers not using PHP in safe mode

= Version 4.18 by Matthew Robinson =

* Option to sort digest posts in ascending or descending order
* Check that plugin options array exists before calling upgrade functions
* Improved reliability of the Preview function
* Extended Preview function to digest emails
* Fixed a code glitch that stopped CATS and TAGS from working
* Fixed incorrect sender information is emails are set to come from Post Author
* Simplified email notification format options in Users->Subscriptions for per-post notifications
* Added Bulk Manage option to update email notification format
* Simplified the usermeta database entries from two format variables down to one
* Removed trailing spaces from some strings for improved i18n support
* Improved Bulk Subscribe and Unsubscribe routines to avoid database artefacts
* Moved Select/Deselect All check box to the top of the category list in admin pages
* Fixed small layout glitch in Manage->Subscribers screen
* Added ChangeLog section to ReadMe to support WordPress.org/extend development

= Version 4.17 by Matthew Robinson =

* Tested for compatibility with WordPress 2.8.x
* Added TAGS and CATS keyword for per-post notification templates
* Fixed bug where confirmation emails may have an empty sender field if notifications come from post author
* Fixed a bug in WPMU CSS
* Added option to exclude new categories by default
* Fixed a bug where emails may not be sent to subscribers when a user subscribes or unsubscribes
* Improved accessing of 'Admin' level users on blogs where user_level is set below 10
* Added ability to send email previews to currently logged in user from Settings->Subscribe2
* Styled admin menu form buttons to fit with WordPress theme
* Improved handling of confirmation sending to reduce errors

= Version 4.16 by Matthew Robinson =

* Correct minor layout issue in Settings->Subscribe2
* Allow users to define the div class name for the widget for styling purposes
* Select from a greater number of notification senders via dropdown list in Settings
* Improved efficiency of newly added WordPressMU code
* Added ability to manage across-blog subscriptions when using WordPressMU
* Fixed bug whereby Public Subscribers may not have got notification emails for posts if Private Posts were blocked
* Added ability to define email Subject contents in Settings->Subscribe2
* Sanity checks of email subject and body templates to ensure they are not empty
* Introduced s2_html_email and s2_plain_email filters to allow manipulation of email messages after construction
* Amended handling of database entries to simplify code and database needs
* Improved the layout of the Subscriber drop down menu
* Added bullet points to the TABLE of posts
* Ensure database remains clean when categories are deleted
* Added new option to manage how auto-subscribe handles excluded categories

= Version 4.15 by Matthew Robinson =

* Fixed E_DEPRECATE warning caused by a variable being passed by reference to the ksort() function
* Fixed called to undefined function caused by typo
* Fixed a syntax error in the SQL code constructors affecting some users

= Version 4.14 by Matthew Robinson =

* Reordered some functions to improve grouping
* Stop s2mail custom variable being added if empty
* Localised 'Send Digest Notification at' string
* Add support for template tags in Post->Mail Subscribers emails
* Improve handling of translation files for more recent version of WordPress
* Implemented <label> tags in the admin pages so text descriptors are click enabled
* Improved subscription preferences for WordPress MU (Huge thanks to Benedikt Forchhammer)
* Added TINYLINK tag to allow TinyURL insertion in place of PERMALINK
* Improved layout of Tools->Subscriber page (Thanks to Anne-Marie Redpath)
* Enhancements to Subscription form layout (Thanks to Anne-Marie Redpath and Andy Steinmark)
* Sender details now uses current user information from Write->Mail Subscribers
* Introduced 's2_template_filter' to allow other plugins to amend the email template on-the-fly

= Version 4.13 by Matthew Robinson =

* Update weekly description
* Improve layout in the Subscribe2 form
* Fixed bug where registering users would not be subscribed to categories if using the checkbox on the registration page
* Improved buttonsnap function checking to reduce PHP notices
* Fixed typo when including CSS information in HTML emails
* Fix 'edit' links in the Tools->Subscribers page for blogs where the WordPress files are not in root
* Improved Tools->Subscribers page layout by hiding some buttons when they are not needed
* Fixed glitch in default options settings file
* Added option to include or exclude a link back to the blog theme CSS information within the HTML emails
* Improve per-post exceptions to sending emails by introducing a separate meta-box rather than relying on a custom field
* Fix for Gallery code in emails sending entire media library
* Updated screen shots

= Version 4.12 by Matthew Robinson =

* Added new option to remove Auto Subscribe option from Users->Your Subscriptions
* New POSTTIME token for digest email notifications
* Preserve mail after sending from Write->Mail Subscribers
* Introduced the Subscriber Counter Widget
* Use Rich Text Editor in Write->Mail Subscribers for the Paid HTML version
* Per User management in Admin
* Added support Uninstall API in WordPress 2.7
* Add support for 'Meta Widget' links
* Subscribers are sorted alphabetically before sending notifications
* Added ability to bulk unsubscribe a list of emails pasted into manage window
* Define number of subscribers in Manage window
* Added options for admin emails when public users subscribe or unsubscribe
* Fixed bug that prevented sending of Reminder emails from Manage->Subscribers
* Amended confirmation code so that only one email is sent no matter how many times users click on (un)subscribe links

= Version 4.11 by Matthew Robinson =

* Works in WordPress 2.7-almost-beta!
* Fixed a bug in the mail() function that meant emails were not sent to recipients if the BCCLimit setting was greater than the total number of recipients for that mail type
* Ensured that the array of recipients was cast correctly in the reminder function
* Fixed display of html entities in the reminder emails
* Fixed a bug in the SQL statements for WordPress MU installations
* Corrected a typo in the message displayed on the WordPress registration page if subscriptions are automatic
* Several layout and inline comment changes

= Version 4.10 by Matthew Robinson =

* Fixed Registration form action from WordPress registrations
* Repositioned the button to send reminder emails
* Implemented WP_CONTENT_URL and WP_CONTENT_DIR
* Added filter for <a href="http://planetozh.com/blog/2008/08/admin-drop-down-menu-more-goodness-an-api/">Admin Drop Down Menu</a>
* Improve functioning of Process button in Manage admin pane
* Improved form compliance with XHTML
* Fixed bug with cron time being changed every time options are changed

= Version 4.9 by Matthew Robinson =

* Send email direct to recipient if BCC is set as 1
* Fix issue where WordPress shortcodes were not stripped out of emails
* Amended Manage page to resolve issues with IE and Opera not passing form information correctly
* Amended Manage page to allow for bulk management of public users
* Amended WordPress API usage for translation files to 2.6 compatible syntax
* Allow Editor and Author users to send emails from Write->Mail Subscribers
* Post collection for CRON function is more dynamic
* CRON function sanity to checks for post content before sending a notification
* Fixed get_register() function to allow for user_activation field
* Corrected typos in options.php
* Added a search box to the Manage->Subscribers window
* Strip tags and HTML entities from email subjects
* Improved message feedback in Write->Mail
* Added html_entity_decode to sender name fields
* Change Menu string for User menu to make it clearer whose preferences are being edited

= Version 4.8 by Matthew Robinson =

* Removed unnecessary return statement at end of publish() function
* Ensured posts in digest are listed in date order
* Improved compatibility with other plugins by only inserting JavaScript code into Subscribe2's own admin pages
* Added BCCLIMIT and S2PAGE to options page with AJAX editing
* Improved setting of CRON task base time
* Improved handling of option values in the options form
* Full XHTML compliance on all subscribe2 admin pages
* Decode HTML entity codes in notification email subjects
* Added Subscribe2 support for blogging via email
* Work-around fix implemented for WordPress the_title bug

= Version 4.7 by Matthew Robinson =

* Added admin control over default auto subscribe to new category option
* Improved Cron code to reduce the chance of duplicate emails
* Fixed a string that was missed from the translation files
* Improved time variable handling for cron functions, especially when UTC is different from both server time and blog time
* Completed code changes to allow WPMU compatibility
* Fixed some issues with the email headers now that Subscribe2 is using wp_mail() again

= Version 4.6 by Matthew Robinson =

* Fixed mis-reporting of server error when unsubscribing
* Fixed fatal errors involving buttonsnap library
* Improved database entry management for new subscribers
* Fixed issue where Subscribe2 grabbed the first page from the database even if it wasn't published
* Fixed upgrade reporting for Debug and Uninstaller plugins

= Version 4.5 by Matthew Robinson =

* Added Support for WordPress 2.5!
* Fixed HTML typo in admin submission message
* Fixed time display for cron jobs in Options->Subscribe2 when displayed on blogs using a time offset
* Added Debug plugin to the download package
* Improved descriptions of email template keywords in Options->Subscribe2
* Display subscribers in batches of 50 in Manage->Subscribers
* Fixed some XHTML validation errors
* Improved admin menu layout for compliance with WordPress 2.5
* Reverted to using wp_mail instead of mail to ensure proper header encoding
* Improved mail header formatting - thanks to Chris Carlson
* Add ability to skip email notification using a Custom Field (s2mail set as "no")
* Improved CSV export - thanks to Aaron Axelsen
* Added some compatibility for WPMU - thanks to Aaron Axelsen
* Added some error feedback to blog users if mails fail to send
* Moved Buttonsnap due to far to many fatal error complaints
* Added option to send notifications for Private posts
* Improved handling of notification for Password Protected Posts

= Version 4.4 by Matthew Robinson =

* Fixed non-substitution of TABLE keyword
* Fixed bug in usermeta update calls in unsubscribe_registered_users function
* Fixed bug in array handling in cron function that may have stopped emails sending
* Improved array handling in the Digest function
* Added an Un-installer to completely removed Subscribe2 from your WordPress install

= Version 4.3 by Matthew Robinson =

* Fixed bug where digest emails were sent to unsubscribed users - Thanks to Mr Papa
* Stripped slashes from Subject when sending from Write->Mail Subscribers - Thanks to James
* Ensured all admin pages created by Subscribe2 are valid XHTML 1.0 Transitional code
* Added default mail templates and other missed string values to i18n files to allow easier first time translation - thanks to Kjell
* Added option to set the hour for digest email notifications provided the schedule interval is one day or greater
* Moved option variable declaration to ensure better caching
* Fixed bug where cron tasks were not removed when options were reset
* Fixed email notifications for future dated posts
* Fixed QuickTag Icons and mouse-over floating text

= Version 4.2 by Matthew Robinson =

* Added translation capability to user feedback strings - thanks to Lise
* Corrected some other translation strings
* Fixed bug in notification emails to admins when new users subscribe
* Updated default options code

= Version 4.1 by Matthew Robinson =

* Fixed sending of notifications for Pages
* Fixed password protected post bug for Digest email notifications
* Fixed blank email headers if admin data is not at ID 1

= Version 4.0 by Matthew Robinson =

* Compatible with WordPress 2.3
* Widget Code now integrated into the main plugin and added as an option
* More Options for Email Notifications
* Category Lists fixed for WordPress 2.3 and now show empty categories

= Version 3.8 by Matthew Robinson =

* Fixed User Menu Settings when Digests enabled
* Changed Registered Subscribers to Registered Users in drop down to avoid confusion
* Minor code revisions for admin menu layout

= Version 3.7 by Matthew Robinson =

* Change from deprecated get_settings -> get_option
* Fix for confirmation links not working for custom installs
* Abandoned wp_mail due to core bugs
* Added Digest Table feature (untested)
* Added icons to manage window (Thanks to http://www.famfamfam.com/lab/icons/)
* Fixed Bulk Manage bug when using i18n files
* Fixed bug in cron emails if <!--more--> tag present

= Version 3.6 by Matthew Robinson =

* Fixed a typo in Content-Type mail headers
* Fixed Auto Register functions to obey Excluded Categories
* Added option to check WP-Register checkbox by default

= Version 3.5 by Matthew Robinson =

* Fixed a bug in the upgrade function that was messing up the options settings
* Updated the include.php file to preset recently introduced option settings

= Version 3.4 by Matthew Robinson =

* QuickTag button now displays a Marker! (HUGE thanks to Raven!)
* Fix for excluded categories in User Menu
* BCCLIMIT typo corrected in Mail function
* Call to translation files moved to avoid call to undefined function
* Options added to send mails for pages and password protected posts
* Option added to display subscription checkbox in WordPress Register screen
* Small typo and layout amendments

= Version 3.3 by Matthew Robinson =

* QuickTag button added! Works with Visual and Standard Editor. __Look in Code for token addition if using RTE.__
* Current Server time displayed for Cron tasks
* Fixed bug so Registered users now identified correctly
* Upgrade function called via WordPress hook to prevent calls to undefined functions
* Fixed a bug affecting Registered Users not appearing in the drop down list
* Improved handling of the Subscribe2 option array

= Version 3.2 by Matthew Robinson =

* Fixed a bug affecting Registered Users not appearing in the drop down list
* Improved handling of the Subscribe2 option array

= Version 3.1 by Matthew Robinson =

* Amended code to use core cron functionality for future posts and digest notifications, no longer need WP-Cron
* Improved HTML code generated for admin pages
* Removed sending of emails for WordPress Pages
* Fixed display issues if S2PAGE is not defined

= Version 3.0 by Matthew Robinson =

* Updated for WordPress 2.1 Branch

= Version 2.22 by Matthew Robinson =

* Fixed User Menu Settings when Digests enabled
* Changed Registered Subscribers to Registered Users in drop down to avoid confusion
* Minor code revisions for admin menu layout

= Version 2.21 by Matthew Robinson =

* Change from deprecated get_settings -> get_option
* Fixed bug in cron emails if <!--more--> tag present

= Version 2.20 by Matthew Robinson =

* Fixed a typo in Content-Type mail headers
* Fixed Auto Register functions to obey Excluded Categories

= Version 2.19 by Matthew Robinson =

* Fixed a bug in the upgrade function that was messing up the options settings

= Version 2.18 by Matthew Robinson =

* BCCLIMIT typo corrected in Mail function
* Call to translation files moved to avoid call to undefined function
* Small typo and layout amendments

= Version 2.17 by Matthew Robinson =

* Current Server time displayed for Cron tasks
* Fixed bug so Registered users now identified correctly
* Upgrade function called via WordPress hook to prevent calls to undefined functions

= Version 2.16 by Matthew Robinson =

* Fixed a bug affecting Registered Users not appearing in the drop down list
* Improved handling of the Subscribe2 option array

= Version 2.15 by Matthew Robinson =

* Improved HTML code generated for admin pages
* Fixed display issues if S2PAGE is not defined

= Version 2.14 by Matthew Robinson =

* Amended DREAMHOST setting to BCCLIMIT as more hosts are limiting emails
* Fixed oversight in upgrade() function

= Version 2.13 by Matthew Robinson =

* Added WordPress nonce functionality to improve admin security

= Version 2.12 by Matthew Robinson =

* Fix for missing Quicktags (probably since version 2.2.10)
* Fix for occasional email issue where excerpts are incomplete

= Version 2.11 by Matthew Robinson =

* Fixed bug that would cause all subscribers to get digest emails
* Added Select All check box to category listing

= Version 2.10 by Matthew Robinson =

* Improved sign up process by double checking email address
* Fix for submenu issues encountered in WP 2.0.6

= Version 2.9 by Matthew Robinson =

* Fixed get_userdata call issue
* Added CSV export
* Reworked options storage routines

= Version 2.8 by Matthew Robinson =

* Fixed missing line return in email headers that was causing failed emails
* Added user feedback messages to profile area
* Added 'Authorname' to the list of message substitutions in email messages
* Fixed name and email substitution in Digest Mails
* Fixed stripslashes issue in email subjects
* Added new 'Action' token for confirmation emails

= Version 2.7 by Matthew Robinson =

* Link to post in HTML emails is now functional
* Fixed bug in Bulk Management so it works when first loaded
* Ability to auto subscribe newly registering users
* Added additional email header information

= Version 2.6 by Matthew Robinson =

* Fixed email headers to comply with RFC2822 standard (after breaking them in the first place)
* Impoved XHTML compliance of user feedback messages and subscription form when presented on a blog
* Tidied up presentation of the code a little
* Cached some additional variables

= Version 2.5 by Matthew Robinson =

* Added functionality to Bulk Manage registered users subscriptions

= Version 2.4 by Matthew Robinson =

* Added functionality to block user specified domains from public subscription

= Version 2.3 by Matthew Robinson =

* Added functionality to allow for Subscribe2 Sidebar Widget
* Added functionality to block public email subscriptins from domains defined under Options
* Added functionality to send an email reminder to all unconfirmed public subscriber
* Added removal of html entities (for example &copy;) from plaintext emails
* Replaced spaces with tabs in Plugin format
* Minor changes to admin layout to match WordPress admin function layout

= Version 2.2 =

* By Scott Merrill, see http://www.skippy.net/blog/category/wordpress/plugins/subscribe2/

== Upgrade Notice ==

See Version History