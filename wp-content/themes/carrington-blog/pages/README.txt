## pages/

WP doesn't support page templates in nested folders like this one, so page organization isn't as clean as we'd like.

Create a page in the theme root:

File name: page-example.php

**Note:** We recommend prefixing all of your page files with 'page-' so that they are easily sorted together in your theme directory.

File contents:

	<?php
	
	/*
	Template Name: Example Template
	*/
	
	if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }
	if (CFCT_DEBUG) { cfct_banner(__FILE__); }
	
	cfct_page('example');
	
	?>

This will then load 'example.php' from the _pages/_ directory, keeping all of your actual page code nicely organized in one spot.

You can also add your page code to the page-example.php file you create in the theme root, but we're hoping to get support for pages in a sub-directory in a future version of WordPress so we are starting with what we consider to be a "proper" organization structure now.


### Supported Filenames

- pages-default.php (or default.php)


### File Descriptions

A "default" template is required. You can create other templates as desired.

