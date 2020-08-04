=== Setmore Plus ===
Contributors: cdillon27
Tags: appointments, booking, calendar, salon, spa, schedule, scheduling, adopt-me
Requires at least: 3.5
Tested up to: 5.0
Stable tag: 3.7.2
Requires PHP: 5.2
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Your customers can make appointments online using the setmore.com service.

== Description ==

> This plugin is up for adoption.

Quickly add a [Setmore Appointments](http://setmore.com) scheduler to your site using this modern, feature-rich plugin.

You can easily set up individual staff booking pages and use multiple languages.

**[Go Demo](https://strongdemos.com/setmore-plus/)**

*This plugin is offered by [Strong Plugins](https://strongplugins.com). We have no affiliation with Setmore Appointments and provide no technical support for their service.*

### About Setmore Appointments

Setmore helps you manage appointments, schedules, and customers, all through an easy-to-use web application. Your customers can book online, and pick their favorite staff, service and time-slot without picking up the phone.

#### Getting started with a free account

Signing up is easy and fast. You don't need a credit card to get started. Start taking appointments right away.

Learn more and get a free Setmore account at [Setmore.com](http://setmore.com).

### Add Setmore to your site

After entering your unique Setmore URL, to display the scheduler on your site:

* use a **widget** to place a "Book Appointments" button in a sidebar area
* use the **shortcode** to embed the Setmore scheduler directly in a page, or add a link or a button that opens the scheduler in a popup window
* create a **menu link** that opens the scheduler in a popup window

The widget and shortcode also work with individual staff booking pages. Full instructions are included.

This plugin can *leave no trace!* If you delete the plugin, all settings will be removed from the database. However, simply deactivating it will leave your settings in place, as expected.

### Privacy and GDPR

This plugin embeds the content of the Setmore online app in an iFrame. It stores the URL for your public setmore.com booking page, the same URL you can enter into any browser to access your Setmore scheduler.

It does not store your setmore.com login credentials, account information or any other private data.

It does not store any visitor data.

By using the embedded Setmore scheduler, you will be agreeing to Setmore's [Terms of Use](https://www.setmore.com/#terms-of-use).

### Try these plugins too

* [Strong Testimonials](https://wordpress.org/plugins/strong-testimonials/) to receive and display testimonials.
* [Simple Custom CSS](https://wordpress.org/plugins/simple-custom-css/) still works great for quick CSS tweaks.
* [Wider Admin Menu](https://wordpress.org/plugins/wider-admin-menu/) lets your admin menu b r e a t h e.

### Translations

* Spanish (es_ES) - Richy Canello

== Installation ==

1. Go to `Plugins > Add New`.
1. Search for "setmore plus".
1. Click "Install Now".

OR

1. Download the zip file.
1. Upload the zip file via `Plugins > Add New > Upload`.

Finally, activate the plugin.

1. Go to `Settings > Setmore Plus`.
1. In another browser tab, sign in to [my.setmore.com](http://my.setmore.com).
1. Copy your Booking Page URL from your "Profile" tab.
1. Paste that URL into the `Setmore Booking URL` field in WordPress.
1. Add the widget to a sidebar, the shortcode to a page, or a custom link to a menu.

== Frequently Asked Questions ==

= How do I get a Setmore account? =

Visit [Setmore.com](http://setmore.com) to get your free account. A [premium plan](http://www.setmore.com/premium) with more features is also available.

= How do I change the "Book Appointment" button? =

In the widget, you can select the default image button, a trendy flat button, or a plain link.

To create a custom button, select the plain link option, then add style rules for `a.setmore` in your theme's stylesheet or custom CSS function, or try [Simple Custom CSS](https://wordpress.org/plugins/simple-custom-css/).

For example, here's a square blue button with white text:

`a.setmore {
	background: #4372AA;
	color: #eee;
	display: inline-block;
	margin: 10px 0;
	padding: 10px 20px;
	text-decoration: none;
	text-shadow: 1px 1px 1px rgba(0,0,0,0.5);
}

a.setmore:hover {
	background: #769CC9;
	color: #fff;
	text-decoration: none;
}`

Need help? Use the [support forum](https://wordpress.org/support/plugin/setmore-plus) or submit a [support ticket](https://support.strongplugins.com/new-ticket).

= Can I use different languages? =

Yes. Setmore.com supports 31 languages. You can select the default language for your site and also select a specific language for each shortcode or widget.

= Leave no trace? What's that about? =

Some plugins and themes don't fully uninstall everything they installed - things like settings, database tables, subdirectories. That bugs me. Sometimes, it bugs your WordPress too.

So this plugin has an option to completely remove itself upon deletion. Simply deactivating the plugin will leave the settings intact.

== Changelog ==

= 3.7.2 =
* Replace use of `extract` in shortcode attribute processing.
* Test in WordPress version 5.

= 3.7.1 =
* Update URLs.

= 3.7 =
* Add a preferred language option.
* Add language option to the widget.
* Add `lang` shortcode attribute.
* Improved performance.

= 3.6.1 =
* Add version number in HTML comment for troubleshooting.

= 3.6 =
* Improve responsiveness so the proper scheduler size is loaded from Setmore.com.
* Separate popup dimensions from embed (iframe) dimensions.
* Add screenshots of Setmore scheduler sizes.

= 3.5 =
* Add `#setmoreplus` option.
* Fix bug on settings page.
* Add Leave No Trace icon.

= 3.4.1 =
* Fix missing button on widget.

= 3.4 =
* Add individual staff booking pages to shortcode and widget.
* Spanish translation.

= 3.3 =
* Add option to load scripts in header instead of footer.

= 3.2 =
* Fix bug when using shortcode and widget on same page.

= 3.1 =
* Fix bug in iframe.

= 3.0 =
* Add option for a menu link to the popup.
* Remove shortcode width & height attributes.
* Improve admin screen.

= 2.3 =
* More shortcode options.

= 2.2.2 =
* Add filter to exempt shortcode from wptexturize in WordPress 4.0.1+.

= 2.2.1 =
* Fix bug in shortcode.

= 2.2 =
* Added "Leave No Trace" feature.
* Added uninstall.php, a best practice.
* Object-oriented refactor.

= 2.1 =
* Improved settings page.

= 2.0 =
* Forked from Setmore Appointments 1.0.
* Updated for WordPress 3.9. New minimum version 3.3.
* Improved widget options.
* New shortcode to add an iframe to a page.
* Using Colorbox for iframe lightbox.
* Ready for internationalization (i18n).

== Upgrade Notice ==

Improved adherence to WordPress coding standards.