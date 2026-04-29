=== EMC - Easily Embed Calendly Scheduling ===
Contributors: turn2honey
Donate link: https://simpma.com/emc/pricing/
Tags: appointment, booking, embed calendar, calendly, scheduling
Requires at least: 4.6
Tested up to: 6.9
Stable tag: 5.5
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Embed Calendly scheduling pages in WordPress and optimize your booking flow with analytics, availability indicator, and conversion tools.

## Description

[EMC Scheduling Manager](https://simpma.com/emc/) makes it easy to **embed Calendly scheduling pages into your WordPress website**.

EMC is designed not only to embed scheduling pages, but also to help businesses **improve booking completion** and understand how visitors interact with their scheduling pages.

Add Calendly booking forms anywhere on your site using a simple shortcode. Display your scheduling page inline, open it in a popup, or trigger it from a button — all without writing code.

Whether you're a consultant, coach, agency, or business owner, this plugin helps you **turn website visitors into scheduled meetings** quickly and easily.

Upgrade to **[EMC Pro](https://simpma.com/emc/pricing/)** to unlock powerful tools for **tracking bookings, improving conversion, upsell rates, and managing scheduling workflows more efficiently.**

## Free Version Features

The **free version** includes everything you need to start embedding Calendly scheduling pages in WordPress:

- **Import Calendly event types** directly into WordPress
- **Embed scheduling pages with a shortcode**
- **Inline embed, popup button, and popup text widgets**
- **Visual widget customizer** to generate embed shortcodes
- **Prefill booking fields for logged-in users**
- **Customizable embed appearance**
- **Quick setup with no coding required**

Perfect for anyone who simply wants to **add a Calendly booking widget to WordPress**.

## Pro Version Features (Advanced Scheduling Tools)

**[EMC Pro](https://simpma.com/emc/pricing/)** expands the plugin from a simple embed solution into a booking optimization toolkit, helping you increase completed bookings and understand what drives scheduling activity.

### Business Integrations & Marketing Tracking

- **WooCommerce integration for booking after purchase**
- **Redirect users after scheduling to custom pages**
- **Track marketing campaigns** Pass common UTM parameters like utm_source, utm_medium, utm_campaign, utm_content, and utm_term directly to your booking pages.
- **CRM-friendly leads** Integrate booking data with your CRM to see exactly which campaigns are converting into booked calls.

### Analytics & Booking Insights

- **Track booking activity and engagement**
- **Know which channels, pages, and campaigns drive appointments and revenue growth.**
- **Export analytics data for reporting**

### Scheduling Workflow Tools

- **View and manage bookings from your WordPress dashboard**
- **Send automated and manual email reminders from WordPress**

### Advanced Embedding Features

- **Dynamic embed options for flexible booking layouts**
- **Enhanced shortcode customization**
- **Elementor integration**

EMC Pro helps turn your booking widget into a **powerful scheduling and conversion tool**.

Upgrade to EMC Pro to unlock powerful features designed to help you **increase bookings and manage scheduling more effectively**.

[Learn More >>](https://simpma.com/emc/pricing/)

## Shortcode

To simply embed a scheduling page, use:

`[calendly url="https://calendly.com/example/call" type="1"]`

Example with customization:

`[calendly url="https://calendly.com/example/call" type="2" text="Book Now" text_color="#ffffff" text_size="14" button_style="1" button_size="1" button_color="#2694ea" branding="false" hide_details="false" style_class="custom_form_style"]`

Use the dynamic embedder when you want to display multiple Calendly event types on a single page, allowing visitors to switch between them without reloading the page.

`[calendly_dynamic_embedder url="https://calendly.com/example/call" form_height="600px"]`

(Pro Feature) Customize your dynamic embedder with these options

- `form_height` - Calendar height
- `style` - 1 for Horizontal tab display, 2 for Vertical tab display
- `tab_color` - Tab color
- `tab_active` - Active tab color
- `text_color` - Text color 
- `text_size` - Text size  
- `show_slots` - 1 Yes, 0 No
- `slots_max` - Maximum slots available
- `slots_text` - Eg "Only %d slots left" where %d is automatically replaced with booking slots left

(Pro Feature) Display your availability slots with

[calendly_slots url="https://calendly.com/example/call"] or [calendly_dynamic_embedder show_slots="1"]

(Pro Feature) Customize how your availability slots is displayed with

- `text` - Eg "Only %d slots left" where %d is automatically replaced with booking slots left
- `text_color` - Text color 
- `text_size` - Text size  
- `max_slots` - Maximum slots available

## Customization

Use the **Widget Customizer** under **Dashboard > EMC > Customizer** or configure the shortcode manually.

### Available Options

- `type`
  - 1 - Inline embed
  - 2 - Popup button
  - 3 - Popup text

- `url` - Calendly scheduling page URL  
- `text` - Button or link text  
- `text_color` - Button or link text color  
- `text_size` - Text size  
- `button_color` - Button background color  
- `button_size`
  - 1 - Small
  - 2 - Medium
  - 3 - Large

- `button_style`
  - 1 - Inline
  - 2 - Floating

- `branding` - Show or hide branding  
- `prefill_fields` - Prefill form fields for logged-in users  
- `hide_cookie_banner` - Hide cookie banner  
- `hide_details` - Hide event details  
- `style_class` - Custom CSS class
- `redirection_url` - Page URL to redirect users to after booking completion (Pro)

## Built for Booking Conversion

EMC Scheduling Manager is designed to do more than embed scheduling pages:

- Encourage faster decisions with limited availability indicators (Pro) 
- Present multiple booking options without overwhelming visitors (Pro) 
- Track how visitors interact with scheduling widgets (Pro)
- Optimize booking flows using real usage data (Pro)

If scheduling is part of your sales or lead generation process, EMC Pro helps you optimize your booking flow for conversion.

## Why Use EMC Scheduling Manager

Designed for consultants, coaches, agencies, and business that rely on scheduled calls to generate leads & sales.

- **Turn website visitors into scheduled meetings** by embedding your scheduling pages directly inside WordPress.
- **Reduce booking friction** with inline widgets, popup buttons, and customizable scheduling layouts.
- **Increase booking completion rates** by displaying limited availability (e.g. “Only 3 slots left”), encouraging faster decisions while remaining transparent.
- **Keep visitors on your website while they book**, improving engagement and reducing drop-offs.
- **Offer multiple meeting options on one page** using dynamic embeds so visitors can quickly choose the right event.
- Upgrade to Pro to unlock **booking insights, and conversion tools** that help you understand and grow your scheduling activity.

Start with the **free version** and upgrade to **EMC Pro** when you're ready to unlock advanced scheduling features.

[Learn More >>](https://simpma.com/emc/pricing/)

## Popular Use Cases

EMC is used by professionals and businesses that rely on scheduled meetings to generate leads, sales, or revenue:

- **Consultants & coaches** offering paid or free strategy calls  
- **Agencies** booking discovery calls and client onboarding sessions  
- **Freelancers** managing availability without email back-and-forth  
- **Online educators** scheduling 1:1 sessions or onboarding calls  
- **WooCommerce store owners** linking products to post-purchase booking flows

Upgrade to Pro to unlock advanced analytics, availability tracking, and booking optimization features.

== Frequently Asked Questions ==

= How do I display scheduling forms on pages? =

Add the shortcode:

`[calendly url="https://calendly.com/example/call" type="1"]`

to any page or post.

= How do I connect to Calendly? =

Enter your Calendly API key in the **API Key tab** on the EMC Scheduling Manager settings page.


= How do I style my embed widget? =

Go to **Dashboard → EMC → Customizer**, select an event type, and adjust the settings to generate a shortcode.


= How do I add a custom CSS class to the embed? =

Use the `style_class` option:

`[calendly url="https://calendly.com/example/call" style_class="custom_form_style"]`


== Disclaimer ==

This plugin is an **unofficial integration for embedding Calendly scheduling pages in WordPress** and is not affiliated with or endorsed by Calendly.

The free version may display optional promotional notices in the WordPress admin dashboard. These notices can be dismissed using the **"Don't show again"** option.

You can also disable them using the following filter:

`

add_filter('emcs_promotions', 'emcs_show_promotions');

function emcs_show_promotions() {
	return false; 
}

`

== Changelog ==

= 5.5 - 29-04-2026 =

- Improved compatibility with older PHP versions

= 5.4 - 06-04-2026 =

- Prevent duplicates on event types sync
- Redirect correcly when sync button is clicked
- Performance improvement

= 5.3 - 17-03-2026 =

- Security improvement

= 5.2 - 14-03-2026 =

- Security improvement
- Fixed dynamic embedder UX
- Correctly hide cookie notice in popup button

= 5.1 - 12-03-2026 =

- Dynamic embedder lite

= 5.0 - 11-03-2026 =

- Added dynamic embedder for adding multiple calendars to a page
- Security and performance improvements

= 4.5 - 21-02-2026 =

- Security fixes

= 4.4 - 05-02-2026 =

- Support for passing UTM and GCLID parameters to Calendly
- Tested & ensured compatibility with the latest WordPress version

= 4.3 - 26-01-2026 =

- Tested & ensured compatibility with the latest WordPress version

= 4.2 - 17-01-2025 =

- Added prefill field option to customizer
- Updated readme
- Adjusted promotion module

= 4.1 - 26-11-2024 =

- Verified compatibility with WordPress updates.
- Updated readme

= 4.0 - 05-11-2024 =

- UI rebranding and redesign
- Added shortcode option for prefilling current logged in user info in booking forms.
- Improved pro version support
- Improved internationalization supportu