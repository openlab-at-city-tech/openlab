=== Read Meter - Reading Time & Progress Bar ===
Contributors: brainstormforce
Donate link: https://www.paypal.me/BrainstormForce
Tags: readtime, progressbar
Requires at least: 4.2
Requires PHP: 5.2
Tested up to: 6.4
Stable tag: 1.0.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The Read Meter plugin displays the estimated reading time for blog posts along with a progress bar.

== Description ==

People often skip reading posts with the fear of the time they’ll spend reading it. Are you losing readers too?

The Read Meter plugin displays the estimated reading time for blog posts along with a progress bar. It works great to give visitors a quick idea about the time needed to read a post and encourages them to go on till the end.

Usually, visitors try to scan the entire post at first glance and grasp as much as information possible from the post. Reading time specified in minutes motivates users to read the post.

A handy progress bar will show the reader’s position on the post. As the reader scrolls the page, the progress bar indicates the remaining part of the post. This lets readers know how far have they reached and how much more do they have to go on.

[Try it out on a free dummy site](https://bsf.io/read-meter-demo)

The plugin uses an advanced image time calculation technique. In case your post contains images, the plugin calculates the time to view those images as well. It adds 12 seconds for the first image, 11 seconds for the second image and so on till the 10th image. After that, it adds 3 seconds for each further image.

It works great to give visitors a quick idea about the time needed to read a post and encourages them to go on till the end.

Here are some key features of the plugin -

+ A simple shortcode - `[read_meter]`,  gives you the flexibility to add read time anywhere on the site.
+ Even if the post is updated multiple times, the plugin will calculate the read time for the most recent version of the post.
+ You can choose to display the read time and a progress bar on various post types.
+ You can decide whether you would like to include images and comments in the read time and progress bar.

That's not all! Here are some more controls you get over **Reading Time** -

+ Display the read time on a blog/archive page or single post page
+ Set the read time position - i.e. Above/Below title or above content
+ Set a read time Prefix and Postfix
+ Use various read time styling options - Spacing, Background color, Font size, etc.

Specific controls for **Progress Bar** -

+ Set a progress bar position - i.e. Top/ Bottom of the page.
+ Use various progress bar styling options - Gradient Background color, Bar thickness, etc.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Once activated, you’ll find Read Meter settings in the Dashboard Settings.

== Frequently Asked Questions ==

= Where can I find the settings of the Read Meter plugin? =
After installing and activating the plugin, settings can be found under Settings > Read Meter

= How is the read time calculated? =
Let’s assume, you have set Words Per Minute ( Settings > Read Meter > General Settings) to 275 and total words in your blog post are 1100. So the total number of words (1100) divided by Words per minute (275) will give you an estimated time of reading i.e. 4 minutes.

= Can I add this for CPT (Custom Post Types)? =
Yes! The plugin works with all CPTs. You can select Post Types from Settings > Read Meter > General Settings

= Where can I add a shortcode? =
Shortcode -  `[read_meter]` can be added to any page with a shortcode module/block. This will work with the Gutenberg editor or any page builder editor.

= What themes does the Read Meter Plugin work with? =
The Read Meter plugin works with all WordPress themes!

== Screenshots ==
1. General Settings
2. Read Time
3. Progress Bar
4. Getting Started

== Changelog ==
= 1.0.6 =
- Fix: Hardened the security.

= 1.0.5 =
- Fix: Progress Bar displaying on 404 Page.

= 1.0.4 =
- Improvement: Loaded the minified versions of CSS and JS files.
- Fix: Progress Bar Opacity adjustment working on mobile devices.
- Fix: Progress Bar working on iPhone Mobile devices.
- Fix: Set Minimum width of the Reading time Container.

= 1.0.3 =
- Fix: Read time postfix not displayed in frontend.

= 1.0.2 =
- Fix: Do not load CSS/JS on pages where read-meter/read-time is not used.

= 1.0.1 =
- Fix: Fixed the Dropdown of Progress bar error.
- Fix: Changed default values of Progress Bar colors.
- Fix: Changed default values of Post type.

= 1.0.0 =
- Initial Release.
