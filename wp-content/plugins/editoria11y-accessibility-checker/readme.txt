=== Editoria11y Accessibility Checker ===
Contributors: itmaybejj, partyka
Tags: accessibility checker, automated testing, quality assurance, SEO
Stable tag: 1.0.13
Tested up to: 6.4
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Content accessibility checker written to be intuitive and useful for non-technical authors and editors.

== Description ==

Editoria11y ("editorial accessibility ally") is a quality assurance tool built for an author's workflow:

1. It provides instant feedback. Authors do not need to remember to press a button or visit a dashboard to check their work.
2. It checks in context on pages, not just within the post editor, allowing it to test content edited in widgets or theme features.
3. It focuses exclusively on **content** issues: assisting authors at improving the things that are their responsibility.

It is meant to **supplement**, not replace, [testing your code and visual design](https://webaim.org/resources/evalquickref/) with developer-focused tools and testing practices.

## The authoring experience

Check out a [demo of the checker itself](https://editoria11y.princeton.edu/demo).

* When **logged-in authors and editors** are viewing published or in-preview pages, Editoria11y's toggle discretely indicates if any issues are present on the current page.
* Clicking the toggle opens the checker's main panel. Alerts are placed on pieces of content with issues, with tooltips to explain each problem and what actions are needed to resolve it. If an issue is not critical or may be OK as written, buttons are available to dismiss the alert on this page, either for the current user ("Hide alert") or for all users ("Mark as Checked and OK").
* When Editoria11y finds **new** issues on a page that was just edited, the panel pops open automatically.
* The main panel also allows authors to jump to the next issue, restore previously dismissed alerts, visualize text alternatives for images on the page ("alts"), and view the document's heading outline.
* Optionally, the checker can also outline blocks with issues while the author is editing the content.

## The admin experience

* Filterable reports let you explore recent issues, which pages have the most issues, which issues are most common, and which issues have been dismissed. These populate and update as content is viewed and updated.
* Various settings are available to constrain checks to specific parts of the page and tweak the sensitivity of several tests.

Note that all this runs locally within your site. This plugin is the WordPress adaptation of the free and open-source [Editoria11y library](https://editoria11y.princeton.edu). Tests run in the browser and findings are stored in your own database; nothing is sent to any third party.

## The tests

* Text alternatives for visual content
    * Images with no alt text
    * Images with a filename as alt text
    * Images with very long alt text
    * Alt text that contains redundant text like “image of” or “photo of”
    * Images in links with alt text that appears to be describing the image instead of the link destination
    * Embedded visualizations that usually require a text alternative
* Meaningful links
    * Links with no text
    * Links titled with a filename
    * Links only titled with generic text: “click here,” “learn more,” “download,” etc.
    * Links that open in a new window without warning
* Document outline and structure
    * Skipped heading levels
    * Empty headings
    * Very long headings
    * Suspiciously short blockquotes that may actually be headings
    * All-bold paragraphs with no punctuation that may actually be headings
    * Suspicious formatting that should probably be converted to a list (asterisks and incrementing numbers/letters prefixes)
    * Tables without headers
    * Empty table header cells
    * Tables with document headers ("Header 3") instead of table headers
* General quality assurance
    * LARGE QUANTITIES OF CAPS LOCK TEXT
    * Links to PDFs and other documents, reminding the user to test the download for accessibility or provide an alternate, accessible format
    * Video embeds, reminding the user to add closed captions
    * Audio embeds, reminding the user to provide a transcript
    * Social media embeds, reminding the user to provide alt attributes
* [Custom results](https://editoria11y.princeton.edu/configuration/#customtests) provided by your JS

== Frequently Asked Questions ==

= How is this different from other checkers? =

Editoria11y is meant to supplement, not replace, these tools. 

Editoria11y is...spellcheck: a seamless, automatic and intuitive integration for content authoring. It:

* Does not require training before use.
* Eschews obfuscation and techno-legal jargon. It explains what the issue is in plain language, with a simple explanation of how to fix it. "This image needs alternative text" with a short explanation of what alternative text is makes sense without prior technical knowledge; "Failure of WCAG 1.1.1 Level A: Non-text Content" does not.
* Deliberately excludes tests for theme and plugin issues, like invalid HTML tags and ARIA attributes. Testing is critically important for themers and developers, but it is work for themers and developers, not content editors. For ongoing quality assurance, Editoria11y provides people with a tool that fits their role, so they only receive alerts for things they can fix.

= How is this different from Sa11y? =

Editoria11y's test suite is quite similar to [Sa11y](https://wordpress.org/plugins/sa11y/). Editoria11y began as a Sa11y fork, and the maintainers collaborate on new tests and optimizations.

The look, feel and features outside of the core test suite are a bit different. At a high level:

* Sa11y focuses on flexibility and extensibility:
    * The end-user can override appearance and test coverage settings from the results panel
    * A legibility scoring library is included
    * A contrast checking library is included
* Editoria11y focuses on providing a shared experience for all editors:
    * An API is added, meaning findings are synchronized with your site's database, populating a site-wide reporting dashboard
    * Manual-checks marked as "OK" are dismissed for **all** users, not just the current user
    * Issues can be highlighted within the Block Editor/Gutenberg as you edit, not just on the published page
    * All configuration is managed in the plugin settings

= Is this an overlay? =

Overlays are tools that modify your site's public pages according to automated attempts to modify its code and design, claiming these machine-generated changes to your site will better meet the accessibility needs of your users. 

Overlays may override your font sizes or colors, or modify its heading tags and buttons. You should familiarize yourself with the [assistive technology compatibility problems overlays may introduce](https://overlayfactsheet.com/) before assuming these changes will be helpful.

**Editoria11y is not an overlay.** It does not modify the site viewed by not-logged-in-users in any way. It is an editor-facing "spellchecker" that helps your site editors create accessible content.

## Installation

Editoria11y's default settings will work great for most sites. 

Your first task after installation should be clicking through a representative sampling of the main pages of your site. This will start to populate your dashboard report, and give you a chance to look for issues to fix or dismiss.

If you notice anything amiss, experiment with these settings:

1. Pick a "Theme for tooltips" that looks nice with your site's colors.
2. If the checker is flagging issues that are not relevant to content editors, either use "Check content in these containers" to constrain checks to the parts of the page with editable content, or "Exclude these elements from checks" to skip over certain elements, regions or widgets.
3. Editoria11y also provides a minimalist "as-you-type" issue highlighter that works inside the Block Editor/Gutenberg. If you find this feature annoying rather than helpful, change "Highlight issues while editing content" to "Only definite errors" or "None."
4. If you do not want PDF or other document types flagged for manual checks, provide a shorter selector list or set "Document types that need manual review" to `false`
5. If your theme has done something very unusual with its layout, such as setting the height of the content container to 0px, you may see confusing alerts when opening Editoria11y tips saying that the highlighted element may be off-screen or invisible. If that happens, disable "Check if elements are visible when using panel navigation buttons." This is disabled by defaults on any WordPress themes we have noticed this on, so if you find a theme 

If you are a theme developer, note that the library dispatches JavaScript events at several key moments (scan finishes, panel opens, tooltip opens or shuts), allowing you to attach custom functionality. JavaScript on sites running Editoria11y can use these events to do things like [automatically opening accordion widgets](https://editoria11y.princeton.edu/configuration/#hidden-content) if they contain hidden alerts, disabling "sticky" site menus if the panel is open, inserting [custom results](https://editoria11y.princeton.edu/configuration/#customtests), or syncing results to third-party dashboards.

And then...tell us how it went! This plugin and its base library are both under active development. Ideally send bug reports and feature requests through the [GitHub issue queue](https://github.com/itmaybejj/editoria11y-wp/issues).

## Credit

Editoria11y's WordPress plugin is maintained by Princeton University's [Web Development Services](https://wds.princeton.edu/) team:

* [John Jameson](https://github.com/itmaybejj): Editoria11y JS and CMS integrations
* [Jason Partyka](https://github.com/jasonpartyka): Devops
* [Brian Osborne](https://github.com/bkosborne): Code review
* [Michael Muzzie](https://www.drupal.org/u/notmike): Wapuu photos

Editoria11y began as a fork of the Toronto Metropolitan University's [Sa11y Accessibility Checker](https://sa11y.netlify.app/), and our teams regularly pass new code and ideas back and forth.

== Screenshots ==
1. Checker with an open "manual check" request
2. Optional feature: highlighting live in the block editor
3. Site-wide reporting dashboard
4. Checker set to dark theme, showing a table header alert

== Changelog ==

= 1.0.13 =
* Themes and plugins can now provide [custom results](https://editoria11y.princeton.edu/configuration/#customtests).
* Old results will clear from the dashboard when clicking through to a page with checking disabled.
* More accurate accessible name calculation for complex situations involving CSS generated content, nested SVG or aria-labelledby references.
* Headings are now clickable in the outline panel.
* Experimental inline checker now highlights specific links with issues within a block.
* Bugfix: Experimental inline checker was not appearing for 
* Bugfix: two consecutive paragraphs starting with "A " could throw a false positive on the possible link test.

= 1.0.12 =
* "Is this a list" test can now detect emoji-based fake lists.
* Dashboard now preserves URL protocol for local development.

= 1.0.11 =
* Bugfix for Type errors when checking HTML5 media embeds.

= 1.0.10 =
* As-you-edit highlighter now recognizes WordPress's placeholder text in image alts.
* Inline styles are now placed on the body tag instead of the checker Root element.
* More detailed documentation on the plugin settings page.

= 1.0.9 =
* Fix for configuration page not showing current selections for some fields.
* Fixes for some CSS conflicts in the block editor.

= 1.0.8 =
* Adds option to restrict viewing reports to site admins.
* Adds CSV export for site reports.

= 1.0.7 =
* Fix for bug that was preventing scrolling the outline and alt tag tabs on the main panel.
* Improvements to alignments and colors on tips.

= 1.0.6 =
* Prevent browsers from displaying cached (out of date) results on the dashboard reports.

= 1.0.5 =
* Bugfix for live edit highlighter.