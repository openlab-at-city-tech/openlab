---
title: Contribute to Genesis Docs
menuTitle: Genesis Docs
layout: layouts/base.njk
permalink: contribute/genesis-docs/index.html
tags: docs
---

Thank you for your interest in contributing to Genesis developer documentation.

<p class="notice-small">
<strong>If you need help with a theme or plugin customization</strong>, please use the <a href="{{ '/contribute/community/' | url }}">community resources</a>.
<br><br>
<strong>If you would like to report a correction or make a request for more developer documentation</strong>, please submit your comments to our <a href="{{ '/contribute/#general-feedback' | url }}">support&nbsp;team</a> or in the <a href="{{ '/contribute/community/#genesiswp-slack-workspace' | url }}">GenesisWP Slack workspace</a>.
</p>

If you would like to edit or add new developer documentation yourself, read on!

## About the Genesis documentation site

This site is built from Markdown files and templates in the main Genesis repository using the [Eleventy](https://www.11ty.io/) static site generator. Documentation for Eleventy is available here: https://www.11ty.io/docs/.

Genesis includes tooling to help you build this site locally, open it in a browser, make changes to documentation files, and see those changes reflected immediately before you contribute your new documentation back to Genesis.

## You will need

1. **Familiarity with the git version control system** and GitHub code hosting service. The [git&nbsp;handbook](https://guides.github.com/introduction/git-handbook/) from GitHub and [this free git course](https://www.git-tower.com/learn/) from Tower are good places to start if you're new to git.
2. **Access to the <a href="{{ '/contribute/genesis-core/#join-the-genesis-github-repository' | url }}">private Genesis GitHub repository</a>**, where Genesis developer documentation lives.
3. **[Node.js](https://nodejs.org/en/) installed on your computer**. The “node.js package manager” (npm) comes with Node.js. npm is what we use to “build” the documentation website from the documentation files.
4. **Basic command line knowledge**. You will need to open a command prompt on your computer, change directories with `cd`, and type the commands below to run the documentation development server.

<p class="notice-small">
If you get stuck with these requirements or the steps below, feel free to post in the <em>contributors</em> channel in the <a href="{{ '/contribute/community/#genesiswp-slack-workspace' | url }}">GenesisWP Slack workspace</a>.
</p>


## Getting started

Once you have Genesis repository access, Node.js, and a git command line or graphical client installed, you can:

1. Visit the [Genesis repository](https://github.com/studiopress/genesis) and [fork](https://help.github.com/articles/fork-a-repo/) it. This creates a copy of Genesis under your own GitHub account. Forking lets you experiment, make changes, and contribute those changes without worrying about breaking the main Genesis repository or obtaining permissions to modify it directly.
2. [Clone your fork](https://help.github.com/articles/cloning-a-repository/) of the Genesis repository. Cloning creates a local copy of your forked repository on your own computer so that you can edit it.
3. Open a command line application, and change directory to your local Genesis directory. (On macOS, open the Terminal app, type `cd` followed by a space, drag the genesis folder onto your terminal window to get the full path, then press enter.)
4. Type `npm install` and press enter to install dependencies needed to build the documentation.
5. Type `npm run docs:css` to build the initial CSS from the Sass files.
6. Type `npm run docs:dev` to build the documentation site. This also starts a server on your machine to preview and work on docs.
7. In your web browser, visit http://localhost:8080 (or the URL you see in your command line application after running `docs:dev`).

You should see the documentation site. It will automatically refresh with changes you make to documentation content and styling. To stop the server, focus the terminal window you launched the development server with and press <kbd>Ctrl</kbd> + <kbd>c</kbd>. (You may see some errors from npm when the server closes, but these are safe to ignore.)

## Making changes

It's best to first create a local [git branch](https://www.git-tower.com/learn/git/ebook/en/command-line/branching-merging/working-with-branches#start) to work in. This keeps your changes separate to the main develop branch where Genesis development occurs, and makes it easier for reviewers to see what you're proposing to alter.

<p class="notice-small">
<strong>If you intend to make extensive changes</strong> that are not just copy edits or styling tweaks, we recommend that you first <a href="https://github.com/studiopress/genesis/issues/new/choose">open an issue in the Genesis repository</a> with a proposal.
</p>

### To edit existing documentation

Existing documentation lives in the `genesis/docs` folder as [Markdown](https://www.markdownguide.org/getting-started/) files with the `.md` extension. Documentation files are named with numerical prefixes to match their position in the menu, using the following convention:

```
1-00-intro.md             → Top-level item, no children.
2-00-basics.md            → Top-level item, parent of 2-01.
2-01-how-genesis-works.md → Child of 2-00-basics.md.
```

You can edit these files directly, and you'll see your changes reflected straight away if the development server is still running.

If you're unsure about where a specific page's Markdown file is located, view the source of the page and look for the comment near the end:

```html
<!-- To change this content, edit ./docs/3-00-features.md -->
```

### To edit the changelog

The <a href="{{ '/changelog' | url }}">changelog page</a> is built automatically from the contents of the `genesis/docs/changelog` directory. Edit the individual file in that folder corresponding to the Genesis version you'd like to alter.

### To add new pages

Add a new page at the relevant level in the menu by following the [above naming convention](#to-edit-existing-documentation).

New Markdown files must begin with a <em>header</em> containing meta data like this:

```
---
title: Contribute to Genesis Docs
menuTitle: Genesis Docs
layout: layouts/base.njk
permalink: contribute/genesis-docs/index.html
tags: docs
---
```

All documentation must include the docs tag, and you should update the permalink depending on what URL you want the finished document to appear at, and the title and menuTitle to reflect the document page h1 and its name in the menu.

To specify that a feature requires a version of WordPress or Genesis, use the `minVersion` meta in the Markdown file heading:

```
---
[rest of header removed]
minVersion: Genesis 2.7.0+
---
```

Or:

```
---
[rest of header removed]
minVersion: Genesis 2.7.0+ and WordPress 4.9.6+
---
```

### To remove pages

If you remove a page, you will need to stop the development server and perform the following actions before you see your changes reflected:

1. Type `npm run docs:clean` to remove the `genesis/docs/_site` folder where documentation is built and served from.
2. Type `npm run docs:css` to build the initial CSS.
3. Type `npm run docs:dev` to build the documentation and start the development server again.

### Formatting links and other content

#### To add images

1. Compress your image using a service such as [TinyPNG](https://tinypng.com/).
2. Copy your image to the `genesis/docs/img` directory.
3. Reference your image in the documentation using this syntax:

{% raw %}
```
<img src="{{ '/img/your-filename.png' | url }}" alt="">
```
{% endraw %}

Complete the alt tag attribute if the image adds important information a partially sighted visitor would benefit from, otherwise leave the alt attribute contents blank. Do not remove the alt attribute.

#### Internal links

You must use the following format for links to other Genesis documentation pages:

{% raw %}
```
<a href="{{ '/page' | url }}">the page</a>
```
{% endraw %}

Passing root-relative URLs through the [Eleventy url filter](https://www.11ty.io/docs/filters/url/) in this way ensures that internal links gain an additional '/genesis/' prefix when the site is built for hosting on GitHub Pages at `https://studiopress.github.io/genesis/`, while continuing to work when you run the local development server (which doesn't have the `/genesis/` prefix).

Note that every heading in documentation pages gets its own ID attribute, so you can link directly to headings with a fragment identifier:

{% raw %}
```
<a href="{{ '/page/#the-title' | url }}">the title</a>
```
{% endraw %}

<p class="notice-small">
Markdown files are pre-processed with the <a href="https://github.com/Shopify/liquid/wiki/Liquid-for-Designers">Liquid templating system</a>, so you can use <a href="https://www.11ty.io/docs/languages/liquid/#supported-features">other Liquid tags</a> too.
</p>

#### External links

External links can use the Markdown format:

```
[WP Engine](https://wpengine.com/)
```

Or regular HTML:

```html
<a href="https://wpengine.com/">WP Engine</a>
```

#### Notices

There are three notice paragraph styles you can use:

```html
<p class="notice">
A regular notice with the <code>notice</code> class.
</p>

<p class="notice-big">
A bigger notice with the <code>notice-big</code> class.
</p>

<p class="notice-small">
A small notice with the <code>notice-small</code> class.
</p>
```

Which produces this styling:

<p class="notice">
A regular notice with the <code>notice</code> class.
</p>

<p class="notice-big">
A bigger notice with the <code>notice-big</code> class.
</p>

<p class="notice-small">
A small notice with the <code>notice-small</code> class.
</p>

Note that notices must use HTML inside. Markdown will not be processed.

#### Buttons

Buttons are styled as plain HTML links with a `button` class:

```html
<a href="https://www.studiopress.com/" class="button">Visit StudioPress</a>
```

Resulting in this:

<a href="https://www.studiopress.com/" class="button">Visit StudioPress</a>

#### Source code

Wrap source code blocks with three backticks and the language name, like this for PHP:

<pre>
```php
/**
 * PHP code
 *
 * This is a comment.
 * It spans multiple lines.
 */
function php_test( $test ) {
    var_dump( $test );
}
```
</pre>

And this for JavaScript:

<pre>
```js
function jsTest(test) {
    console.log('This is a test.');
}
```
</pre>

Which results in this: 

```php
/**
 * PHP code
 *
 * This is a comment.
 * It spans multiple lines.
 */
function php_test( $test ) {
    var_dump( $test );
}
```

```js
function jsTest(test) {
    console.log('This is a test.');
}
```

Code on a single line or within a sentence should be wrapped in single backticks:

```
The `var_dump()` function is not a replacement for Xdebug.
```

Which produces:

The `var_dump()` function is not a replacement for Xdebug.

Code syntax highlighting is prerendered by Eleventy during documentation builds for performance, instead of depending on JavaScript in the visitor's browser.

#### Keyboard shortcuts

Wrap keyboard characters with `<kbd>` to style them as keys:

```html
<kbd>Ctrl</kbd> + <kbd>c</kbd>
```

Which produces this:

<kbd>Ctrl</kbd> + <kbd>c</kbd>

### Editing HTML templates

Templates are located in these `_includes` directories, with an `.njk` extension:

- `docs/_includes/layouts/`
- `docs/_includes/components/`

Templates use the [Nunjucks](https://mozilla.github.io/nunjucks/) JavaScript templating engine, and Eleventy includes [templating documentation here](https://www.11ty.io/docs/templates/).

### Editing styling

Find styling at `docs/sass`. This is automatically minified and built as `docs/css/style.css`. CSS source maps are generated for the development server to help you inspect elements and see which Sass file is responsible for styling.

## Contribute your changes

To contribute your documentation changes back to Genesis:

1. [Commit your changes](https://www.git-tower.com/learn/git/ebook/en/command-line/basics/basic-workflow#start) in the branch you created.
2. [Push the changes](https://www.git-tower.com/learn/git/ebook/en/command-line/remote-repositories/publish-local-branch#start) to your fork of Genesis.
3. [Open a pull request](https://github.com/studiopress/genesis/pulls) in the main Genesis repository. This is a way of asking that your changes be merged into Genesis itself.

## Building Genesis docs for production

These steps are included for completeness. You will not need to do this unless you intend to host Genesis documentation.

First, stop the documentation development server if it's running. Then:

1. From the Genesis directory, type `npm install` to install dependencies if you have not done this already.
2. Type `npm run docs` to build docs in the `genesis/docs/_site` directory.
3. Copy the contents of the `genesis/docs/_site` directory to the root of your server, or to the gh-pages branch of your repository if using GitHub Pages.

<p class="notice-small">
If docs will not be hosted in a <code>/genesis/</code> subdirectory such as <a href="https://studiopress.github.io/genesis/">https://studiopress.github.io/genesis/</a>, you will need to alter or remove the <code>--pathprefix=</code> flag in the <code>docs:eleventy</code> script in <code>genesis/package.json</code>.
</p>
