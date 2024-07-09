=== PDF Embedder ===
Contributors: slaFFik, jaredatch, smub
Tags: pdf, pdf viewer, embed pdf, pdf document, pdf block
Requires at least: 5.8
Requires PHP: 7.0
Tested up to: 6.5
Stable tag: 4.8.2
License: GPL-2.0-or-later

Seamlessly embed PDFs into your content, with customizations and intelligent responsive resizing, and no third-party services or iframes.

== Description ==

Upload PDF files and embed them directly into your site's posts and pages. It works as simple as adding images! Your PDF files will be automatically sized to their natural size and shape. You can also specify a width and the correct height will be calculated automatically. Our PDF Embedder plugin is fully responsive, so the embedded PDF will also look perfect, on any device. Additionally, the pdf embedder will automatically resize whenever the browser dimensions change.

The plugin has a unique method for embedding PDF files with the immense flexibility over the appearance of your document.

The PDF viewer has Next and Previous buttons to navigate the document, and zoom buttons if some areas of a PDF file are too small for your screen.

Your PDF files are embedded within your existing WordPress pages so we have full control over appearance, and all Javascript and other files are served by your own server (not by Google or any other third-party who may not be able to guarantee their own reliability). This means your PDF files will load fast, without any speed penalty.

Other PDF embedder plugins insert the PDF into an 'iframe' which means they do not get the flexibility over sizing. Ours does not and that is an incredible benefit to the overall viewing experience across all devices.

In the free plugin, there is no button for users to download the PDF, but download options are available in the Premium versions along with other awesome features.

Another Premium feature are Hyperlinks in your PDF being clickable. Links in the free plugin cannot be clicked.


= Usage =

1. Once installed and Activated, click Add Media from any page or post, just like adding an image, but drag and drop a PDF file instead.

1. In the Classic Editor when you insert into your post, it will appear in the editor as a 'shortcode' as follows:

1. <code>[pdf-embedder url="https://example.com/wp-content/uploads/2024/01/Plan-Summary.pdf"]</code>

1. You can change the default appearance - e.g. width, and toolbar position/appearance through **Settings -> PDF Embedder** and also shortcode or block attributes.

To override your site-wide defaults on an individual embed, see the [Plugin Instructions](https://wp-pdf.com/free-instructions/?utm_source=wprepo&utm_medium=link&utm_campaign=liteplugin) for information about sizing options plus other ways to customize the shortcodes.

= Premium Features =

Features available in the PDF Embedder Premium versions:

* Download button in the toolbar
* Continuous scroll between pages
* Hyperlinks are fully functional
* Full screen mode
* Edit page number to jump straight to page
* Track number of downloads and views
* Mobile-friendly
* Secure option - difficult to download original PDF
* Removes wp-pdf.com branding

**See [wp-pdf.com](https://wp-pdf.com/?utm_source=wprepo&utm_medium=link&utm_campaign=liteplugin) for details!**

= Mobile-friendly embedding using PDF Embedder Premium =

The free version will work on most mobile browsers, but cannot position the document entirely within the screen.

Our **PDF Embedder Premium** plugin solves this problem with an intelligent 'full screen' mode.

When the document is smaller than a certain width, the document displays only as a 'thumbnail' with a large 'View in Full Screen' button for the user to click when they want to study your document.

This opens up the document so it has the full focus of the mobile browser, and the user can move about the document without hitting other parts of the web page by mistake. Viewers can then Click Exit to return to the regular page.

The user can also swipe continuously between all pages of the PDF which is more natural than clicking the navigation buttons to navigate.

See our site [wp-pdf.com](https://wp-pdf.com/premium/?utm_source=wprepo&utm_medium=link&utm_campaign=liteplugin) for more details and purchase options.

= Protect your PDFs with our premium document embedder version =

Our **PDF Embedder Premium** plugin on its Pro plan provides the same simple but elegant viewer as the Basic version, with the added protection that it is difficult for users to download or print the original PDF document.

This means that your PDF is unlikely to be shared outside your site where you have no control over who views, prints, or shares it.

Optionally, add a watermark containing any text, including the logged in user's name or email address to discourage sharing of screenshots.

See our site [wp-pdf.com](https://wp-pdf.com/secure/?utm_source=wprepo&utm_medium=link&utm_campaign=liteplugin) for more details and purchase options.

= PDF Thumbnails =

Our **PDF Thumbnails** plugin automatically generates fixed image versions of all PDF files in your Media Library, to use on your site as you wish.

You can use them as featured images in posts containing an embedded version of the PDF, or as a visual clickable link to download the PDF directly.
It also displays the thumbnail as the "icon" for the PDF in the Media Library, making it easy for authors to locate the PDFs they need to insert in a post.

See our site [wp-pdf.com/thumbnails/](https://wp-pdf.com/thumbnails/?utm_source=wprepo&utm_medium=link&utm_campaign=liteplugin) for more details and purchase options.

With thanks to the Mozilla team for developing the underlying [pdf.js](https://github.com/mozilla/pdf.js) technology used by this PDF documents viewer plugin.

== Screenshots ==

1. Uploaded PDF is displayed within your page/post at the correct size to fit.
2. User hovers over document to see Next/Prev page buttons.
3. Settings can change appearance of the viewer, including size.

== Frequently Asked Questions ==

= How can I obtain support for this product? =

We have [instructions](https://wp-pdf.com/free-instructions/?utm_source=wprepo&utm_medium=link&utm_campaign=liteplugin) and a [Knowledge Base](https://wp-pdf.com/kb/?utm_source=wprepo&utm_medium=link&utm_campaign=liteplugin) on our website explaining common setup queries and issues.

We try to review daily and respond to support queries posted on the 'Support' forum here on the wordpress.org plugin page.

= How can I change the Size or customize the Toolbar? =

See Settings -> PDF Embedder in your WordPress admin to change site-wide defaults. You can also override individual embeds by modifying the shortcode attributes or using block options (applicable if you are using the Block Editor).

Resizing works as follows:

* If `width='max'` the width will take as much space as possible within its parent container (e.g. column within your page).
* If width is a number (e.g. `width="500"`) then it will display at that number of pixels wide.

Please note: both height and width expect either the number (integer) or just the word `max`. Everything else will have no effect.

*In all cases, if the parent container is narrower than the width calculated above, then the document width will be reduced to the size of the container.*

The height will be calculated so that the document fits naturally, given the width already calculated.

The Next/Prev toolbar can appear at the top or bottom of the document (or both or none), and it can either appear only when the user hovers over the document or it can be fixed at all times.

See the [Plugin Instructions](https://wp-pdf.com/free-instructions/?utm_source=wprepo&utm_medium=link&utm_campaign=liteplugin) for more details about sizing and toolbar options.

= PDF Embedder Premium feature list =

Features available in the premium versions of the plugin:

* Download button in the toolbar
* Continuous scroll between pages
* Hyperlinks are fully functional, both within the PDF document and leading outside of the file to any other URL
* Full screen mode
* Edit page number to jump straight to page when showing PDF Viewer
* Track number of downloads and views of each PDF file
* Mobile-friendly
* Secure option - difficult to download original PDF
* Watermark - add own text globally to all PDFs or selectively to only some of them

See [wp-pdf.com](https://wp-pdf.com/?utm_source=wprepo&utm_medium=link&utm_campaign=liteplugin) for details!


= Can I improve the viewing experience for mobile users? =

Yes, our **PDF Embedder Premium** plugin has an intelligent 'full screen' mode.
When the document is smaller than a certain width, the document displays only as a 'thumbnail' with a large 'View in Full Screen' button for the user to click when they want to study your document.
This opens up the document so it has the full focus of the mobile browser, and the user can move about the document without hitting other parts of the web page by mistake.
Exiting the document is possible by clicking the "Full screen" icon in the toolbar again.

See our website [wp-pdf.com](https://wp-pdf.com/premium/?utm_source=wprepo&utm_medium=link&utm_campaign=liteplugin) for more details and purchase options.

= Can I protect my PDFs so they are difficult for viewers to download directly? =

Not with the free or Basic/Plus pdf embedder premium versions - it is relatively easy to find the link to download the file directly.

A **Pro** version is available that encrypts the PDF during transmission, so it is difficult for a casual user to save or print the file for use outside your site.

See our website [wp-pdf.com](https://wp-pdf.com/secure/?utm_source=wprepo&utm_medium=link&utm_campaign=liteplugin) for more details and purchase options.

= Can I add a Download button to the toolbar? =

This is possible only in the PDF Embedder Premium version. As a workaround in the free version, you could add a direct link to the PDF beneath the embedded version.

To do this, copy the URL from the pdf-embedder shortcode and insert it into a link using HTML such as this:
&lt;a href="(url of PDF)"&gt;Download Here&lt;/a&gt;

= Are Hyperlinks supported? =

The Premium versions allow functioning hyperlinks - both internal links within the document, and links to external websites.

== Installation ==

Easiest way:

1. Go to your WordPress admin control panel's "Plugins > Add new" page
1. Search for 'PDF Embedder'
1. Click Install
1. Click Activate

If you cannot install from the WordPress plugins directory for any reason, and need to install from ZIP file:

1. Upload directory and contents to the `/wp-content/plugins/` directory, or upload the ZIP file directly in the Plugins section of your WordPress admin
1. Click Activate on the "Plugins" screen.

== Changelog ==

= 4.8.2 =
* Fixed: PDF files containing the text in certain languages (like Korean or Japanese) were not rendered properly due to a bug in PDF.js library incorrectly handling passed options.

= 4.8.1 =
* Fixed: Make sure that when `width` and `height` shortcode/option values have an incorrect value, the plugin does not generate a fatal error.

= 4.8.0 =
* Changed: Compatibility with WordPress 6.5.
* Changed: Make the PDF Embedder block extensible.
* Changed: Improved the look and feel of the PDF Embedder block inside the Block Editor.
* Changed: Removed some unnecessary files from the released version to decrease the zip size.
* Fixed: Improved performance for the majority of sites by not loading an internal Action Scheduler library (which was also updated to v3.7.4) when it is not used.
* Fixed: Hide the Toolbar Hover options in the block if the "No Toolbar" option is chosen.
* Fixed: Security fixes in the way certain PDF files are rendered to prevent arbitrary scripts execution.

= 4.7.1 =
* Changed: The logic for displaying notices was adjusted.
* Fixed: Improved handling of incorrect URLs supplied to the shortcode - PDF viewer won't even try to render it.

= 4.7.0 =
* IMPORTANT: The minimum WordPress version has been raised to WordPress v5.8.
* IMPORTANT: The minimum PHP version has been raised to PHP v7.0.
* IMPORTANT: If you are using a caching plugin and added PDF Embedder JS files to the exclusion list, you will need to do that again due to changed file names.
* Added: New option for the toolbar location called "No Toolbar" is now available. It allows you to hide the toolbar completely.
* Changed: Plugin admin area interface has been refreshed.
* Changed: The plugin has been tested with the latest version of WordPress.
* Changed: Block was rewritten from scratch, and now it looks better in the Block Editor, and also syncs its default settings with global plugin options.
* Fixed: A lot of strings in the plugin have been fixed to make them translatable and accurate.
* Fixed: Several security related improvements have been introduced (data sanitization and escaping).
* Fixed: Text in PDF files in certain languages (like, Japanese and Korean) was not rendered correctly.

The full changelog is located in the changelog.txt file.
