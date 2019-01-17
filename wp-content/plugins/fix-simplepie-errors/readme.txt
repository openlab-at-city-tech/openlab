=== Fix - SimplePie Errors ===
Contributors:       Michael Uno, miunosoft
Donate link:        http://en.michaeluno.jp/donate
Tags:               SimplePie, fix, patch, PHP7
Requires at least:  4.0
Requires PHP:       7.0
Tested up to:       4.9.8
Stable tag:         1.0.0
License:            GPLv2 or later
License URI:        http://www.gnu.org/licenses/gpl-2.0.html

A temporary fix for an incompatibility issue of the built-in library, SimplePie 1.3.1, with PHP 7.1 or above.

== Description ==

= Getting PHP Errors? =

`
PHP Warning:  A non-numeric value encountered in .../wp-includes/SimplePie/Parse/Date.php
`

For details, see [here](https://core.trac.wordpress.org/ticket/42515).

So until the issue is fixed in the core, this plugin can be used to avoid the warnings for the time being.

= Fix =
Just activate the plugin. 

== Installation ==

= Install =
1. Upload **`_fix-simplepie-errors.php`** and other files compressed in the zip folder to the **`/wp-content/plugins/`** directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

= Getting Started = 
1. Just activate the plugin. 

== Frequently asked questions ==

= Is it safe to run this plugin? =

Mostly, yes. What this plugin does is to load the `SimplePie` class with a patch before WordPress loades the buit-in one. WordPress checks whether the class is already loaded and if yes it does not load the class.

In some occasional cases, there might be conflicts with other third-party programs. If that happens, please report in the support forum.


== Other Notes ==

= Patched Code = 
In SimplePie/Parse/Date.php, the line 694,

`
$second = round($match[6] + $match[7] / pow(10, strlen($match[7])));
`

is changed to 

`
$second = round($match[6] + (int) $match[7] / pow(10, strlen($match[7])));
`

== Screenshots ==


== Changelog ==

= 1.0.1 - 11/28/2018 =
- Fixed a month mapping error.

= 1.0.0 - 10/26/2018 =
- Released initially.
