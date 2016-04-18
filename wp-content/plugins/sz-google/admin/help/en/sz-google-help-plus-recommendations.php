<?php

/**
 * This file contains information related to a help section 
 * of the plugin. Each directory is a specific language
 *
 * @package SZGoogle
 * @subpackage Admin
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die(); 

// Variable definition HTML for the preparation of the
// string which contains the documentation of this feature

$HTML = <<<EOD

<h2>Description</h2>

<p>With this feature you can place on its website a widget that displays the recommendations related to the pages of your website 
based on social iterations. This feature will be displayed only on the mobile version of the web site and ignored on different 
devices. To enable this option, you must select the specific field that you find the admin panel but you also need to perform 
operations on google+ page connected to your site. <a target="_blank" href="https://developers.google.com/+/web/recommendations/?hl=it">Content recommendations for mobile websites</a>.</p>

<h2>Configuration</h2>

<p>In the settings section of the Google+ page you can control the behavior of the widget that relates to the advice and the display 
mode. So do not try to change these settings in the options but use the plugin configuration page directly on google plus.</p>

<p><b>The following options are available from the settings page:</b></p>

<ul>
<li>Turn on or off recommendations.</li>
<li>Choose pages or paths which should not show recommendations.</li>
<li>Choose pages or paths to prevent from displaying in the recommendations bar.</li>
</ul>

<p><b>Choose when to show the recommendations bar:</b></p>

<ul>
<li>When the user scrolls up.</li>
<li>When the user scrolls past an element with a specified ID.</li>
<li>When the user scrolls past an element that matches a DOM query selector.</li>
</ul>

<h2>Warnings</h2>

<p>The plugin <b>SZ-Google</b> has been developed with a technique of loading individual modules to optimize overall performance, 
so before you use a shortcode, a widget, or a PHP function you should check that the module general and the specific option appears 
enabled via the field dedicated option that you find in the admin panel.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ recommendations','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));