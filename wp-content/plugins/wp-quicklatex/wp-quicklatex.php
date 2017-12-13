<?php
/*
		Plugin Name: WP QuickLaTeX
		Plugin URI: http://www.holoborodko.com/pavel/quicklatex/
		Description: Access to complete LaTeX distribution. Publish formulae & graphics using native LaTeX syntax directly in the text. Inline formulas, displayed equations auto-numbering, labeling and referencing, AMS-LaTeX, <code>TikZ</code>, custom LaTeX preamble. No LaTeX installation required. Easily customizable using UI dialog. Actively developed and maintained. Visit <a href="http://www.holoborodko.com/pavel/quicklatex/">QuickLaTeX homepage</a> for more info.
		Version: 3.8.4
		Author: Pavel Holoborodko
		Author URI: http://www.holoborodko.com/pavel/
		Copyright: Pavel Holoborodko
		License: GPL2
*/

/*
	Advanced LaTeX plugin for Wordpress.

	Project homepage: http://www.holoborodko.com/pavel/quicklatex/
	Contact e-mail:   pavel@holoborodko.com

 	Copyright 2008-2015 Pavel Holoborodko
	All rights reserved.

	Contributors:
		Dmitriy Gubanov - server side development (http://cityjin.com)
		Kim Kirkpatrick - ideas & plugin development (http://qm-interpretation.com/)
		Ulrich Pinkall  - feature suggests (http://www3.math.tu-berlin.de/geometer/wordpress/vismathWS10/)
		Rob J. Hyndman  - feature suggests (http://robjhyndman.com/)

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions
	are met:

	1. Redistributions of source code must retain the above copyright
	notice, this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	notice, this list of conditions and the following disclaimer in the
	documentation and/or other materials provided with the distribution.

	3. Redistributions of any form whatsoever must retain the following
	acknowledgment:
	"
         This product includes software developed by Pavel Holoborodko
         Web: http://www.holoborodko.com/pavel/
         e-mail: pavel@holoborodko.com
	"

	4. This software cannot be, by any means, used for any commercial
	purpose without the prior permission of the copyright holder.

	5. This software is for individual usage only. It cannot be used as a part
	of blog hosting services for multiple users like WordPress MU or any other
	"software as a service" systems without the prior permission of the copyright holder.

	Any of the above conditions can be waived if you get permission from
	the copyright holder.

	THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTORS ``AS IS'' AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
	IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
	ARE DISCLAIMED.  IN NO EVENT SHALL THE AUTHOR OR CONTRIBUTORS BE LIABLE
	FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
	DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
	OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
	HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
	LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
	OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
	SUCH DAMAGE.
*/

	define("QUICKLATEX_PRODUCTION", true);
	
	// Prevent direct call to this php file
	if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

	// Version check
	global $wp_version;

	$exit_msg='WP QuickLaTeX requires Wordpress 2.8 or newer. Please update!';

	if (version_compare($wp_version,"2.8","<")){
		exit ($exit_msg);
	}

	if( !class_exists( 'WP_Http' ) )
			include_once( ABSPATH . WPINC. '/class-http.php' );
	
	// Define some constants http://codex.wordpress.org/Determining_Plugin_and_Content_Directories
	if ( ! defined( 'WP_CONTENT_DIR' ) )
		  define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );

	if ( ! defined( 'WP_PLUGIN_DIR' ) )
		  define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

	if ( ! defined( 'WP_QUICKLATEX_PLUGIN_DIR' ) )
		  define( 'WP_QUICKLATEX_PLUGIN_DIR', plugin_dir_url(__FILE__));

	if ( ! defined( 'WP_QUICKLATEX_CACHE_DIR' ) )
		  define( 'WP_QUICKLATEX_CACHE_DIR', WP_CONTENT_DIR.'/ql-cache' );

	if ( ! defined( 'WP_QUICKLATEX_CACHE_URL' ) )
		  define( 'WP_QUICKLATEX_CACHE_URL', content_url(). '/ql-cache' );
		  
		  
	// Default settings (you can use the filter to modify these)
	$def_options = array(
		'font_size'      	=> 17, 			    // 17px
		'font_color' 	 	=> '000000',		// black text
		'bg_type'        	=> 0, 				// Transparent
		'bg_color'       	=> 'ffffff',		// white background if opaque
		'latex_mode'     	=> 0,  				// auto mode
		'preamble'       	=> "\\usepackage{amsmath}\n\\usepackage{amsfonts}\n\\usepackage{amssymb}\n",
		'use_cache'      	=> 1,  				// use cache
		'show_errors'		=> 0,  				// 0 - do not show errors
		'add_footer_link'	=> 0,				// 0 - do not use it
		'is_preamble_corrected' => 1,			// is preamble corrected
		'displayed_equations_align' => 0,		// 0 - center, 1 - left, 2 -right
		'eqno_align'				=> 0,		// 0 - right, 1 - left

												// Plugin always handles [latex]...[/latex] tags and $$ .. $$, $$! ... $$ for compatibility
												// with older plugin versions.
												// User can use additional settings:
		'latex_syntax'			    => 0,		//  0 - Compatibility with LaTeX :
												//  \( ... \), $ ... $ - inline formulas, use \$ to escape $ from compilation
												//  \[ ... \], $$ .. $$ - displayed equations
												//  \begin{equation[*]} ... \end{equation}
												//  \begin{align[*]} ... \end{align}
												//	\begin{gather[*]} ...  \end{gather}
												//  \begin{multline[*]} ...
												//  \begin{flalign[*]} ...
												//  \begin{eqnarray[*]} ...
												//  \begin{alignat[*]} ...
												//  \begin{displaymath[*]} ...

		'exclude_dollars'		    => 0,		//  1 - Exclude $ ... $ from processing

		'image_format'				=> 3		// Image Format:
												// 0 - GIF
												// 1 - PNG
												// 2 - SVG
												// 3 - Automatic
	);
	
	// Set Globals to Defaults
	$ql_size        	= $def_options['font_size'];
	$ql_color       	= $def_options['font_color'];
	$ql_bg_type     	= $def_options['bg_type'];
	$ql_bg_color    	= $def_options['bg_color'];
	$ql_mode        	= $def_options['latex_mode'];
	$ql_preamble    	= quicklatex_sanitize_text($def_options['preamble']);
	$ql_use_cache   	= $def_options['use_cache'];
	$ql_show_errors 	= $def_options['show_errors'];
	$ql_link        	= $def_options['add_footer_link'];
	$ql_eqalign     	= $def_options['displayed_equations_align'];
	$ql_eqnoalign   	= $def_options['eqno_align'];
	$ql_latexsyntax 	= $def_options['latex_syntax'];
	$ql_exclude_dollars = $def_options['exclude_dollars'];
	$ql_imageformat 	= $def_options['image_format'];
	$ql_nlspage 		= $ql_latexsyntax; // Do we have NLS page or not?
	
	// Autonumbering.
	// Set equation number to 1 on the page start
	$ql_autoeqno = 1;

	// Set default global settings
	$ql_atts = null;
	
	// \label{}, \ref{} mechanics
	$ql_label_eqno = null;
	$ql_label_link = null;

	// Diagnostic
	$ql_fpp  = 0;
	
	// Register main actions	  
	add_action('init','quicklatex_init');
	add_action('admin_init', 'quicklatex_admin_init');
	add_action('admin_menu', 'quicklatex_menu');
	add_action('wp_print_scripts', 'quicklatex_frontend_scripts');

	if (function_exists('register_uninstall_hook'))
    	register_uninstall_hook(__FILE__, 'uninstall_quicklatex');
	
	//Load Styles, Create DB, Register hooks
	function quicklatex_init()
	{
		global $def_options;

		//Get access to global variables
		global $ql_size, $ql_color, $ql_bg_type, $ql_bg_color, $ql_mode, $ql_preamble, $ql_use_cache, $ql_show_errors, $ql_link;
		global $ql_eqalign, $ql_eqnoalign, $ql_latexsyntax;
		global $ql_exclude_dollars, $ql_nlspage, $ql_atts;
		global $ql_autoeqno;
		global $ql_imageformat;
		global $ql_label_eqno;
		global $ql_label_link;
		
		// Register Styles
		wp_register_style('wp-quicklatex-format', WP_QUICKLATEX_PLUGIN_DIR.'css/quicklatex-format.css');
		wp_enqueue_style('wp-quicklatex-format');
		
		// Check do we have options in DB. Write defaults if not.
		$g_options = get_option('quicklatex');
		if($g_options == false)
		{
			// Write Default options to DB
			update_option('quicklatex',$def_options);
		}else{

			// Add options to DB

			//1. AMS - packages.
			if(isset($g_options['is_preamble_corrected'])==false) // Do we have it in DB?
			{
				$preamble = trim($g_options['preamble']);
				if($preamble == '')
				{
					// Just setup include ams-packages to the preamble, if it is empty
					$preamble = "\\usepackage{amsmath}\n\\usepackage{amsfonts}\n\\usepackage{amssymb}\n";
				}else{

					// Check whether preamble already has ams packages one by one and add them if not.
				   if (strpos($preamble, "amssymb")  === false) $preamble ="\\usepackage{amssymb}\n".$preamble;
				   if (strpos($preamble, "amsfonts") === false) $preamble ="\\usepackage{amsfonts}\n".$preamble;
				   if (strpos($preamble, "amsmath")  === false) $preamble ="\\usepackage{amsmath}\n".$preamble;
			   }

				// Setup & add options to DB
				$g_options['preamble'] = $preamble;
				$g_options['is_preamble_corrected'] = 1;
			}

			// Displayed Equations Alignment
			if(isset($g_options['displayed_equations_align'])==false) // Do we have it in DB?
			{
				// Setup & add options to DB
				$g_options['displayed_equations_align'] = 0;
			}

			// Displayed Equations Numbering Alignment
			if(isset($g_options['eqno_align'])==false) // Do we have it in DB?
			{
				// Setup & add options to DB
				$g_options['eqno_align'] = 0;
			}

			// Syntax compatible with LaTeX
			if(isset($g_options['latex_syntax'])==false) // Do we have it in DB?
			{
				// Setup & add options to DB
				$g_options['latex_syntax'] = 0;
			}

			// Syntax compatible with LaTeX
			if(isset($g_options['exclude_dollars'])==false) // Do we have it in DB?
			{
				// Setup & add options to DB
				$g_options['exclude_dollars'] = 0;
			}

			// Image Format
			if(isset($g_options['image_format'])==false) // Do we have it in DB?
			{
				// Setup & add options to DB
				$g_options['image_format'] = 1;
			}else{
			
				// Switch from GIF to PNG
				if($g_options['image_format']==0) $g_options['image_format'] = 1;			  
			}

			// Add all new fields to DB
			update_option('quicklatex',$g_options);
		}
		
		// Load current options from DB to globals
		//$g_options     	    = get_option('quicklatex');
		
		$ql_size        	=$g_options['font_size'];
		$ql_color       	=$g_options['font_color'];
		$ql_bg_type     	=$g_options['bg_type'];
		$ql_bg_color    	=$g_options['bg_color'];
		$ql_mode        	=$g_options['latex_mode'];
		$ql_preamble    	=quicklatex_sanitize_text($g_options['preamble']);
		$ql_use_cache   	=$g_options['use_cache'];
		$ql_show_errors 	=$g_options['show_errors'];
		$ql_link        	=$g_options['add_footer_link'];
		$ql_eqalign     	=$g_options['displayed_equations_align'];
		$ql_eqnoalign   	=$g_options['eqno_align'];
		$ql_latexsyntax 	=$g_options['latex_syntax'];
		$ql_exclude_dollars =$g_options['exclude_dollars'];
		$ql_imageformat 	=$g_options['image_format'];
		$ql_nlspage 		=$ql_latexsyntax; // Do we have NLS page or not?
		
		// Autonumbering.
		// Set equation number to 1 on the page start
		$ql_autoeqno = 1;

		// Set default global settings
		$ql_atts = null;
		
		// \label{}, \ref{} mechanics
		$ql_label_eqno = null;
		$ql_label_link = null;
		
		// Register filters
		add_filter( 'the_content',  'quicklatex_parser',7);
		add_filter( 'comment_text', 'quicklatex_parser',7);
		add_filter( 'the_title',    'quicklatex_parser',7);
		add_filter( 'the_excerpt',  'quicklatex_parser',7);
		add_filter( 'thesis_comment_text',  'quicklatex_parser',7);
		add_filter( 'plugin_action_links',  'quicklatex_action_links', 10, 2);		
	}

	function quicklatex_menu()
	{
		if (function_exists('add_menu_page'))
		{
			$page = add_menu_page(
						  'QuickLaTeX', 									// $page_title
						  'QuickLaTeX', 									// $menu_title
						  'manage_options',							    	// $capability, http://codex.wordpress.org/Roles_and_Capabilities
						  'quicklatex-settings',							// $menu_slug	
						  'quicklatex_options_do_page',						// $function which generates admin page
						  WP_QUICKLATEX_PLUGIN_DIR.'images/quicklatex_menu_icon.png'   //$icon_url
						  );

			// Using registered $page handle to hook script load
			// http://codex.wordpress.org/Function_Reference/wp_enqueue_script
        	add_action('admin_print_scripts-'. $page, 'quicklatex_admin_scripts');
        	add_action('admin_print_styles-'. $page, 'quicklatex_admin_styles');
		}

		if (function_exists('add_submenu_page'))
		{
			//add_submenu_page('wp-quicklatex/wp-quicklatex-admin.php','Options', 'Options', 'manage_options', 'wp-quicklatex/wp-quicklatex-admin.php');
			//add_submenu_page('wp-quicklatex/wp-quicklatex-admin.php','Uninstall', 'Uninstall',  'manage_options', 'wp-quicklatex/wp-quicklatex-uninstall.php');
		}
	}

 	function quicklatex_frontend_scripts()
    {
		// Load JS on front page	
		if (!is_admin())
			{
			  wp_enqueue_script('jquery');
			  wp_enqueue_script('wp-quicklatex-frontend', WP_QUICKLATEX_PLUGIN_DIR.'js/wp-quicklatex-frontend.js', array('jquery'), '1.0');
		    }	
	}
	
 	function quicklatex_admin_scripts()
    {
		// Load JS on admin page
        wp_enqueue_script('jquery');
		wp_enqueue_script('postbox');
		wp_enqueue_script('wp-quicklatex-colorpicker', WP_QUICKLATEX_PLUGIN_DIR.'js/colorpicker/js/colorpicker.js', array('jquery'), '1.0');
		wp_enqueue_script('wp-quicklatex-icheckbox', WP_QUICKLATEX_PLUGIN_DIR.'js/icheckbox/js/icheckbox.js', array('jquery'), '1.0');

		wp_enqueue_script('jquery-form',array('jquery') ); // KAK AJAX
		wp_enqueue_script('jquery-ui-tabs',array('jquery','jquery-ui-core','jquery-ui-widget') ); // KAK UI
    }

 	function quicklatex_admin_styles()
    {
		wp_register_style('wp-quicklatex-plugin', WP_QUICKLATEX_PLUGIN_DIR.'css/quicklatex-plugin.css');
		wp_enqueue_style('wp-quicklatex-plugin');

		wp_register_style('wp-quicklatex-colorpicker', WP_QUICKLATEX_PLUGIN_DIR.'js/colorpicker/css/colorpicker.css');
		wp_enqueue_style('wp-quicklatex-colorpicker');

		wp_register_style('wp-quicklatex-icheckbox', WP_QUICKLATEX_PLUGIN_DIR.'js/icheckbox/css/icheckbox.css');
		wp_enqueue_style('wp-quicklatex-icheckbox');

		wp_enqueue_style('jquery-ui', WP_QUICKLATEX_PLUGIN_DIR.'css/smoothness/jquery-ui-1.8.9.custom.css');  // KAK UI

    }

	function quicklatex_action_links($links, $file) {
		static $this_plugin;

		if (!$this_plugin) {
			$this_plugin = plugin_basename(__FILE__);
		}

		if ($file == $this_plugin) {
			// The "page" query string value must be equal to the slug
			// of the Settings admin page we defined earlier, which in
			// this case equals "myplugin-settings".
			$settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=quicklatex-settings">Settings</a>';
			array_unshift($links, $settings_link);
		}
		
		return $links;
	}

	// Delete DB entry on uninstall
	function uninstall_quicklatex()
	{
		delete_option("quicklatex");
	}

	// Register the plugin's setting
	function quicklatex_admin_init()
	{
		//http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/
		register_setting( 'quicklatex_options', 'quicklatex', 'quicklatex_validate_options');
	}

	// Validate user input
	// http://ottodestruct.com/blog/2009/wordpress-settings-api-tutorial/
	function quicklatex_validate_options($input)
	{
		$newinput = $input;
		$newinput['font_color'] = quicklatex_sanitize_color(trim($input['font_color']));
		if($newinput['bg_type']==1)
		{
			// if opaque - sanitize color
			$newinput['bg_color'] = quicklatex_sanitize_color(trim($input['bg_color']));
		}

		$newinput['preamble'] = trim($input['preamble']);

		// Form doesn't send checkbox value if it is set to off.
		// So we should check that manually
		if (isset($newinput['use_cache']) == false ) $newinput['use_cache'] = 0;
		if (isset($newinput['show_errors']) == false ) $newinput['show_errors'] = 0;
		if (isset($newinput['latex_syntax']) == false ) $newinput['latex_syntax'] = 0;
		if (isset($newinput['exclude_dollars']) == false ) $newinput['exclude_dollars'] = 0;
		
		// Make sure we never get GIF
		if($newinput['image_format']==0) $newinput['image_format'] = 1;			  
		
		// POST sends only fields being used on the form
		// Fill out all other fields - otherwise they are not stored in the DB!!!!
		$options = get_option('quicklatex');
		$newinput = wp_parse_args($newinput, $options);

		return $newinput;
	}

	function quicklatex_options_do_page()
	{
	?>
		<?php $options = get_option('quicklatex'); ?>

		<?php
			if( $options['use_cache'] == 1 )
				if( false == is_quicklatex_cache_writable(WP_QUICKLATEX_CACHE_DIR) )
					echo '<div id="message" class="error"><p style="line-height:150%;"><strong>QuickLaTeX cannot access cache directory for formula images: <i>'.WP_QUICKLATEX_CACHE_DIR.'</i></strong>.<br />'.'Please create it and make sure it is writeable (by <span class="ql-code" style="font-size:13px;">chmode 755</span> or through File Manager in cPanel).'.'<br />'.'<strong>Do not ignore this warning -- caching is crucial for performance of your site.</strong>'.'</p></div>';
		?>

	<div id="wrap" >
		<div id="ql-header">
			<!-- Logo -->
			<p style="text-align:center;width:70%;">
					<img <?php echo 'src="'.WP_QUICKLATEX_PLUGIN_DIR.'images/quicklatex_logo.gif'.'"'; ?> alt="QuickLaTeX Logo"/>
			</p>
		</div>

		<div id="holder">
		
		<div class="metabox-holder">		
		
			<!-- settings area -->

			<form id="optionsform" method="post" action="options.php">  <!-- AJAX id added -->
			<?php settings_fields('quicklatex_options'); ?>

				<div id="tabs">
					<ul>
						<li><a href="#tab-welcome">Getting started</a></li>
						<li><a href="#tab-basic">Basic Settings</a></li>
						<li><a href="#tab-advanced">Advanced</a></li>												
						<li><a href="#tab-system">System</a></li>						
						<li><a href="#tab-about">About</a></li>												
					</ul>

					<!-- Welcome -->
					<div id="tab-welcome">
						<p>
						Activate QuickLaTeX on a page, post, or comment with the shortcode <span class="ql-code">[latexpage]</span>. Then you may insert LaTeX expressions directly in the text by surrounding them with <span class="ql-code">$..$</span> or place them displayed with <span class="ql-code">\[..\]</span> as you usually do typing offline LaTeX documents.
						</p>
						
						<p>
						You may also use display environments <span class="ql-code">equation, align, displaymath, eqnarray, multline, flalign, gather, and alignat</span>. 
						</p>
						<p>
						Here is example of a page with LaTeX formulas (how it appears in Wordpress editor):
						</p>
						<div class="ql-examples">
[latexpage]<br />
At first, we sample $f(x)$ in the $N$ ($N$ is odd) equidistant points around $x^*$:<br />
\[<br />
&nbsp;&nbsp;&nbsp;f_k = f(x_k),\: x_k = x^*+kh,\: k=-\frac{N-1}{2},\dots,\frac{N-1}{2}<br />
\]<br />
where $h$ is some step.<br />
Then we interpolate points $\{(x_k,f_k)\}$ by polynomial <br />
\begin{equation}&nbsp;\label{eq:poly}<br />
&nbsp;&nbsp;&nbsp;P_{N-1}(x)=\sum_{j=0}^{N-1}{a_jx^j}<br />
\end{equation}<br />
Its coefficients $\{a_j\}$ are found as a solution of system of linear equations:<br />
\begin{equation}&nbsp;\label{eq:sys}<br />
&nbsp;&nbsp;&nbsp;\left\{ P_{N-1}(x_k) = f_k\right\},\quad  k=-\frac{N-1}{2},\dots,\frac{N-1}{2}<br />
\end{equation}<br />
Here are references to existing equations: (\ref{eq:poly}), (\ref{eq:sys}).<br />
Here is reference to non-existing equation (\ref{eq:unknown}).<br />
						</div>
						<p>
						Same page processed by QuickLaTeX and published (how visitors see it in a browser):
						</p>			
						<p style="text-align:center;">
						<img <?php echo 'src="'.WP_QUICKLATEX_PLUGIN_DIR.'images/example-1.png'.'"'; ?> alt="QuickLaTeX Example"/>						
						</p>
						
						<p>
						For the display environments, equation numbering is automatic, but this may be overridden and the number set explicitly with <span class="ql-code">\tag{..}</span> placed within the display expression.
						</p>
						
						<p>
						A number of options may be set for an expression with attribute tags such as <span class="ql-code"> size, color, background, align</span>, as arguments of <span class="ql-code">\quicklatex{}</span> placed within the expression: 
						</p>
						<div class="ql-examples">
						\[<br />
&nbsp;&nbsp;&nbsp;\quicklatex{color="#00ff00" size=25}<br />
&nbsp;&nbsp;&nbsp;\boxed{f(x)=\int_1^{\infty}\frac{1}{x^2}\,\mathrm{d}x=1}<br />
						\]<br />
						</div>
						<p>
						renders with green font of 25 pixels height:
						</p>
						<p style="text-align:center;">
						<img <?php echo 'src="'.WP_QUICKLATEX_PLUGIN_DIR.'images/example-2.png'.'"'; ?> alt="QuickLaTeX Example"/>						
						</p>
						<p>
						Compilation of an expression may be suppressed, showing instead the LaTeX source, by preceding the expression with a <span class="ql-code">!</span>.
						</p>
						
						<p>
						For mathematical graphs you may use <span class="ql-code">tikzpicture</span> and <span class="ql-code">pgfplots</span>, e.g. : 
						</p>
						<div class="ql-examples">
\begin{tikzpicture}<br />
[+preamble]<br />
&nbsp;&nbsp;&nbsp;\usepackage{pgfplots}<br />
&nbsp;&nbsp;&nbsp;\pgfplotsset{compat=newest}<br />
[/preamble]<br />
&nbsp;&nbsp;&nbsp;\begin{axis}<br />
&nbsp;&nbsp;&nbsp;&nbsp;\addplot3[surf,domain=0:360,samples=40] {cos(x)*cos(y)};<br />
&nbsp;&nbsp;&nbsp;\end{axis}<br />
\end{tikzpicture}
						</div>
						<p>
						compiles to
						</p>
						<p style="text-align:center;">
						<img <?php echo 'src="'.WP_QUICKLATEX_PLUGIN_DIR.'images/example-3.png'.'"'; ?> alt="QuickLaTeX Example"/>						
						</p>
						<p>
						see <a href="http://www.holoborodko.com/pavel/quicklatex/" target="_blank">on-line tikz help</a> for examples and more information.
						</p>
						<p>
						Whether or not QuickLaTeX has been activated with <span class="ql-code">[latexpage]</span>, you may always place a LaTeX expression within <span class="ql-code">[latex] .. [/latex]</span> shortcodes everywhere on the site.  Attribute tags are allowed: <span class="ql-code">[latex attrs]...[/latex]</span>.
						</p>
						
						<p class="ql-head-desc">
						Visit <a href="http://www.holoborodko.com/pavel/quicklatex/" target="_blank">QuickLaTeX's home page</a> for more information on features, examples, tips &amp; tricks, <span class="ql-code">tikZ</span> graphics inclusion, etc.
						</p>
					</div>


					<!-- Basic settings -->
					<div id="tab-basic">
						<p class="ql-heading ql-bottom-border">
								Set default styling. These settings can be overridden on a per formula basis using <span class="ql-code">\quicklatex{}</span> within its LaTeX code. For more precise tuning, modify <span class="ql-code">quicklatex-format.css</span>.
						</p>

						<table class="form-table">
							<tbody>

							<!-- Font Size -->
							<tr>
								<th valign="top" scope="row">Font Size</th>
								<td valign="top">
									<select name="quicklatex[font_size]" class="select">
									<?php
										for($i=5;$i<101;$i++)
										{
											if ($i==$options['font_size']){
													echo '<option value="'.$i.'"'.'selected="selected">'.$i.'px</option>';
												}else{
													echo '<option value="'.$i.'">'.$i.'px</option>';
											}
										}
									?>
									</select>
								</td>
								<td></td>
							</tr>

							<tr class="ql-row">
								<td class="ql-desc" colspan="3">
								<small>
								Choose formula font size to match text on your website.
								</small>
								</td>
							</tr>

							<!-- Font Color -->
							<tr class="ql-row even">
								<th valign="top" scope="row">Font Color</th>
								<td valign="top">
								<input class="text" id="textcolor" type="text" name="quicklatex[font_color]" value="<?php echo $options['font_color']; ?>"/>
								</td>
								<td>&nbsp;</td>
							</tr>

							<tr class="ql-row even">
								<td class="ql-desc" colspan="3">
								<small>
									RGB triplet in CSS format - six digit hexadecimal number.
									Please see <a href="http://w3schools.com/css/css_colors.asp">CSS Colors</a> for examples.
								</small>
								</td>
							</tr>

							<!-- Background Color -->
							<tr class="ql-row">
								<th valign="top">
									Background Color
								</th>
								<td>
									<select class="select" name="quicklatex[bg_type]" id="bgcolor_combobox">
									<?php 
										
										echo '<option value="0"'.(0==$options['bg_type']?'selected="selected"':'').'>'.'Transparent'.'</option>'; 															
										echo '<option value="1"'.(1==$options['bg_type']?'selected="selected"':'').'>'.'Opaque'.'</option>'; 															
									?>
									</select>
								</td>
								<td>
									<input class="text" type="text" id="bkgcolor" name="quicklatex[bg_color]" value="<?php echo $options['bg_color']; ?>"/>
								</td>
							</tr>

							<tr class="ql-row">
								<td class="ql-desc" colspan="3">
								<small>
								Formulas are rendered with transparent background by default. Setup opaque color in <a href="http://w3schools.com/css/css_colors.asp">CSS format</a> otherwise.
								</small>
								</td>
							</tr>

							</tbody>
						</table>

						<table class="form-table">
							<tbody>

							<!-- Displayed Equations Alignment -->
							<tr class="ql-row">
								<th valign="top" scope="row">Displayed Equations Alignment</th>
								<td valign="top">
									<select class="select" name="quicklatex[displayed_equations_align]">
									<?php 
										
										echo '<option value="0"'.(0==$options['displayed_equations_align']?'selected="selected"':'').'>'.'center'.'</option>'; 															
										echo '<option value="1"'.(1==$options['displayed_equations_align']?'selected="selected"':'').'>'.'left'.'</option>'; 															
										echo '<option value="2"'.(2==$options['displayed_equations_align']?'selected="selected"':'').'>'.'right'.'</option>'; 															
									?>
									</select>
								</td>

								<td></td>
							</tr>

							<!-- Equation Number Position -->
							<tr class="ql-row even">
								<th valign="top" scope="row">Equation Number Position</th>
								<td valign="top">
									<select class="select" name="quicklatex[eqno_align]">
									<?php 
										
										echo '<option value="0"'.(0==$options['eqno_align']?'selected="selected"':'').'>'.'right'.'</option>'; 															
										echo '<option value="1"'.(1==$options['eqno_align']?'selected="selected"':'').'>'.'left'.'</option>'; 															
									?>
									</select>
								</td>
								<td></td>
							</tr>

							</tbody>
						</table>
						<div class="ql-alignright">
							<input class='button-primary' type='submit' name='submit' value='<?php _e('Update QuickLaTeX Settings &raquo;'); ?>' />
						</div>
						<br class="clear" />
					</div>

					<!-- System settings -->
					<div id="tab-system">
						<p class="ql-heading ql-bottom-border">
						QuickLaTeX converts formulas into SVG/PNG images and tries to cache them on your site for maximum performance.<br />
						By default QuickLaTeX is tolerant to mistakes in LaTeX code and stops only on critical errors.<br />
						You can tune these settings here.<br />
						</p>
					
						<table class="form-table">
						<tbody>

							<!-- Image Format -->
							<tr class="ql-row even">
								<th valign="top" scope="row">Image format</th>
								<td valign="top">
									<select class="select" name="quicklatex[image_format]" id="imgformat_combobox">
									<?php 
										echo '<option value="0"'.(0==$options['image_format']?'selected="selected"':'').'disabled="disabled">'.'GIF'.'</option>'; 															
										echo '<option value="1"'.(1==$options['image_format']?'selected="selected"':'').'>'.'PNG'.'</option>'; 															
										echo '<option value="2"'.(2==$options['image_format']?'selected="selected"':'').'>'.'SVG'.'</option>'; 															
										echo '<option value="3"'.(3==$options['image_format']?'selected="selected"':'').'>'.'Auto'.'</option>'; 	
									?>
									</select>
								</td>
								<td></td>
							</tr>

							<tr class="ql-row even">
								<td class="ql-desc" colspan="3">
									<small>
									Output image format. 'Auto' is recommended choice.
									<!-- SVG is the best, but not implemented yet. In the next versions QuickLaTeX will choose image format automatically depending on visitor's browser capability. -->
									</small>
								</td>
							</tr>

							<!-- Cache -->
							<tr class="ql-row">
								<th valign="top" scope="row">Cache images locally</th>
								<td valign="top">
									<input type="checkbox" name="quicklatex[use_cache]" value="1" <?php if($options['use_cache']==1)echo 'checked="checked"'; ?>/>
								</td>
								<td></td>
							</tr>

							<tr class="ql-row">
								<td class="ql-desc" colspan="3">
									<small>
									Absolutely essential for performance of your site. Also we recommend you to use <a href="http://wordpress.org/extend/plugins/wp-super-cache/">WP Super Cache</a> for optimal speed.
									</small>
								</td>
							</tr>

							<!-- Show LaTeX errors & warnings -->
							<tr class="ql-row even">
								<th valign="top" scope="row">Debug mode</th>
								<td valign="top">
									<input type="checkbox" name="quicklatex[show_errors]" value="1" <?php if($options['show_errors']==1)echo 'checked="checked"'; ?>/>													</td>
								<td> </td>
							</tr>
							<tr class="ql-row even">
								<td  valign="top" class="ql-desc" colspan="3">
									<small>
									Forces QuickLaTeX compiler to be strict and show all warnings and errors.
									</small>
								</td>
							</tr>


							</tbody>
						</table>

						<div class="ql-alignright">
							<input class='button-primary' type='submit' name='submit' value='<?php _e('Update QuickLaTeX Settings &raquo;'); ?>' />
						</div>
						<br class="clear" />
					</div>


					<!-- Advanced -->
					<div id="tab-advanced">
						<p class="ql-heading ql-bottom-border">
						QuickLaTeX processes native LaTeX shorthands on the pages activated by <span class="ql-code">[latexpage]</span> tag.<br />
						However you can activate LaTeX syntax interpretation sitewide (on every post, page and comment).<br />
						No tags are needed in this mode.<br />
						</p>
					
						<table class="form-table">
						<tbody>
							<!-- LaTeX shortcodes -->
							<tr class="ql-row">
								<th valign="middle" scope="row" style="vertical-align:middle;">Use LaTeX Syntax Sitewide</th>
								<td valign="middle">
									<input type="checkbox" name="quicklatex[latex_syntax]" value="1" <?php if($options['latex_syntax']==1)echo 'checked="checked"'; ?>/>
								</td>
							</tr>

							<tr class="ql-row">
								<td class="ql-desc" colspan="3">
									<small>
									This option activates LaTeX interpretation on all pages, regardless of the use of <span class="ql-code">[latexpage]</span>. This is useful for a site that is authored exclusively by LaTeX users.  If this is turned on, all authors must be made aware that <span class="ql-code">$</span> is a special symbol, and that the standard dollar symbol must be typed <span class="ql-code">$</span>.
									</small>
								</td>
							</tr>

							<!-- TeX shortcodes -->
							<tr class="ql-row even">
								<th scope="row" style="vertical-align:middle;">Exclude <span class="ql-code-big">$ .. $</span></th>
								<td valign="middle">
									<input type="checkbox" name="quicklatex[exclude_dollars]" value="1" <?php if($options['exclude_dollars']==1)echo 'checked="checked"'; ?>/>
								</td>
								<td> </td>
							</tr>

							<tr class="ql-row even">
								<td class="ql-desc" colspan="3">
									<small>
									The use of the traditional TeX <span class="ql-code">$</span> conflicts with the non-LaTeX user's expectation of its meaning. This option allows us to turn its interpretation off, so standard dollar signs need no special treatment and will not confuse the non-mathematical user.
									</small>
								</td>
							</tr>
							</tbody>
						</table>
						
						<p class="ql-heading-simple" style="margin-top:30px;">
						Please setup LaTeX preamble for the whole* website below.<br /> You can define new commands and include additional packages as usual:
						</p>
						<textarea class="ql-preamble" name="quicklatex[preamble]" rows="10" cols="50"><?php echo $options['preamble']; ?></textarea>
						<p class="ql-notes">
						*Global preamble can be overriden by <span class="ql-code">[preamble]</span> tag for particular equation.
						</p>
						<div class="ql-alignright">
							<input class='button-primary' type='submit' name='submit' value='<?php _e('Update QuickLaTeX Settings &raquo;'); ?>' />
						</div>
						<br class="clear" />
					</div>

					<!-- About -->
					<div id="tab-about">
<p class="ql-about">
QuickLaTeX is free under linkware license. Which means service can be used: (a) on non-commercial websites; (b) with visible and direct backlink to <a href="http://www.holoborodko.com/pavel/quicklatex/">QuickLaTeX homepage</a>.
</p>

<p class="ql-about">
<strong><span class="ql-powered">"Powered by <a href="http://www.holoborodko.com/pavel/quicklatex/">QuickLaTeX</a>"</span></strong> somewhere on the site would greatly support us and inspire future development.  
</p>
						&#9632;&nbsp;People behind QuickLaTeX
						<DL class="ql-devs">
							<DT><a href="http://holoborodko.com/pavel/" target="_blank">Pavel Holoborodko</a></dt>
							<DD class="ql-people">Author & core QuickLaTeX developer.</dd>
						
							<DT><a href="http://cityjin.com" target="_blank">Dmitriy Gubanov</a></dt>
							<DD class="ql-people">Server-side implementation and administration.</dd>
							
							<DT><a href="http://www.legacy.com/obituaries/santafenewmexican/obituary.aspx?pid=155800823" target="_blank">Kim Kirkpatrick</a> <i>(Miss you my dear friend, may your soul rest in peace)</i></dt>
							<DD class="ql-people">Ideas &amp; plugin development.</dd>
						</DL>

						&#9632;&nbsp;Top idea contributors
						<DL class="ql-devs">
							<DT><a href="http://robjhyndman.com/" target="_blank">Rob J. Hyndman</a></dt>
							<DD class="ql-people">AMS-LaTeX support which lead to custom preamble feature.</dd>
							
							<DT><a href="http://www3.math.tu-berlin.de/geometer/wordpress/vismathWS10/" target="_blank">Ulrich Pinkall</a></dt>
							<DD class="ql-people">Native LaTeX syntax embedded directly in the text.</dd>
						</DL>
					</div>

				</div> <!-- tabs -->
			</form> <!-- form -->
		</div>
		</div> <!-- holder -->


		<!-- right sidebar -->
		<div class="postbox-container" id="likethis">
		<div class="metabox-holder">		
				<div id="ql-support" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span>Like QuickLaTeX?</span></h3>
				
					<div class="inside">
						<div class="ql-postbox-content">

						<p class="ql-p-justified">
						<strong>
						    Want to help make this plugin even better? 
							Donate to keep new improvements coming!
						</strong>
						</p>	
				
							<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
								<input type="hidden" name="cmd" value="_s-xclick" />
								<input type="hidden" name="hosted_button_id" value="PG7NTGB7YAMXN" />
								<table class="ql-el-centered">
									<tr><td><input type="hidden" name="on0" value="Pick Your Fibonacci Number" />Pick Your Fibonacci Number</td></tr>
									<tr>
										<td>
											<select name="os0">
												<option value="Thanks!" selected="selected">$5.00 - Thanks!</option>											
												<option value="Two beers">$8.00 - Two beers</option>												
												<option value="One meal">$13.00 - One meal</option>																								
												<option value="Supporter">$21.00 - Supporter</option>
												<option value="1mo Hosting Donator">$34.00 - 1mo Hosting Donator </option>
												<option value="2mo Hosting Donator">$55.00 - 2mo Hosting Donator</option>
												<option value="Super-human">$89.00 - Super-human</option>												
											</select>
										</td>
									</tr>
									<tr>
										<td style="text-align:center;">
											<input type="hidden" name="currency_code" value="USD" />
											<input type="image" <?php echo 'src="'.WP_QUICKLATEX_PLUGIN_DIR.'images/donate.gif'.'"'; ?> name="submit" alt="PayPal - The safer, easier way to pay online." />
										</td>
									</tr>
								</table>
							</form>
							
						<p class="ql-p-centered">
							<small>	All donations are used for development of the plugin.</small>							
						</p>
						
						</div>
					</div>
				</div> <!-- Like this plugin? -->
		
				<div id="ql-partners" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span>Supporters:</span></h3>
					<div class="inside">
						<p class="ql-p-centered">
						<a href="http://www.advanpix.com/" target="_blank">
							<img <?php echo 'src="'.WP_QUICKLATEX_PLUGIN_DIR.'images/amct_logo.png'.'"'; ?> alt="Advanpix Multiprecision Computing Toolbox"/>					
						</a>
						</p>
					</div>				
				</div>
		</div>
		</div>

	</div> <!-- wrap -->


	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($)
		{
			$(':checkbox').iphoneStyle();

			// postboxes setup
			postboxes.add_postbox_toggles('ql-overview');

			// Use transparent background color by default
			is_bgcolor_visible = <?php if($options['bg_type']==0) echo 'false';  else echo 'true'; ?>;

			if(is_bgcolor_visible==true)
			{
				$("#bkgcolor").show();
			}else{
				$("#bkgcolor").hide();
			}


			$("#bgcolor_combobox").change(function () {
					  $("#bgcolor_combobox option:selected").each(function () {
							if($(this).val()==1)
							{
								$("#bkgcolor").show();
							}else{
								$("#bkgcolor").hide();										
							}
							
						  });
					  //$("div#test").text(str);
					})
					//.trigger('change');

			$('#textcolor').ColorPicker({
				onChange: function(hsb, hex, rgb, el) {
					// Set text color to be visible
					$("#textcolor").val(hex);
					$("#textcolor").css('background-color', '#' + hex);
					if(0.213 * rgb.r +	0.715 * rgb.g +0.072 * rgb.b < 128)
						$("#textcolor").css('color', '#FFFFFF');
					else
						$("#textcolor").css('color', '#000000');
				},

				onSubmit: function(hsb, hex, rgb, el) {
					$(el).val(hex);
					$(el).ColorPickerHide();
				},

				onBeforeShow: function () {
					$(this).ColorPickerSetColor(this.value);
				}
			}).keyup(function(){

				// In case if user edit color field from keyboard:
				var color = $(this).val();

				// Sanitize color
				var len = 6 - color.length;
				if (len>0)
					 color = color+new Array(len+1).join('0');
				else
					 color = color.substr(0,6);

				// Update textcolor field
				var hex = parseInt(color, 16);
				var rgb = {r: hex >> 16, g: (hex & 0x00FF00) >> 8, b: hex & 0x0000FF};

				if(0.213 * rgb.r +	0.715 * rgb.g +0.072 * rgb.b < 128)
				{
					 $(this).css('color','#FFFFFF');
				}else{
					 $(this).css('color','#000000');
				}

				$(this).css('background-color','#'+color);

				// Update colorpicker interface using RGB colors
				// Colors can be setup with some precision introduced by ColorPicker
				$(this).ColorPickerSetColor(rgb);
			});


			$('#bkgcolor').ColorPicker({

				onChange: function(hsb, hex, rgb, el) {
					// Set text color to be visible
					$("#bkgcolor").val(hex);
					$("#bkgcolor").css('background-color', '#' + hex);
					if(0.213 * rgb.r +	0.715 * rgb.g +0.072 * rgb.b < 128)
					{
						$("#bkgcolor").css('color', '#FFFFFF');
					}else{
						$("#bkgcolor").css('color', '#000000');
					}
				},

				onSubmit: function(hsb, hex, rgb, el) {
					$(el).val(hex);
					$(el).ColorPickerHide();
				},
				onBeforeShow: function () {
					$(this).ColorPickerSetColor(this.value);
				}
			}).keyup(function(){

				// In case if user edit color field from keyboard:
				var color = $(this).val();

				// Sanitize color
				var len = 6 - color.length;
				if (len>0)
					 color = color+new Array(len+1).join('0');
				else
					 color = color.substr(0,6);

				// Update textcolor field
				var hex = parseInt(color, 16);
				var rgb = {r: hex >> 16, g: (hex & 0x00FF00) >> 8, b: hex & 0x0000FF};

				if(0.213 * rgb.r +	0.715 * rgb.g +0.072 * rgb.b < 128)
				{
					 $(this).css('color','#FFFFFF');
				}else{
					 $(this).css('color','#000000');
				}

				$(this).css('background-color','#'+color);

				// Update colorpicker interface using RGB colors
				// Colors can be setup with some precision introduced by ColorPicker
				$(this).ColorPickerSetColor(rgb);
			});

			// Init Color selection + input fields sync
			var txtcolor = $("#textcolor").val();

			hex = parseInt(txtcolor, 16);
			r   = hex >> 16;
			g   = (hex & 0x00FF00) >> 8;
			b   = hex & 0x0000FF;
			if(0.213 * r +	0.715 * g +0.072 * b < 128)
			{
				 $("#textcolor").css('color','#FFFFFF');
			}else{
				 $("#textcolor").css('color','#000000');
			}

			$("#textcolor").css('background-color','#'+txtcolor);


			var bkgcolor = $("#bkgcolor").val();
			hex = parseInt(bkgcolor, 16);
			r   = hex >> 16;
			g   = (hex & 0x00FF00) >> 8;
			b   = hex & 0x0000FF;
			if(0.213 * r +	0.715 * g +0.072 * b < 128)
			{
				 $("#bkgcolor").css('color','#FFFFFF');
			}else{
				 $("#bkgcolor").css('color','#000000');
			}
			$("#bkgcolor").css('background-color','#'+bkgcolor);
		});



		//]]>
	</script>

	<!-- AJAX -->
	<script type="text/javascript">
		//<![CDATA[

		jQuery(document).ready( function($)
		{
			$("#optionsform").ajaxForm(
				{ success :
						function() {
							var bp = $(".button-primary");
							var hold = bp.val();
							bp.val("<?php _e('Submitted');?>");
							setTimeout(function(){bp.val(hold);}, 1500);
						}
				}	);

			$(function() {
				$("#tabs").tabs();
			});


		});

		//]]>
	</script>

	<?php
	} //quicklatex_options_do_page
	
	// Compile formula with parameters
	function quicklatex_kernel($atts, $formula_rawtext)
	{
		//Get access to global variables
		global $ql_size, $ql_color, $ql_bg_type, $ql_bg_color, $ql_mode, $ql_preamble, $ql_use_cache, $ql_show_errors, $ql_link;
		global $ql_eqalign, $ql_eqnoalign, $ql_latexsyntax;
		global $ql_autoeqno;
		global $ql_imageformat;
		global $ql_label_eqno;
		global $ql_label_link;
		global $ql_fpp;
		
		// Default atts for formula compilation - inherited from globals
		$default_atts = array(
						  'size' 		=> $ql_size,					// font size in pixels
						  'color' 		=> $ql_color,					// text color
						  'background' 	=> false,						// background color
						  'mode' 		=> $ql_mode,					// mode, magical
						  'example'		=> 'false',						// show source code of formula without compilation
						  'errors'		=> $ql_show_errors,				// be strict and show latex errors & warnings
						  'reqno'		=> null,    					// eq. number placed on the right
						  'leqno'		=> null,   						// eq. number placed on left
						  'eqno'		=> null,    					// eq. number with aligment from global option
						  'align'		=> null,	 					// horizontal align of displayed equation - valid only for displayed equations.
						  'width'		=> null,   						// picture width - valid only for pictures (tikz).
						  'eqlabel'		> null	    					// eq. label placed on the opposite side of eqno.
						  );

		// Rewrite default atts with parameters supplied by user
		$atts = shortcode_atts($default_atts, $atts);

		// Sanitize formula text
		// We need it here because quicklatex_kernel() can be called from many places with/without prior sanitization
		$formula_text = quicklatex_sanitize_text($formula_rawtext);
		
		// Check for embedded parameters introduced by \quicklatex{}
		// They supersede all other params, even [latex ... ]
		// All parameters are the same except for 'example' - since it doesn't have much meaning inside formula
		if(preg_match('/(\\\\quicklatex\{(.*?)\})/si',$formula_text,$m))
		{
			// Parse embedded attributes
			$embedded_atts = shortcode_parse_atts($m[2]);

			// Rewrite atts with values defined inside the tag
			$atts = shortcode_atts($atts, $embedded_atts);

			// Remove \quicklatex{} command from the formula to avoid it compilation be the server
			$formula_text = str_replace($m[1],'', $formula_text);
		}

	   // Extract parameter values as local variables
	   extract($atts);

		// Lookup tables for CSS classes based on parameters
		$align_val 	= array(	0  => 'center',
								1  => 'left',
								2  => 'right'
							);

	    $align_css	= array(	'center' => 'ql-center-displayed-equation',
								'left'   => 'ql-left-displayed-equation',
								'right'  => 'ql-right-displayed-equation'
							);

		$eqno_align_css = array(	0  => 'ql-right-eqno',
									1  => 'ql-left-eqno',
								);

		// Our main variables
		$image_url    = false;
		$image_align  = false;
		$image_height = 0;
		$image_width  = 0;		
		$status 	 = -100; // indicates global (unknown) error
		$error_msg   = "Unknown error";
		$out_str     = "Unknown error";
		$displayed_equation = false;
		$tikz_picture = false;
		$tikz_width = $width;
		$auto_eqno = false;
		$eqno_parentheses = true; // could be a global option in ??? future
		$label_link = null;
		$ql_fpp = $ql_fpp + 1;
		$imageformat = $ql_imageformat; //shortcut for global var
		
		// Check for custom preamble in the formula_text
		if(preg_match('/(\[(\+?)preamble\b(.*?)\](.*?)\[\/\+?preamble\])/si', $formula_text, $m))
		{
			// Preamble has been sanitized already.
			// So no need for further processing
			$premode  = $m[2];

			if($premode=='+')
			{
				// Merge local definitions with global preamble
			 	$preamble = $ql_preamble."\n".$m[4];
			}else{
				// Override global preamble (by default)
				// if no prefix supplied

				// Since local parameters/vars replace global ones.
				// If user wants to add something in global preamble - he/she should do that using admin UI page.
			 	$preamble = $m[4];
			}

			// Remove preamble text from the formula.
			$formula_text = str_replace($m[1],'',$formula_text);
		}else{

			// Use global preamble if no local is supplied
			$preamble = $ql_preamble;
		}

		// Quick check of the parameters
		// color
		$color 		  = quicklatex_sanitize_color($color);

		// Check align parameter.
		if(!is_null($align)) $align = strtolower($align);      // sanitize user's setting
		else 				 $align = $align_val[$ql_eqalign]; // or take global option

		// Check size for valid ranges
		if($size<5)  $size = 5;
		if($size>99) $size = 99;

		// Check mode
		if($mode!=0 && $mode!=1) $mode = 0;

		// Check background
		if($background==false)
		{
			if($ql_bg_type==1) $background = $ql_bg_color;
			else $background ='transparent';
		}else{
			if($background!='transparent')
				$background = quicklatex_sanitize_color($background);
		}

		if(''!=$formula_text)
		{
			// Analyze formula for environments, figures, etc.

			// Check formula & preamble for tikz/pgf environments
			$pattern = '/(!*\\\\begin\{(tikz|pgf|rxn).*?\\\\end\{\2.*?\})/si';
			if(preg_match($pattern,$formula_text) || preg_match($pattern,$preamble))
			{
				$tikz_picture = true;
			}

			if(!$tikz_picture)
			{
				// Detect unnumbered displayed equations
				// (still can be numbered by \tag{})
				// $$ .. $$, \[ ... \]
				if(preg_match('/(!*\$\$(.*?)\$\$)/s',$formula_text) ||
				   preg_match('/(!*\\\\\[.*?\\\\\])/s',$formula_text) ||
				   preg_match('/(!*\\\\begin\{(displaymath)\}.*?\\\\end\{\2\})/si',$formula_text)
				   )
				{
					$displayed_equation = true;
				}

				//Detect numbered displayed equations
				if(!$displayed_equation)
				{
				    $pattern = '/(!*\\\\begin\{(equation|eqnarray|align|multline|flalign|gather|alignat)(\**)\}.*?\\\\end\{\2\**\})/si';
					if(preg_match($pattern,$formula_text,$m))
					{
						if($m[3] != "*")
						{
							// Replace \2 with \2* for proper compilation
							$formula_text = str_replace($m[2],$m[2].'*',$formula_text);

							// Check if eqno was supplied
							// Auto number equation if not
							if(is_null($eqno))
							{
								$auto_eqno = true;
							}
						}
						$displayed_equation = true;
					}
				}

				if($displayed_equation)
				{
					// Displayed equation numbering:
					// 1. Search for \tag - it overrides auto numbering and even eqno.
					// 2. If no \tag nor eqno were supplied then use auto numbering.
					// Priority \tag > eqno > autonumbering

					// Parse \tag[*]{} for custom eqno
					if(preg_match('/(\\\\tag(\**)\{(.*?)\})/si',$formula_text,$m))
					{
						$eqno = $m[3];
						if($m[2] != "*")
						{
							$eqno_parentheses = true;
						}else{
							$eqno_parentheses = false;
						}

						// Remove \tag text from the formula to avoid it compilation by server
						$formula_text = str_replace($m[1],'', $formula_text);
					}else{
						if($auto_eqno)
						{
							// Set up number for the equation
							$eqno = $ql_autoeqno;

							// Increment global eqno
							$ql_autoeqno = $ql_autoeqno + 1;
						}
					}
				}
			}
			
			// If we have number for equation then 
			if(isset($eqno))
			{
				// search for \label{} command
				if(preg_match('/(\\\\label\{(.*?)\})/si',$formula_text,$m))
				{
					$label_id = trim($m[2]);
					$label_link = "id".crc32($formula_text);
					
					$ql_label_eqno[$label_id] = $eqno;
					$ql_label_link[$label_id] = $label_link;
					
					// Remove \label{} text from the formula to avoid it compilation by server
					$formula_text = str_replace($m[1],'', $formula_text);
				}				
			}
			
			if($displayed_equation)
			{
				// Remove empty lines - important for displayed environments
				// This kind of sanitization should be done last, after [preamble] processing, etc.
				// http://stackoverflow.com/questions/709669/how-do-i-remove-blank-lines-from-text-in-php
				$formula_text = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $formula_text);
			}

		  	// Build hash based on local and global settings.
			// So it will change if any setting is changed.
			//$formula_hash = md5($formula_rawtext.$preamble.$size.$color.$background.$mode.$errors.$imageformat.$width);
			$formula_hash = md5($formula_rawtext.$preamble.$size.$color.$background.$mode.$errors.'1'.$width);			
			
			$extrastyles = '';
			switch ($imageformat) {
				case 0: // GIF, use PNG anyway
				case 1: // PNG
					$image_ext   = 'png';
					break;
					
				case 2: // SVG
					$image_ext   = 'svg';
					break;

				case 3: // Auto
					$image_ext   = 'png';
					$extrastyles = 'quicklatex-auto-format';
					break;
			}

			$info_file  = 'quicklatex.com-'.$formula_hash.'_l3.txt';
			$image_file = 'quicklatex.com-'.$formula_hash.'_l3.'.$image_ext;
			
			//$png_image_file = 'quicklatex.com-'.$formula_hash.'_l3.svg';			
			//$svg_image_file = 'quicklatex.com-'.$formula_hash.'_l3.svg';
			
			$cache_url  = WP_QUICKLATEX_CACHE_URL;
			$cache_path = WP_QUICKLATEX_CACHE_DIR;

			$info_full_path  = $cache_path.'/'.$info_file;
		    $image_full_path = $cache_path.'/'.$image_file;

			// Should we use cache?
			if($ql_use_cache==1)
			{
			    // Check info file in cache 
				if(file_exists($info_full_path)  && is_readable($info_full_path))
				{
					// Check required format first
					$inCache = file_exists($image_full_path) && is_readable($image_full_path);
					
					if($imageformat==3)
					{
						// Check also for SVG in Auto mode						
						$image_full_path_svg = str_replace("png", "svg", $image_full_path);
						$inCache = $inCache && file_exists($image_full_path_svg) && is_readable($image_full_path_svg);
					}					

					if($inCache)
					{
						// Everything is Ok - read cached image properties
						$handle       = fopen($info_full_path, "r");
						$image_url    = rtrim(fgets($handle),"\n");
						$image_align  = rtrim(fgets($handle),"\n");
						$image_width  = rtrim(fgets($handle),"\n");							
						$image_height = rtrim(fgets($handle),"\n");
						fclose($handle);

						$image_url = WP_QUICKLATEX_CACHE_URL.'/'.$image_file;
						$status = 0;
					}
				}
			}

			// Check if we still do not have image_url, reasons:
			// - we pushed to not use cache
			// - or formula is not in the cache
			if(!$image_url)
			{
					// Cannot access cache or formula is not in the cache.
					// Create new query to the QuickLaTeX.com to generate formula

					// URL for POST request
					if (QUICKLATEX_PRODUCTION)		$url = 'http://www.quicklatex.com/latex3.f';
					else 					    	$url = 'http://quicklatex.lan/latex3.f';		

					$body =       'formula=' .quicklatex_encode($formula_text);
					$body = $body.'&fsize='  .$size.'px';
					$body = $body.'&fcolor=' .$color;
					$body = $body.'&mode='   .$mode;
					$body = $body.'&remhost='.quicklatex_encode(get_option('siteurl').' '.get_permalink());
					$body = $body.'&out='    .$imageformat;

					if($preamble!='')               $body = $body.'&preamble='.quicklatex_encode($preamble);
					if($background!='transparent')	$body = $body.'&bcolor='.$background;

					if($errors==1)	                $body = $body.'&errors=1';

					if($tikz_picture == true)
					{
						if(!is_null($tikz_width))
						{
							$body = $body.'&width='.$tikz_width;
						}
					}

					// Send request to compile LaTeX code on the server
					$server = new WP_Http;

					//echo '<pre>'.$body.'</pre>';

					$server_resp = $server->post($url,array('body'=>$body, 'timeout'=> 40));

					if(!is_wp_error($server_resp)) // Check for error codes $server_resp['response']['code']
					{
						//echo '<pre>'.$server_resp['body'].'</pre>';					
						
						// Everything is ok, parse server response
						if (preg_match("/^([-]?\d+)\r\n(\S+)\s([-]?\d+)\s(\d+)\s(\d+)\r?\n?([\s\S]*)/", $server_resp['body'], $regs))
						{
						
							$status       = $regs[1];
							$image_url    = $regs[2];
							$image_align  = $regs[3];
							$image_width  = $regs[4];
							$image_height = $regs[5];
							$error_msg    = $regs[6];

							if (!QUICKLATEX_PRODUCTION)	$image_url = str_replace("quicklatex.com", "localhost", $image_url);
							
							if ($status == 0) // Everything is all right!
							{
								// Write formula to the cache if we allowed to
								if($ql_use_cache==1) //
								{
									if(is_quicklatex_cache_writable($cache_path))
									{
										// Cache info file
										$handle = fopen($info_full_path, "w");
										fwrite($handle,$image_url."\n");
										fwrite($handle,$image_align."\n");						
										fwrite($handle,$image_width."\n");												
										fwrite($handle,$image_height."\n");												
										fclose($handle);

										$isSuccess = false;
										
										// Cache image file - PNG
										$image_data = $server->request($image_url);
										if(!is_wp_error($image_data))
										{
										
											$handle = fopen($image_full_path, "w");
											fwrite($handle,$image_data['body']);
											fclose($handle);

											$isSuccess = true;
										}else{
										
											$error_msg = "Cannot download image from QuickLaTeX server: ".$image_data->get_error_message()."\nPlease make sure your server/PHP settings allow HTTP requests to external resources (\"allow_url_fopen\", etc.)\nThese links might help in finding solution:\nhttp://wordpress.org/extend/plugins/core-control/\nhttp://wordpress.org/support/topic/an-unexpected-http-error-occurred-during-the-api-request-on-wordpress-3?replies=37";															
										}
										
										// Cache image file - SVG
										$svg_image_url  = str_replace("png", "svg", $image_url);
										$svg_image_data = $server->request($svg_image_url);
										if(!is_wp_error($svg_image_data))
										{
											$svg_image_full_path = str_replace("png", "svg", $image_full_path);
											$handle = fopen($svg_image_full_path, "w");
											fwrite($handle,$svg_image_data['body']);
											fclose($handle);
											
											$isSuccess = $isSuccess && true;
										}else{
										
											$error_msg = "Cannot download image from QuickLaTeX server: ".$svg_image_data->get_error_message()."\nPlease make sure your server/PHP settings allow HTTP requests to external resources (\"allow_url_fopen\", etc.)\nThese links might help in finding solution:\nhttp://wordpress.org/extend/plugins/core-control/\nhttp://wordpress.org/support/topic/an-unexpected-http-error-occurred-during-the-api-request-on-wordpress-3?replies=37";															
										}
										
										if($isSuccess)	$image_url = WP_QUICKLATEX_CACHE_URL.'/'.$image_file;
										else			$image_url = false;
									}
								}
							}
						}
						
					}else{
					
						$error_msg = "Cannot connect to QuickLaTeX server: ".$server_resp->get_error_message()."\nPlease make sure your server/PHP settings allow HTTP requests to external resources (\"allow_url_fopen\", etc.)\nThese links might help in finding solution:\nhttp://wordpress.org/extend/plugins/core-control/\nhttp://wordpress.org/support/topic/an-unexpected-http-error-occurred-during-the-api-request-on-wordpress-3?replies=37";
					}
                    unset($server);
			} // if(!$image_url)


			if($image_url) // Do we have a valid image_url?
			{
			  	if($status == 0) //No errors
				{
					if($mode==0) // Auto mode
					{
						if($tikz_picture == true)
						{
								// tikZ picture
								$out_str  = '<p class="ql-center-picture">';
								$out_str .= "<img src=\"$image_url\""." height=\"$image_height\" width=\"$image_width\""." class=\"ql-img-picture $extrastyles\""." alt=\"Rendered by QuickLaTeX.com\" title=\"Rendered by QuickLaTeX.com\"/>";
								$out_str .= "</p>";
						}else{
							if($displayed_equation==false)
							{
								// Inline formula
								// Apply ql-inline-formula style class, setup correct vertical alignment

								$out_str  = "<img src=\"$image_url\" class=\"ql-img-inline-formula $extrastyles\" alt=\"".quicklatex_alt_text($formula_text)."\" title=\"Rendered by QuickLaTeX.com\"";
								$out_str .= " height=\"$image_height\" width=\"$image_width\"";
								$out_str .= " style=\"vertical-align: ".-$image_align."px;\"/>";								
								
							}else{
							
								// Displayed equation
								// set up CSS based on tag parameter && global setting
								$out_str = "";

								// Insert link for further references
								if(!is_null($label_link))
								    $out_str .= "<a name=\"$label_link\"></a>";

								// We need image_height for correct vertical centering									
								$out_str .= '<p class="'.$align_css[$align].'"';
								if($image_height >= 0) 
									$out_str .= " style=\"line-height: ".$image_height."px;\"";
									
								// Close <p> tag
								$out_str .= ">";
								
								// Write markup for eqnos and center them vertically
								$lwrap = $eqno_parentheses ? "(" : "";
								$rwrap = $eqno_parentheses ? ")" : "";								

								if ( !is_null($eqno) || !is_null($eqlabel) )
								{
									$eqnx = (!is_null($eqno)) ? $lwrap."$eqno".$rwrap : "&nbsp;";
									$eqnl = (!is_null($eqlabel)) ? "$eqlabel" : "&nbsp;";
									$out_str .= "<span class=\"$eqno_align_css[$ql_eqnoalign]\"> $eqnx </span>";
									$out_str .= "<span class=\"". $eqno_align_css[($ql_eqnoalign+1) % 2] ."\"> $eqnl </span>";
								}else{
									$eqnx = (!is_null($reqno)) ? $lwrap."$reqno".$rwrap : "&nbsp;";
									$out_str .= "<span class=\"ql-right-eqno\"> $eqnx </span>";
									$eqnx = (!is_null($leqno)) ? $lwrap."$leqno".$rwrap : "&nbsp;";
									$out_str .= "<span class=\"ql-left-eqno\"> $eqnx </span>";
								}
								
								// Place image on the page
								$out_str .= "<img src=\"$image_url\""." height=\"$image_height\" width=\"$image_width\""." class=\"ql-img-displayed-equation $extrastyles\" alt=\"".quicklatex_alt_text($formula_text)."\" title=\"Rendered by QuickLaTeX.com\"";

								$out_str .= "/>";

								$out_str .= "</p>";

							}// else for $displayed_equation==false
						} // else $tikz_picture==true
					}else{
					    // $mode = 1
						// Apply ql-manual-mode
						$out_str = "<img src=\"$image_url\" height=\"$image_height\" width=\"$image_width\" class=\"ql-manual-mode $extrastyles\" alt=\"Rendered by QuickLaTeX.com\" title=\"Rendered by QuickLaTeX.com\"/>";
					}
					
				}else{ // status == 0
					// error msg can contain tags $ ... $ which should be escaped
					// so we encode them as html entities
				    $error_msg = quicklatex_verbatim_text($error_msg);
					$out_str = "<pre class=\"ql-errors\">*** QuickLaTeX cannot compile formula:\n".quicklatex_verbatim_text($formula_text)."\n\n*** Error message:\n$error_msg</pre>";
				}
				
			}else{ // image_url

				// There is no formula in the cache & we couldn't connect/download it from QuickLaTeX server either.
				// show error instead of formula
				// error msg can contain tags $ ... $ which should be escaped
				// so we encode them as html entities
				$error_msg = quicklatex_verbatim_text($error_msg);
				$out_str = "<pre class=\"ql-errors\">*** QuickLaTeX cannot compile formula:\n".quicklatex_verbatim_text($formula_text)."\n\n*** Error message:\n$error_msg</pre>";
			}
			
			return $out_str;
		}
	}

	// Called once per page/comment/block
	function quicklatex_parser($content)
	{
		global $ql_latexsyntax;
	    global $ql_autoeqno;
		global $ql_atts;
		global $ql_label_eqno;
		global $ql_label_link;
		global $ql_nlspage;
		global $ql_fpp;
		
		$start = quicklatex_microtime_float();
		
		// Reset labels, refs on every page
		$ql_label_eqno = array();
		$ql_label_link = array();
		$ql_fpp  = 0;
		
		// Reset eqno for every post
		$ql_autoeqno = 1;

		$ql_nlspage = $ql_latexsyntax;

		// Detect [latexpage] and handle it parameters - global for all page		
		// Attention! This routine can change global variables like $ql_nlspage, $ql_autoeqno, $ql_atts
		$content = preg_replace_callback('/(!*\[latexpage\b(.*?)\])/si','do_quicklatex_latexpage',$content);		

		if($ql_nlspage == true)
		{
			// Parse mixed syntax: NLS + [latex] + [latexregion]
			$content = quicklatex_native_syntax_parser($content,true);

		}else{

			// Use [latex] or [latexregion] to mark LaTeX regions

			// [latex]...[/latex] tags and $$ .. $$, $$! ... $$ for compatibility with older plugin versions.

			// [latex] ... [/latex]
			// [latex example=true] ... [/latex] - print preformatted source code <pre>[latex] ... [/latex]</pre>
			// ![latex] ... [/latex]  - print source code including [latex] tags (verbatim output without formatting).
			// [[latex] ... [/latex]] - print source code without [latex] tags (verbatim output without formatting)
			$content = preg_replace_callback('/((.?)(\[(latex|tex|math|latexregion)\b(.*?)\](.*?)\[\/\4\])(.?))/si', 'do_quicklatex_tags_proxy', $content);

			//  $$ ... $$, $$! ... $$ - just for the backward compatibility.
			// !$$ ... $$ - print source code with <pre> ... </pre>
			//$content = preg_replace_callback('/(!*\$\$(.*?)\$\$)/s','do_quicklatex_doubledollars',$content);
		}
		
		// Make correct referencing of equations if any
		$content = preg_replace_callback('/(!*\\\\ref\{(.*?)\})/si','do_quicklatex_references', $content);
		
		// Diagnostics
		$stop = quicklatex_microtime_float();		
		$time = (int)(($stop - $start)*1000.0);
		
		// We gather statistics on execution time to find out do we need to optimize parsing or not in future versions
		if($ql_fpp > 0)
		{
			// Do not count bots since they are not users and we are looking for user experience.
			$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
			if(!quicklatex_is_bot($agent))
			{
				$usecache = is_quicklatex_cache_writable(WP_QUICKLATEX_CACHE_DIR);
				$permalink = quicklatex_encode(get_option('siteurl').' '.get_permalink());
				
				if (QUICKLATEX_PRODUCTION)		$url = 'http://www.quicklatex.com/latex3s.f';
				else 					    	$url = 'http://localhost/latex3s.f';		

				$body  = 'fpp='        .$ql_fpp;
				$body .= '&time='      .$time;
				$body .= '&url='       .$permalink;
				$body .= '&usecache='  .(int)$usecache;
				
				// Send statistics to the server
				$server = new WP_Http;
				$server->post($url,array('body'=>$body, 'blocking'=>false));
                unset($server);
			}
		}

		return $content;
	}

	function quicklatex_native_syntax_parser($content,$mixed)
	{
		global $ql_exclude_dollars;

		if($mixed==true)
		{
			// NLS + [latex] + [latexregion] mixed syntax processing.

			// [latex] ... [/latex]
			// [latexregion] ... [/latexregion]
			// \begin{equation[*]} ... \end{\2}
			// \begin{align[*]} ... \end{\2}
			// \begin{displaymath[*]} ... \end{\2}
			// \begin{eqnarray[*]} ... \end{\2}
			// \begin{multline[*]} ... \end{\2}
			// \begin{flalign[*]} ... \end{\2}
			// \begin{gather[*]} ... \end{\2}
			// \begin{alignat[*]} ... \end{\2}
			// !\begin{} ... \end{}  - verbatim source code

			$mixedpattern ='/((.?)(\[(latex|tex|math|latexregion)\b(.*?)\](.*?)\[\/\4\])(.?))|(!*\\\\begin\{(equation|displaymath|eqnarray|align|multline|flalign|gather|alignat).*?\\\\end\{\9\**\})/si';

			$content = preg_replace_callback($mixedpattern,'do_quicklatex_mixed_syntax',$content);
		}else{

			// Only Native LaTeX syntax processing

			// \begin{equation[*]} ... \end{\2}
			// \begin{align[*]} ... \end{\2}
			// \begin{displaymath[*]} ... \end{\2}
			// \begin{eqnarray[*]} ... \end{\2}
			// \begin{multline[*]} ... \end{\2}
			// \begin{flalign[*]} ... \end{\2}
			// \begin{gather[*]} ... \end{\2}
			// \begin{alignat[*]} ... \end{\2}
			// !\begin{} ... \end{}  - verbatim source code
			$content = preg_replace_callback('/(!*\\\\begin\{(equation|displaymath|eqnarray|align|multline|flalign|gather|alignat).*?\\\\end\{\2\**\})/si','do_quicklatex_displayed_equations',$content);
		}

		// tikZ & pgf environments:
		//  \begin{tikz ...
		//  \begin{pgf ...
		// !\begin{} ... \end{}  - verbatim source code
		$content = preg_replace_callback('/(!*\\\\begin\{(tikz|pgf|rxn).*?\\\\end\{\2.*?\})/si','do_quicklatex_displayed_equations', $content);
		// Displayed math environments:
		//  \[ ... \]
		// !\[ ... \] - verbatim source code
		$content = preg_replace_callback('/(!*\\\\\[.*?\\\\\])/s','do_quicklatex_displayed_equations', $content);

		//  $$ ... $$
		// !$$ ... $$ - verbatim source code
		$content = preg_replace_callback('/(!*\$\$(.*?)\$\$)/s','do_quicklatex_doubledollars',$content);

		// Inline formulas
		//  \( ... \)
		// !\( ... \)  - verbatim source code
		$content = preg_replace_callback('/(!*\\\\\((.*?)\\\\\))/s','do_quicklatex_inline_formulas', $content);			// Inline formulas

		// Inline formulas
		//  \begin{math} ... \end{math}
		// !\begin{math} ... \end{math}  - verbatim source code
		$content = preg_replace_callback('/(!*\\\\begin\{math\}(.*?)\\\\end\{math\})/si','do_quicklatex_inline_formulas', $content);

		// Inline formulas
		// \$ - escape any processing (way to insert dollar sign)
		$content = preg_replace('/\\\\\$/s','&#36;', $content);

		if($ql_exclude_dollars == 0)
		{
			//  $ ... $
			// !$ ... $ - verbatim source code
			$content = preg_replace_callback('/(!*\$(?:latex)?(.*?)\$)/s','do_quicklatex_inline_formulas', $content);
		}

		return $content;
	}
	
	// [!][latexpage ...]
	function do_quicklatex_latexpage($m)
	{
		global $ql_nlspage;
		global $ql_atts;
		global $ql_autoeqno;
		
		$wrap_text	= $m[1];		
		$attr = $m[2];
		
		if(substr($wrap_text, 0, 1) == "!")
		{
			// Show source code if the first symbol !, e.g:	!$ ... $
			return quicklatex_verbatim_text(substr($wrap_text, 1));
		}else{
		
			// Parse tag parameters
			$attr = shortcode_parse_atts(quicklatex_sanitize_text($attr));

			// Setup starting eqno for the page
			if(isset($attr['eqno']))
			{
				$ql_autoeqno = $attr['eqno'];
				unset($attr['eqno']);
			}

			// Set global attributes for the page
			$ql_atts = $attr;

			// Enable NLS for the page
			$ql_nlspage = true;
			
			// Finally remove [latexpage] tag from the page			
			return '';
		}
	}

	function do_quicklatex_references($m)
	{
		global $ql_label_eqno;
		global $ql_label_link;

		$wrap_text	= trim($m[1]);		
		$label_id 	= trim($m[2]);

		if(substr($wrap_text, 0, 1) == "!")
		{
			// Show source code if the first symbol !, e.g:	!$ ... $
			return quicklatex_verbatim_text(substr($wrap_text, 1));
		}else{
			// Check do we have this label in the list of existing labels.
			if(!empty($ql_label_eqno[$label_id]))
			{
				return "<a href=\"#$ql_label_link[$label_id]\">".$ql_label_eqno[$label_id]."</a>";
			}else{
				return "??";			
			}
		}
	}
	
	// Process [latex] & envs. in one loop - for correct auto numbering
	// Mixed syntaxis - [latex] & native environments
	function do_quicklatex_mixed_syntax($m)
	{
		if(!empty($m[4]))
		{
			// latex|math|tex|latexregion
			return do_quicklatex_tags_proxy($m);
		}else{
			// native environments
			$n = array();
			$n[1] = $m[8];
			return do_quicklatex_displayed_equations($n);
		}
	}

	// Full LaTeX compatibility mode
	// $..$ for inline formulas and \[ ... \] for displayed equations + environments
	function do_quicklatex_displayed_equations($m)
	{
		global $ql_atts;

		$formula_text =	trim($m[1]);

		if(substr($formula_text, 0, 1) == "!")
		{
			// Show source code if the first symbol	!
			return quicklatex_verbatim_text(substr($formula_text, 1));
		}else{

			// Compile it usual way otherwise
			return quicklatex_kernel($ql_atts,$formula_text);
		}
	}

	function do_quicklatex_inline_formulas($m)
	{
		global $ql_atts;

		$wrap_text    = trim($m[1]);
		$formula_text = trim($m[2]);

		if(substr($wrap_text, 0, 1) == "!")
		{
			// Show source code if the first symbol !, e.g:	!$ ... $
			return quicklatex_verbatim_text(substr($wrap_text, 1));
		}else{

			// Compile it usual way otherwise
			return quicklatex_kernel($ql_atts,$formula_text);
		}
	}

	// Handle $$..$$ and $$!..$$
	function do_quicklatex_doubledollars($m)
	{
		global $ql_atts;

		$wrap_text    = trim($m[1]); // = [!]$$formula_text$$
		$formula_text =	trim($m[2]);

		if(substr($wrap_text, 0, 1) == "!")
		{
			// Show source code if the first symbol !, e.g:	!$$ ... $$
			return quicklatex_verbatim_text(substr($wrap_text, 1));
		}else{

			// if $$!..$$ - skip the first symbol '!'
			if (substr($formula_text, 0, 1) == "!")
			{
				$formula_text = substr($formula_text, 1);
			}

			// Compile formula as displayed
			return quicklatex_kernel($ql_atts,'\\['.$formula_text.'\\]');
		}
	}

	// Handle [latex]...[/latex]
	function do_quicklatex_tags_proxy($m)
	{
		global $ql_atts, $ql_autoeqno;

	    // 1 - everything
		// 2 - first symbol
		// 3 - everything without first and last symbol
		// 4 - tag
		// 5 - params
		// 6 - enclosed text
		// 7 - last symbol

		// Do not trim it !!!!!!
		$wrap_text = $m[1];
		$first = $m[2];
		$subwrap_text = $m[3];
		$tag  = strtolower($m[4]);  // latex|tex|math|latexregion
		$attr = shortcode_parse_atts(quicklatex_sanitize_text($m[5]));
		$text = $m[6];
		$last = $m[7];

		if($first == "!")
		{
			// Show source code if the first symbol !, e.g:	![latex] ... [/latex]
			// Also needs to be converted into html codes for protection
			return quicklatex_verbatim_text($subwrap_text).$last;
		}

		if($first == '[' && $last == ']')
		{
			// Show source code without [latex] tags if [[latex] ... [/latex]]
			// Also needs to be converted into html codes for protection
			return quicklatex_verbatim_text($text);
		}

		if(!empty($attr['example']))
			if($attr['example']=='true')
			{
				// Show preformatted source code
				// Remove example=true
				$params	= preg_replace('/\s+example\s*=\s*true/', '', $m[5]);

				return '<pre>'.quicklatex_verbatim_text('['.$tag.$params.']'.$text.'[/'.$tag.']').'</pre>';
			}

		if($tag == 'latexregion')
		{
			// [latexregion] ... [/latexregion] encloses region with native LaTeX code

			// Process global parameters
			// eqno
			$autoeqno_backup = $ql_autoeqno;
			if(isset($attr['eqno']))
			{
				$ql_autoeqno = $attr['eqno'];
				unset($attr['eqno']);
			}

			// Set global attributes for the region
			$ql_atts = $attr;

			// Process embedded native LaTeX syntax, envs. one by one - no mixing
			$out = $first.quicklatex_native_syntax_parser($text,false).$last;

			// Return global params to default
			$ql_atts = null;
			$ql_autoeqno = $autoeqno_backup;

			return $out;
		}

		// Compatibility with
		// Compile everything inside [latex] ... [/latex] as one formula by default.
		// and do not parse for separate envs.
		// Helpful if someone wants to redefine standard evs., use non-math LaTeX before/after envs., etc.
		return $first.quicklatex_kernel($attr,$text).$last;
	}

	// ********************************************************
	// Utilities
	
	// Try to create and check if cache folder is writeable
	// Reference: use is_readable() to check readability
	function is_quicklatex_cache_writable($path)
	{
		// Check if cache directory exists
		if (false==file_exists($path))
		{
			// Try to create if it doesn't
			wp_mkdir_p($path);
		}
		return is_writable($path);
	}

	// Convert color to valid format. Extract only valid hex symbols,
	// add zeros so length to be of 6 symbols.
	function quicklatex_sanitize_color( $color )
	{
		$color = substr( preg_replace( '/[^0-9a-f]/i', '', $color ), 0, 6 );
		if ( 6 > $l = strlen($color) )
			$color .= str_repeat('0', 6 - $l );
		return $color;
	}

	// Taken from examples from the page
	// http://jp2.php.net/manual/en/function.html-entity-decode.php
	function quicklatex_unhtmlentities($string)
	{
		static $trans_tbl;

		// replace &nbsp; manually
		$string = str_replace("&nbsp;"," ",$string);
		
		// replace numeric entities
		$string = preg_replace_callback(
                '/&#x([0-9a-f]+);/i', 
                function($matches) { return quicklatex_unichr(hexdec($matches[1])); },
                $string
        );
                
		$string = preg_replace_callback(
                '/&#([0-9]+);/', 
                function($matches) { return quicklatex_unichr($matches[1]); },                
                $string
        );
		
		// replace other literal entities	
		if (!isset($trans_tbl))
		{
			$trans_tbl = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
			$trans_tbl = array_flip($trans_tbl);
		}

		return strtr($string, $trans_tbl);
	}

	// Miguel Perez's function
	// http://jp.php.net/manual/en/function.chr.php#77911
	function quicklatex_unichr($c)
	{
		if ($c <= 0x7F) {
			return chr($c);
		} else if ($c <= 0x7FF) {
			return chr(0xC0 | $c >> 6) . chr(0x80 | $c & 0x3F);
		} else if ($c <= 0xFFFF) {
			return chr(0xE0 | $c >> 12) . chr(0x80 | $c >> 6 & 0x3F)
										. chr(0x80 | $c & 0x3F);
		} else if ($c <= 0x10FFFF) {
			return chr(0xF0 | $c >> 18) . chr(0x80 | $c >> 12 & 0x3F)
										. chr(0x80 | $c >> 6 & 0x3F)
										. chr(0x80 | $c & 0x3F);
		} else {
			return false;
		}
	}

	// Strip html tags listed in $tags
	// http://www.php.net/manual/en/function.strip-tags.php#100054
	function quicklatex_strip_only_tags($str, $tags, $stripContent=false)
	{
		$content = '';
		if(!is_array($tags))
		{
			$tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
			if(end($tags) == '') array_pop($tags);
		}

		foreach($tags as $tag)
		{
			if ($stripContent)
				$content = '(.+</'.$tag.'(>|\s[^>]*>)|)';

			$str = preg_replace('#</?'.$tag.'(>|\s[^>]*>)'.$content.'#is', '', $str);
		}

		return $str;
	}

	// Prepare latex source code for alt 
	function quicklatex_alt_text($string)
	{
		$string = quicklatex_verbatim_text($string);
		
		// Remove all newlines since Wordpress replaces them with <br /> which breaks 
		// ALT attribute validation
		$string = preg_replace("/(\r?\n)/", " ", $string);		
		
		return $string;
	}

	// Prepare latex source code for output on the page
	function quicklatex_verbatim_text($string)
	{
		// Decode HTML entities (numeric or literal) to characters, e.g. &amp; to &.
		$string = quicklatex_unhtmlentities($string);

		// Encode everything (even ASCII) in HTML hex codes
		$string = quicklatex_utf8tohtml($string, true);
		
		return $string;
	}


	// Convert extended symbols to near-equivalent ASCII
	function quicklatex_utf2latex($string)
	{
			// We have string where all inacceptable characters (extended UTF-8) are
			// encoded as html numeric literals &#...
			// http://leftlogic.com/lounge/articles/entity-lookup/  -  best
			// quicklatex_unhtmlentities doesn't decode &nbsp; to ASCII 32  but rather to 0xa0
			$string = str_replace(
				array('&#8804;', '&#8805;', '&#8220;', '&#8221;', '&#039;', '&#8125;', '&#8127;', '&#8217;', '&#8216;', '&#038;', '&#8211;', "\xa0" ),
				array('\le',    '\ge ',      '``',      "''",       "'",      "'",       "'",       "'",	      "'",      '&',       "-",    ' ' ),
				$string
			);

			return $string;
	}

	// Sanitizes text to be acceptable for LaTeX
	// Goals are:
	// 1. convert extended Unicode symbols to near-equivalent ASCII suitable for LaTeX.
	// 2. convert HTML entities to symbols.
	// 3. strip selected HTML tags. We cannot use strip_tags since it also strips HTML comments
	//    <!-- --> which actually can be part of the LaTeX code (e.g. represent arrows in tikZ picture)
	function quicklatex_sanitize_text($string)
	{
		if($string != '')
		{
			// We need to replace any unicode character to near-equivalent ASCII to feed LaTeX.
			// Encode UTF-8 characters by hex codes - needed for further conversion
			$string = quicklatex_utf8tohtml($string, false);
			
			// Latex doesn't understand some fancy symbols 
			// inserted by WordPress as HTML numeric entities
			// Make sure they are not included in the formula text.
			// Add lines as needed using HTML symbol translation references:
			// http://www.htmlcodetutorial.com/characterentities_famsupp_69.html
			// http://www.ascii.cl/htmlcodes.htm
			// http://leftlogic.com/lounge/articles/entity-lookup/  -  best
			$string = quicklatex_utf2latex($string);
			
			// Decode HTML entities (numeric or literal) to characters, e.g. &amp; to &.
			$string = quicklatex_unhtmlentities($string);

			// Strip <br /> </p> tags. 
			// We cannot use strip_tags since it also strips HTML comments:
			// <!-- --> which actually can be part of the LaTeX code (e.g. represent arrows in tikZ picture) 
			$string = quicklatex_strip_only_tags($string,array('p','br'));
		}
		return $string;
	}

	// Simplified encoding of LaTeX code pieces
	// for transmission to server
	function quicklatex_encode($string)
	{

		$string = str_replace(
				array(     '%',    '&'),
				array(   '%25',  '%26'),
				$string
		);

		return $string;
	}

	// Replace any unicode character by their html codes (&#xxxx)
	// Encode ASCII symbols by parameter
	// http://www.php.net/manual/en/function.htmlentities.php#96648
	function quicklatex_utf8tohtml($utf8, $encodeASCII)
		{
			$result = '';
			for ($i = 0; $i < strlen($utf8); $i++) {
				$char = $utf8[$i];
				$ascii = ord($char);
				if($ascii < 32){
					// control codes - just copy
					$result .= $char;
				}else if ($ascii < 128) {
					// one-byte character
					$result .= ($encodeASCII) ? '&#'.$ascii.';' : $char;
				} else if ($ascii < 192) {
					// non-utf8 character or not a start byte
				} else if ($ascii < 224) {
					// two-byte character
					$result .= htmlentities(substr($utf8, $i, 2), ENT_QUOTES, 'UTF-8');
					$i++;
				} else if ($ascii < 240) {
					// three-byte character
					$ascii1 = ord($utf8[$i+1]);
					$ascii2 = ord($utf8[$i+2]);
					$unicode = (15 & $ascii) * 4096 +
							   (63 & $ascii1) * 64 +
							   (63 & $ascii2);
					$result .= "&#$unicode;";
					$i += 2;
				} else if ($ascii < 248) {
					// four-byte character
					$ascii1 = ord($utf8[$i+1]);
					$ascii2 = ord($utf8[$i+2]);
					$ascii3 = ord($utf8[$i+3]);
					$unicode = (15 & $ascii) * 262144 +
							   (63 & $ascii1) * 4096 +
							   (63 & $ascii2) * 64 +
							   (63 & $ascii3);
					$result .= "&#$unicode;";
					$i += 3;
				}
		}
		return $result;
	}
	
	function quicklatex_microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}	
	
	// Very simple bot detection
	// http://codytaylor.org/2009/06/detect-bots-by-parsing-the-user-agent-with-php.html
	// returns true if the user agent is a bot
	function quicklatex_is_bot($user_agent)
	{
	  //if no user agent is supplied then assume it's a bot
	  if($user_agent == "")
		return 1;

	  //array of bot strings to check for
	  $bot_strings = Array(  "google",     "bot",
							"yahoo",     "spider",
							"archiver",   "curl",
							"python",     "nambu",
							"twitt",     "perl",
							"sphere",     "PEAR",
							"java",     "wordpress",
							"radian",     "crawl",
							"yandex",     "eventbox",
							"monitor",   "mechanize",
							"facebookexternal"
						);
						
	  foreach($bot_strings as $bot)
	  {
		if(strpos($user_agent,$bot) !== false)
		{ return true; }
	  }
	  
	  return 0;
	}	
?>