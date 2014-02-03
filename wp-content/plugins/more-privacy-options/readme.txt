=== More Privacy Options ===
Contributors: dsader
Donate link: http://dsader.snowotherway.org
Tags: visibility, privacy, private blog, multisite, members only, network visibility, site visibility
Requires at least: 3.7.1
Tested up to: 3.7.1
Stable tag: Trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add more privacy(visibility) options to a WordPress 3.7.1 Multisite Network. Settings->Reading->Visibility:Network Users, Blog Members, or Admins Only. Network Settings->Network Visibility Selector: All Blogs Visible to Network Users Only or Visibility managed per blog as default.

== Description ==
Adds three more levels of privacy(visibility) to the Settings-->Reading page.

1. Blog visible to any logged in community member - "Network Users Only".

2. Blog visible only to registered users of blog - "Blog Members Only".

3. Blog visible only to administrators - "Admins Only".

Mulitsite Network Admin can set an override on blog privacy at "Network Visibility Selector" on Network Settings page

Multisite Network Admin can set privacy options at Network-Sites-Edit under "Settings Tab" as well.

Network Admin receives an email when blog privacy changes.

RSS feeds require authentication.

robots.txt updates accordingly.

Ping sites filters correctly.

Privacy status reflected in Dashboard "Right Now" box.

Uses WP3+ functions auth_redirect(), network_home_url(), and home_url() for SSL login redirects.

Login message has link to sign-up page of a "Network Users Only" blog or a link the blog admin email if user is logged in but not a member of a "Members Only" blog.

WP svn via Aptana 3

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `ds_wp3_private_blog.php` to the `/wp-content/mu-plugins/` directory
2. Set multisite "Network Visibility" option at Network-Settings page
3. Set individual blog visibility options at Settings-Privacy page, or...
4. Set individual blog visibility options at Network Admin-Sites-Edit page

== Frequently Asked Questions ==

* Will this plugin also protect feeds? Yes.
* Will this plugin protect uploaded files and images? No.

== Screenshots ==

1. Settings Reading: Site Visibility Settings
2. Network Settings: Network Visibility Selector
3. Network Sites Edit: Site Settings(scroll way down to see)

== Changelog ==

= 3.7.1 =
* Tested up to: WP 3.7.1

== Upgrade Notice ==
= 3.7.1 =
* Tested up to: WP 3.7.1
