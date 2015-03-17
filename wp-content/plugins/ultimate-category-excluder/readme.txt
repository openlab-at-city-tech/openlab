=== Ultimate Category Excluder ===
Contributors: planetmike
Donate link: http://www.planetmike.com/donations/
Tags: category, categories, exclude, visible, hidden, hide, invisible, remove
Requires at least: 3.1
Tested up to: 3.6
Stable tag: 0.96
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: http://www.planetmike.com/donations/

Ultimate Category Excluder allows you to quickly and easily exclude categories from your front page, archives, feeds, and search results.

== Description ==

Ultimate Category Excluder, abbreviated as UCE, is a WordPress plugin that allows you to quickly and easily exclude categories from your front page, archives, feeds, and searches. Just select which categories you want to be excluded, and UCE does all the work for you!

== Installation ==

1. Download Ultimate Category Excluder.
2. Unzip the ultimate-category-excluder.zip file.
3. Activate the plugin on your plugins page.
4. You can edit the options by going under "Options" and then "Category Exclusion."
5. If you are upgrading from an older version, you need to go into your "Category Exclusion" settings, choose at least one option from the Searches column, save settings, then go back and clear that option and save again.
6. (Optional) I suggest you subscribe to my <a href="http://www.planetmike.com/feed/">RSS feed</a> so you can stay informed about any updates to Ultimate Category Excluder.

== Frequently Asked Questions ==

= When I go to my search results page for any search I do, I get this PHP error: Warning: Invalid argument supplied for foreach() in /wp-content/plugins/ultimate-category-excluder/ultimate-category-excluder.php on line =

Go into your "Category Exclusion" settings, choose at least one option from the Searches column, save settings, then go back and clear that option and save again.

== Changelog ==

= 0.96 =
* September 11, 2013 - Went back to the last known (no complaints at least!) version, 0.84. 

= 0.95 =
* September 10, 2013 - Fixed some more bugs that I introduced while trying to fix other bugs. Cleaned up the code, tried to be more consistent with line spacing and indents so everything is easier to read. Released the Beta version.

= 0.94 =
* September 9, 2013 - It turns out that some themes process their home pages in odd ways. This resulted in UCE no longer correctly filtering out the excluded categories. This was a hard bug to figure out, as I couldn't reproduce it on any of my sites. I think I've got it fixed now, but I do have a report that UCE is now causing a conflict with a separate page on the site. So I am working on that; but since this version should fix most people's home pages, I'm releasing it while continuing to bug hunt. Thank you for your patience. Many thanks to Michael Westergaard, Alyx Hydrick, Alain Saintpo, Seth Vore, and Michael Pollock for their help in figuring out what was happening.

I also fixed several very subtle bugs that were throwing error messages behind the scenes. And I tweaked the system information section at the bottom of the UCE settings page.

Added the Danish tranlation. I don't who to thank for providing this. 
Added Brazilian translation. Thank you to Leandro Callegari Coelho.
Feel free to send me other languages if you like.

= 0.91 =
* September 4, 2013 - You can now exclude categories from appearing in results from the built-in WordPress search engine. Other search engines (e.g. Bing, Google) may still be able to find and index your content. Also fixed a bug that excluded categories from appearing in the Post Editor. And fixed a bug in the system information section.

= 0.84 =
* August 3, 2013 - Fixed empty category bug. Again. 

= 0.83 =
* August 2, 2013 - Added code to give version info for MySQL, PHP, WP and UCE. UCE also works with WP 3.6.

= 0.8 =
* July 7, 2011 - Categories that do not have any posts in them will now appear on the list of categories.

= 0.7 =
* May 6, 2011 - Added internationalization (i18n) based on Patrick Skiebe's suggestion and code. He has provided a German translation. Feel free to send me other languages if you like.

= 0.6 =
* February 24, 2011 - Addressed a bug in UCE that didn’t handle multiple excluded categories correctly.

= 0.5 =
* February 24, 2011 - Addressed a bug in WP 3.1.

= 0.4 =
* October 10, 2009 - A user pointed out a bug when trying to filter down categories in the edit posts admin area. I believe I’ve fixed this, but let me know if you still have trouble.

= 0.3 =
* June 20, 2009 - James Revillini pointed out a few fairly obvious bugs. I’ve incorporated his changes into the software.

= 0.21 Beta =
* January 10, 2008 - Initial release, fixed file name bug, dashes vs. underscores

= 0.2 Beta =
* December 13, 2007 - Initial release, tweaked to refer to PlanetMike.com, no functionality changed

= 0.1 Beta =
* February 14, 2007 - Initial release
