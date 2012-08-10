<?php
define ('WEAVER_START_THEME','Wheat');
define ('WEAVER_TRANS','weaver');
define ('WEAVER_TRANSADMIN','weaver-admin');
define ('WEAVER_DEFAULT_COLOR','default-style-color');

/* need to set these for each indivitual distribution */

define ('WEAVER_VERSION','2.2.9');
define ('WEAVER_VERSION_ID',220);

define ('WEAVER_THEMENAME', 'Weaver');

define ('WEAVER_THEMEVERSION',WEAVER_THEMENAME . ' ' . WEAVER_VERSION);
/* special case definitions */

/* MULTI-SITE Control
  All non-checkbox options for this theme are filtered based on the 'unfiltered_html' capability,
  so non-admins and non-editors can only add safe html to the various options. It should be
  farily safe to leave all theme options available on your Multi-site installation. If you want
  to eliminate most of the options that let users enter HTML, then set this option to false.
*/

define('WEAVER_MULTISITE_ALLOPTIONS', true);  // Set to true to allow all options for users on multisite
?>
