=== Gutenberg ===
Contributors: matveb, joen, karmatosed
Requires at least: 5.1.0
Tested up to: 5.2
Stable tag: 6.7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The block editor was introduced in core WordPress with version 5.0. This beta plugin allows you to test bleeding-edge features around editing and customization projects before they land in future WordPress releases.

== Description ==

Gutenberg is more than an editor. While the editor is the focus right now, the project will ultimately impact the entire publishing experience including customization (the next focus area).

<a href="https://wordpress.org/gutenberg">Discover more about the project</a>.

= Editing focus =

> The editor will create a new page- and post-building experience that makes writing rich posts effortless, and has “blocks” to make it easy what today might take shortcodes, custom HTML, or “mystery meat” embed discovery. — Matt Mullenweg

One thing that sets WordPress apart from other systems is that it allows you to create as rich a post layout as you can imagine -- but only if you know HTML and CSS and build your own custom theme. By thinking of the editor as a tool to let you write rich posts and create beautiful layouts, we can transform WordPress into something users _love_ WordPress, as opposed something they pick it because it's what everyone else uses.

Gutenberg looks at the editor as more than a content field, revisiting a layout that has been largely unchanged for almost a decade.This allows us to holistically design a modern editing experience and build a foundation for things to come.

Here's why we're looking at the whole editing screen, as opposed to just the content field:

1. The block unifies multiple interfaces. If we add that on top of the existing interface, it would _add_ complexity, as opposed to remove it.
2. By revisiting the interface, we can modernize the writing, editing, and publishing experience, with usability and simplicity in mind, benefitting both new and casual users.
3. When singular block interface takes center stage, it demonstrates a clear path forward for developers to create premium blocks, superior to both shortcodes and widgets.
4. Considering the whole interface lays a solid foundation for the next focus, full site customization.
5. Looking at the full editor screen also gives us the opportunity to drastically modernize the foundation, and take steps towards a more fluid and JavaScript powered future that fully leverages the WordPress REST API.

= Blocks =

Blocks are the unifying evolution of what is now covered, in different ways, by shortcodes, embeds, widgets, post formats, custom post types, theme options, meta-boxes, and other formatting elements. They embrace the breadth of functionality WordPress is capable of, with the clarity of a consistent user experience.

Imagine a custom “employee” block that a client can drag to an About page to automatically display a picture, name, and bio. A whole universe of plugins that all extend WordPress in the same way. Simplified menus and widgets. Users who can instantly understand and use WordPress  -- and 90% of plugins. This will allow you to easily compose beautiful posts like <a href="http://moc.co/sandbox/example-post/">this example</a>.

Check out the <a href="https://wordpress.org/gutenberg/handbook/reference/faq/">FAQ</a> for answers to the most common questions about the project.

= Compatibility =

Posts are backwards compatible, and shortcodes will still work. We are continuously exploring how highly-tailored metaboxes can be accommodated, and are looking at solutions ranging from a plugin to disable Gutenberg to automatically detecting whether to load Gutenberg or not. While we want to make sure the new editing experience from writing to publishing is user-friendly, we’re committed to finding  a good solution for highly-tailored existing sites.

= The stages of Gutenberg =

Gutenberg has three planned stages. The first, aimed for inclusion in WordPress 5.0, focuses on the post editing experience and the implementation of blocks. This initial phase focuses on a content-first approach. The use of blocks, as detailed above, allows you to focus on how your content will look without the distraction of other configuration options. This ultimately will help all users present their content in a way that is engaging, direct, and visual.

These foundational elements will pave the way for stages two and three, planned for the next year, to go beyond the post into page templates and ultimately, full site customization.

Gutenberg is a big change, and there will be ways to ensure that existing functionality (like shortcodes and meta-boxes) continue to work while allowing developers the time and paths to transition effectively. Ultimately, it will open new opportunities for plugin and theme developers to better serve users through a more engaging and visual experience that takes advantage of a toolset supported by core.

= Contributors =

Gutenberg is built by many contributors and volunteers. Please see the full list in <a href="https://github.com/WordPress/gutenberg/blob/master/CONTRIBUTORS.md">CONTRIBUTORS.md</a>.

== Frequently Asked Questions ==

= How can I send feedback or get help with a bug? =

We'd love to hear your bug reports, feature suggestions and any other feedback! Please head over to <a href="https://github.com/WordPress/gutenberg/issues">the GitHub issues page</a> to search for existing issues or open a new one. While we'll try to triage issues reported here on the plugin forum, you'll get a faster response (and reduce duplication of effort) by keeping everything centralized in the GitHub repository.

= How can I contribute? =

We’re calling this editor project "Gutenberg" because it's a big undertaking. We are working on it every day in GitHub, and we'd love your help building it.You’re also welcome to give feedback, the easiest is to join us in <a href="https://make.wordpress.org/chat/">our Slack channel</a>, `#core-editor`.

See also <a href="https://github.com/WordPress/gutenberg/blob/master/CONTRIBUTING.md">CONTRIBUTING.md</a>.

= Where can I read more about Gutenberg? =

- <a href="http://matiasventura.com/post/gutenberg-or-the-ship-of-theseus/">Gutenberg, or the Ship of Theseus</a>, with examples of what Gutenberg might do in the future
- <a href="https://make.wordpress.org/core/2017/01/17/editor-technical-overview/">Editor Technical Overview</a>
- <a href="https://wordpress.org/gutenberg/handbook/reference/design-principles/">Design Principles and block design best practices</a>
- <a href="https://github.com/Automattic/wp-post-grammar">WP Post Grammar Parser</a>
- <a href="https://make.wordpress.org/core/tag/gutenberg/">Development updates on make.wordpress.org</a>
- <a href="https://wordpress.org/gutenberg/handbook/">Documentation: Creating Blocks, Reference, and Guidelines</a>
- <a href="https://wordpress.org/gutenberg/handbook/reference/faq/">Additional frequently asked questions</a>


== Changelog ==

### Features

*   [Support gradients](https://github.com/WordPress/gutenberg/pull/18001) in Cover block.
*   Add a breadcrumb bar to support [block hierarchy selection](https://github.com/WordPress/gutenberg/pull/17838).

### Enhancements

*   Cover block: change the [minimum height input step size](https://github.com/WordPress/gutenberg/pull/17927) to one.
*   Allow setting a [display name for blocks](https://github.com/WordPress/gutenberg/pull/17519) based on their content in the BlockNavigator.
*   [Hide the gradients panel](https://github.com/WordPress/gutenberg/pull/18091) if an empty set of gradients is explicitly defined.
*   [Do not transform list items into paragraphs](https://github.com/WordPress/gutenberg/pull/18032) when deleting first list item and list is not empty.
*   Replace inline styles with [classnames for the gradient palette](https://github.com/WordPress/gutenberg/pull/18008).
*   [Preserve list attributes](https://github.com/WordPress/gutenberg/pull/17144) (start, type and reversed) when pasting or converting HTML to blocks. 

### Bugs

*   [Clear local autosaves](https://github.com/WordPress/gutenberg/pull/18051) after successful saves.
*   Fix the [columns block](https://github.com/WordPress/gutenberg/pull/17968) width overflow issue when using more than two columns.
*   Fix the [Link Rel input](https://github.com/WordPress/gutenberg/pull/17398) not showing the saved value of the link rel attribute.
*   Fix JavaScript errors triggered when using [links without href](https://github.com/WordPress/gutenberg/pull/17928) in HTML mode.
*   Move the [default list styles](https://github.com/WordPress/gutenberg/pull/17958) to the theme editor styles.
*   Fix [Invalid import](https://github.com/WordPress/gutenberg/pull/17969) statement for deprecated call in the Modal component.
*   Fix a small visual glitch in the [Publish button](https://github.com/WordPress/gutenberg/pull/18016).
*   Prevent blank page when using the [Media Modal Edit Image "back"](https://github.com/WordPress/gutenberg/pull/18007) button.
*   Allow the [shortcode transform](https://github.com/WordPress/gutenberg/pull/17925) to apply to all the provided shortcode aliases. 
*   Fix JavaScript error triggered when using arrows on an [empty URLInput](https://github.com/WordPress/gutenberg/pull/18088).
*   Fix [extra margins added to Gallery blocks](https://github.com/WordPress/gutenberg/pull/18019) by list editor styles.
*   Fix [custom button background color](https://github.com/WordPress/gutenberg/pull/18037) not reflected on reload.
*   [Preserve List block attributes](https://github.com/WordPress/gutenberg/pull/18102) when splitting into multiple lists. 
*   Fix [checkbox styles](https://github.com/WordPress/gutenberg/pull/18108) when used in metaboxes.
*   Make the [FontSizePicker style](https://github.com/WordPress/gutenberg/pull/18078) independent from WordPress core styles.
*   Fix overlapping controls in the [Inline Image formatting toolbar](https://github.com/WordPress/gutenberg/pull/18090).
*   Fix [strikethrough formatting](https://github.com/WordPress/gutenberg/pull/17187) when copy/pasting from Google Docs in Safari.
*   Allow [media upload post processing](https://github.com/WordPress/gutenberg/pull/18106) for all 5xx REST API responses.

## Experiments

*   Navigation block:
    *   Support [color customization](https://github.com/WordPress/gutenberg/pull/17832).
    *   Improve the [Link edition UI](https://github.com/WordPress/gutenberg/pull/17986).
*   Block Content Areas:
    *   Implement a frontend [template loader](https://github.com/WordPress/gutenberg/pull/17626) based on the  **wp_template** CPT.
    *   Add a temporary [UI to edit **wp_template**](https://github.com/WordPress/gutenberg/pull/17625) CPT posts.
    *   Add a [Site title block](https://github.com/WordPress/gutenberg/pull/17207).

### New APIs

*   Add [VisuallyHidden](https://github.com/WordPress/gutenberg/pull/18022) component.
*   Add [**@wordpress/base-styles**](https://github.com/WordPress/gutenberg/pull/17883) package to share the common variables/mixins used by the WordPress packages.
*   Add [Platform component](https://github.com/WordPress/gutenberg/pull/18058) to allow writing platform (web, mobile) specific logic.
*   Add isInvalidDate prop to [DatePicker](https://github.com/WordPress/gutenberg/pull/17498).
*   @wordpress/env improvements:
    *   Support [custom ports](https://github.com/WordPress/gutenberg/pull/17697).
    *   Support using it for [themes](https://github.com/WordPress/gutenberg/pull/17732).
*   Add a new experimental React  hook to [support colors in blocks](https://github.com/WordPress/gutenberg/pull/16781).
*   Add a new experimental [DimentionControl](https://github.com/WordPress/gutenberg/pull/16791) component.

### Various

*   Storybook:
    *   Add a story for the [CheckboxControl](https://github.com/WordPress/gutenberg/pull/17891) component.
    *   Add a story for the [Dashicon](https://github.com/WordPress/gutenberg/pull/18027) component.
    *   Add a story for the [ColorPalette](https://github.com/WordPress/gutenberg/pull/17997) component.
    *   Add a story for the [ColorPicker](https://github.com/WordPress/gutenberg/pull/18013) component.
    *   Add a story for the [ExternalLink](https://github.com/WordPress/gutenberg/pull/18084) component.

Add knobs to the [ColorIndicator Story](https://github.com/WordPress/gutenberg/pull/18015).

*   Several other [enhancements to existing stories](https://github.com/WordPress/gutenberg/pull/18030).
*   [Linting fixes](https://github.com/WordPress/gutenberg/pull/17981) for Storybook config.
*   Fix Lint warnings triggered by [JSDoc definitions](https://github.com/WordPress/gutenberg/pull/18025).
*   [Reorganize e2e tests](https://github.com/WordPress/gutenberg/pull/17990) [specs](https://github.com/WordPress/gutenberg/pull/18020) into three folders: editor, experimental and plugin.
*   Cleanup [skipped e2e tests](https://github.com/WordPress/gutenberg/pull/18003).
*   Add a [link to Storybook](https://github.com/WordPress/gutenberg/pull/17982) from the Gutenberg playground.
*   Optimize the **@wordpress/compose** package to [support tree-shaking](https://github.com/WordPress/gutenberg/pull/17945).
*   Code Quality:
    *   Refactor the [Button block edit function](https://github.com/WordPress/gutenberg/pull/18006) to use a functional component.
    *   Change the name of the [accumulated variables](https://github.com/WordPress/gutenberg/pull/17893) in reduce functions.
    *   Remove wrapper around the [Table block cells](https://github.com/WordPress/gutenberg/pull/17711).
*   Fix several issues related to [Node 12](https://github.com/WordPress/gutenberg/pull/18054) [becoming](https://github.com/WordPress/gutenberg/pull/18057) LTS.
*   Add the [Block Inspector](https://github.com/WordPress/gutenberg/pull/18077) to the Gutenberg playground.

### Documentation

*   Enhance the [Git workflow](https://github.com/WordPress/gutenberg/pull/17662) documentation.
*   Clarify [block naming conventions](https://github.com/WordPress/gutenberg/pull/18117).
*   Tweaks and typos: [1](https://github.com/WordPress/gutenberg/pull/17980), [2](https://github.com/WordPress/gutenberg/pull/18039).

