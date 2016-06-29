=== WP API ===
Contributors: myflashlab
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=payments%40premierweb%2enet%2eau&lc=US&item_name=wp%2dapi&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest
Tags: mobile, air, app, json, cms, api
Requires at least: 2.8
Tested up to: 3.2.1
Stable tag: 1.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Wordpress api in JSON format

== Description ==

wp-api translates your whole wordpress blog into a JSON formatted api. you can use many different methods to retrieve 
information about posts, pages, authors, tags and categories. You may also use the api methods to submit new comments.

We had to write this plugin from ground up because we were working on an adobe air mobile project that required us to
show posts from a wordpress site! we're still upgrading the app and we need to upgrade this api even more also. so we 
decided to put it up on wordpress directory for everyone to use and have fun :)

to get detailed description on how to use the api, go to wp-api page from the 'settings' menu to see the methods and examples.

== Installation ==

1. Upload the `wp-api` folder to `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to `wp-api` page in the `Settings` menu

== Frequently Asked Questions ==

= Are you going to upgrade this plugin to do more stuff? =

Yes, there are many features that we're hoping to add to the api. it's just a matter of time. please feel free to follow us on
<a href='https://www.facebook.com/myflashlab'>facebook</a> for the latest news and updates to the plugin.

== Screenshots ==

1. Sample JSON output by wp-api

== Changelog ==

= 1.0.3 =

* fixed comment parents. you can chooes which parent to post your comment to.

= 1.0.2 =
* set type argument for get_posts and search method
* create Continue reading for post_excerpt
* change nicename to nickname in all methods

= 1.0.1 =
* removed the jQuery for better performance in the control panel

= 1.0 =
* uploaded to wordpress directory


== Upgrade Notice ==

= 1.0.3 =

* fixed comment parents. you can chooes which parent to post your comment to.

= 1.0.2 =
* set type argument for get_posts and search method
* create Continue reading for post_excerpt
* change nicename to nickname in all methods

= 1.0.1 =
* removed the jQuery for better performance in the control panel

= 1.0 =
Fresh installation