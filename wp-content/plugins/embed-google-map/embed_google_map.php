<?php
/*
Plugin Name: Embed Google Map
Plugin URI: http://pkrete.com/wp/plugins/embed-google-map/v32.html
Description: Embed Google Map is a plugin for embedding one or more Google Maps to WordPress pages, posts, text widgets and templates.
Author: Petteri Kivim&auml;ki
Version: 3.2
Author URI: http://www.linkedin.com/in/pkivimaki
*/

/* Register WordPress hooks - Frontend */

// Run the function on post content prior to printing on the screen
add_filter('the_content', 'the_google_map_embedder');
// Run the function on Text Widget content prior to printing on the screen
add_filter('widget_text', 'the_google_map_embedder');
// Register short code
add_shortcode( 'google_map', 'google_map_shortcode' );

/* Register WordPress hooks - Backend */

if ( is_admin() ) {
	// Add link to the Settings menu
	add_action('admin_menu', 'embed_google_map_create_menu');

	// Add link to settings in the Plugins page
	add_filter('plugin_action_links','embed_google_map_plugin_actions', 10, 2);
}

/* Frontend functions */	

function google_map_shortcode( $atts, $content ) {
	// If $atts is not empty, loop through the attributes
	if (!empty($atts)) {
		// Append attributes to content
		foreach($atts as $key => $value) {
    		$content .= "|$key:$value";
		}
	}
	// Wrap content inside {google_map} tags
	$content = "{google_map}$content{/google_map}";
	// Process the content
	return the_google_map_embedder($content);
}

function the_google_map_embedder($content) {

	// Regex for finding all the google_map tags
	$regex = "#{google_map}(.*?){/google_map}#s";
	// Read all the tags in an array
	$found = preg_match_all($regex, $content, $matches);
	// Get default options
	$options = get_option('embed_google_map_options');
	// Initialize options
	init_embed_google_map_options($options);
	
	// Check if any matches were found
	if ( $found ) {
	     // Loop through all the matches
		foreach ( $matches[0] as $value ) {
			// Plugin params
			$plgParams = new EmbedGoogleMapParameters;		
			// Set default parameters
			$plgParams->setVersion($options['version']);
			$plgParams->setEmbedAPIKey($options['embed_api_key']);			
			$plgParams->setMapType($options['map_type']);
			$plgParams->setZoomLevel($options['zoom_level']);
			$plgParams->setLanguage($options['language']);
			$plgParams->setAddLink($options['add_link']);
			$plgParams->setLinkLabel($options['link_label']);
			$plgParams->setLinkFull($options['link_full']);
			$plgParams->setShowInfo($options['show_info']);
			$plgParams->setHeight($options['height']);
			$plgParams->setWidth($options['width']);
			$plgParams->setBorder($options['border']);
			$plgParams->setBorderStyle($options['border_style']);
			$plgParams->setBorderColor($options['border_color']);
			$plgParams->setHttps($options['https']);
			$plgParams->setInfoLabel($options['info_label']);	
									
			$map = $value;
			$map = str_replace('{google_map}','', $map);
			$map = str_replace('{/google_map}','', $map);
			$find = '|';

			// Check parameters
			if( strstr($map, $find) ) {
				// New Parser object
				$parser = new EmbedGoogleMapParser;
				// Parse parameters
				$parser->parse($map, $plgParams);
			} else {
				$plgParams->setAddress($map);
			}
			
			// Create new HTML builder
			$builder = EmbedGoogleMapBuilderFactory::createBuilder($plgParams->getVersion());
			// Generate HTML code
			$replacement = $builder->buildHtml($plgParams);
			// Replace the tag with the html code that embeds the map
			$content = str_replace($value, $replacement, $content);
		}
	}
	return $content;
}

/* Backend functions */	

function embed_google_map_create_menu() {
	// Add link to the Settings menu
    add_options_page('Embed Google Maps Options', 'Embed Google Map', 'manage_options', 'embed_google_map.php', 'embed_google_map_page');

	//call add action function
	add_action( 'admin_init', 'register_embed_google_map_settings' );
}

function register_embed_google_map_settings() {
	//register settings
	register_setting( 'embed_google_map-settings-group', 'embed_google_map_options', 'embed_google_map_options_validate' );
}

function embed_google_map_page() {
	screen_icon();
	?>
	<div class="wrap">
    <h2>Embed Google Map Settings</h2>
	<p>
		Embed Google Map is a plugin for embedding one or more Google Maps to WordPress posts, pages, text widgets and templates. Adding maps is very simple, just add the address or the coordinates which location you want to show an a map inside google_map tags to a post or a page or a text widget or a template, and that's it!
	</p>
	<p>
		The plugin supports Google Maps, Google Maps Classic and Google Maps Embed API. The version to be used can be set by using the Version setting (supported values: new, classic, embed). Google Maps and Google Maps Classic do not require an API key, but for Google Maps Embed API an API key is required instead. Not all the parameters are supported by all the versions. Please see the supported parameters below.
	</p>
	<p>
		It's possible to define the version of Google Maps, the type of the map (normal, satellite, hybrid, terrain), the size of the map, the language of the Google Maps interface, zoom level, border width, border color, border style, link to the full size map, custom labels and hide/show the info label. Both HTTP and HTTPS protocols are supported. The settings defined in this page are the default settings used for all the maps in the site, and they can be overridden for individual maps.
	</p>
	<form method="post" action="options.php">
		<?php 
			settings_fields('embed_google_map-settings-group');
			$options = get_option('embed_google_map_options');
			init_embed_google_map_options($options);
		?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><span title="Google Maps version.">Version:</span></th>
                <td>
					<select name="embed_google_map_options[version]">
						<option value="new" <?php echo ($options['version'] == "new") ? 'selected="selected"' : ''; ?>>Google Maps</option>
						<option value="classic" <?php echo ($options['version'] == "classic") ? 'selected="selected"' : ''; ?>>Google Maps Classic</option>
						<option value="embed" <?php echo ($options['version'] == "embed") ? 'selected="selected"' : ''; ?>>Google Maps Embed API</option>
					</select>
				</td>
			</tr>		
			<tr valign="top">
				<th scope="row"><span title="Map type.">Map type:</span></th>
                <td>
					<select name="embed_google_map_options[map_type]">
						<option value="m" <?php echo ($options['map_type'] == "m") ? 'selected="selected"' : ''; ?>>Normal map</option>
						<option value="k" <?php echo ($options['map_type'] == "k") ? 'selected="selected"' : ''; ?>>Satellite</option>
						<option value="h" <?php echo ($options['map_type'] == "h") ? 'selected="selected"' : ''; ?>>Hybrid</option>
						<option value="p" <?php echo ($options['map_type'] == "p") ? 'selected="selected"' : ''; ?>>Terrain</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><span title="Zoom level.">Zoom level:</span></th>
                <td>
					<select name="embed_google_map_options[zoom_level]">
						<option value="0" <?php echo ($options['zoom_level'] == "0") ? 'selected="selected"' : ''; ?>>0</option>
						<option value="1" <?php echo ($options['zoom_level'] == "1") ? 'selected="selected"' : ''; ?>>1</option>
                        <option value="2" <?php echo ($options['zoom_level'] == "2") ? 'selected="selected"' : ''; ?>>2</option>
                        <option value="3" <?php echo ($options['zoom_level'] == "3") ? 'selected="selected"' : ''; ?>>3</option>
                        <option value="4" <?php echo ($options['zoom_level'] == "4") ? 'selected="selected"' : ''; ?>>4</option>
                        <option value="5" <?php echo ($options['zoom_level'] == "5") ? 'selected="selected"' : ''; ?>>5</option>
                        <option value="6" <?php echo ($options['zoom_level'] == "6") ? 'selected="selected"' : ''; ?>>6</option>
                        <option value="7" <?php echo ($options['zoom_level'] == "7") ? 'selected="selected"' : ''; ?>>7</option>
                        <option value="8" <?php echo ($options['zoom_level'] == "8") ? 'selected="selected"' : ''; ?>>8</option>
                        <option value="9" <?php echo ($options['zoom_level'] == "9") ? 'selected="selected"' : ''; ?>>9</option>
                        <option value="10" <?php echo ($options['zoom_level'] == "10") ? 'selected="selected"' : ''; ?>>10</option>
                        <option value="11" <?php echo ($options['zoom_level'] == "11") ? 'selected="selected"' : ''; ?>>11</option>
                        <option value="12" <?php echo ($options['zoom_level'] == "12") ? 'selected="selected"' : ''; ?>>12</option>
                        <option value="13" <?php echo ($options['zoom_level'] == "13") ? 'selected="selected"' : ''; ?>>13</option>
                        <option value="14" <?php echo ($options['zoom_level'] == "14" || !isset($options['zoom_level'])) ? 'selected="selected"' : ''; ?>>14</option>
                        <option value="15" <?php echo ($options['zoom_level'] == "15") ? 'selected="selected"' : ''; ?>>15</option>
                        <option value="16" <?php echo ($options['zoom_level'] == "16") ? 'selected="selected"' : ''; ?>>16</option>
                        <option value="17" <?php echo ($options['zoom_level'] == "17") ? 'selected="selected"' : ''; ?>>17</option>
                        <option value="18" <?php echo ($options['zoom_level'] == "18") ? 'selected="selected"' : ''; ?>>18</option>
                        <option value="19" <?php echo ($options['zoom_level'] == "29") ? 'selected="selected"' : ''; ?>>19</option>
                        <option value="20" <?php echo ($options['zoom_level'] == "20") ? 'selected="selected"' : ''; ?>>20</option>
                        <option value="21" <?php echo ($options['zoom_level'] == "21") ? 'selected="selected"' : ''; ?>>21</option>						
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><span title="Language.">Language:</span></th>
				<td>
					<select name="embed_google_map_options[language]">
						<option value="-">Undefined</option>
						<option value="ar"<?php echo ($options['language'] == "ar") ? 'selected="selected"' : ''; ?>>Arabic</option>
						<option value="eu" <?php echo ($options['language'] == "eu") ? 'selected="selected"' : ''; ?>>Basque</option>
						<option value="bn" <?php echo ($options['language'] == "bn") ? 'selected="selected"' : ''; ?>>Bengali</option>
						<option value="bg" <?php echo ($options['language'] == "bg") ? 'selected="selected"' : ''; ?>>Bulgarian</option>
						<option value="ca" <?php echo ($options['language'] == "ca") ? 'selected="selected"' : ''; ?>>Catalan</option>
						<option value="zh-CN" <?php echo ($options['language'] == "zh-CN") ? 'selected="selected"' : ''; ?>>Chinese (simplified)</option>
						<option value="zh-TW" <?php echo ($options['language'] == "zh-TW") ? 'selected="selected"' : ''; ?>>Chinese (traditional)</option>
						<option value="hr" <?php echo ($options['language'] == "hr") ? 'selected="selected"' : ''; ?>>Croatian</option>
						<option value="cs" <?php echo ($options['language'] == "cs") ? 'selected="selected"' : ''; ?>>Czech</option>
						<option value="da" <?php echo ($options['language'] == "da") ? 'selected="selected"' : ''; ?>>Danish</option>
						<option value="nl" <?php echo ($options['language'] == "nl") ? 'selected="selected"' : ''; ?>>Dutch</option>
						<option value="en" <?php echo ($options['language'] == "en") ? 'selected="selected"' : ''; ?>>English</option>
						<option value="en-AU" <?php echo ($options['language'] == "en-AU") ? 'selected="selected"' : ''; ?>>English (Australian)</option>
						<option value="en-GB" <?php echo ($options['language'] == "en-GB") ? 'selected="selected"' : ''; ?>>English (Great Britain)</option>
						<option value="fa" <?php echo ($options['language'] == "fa") ? 'selected="selected"' : ''; ?>>Farsi</option>
						<option value="fil" <?php echo ($options['language'] == "fil") ? 'selected="selected"' : ''; ?>>Filipino</option>
						<option value="fi" <?php echo ($options['language'] == "fi") ? 'selected="selected"' : ''; ?>>Finnish</option>
						<option value="fr" <?php echo ($options['language'] == "fr") ? 'selected="selected"' : ''; ?>>French</option>
						<option value="gl" <?php echo ($options['language'] == "gl") ? 'selected="selected"' : ''; ?>>Galician</option>
						<option value="de" <?php echo ($options['language'] == "de") ? 'selected="selected"' : ''; ?>>German</option>
						<option value="el" <?php echo ($options['language'] == "el") ? 'selected="selected"' : ''; ?>>Greek</option>
						<option value="gu" <?php echo ($options['language'] == "gu") ? 'selected="selected"' : ''; ?>>Gujarati</option>
						<option value="iw" <?php echo ($options['language'] == "iw") ? 'selected="selected"' : ''; ?>>Hebrew</option>
						<option value="hi" <?php echo ($options['language'] == "hi") ? 'selected="selected"' : ''; ?>>Hindi</option>
						<option value="hu" <?php echo ($options['language'] == "hu") ? 'selected="selected"' : ''; ?>>Hungarian</option>
						<option value="id" <?php echo ($options['language'] == "id") ? 'selected="selected"' : ''; ?>>Indonesian</option>
						<option value="it" <?php echo ($options['language'] == "it") ? 'selected="selected"' : ''; ?>>Italian</option>
						<option value="ja" <?php echo ($options['language'] == "ja") ? 'selected="selected"' : ''; ?>>Japanese</option>
						<option value="kn" <?php echo ($options['language'] == "kn") ? 'selected="selected"' : ''; ?>>Kannada</option>
						<option value="ko" <?php echo ($options['language'] == "ko") ? 'selected="selected"' : ''; ?>>Korean</option>
						<option value="lv" <?php echo ($options['language'] == "lv") ? 'selected="selected"' : ''; ?>>Latvian</option>
						<option value="lt" <?php echo ($options['language'] == "lt") ? 'selected="selected"' : ''; ?>>Lithuanian</option>
						<option value="ml" <?php echo ($options['language'] == "ml") ? 'selected="selected"' : ''; ?>>Malayalam</option>
						<option value="mr" <?php echo ($options['language'] == "mr") ? 'selected="selected"' : ''; ?>>Marathi</option>
						<option value="no" <?php echo ($options['language'] == "no") ? 'selected="selected"' : ''; ?>>Norwegian</option>
						<option value="nn" <?php echo ($options['language'] == "nn") ? 'selected="selected"' : ''; ?>>Norwegian Nynorsk</option>
						<option value="or" <?php echo ($options['language'] == "or") ? 'selected="selected"' : ''; ?>>Oriya</option>
						<option value="pl" <?php echo ($options['language'] == "pl") ? 'selected="selected"' : ''; ?>>Polish</option>
						<option value="pt" <?php echo ($options['language'] == "pt") ? 'selected="selected"' : ''; ?>>Portuguese</option>
						<option value="pt-BR" <?php echo ($options['language'] == "pt-BR") ? 'selected="selected"' : ''; ?>>Portuguese (Brazil)</option>
						<option value="pt-PT" <?php echo ($options['language'] == "pt-PT") ? 'selected="selected"' : ''; ?>>Portuguese (Portugal)</option>
						<option value="ro" <?php echo ($options['language'] == "ro") ? 'selected="selected"' : ''; ?>>Romanian</option>
						<option value="rm" <?php echo ($options['language'] == "rm") ? 'selected="selected"' : ''; ?>>Romansch</option>
						<option value="ru" <?php echo ($options['language'] == "ru") ? 'selected="selected"' : ''; ?>>Russian</option>
						<option value="sk" <?php echo ($options['language'] == "sk") ? 'selected="selected"' : ''; ?>>Slovak</option>
						<option value="sl" <?php echo ($options['language'] == "sl") ? 'selected="selected"' : ''; ?>>Slovenian</option>
						<option value="sr" <?php echo ($options['language'] == "sr") ? 'selected="selected"' : ''; ?>>Serbian</option>
						<option value="es" <?php echo ($options['language'] == "es") ? 'selected="selected"' : ''; ?>>Spanish</option>
						<option value="sv" <?php echo ($options['language'] == "sv") ? 'selected="selected"' : ''; ?>>Swedish</option>
						<option value="tl" <?php echo ($options['language'] == "tl") ? 'selected="selected"' : ''; ?>>Tagalog</option>
						<option value="ta" <?php echo ($options['language'] == "ta") ? 'selected="selected"' : ''; ?>>Tamil</option>
						<option value="te" <?php echo ($options['language'] == "te") ? 'selected="selected"' : ''; ?>>Telugu</option>
						<option value="th" <?php echo ($options['language'] == "th") ? 'selected="selected"' : ''; ?>>Thai</option>
						<option value="tr" <?php echo ($options['language'] == "tr") ? 'selected="selected"' : ''; ?>>Turkish</option>
						<option value="uk" <?php echo ($options['language'] == "uk") ? 'selected="selected"' : ''; ?>>Ukrainian</option>
						<option value="vi" <?php echo ($options['language'] == "vi") ? 'selected="selected"' : ''; ?>>Vietnamese</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><span title="Add link to Google Maps.">Add link:</span></th>
				<td><input name="embed_google_map_options[add_link]" type="checkbox" value="1" <?php checked('1', $options['add_link']); ?> /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><span title="Link label.">Link label:</span></th>
				<td><input type="text" name="embed_google_map_options[link_label]" value="<?php echo $options['link_label']; ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><span title="Open link in full screen mode.">Link full:</span></th>
				<td><input name="embed_google_map_options[link_full]" type="checkbox" value="1" <?php checked('1', $options['link_full']); ?> /></td>
			</tr>			
			<tr valign="top">
				<th scope="row"><span title="Show info label.">Show info:</span></th>
				<td><input name="embed_google_map_options[show_info]" type="checkbox" value="1" <?php checked('1', $options['show_info']); ?> /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><span title="Custom info label.">Info label:</span></th>
				<td><input type="text" name="embed_google_map_options[info_label]" value="<?php echo $options['info_label']; ?>" /></td>
			</tr>		
			<tr valign="top">
				<th scope="row"><span title="Default height.">Height:</span></th>
				<td><input type="text" name="embed_google_map_options[height]" value="<?php echo $options['height']; ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><span title="Default width.">Width:</span></th>
				<td><input type="text" name="embed_google_map_options[width]" value="<?php echo $options['width']; ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><span title="Frame border width.">Border:</span></th>
				<td>
					<select name="embed_google_map_options[border]">
						<option value="0" <?php echo ($options['border'] == "0") ? 'selected="selected"' : ''; ?>>0</option>
						<option value="1" <?php echo ($options['border'] == "1") ? 'selected="selected"' : ''; ?>>1</option>
						<option value="2" <?php echo ($options['border'] == "2") ? 'selected="selected"' : ''; ?>>2</option>
						<option value="3" <?php echo ($options['border'] == "3") ? 'selected="selected"' : ''; ?>>3</option>
						<option value="4" <?php echo ($options['border'] == "4") ? 'selected="selected"' : ''; ?>>4</option>
						<option value="5" <?php echo ($options['border'] == "5") ? 'selected="selected"' : ''; ?>>5</option>
						<option value="6" <?php echo ($options['border'] == "6") ? 'selected="selected"' : ''; ?>>6</option>
						<option value="7" <?php echo ($options['border'] == "7") ? 'selected="selected"' : ''; ?>>7</option>
						<option value="8" <?php echo ($options['border'] == "8") ? 'selected="selected"' : ''; ?>>8</option>
						<option value="9" <?php echo ($options['border'] == "9") ? 'selected="selected"' : ''; ?>>9</option>
						<option value="10" <?php echo ($options['border'] == "10") ? 'selected="selected"' : ''; ?>>10</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><span title="Frame border style.">Border style:</span></th>
                <td>
					<select name="embed_google_map_options[border_style]">
						<option value="none" <?php echo ($options['border_style'] == "none") ? 'selected="selected"' : ''; ?>>None</option>
						<option value="hidden" <?php echo ($options['border_style'] == "hidden") ? 'selected="selected"' : ''; ?>>Hidden</option>
						<option value="dotted" <?php echo ($options['border_style'] == "dotted") ? 'selected="selected"' : ''; ?>>Dotted</option>
						<option value="dashed" <?php echo ($options['border_style'] == "dashed") ? 'selected="selected"' : ''; ?>>Dashed</option>
						<option value="solid" <?php echo ($options['border_style'] == "solid") ? 'selected="selected"' : ''; ?>>Solid</option>
						<option value="double" <?php echo ($options['border_style'] == "double") ? 'selected="selected"' : ''; ?>>Double</option>
					</select>
				</td>
			</tr>	
			<tr valign="top">
				<th scope="row"><span title="Frame border color in hexadecimal format.">Border color:</span></th>
				<td><input type="text" name="embed_google_map_options[border_color]" value="<?php echo $options['border_color']; ?>" /></td>
			</tr>	
			<tr valign="top">
				<th scope="row"><span title="Use HTTPS protocol.">HTTPS:</span></th>
				<td><input name="embed_google_map_options[https]" type="checkbox" value="1" <?php checked('1', $options['https']); ?> /></td>
			</tr>	
			<tr valign="top">
				<th scope="row"><span title="Google Maps Embed API key. Required only when Google Maps Embed API version is used.">Embed API key:</span></th>
				<td><input type="text" name="embed_google_map_options[embed_api_key]" value="<?php echo $options['embed_api_key']; ?>" /></td>
			</tr>			
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
		
	<h3>Basic Usage</h3>
	<p>To embed a map in a post or a page use the following code:</p>
	<ul>
		<li>{google_map}address{/google_map}</li>
	</ul>
	<h3>Overriding default settings</h3>
	<p>To override one or more default settings use the following code:</p>
	<ul>	
		<li>{google_map}address{/google_map}</li>
		<li>{google_map}address|version:classic{/google_map}</li>
		<li>{google_map}address|zoom:10{/google_map}</li>
		<li>{google_map}address|zoom:10|lang:it{/google_map}</li>
		<li>{google_map}address|width:200|height:200|border:1|border_style:solid|border_color:#000000{/google_map}</li>
		<li>{google_map}address|width:200|height:200|link:yes|link_label:Label{/google_map}</li>
		<li>{google_map}address|link:yes{/google_map}</li>
		<li>{google_map}address|type:satellite{/google_map}</li>
		<li>{google_map}address|show_info:yes|info_label:Label{/google_map}</li>
		<li>{google_map}address|link_full:yes{/google_map}</li>
		<li>{google_map}address|https:yes{/google_map}</li>		
		<li><b>*</b>{google_map}latitude,longitude{/google_map}</li>
	</ul>
	
	<p><b>*</b> latitude,longitude = coordinates in decimal degrees</p>
	
	<h3>Shortcode</h3>
	
	<p>To embed a map in a template use <i>[google_map]</i> shortcode. For example:</p>
	<ul>
		<li>echo do_shortcode('[google_map]'.$address.'[/google_map]');</li>
	</ul>
	<p>All the settings supported by Embed Google Maps plugin can be set as shortcode attributes. For example:</p>
	<ul>
		<li>echo do_shortcode('[google_map version="classic" lang="en" link="yes" width="200" height="200"]'.$address.'[/google_map]');</li>
	</ul>
	
	<p>In addition, <i>[google_map]</i> shorcode can be used inside pages, posts and text widgets too For example:</p>
	<ul>
		<li>[google_map]address[/google_map]</li>
		<li>[google_map version="classic" lang="en" link="yes" width="200" height="200"]address[/google_map]</li>
	</ul>
	
	<h3>Google Map Versions</h3>
	<p>
		The plugin supports Google Maps, Google Maps Classic and Google Maps Embed API. The version to be used can be set by using the Version setting (supported values: new, classic, embed). Google Maps and Google Maps Classic do not require an API key, but for Google Maps Embed API an API key is required instead. Not all the parameters are supported by all the versions. Please see the supported parameters below.
	</p>
	
	<h3>Google Maps</h3>
	<ul>
		<li>map type (normal, satellite)</li>
		<li>zoom level</li>
		<li>
			language - By default, visitors will see a map in their own language which is defined by the locale of their browser. The setting takes effect only when a map is opened through the additional link to Google Maps
		</li>
		<li>add link</li>
		<li>link label</li>
		<li>height</li>
		<li>width</li>
		<li>border</li>
		<li>border style</li>
		<li>border color</li>
		<li>HTTPS</li>
	</ul>

	<h3>Google Maps Classic</h3>
	<ul>
		<li>map type (normal, satellite, hybrid, terrain)</li>
		<li>zoom level</li>
		<li>language</li>
		<li>add link</li>
		<li>link label</li>
		<li>link to full screen</li>
		<li>show info</li>
		<li>info label</li>
		<li>height</li>
		<li>width</li>
		<li>border</li>
		<li>border style</li>
		<li>border color</li>
		<li>HTTPS</li>
	</ul>
	
	<h3>Google Maps Embed API</h3>
	<ul>
		<li>map type (normal, satellite)</li>
		<li>zoom level</li>
		<li>language</li>
		<li>height</li>
		<li>width</li>
		<li>border</li>
		<li>border style</li>
		<li>border color</li>
		<li>HTTPS</li>
	</ul>

<?php
}

function embed_google_map_options_validate($input) {
	// version is 'new', 'classic' or 'embed'
	$input['version'] = ( preg_match('/^(new|classic|embed)$/i', $input['version']) ? $input['version'] : 'new' );
	// map type is 'm', 'k', 'h' or 'p'
	$input['map_type'] = ( preg_match('/^(m|k|h|p)$/', $input['map_type']) ? $input['map_type'] : 'm' );
	// zoom level is between 0-21
	$input['zoom_level'] = ( $input['zoom_level'] >= 0 && $input['zoom_level'] <= 21 ? $input['zoom_level'] : 14 );
    // add_link is either 0 or 1
    $input['add_link'] = ( $input['add_link'] == 1 ? 1 : 0 );
	// link_full is either 0 or 1
    $input['link_full'] = ( $input['link_full'] == 1 ? 1 : 0 );
	// height can contain only digits, whitespaces are stripped from the beginning and end of the value
	$input['height'] = ( preg_match('/^\d+$/', trim($input['height'])) ? trim($input['height']) : 400 );
	// show_info is either 0 or 1
    $input['show_info'] = ( $input['show_info'] == 1 ? 1 : 0 );
	// width can contain only digits, whitespaces are stripped from the beginning and end of the value
	$input['width'] = ( preg_match('/^\d+$/', trim($input['width'])) ? trim($input['width']) : 300 );
	// border is between 0-10
	$input['border'] = ( $input['border'] >= 0 && $input['border'] <= 10 ? $input['border'] : 0 );
	// border style is 'none', 'hidden', 'dotted', 'dashed', 'solid' or 'double'
	$input['border_style'] = ( preg_match('/^(none|hidden|dotted|dashed|solid|double)$/i', $input['border_style']) ? $input['border_style'] : 'solid' );
	// border color is a hex color
	$input['border_color'] = ( preg_match('/^#[a-f0-9]{6}$/i', $input['border_color']) ? $input['border_color'] : '#000000' );
    // https is either 0 or 1
    $input['https'] = ( $input['https'] == 1 ? 1 : 0 );	
	
	return $input;
}

function init_embed_google_map_options(&$options) {
	if(!isset($options['version'])) { $options['version'] = 'new'; }
	if(!isset($options['embed_api_key'])) { $options['embed_api_key'] = ''; }
	if(!isset($options['map_type'])) { $options['map_type'] = 'm'; }
	if(!isset($options['zoom_level'])) { $options['zoom_level'] = 14; }
	if(!isset($options['language'])) { $options['language'] = ''; }
	if(!isset($options['add_link'])) { $options['add_link'] = 0; }
	if(!isset($options['link_label'])) { $options['link_label'] = 'View Larger Map'; } 
	if(!isset($options['link_full'])) { $options['link_full'] = 0; }
	if(!isset($options['show_info'])) { $options['show_info'] = 1; }
	if(!isset($options['info_label'])) { $options['info_label'] = ''; } 
	if(!isset($options['height'])) { $options['height'] = 400; }
	if(!isset($options['width'])) { $options['width'] = 300; }
	if(!isset($options['border'])) { $options['border'] = 0; }
	if(!isset($options['border_style'])) { $options['border_style'] = 'solid'; }
	if(!isset($options['border_color'])) { $options['border_color'] = '#000000'; }
	if(!isset($options['https'])) { $options['https'] = 0; }
}

function embed_google_map_plugin_actions($links, $file) {
	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
	if ($file == $this_plugin){
		$my_link = '<a href="admin.php?page=embed_google_map.php">' . __('Settings') . '</a>';
		array_unshift($links, $my_link);
	}
	return $links;
}

class EmbedGoogleMapParameters {
	private $version = "new";
    private $embedAPIKey = "";
    private $address = "";
    private $mapType = "normal";
    private $zoomLevel = 14;
    private $language = "en";
    private $addLink =  0;
    private $linkLabel = "";
    private $linkFull = 0;
    private $showInfo =  1;
    private $height = 300;
    private $width =  400;
    private $border =  0;
    private $borderStyle =  "solid";
    private $borderColor =  "#000000";
    private $https = 0;
    private $infoLabel = "";	
	
    public function setVersion($value) {
		$this->version = $value;
    }

    public function getVersion() {
		return $this->version;
    }
	
	public function setEmbedAPIKey($value) {
		$this->embedAPIKey = $value;
    }

    public function getEmbedAPIKey() {
		return $this->embedAPIKey;
    }
	
    public function setAddress($value) {
		$this->address = $value;
    }

    public function getAddress() {
		return $this->address;
    }

    public function setMapType($value) {
		$this->mapType = $value;
    }

    public function getMapType() {
		return $this->mapType;
    }

    public function setZoomLevel($value) {
		$this->zoomLevel = $value;
    }

    public function getZoomLevel() {
		return $this->zoomLevel;
    }
    public function setLanguage($value) {
		$this->language = $value;
    }

    public function getLanguage() {
		return $this->language;
    }

    public function setAddLink($value) {
		$this->addLink = $value;
    }

    public function getAddLink() {
		return $this->addLink;
    }

    public function setLinkLabel($value) {
		$this->linkLabel = $value;
    }

    public function getLinkLabel() {
		return $this->linkLabel;
    }

    public function setLinkFull($value) {
		$this->linkFull = $value;
    }

    public function getLinkFull() {
		return $this->linkFull;
    }

    public function setShowInfo($value) {
		$this->showInfo = $value;
    }

    public function getShowInfo() {
		return $this->showInfo ;
    }

    public function setHeight($value) {
		$this->height = $value;
    }

    public function getHeight() {
		return $this->height;
    }

    public function setWidth($value) {
		$this->width = $value;
    }

    public function getWidth() {
		return $this->width;
    }

    public function setBorder($value) {
		$this->border = $value;
    }

    public function getBorder() {
		return $this->border;
    }

    public function setBorderStyle($value) {
		$this->borderStyle = $value;
    }

    public function getBorderStyle() {
		return $this->borderStyle;
    }


    public function setBorderColor($value) {
		$this->borderColor = $value;
    }

    public function getBorderColor() {
		return $this->borderColor;
    }

    public function setHttps($value) {
		$this->https = $value;
    }

    public function getHttps() {
		return $this->https;
    }

    public function setInfoLabel($value) {
		$this->infoLabel = $value;
    }

    public function getInfoLabel() {
		return $this->infoLabel;
    }

    public function setIsGoogleMapsEngine($value) {
		$this->isGoogleMapsEngine = $value;
    }

    public function isGoogleMapsEngine() {
		if(preg_match('/^http(s|):\/\/mapsengine\.google\.com/i', $this->address)) {
			return 1;
		}
		return 0;
    }

    public function isLink() {
		if(preg_match('/^http(s|):\/\//i', $this->address)) {
			return 1;
		}
		return 0;				
    }
 }
 
class EmbedGoogleMapParser {
	
	public function parse($string, &$params) {
		$arr = explode('|',$string);
		$params->setAddress($arr[0]);

		foreach ( $arr as $phrase ) { 
			if ( strstr(strtolower($phrase), 'version:') )	
			{         
				$tpm1 = explode(':',$phrase);
				$tmp1 = trim($tpm1[1], '"');
				if(strcmp(strtolower($tmp1),'new') == 0) {
					$params->setVersion("new");
				} else if(strcmp(strtolower($tmp1),'classic') == 0) {
					$params->setVersion("classic");
				} else if(strcmp(strtolower($tmp1),'embed') == 0) {
					$params->setVersion("embed");
				}        
			}
		
			if ( strstr(strtolower($phrase), 'zoom:') )	
			{         
				$tpm1 = explode(':',$phrase);
				$params->setZoomLevel(trim($tpm1[1], '"'));
			}
        
			if ( strstr(strtolower($phrase), 'height:') )	
			{         
				$tpm1 = explode(':',$phrase);
				$params->setHeight(trim($tpm1[1], '"'));
			}
            
			if ( strstr(strtolower($phrase), 'width:') )
			{
				$tpm1 = explode(':',$phrase);
				$params->setWidth(trim($tpm1[1], '"'));
			}
        
			if ( strstr(strtolower($phrase), 'border:') )	
			{         
				$tpm1 = explode(':',$phrase);
				$params->setBorder(trim($tpm1[1], '"'));
			}	
        
			if ( strstr(strtolower($phrase), 'border_style:') )	
			{         
				$tpm1 = explode(':',$phrase);        	
				$border_style = trim($tpm1[1], '"');
				$border_style = ( preg_match('/^(none|hidden|dotted|dashed|solid|double)$/i', $border_style) ? $border_style : 'solid' );
				$params->setBorderStyle($border_style);
			}	
        
			if ( strstr(strtolower($phrase), 'border_color:') )	
			{         
				$tpm1 = explode(':',$phrase);
				$params->setBorderColor(trim($tpm1[1], '"'));        	
			}	
        
			if ( strstr(strtolower($phrase), 'lang:') )	
			{         
				$tpm1 = explode(':',$phrase);
				$params->setLanguage(trim($tpm1[1], '"'));
			}        	
        
			if ( strstr(strtolower($phrase), 'link:') )
			{
				$tpm1 = explode(':',$phrase);
				$tmp1 = trim($tpm1[1], '"');
				if(strcmp(strtolower($tmp1),'yes') == 0) {
					$params->setAddLink(1);
				} else {
					$params->setAddLink(0);
				}
			}
        
			if ( strstr(strtolower($phrase), 'link_label:') )	
			{         
				$tpm1 = explode(':',$phrase);
				$params->setLinkLabel(trim($tpm1[1], '"'));
			}
		
			if ( strstr(strtolower($phrase), 'https:') )
			{
				$tpm1 = explode(':',$phrase);
				$tmp1 = trim($tpm1[1], '"');
				if(strcmp(strtolower($tmp1),'yes') == 0) {
					$params->setHttps(1);
				} else {
					$params->setHttps(0);
				}
			}		

			if ( strstr(strtolower($phrase), 'type:') )
			{
				$tpm1 = explode(':',$phrase);
				$tmp1 = trim($tpm1[1], '"');
				if(strcmp(strtolower($tmp1),'normal') == 0) {
					$params->setMapType("m");
				} else if(strcmp(strtolower($tmp1),'satellite') == 0) {
					$params->setMapType("k");
				} else if(strcmp(strtolower($tmp1),'hybrid') == 0) {
					$params->setMapType("h");
				} else if(strcmp(strtolower($tmp1),'terrain') == 0) {
					$params->setMapType("p");
				} 						
			}           
        
			if ( strstr(strtolower($phrase), 'link_full:') )
			{
				$tpm1 = explode(':',$phrase);
				$tmp1 = trim($tpm1[1], '"');
				if(strcmp(strtolower($tmp1),'yes') == 0) {
					$params->setLinkFull(1);
				} else {
					$params->setLinkFull(0);
				}
			}
      
			if ( strstr(strtolower($phrase), 'show_info:') )
			{
				$tpm1 = explode(':',$phrase);
				$tmp1 = trim($tpm1[1], '"');
				if(strcmp(strtolower($tmp1),'yes') == 0) {
					$params->setShowInfo(1);
				} else {
					$params->setShowInfo(0);
				}
			}
        
			if ( strstr(strtolower($phrase), 'info_label:') )	
			{         
				$tpm1 = explode(':',$phrase);
				$params->setInfoLabel(trim($tpm1[1], '"'));
			}		
		}
    }
}

abstract class EmbedGoogleMapHtmlBuilder {
    abstract public function buildHtml(&$params);

    protected function getUrl(&$params, $baseUrl) {
		$url = "";
		if($params->isLink() == 1 && $params->isGoogleMapsEngine() == 0) {
			$url = $params->getAddress();
		} else if($params->isGoogleMapsEngine() == 1) {
			$url = $params->getAddress();
			$alternatives = array("/edit", "/viewer");
			$url = str_replace($alternatives,'/embed', $url);
		} else {
			$url = $baseUrl;
		}
		if($params->getHttps() == 1) {
			$url = str_replace('http://','https://', $url);
		}
		return $url;
    }
	
	protected function getIFrameBegin(&$params) {
		$width="width='".$params->getWidth()."'";
		$height="height='".$params->getHeight()."'";
		$style="style='border: ".$params->getBorder()."px ".$params->getBorderStyle()." ".$params->getBorderColor()."'";
		return "\n<iframe $width $height $style ";
	}
	
	protected function getLinkHtml($url, $label) {
		return "<div><a href='$url' target='new'>$label</a></div>\n";
	}
}

class EmbedGoogleMapClassicHtmlBuilder extends EmbedGoogleMapHtmlBuilder {

    private $baseUrl = "http://maps.google.com/";

    public function buildHtml(&$params) {
		$url = parent::getUrl($params, $this->baseUrl);

		$html = parent::getIFrameBegin($params);

		if($params->isLink() == 0) {
			$url .= "?q=".$params->getAddress();
			if(strlen($params->getInfoLabel()) > 0) {
				$url .= "(".$params->getInfoLabel().")";
			}
		}

		if($params->isGoogleMapsEngine() == 0) {
			$url .= "&z=".$params->getZoomLevel();
			$url .= "&t=".$params->getMapType();

			if(strcmp($params->getLanguage(),'-') != 0) {
				$url .= "&hl=".$params->getLanguage();
			}

			$info = ($params->getShowInfo() == 1) ? "" : "&iwloc=near";			
					
			// Unicode properties are available only if PCRE is compiled with "--enable-unicode-properties" 
			// '\pL' = any Unicode letter
			if (preg_match('/^[^\pL]+$/u', $params->getAddress())) {
				$info = ($params->getShowInfo() == 1) ? "&iwloc=near" : "";
			}
	 
			$url .= $info;
		}
		$html .= "src='$url&output=svembed'></iframe>\n";
  
		if($params->getAddLink() == 1) {
			$output = ($params->getLinkFull() == 1) ? "&output=svembed" : "&output=classic";
			if($params->isGoogleMapsEngine() == 1) {
				$url = str_replace('/embed','/viewer', $url);
			} else {
				$url .= $output;
			}
			$html .= parent::getLinkHtml($url, $params->getLinkLabel());
		}
		return $html;
    }
}

class EmbedGoogleMapNewHtmlBuilder extends EmbedGoogleMapHtmlBuilder {

    private $baseUrl = "https://www.google.com/maps";

    public function buildHtml(&$params) {
		$url = parent::getUrl($params, $this->baseUrl);

		$html = parent::getIFrameBegin($params);

		if($params->isLink() == 0) {
			$url .= "?q=".$params->getAddress();
		}

		if($params->isGoogleMapsEngine() == 0) {
			$url .= "&z=".$params->getZoomLevel();
			$url .= "&t=".$this->getMapType($params->getMapType());
			if(strcmp($params->getLanguage(),'-') != 0) {
			  $url .= "&hl=".$params->getLanguage();
			}
		}
		$html .= "src='$url&output=embed'></iframe>\n";
  
		if($params->getAddLink() == 1) {
			if($params->isGoogleMapsEngine() == 1) {
				$url = str_replace('/embed','/viewer', $url);
			} else if($params->isLink() == 0) {
				$url = str_replace('/maps','/maps/preview', $url);
			}
			$html .= parent::getLinkHtml($url, $params->getLinkLabel());
		}
		return $html;
    }
	
	private function getMapType($mapType) {
		if(strcmp(strtolower($mapType),'m') == 0 || strcmp(strtolower($mapType),'k') == 0) {
			return $mapType;
		} else {
			return 'm';
		}
    }
}
  
class EmbedGoogleMapEmbedAPIHtmlBuilder extends EmbedGoogleMapHtmlBuilder {

    private $baseUrl = "https://www.google.com/maps/embed/v1/search";

    public function buildHtml(&$params) {
		$url = parent::getUrl($params, $this->baseUrl);

		$html = parent::getIFrameBegin($params);

		if($params->isLink() == 0) {
			$url .= "?key=".$params->getEmbedAPIKey();
			$url .= "&q=".urlencode($params->getAddress());
			$url .= "&zoom=".$params->getZoomLevel();
			$url .= "&maptype=".$this->getMapType($params->getMapType());
			if(strcmp($params->getLanguage(),'-') != 0) {
			  $url .= "&language=".$params->getLanguage();
			}
		}
		if($params->isLink() == 1 && $params->isGoogleMapsEngine() == 0) {
			$html .= "src='$url&output=embed'></iframe>\n";
		} else {
			$html .= "src='$url'></iframe>\n";
		}
  
		if($params->getAddLink() == 1) {
			if($params->isGoogleMapsEngine() == 1) {
				$url = str_replace('/embed','/viewer', $url);
				$html .= parent::getLinkHtml($url, $params->getLinkLabel());
			} else if($params->isLink() == 1) {
				$html .= parent::getLinkHtml($url, $params->getLinkLabel());
			}		
		}
		return $html;
    }
	
	private function getMapType($mapType) {
		if(strcmp(strtolower($mapType),'m') == 0) {
			return 'roadmap';
		} else if(strcmp(strtolower($mapType),'k') == 0) {
			return 'satellite';
		} else {
			return 'roadmap';
		}
	}
}
  
class EmbedGoogleMapBuilderFactory {
    public static function createBuilder($version) {
		if(strcmp($version,'classic') == 0) {
			return new EmbedGoogleMapClassicHtmlBuilder;
		} else if(strcmp($version,'new') == 0) {
			return new EmbedGoogleMapNewHtmlBuilder;
		} else if(strcmp($version,'embed') == 0) {
			return new EmbedGoogleMapEmbedAPIHtmlBuilder;
		}
	  return new EmbedGoogleMapNewHtmlBuilder;
	}
}
?>