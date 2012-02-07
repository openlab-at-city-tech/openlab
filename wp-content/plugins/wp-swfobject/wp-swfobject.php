<?php
/*
Plugin Name: WP-SWFObject
Plugin URI: http://blog.unijimpe.net/wp-swfobject/
Description: Allow insert Flash Movies into WordPress blog using SWFObject library. For use this plugin: [SWF]pathtofile, width, height[/SWF].
Version: 2.4
Author: Jim Penaloza Calixto 
Author URI: http://blog.unijimpe.net
*/

// Define Global params
$wpswf_version	= "2.4";										// version of plugin 
$wpswf_random	= substr(md5(uniqid(rand(), true)),0,4);		// create unique id for divs
$wpswf_number	= 0; 											// number of swf into page
$wpswf_params	= array("swf_version"		=>	"9.0.0",		// array of config options
						"swf_bgcolor"		=>	"#FFFFFF",
						"swf_wmode"			=>	"window",
						"swf_menu"			=>	"false",
						"swf_quality"		=>	"high",
						"swf_fullscreen"	=>	"false",
						"swf_scriptaccess"	=>	"always",
						"swf_align"			=>	"none",
						"swf_message"		=>	"This movie requires Flash Player 9",
						"swf_file"			=>	"v20int",
						"swf_showinfo"		=>	"false",
						"swf_annotations"	=>  "false",
						"swf_loading"		=>  "false",
						"swf_msgiphone"		=>	"SWF Movie"
						);
$wpswf_files	= array(
						"v15int"			=>	WP_PLUGIN_URL."/wp-swfobject/1.5/swfobject.js",
						"v20int"			=>	WP_PLUGIN_URL."/wp-swfobject/2.0/swfobject.js",
						"v20ext"			=>	"http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"
						);

// Define General Options
add_option("swf_version",		$wpswf_params["swf_version"], 		'Version of Flash Player.');
add_option("swf_bgcolor", 		$wpswf_params["swf_bgcolor"], 		'Background Color for Flash Movie.');
add_option("swf_wmode", 		$wpswf_params["swf_wmode"], 		'WMode for Flash Movie.');
add_option("swf_menu", 			$wpswf_params["swf_menu"], 			'Option for Activate menu for Flash Movie.');
add_option("swf_quality", 		$wpswf_params["swf_quality"], 		'Default quality for Flash Movie.');
add_option("swf_fullscreen",	$wpswf_params["swf_fullscreen"],	'If Allow Fullscreen mode for Flash Movie.');
add_option("swf_scriptaccess",	$wpswf_params["swf_scriptaccess"],	'Controls the ability to perform outbound URL access from SWF file');
add_option("swf_align", 		$wpswf_params["swf_align"], 		'Align for Flash Movie.');
add_option("swf_message", 		$wpswf_params["swf_message"], 		'Message for missing player.');
add_option("swf_file", 			$wpswf_params["swf_file"], 			'File version of SWFObject.');
add_option("swf_showinfo", 		$wpswf_params["swf_showinfo"], 		'Display information like the Youtube title and rating.');
add_option("swf_annotations", 	$wpswf_params["swf_annotations"], 	'Display annotations in Youtube videos.');
add_option("swf_loading", 		$wpswf_params["swf_loading"], 		'Display loading for SWFs.');
add_option("swf_msgiphone", 	$wpswf_params["swf_msgiphone"],		'Message for iPhone Browser.');

function wpswfConfig() {
	// get config options into array var
	global $wpswf_params;
    static $config;
    if ( empty($config) ) {
		foreach( $wpswf_params as $option => $default) {
			$config[$option] = get_option($option);
		}
    }
    return $config;
}
function wpswfParse($text) {
	// regexp for find swfs
    return preg_replace_callback('|\[swf\](.+?),\s*(\d+)\s*,\s*(\d+)\s*(,(.+?))?\[/swf\]|i', 'wpswfObject', $text);
}
function wpswfObject($match) {
    global $wpswf_random, $wpswf_number;
	$wpswf_config = wpswfConfig();
	$wpswf_number++;
	
	$swf_file = $wpswf_config['swf_file'];
	if (is_feed() || $doing_rss) {
		$swf_file = "vxhtml";
	}
	if (!(strpos($_SERVER['HTTP_USER_AGENT'], "iPhone") === false)) {
		$swf_file = "iphone";
	}
	
	$swf_path = trim(str_replace("&#038;", "&", $match[1]));
	$swf_vars = trim(str_replace("&#038;", "&", $match[4]));
	$swf_width = trim($match[2]);
	$swf_height = trim($match[3]);
	$swf_tubekey = "";
	
	// Parse Youtube Videos
	$swf_tube = parse_url($swf_path);
	if ($swf_tube["host"] == "www.youtube.com") {
		if ($swf_tube["path"] == "/watch") {
			parse_str($swf_tube["query"], $tube_que);
			if ($tube_que["v"] != "") { 
				$swf_tubekey = $tube_que["v"];
			}
		} else {
			parse_str($swf_tube["path"], $tube_que);
			if (key($tube_que) != "") {
				$swf_tubekey = substr(key($tube_que),3);
			} 
		}
		$swf_path = "http://www.youtube.com/v/".$swf_tubekey."&amp;rel=0&amp;showsearch=0";
		
		if ($wpswf_config['swf_fullscreen'] == "true" && $swf_file != "iphone") {
			$swf_path.= "&amp;fs=1";
		}
		if ($wpswf_config['swf_showinfo'] == "false" && $swf_file != "iphone") {
			$swf_path.= "&amp;showinfo=0";
		}
		if ($wpswf_config['swf_annotations'] == "false" && $swf_file != "iphone") {
			$swf_path.= "&amp;iv_load_policy=3";
		}
	}
	
	// Show embed for Youtube videos in iPhone
	if ($swf_tubekey != "" && $swf_file == "iphone") {
		$swf_file = "youtube";
	}
	
	// Create Style for Loading
	$tmploading = "";
	if ($wpswf_config['swf_loading'] == "true") {
		$tmploading = "background: url(".WP_PLUGIN_URL."/wp-swfobject/loading.gif) no-repeat center center; border: 1px solid #E6E6E6;";
	} 
	// Create DIVs for Align
	switch ($wpswf_config['swf_align']) {
		case "center";
			$tmpalign = "margin-left:auto; margin-right:auto; ";
			break;
		case "left";
			$tmpalign = "margin-right:auto; ";
			break;
		case "right";
			$tmpalign = "margin-left:auto; ";
			break;
		default:
			$tmpalign = "";
			break;
	}
	if ($swf_file != "vxhtml" && $swf_file != "youtube") {
		$writeswf = "<div id=\"swf".$wpswf_random.$wpswf_number."\">".$wpswf_config['swf_message']."</div>";
		if ($tmpalign != "") {
			$writeswf = "<div style=\"text-align:center; width:".$swf_width."px; height:".$swf_height."px; line-height:".$swf_height."px; ".$tmpalign." ".$tmploading."\">".$writeswf."</div>\n";
		}
	}
	// Write code for SWF
	switch ($swf_file) {
		case "iphone":
			$writeswf = "<div style=\"text-align:center; width:".$swf_width."px; height:".$swf_height."px; line-height:".$swf_height."px; ".$tmpalign.";\">";
			$writeswf.= "<div id=\"swf".$wpswf_random.$wpswf_number."\"><strong>".$wpswf_config['swf_msgiphone']."</strong></div>";
			$writeswf.= "</div>\n";
			break;
		case "v15int":
			// Use SWFObject 1.5 code
			$writeswf.= "\n<script type=\"text/javascript\">\n";
			$writeswf.= "\tvar vswf = new SWFObject(\"".$swf_path."\", \"id".$wpswf_number."\", \"".$swf_width."\", \"".$swf_height."\", \"".$wpswf_config['swf_version']."\", \"".$wpswf_config['swf_bgcolor']."\");\n";
			$writeswf.= "\tvswf.addParam(\"wmode\", \"".$wpswf_config['swf_wmode']."\");\n";
			$writeswf.= "\tvswf.addParam(\"menu\", \"".$wpswf_config['swf_menu']."\");\n";
			$writeswf.= "\tvswf.addParam(\"quality\", \"".$wpswf_config['swf_quality']."\");\n";
			$writeswf.= "\tvswf.addParam(\"allowScriptAccess\", \"".$wpswf_config['swf_scriptaccess']."\");\n";
			if ($wpswf_config['swf_fullscreen'] == "true") {
				$writeswf.= "\tvswf.addParam(\"allowFullScreen\", \"".$wpswf_config['swf_fullscreen']."\");\n";
			}
			if ($swf_vars != "") {
				$swf_vars = str_replace("&amp;", "&", $swf_vars);
				parse_str(substr($swf_vars,1), $swf_params);
				foreach ($swf_params as $swf_param => $swf_value) {
					$writeswf.= "\tvswf.addVariable(\"".$swf_param."\", \"".$swf_value."\");\n";
				}
			}
			$writeswf.= "\tvswf.write(\"swf".$wpswf_random.$wpswf_number."\");\n";
			$writeswf.= "</script>\n";
			break;
		case "vxhtml":
			// Use XHTML code
			$writeswf.= "\n<object width=\"".$swf_width."\" height=\"".$swf_height."\">\n";
			$writeswf.= "<param name=\"movie\" value=\"".$swf_path."\"></param>\n";
			$writeswf.= "<param name=\"quality\" value=\"".$wpswf_config['swf_quality']."\"></param>\n";
			$writeswf.= "<param name=\"wmode\" value=\"".$wpswf_config['swf_wmode']."\"></param>\n";
			$writeswf.= "<param name=\"menu\" value=\"".$wpswf_config['swf_menu']."\"></param>\n";
			$writeswf.= "<param name=\"bgcolor\" value=\"".$wpswf_config['swf_bgcolor']."\"></param>\n";
			$writeswf.= "<param name=\"allowScriptAccess\" value=\"".$wpswf_config['swf_scriptaccess']."\"></param>\n";
			if ($wpswf_config['swf_fullscreen'] == "true") {
				$writeswf.= "<param name=\"allowFullScreen\" value=\"true\"></param>\n"; 
			}
			if ($swf_vars != "") {
				$writeswf.= "<param name=\"flashvars\" value=\"".substr($swf_vars,1)."\"></param>\n";
			}
			$writeswf.= "<embed type=\"application/x-shockwave-flash\" width=\"".$swf_width."\" height=\"".$swf_height."\" src=\"".$swf_path."\" ";
			$writeswf.= "quality=\"".$wpswf_config['swf_quality']."\" bgcolor=\"".$wpswf_config['swf_bgcolor']."\" wmode=\"".$wpswf_config['swf_wmode']."\" menu=\"".$wpswf_config['swf_menu']."\" ";
			if ($wpswf_config['swf_fullscreen'] == "true") {
				$writeswf.=	"allowFullScreen=\"true\" ";
			}
			if ($swf_vars != "") {
				$writeswf.= "flashvars=\"".substr($swf_vars,1)."\" ";
			}
			$writeswf.= "></embed>\n";
			$writeswf.= "</object>\n";
			break;
		case "youtube":
			// Use XHTML code
			$writeswf.= "\n<object width=\"".$swf_width."\" height=\"".$swf_height."\" type=\"application/x-shockwave-flash\" data=\"".$swf_path."\">\n";
			$writeswf.= "<param name=\"src\" value=\"".$swf_path."\" />\n";
			$writeswf.= "<param name=\"wmode\" value=\"".$wpswf_config['swf_wmode']."\" />\n";
			$writeswf.= "</object>\n";
			break;
		default:
			// Use SWFObject 2.0 code
			$wpswf_params = "wmode: \"".$wpswf_config['swf_wmode']."\", ";
			$wpswf_params.= "menu: \"".$wpswf_config['swf_menu']."\", ";
			$wpswf_params.= "quality: \"".$wpswf_config['swf_quality']."\", ";
			$wpswf_params.= "bgcolor: \"".$wpswf_config['swf_bgcolor']."\", ";
			$wpswf_params.= "allowScriptAccess: \"".$wpswf_config['swf_scriptaccess']."\"";
			if ($wpswf_config['swf_fullscreen'] == "true") {
				$wpswf_params.= ", allowFullScreen: \"".$wpswf_config['swf_fullscreen']."\"";
			}
			
			$wpswf_fvars = "";
			if ($swf_vars != "") {
				$swf_vars = str_replace("&amp;", "&", $swf_vars);
				parse_str(substr($swf_vars,1), $swf_params);
				foreach ($swf_params as $swf_param => $swf_value) {
					$wpswf_fvars .= ", ". $swf_param . ": \"".$swf_value."\"";
				}
			}
			$writeswf.= "\n<script type=\"text/javascript\">\n";
			$writeswf.= "\tswfobject.embedSWF(\"".$swf_path."\", \"swf".$wpswf_random.$wpswf_number."\", \"".$swf_width."\", \"".$swf_height."\", \"".$wpswf_config['swf_version']."\", \"\", {".substr($wpswf_fvars, 2)."}, {".$wpswf_params."}, {});\n";
			$writeswf.= "</script>\n";
			break;
	}
	return $writeswf;
}
function wp_swfobject_echo($swffile, $swfwidth, $swfheigth, $swfvars = "") {
    echo wpswfObject( array( null, $swffile, $swfwidth, $swfheigth, "&".$swfvars) );
}
function wpswfOptionsPage() {
	// update general options
	global $wpswf_version, $wpswf_params;
	if (isset($_POST['swf_update'])) {
		check_admin_referer();
		foreach( $wpswf_params as $option => $default ) {
			$swf_param = trim($_POST[$option]);
			if ($swf_param == "") {
				$swf_param = $default;
			}
			update_option($option, $swf_param);
		}
		echo "<div class='updated'><p><strong>WP-SWFObject options updated</strong></p></div>";
	}
	$wpswf_config = wpswfConfig();
?>
		<form method="post" action="options-general.php?page=wp-swfobject.php">
		<div class="wrap">
			<h2>WP-SWFObject <sup style='color:#D54E21;font-size:12px;'><?php echo $wpswf_version; ?></sup></h2>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="swf_file">SWFObject Version</label>
						</th>
						<td>
							<select name="swf_file" id="swf_file">
								<option value="v15int" <?php if ($wpswf_config["swf_file"] == "v16int") { echo "selected=\"selected\""; } ?>>SWFObject 1.5</option>
								<option value="v20int" <?php if ($wpswf_config["swf_file"] == "v20int") { echo "selected=\"selected\""; } ?>>SWFObject 2.0</option>
								<option value="v20ext" <?php if ($wpswf_config["swf_file"] == "v20ext") { echo "selected=\"selected\""; } ?>>SWFObject 2.0 (from Google Library)</option>
								<option value="vxhtml" <?php if ($wpswf_config["swf_file"] == "vxhtml") { echo "selected=\"selected\""; } ?>>XHTML (&lt;object&gt;)</option>
							</select>
							<span class="description">Select version of SWFObject.</span>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swf_version">Flash Player Version</label>
						</th>
						<td>
							<input type="text" maxlength="12" name="swf_version" id="swf_version" value="<?php echo $wpswf_config["swf_version"]; ?>" class="regular-text" />
							<span class="description">Enter number of flash version required for flash player.</span>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swf_bgcolor">Background Color</label>
						</th>
						<td>
							<input type="text" maxlength="7" name="swf_bgcolor" id="swf_bgcolor" value="<?php echo $wpswf_config["swf_bgcolor"]; ?>" class="regular-text" />
							<span class="description">Enter HEX number for background color for flash movie.</span>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swf_wmode">Window Mode</label>
						</th>
						<td>
							<select name="swf_wmode" id="swf_wmode">
								<option value="window" <?php if ($wpswf_config["swf_wmode"] == "window") { echo "selected=\"selected\""; } ?>>Window</option>
								<option value="opaque" <?php if ($wpswf_config["swf_wmode"] == "opaque") { echo "selected=\"selected\""; } ?>>Opaque</option>
								<option value="transparent" <?php if ($wpswf_config["swf_wmode"] == "transparent") { echo "selected=\"selected\""; } ?>>Transparent</option>
							</select>
							<span class="description">Select wmode for movie, by defaul is <strong>window</strong>.</span>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swf_menu">Show Menu</label>
						</th>
						<td>
							<select name="swf_menu" id="swf_menu">
								<option value="true" <?php if ($wpswf_config["swf_menu"] == "true") { echo "selected=\"selected\""; } ?>>True</option>
								<option value="false" <?php if ($wpswf_config["swf_menu"] == "false") { echo "selected=\"selected\""; } ?>>False</option>
							</select>
							<span class="description">Select option for show/hide menu.</span>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swf_quality">Quality Movie</label>
						</th>
						<td>
							<select name="swf_quality" id="swf_quality">
								<option value="low" <?php if ($wpswf_config["swf_quality"] == "low") { echo "selected=\"selected\""; } ?>>Low</option>
								<option value="autolow" <?php if ($wpswf_config["swf_quality"] == "autolow") { echo "selected=\"selected\""; } ?>>Autolow</option>
								<option value="autohigh" <?php if ($wpswf_config["swf_quality"] == "autohigh") { echo "selected=\"selected\""; } ?>>Autohigh</option>
								<option value="medium" <?php if ($wpswf_config["swf_quality"] == "medium") { echo "selected=\"selected\""; } ?>>Medium</option>
								<option value="high" <?php if ($wpswf_config["swf_quality"] == "high") { echo "selected=\"selected\""; } ?>>High</option>
								<option value="best" <?php if ($wpswf_config["swf_quality"] == "best") { echo "selected=\"selected\""; } ?>>Best</option>
							</select>
							<span class="description">Select quality for flash movie, by default is <strong>hight</strong>.</span>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swf_fullscreen">Allow Fullscreen</label>
						</th>
						<td>
							<select name="swf_fullscreen" id="swf_fullscreen">
								<option value="true" <?php if ($wpswf_config["swf_fullscreen"] == "true") { echo "selected=\"selected\""; } ?>>True</option>
								<option value="false" <?php if ($wpswf_config["swf_fullscreen"] == "false") { echo "selected=\"selected\""; } ?>>False</option>
							</select>
							<span class="description">Allow Fullscreen (You must have version >= 9,0,28,0 of Flash Player).</span>
						</td>
					</tr>
                    <tr>
						<th scope="row">
							<label for="swf_scriptaccess">Allow Script Access</label>
						</th>
						<td>
							<select name="swf_scriptaccess" id="swf_scriptaccess">
								<option value="always" <?php if ($wpswf_config["swf_scriptaccess"] == "always") { echo "selected=\"selected\""; } ?>>Always</option>
								<option value="sameDomain" <?php if ($wpswf_config["swf_scriptaccess"] == "sameDomain") { echo "selected=\"selected\""; } ?>>Same Domain</option>
                                <option value="never" <?php if ($wpswf_config["swf_scriptaccess"] == "never") { echo "selected=\"selected\""; } ?>>Never</option>
							</select>
							<span class="description">Controls the ability to perform outbound URL access from SWF file.</span>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swf_align">Align</label>
						</th>
						<td>
							<select name="swf_align" id="swf_align">
								<option value="none" <?php if ($wpswf_config["swf_align"] == "none") { echo "selected=\"selected\""; } ?>>None</option>
								<option value="left" <?php if ($wpswf_config["swf_align"] == "left") { echo "selected=\"selected\""; } ?>>Left</option>
								<option value="center" <?php if ($wpswf_config["swf_align"] == "center") { echo "selected=\"selected\""; } ?>>Center</option>
								<option value="right" <?php if ($wpswf_config["swf_align"] == "right") { echo "selected=\"selected\""; } ?>>Right</option>
							</select>
							<span class="setting-description">Align for Flash Movies into Post.</span>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swf_message">Message Require Flash</label>
						</th>
						<td>
							<input type="text" name="swf_message" id="swf_message" value="<?php echo $wpswf_config["swf_message"]; ?>" class="regular-text" />
							<span class="description">Enter message for warning missing player.</span>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="swf_message">Message iPhone Browser</label>
						</th>
						<td>
							<input type="text" name="swf_msgiphone" id="swf_msgiphone" value="<?php echo $wpswf_config["swf_msgiphone"]; ?>" class="regular-text" />
							<span class="description">Enter message for iphone Browser.</span>
						</td>
					</tr>
					<tr>
					    <th scope="row">
					  		<label for="swf_align">Show Info (Youtube)</label>
					    </th>
					 	<td>
							<select name="swf_showinfo" id="swf_showinfo">
								<option value="true" <?php if ($wpswf_config["swf_showinfo"] == "true") { echo "selected=\"selected\""; } ?>>True</option>
								<option value="false" <?php if ($wpswf_config["swf_showinfo"] == "false") { echo "selected=\"selected\""; } ?>>False</option>
							</select>
							<span class="setting-description">Display information like the Youtube video title and rating.</span>
					 	</td>
				    </tr>
					<tr>
					    <th scope="row">
					  		<label for="swf_annotations">Show Annotations (Youtube)</label>
					    </th>
					 	<td>
							<select name="swf_annotations" id="swf_annotations">
								<option value="true" <?php if ($wpswf_config["swf_annotations"] == "true") { echo "selected=\"selected\""; } ?>>True</option>
								<option value="false" <?php if ($wpswf_config["swf_annotations"] == "false") { echo "selected=\"selected\""; } ?>>False</option>
							</select>
							<span class="description">Display annotations in Youtube videos.</span>
					 	</td>
				    </tr>
					<tr>
					    <th scope="row">
					  		<label for="swf_loading">Show Loading</label>
					    </th>
					 	<td>
							<select name="swf_loading" id="swf_loading">
								<option value="true" <?php if ($wpswf_config["swf_loading"] == "true") { echo "selected=\"selected\""; } ?>>True</option>
								<option value="false" <?php if ($wpswf_config["swf_loading"] == "false") { echo "selected=\"selected\""; } ?>>False</option>
							</select>
							<span class="description">Display Loading for SWFs.</span>
					 	</td>
				    </tr>
					</table>
					<p class="submit">
					  <input name="swf_update" value="Save Changes" type="submit" class="button-primary" />
					</p>
					<table>
					<tr>
						<th width="30%" style="padding-top: 10px; text-align:left;" colspan="2">
							More Information and Support
						</th>
					</tr>
					<tr>
						<td colspan="2">
						  <p>Check our links for updates and comment there if you have any problems / questions / suggestions. </p>
					      <ul>
					        <li><a href="http://blog.unijimpe.net/wp-swfobject/">Plugin Home Page</a></li>
			                <li><a href="http://forum.unijimpe.net/?CategoryID=4">Plugin Forum Support</a> </li>
			                <li><a href="http://blog.unijimpe.net/">Author Home Page</a></li>
				            <li><a href="http://code.google.com/p/swfobject/">SWFObject 2.0 Home Page</a> </li>
				        </ul></td>
				  </tr>
				</table>
			
		</div>
		</form>
<?php
}
function wpswfAddMenu() {
	// add menu options
	add_options_page('WP-SWFObject Options', 'WP-SWFObject', 8, basename(__FILE__), 'wpswfOptionsPage');
}
function wpswfAddheader() {
	// Add SWFObject to header
	global $wpswf_version, $wpswf_files;
	echo "\n<!-- WP-SWFObject ".$wpswf_version." by unijimpe -->";
	if (get_option('swf_file') != "vxhtml") { 
	echo "\n<script src=\"".$wpswf_files[get_option('swf_file')]."\" type=\"text/javascript\"></script>\n";
	}
}

add_filter('the_content', 'wpswfParse');
add_filter('widget_text', 'wpswfParse');
add_action('wp_head', 'wpswfAddheader');
add_action('admin_menu', 'wpswfAddMenu');
?>