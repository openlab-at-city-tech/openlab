=== MapPress Maps for WordPress ===
Contributors: chrisvrichardson
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4339298
Tags: maps, google maps, map, map markers, google map, leaflet maps, leaflet map plugin, google maps plugin, gpx, wp google maps, wp google map, map plugin, store locator, google map plugin, map widget,
Requires at least: 5.9.5
Requires PHP: 7.0
Tested up to: 6.4
Stable tag: 2.88.13

== Description ==
MapPress is the easiest way to add beautiful interactive Google and Leaflet maps to WordPress.

Create **unlimited maps and markers** using Gutenberg blocks or the classic editor.  The popup map editor makes creating and editing maps easy!

Upgrade to [MapPress Pro](https://mappresspro.com/mappress) for even more features, including custom icons, search and filter, clustering, and much more.  See it in action on the [MapPress Home Page](https://mappresspro.com/mappress) or test it yourself with a [Free Demo Site](https://mappresspro.com/demo)!

[Home Page](https://mappresspro.com/mappress)
[What's New](https://mappresspro.com/whats-new)
[Documentation](https://mappresspro.com/mappress-documentation)
[FAQ](https://mappresspro.com/mappress-faq)
[Support](https://mappresspro.com/mappress-faq)

== Screenshots ==
1. MapPress settings page
2. Map Library in Gutenberg
3. Creating a map
4. Creating a mashup

= Key Features =
* Best Google Maps plugin for WordPress
* Unlimited maps and markers
* Leaflet maps, no API key needed
* Gutenberg editor map blocks
* Classic editor support
* Styled maps
* Marker clustering
* Add maps to any post, page or custom post type
* Responsive maps
* Size maps by pixels, percent or viewport
* Popups with custom text, photos, images, and links
* Google overlays for traffic, bicycling and transit
* Directions from Google Maps
* Geocoders from Google, Nominatim, and Mapbox
* KML map overlays
* GPX tracks
* Draw polygons, circles, and lines
* Generate maps using PHP
* WPML compatible
* MultiSite compatible

= Pro Version Features =
* Get [MapPress Pro](https://mappresspro.com/mappress) for additional functionality
* Custom markers upload
* Marker editor with thousands of icons, shapes and colors
* Gutenberg "mashup" block for searchable maps and store locators
* Filter locations by taxonomies, tags and categories
* Map widget and mashup widget
* Customizable templates for markers and lists
* Generate maps automatically from custom fields
* Assign marker icons by taxonomy, tag, or category
* Advanced Custom Fields (ACF) integration

= Localization =
Please [Contact me](https://mappresspro.com/contact) to provide a translation.  Many thanks to all the folks who have created and udpated translations.

== Installation ==

See full [installation intructions and Documentation](https://mappresspro.com/mappress-documentation)
1. Install and activate the plugin through the 'Plugins' menu in WordPress
1. You should now see a MapPress meta box in in the 'edit posts' screen

[Home Page](https://mappresspro.com/mappress)
[Documentation](https://mappresspro.com/mappress-documentation)
[FAQ](https://mappresspro.com/mappress-faq)
[Support](https://mappresspro.com/forums)

== Frequently Asked Questions ==
Please see the plugin documentation pages:

[Home Page](https://mappresspro.com/mappress)
[Documentation](https://mappresspro.com/mappress-documentation)
[FAQ](https://mappresspro.com/mappress-faq)
[Support](https://mappresspro.com/forums)

== Upgrade ==

1. Deactivate your old MapPress version
1. Delete your old MapPress version (don't worry, the maps are saved in the database)
1. Follow the installation instructions to install the new version

== Changelog ==

= 2.88.13 =
* Fixed: POI modal not closing
* Fixed: Map settings 'filter' checkbox not working

= 2.88.12 =
* Fixed: bump WP version in readme

= 2.88.11 =
* Fixed: map picker not showing maps after saved in editor

= 2.88.10 =
* Fixed: settings error when upgrading from old version with non-array filters

= 2.88.9 =
* Fixed: notices for POIs with point as array instead of object

= 2.88.8 =
* Fixed: unable to use individual poi.data fields in popup templates

= 2.88.7 =
* Fixed: JS error when displaying some filters with checkboxes

= 2.88.6 =
* Fixed: JS error when adding new POI data fields
* Fixed: Mashup query block dropdown sometimes showed first item checked
* Fixed: Mashup block shows all locations instead of using specified query, due to sanitizing

= 2.88.5 =
* Added: parameter 'name' (map name) now included in filter 'mappress_filter_values'
* Fixed: single quotes incorrectly escaped in POI templates
* Fixed: shortcode attributes allowed single quotes

= 2.88.4 =
* Added: show invalid address when geocoding from settings screen
* Fixed: template editor reversed label and token name
* Fixed: overflow of map container inside iframe
* Fixed: with left/right thumbnail, POI content truncated when no thumbnail present
* Fixed: bulk geocoding should be limited to the selected post types in the 'geocoding' settings section
* Fixed: suppress the geocoding errors section when ACF is present, since it interferes with the mappress_error custom field

= 2.88.3 =
* Fixed: map doesn't display when geolocation centering 

= 2.88.2 =
* Fixed: WP dialog changes cause issues with intro guide

= 2.88.1 = 
* Fixed: free version error with filter class

= 2.88 =
* Added: initial search can now be specified by URL parameter (enter parameter name in settings)
* Added: LocationIQ geocoder
* Changed: removed wp element dependency

= 2.87.5 =
* Fixed: sort by POIs by title not working when map center is defined only by bounds

= 2.87.4 = 
* Hotfix for bug in 2.87.3

= 2.87.3 =
* Changed: add delay for Leaflet initialopeninfo ("load" event triggers too early throwing off popup location)
* Fixed: debounce triggering on initialization

= 2.87.2 =
* Fixed: search not working for some geocoders
* Fixed: mapbox key not working if defined in wp-config.php

= 2.87.1 =
* Changed: POI modal is now displayed inside mapp-dialog component
* Changed: close 'X' added for dialog component
* Fixed: warning if all sizes deleted

= 2.87 = 
* Added: POI list can now be sorted by distance 
* Added: POI distance can be included templates for popups and POI list
* Added: enable custom map tiles for Leaflet
* Changed: removed dependency on underscore library
* Fixed: web component too small when displayed in iframe
* Fixed: POI drag and drop sorting not working in map editor
* Fixed: Google warning for deprecated bounds in places calls
* Fixed: console warning when mousing over user location blue dot

= 2.86.15 =
* Fixed: ACF mashup not working with custom meta query

= 2.86.14 =
* Fixed: mapbox custom styles not saving

= 2.86.13 =
* Changed: resurrect html/visual tabs for poi editor

= 2.86.12 =
* Fixed: don't display fullscreen control on unsupported (iOS) devices
* Fixed: warning when map center is a string

= 2.86.11 = 
* Fixed: PHP 8.2 warning message for trim()

= 2.86.10 =
* Fixed: warning message with PHP 8.1 and 8.2
* Fixed: typo in wp-config setting for mapbox token
* Fixed: compatibility fix for latest Gutenberg modal

= 2.86.9 =
* Changed: switched deregister to options
* Fixed: map picker not working if latest Gutenberg plugin is active
* Fixed: fix missing user maps caused by WP 6.0+ user query change

= 2.86.8 =
* Fixed: Leaflet GPX files not centering properly

= 2.86.7 =
* Fixed: gutenberg plugin interferes with modal editor display
* Fixed: Leaflet can now load GPX files (and so can Google).  Just enter the URL in the map editor's search bar.

= 2.86.6 =
* Fixed: missing add new button in standalone map library

= 2.86.5 =
* Fixed: when pois are clicked inside iframe, open in the parent window instead of the iframe
* Fixed: missing alt tags on some images

= 2.86.4 =
* Fixed: new filters defaulting to single checkbox instead of multiple checkboxes
* Fixed: filters CSS position wrong when rendering in web component

= 2.86.3 =
* Fixed: filters deleted on save

= 2.86.2 =
* Fixed: syntax error

= 2.86.1 =
* Fixed: media uploader not opening above map editor dialog
* Fixed: link inserter not opening above poi editor dialog
* Changed: improved updater performance

= 2.86 =
* Added: 'POI Fields' setting: enables POI field data entry, filtering and display
* Added: filtering for individual maps based on POI data fields
* Added: option for setting search placeholder
* Added: setting to render maps as web components
* Added: compatibility with React 18 (faster rendering, etc.)
* Added: fullscreen control for Leaflet and Google
* Added: search for maps by multiple keywords
* Changed: replaced Gutenberg dialog component with standard html dialog element
* Changed: removed locutus
* Changed: added poi field data to default map popup template
* Changed: removed 'classname' and 'embed' map attributes
* Fixed: dragging not working for polygons
* Fixed: mashup filters not retaining checked selections
* Fixed: it was possible to save new maps multiple times
* Fixed: map list scrolling to bottom after move to trash (to focus on snackbar)
* Fixed: complianz blocking Leaflet maps if marker clustering is disabled

= 2.85.9 =
* Fixed: "geocode users" button hidden in settings

= 2.85.8 =
* Fixed: error when loading custom mappress.css inside iframes
* Fixed: WPML language switcher delayed for mashup queries

= 2.85.7 =
* Fixed: error if tinyMCE is disabled in user profile

= 2.85.6 =
* Fixed: trailing comma in API affects early PHP 7.x

= 2.85.5 =
* Changed: PHP version bumped to 7.0
* Fixed: PHP error in 7.2 with trailing commas
* Fixed: additional sanitization for arguments in rest API
* Fixed: sizes settings defaults to first size

= 2.85.4 =
* Fixed: translations not working

= 2.85.3 =
* Fixed: editor centering on POI after edits

= 2.85.2 =
* Fixed: drag/drop not working for options lists
* Fixed: whitespace trimmed from sizes
* Fixed: Gutenberg plugin makes map editor too small
* Fixed: error on settings screen for ACF help field

= 2.85.1 =
* Fixed: lat/lng editing not working for leaflet

= 2.85 =
* Added: New settings screen and index sidebar
* Added: updated REST API to include schema
* Added: tabbed POI editor with lat/lng and address fields
* Added: setting to limit POI list to viewport without search
* Changed: updated controls for color picker, style picker, filters, and poi editor
* Changed: new combobox for field mapping settings
* Fixed: Google API callback parameter is now mandatory
* Fixed: shortcode geolocate parameter ignored
* Fixed: drag and drop improved for both settings and POIs
* Fixed: error when setting map center before it has been moved
* Fixed: editor zooming in on polygons and shapes
* Fixed: some strings not included in translation POT file

= 2.84.22 =
* Fixed: missing pagination text in POT file
* Changed: improved processing for empty ACF map fields

= 2.84.21 =
* Changed: update version compatibility
* Changed: add alt tags to template icons

= 2.84.20 =
* Fixed: sanitize map name in iframes

= 2.84.19 =
* Fixed: geolocate parameter not passed through from shortcode

= 2.84.18 =
* Fixed: unable to save settings when sizes are numeric

= 2.84.17 =
* Fixed: mashup query bug in 2.84.16

= 2.84.16 =
* Added: German translation
* Changed: internal changes to settings screen
* Fixed: directions not working for lat/lng POIs

= 2.84.15 =
* Added: support for hyphenated poi.props variables
* Changed: parse shortcodes in poi body (frontend only)
* Changed: fix for WP async image bug is now applied only for WP version < 6.1.1
* Fixed: directions tab blocked by popup blocker

= 2.84.14 =
* Fixed: directions not rendering properly when POI list is disabled
* Fixed: popup not always centering when canvas is resized
* Fixed: directions CSS made form too small

= 2.84.13 =
* Fixed: temporary fix for WordPress 6.1 async image issue: https://core.trac.wordpress.org/ticket/56969.  Fix prevents modifying image URLs.

= 2.84.12 =
* Fixed: readme changelog not showing current version
* Fixed: script error when using Complianz + Leaflet + marker clustering

= 2.84.11 =
* Fixed: for GDPR, default "red-dot" icon now loaded from plugin directory
* Changed: added partial pl_PL translation

= 2.84.10 =
* Fixed: POI hover effect not triggering if POI isn't opened on hover

= 2.84.9 =
* Added: local leaflet libraries for GDPR
* Changed: removed obsolete translation files

= 2.84.8 =
* Fixed: complianz not working

= 2.84.7 =
* Fixed: patch in 2.84.6 caused geocoding to fail when adding markers and opening popups
* Fixed: JavaScript not executing inside popup templates

= 2.84.6 =
* Fixed: error when manually centering some maps, in toJSON() method

= 2.84.5 =
* Fixed: translations loading from plugin directory

= 2.84.4 =
* Added: maps GDPR compliance using the 'Complianz' plugin
* Changed: renamed 'iframes' setting to 'compatibility mode'
* Changed: iframes forced when Jetpack infinite scroll active
* Fixed: mashup query not filtering POIs when run in iframe

= 2.84.3 =
* Added: new Google marker clusterer (https://github.com/googlemaps/js-markerclusterer)
* Added: setting to geolocate on user when map is first displayed
* Changed: better initial centering when poiZoom is set
* Changed: updates to welcome guide and deactvation menu
* Fixed: KML files not centering when added in editor
* Fixed: KML error when using Leaflet
* Fixed: initial centering when geolocating and browser geolocation is disabled

= 2.84.2 =
* Fixed: map sizing incorrectly when using inline list in iframe

= 2.84.1 =
* Added: Google AMP compatibility
* Added: better help text for the "poiZoom" setting
* Changed: revert iframes to template_redirect
* Changed: removed CSS centering for popup texts
* Fixed: templates and scripts loaded on the main page when iframes active

= 2.84 =
* Added: new map editor
* Fixed: Google sheet upload error
* Fixed: map styles search not working if enter key pressed

= 2.83.23 =
* Fixed: database upgrade running for new installs
* Fixed: option setting to initially close sidebar is ignored

= 2.83.22 =
* Changed: allow popup to size larger when thumbnails are set to top, but no image is present
* Fixed: missing scrollbars when popup content is large
* Fixed: warning if default size selected in settings is invalid

= 2.83.21 =
* Fixed: typo in setting 'showCoverageOnHover'

= 2.83.20 =
* Fixed: SVN publish

= 2.83.19 =
* Fixed: remove generated iframe from build

= 2.83.18 =
* Changed: enabled 'check now' button even when license is active

= 2.83.17 =
* Fixed: mini map class not being applied to small maps
* Fixed: other plugins break iframes by adding 'defer' to script tags

= 2.83.16 =
* Changed: prevent WP from overwriting Pro with free version

= 2.83.15 =
* Fixed: setting initialopeninfo with no map POIs causes JS error

= 2.83.14 =
* Fixed: console warnings in Google marker clusterer from deprecated google.maps.addDomEventListener
* Fixed: iframe not resizing when height is 'vh'

= 2.83.13 =
* Added: setting to allow mashup thumbnail images to come from either post or POI (mashupThumbs)
* Added: fast iframes, and iframes that resize to inline (bottom) POI list layout
* Changed: popups opened by marker hover now close after a short delay when mouse is moved away
* Fixed: POI list not scrolling to top on page change

= 2.83.12 =
* Changed: Google now returns viewport for street addresses, so poiZoom (default zoom) setting applies even if viewport is present
* Fixed: map loses attachment if attached and then immediately edited

= 2.83.11 =
* Fixed: syntax error in API for old versions of PHP

= 2.83.10 =
* Changed: map minimum width changed from 250 to 200px
* Changed: template editor split to separate module
* Changed: post attachment control updated
* Changed: REST API code added
* Fixed: hideEmpty mashup parameter not compatible with new query functions

= 2.83.9 =
* Changed: importer updated to allow upper-case column names
* Changed: updated authors in mashup block to reflect new core data
* Fixed: focus incorrect when creating new map and selecting title
* Fixed: map not linked to post when creating new post
* Fixed: refresh query button not working in mashup block

= 2.83.8 =
* Fixed: mashup block shortcode viewer removed
* Fixed: importer sample map selecting all POIs at once

= 2.83.7 =
* Fixed: republish 2.83.6 changes

= 2.83.6 =
* Fixed: map iframe interfering with theme customizer

= 2.83.5 =
* Changed: workaround for other plugins loading obsolete versions of wp.element
* Fixed: clicking on mashup thumbnail image not opening underlying post

= 2.83.4 =
* Fixed: updated German translation
* Fixed: double markers showing when using multiple maps with Leaflet clustering

= 2.83.3 =
* Fixed: POI list pagination incorrect

= 2.83.2 =
* Fixed: drag and drop error with Leaflet polyfill

= 2.83.1 =
* Fixed: error from Leaflet json polyfill when theme overwrites Leaflet

= 2.83 =
* Added: setting to disable Leaflet cluster outline polygons
* Changed: editor maps switched to react
* Fixed: directions not working for POIs with no address

= 2.82.4 =
* Fixed: directions link not working

= 2.82.3 =
* Fixed: markers shown outside clusters on initial load
* Fixed: editor marker drag/drop not working

= 2.82.2 =
* Fixed: zooming in and out on Google clusters could result in 'null' marker
* Fixed: revert auto-sizing iframes; not compatible with viewport ('vh') sizing

= 2.82.1 =
* Fixed: maps sized wrong when using sizes without units

= 2.82 =
* Changed: frontend loader and rendering switched to react components
* Changed: iframes resize to content

= 2.81.2 =
* Changed: convert import/settings to react map

= 2.81.1 =
* Fixed: url query parameter removed, some sites throw 403 error

= 2.81 =
* Fixed: POI list showing extra POI beyond page size
* Fixed: Map editor page size should not be controlled by front-end settings
* Changed: begin React code transition for admin

= 2.80.11 =
* Fixed: innodb utf8mb4 index on map title limited to 191 characters

= 2.80.10 =
* Fixed: sorting not working in map list
* Fixed: save button not disabled during map save
* Fixed: trashed maps included in mashups

= 2.80.9 =
* Fixed: array not initialized for custom props

= 2.80.8 =
* Fixed: missing token description in template editor
* Fixed: multiple custom fields not pulled into templates

= 2.80.7 =
* Fixed: settings not saved in setup wizard

= 2.80.6 =
* Added: trigger DB upgrade automatically

= 2.80.5 =
* Fixed: maps not displaying when scripts output in footer

= 2.80.4 =
* Added: enabled user maps

= 2.80.3 =
* Changed: authorization 'edit_posts' is now used instead of 'manage_options' for the 'maps' menu
* Changed: thumbnail images now specify size for better popup sizing

= 2.80.2 =
* Fixed: POI list not selecting open POI
* Fixed: mashup error when debugging enabled
* Fixed: error when dismissing notices

= 2.80.1 =
* Fixed: database upgrade check incorrect

= 2.80 =
* Added: settings added for directions links in POI list
* Changed: filters output even when closed, to allow custom CSS modification
* Fixed: POIs filtered by map bounds even when search disabled

= 2.77.3 =
* Fixed: thumbnail not positioned properly in popup modal
* Fixed: template 'default' tab showing current template instead
* Fixed: POIs were being filtered by bounds even when search disabled


= 2.77.2 =
* Fixed: shapes not centering correctly when clicked
* Fixed: not possible to enable POI hover and open POIs in a new tab or modal
* Changed: added lazy loading and speed tests for iframes
* Changed: deactivation screen updated

= 2.77.1 =
* Fixed: ACF map fields not being read in mashups
* Fixed: enable beta versions checkbox not working

= 2.77 =
* Changed: source files renamed
* Fixed: show filter options without escaping

= 2.76.6 =
* Changed: updated query filters for WP 6.0
* Fixed: adjusted infowindow sizing for sub-pixel rendering

= 2.76.5 =
* Fixed: adjust webpack configuration to pick up missing translations

= 2.76.4 =
* Fixed: mashup inline list not scrolling
* Fixed: category filter include/exclude not working

= 2.76.3 =
* Fixed: mashup list pagination not working

= 2.76.2 =
* Fixed: directions link not working

= 2.76.1 =
* Fixed: syntax error in mashups
* Fixed: missing translation for pages
* Fixed: list page size not working

= 2.76 =
* Added: images can now be attached to POIs
* Added: if multiple images exist, an image gallery is displayed in the map list and popups
* Fixed: KML overlays were not displaying properly

= 2.75.6 =
* Fixed: error when dragging Leaflet markers

= 2.75.5 =
* Fixed: geocoding errors written to posts with no custom fields
* Fixed: thumbnails not displaying properly in list
* Fixed: insert not working for map sidebar panel

= 2.75.4 =
* Fixed: maps with save center not displaying

= 2.75.3 =
* Fixed: directions 'to' address blank

= 2.75.2 =
* Changed: removed unused list templates
* Fixed: missing POT translation for filter counts
* Fixed: POI popup modal not working

= 2.75.1 =
* Fixed: CSS preventing scrolling bottom POI list
* Fixed: POI list not displaying in editor if disabled in settings
* Fixed: blank map edit screen for some sites

= 2.75 =
* Changed: completed removal of obsolete Algolia geocoder
* Changed: updated JavaScript: map editor, POI editor, POI list, directions, map menu, map picker and settings
* Changed: clustering libraries sourced from CDN

= 2.74.3 =
* Fixed: removed import menu from free version
* Fixed: removed french translation from plugin directory

= 2.74.2 =
* Fixed: custom field geocoding not working

= 2.74.1 =
* Fixed: option screen alignment wrong for some options
* Fixed: travel line animation setting not saving properly

= 2.74 =
* Added: option to connect POIs with lines, for travel blogs, etc.  Lines can be enabled/disabled in the settings or with the shortcode: [mappress lines="true"]
* Added: new filters form using AJAX
* Added: import screen for importing maps from CSV files
* Changed: geocoding custom fields now use a datalist dropdown for easier entry
* Fixed: Leaflet popup not centered when POI is opened from off-screen
* Fixed: translations not available for JavaScript texts
* Fixed: directions not opening when list is below map
* Fixed: hovering highlight not removed
* Fixed: on some servers compression settings prevented AJAX calls with output buffering enabled

= 2.73.18 =
* Fixed: added back ability to programmatically specify center as array of (lat,lng)

= 2.73.17 =
* Added: KML URL is now output when there is an error loading the KML file
* Fixed: geocoder not recognizing some locations, including "lat,lng" entries

= 2.73.16 =
* Fixed: autocomplete not creating new POIs

= 2.73.15 =
* Changed: replaced JQuery Autocomplete with new search box

= 2.73.14 =
* Fixed: check for wp-config settings preventing file changes

= 2.73.13 =
* Fixed: check for wp-config settings preventing file changes

= 2.73.12 =
* Fixed: inline directions input not working

= 2.73.11 =
* Fixed: include/exclude not working for taxonomy filters

= 2.73.10 =
* Fixed: notice on widget screen
* Fixed: errors on beta theme editor screen
* Changed: Remove jQuery version check and jQuery tabs control

= 2.73.9 =
* Fixed: map doesn't display if google directions used
* Changed: filter CSS updated

= 2.73.8 =
* Fixed: allow autoptimize to process scripts
* Fixed: underscore functions and templates broken by woocommerce lodash

= 2.73.7 =
* Fixed: notice in wp_query groupby

= 2.73.6 =
* Fixed: exclude wp JS from autoptimize

= 2.73.5 =
* Fixed: error resizing maps in jQuery tabs

= 2.73.4 =
* Added: base code for mashups by users
* Fixed: maps attached to a trashed post now appear in the map library
* Fixed: template editor now inserts properly-formatted tokens for custom fields
* Fixed: mashup query filtes could interfere with queries from POI oembeds

= 2.73.3 =
* Fixed: PHP error when loading filters template

= 2.73.2 =
* Fixed: possible PHP error on settings screen
* Fixed: box-sizing added to layout CSS, directions made max width in mini view

= 2.73.1 =
* Fixed: directions not displaying

= 2.73 =
* Important: filters CSS has been updated, please update any custom filter forms to match
* Added: better popup panning and sizing
* Added: new custom JSON styles can be created in the style editor
* Added: setting for filter position (search box or POI list)
* Added: new filter editor in MapPress settings
* Added: post count in filter dropdown
* Added: new filter types: post type and text box
* Added: user-defined labels for filters
* Added: filter display formats (select/checkbox/radio)
* Added: include or exclude specific terms (tags, categories,...) for filters
* Fixed: filters size better in mini mode
* Fixed: POI body not showing in Firefox when thumbnails on left/right
* Fixed: control for attaching posts to maps now shows the correct custom post type
* Fixed: mashup block not updating when query parameters change
* Fixed: Gutenberg boolean attributes defaulting to false when converting classic blocks
* Fixed: settings screen not displaying on some wordpress hosted sites

= 2.72.5 =
* Fixed: list toggle not working

= 2.72.4 =
* Fixed: directions link not working if no POI list present

= 2.72.3 =
* Increment version

= 2.72.2 =
* Changed: allow DOM events to bubble out of the map container

= 2.72.1 =
* Fixed: POI drag and drop sorting not working in editor
* Fixed: shortcodes in AJAX calls now include scripts with map/mashup output

= 2.72 =
* Changed: mashup queries now use a single SQL statement, for hosts that limit SQL size
* Fixed: youtube videos inside popups did not play full screen
* Fixed: [mashup query="current"] now displays current posts correctly

= 2.71.1 =
* Added: option for POI list page size
* Added: option for POI list open/closed when map is loaded
* Fixed: directions not working on Android

= 2.71 =
* Added: enable search for individual maps
* Added: classic editor button updated for compatibility with Enfold theme
* Changed: remove initialOpenDirections parameter
* Changed: speed up Nominatim autocomplete
* Changed: internal updates to ES6 JS for options and maps

= 2.70.1 =
* Changed: clearer highlighting in map list
* Changed: remove beta version
* Changed: remove IE11 support

= 2.70 =
* Added: maps can now be trashed or restored