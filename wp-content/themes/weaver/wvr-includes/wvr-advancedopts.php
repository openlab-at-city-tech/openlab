<?php
/*
    ========================= Weaver Admin Tab - Advanced Options ==============
*/

function weaver_advanced_admin() {  ?>

<div id="tabwrap_adv" style="padding-left:5px;">
    <div id="tab-container-adv" class='yetiisub'>
	<ul id="tab-container-adv-nav" class='yetiisub'>
<?php if (weaver_allow_multisite()) { ?>
	    <li><a href="#adtab0"><?php echo(__('&lt;HEAD&gt; Section',WEAVER_TRANSADMIN)); ?></a></li>
	    <li><a href="#adtab1"><?php echo(__('HTML Insertion',WEAVER_TRANSADMIN)); ?></a></li>
<?php } ?>
	    <li><a href="#adtab2"><?php echo(__('Page Template Options',WEAVER_TRANSADMIN)); ?></a></li>
<?php if (weaver_allow_multisite()) { ?>
	    <li><a href="#adtab3"><?php echo(__('Site Options',WEAVER_TRANSADMIN)); ?></a></li>
<?php } ?>
	    <li><a href="#adtab4"><?php echo(__('Shortcodes',WEAVER_TRANSADMIN)); ?></a></li>
	    <li><a href="#adtab5"><?php echo(__('Administrative',WEAVER_TRANSADMIN)); ?></a></li>
	</ul>

        <?php  weaver_sapi_form_top('weaver_advanced_settings_group','ttw_advanced_options_form'); ?>
	    <h3>Advanced Options
<?php weaver_help_link('help.html#AdvancedOptions','Help for Advanced Options'); ?></h3>
<?php /*
	    <div style="float:right; width:33%; border:1px solid #888; padding-left:8px;"><small>Are you a professional website designer?
	    If Weaver is helping you do your job, please make a donation to this theme!</small></div> */
?>
<?php weaver_sapi_submit('saveadvanced', 'Save All Advanced Options'); ?><br /><br />

<!-- ***************************************************** -->
<div id="adtab0" class="tab_adv" >
    <?php   weaver_adv_head_section();
?>
</div> <!-- adtab 0 -->

<!-- ***************************************************** -->
<?php if (weaver_allow_multisite()) { ?>
<div id="adtab1" class="tab_adv" >
    <?php  weaver_adv_html_insert(); ?>
</div> <!-- adtab1 -->
<?php } // end of major section of not allowed on multisite ?>

<!-- ***************************************************** -->
<div id="adtab2" class="tab_adv" >
    <?php  weaver_adv_page_template(); ?>
</div> <!-- adtab2 -->

<!-- ***************************************************** -->
<?php if (weaver_allow_multisite()) : ?>
<div id="adtab3" class="tab_adv" >
    <?php weaver_adv_site_opts(); ?>
</div> <!-- site options -->
<?php endif; ?>

<!-- ***************************************************** -->
<div id="adtab4" class="tab_adv" >
    <?php weaver_adv_wvr_shortcodes(); ?>
</div> <!-- shortcodes -->

<!-- ***************************************************** -->
<div id="adtab5" class="tab_adv" >
    <?php  weaver_adv_admin_opts(); ?>
</div> <!-- admin -->

<?php weaver_sapi_form_bottom('ttw_advanced_options_form'); // can't cross divs (IE9) ?>

</div> <!-- tab-container-adv -->
</div> <!-- #tabwrap_adv-->

<script type="text/javascript">
	var tabberAdv = new Yetii({
	id: 'tab-container-adv',
	tabclass: 'tab_adv',
	persist: true
	});
</script>
<hr />
<?php
}

function weaver_adv_head_section() {
?>
<label><span style="color:blue; font-weight:bold; font-size: larger;">Introduction and &lt;HEAD&gt; Section</span></label><br />
		<p>The <b>Advanced Options</b> tab and its sub-tabs have options for more advanced control of your site.
		Many of the fields on these sub-tabs allow you to add HTML code, special CSS rules, or even JavaScripts to your site.
		These are sometimes required by third-party plugins and widgets, but more often will be used to customize
		and enhance your site's desgn. You can copy/paste/edit many of the CSS Snippets provided from the <b>Snippets</b> tab.
		You may find it helpful if you take the time to learn a bit about HTML coding to use these fields most effectively.</p>

		<p><small>The values you put here are saved in the WordPress database, and will survive theme upgrades and other changes.</small></p>

		<p><small>PLEASE NOTE: Only minimal validation is made on the field values, so be careful not to use invalid code.
                Invalid code is usually harmless, but it can make your site display incorrectly. If your site looks broken after make changes here,
                please double check that what you entered uses valid HTML or CSS rules.</small></p>
                <hr />

		 <!-- ======== -->
<?php if (weaver_allow_multisite()) { ?>
		<a name="headsection" id="headsection"></a>
		<label><span style="color:blue;font-size:110%"><b>&lt;HEAD&gt; Section</b></span></label><br/>

                This input area is one of the most powerful options available in Weaver for customizing your site.
		Code entered into this box is included right before the &lt;/HEAD&gt; tag on each page of your site. The most important
		use for this area is to enter custom CSS rules to control the look of your site. (<em>Important!</em> The styling in this
		section is treated as per-site values, and won't be included when you save a theme. Use "Add CSS Rules" option below
		to add CSS as part of theme definition.)
		<br />
		<small>Note: The <b>Snippets</b>
		tab contains many examples of CSS rules to customize your site. You can also use the <em>page-head-code</em>
		Custom Field to enter similar code on a per page basis from the "Edit Page" panel.
		This field can also be used for entering JavaScript or links to JavaScript files, or anything else that
                belongs in the &lt;HEAD&gt;. For example, Google Analytics code belongs here.</small>
		<br />

		<textarea name="<?php weaver_sapi_advanced_name('ttw_head_opts'); ?>" rows=7 style="width: 95%"><?php echo(weaver_esc_textarea(weaver_getopt('ttw_head_opts'))); ?></textarea>
		<br /><?php weaver_sapi_submit('saveadvanced', 'Save All Advanced Options'); ?><br />
		<!-- ===================================================== -->
		<hr />
		<a name="advancedcss" id="advancedcss"></a>
		<label><span style="color:#0000FF; font-weight:bold; font-size: larger;">Advanced CSS Options</span></label><br /><br />

		<!-- ======== -->
		<label><span style="color:#6666FF;"><b>Add CSS Rules to Weaver's style rules</b></span></label><br/>
		<small>Some advanced developers prefer that their custom CSS rules be added to the weaver_style.css file rather than
		included in the &lt;HEAD&gt; Section above. Any CSS style rules here will be added at the end of the regular Weaver
		CSS rules. Do not add &lt;style&gt; and &lt;/style&gt; here - just CSS. These rules <em>will</em> be saved with your theme .wvr file.</small>
		<br />
		<textarea name="<?php weaver_sapi_advanced_name('ttw_add_css'); ?>" rows=2 style="width: 95%"><?php echo(weaver_esc_textarea(weaver_getopt('ttw_add_css'))); ?></textarea>
<!-- ======== --><br />
		<label><span style="color:#6666FF;"><b>Predefined Theme CSS Rules</b></span></label><br/>
		<small>This area is mostly for backward compatibility. If a predefined sub-theme requires some CSS that
		can't be applied with a Main Options CSS rule, then that CSS will appear here. You aslo may edit it if
		needed to make your own theme work. If you are defining a theme you want to share, you should move any CSS
		definitions from the &lt;HEAD&gt; Section to here for your final version. That will leave the &lt;HEAD&gt;
		Section empty for others to add more customizations.
		This code is included before the &lt;HEAD&gt; Section code in the final HTML output file.</small>
		<br />
		<textarea name="<?php weaver_sapi_advanced_name('ttw_theme_head_opts'); ?>" rows=2 style="width: 95%"><?php echo(weaver_esc_textarea(weaver_getopt('ttw_theme_head_opts'))); ?></textarea>
<?php
    }	// not multisite
}

function weaver_adv_html_insert() {
?>

		<a name="htmlcode" id="htmlcode"></a>
		<label><span style="color:blue; font-weight:bold; font-size: larger;">HTML Code Insertion Areas</span></label><br />
		The following options allow you to insert HTML code into various regions of you page. These areas must use
		HTML markup code, and all can include <a href="http://codex.wordpress.org/Shortcode" target="_blank">WP shortcodes</a>.
		<em>Important:</em> You almost certainly will need to provide CSS styling rules for the HTML elements in
		these code blocks, either within specific tags using <em>style="..."</em> rules, or by adding CSS rules
		on the &lt;HEAD&gt; Section sub-tab. Note: these Insertion blocks are listed in the order they will be added
		to your pages, but the "Site Header Insert Code" block may be the most commonly used.
		<br /><small>Note: Providing <em>margin</em> style values, including negative values, can be used to control the spacing
		before and after these code blocks.</small>
		<br /><br />

		<!-- ======== -->

		<label><span style="color:blue;"><b>Pre-Header Code</b></span></label></br />

                This code will be inserted right before the header area (between the "wrapper" and the "header" div tags), above the
		menus and site image. This block also can be used as an alternative to the Weaver Header Widget Area.
		<br />

		<textarea name="<?php weaver_sapi_advanced_name('ttw_preheader_insert'); ?>" rows=3 style="width: 95%"><?php echo(weaver_esc_textarea(weaver_getopt('ttw_preheader_insert'))); ?></textarea>
		<br />
		<label>Hide on front page: </label><input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_hide_front_preheader'); ?>" id="ttw_hide_front_preheader" <?php checked(weaver_getopt_checked( 'ttw_hide_front_preheader' )); ?> />
		<small>If you check this box, then the code from this area will not be displayed on the front (home) page.</small><br />
		<label>Hide on non-front pages: </label><input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_hide_rest_preheader'); ?>" id="ttw_hide_rest_preheader" <?php checked(weaver_getopt_checked( 'ttw_hide_rest_preheader' )); ?> />
		<small>If you check this box, then the code from this area will not be displayed on non-front pages.</small>
		<br /><br />

		<!-- ======== -->

		<label><span style="color:blue;"><b>Site Header Insert Code</b></span></label><?php
		weaver_help_link('help.html#HeaderInsCode','Help for Site Header Insert Code');
		?><br/>
		This code HTML will be inserted into the <em>#branding div</em> header area right above where the standard site
		header image goes. You can use it for logos, better site name text - whatever. When used in combination with hiding the site title,
		header image, and the menu, you can design a completely custom header. (The Weaver Plus plugin provides
		tools to make this process much easier.) If you hide the title, image, and header, no other code is generated
		in the #branding div, so this code can be a complete header replacement. You can also use WP shortcodes to embed plugins, including
		rotating image slideshows such as <a href="http://www.jleuze.com/plugins/meteor-slides/" target="_blank">Meteor Slides</a>.
		And Weaver automatically supports the
		<a href="http://wordpress.org/extend/plugins/dynamic-headers/" target="_blank">Dynamic Headers</a> plugin which allows you
		create highly dynamic headers from its control panel - just install and it will work without any other code edits.
		<br />
		<textarea name="<?php weaver_sapi_advanced_name('ttw_header_insert'); ?>" rows=3 style="width: 95%"><?php echo(weaver_esc_textarea(weaver_getopt('ttw_header_insert'))); ?></textarea>
		<br />
		<label>Insert on Front Page Only: </label><input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_header_frontpage_only'); ?>" id="ttw_header_frontpage_only" <?php checked(weaver_getopt_checked( 'ttw_header_frontpage_only' )); ?> />
		<small>If you check this box, then this Header code will be used only when the front page is displayed. Other
		pages will be displayed using normal header settings. Checking this will also automatically hides the standard
		header image just on the front page. <b>Important:</b> This "Insert on Front Page Only" works, but you can
		get much more finely tuned control using the various "Per Page" options available, as well as using the
		'page-header-insert-code' Custom Field described in the Per Page box on the Page Editor panel.
		</small>
		<br /><br />
<?php weaver_sapi_submit('saveadvanced', 'Save All Advanced Options');?> <br />
		<!-- ======== -->

		<label><span style="color:blue;"><b>Post-Header Code</b></span></label><br/>
                This code will be inserted right after the header area and before the main content area (between the "header"
		and the "main" div tags), below the menus and site image.
		<br />
		<textarea name="<?php weaver_sapi_advanced_name('ttw_postheader_insert'); ?>" rows=3 style="width: 95%"><?php echo(weaver_esc_textarea(weaver_getopt('ttw_postheader_insert'))); ?></textarea>
		<br />
		<label>Hide on front page: </label><input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_hide_front_postheader'); ?>" id="ttw_hide_front_postheader" <?php checked(weaver_getopt_checked( 'ttw_hide_front_postheader' )); ?> />
		<small>If you check this box, then the code from this area will not be displayed on the front (home) page.</small><br />
		<label>Hide on non-front pages: </label><input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_hide_rest_postheader'); ?>" id="ttw_hide_rest_postheader" <?php checked(weaver_getopt_checked( 'ttw_hide_rest_postheader' )); ?> />
		<small>If you check this box, then the code from this area will not be displayed on non-front pages.</small>
		<br /><br />

		<!-- ======== -->

		<label><span style="color:blue;"><b>Pre-Footer Code</b></span></label><br/>
                This code will be inserted right after the main content area and before the footer area (between the "main"
		and the "footer" div tags).
		<br />
		<textarea name="<?php weaver_sapi_advanced_name('ttw_prefooter_insert'); ?>" rows=3 style="width: 95%"><?php echo(weaver_esc_textarea(weaver_getopt('ttw_prefooter_insert'))); ?></textarea>
		<br />
		<label>Hide on front page: </label><input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_hide_front_prefooter'); ?>" id="ttw_hide_front_prefooter" <?php checked(weaver_getopt_checked( 'ttw_hide_front_prefooter' )); ?> />
		<small>If you check this box, then the code from this area will not be displayed on the front (home) page.</small><br />
		<label>Hide on non-front pages: </label><input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_hide_rest_prefooter'); ?>" id="ttw_hide_rest_prefooter" <?php checked(weaver_getopt_checked( 'ttw_hide_rest_prefooter' )); ?> />
		<small>If you check this box, then the code from this area will not be displayed on non-front pages.</small>
		<br /><br />

		<!-- ======== -->

		<label><span style="color:blue;"><b>Site Footer Area Code</b></span></label><br/>
                This code will be inserted into the site footer area, right before the before the "Powered by" credits, but after any Footer widgets. This
                could include extra information, visit counters, etc. (Per-site option.)
		<br />
		<textarea name="<?php weaver_sapi_advanced_name('ttw_footer_opts'); ?>" rows=3 style="width: 95%"><?php echo(weaver_esc_textarea(weaver_getopt('ttw_footer_opts'))); ?></textarea>
		<br /><br />

		<!-- ======== -->

		<label><span style="color:blue;"><b>Post-Footer Code</b></span></label><br/>
                This code will be inserted right after the footer area (between the "footer" and the "wrapper" /div tags).
		<br />
		<textarea name="<?php weaver_sapi_advanced_name('ttw_postfooter_insert'); ?>" rows=3 style="width: 95%"><?php echo(weaver_esc_textarea(weaver_getopt('ttw_postfooter_insert'))); ?></textarea>
		<br />
		<label>Hide on front page: </label><input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_hide_front_postfooter'); ?>" id="ttw_hide_front_postfooter" <?php checked(weaver_getopt_checked( 'ttw_hide_front_postfooter' )); ?> />
		<small>If you check this box, then the code from this area will not be displayed on the front (home) page.</small><br />
		<label>Hide on non-front pages: </label><input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_hide_rest_postfooter'); ?>" id="ttw_hide_rest_postfooter" <?php checked(weaver_getopt_checked( 'ttw_hide_rest_postfooter' )); ?> />
		<small>If you check this box, then the code from this area will not be displayed on non-front pages.</small>
		<br /><br />

		<!-- ======== -->

		<label><span style="color:blue;"><b>Pre-Sidebar Code</b></span></label><br/>
                This code will be inserted right above the first sidebar area. <small>(Note: some HTML elements may require using 'style="display:inline"' to avoid
		display over entire page width. This also doesn't work well with the "Two, left and right sides" sidebar arrangement.)</small>
		<br />
		<textarea name="<?php weaver_sapi_advanced_name('ttw_presidebar_insert'); ?>" rows=3 style="width: 95%"><?php echo(weaver_esc_textarea(weaver_getopt('ttw_presidebar_insert'))); ?></textarea>
		<br />
		<label>Hide on front page: </label><input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_hide_front_presidebar'); ?>" id="ttw_hide_front_presidebar" <?php checked(weaver_getopt_checked( 'ttw_hide_front_presidebar' )); ?> />
		<small>If you check this box, then the code from this area will not be displayed on the front (home) page.</small><br />
		<label>Hide on non-front pages: </label><input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_hide_rest_presidebar'); ?>" id="ttw_hide_rest_presidebar" <?php checked(weaver_getopt_checked( 'ttw_hide_rest_presidebar' )); ?> />
		<small>If you check this box, then the code from this area will not be displayed on non-front pages.</small>
		<?php  /* </div> <!-- end code insert areas --> */ ?>
		<br /><br />
		<?php weaver_sapi_submit('saveadvanced', 'Save All Advanced Options');?>
<?php
}

function weaver_adv_page_template() {
?>
		<a name="custompage" id="custompage"></a>
		<label><span style="color:#00f; font-weight:bold; font-size: larger;">Custom Page Template Options</span></label><br />
		Weaver includes several page templates. Some have extra options that are supported on this sub-tab.<br /><br />

		<label><span style="color:#4444CC;"><b>Per Page Widget Areas</b></span></label>
		<?php
		weaver_help_link('help.html#PPWidgets',__('Help for Per Page Widget Areas',WEAVER_TRANSADMIN));
		?><br/>
		<small>Weaver will create extra widget areas that will be displayed at the top of the content area on a per page basis. Enter
		a list of one or more widget area names separated by commas. Your names should include only letters, numbers, or underscores -
		no spaces or other special characters. The widgets areas will appear on the Appearance->Widgets menus. They can be included
		on individual pages by adding the name "Weaver Options For This Page" box on the Edit Page menu.</small>
		<br />
		<textarea name="<?php weaver_sapi_advanced_name('ttw_perpagewidgets'); ?>" rows=1 style="width: 95%"><?php echo(weaver_esc_textarea(weaver_getopt('ttw_perpagewidgets'))); ?></textarea>
		<br />
		<small>Note: these extra widget areas are also used by the Weaver Plus Widget Area shortcode.</small><br /><br />

<?php if (weaver_allow_multisite()) : ?>
		<label><span style="color:#4444CC;"><b>"Custom Header Page Template" Code and Options</b></span></label><br/>
		<small>This block functions exactly the same as <strong>Site Header Insert Code</strong> when used with pages created with the
		<em>Custom Header (see Adv Opts admin)</em> page template. The template creates pages that use
		only this code to display a header (they don't use the standard site header image).
		Use Per Page Options on main Edit Page menu to control Menu and Site Title visibility, or add code on a per page basis.</small>
		<br />

		<textarea name="<?php weaver_sapi_advanced_name('ttw_custom_header_insert'); ?>" rows=2 style="width: 95%"><?php echo(weaver_esc_textarea(weaver_getopt('ttw_custom_header_insert'))); ?></textarea>
<?php endif ; // end of major section of not allowed on multisite ?>
		<!-- ======== -->

		<br /><br /><label><span style="color:#4444CC;"><b>"Blank Page Template" Options</b></span></label><br/>
		<small>The <em>Blank - (see Adv Opts admin)</em> page template will wrap the content of an associated page only
		with the '#wrapper' HTML div surrounding the optional #header and #footer divs, and the #main div for the actual content.
		('&lt;div id="wrapper">&lt;div id="header">header&lt;/div>&lt;div id="main">Content of page&lt;/div>&lt;div id="footer">footer&lt;/div>').
		You will probably want to wrap your content in its own div with styling defined
		by a class added to the &lt;HEAD> Section. Use Per Page Options on main Page edit menu to control Menu, Site Title, and Header Image visibility.</small>

		<br /><br /><label><span style="color:#4444CC;"><b>Other Page Templates</b></span></label><br/>
		<ul style="list-style-type:disc;margin-left:20px;">
		    <li>The <em>2 Col Content</em> template splits content into two columns. You manually set the column
		    split using the standard WP '&lt;--more-->' convention. Columns will split first horizontally, then vertically (you
		    can have more than one &lt;--more--> tag).</li>
		    <li>The <em>Alternative Sidebar</em> templates have a single, fixed width sidebar that uses only the
		    <em>Alternative Widget Area</em>.</li>
		    <li>The <em>One column, no sidebar</em> template produces a single content column with no sidebars.</li>
		    <li>The <em>Pop Up</em> template allows total custom HTML styling with no predefined div's.</li>
		    <li>The <em>Sitemap</em> provides a page with a basic sitemap.</li>
		</ul>
		<!-- ===================================================== -->

<?php
}

function weaver_adv_site_opts() {
?>
		<a name="siteopts" id="siteopts"></a>
		<label><span style="color:#00f; font-weight:bold; font-size: larger;">Site Options</span></label><br />
		The following options are related to the current site. (The Administrative sub-tab also has some per-site
		options.) These options are <strong>not</strong> considered to be a part of the theme, and are not saved in the theme
		settings file when you save a theme. These options are saved in the WP database, and will survive an
		upgrade to a new Weaver version. (The settings on previous sub-tabs are saved when you save your theme settings.)
		<hr />


		<label><span style="color:#4444CC;"><b>Add HTML to Primary Menu Bar</b></span></label><br/>
		<small>Any HTML you provide here will be added to the left or right end of the primary menu bar. This will most likely be images and links to Twitter, Feedburner,
		or other social links. The maximum and optimal height for images is 24px. For other sized images, add <em>style="top:2px"</em>
		to the &lt;img&gt; tag (adjust the 2px to make it line up). Combine all links you need into this one spot.</small>
		<br />
		Left Side:<br /></label><textarea name="<?php weaver_sapi_advanced_name('ttw_menu_addhtml-left'); ?>" rows=1 style="width: 95%"><?php echo(weaver_esc_textarea(weaver_getopt('ttw_menu_addhtml-left'))); ?></textarea>
		<br />
		Right Side:<br /><textarea name="<?php weaver_sapi_advanced_name('ttw_menu_addhtml'); ?>" rows=1 style="width: 95%"><?php echo(weaver_esc_textarea(weaver_getopt('ttw_menu_addhtml'))); ?></textarea>
		<br /><br />

		<!-- ======== -->
                <label><span style="color:#4444CC;"><b>SEO Tags</b></span></label><br/>
		<small>Every site should have at least "description" and "keywords" meta tags
		for basic SEO (Search Engine Optimization) support. Please edit these tags to provide more information about your site, which is inserted
		into the &lt;HEAD&gt; section of your site. You might want to check out other WordPress SEO plugins if you need more advanced SEO. Note
		that this information is not part of your theme settings, and will not be included when you save or restore your theme.</small>
		<br />

		<textarea name="<?php weaver_sapi_advanced_name('ttw_metainfo'); ?>" rows=2 style="width: 95%"><?php echo(weaver_esc_textarea(weaver_getopt('ttw_metainfo'))); ?></textarea>
		<br>
                <label>Use SEO plugin instead: </label><input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_hide_metainfo'); ?>" id="ttw_hide_metainfo" <?php checked(weaver_getopt_checked( 'ttw_hide_metainfo' )); ?> />
		<small>You will want to check this box if you are using one of the WordPress SEO plugins. If you check this box, then this meta information will not be added to your site,
		and a standard WP &lt;title&gt; compatible with SEO plugins will be used. By default, Weaver generates header information that is SEO compatible, but does
		not have some of the advanced optimizations available from the plugins.
		</small>
                <br /><br />

		<!-- ======== -->

                <label><span style="color:#4444CC;"><b>Site Copyright</b></span></label><br/>
		<small>If you fill this in, the default copyright notice in the footer will be replaced with the text here. It will not
		automatically update from year to year. Use &amp;copy; to display &copy;. You can use other HTML as well.</small>
		<br />

		<textarea name="<?php weaver_sapi_advanced_name('ttw_copyright'); ?>" rows=1 style="width: 95%"><?php echo(weaver_esc_textarea(weaver_getopt('ttw_copyright'))); ?></textarea>
		<br>
                <label>Hide Powered By tag: </label><input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_hide_poweredby'); ?>" id="ttw_hide_poweredby" <?php checked(weaver_getopt_checked( 'ttw_hide_poweredby' )); ?> />
		<small>Check this to hide the "Proudly powered by" notice in the footer.</small>
                <br /><br />

		<!-- ======== -->

                <label><span style="color:#4444CC;"><b>The Last Thing</b></span></label><br/>
		<small>This code is inserted right before the closing &lt;/body&gt; tag.
                Some outside sites may provide you with JavaScript code that should be put here. (Note
		that this information is not part of your theme settings, and will not be included when you save or restore your theme.)</small>
		<br />
		<textarea name="<?php weaver_sapi_advanced_name('ttw_end_opts'); ?>" rows=1 style="width: 95%"><?php echo(weaver_esc_textarea(weaver_getopt('ttw_end_opts'))); ?></textarea>
                <br /><br />
		<?php weaver_sapi_submit('saveadvanced', 'Save All Advanced Options');?> <br />

		<!-- ======== -->
<?php
}

function weaver_adv_wvr_shortcodes() {
?>
		<a name="shortcodes" id="shortcodes"></a>
		<label><span style="color:blue;font-weight:bold; font-size: larger;"><b>Weaver Shortcodes</b></span></label>&nbsp;
		<?php weaver_help_link('help.html#shortcodes','Help for Weaver Shortcodes'); ?>
		<br />The Weaver theme includes some shortcodes to allow you to easily add extra content and features to your regular content. Shortcodes are explained more
		in the help - click the (?). This section just provides a brief summary of Weaver's shortcodes. (Weaver has just one short code. There are
		several more included with the Weaver Plus plugin.)
		<br /><br />
		<strong>[weaver_show_posts] Shortcode</strong><br />
		<br />Displays a filtered selection of posts withing a page. Summary of all parameters, shown with default values. You don't need to supply every
		option when you add the [weaver_show_posts] to your own content.<br />
		<em>[weaver_show_posts cats="" tags="" author="" single_post="" orderby="date" sort="DESC" number="5" show="full" hide_title=""
		hide_top_info="" hide_bottom_info="" show_featured_image="" show_avatar="" show_bio="" excerpt_length="" style=""
		class="" header="" header_style="" header_class="" more_msg=""]</em>
		<br />
		<p><strong style="color:red;">Warning!</strong> This short code should be used only on regular pages and
		the sidebar widget areas. Do not use it in Posts, or in Top widget areas.</p>
		<strong>The Weaver Plus plugin provides several other shortcodes.</strong>
<?php
}

function weaver_adv_admin_opts() {
?>
		<a name="adminopts" id="adminopts"></a>
		<label><span style="color:#00f; font-weight:bold; font-size: larger;"><b>Administrative Options</b></span></label><br/>
		These options control some administrative options and appearance features.
		<br />

<br /> <small><span style="color:red;"><b>IMPORTANT NOTE:</b></span> Weaver includes support for Rounded Corners and Shadows for Internet Explorer 7/8
via an add-on script called PIE. The script has been <strong>enabled</strong> by default. PIE doesn't work with the rounded menus. It also may have
a few incompatibilities with some Flash content and other images that might require z-index adjustment.
If you have difficulties or don't like the way your site renders in IE 7/8, you can disable the support.</small>

<br />
    <input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_hide_PIE'); ?>" id="ttw_hide_PIE" <?php checked(weaver_getopt_checked( 'ttw_hide_PIE' )); ?> />
    <label>Disable IE rounded corners support - </label><small>If you are having issues with IE and rounded corners, please disable this option.</small><br />

<br />
    <input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_hide_editor_style'); ?>" id="ttw_hide_editor_style" <?php checked(weaver_getopt_checked( 'ttw_hide_editor_style' )); ?> />
	<label>Disable Page/Post Editor Styling - </label><small>Checking this box will disable the Weaver sub-theme based styling in the Page/Post editor.
	If you have a theme using transparent backgrounds, this option will likely improve the Post/Page editor visibility.</small><br />

    <input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_show_preview'); ?>" id="ttw_show_preview" <?php checked(weaver_getopt_checked( 'ttw_show_preview' )); ?> />
	<label>Show Site Preview - </label><small>Checking this box will show a Site Preview at the bottom of the screen which might slow down response a bit.</small><br />

    <input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_hide_updatemsg'); ?>" id="ttw_hide_updatemsg" <?php checked(weaver_getopt_checked( 'ttw_hide_updatemsg' )); ?> />
	<label>Hide Update Messages - </label><small>Checking this box will hide the Weaver version update announcements on the Weaver Admin page.</small><br />

    <input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_hide_theme_thumbs'); ?>" id="ttw_hide_theme_thumbs" <?php checked(weaver_getopt_checked( 'ttw_hide_theme_thumbs' )); ?> />
	<label>Hide Theme Thumbnails - </label><small>Checking this box will hide the Sub-theme preview thumbnails on the Weaver Themes tab which might speed up response a bit.</small><br />

    <input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_hide_auto_css_rules'); ?>" id="ttw_hide_auto_css_rules" <?php checked(weaver_getopt_checked( 'ttw_hide_auto_css_rules' )); ?> />
	<label>Don't auto-display CSS rules - </label><small>Checking this box will disable the auto-display of Main Option elements that have CSS settings.</small><br />

    <input name="<?php weaver_sapi_advanced_name('ttw_css_rows'); ?>" id="ttw_css_rows" type="text" style="width:30px;height:20px;" class="regular-text" value="<?php echo(weaver_esc_textarea(weaver_getopt('ttw_css_rows'))); ?>" />
    <label>Set CSS+ text box height - </label><small>You can increase the default height of the CSS+ input area.</small>
<br />

    <input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_notab_mainoptions'); ?>" id="ttw_notab_mainoptions" <?php checked(weaver_getopt_checked( 'ttw_notab_mainoptions' )); ?> />
	<label>Show All Main Options at once - </label><small>If you want to see all the main options at once (not displayed by tabs), check this box.</small><br />

    <input type="checkbox" name="<?php weaver_sapi_advanced_name('ttw_force_inline_css'); ?>" id="ttw_force_inline_css" <?php checked(weaver_getopt_checked( 'ttw_force_inline_css' )); ?> />
	<label>Use Inline CSS - </label><small>Checking this box will have Weaver generate CSS inline rather than use the style-weaver.css external style sheet.</small><br />

<?php
    if (get_option('ttw_options')) { ?>
	<input type="checkbox" name="<?php weaver_sapi_advanced_name('wvr_hide_if_are_oldWeaver_opts'); ?>" id="wvr_hide_if_are_oldWeaver_opts" <?php checked(weaver_getopt_checked( 'wvr_hide_if_are_oldWeaver_opts' )); ?> />
	<label>Hide import notice if settings from old Weaver versions exist - </label><small>This option controls the display of the import old Weaver settings box.</small><br />
<?php
    }

    $type = get_filesystem_method(array());
    if (($type == 'ftpext' && !function_exists('weaver_fileio_plugin') )) {
?>
	<br /><br /><label><span style="color:#00f; font-weight:bold; font-size: larger;"><b>FTP File Access</b></span></label><br/>
	<p>Your system requires internal FTP access for full file access functionality. You do not have the Weaver File Access
	Plugin installed. If you don't have the ftp access credentials defined in your wp-config.php file, you will have options
	here to provide the required values.
<?php
        if (!(defined('FTP_HOST') && defined('FTP_USER') && defined('FTP_PASS'))) {
	    _e('Please enter your FTP credentials to proceed.',WEAVER_TRANSADMIN); echo ' ';
	    _e('If you do not remember your credentials, you should contact your web host.',WEAVER_TRANSADMIN); echo "</p>\n";
?>
	<br /><label><?php _e('Hostname',WEAVER_TRANSADMIN) ?>: &nbsp;&nbsp;&nbsp;</label><input name="<?php weaver_sapi_advanced_name('ftp_hostname'); ?>" id="ftp_hostname" type="text" style="width:300px;height:20px;" class="regular-text" value="<?php echo(weaver_esc_textarea(weaver_getopt('ftp_hostname'))); ?>" />
    <small>Specify the name of your host. Usually something like 'example.com'.</small>
    <br /><label><?php echo __('FTP Username',WEAVER_TRANSADMIN);?>: </label><input name="<?php weaver_sapi_advanced_name('ftp_username'); ?>" id="ftp_username" type="text" style="width:300px;height:20px;" class="regular-text" value="<?php echo(weaver_esc_textarea(weaver_getopt('ftp_username'))); ?>" />
    <small>Specify your FTP Username.</small>
    <br /><label><?php _e('FTP Password',WEAVER_TRANSADMIN) ?>: </label><input name="<?php weaver_sapi_advanced_name('ftp_password'); ?>" id="ftp_password" type="password" style="width:300px;height:20px;" class="regular-text" value="<?php  echo(weaver_esc_textarea(weaver_decrypt(weaver_getopt('ftp_password')))); ?>" />
    <small>Specify your FTP Password. This will be saved in an encrypted form.</small>
<br />
<?php
	} else {
?>
	    <p><em>FTP Credentials used from your wp-config.php file.</em></p>
<?php
	}
?>
<br /><label>Hide FTP Access Start up Dialog: </label>
<input type="checkbox" name="<?php weaver_sapi_advanced_name('ftp_hide_check_message'); ?>" id="ftp_hide_check_message" <?php checked(weaver_getopt_checked( 'ftp_hide_check_message' )); ?> />
	<small>If you check this, then the FTP File Access message box will not be displayed when you enter Weaver Admin. Weaver will continue to operate in
	reduced functionality mode: no editor styling, no save/restore, Inline CSS.</small><br />
<?php
    }

?>

    <br /><?php weaver_sapi_submit('saveadvanced', 'Save All Advanced Options'); ?><br /><br />
    <?php /* The following three hidden inputs allow the SAPI to save the values. If you don't do this here, then the values will
	    be set to false, and lost! SAPI is not tolerant of submitting a form that doesn't include every setting for the form group. */ ?>
    <input name="<?php weaver_sapi_advanced_name('ttw_subtheme'); ?>" id="ttw_subtheme" type="hidden" value="<?php echo weaver_getopt( 'ttw_subtheme' ); ?>" />
    <input name="<?php weaver_sapi_advanced_name('ttw_theme_image'); ?>" id="ttw_theme_image" type="hidden" value="<?php echo weaver_getopt( 'ttw_theme_image' ); ?>" />
    <input name="<?php weaver_sapi_advanced_name('ttw_theme_description'); ?>" id="ttw_theme_description" type="hidden" value="<?php echo weaver_getopt( 'ttw_theme_description' ); ?>" />
    <input name="<?php weaver_sapi_advanced_name('ttw_themename'); ?>" id="ttw_themename" type="hidden" value="<?php echo weaver_getopt( 'ttw_themename' ); ?>" />
    <input name="<?php weaver_sapi_advanced_name('ttw_version_id'); ?>" id="ttw_version_id" type="hidden" value="<?php echo weaver_getopt( 'ttw_version_id' ); ?>" />
    <input name="<?php weaver_sapi_advanced_name('ttw_style_version'); ?>" id="ttw_style_version" type="hidden" value="<?php echo weaver_getopt( 'ttw_style_version' ); ?>" />

    <hr />
    <p><em>Note: </em>Clear all settings moved to Save/Restore tab<p>
<?php
}
?>
