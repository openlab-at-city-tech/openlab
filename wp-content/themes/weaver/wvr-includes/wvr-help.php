<?php
/*
    ========================= Weaver Admin Tab - Help ==============
*/
function weaver_help_admin() {
    /* admin tab for help */
    $tdir = get_template_directory_uri();

    $readme = $tdir."/help.html";

?>
    <h3>Help</h3>
    <p>You will notice
    <?php weaver_help_link('help.html#top', __('Weaver Help',WEAVER_TRANSADMIN)); ?>
    next to many options and option sections on the Weaver Admin tabs. Clicking the ? will open the Weaver Help
    file to the appropriate place.</p>

    <p>More help is available at the <a href="http://weavertheme.com" target="_blank"><strong>Weaver Theme web site</strong></a>, which includes
    a support forum.</p>

    <h4>Weaver Help</h4>
    <?php
    echo("<div class=\"wvr-help\"><h2><b><a href=\"$readme\" target=\"_blank\">Weaver Theme Documentation</a></b> -- Version 2.2</h2>
<h4><a href=\"$readme#release_notes_2_2\" title=\"Important notes about Version 2.2 \" target=\"_blank\">See Weaver 2.2 Release Notes</a>
</h4>
<h3>Table of Contents</h3>
<ul>
  <li><a href=\"$readme#get_started\" target=\"_blank\">How to get started</a></li>
  <li><a href=\"$readme#DesignHints\" target=\"_blank\">Design Hints</a></li>
  <li><a href=\"$readme#homepage\" target=\"_blank\">All About Your Site's Home Page</a></li>
  <li><a href=\"$readme#PredefinedThemes\" target=\"_blank\">Weaver Predefined Sub-Themes</a></li>
  <li><a href=\"$readme#MainOptions\" target=\"_blank\">Weaver Main Options</a>
  <ul>
  <li><a href=\"$readme#GenApp\" target=\"_blank\">General Appearance</a></li>
  <li><a href=\"$readme#HeaderOpt\" target=\"_blank\">Header Options</a></li>
  <li><a href=\"$readme#FooterOpt\" target=\"_blank\">Footer Options</a></li>
  <li><a href=\"$readme#ContentAreas\" target=\"_blank\">Content Areas</a></li>
  <li><a href=\"$readme#PPSpecifics\" target=\"_blank\">Post Page Specifics</a></li>
  <ul>
  <li><a href=\"$readme#FeaturedImage\" target=\"_blank\">The Featured Image</a></li>
  </ul>
  <li><a href=\"$readme#WidgetAreas\" target=\"_blank\">Widget Areas</a></li>
  </ul>
  </li>
    <li><a href=\"$readme#AdvancedOptions\" target=\"_blank\">Weaver Advanced Options</a></li>
    <li><a href=\"$readme#SaveRestore\" target=\"_blank\">Save/Restore Themes</a></li>
    <li><a href=\"$readme#PageTemplates\" target=\"_blank\">Weaver Page Templates</a></li>
  <ul>
  <li><a href=\"$readme#PerPostTemplate\" target=\"_blank\">Settings for \"Page with Posts\" Template</a></li>
  </ul>
  <li><a href=\"$readme#editstyling\" target=\"_blank\">Post/Page Editor Styling</a>
  <li><a href=\"$readme#PerPage\" target=\"_blank\">Per Page and Per Post Options</a>
      <ul>
        <li><a href=\"$readme#gallerypost\" target=\"_blank\">Gallery Post Format</a></li>
        <li><a href=\"$readme#shortcodes\" target=\"_blank\">Weaver Shortcodes</a></li>
        </ul>
  <li><a href=\"$readme#CustomMenus\" target=\"_blank\">Custom Menus</a></li>
  <li><a href=\"$readme#plugins\">Built-in Support for Other Plugins</a></li>
  <li><a href=\"$readme#ie\">Internet Explorer Compatibility</a></li>
  <li><a href=\"$readme#language_support\" target=\"_blank\">Using Weaver in your language</a></li>
  <li><a href=\"$readme#File_access_plugin\"><strong>Weaver File Access Plugin</strong></a></li>

  <li><a href=\"$readme#TechNotes\" target=\"_blank\">Technical Notes</a></li>
  <li><a href=\"$readme#divhierarchy\" target=\"_blank\">Weaver HTML&lt;div&gt;Hierarchy</a></li>

  <li><a href=\"$readme#v2ReleaseNotes\" target=\"_blank\">Version 2.0 Release Notes</a></li>
</ul></div>\n");
  ?>
        <h4>Weaver Main Options Summary</h4>
        <p>Click the thumbnail to view an image that summarizes many of Weaver's options. There are also many other options that
        are not displayed on this chart.</p>
    <p ><a href="<?php echo $tdir;?>/images/ttw-options.png" target="_blank"><img src="<?php echo $tdir;?>/images/ttw-options-thumb.png" /></a></p>
    <?php
    weaver_show_bullets();
    weaver_show_versions();
    echo("<hr>\n");
}

function weaver_show_versions() {
    printf("<h4>" . get_current_theme() . " Version Information</h4>\n");
    printf('<div class="wvr-help"><ul style="list-style-type=square !important">' . "\n");
    printf('<li>' . __('You are using %s.',WEAVER_TRANS) . "</li>\n", WEAVER_THEMEVERSION);
    if (defined('WEAVER_PLUS_VERSION')) {
        printf('<li>' . __('You are using %s.',WEAVER_TRANS) . "</li>\n", 'Weaver Plus ' . WEAVER_PLUS_VERSION);
    } else {
            printf('<li>' . __('You are not using %s.',WEAVER_TRANS) . "</li>\n", 'Weaver Plus');
    }
    if (function_exists('weaver_fileio_plugin')) {
        printf('<li>' . __('You are using %s.',WEAVER_TRANS) . "</li>\n", 'Weaver File Access Plugin');
    } else if (weaver_f_file_access_available()) {
        printf('<li>' . __('You are using %s.',WEAVER_TRANS) . "</li>\n", 'WordPress WP_Filesystem');
    } else {
        printf('<li>' . __('You are not using %s.',WEAVER_TRANS) . "</li>\n", 'File Access');
    }
    printf('<li>' . __('You are using <span class="b">WordPress %s</span>.')  . "</li>\n", $GLOBALS['wp_version'] );

    printf('<li>' . __('You are using %s.',WEAVER_TRANS) . "</li>\n", 'PHP ' . PHP_VERSION);


    printf("</ul></div>\n");
}

function weaver_show_bullets()
{
    $where = get_template_directory_uri() . '/images/bullets/';

    printf("<h4>Widget List Bullet Examples (only black shown)</h4>\n");

printf('| <img src="%s" /> - %s ',$where.'arrow1-black.gif','arrow1');
printf('| <img src="%s" /> - %s ',$where.'arrow2-black.gif','arrow2');
printf('| <img src="%s" /> - %s ',$where.'arrow3-black.gif','arrow3');
printf('| <img src="%s" /> - %s ',$where.'arrow4-black.gif','arrow4');
printf('| <img src="%s" /> - %s ',$where.'arrow5-black.gif','arrow5'); echo("|<br />\n");
printf('| <img src="%s" /> - %s ',$where.'check1-black.gif','check1');
printf('| <img src="%s" /> - %s ',$where.'circle1-black.gif','circle1');
printf('| <img src="%s" /> - %s ',$where.'circle2-black.gif','circle2');
printf('| <img src="%s" /> - %s ',$where.'diamond1-black.gif','diamond1');
printf('| <img src="%s" /> - %s ',$where.'diamond2-black.gif','diamond2'); echo("|<br />\n");
printf('| <img src="%s" /> - %s ',$where.'plus1-black.gif','plus1');
printf('| <img src="%s" /> - %s ',$where.'square1-black.gif','square1');
printf('| <img src="%s" /> - %s ',$where.'square2-black.gif','square2');
printf('| <img src="%s" /> - %s ',$where.'square3-black.gif','square3'); echo("|<br />\n");
printf('| <img src="%s" /> - %s ',$where.'star1-black.gif','star1');
printf('| <img src="%s" /> - %s ',$where.'star2-black.gif','star2');
printf('| <img src="%s" /> - %s ',$where.'star3-black.gif','star3'); echo("|<br />\n");

}

/*
    ========================= Weaver Admin Tab - Snippets ==============
*/
function weaver_snippets_admin() {
    /* admin tab for snippets */

    $tdir = get_template_directory_uri();
?>

    <h3>CSS Snippets for Weaver</h3>
    <p style="padding-left:120px;font-size:200%;font-weight:bold;">
        <a href="<?php echo $tdir;?>/snippets.html" target="_blank">View Snippets</a></p>
    <p>While most style elements on your site can be controlled using the regular options, sometimes you can fine
    tune your look by adding new CSS rules to the &lt;HEAD&gt; Section on the Advanced Options tab. The Weaver
    <a href="<?php echo $tdir;?>/snippets.html" target="_blank">Snippets</a> help file contains
    quite a few CSS snippets that can be used and modified for your site.</p>


    <?php
}

/*
    ========================= Weaver Admin Tab - CSS Help ==============
*/
function weaver_csshelp_admin() {
    /* admin tab csshelp */
    $tdir = get_template_directory_uri();
?>

    <h3>CSS Help</h3>
    <p style="padding-left:120px;font-size:200%;font-weight:bold;">
        <a href="<?php echo $tdir;?>/css-help.html" target="_blank">View CSS Help</a></p>
     <p>The <a href="<?php echo $tdir;?>/css-help.html" target="_blank">View CSS Help</a> file contains a short
     tutorial on CSS. It is a great place to get started learning a bit of CSS that can help you add style
     to your site.
    <?php
}

?>
