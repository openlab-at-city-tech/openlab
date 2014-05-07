=== Edge Suite ===
Contributors: ti2m
Tags: media, animation, interactive, adobe edge animate, edge animate, edge, embed, integration
Requires at least: 3.6
Tested up to: 3.8
Stable tag: 0.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Manage and view your Adobe Edge Animate compositions on your website.


== Description ==

Upload of compositions through published OAM files. Integrate Adobe Edge Animate compositions seamlessly into your website.

Detailed tutorials on how to install and use Edge Suite can be found here:

* <a href="http://old.edgedocks.com/content/edge-suite-integrate-edge-animate-wordpress">Edge Docks - written tutorial</a>
* <a href="http://tv.adobe.com/watch/create-like-crazy-with-adobe-edge/episode-5-spice-up-your-wordpress-site-with-edge-animate/">Adobe TV - video tutorial</a>

More resources can be found at <a href="http://edgedocks.com/">EdgeDocks.com</a>.

The plugin has been intensively tested over the last couple of months and seems to be rather stable, it is up to you though if you want to try and use it in production. If you experience
any problems please read the FAQ before opening an issue in the support section. If you like the plugin please vote for it and let us know that it works for your wordpress version.

== Features ==

* Upload Edge Animate compositions through published OAM files
* Manage all compositions
* Easy placement of compositions on the website
* Shortcode support for posts and widgets
* Sharing of resources
* jQuery no conflict mode

== Frequently Asked Questions ==

= Dev version =

When experiencing any problems, please always try the latest dev version (http://downloads.wordpress.org/plugin/edge-suite.zip).
 Manual installation is needed. New features and bugfixes are always tested first in the dev version before they are being merged
 into the stable version.

= General things to check when problems occur =

* Open the debug console in Chrome (mac: alt + cmd + j) or Firefox and check for JavaScript errors.

= jQuery issues =

 Try to enable jQuery no conflict mode in the settings page, this might resolve conflicts between Edge Animates
 version of jQuery and other plugins.

= Animations don't show up =

Uploading worked but nothings shows up on the page. Things to check:

* Look at the source code of the page and search for:

* "stage" - You should find a div container, if so HTML rendering went fine.

* "_preloader" - You should find a script tag, if so JS inclusion went fine.

* If "stage" or "_preloader" are not found, disable other plugins for testing to check if they might interferer.

* For testing remove all other fancy JavaScript like galleries, slideshows, etc. that are placed alongside the animation, the JS might collide.

* Enable the JS debug log in the settings

* Enable jQuery no-conflict mode in the settings

= Custom JavaScript =

When using custom JavaScript code make sure you reference the Stage through

    sym.$('Stage')

$('#Stage') or sym.$('#Stage') will not work. The reason is that Edge Suite needs to alter the Stage Div Id, so '#Stage'
does not exist. When using sym.$('Stage') Edge Animate will use its internal reference to get the stage.

= Head Cleaner: Animations don't show up =

Head Cleaner basically skips the processing of edge_suite_header() which is needed to inject the Edge Javascript.
Under Settings > Head Cleaner > Active Filters check the box "Don't process!" for "edge_suite_header" and click "Save options".
This stops Head Cleaner from "processing" ede_suite_header(), which basically means allowing edge_suite_header() (reverse logic).

= PHP ZipArchive not found =

zip.so needs to be installed as a PHP library

= Background animation =

If you want to use your composition as a background animation, try the following CSS on your stage id, e.g. #Stage_mycomp

    #Stage_mycomp{
        position: absolute !important; /* That is not pretty, but no way around it. */
        top: 0px; /* Aligns the animation with the top of the header (can be removed or changed) */
        z-index: -5; /* Places animation in the background, set to a positive value for an overlay */

        /* Only use the following if you want to center the animation */
        left: 50%;
        margin-left: -346px; /* Half the width of the stage */
        top: 50%;
        margin-top: -200px; /* Half the height of the stage */
    }


== Installation ==

1. IMPORTANT: Backup your complete wordpress website, this module is in early development state!
1. Install the Edge Suite plugin as any other wordpress plugin.
1. Make sure /wp-content/uploads/edge_suite was created and is writable.
1. Backup your complete theme folder.
1. Publish your project in Adobe Edge with option "Animate Deployment Package". It will generate a single OAM file.
1. Go to "Edge Suite > Manage", select the oam file and upload it.
1. Upload as many composition as you want.
1. After uploading, the compositions can be placed in multiple ways on the website:
1. Shortcodes:
    * Take a look at the manage page drop down and remember the id of the composition you want to integrate. E.g. for "3 - My first edge compositions" the id is 3.
    * Edit a page and include [edge_animation id="3"] where 3 is of course your composition id.
    * Save the post, the composition shows up.
    * You can also use [edge_animation id="3" left="auto"] to center the stage on the page.
    * If you want to define a pixel base left an top offset of e.g. 10px from the left and 20px from the top, try [edge_animation id="3" left="10" top="20"]
1. Template based:
    * Here you insert a PHP snippet in your theme files:
    * Find e.g. the header.php file in your theme.
    * Insert the following snippet where the compositions should appear (inside php tags):

      if(function_exists('edge_suite_view')){echo edge_suite_view();}

    * Placing the code within in a link tag (<a href=""...) can cause problems when the composition is interactive.
    * You now have three options to tell wordpress which composition to show where the PHP snippet was placed.
    * Default: A composition that should be shown on all pages can be selected on the "Edge Suite > settings" page under "Default composition".
    * Homepage: A composition that is only meant to show up on the homepage can also be selected there.
    * Page/Post: In editing mode each post or a page has a composition selection that, when chosen, will overwrite the default composition.


== Support ==

Please report any bugs to the Edge Suite support queue on wordpress.


== Changelog ==

= 0.2 =
Change of filesystem usage

= 0.3 =
Changes to support Edge Animate version 1.0, minified files, oam files

= 0.4 =
Bugfixes

= 0.5 =
Bugfixes, jQuery no conflict mode, readme update

= 0.6 =
Bugfixes, readme update, better error handling, Edge Animate 3 updates



