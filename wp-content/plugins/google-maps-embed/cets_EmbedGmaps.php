<?php
/*
Plugin Name: Google Maps Embed
Plugin URI: 
Description: This plugin adds an icon to the Visual editor that allows a user to embed a Google Map into a post or page. Cooperative Extension Technology Services does not provide support for Google services.
Author: Deanna Schneider and Jason Lemahieu
Version: 1.13
Author URI: 


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class cets_EmbedGmaps {

	function __construct() {	
				
		// define URL
		define('cets_EmbedGmaps_ABSPATH', WP_PLUGIN_DIR.'/'.plugin_basename( dirname(__FILE__) ).'/' );
		define('cets_EmbedGmaps_URLPATH', WP_PLUGIN_URL.'/'.plugin_basename( dirname(__FILE__) ).'/' );
				
		include_once (dirname (__FILE__)."/lib/shortcodes.php");
		include_once (dirname (__FILE__)."/tinymce/tinymce.php");
					
	
	//add the quick tags and functions
	add_action( 'edit_form_advanced', array(&$this, 'AddQuicktagsAndFunctions') );
	add_action( 'edit_page_form', array(&$this, 'AddQuicktagsAndFunctions') );
	
	if ( in_array( basename($_SERVER['PHP_SELF']), apply_filters( 'vvq_editor_pages', array('post-new.php', 'page-new.php', 'post.php', 'page.php') ) ) ) {
				
				add_action('admin_init', array(&$this, 'adminRegisterScripts' ) );
				add_action('admin_enqueue_scripts', array(&$this, 'adminEnqueueScripts') );
				
				add_action( 'admin_head', array(&$this, 'EditorCSS') );
				add_action( 'admin_footer', array(&$this, 'OutputjQueryDialogDiv') );
		}
	}
	
	function adminRegisterScripts() {
		wp_register_script( 'jquery-ui-draggable', plugins_url('/google-maps-embed/lib/jquery-ui/ui.draggable.js'), array('jquery-ui-core'), '1.5.2' );
		wp_register_script( 'jquery-ui-resizable', plugins_url('/google-maps-embed/lib/jquery-ui/ui.resizable.js'), array('jquery-ui-core'), '1.5.2' );
		wp_register_script( 'jquery-ui-dialog', plugins_url('/google-maps-embed/lib/jquery-ui/ui.dialog.js'), array('jquery-ui-core'), '1.5.2' );
				
		wp_register_style( 'cets-jquery-ui-css', plugins_url('/google-maps-embed/lib/jquery-ui/cets-jquery-ui.css'), array(), false, 'screen' );
	}
	
	function adminEnqueueScripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-resizable' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		
		wp_enqueue_style( 'cets-jquery-ui-css' );
	}
		
	// all the quick tag stuff is pretty much stolen from Viper. He rocks.
	function addQuicktagsAndFunctions() {		
	
	// Set defaults
		$width = 350;
		$height = 425;
		$marginwidth = 0;
		$marginheight = 0;
		$frameborder = 0;
		$scrolling = 'no';
		
	
	// This is the tiny mce button stuff
		$types = array(
			'cetsEmbedGmap' => array(
			__('Embed Gmap', 'cets_EmbedGmaps'),
			__('Embed a Google map', 'cets_EmbedGmaps'),
			__('Please enter the link for the Google Map', 'cets_EmbedGmaps'),
			'http://maps.google.com/?ie=UTF8&ll=37.0625,-95.677068&spn=53.609468,107.138672&z=4'
			)
			
		);
		
		
		
	$buttonshtml = $datajs = '';
		foreach ( $types as $type => $strings ) {
			// HTML for quicktag button
			$buttonshtml .= '<input type="button" class="ed_button" onclick="cets_GEButtonClick(\'' . $type . '\')" title="' . $strings[1] . '" value="' . $strings[0] . '" />';

			// Create the data array
			$datajs .= "	cets_GEData['$type'] = {\n";
			$datajs .= '		title: "' . $this->esc_js( ucwords( $strings[1] ) ) . '",' . "\n";
			$datajs .= '		instructions: "' . $this->esc_js( $strings[2] ) . '",' . "\n";
			$datajs .= '		example: "' . esc_js( $strings[3] ) . '"';
			$datajs .= ",\n		width: " . $width . ",\n";
			$datajs .= "		height: " . $height .",\n";
			$datajs .= "		marginwidth: " . $marginwidth . ",\n";
			$datajs .= "		marginheight: " . $marginheight . ",\n";
			$datajs .= "		frameborder: " . $frameborder . ",\n";
			$datajs .= "        scrolling: '" . $scrolling . "'";
			
			$datajs .= "\n	};\n";
		}	
		
		?>
	<script type="text/javascript">
	// <![CDATA[
		// Video data
		var cets_GEData = {};
	<?php echo $datajs; ?>
		
		// Set default heights (IE sucks)
		if ( jQuery.browser.msie ) {
			var cets_GEDialogDefaultHeight = 289;
			var cets_GEDialogDefaultExtraHeight = 300;
		} else {
			var cets_GEDialogDefaultHeight = 258;
			var cets_GEDialogDefaultExtraHeight = 300;
		}
		
		// This function is run when a button is clicked. It creates a dialog box for the user to input the data.
		function cets_GEButtonClick( tag ) {
			
			// Close any existing copies of the dialog
			cets_GEDialogClose();
	
			// Calculate the height/maxHeight (i.e. add some height for Blip.tv)
			cets_GEDialogHeight = cets_GEDialogDefaultHeight;
			cets_GEDialogMaxHeight = cets_GEDialogDefaultHeight + cets_GEDialogDefaultExtraHeight;
			
	
			// Open the dialog while setting the width, height, title, buttons, etc. of it
			var buttons = { "<?php echo esc_js('Okay', 'cets_EmbedGmaps'); ?>": cets_GEButtonOkay, "<?php echo esc_js('Cancel', 'cets_EmbedGmaps'); ?>": cets_GEDialogClose };
			var title = cets_GEData[tag]["title"];
			
			jQuery("#cets_GE-dialog").dialog({ autoOpen: false, width: 750, minWidth: 750, height: cets_GEDialogHeight, minHeight: cets_GEDialogHeight, maxHeight: cets_GEDialogMaxHeight, title: title, buttons: buttons, resize: cets_GEDialogResizing });
			
			// Reset the dialog box incase it's been used before
			jQuery("#cets_GE-dialog-slide-header").removeClass("selected");
			jQuery("#cets_GE-dialog-input").val("");
			jQuery("#cets_GE-dialog-tag").val(tag);
	
			// Set the instructions
			jQuery("#cets_GE-dialog-message").html("<p>" + cets_GEData[tag]["instructions"] + "</p><p><strong><?php echo esc_js( __('Example:', 'cets_EmbedGmaps') ); ?></strong></p><p><code>" + cets_GEData[tag]["example"] + "</code></p>");
	
			// Style the jQuery-generated buttons by adding CSS classes and add second CSS class to the "Okay" button
			jQuery(".ui-dialog button").addClass("button").each(function(){
				if ( "<?php echo esc_js('Okay', 'cets_EmbedGmaps'); ?>" == jQuery(this).html() ) jQuery(this).addClass("button-highlighted");
			});
	
			// Hide the Dimensions box if we can't add dimensions
			if ( cets_GEData[tag]["width"] ) {
				jQuery(".cets_GE-dialog-slide").removeClass("hidden");
				jQuery("#cets_GE-dialog-width").val(cets_GEData[tag]["width"]);
				jQuery("#cets_GE-dialog-height").val(cets_GEData[tag]["height"]);
				jQuery("#cets_GE-dialog-marginheight").val(cets_GEData[tag]["marginheight"]);
				jQuery("#cets_GE-dialog-marginwidth").val(cets_GEData[tag]["marginwidth"]);
				jQuery("#cets_GE-dialog-frameborder").val(cets_GEData[tag]["frameborder"]);
				jQuery("#cets_GE-dialog-scrolling").val(cets_GEData[tag]["scrolling"]);
				
			} else {
				jQuery(".cets_GE-dialog-slide").addClass("hidden");
				jQuery(".cets_GE-dialog-dim").val("");
			}
	
			// Do some hackery on any links in the message -- jQuery(this).click() works weird with the dialogs, so we can't use it
			jQuery("#cets_GE-dialog-message a").each(function(){
				jQuery(this).attr("onclick", 'window.open( "' + jQuery(this).attr("href") + '", "_blank" );return false;' );
			});
	
			// Show the dialog now that it's done being manipulated
			jQuery("#cets_GE-dialog").dialog("open");
			
			
			// Focus the input field
			jQuery("#cets_GE-dialog-input").focus();
		}
	
		// Close + reset
		function cets_GEDialogClose() {
			jQuery(".ui-dialog").height(cets_GEDialogDefaultHeight);
			
			if (jQuery('#cets_GE-dialog').dialog('isOpen') ) {
				jQuery("#cets_GE-dialog").dialog("close");
			}
		}
	
		// Callback function for the "Okay" button
		function cets_GEButtonOkay() {
	
			var tag = jQuery("#cets_GE-dialog-tag").val();
			var text = jQuery("#cets_GE-dialog-input").val();
			var width = jQuery("#cets_GE-dialog-width").val();
			var height = jQuery("#cets_GE-dialog-height").val();
			var marginheight = jQuery("#cets_GE-dialog-marginheight").val();
			var marginwidth = jQuery("#cets_GE-dialog-marginwidth").val();
			var frameborder = jQuery("#cets_GE-dialog-frameborder").val();
			var scrolling = jQuery("#cets_GE-dialog-scrolling").val();
			
			if ( !tag || !text ) return cets_GEDialogClose();
	
			// Create the shortcode here
			var text = "[" + tag + " src=" + text + " width=" + width + " height=" + height + " marginwidth=" + marginwidth + " marginheight=" + marginheight + " frameborder=" + frameborder +  " scrolling=" + scrolling + "]";
			
	
			if ( typeof tinyMCE != 'undefined' && ( ed = tinyMCE.activeEditor ) && !ed.isHidden() ) {
				ed.focus();
				if (tinymce.isIE)
					ed.selection.moveToBookmark(tinymce.EditorManager.activeEditor.windowManager.bookmark);
	
				ed.execCommand('mceInsertContent', false, text);
			} else
				edInsertContent(edCanvas, text);
	
			cets_GEDialogClose();
		}
	
		// This function is called while the dialog box is being resized.
		function cets_GEDialogResizing( test ) {
			if ( jQuery(".ui-dialog").height() > cets_GEDialogHeight ) {
				jQuery("#cets_GE-dialog-slide-header").addClass("selected");
			} else {
				jQuery("#cets_GE-dialog-slide-header").removeClass("selected");
			}
		}
	
		// On page load...
		jQuery(document).ready(function(){
			// Add the buttons to the HTML view 
			var buttonshtml = '<input type=\"button\" class=\"ed_button\" onclick=\"cets_GEButtonClick(\'cetsEmbedGmap\')\" title=\"Embed a Google map\" value=\"Embed Gmap\" />';
			
			//jQuery("#ed_toolbar").append(<?php echo $this->esc_js( $buttonshtml ); ?>);
			jQuery("#ed_toolbar").append(buttonshtml);
			
			// Make the "Dimensions" bar adjust the dialog box height
			jQuery("#cets_GE-dialog-slide-header").click(function(){
				if ( jQuery(this).hasClass("selected") ) {
					jQuery(this).removeClass("selected");
					jQuery(this).parents(".ui-dialog").animate({ height: cets_GEDialogHeight });
				} else {
					jQuery(this).addClass("selected");
					jQuery(this).parents(".ui-dialog").animate({ height: cets_GEDialogMaxHeight });
				}
			});
	
			// If the Enter key is pressed inside an input in the dialog, do the "Okay" button event
			jQuery("#cets_GE-dialog :input").keyup(function(event){
				if ( 13 == event.keyCode ) // 13 == Enter
					cets_GEButtonOkay();
			});
	
			// Make help links open in a new window to avoid loosing the post contents
			jQuery("#cets_GE-dialog-slide a").each(function(){
				jQuery(this).click(function(){
					window.open( jQuery(this).attr("href"), "_blank" );
					return false;
				});
			});

			jQuery('#cets_GE-dialog').dialog({ autoOpen: false });
		});
	// ]]>
	</script>
	<?php
	} //end addquicktags function
	
	// WordPress' esc_js() won't allow <, >, or " -- instead it converts it to an HTML entity. This is a "fixed" function that's used when needed.
	function esc_js($text) {
		$safe_text = addslashes($text);
		$safe_text = preg_replace('/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes($safe_text));
		$safe_text = preg_replace("/\r?\n/", "\\n", addslashes($safe_text));
		$safe_text = str_replace('\\\n', '\n', $safe_text);
		return apply_filters('esc_js', $safe_text, $text);
	}
		
	
	
	function OutputjQueryDialogDiv() { ?>
		<div class="hidden">
			<div id="cets_GE-dialog">
				<div class="cets_GE-dialog-content">
					<div id="cets_GE-dialog-message"></div>
					<p><input type="text" id="cets_GE-dialog-input" style="width:98%" /></p>
					<input type="hidden" id="cets_GE-dialog-tag" />
				</div>
				<div id="cets_GE-dialog-slide-header" class="cets_GE-dialog-slide ui-dialog-titlebar"><?php _e('Dimensions', 'cets_gmapsEmbed'); ?></div>
				<div id="cets_GE-dialog-slide" class="cets_GE-dialog-slide cets_GE-dialog-content">
					
					<p>Width: <input type="text" id="cets_GE-dialog-width" class="cets_GE-dialog-dim" style="width:50px" /> <br/>
					Height: <input type="text" id="cets_GE-dialog-height" class="cets_GE-dialog-dim" style="width:50px" /> pixels</p>
					
					<p>
					Margin Width: <input type="text" id="cets_GE-dialog-marginwidth" class="cets_GE-dialog-dim" style="width:50px" /> <br/>
					Margin Height: <input type="text" id="cets_GE-dialog-marginheight" class="cets_GE-dialog-dim" style="width:50px" /> pixels		
					</p>
					
					<p>
						Frameborder: <input type="text" id="cets_GE-dialog-frameborder" class="cets_GE-dialog-dim" style="width:50px" />
					</p>
					<p>
					Should the iframe scroll? <select name="cets_GE-dialog-scrolling" id="cets_GE-dialog-scrolling">
	            <option value="no" SELECTED>No</option>
				<option value="yes" >Yes</option>
				<option value="auto" >Auto</option>
            </select>
					</p>
					
				</div>
				</div>
			</div>
		</div>
		
		<?php
	}
	
	function EditorCSS() {
		echo "<style type='text/css'>\n	#cets_GE-precacher { display: none; }\n";

		// Attempt to match the dialog box to the admin colors
		$color = ( 'classic' == get_user_option('admin_color', $user_id) ) ? '#CFEBF7' : '#EAF3FA';
		$color = apply_filters( 'cets_GE_titlebarcolor', $color ); // Use this hook for custom admin colors
		echo "	.ui-dialog-titlebar { background: $color; }\n";
		echo "</style>\n";
	}
	
	
}// end embed_Gmaps class
	

// Start this plugin once all other plugins are fully loaded
add_action( 'plugins_loaded', 'cets_embed_gmaps_init' );
function cets_embed_gmaps_init() {
	$cets_EmbedGmaps = new cets_EmbedGmaps();
}
