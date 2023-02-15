# BU Learning Blocks

[![Maintainability](https://api.codeclimate.com/v1/badges/b920b4489aa4ded7bd77/maintainability)](https://codeclimate.com/github/bu-ist/bu-learning-blocks/maintainability)

## About

BU Learning Blocks (BULB) is a collection of Gutenberg blocks and WordPress Custom Post Types that enable the easy creation of academic lessons. With BULB you can facilitate online learning by embedding self-assessment questions directly into your lesson. Creating and publishing a BULB Lesson is no different than creating a standard WordPress Page. The plugin provides two key capabilities that are not provided by WordPress:

- A set of blocks that help you add different types of self-assessment questions
- A way to order and navigate multiple Lesson Pages in a specific sequence

BULB is not a Learning Management System (LMS) and, currently, does not have typical LMS features such as scoring or timers. The objective of BULB is to improve learning and retention through in-line questions that reinforce the subject matter and allow students to test their understanding directly within the Lesson Page.

BULB questions are inserted into the lesson content through the placement of blocks into the page editor. The questions are added and articulated in the WordPress block editor and are saved within the Lesson Page content. BULB does not add any tables to the WordPress database.

BULB is compatible with WordPress 5.3.2 and above and the Gutenberg editor must be enabled.

Additional documentation is available in the [BULB user guide](https://developer.bu.edu/bulb/).

## Installing and activating

BULB can be installed and activated like any other WordPress plugin.  

When activated, BU Learning Blocks presents a choice to install only the question blocks or both the question blocks and the BULB custom post type. BULB Question Blocks can be used on WordPress Posts or Pages, and on BULB Lesson Pages. If you wish to use the question blocks in your site content, but do not wish to create BULB Lessons, select “Install Blocks Only”.

The plugin can be activated and deactivated, no custom posts will be deleted.  Deleting the plugin will cause all of the custom post type data to be deleted as well.

## Developing with BULB

To get started developing the plugin, clone this repo and run `npm install`.

To compile working changes run `npm start`.  This will start the [wp-scripts](https://www.npmjs.com/package/@wordpress/scripts) based development toolchain.

This plugin also includes the [wp-env local development setup](https://www.npmjs.com/package/@wordpress/env).

If you have Docker installed, you can start a local WordPress Docker container to test the plugin.  Run this:

```bash
npm run wp-env start
```

to initialize a local wp site container.  

After starting, a new WordPress site with the plugin installed will be available at http://localhost:8888/

## BU React Questions

BULB uses [BU React Questions](https://www.npmjs.com/package/@bostonuniversity/react-questions) to render the interactive question blocks.  Changes to the front-end rendering of interactive questions must be made there.

BU React Questions is also a standalone package that can be used independently of BULB.

## BU Navigation

BULB uses the [core components from the BU Navigation plugin](https://github.com/bu-ist/bu-navigation-core-widget) to provide the lesson page navigation widget.  This package is installed through composer, and committed to the repo for ease of deployment.
