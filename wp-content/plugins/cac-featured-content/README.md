# Introduction
The CAC Featured Content Widget is a plugin developed for the [CUNY Academic Commons](http://commons.gc.cuny.edu) an academic social network powered by WordPress, BuddyPress, and MediaWiki. The widget provides several useful tools for featuring selected content, such as Featured Blogs, Featured Groups, Featured Posts, Featured Members and Featured Resources. Currently the plugin will work on both single and multisite installs, but is BuddyPress __dependent__. It will __not__ work on a non-BuddyPress install.

# Features
The five featured content types (blog, group, post, member and resource) share a lot of the code behind the scenes. Their layouts (views) and structure are all very similar. It's only the specific details about each that change. The admin interface for the plugin has been augmented with autocomplete functionality to help simplify administrative tasks. The featured blog address, featured group name, featured post name and featured member username will all provide results from your BuddyPress/MultiSite installation as you type.

If the description provided by the chosen featured content type is not suitable, for whatever reason, the plugin offers a __Custom Description__ text field to provide an alternative. The text entered in this field will override any description that was automatically parsed by the plugin while querying for your featured content type. This field also serves as the only description for the __Featured Resource__ content type.

The crop length (in characters) of the description can be controlled via the plugin's __Crop Length__ input field. Either the automatically parsed description, or the custom description text, will be cropped with ellipses appended to the end. The default is 250 characters.

The widget that's displayed to the user on the front of the site provides a link after the description to allow the visitor to view the remainder of the featured content in its full glory. The link text defaults to "Read More...", but can be customized through the __Read More Label__ field.

Because many site admins do not have access to how a theme styles the HTML output of the widgets added to a sidebar, the admin section allows you to choose what heading element will be used to wrap the widget's title. You can choose from an &lt;h1&gt; all the way down to an &lt;h6&gt;. This allows you to add the widget to any number of sidebars (or widgetized areas) in a theme that has defined different looks between different page sections.

You have almost complete control over the plugin's image handling capabilities. The __Display Images__ checkbox toggles the displaying of all images. When images are displayed they will be chosen based on the type of featured content, unless you enter a URL to a specific image in the __Image URL__ field. For groups, the image is the group’s avatar; for members their personal avatar is used; for a blog the author’s avatar is used; and for posts the image used is either the first image within the post or the avatar of the post's author. The resource type will use the URL from the __Image URL__ field to load an image from an external source. The size of the thumbnail displayed in the widget can be controlled through the __Image Width__ and __Image Height__ fields, which are both set to 50px by default.

__Note:__ *The incorporation of additional methods for including images is planned for future releases.*

# Technical Details
For the interested reader, this plugin behaves similarly to the WordPress template hierarchy or a loose interpretation of the Model-View-Controller (MVC) design paradigm. Execution begins in `cac-featured-content.php`. This is the main plugin file, containing the usual WordPress plugin comments, and begins loading the plugin by requiring `cac-featured-main.php` on the bp_include action hook. This ensures BuddyPress has been loaded prior to loading the rest of the plugin's files. `cac-featured-main.php` contains the definition of the `CAC_Featured_Content_Widget` class. This class/plugin structure is a modified version of the WordPress plugin widget boilerplate available [here](https://gist.github.com/1229641). This file also defines the `CAC_Featured_Content_View` class which will be used by `cac-featured-controller.php` to gather information specific to the type of featured content to be displayed. The `CAC_Featured_Content_Widget` class is responsible for generating the widget view in both the admin section as well as the front of your site, and handles all the database updating when an option has changed. This class is instantiated on the `widgets_init` hook during WordPress' initialization, and acts as the 'M' in MVC.

When the widget is rendered on the front of your site the `widget()` method of the `CAC_Featured_Content_Widget` class is called. This method simply delegates that job to `cac-featured-controller.php`. This file acts as the 'C' in MVC, it gathers all the details specific to the requested featured content type and stores them in an instance of the `CAC_Featured_Content_View` class mentioned above. It leans on some static methods defined in `cac-featured-helper.php` to perform some of the heavy lifting in finding specific blogs, posts and images. Once parsing of this file has completed it passes execution onto the appropriate view template, stored in the views/ subdirectory.

The 'V' in MVC comes from one of the five files in the views directory. Each file contains the appropriate PHP and HTML markup to render its specific featured content type.

The interactive features of the widget's admin interface come from a combination of the Javascript in `js/cac_featured_admin.js` and some final PHP in `cac-featured-autoomplete.php`. The autocomplete functionality comes from the four classes defined in that file. They are responsible for answering the requests that come from the ajax actions initiated in the Javascript on various change events.

## Changelog

### 1.0.4
* multisite optimizations for Featured "Post" Content type
* fixed $wpdb->prepare warning

### 1.0.3
* fixed js error that was improperly referencing widget number on new widgets

### 1.0.2
* fixed small readme error
* updated javascript to support new features
* improved multi-widget support
* fixed an autocomplete bug

### 1.0.1
* improved code to work better with 3rd party plugins (Domain Mapping)
* added the ability to choose the widget title's HTML element
* adjusted layout of some admin input elements
* added some more default styles
* updated HTML output in content type views

### 1.0.0
* separated plugin responsibilities into MVC-like structure
* rewrote readme files
* added code to handle multiste and non-multisite installs
* simplified html structure in all view files
* added autocomplete functionality
* rewrote helper class to work with new admin widget layout
* rewrote admin js file to work with new admin widget layout
* started new clean cac-featured-content plugin structure
