=== Embed Calendly ===
Contributors: turn2honey
Donate link: https://embedcalendly.com/pricing
Tags: appointment, appointment booking, appointment scheduling, booking calendar, calendly, embed calendly
Requires at least: 4.0
Tested up to: 6.3.1
Stable tag: 3.7
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easy and simple way to embed Calendly scheduling pages on WordPress.

== Description ==

[Embed Calendly](https://embedcalendly.com) helps you add Calendly scheduling pages to your WordPress website in an easy and simple way. 

Allows visitors to easily schedule meetings *anywhere* on your WordPress website, through Calendly.

With an easy to use widget customizer, design your embed widget directly from the WordPress dashboard.

== Free Version Features ==

1. Import Calendly event types to WordPress
2. Customize embed widgets via shortcode.
3. Easy to use widget customizer for customizing and generating embed widget shortcodes.
4. Supports inline, text popup and button popup embed options.

== Pro Version Features ==

1. View booked events from WordPress
2. Analytics dashboard for tracking calendar conversion
3. Import Calendly event types to WordPress
4. Customize embed widgets via shortcode.
5. Easy to use widget customizer for customizing and generating embed widget shortcodes.
6. Supports inline, text popup and button popup embed options.

== Shortcode ==

Embed Calendly scheduling page on WordPress with:

`[calendly url="https://calendly.com/example/call" type="1"]`

Or

`[calendly url="https://calendly.com/example/call" type="2" text="Book Now" text_color="#ffffff" text_size="14" button_style="1" button_size="1" button_color="#2694ea" branding="false" hide_details="false" style_class="custom_form_style"]`

== Customization == 

You can customize the embed widget using the widget customizer at *Dashboard > Embed Calendly > Customizer*, or 
with the following shortcode options:

*   `type` - Embed form type. *1* - inline embed, *2* - popup button embed, *3* - popup text embed

*   `url` - Scheduling page link

*   `text` - Button/Link text

*   `text_color` - Button/Link text color

*   `text_size` - Button/Link text size

*   `button_color` - Button background color. Any hexadecimal color code is supported here

*   `button_size` - Button size. *1* - Samll, *2* - Medium, *3* - Large

*   `button_style` - Button style. *1* - Inline, *2* - Float

*   `branding` - true/false. Show or hide branding

*   `hide_cookie_banner` - 0(false) or 1(true). Hide or show cookie settings/banner
    
*    `hide_details` - 0(false) or 1(true). Hide or show details

*   `style_class` - CSS style name for adding custom css style to embed widget

== Frequently Asked Questions ==

= How do I display scheduling forms on pages? =

Add `[calendly url="https://calendly.com/example/call" type="1"]` shortcode to any page you want to display the form on.

= How do I connect to Calendly? =

Paste your Calendly api key in the API Key tab on Embed Calendly settings page

= How do I style my embed widget on WordPress? =

Go to * Dashboard > Embed Calendly > Customizer * and select an event type from the dropdown. Then adjust the customizer settings to suit you.

= How do I add additional CSS class to embed widgets? =

Use the `style_class` option when adding the shortcode. 
Example: [calendly type="1" url="https://calendly.com/example/call" style_class="custom_form_style"]

== Disclaimer == 

The free version comes with optional promotion notices that can be easily disabled by clicking the "Don't show again" button, near the notice.

These notices are shown in your admin dashboard, and once any of them is disabled, all other promotion notices from Embed Calendly are disabled.

You can upgrade to the [pro version](https://embedcalendly.com/pricing/) to automatically disable all promotion notices.

Optionally, you can disable the promotions by also including the below code snippet in your theme's function.php file:

`

add_filter('emcs_promotions', 'emcs_show_promotions');

function emcs_show_promotions() {
	return false; 
}

`

== Changelog ==

= 3.7 - 12-10-2023 =

- Improved security
- Adjusted promotion module


= 3.6 - 09-07-2023 =

- Fixed customizer bug on Divi theme
- Adjusted promotion module


= 3.5 - 25-06-2023 =

- Published changelog
- Added optional promotion
- Added option to permanently disable all promotion notices
- Updated readme
- Added pro version support
- Updated settings page UI
- Removed donation section from settings page


= 3.4 - 13-05-2023 =

- Removed experimental promotion


= 3.3 - 22-04-2023 =

- Added experimental promotion


= 3.2 - 14-12-2022 =

- Added internationalization support


= 3.1 - 18-10-2022 =

- Enqueue style and script on demand
- Updated widget script
- Fixed popup embed error


= 3.0 - 08-05-2022 =

- Added support for v2 api key
- Fixed inline embed mobile responsiveness issue
- Implemented cookie banner on customizer and other fixes
- Updated settings page
