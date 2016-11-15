=== Enigma ===
Contributors: Shuhai Shen
Donate link: http://mv2.it/donate
Tags: spam, bot, robot, encrypt, anti-spam, enigma
Requires at least: 2.9
Tested up to: 4.5
Stable tag: trunk
License: MIT
License URI: http://www.opensource.org/licenses/mit-license.php

Enigma encrypts text on demand and decrypts it on client to avoid your email
 address and any other sensitive content caught robots.

== Description ==

Enigma encrypts text on demand and decrypts it on client to avoid your email
 address and any other sensitive content caught robots.

Use short code [enigma]...[/enigma] to encrypt any content of your post.

Samples:

1. [enigma]content I don't want search engine to catch.[/enigma]

    The content inside engima tag will be encrypted and search engines
    get nothing.

2. [enigma text="Hello World"]Actual Text[/enigma]

    Search engine will only see "Hello World" but normal user will see
    "Actual Text".

3. [enigma text="Click here" ondemand="y"]<img ...>[/enigma]

    A clickable instruction "Click here" is shown, and after user clicks it,
    the text will be replaced with the <img>.

4. [engima text="Reply and see" ondemand="replied"]Content that is not visiable until reader replies.[/engima]
    The content will be invisible to user, until he/she replies. This feature
    is compatible with Wordpress caching plugins.

== Installation ==

1. Install Engima from Wordpress website.

2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

What browsers does Engima support?
All major browsers, including but not limited to, Chrome, Firefox,
 Internet Explorer, Safari, etc.

== Screenshots ==

Not applicable! There is no GUI in enigma. All the encryption and
decryption is done automatically.

== Changelog ==

= 2.7 =
Fixed usage of shortcode_atts.

= 2.6 =
Allow embeded shortcode.

= 2.5 =
Multiple type of encoding.

= 2.4 =
* Actually integrated with Google Analytics.
* Enigma now fires a 'Enigma:Changed' event on document, after hidden content are displayed.

= 2.3 =
* Integration with Google Analytics. If you have GA enabled at your blog, Engima
  now sends an event when user clicks a Engima clickable link.

= 2.2 =
* Add an experimental feature that content requires user to reply to see.

= 2.1.3 =
* Performance improvement.

= 2.1.1 =
* Slightly improved performance.

= 2.1 =
* Rewrite of core functionalities.

= 2.0 =
* Add new attribute 'ondemond'.

= 1.8 =
* Remove document.write for infinite scroll.

= 1.7 =
* Update to WordPress 3.9.1

= 1.6 =
* Remove client JS.

= 1.4 =
* Bug fixes.

= 1.3 =
* Bug fixes. 

= 1.1.1 =
* Fix small bug on handling ascii code.

= 1.1 =
* Optimize internal logic.