=== Simple Mathjax ===

Contributors: sgcoskey, pkra, christianperfect
Donate link: https://boolesrings.org
Tags: mathjax, latex
Requires at least: 3.0
Tested up to: 6.2
Stable tag: 2.0.2

Yet another plugin to add MathJax support to your wordpress blog.
Just wrap your equations inside $ signs and MathJax will render them visually.

== Description ==

This wordpress plugin is yet another simple plugin to load the [MathJax](http://www.mathjax.org) scripts at the bottom of all of your pages. It uses a very all-inclusive mathjax configuration by default, with $'s and $$'s the default delimeters for in-line and displayed equations.

A preference pane is added to the "Settings" group where you can choose whether to use MathJax version 2 or 3, change the MathJax server location (CDN) and the MathJax configuration settings. (See [this page](https://docs.mathjax.org/en/latest/configuration.html#using-in-line-configuration-options) for details on the options available.) 
You can also specify a LaTeX "preamble" of newcommands which will be loaded in a hidden element near the top of each page.

Fork this plugin on [GitHub](https://github.com/boolesrings/Simple-Mathjax-wordpress-plugin).

== Installation ==

Nothing unusual here!

== Changelog ==

= 2.0.1 =
Bumped the default CDN URL for MathJax 2 to use 2.7.8.

= 2.0 =
MathJax 3 support, and the default config makes MathJax ignore the new block editor.

= 1.1 = 
update latest mathjax version, load mathjax in footer instead of header

= 1.0 =
send default url to new cdn

= 0.5 =
minor code cleanup, allow mathjax in admin screens

= 0.4 =
use safe mode (prevents evil scripts) by default

= 0.3 =
use wp_enqueue_script to allow others to use mathjax as a dependency (christianp). removed disqus compatibility due to reports of it no longer working.

= 0.2 =
added disqus compatibility. enclosed the preamble in a hidden span to
cover a small space created by mathjax v.2

= 0.1 =
improved loading of the LaTeX preamble so that it appears just
below the body tag, rather than in the header

= 0.0 =
initial release
