=== Simple Pull Quote ===
Contributors: llamaman, themightymo
Donate link: http://themightymo.com/donate
Tags: pull quote,quotes, quotation
Requires at least: 2.5
Tested up to: 4.4.2
Stable tag: trunk

The Simple Pull Quote WordPress Plugin provides an easy way for you to insert pull quotes into your posts and pages.  

== Description ==

Simple Pull Quote Wordpress Plugin provides an easy way for you to insert pull quotes into your posts and pages.  It adds an easy-to-use "Pullquote" button to both the HTML and TinyMCE editors. 

See the plugin in action as well as how to use it:
http://youtu.be/JGudI9gr9iE

[youtube http://www.youtube.com/watch?v= JGudI9gr9iE]

== Installation ==

1. Either use the built-in Wordpress plugin installer to grab the plugin from the Wordpress plugin repository, or upload the entire contents of the `simple-pull-quote.zip` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==
Q: How do I make the quote appear on the left side?  
A: After you create your pull quote, add “class=“left” to it like this: [pullquote class="left”]TEXT HERE[/pullquote]

Q: What if I am using a visual editor besides TinyMCE?
A: You will need to manually add the shortcode [pullquote]YOUR TEXT HERE[/pullquote]

Q: Is there a maximum number of pull quotes I can have on a single blog post or page?
A: No.  You can have as many pull quotes as you would like.

Q: How do I change the colors and background image on the pullquotes?
A: Override the default "simplePullQuote" class in your theme's CSS file.  Here is a “How To” video about this: http://youtu.be/qvg2BFnN0pQ
[youtube http://www.youtube.com/watch?v=qvg2BFnN0pQ]

Q: Where is the "Pullquote" button in the HTML editor?
A: I don't know.  It disappeared with the most recent version of WordPress, and I am working on fixing it.

= Questions, Comments, Pizza Recipes? =

Look me up on [Twitter](http://twitter.com/themightymo "Twitter") or contact me [here](http://www.themightymo.com/contact-us/ "Contact Me").


== Usage ==

1. Select the text that you want to use as your pull quote.
2. Click on the "Pullquote" button in either the Visual or HTML editor.
<br /><br />For more help on usage, visit the [Simple Pull Quote Homepage](http://themightymo.com/simple-pull-quote "Simple Pull Quote Wordpress Plugin") for a visual guide.

= How do I update the look of the pull quotes? =

To change the look of your pull quotes, open your theme's "style.css" file and create a CSS class called "simplePullQuotes".  Edit this class according to your tastes.  **IMPORTANT:** Make sure your theme's "wp_head()" function comes before your theme's stylesheet or else this won't work.

= How do I use more than one pull quote in a single post or page? =

Simply select the text that you want to use as a pull quote and click the "Pullquote" button in either the visual or html editor.

== Upgrade Notice == 

Version 1.0 of Simple Pull Quote is backwards compatible with previous versions.  Upgrade freely!

== Changelog == 
= 1.4 = 
Maintenance: Added icons for new plugin search ui

= 1.4 =
* Cleaned up readme.txt’s changelog
* Added “quicktags” dependency to javascript load per http://wordpress.org/support/topic/wp_enqueue_script-missing-dependency?replies=2#post-3451836 (Thanks Aaron Campbell)
* Enqueue the simple-pull-quote.js on specific pages rather than on every admin page per http://wordpress.org/support/topic/wp_enqueue_script-missing-dependency?replies=2#post-3451836 (Thanks Aaron Campbell)
* Removed white space at the bottom of simple-pull-quote.php and simple-pull-quote-tinymce.php

= 1.3 =

= 1.2 =

* Fixed the file path reference to the stylesheet to use "plugins_url" rather than "wpurl"
* Thanks to John LeBlanc for this fix: http://wordpress.org/support/profile/johnleblanc

= 1.1 =

* Removed inadvertent line breaks caused by wptexturize()
* Thanks to http://sww.co.nz/solution-to-wordpress-adding-br-and-p-tags-around-shortcodes/ for this fix 

= 1.0 =

* No need for custom fields!  (However legacy support for custom fields still exists.)
* Added "Pullquote" buttons to both the html and visual editors.
* Updated CSS Code
* Many thanks to [Darrell Schulte](http://twitter.com/darrell_schulte "Darrell Schulte") for his help in making version 1.0 a reality! 

= 0.2.4 =

* Fixed Wordpress Plugin Repository file downloading issue.

= 0.2.3 =

* Switched the quote .gif file with a .png that will support any color background.


= 0.2.2 =

* Added shortcode for multiple quotes: [quote1], [quote2]

= 0.2.1 =

* Removed text-based quotes by default. Now users must add their own quotes if they want them.  This gives users more flexibility.<br />

= 0.2 =

* Initial public release


== Screenshots ==

1. Simple Pull Quote plugin in action!
2. Add pullquotes via the Wordpress TinyMCE editor.
3. Add pullquotes via the Wordpress HTML editor.
