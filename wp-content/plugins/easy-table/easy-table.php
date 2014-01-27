<?php
/*
Plugin Name: Easy Table
Plugin URI: http://takien.com/
Description: Create table in post, page, or widget in easy way.
Author: Takien
Version: 1.4
Author URI: http://takien.com/
*/

/*  Copyright 2013 takien.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

if(!defined('ABSPATH')) die();

if (!class_exists('EasyTable')) {
class EasyTable {


/**
* Default settings
* Plugin will use this setting if user not made custom setting via settings page or tag.
*/
var $settings = Array(
	'shortcodetag'  => 'table',
	'attrtag'       => 'attr',
	'tablewidget'   => false,
	'scriptloadin'  => Array('is_single','is_page'),
	'class'         => '',
	'caption'       => false,
	'width'         => '100%',
	'align'         => 'left',
	'th'            => true,
	'tf'            => false,
	'border'        => 0,
	'id'            => false,
	'theme'         => 'default',
	'tablesorter'   => false,
	'loadcss'       => true,
	'scriptinfooter'=> false,
	'delimiter'     => ',',
	'file'          => false,
	'trim'          => false, /*trim, since 1.0*/
	'enclosure'     => '&quot;',
	'escape'        => '\\',
	'nl'            => '~~',
	'csvfile'       => false,
	'terminator'    => '\n', /*row terminator, since 1.0*/
	'limit'         => 0, /*max row to be included to table, 0 = unlimited, since 1.0*/
	'fixlinebreak'  => false
);


function EasyTable(){
	$this->__construct();
}

function __construct(){
	$plugin = plugin_basename(__FILE__);
	add_filter("plugin_action_links_$plugin",  array(&$this,'easy_table_settings_link' ));
	
	load_plugin_textdomain('easy-table', false, basename( dirname( __FILE__ ) ) . '/languages' );
	
	add_action('admin_init', 		 array(&$this,'easy_table_register_setting'));
	add_action('admin_head',		 array(&$this,'easy_table_admin_script'));
	add_action('wp_enqueue_scripts', array(&$this,'easy_table_script'));
	add_action('wp_enqueue_scripts', array(&$this,'easy_table_style'));
	add_action('admin_menu', 		 array(&$this,'easy_table_add_page'));
	add_action('contextual_help', 	 array(&$this,'easy_table_help'));
	add_shortcode($this->option('shortcodetag'),  array(&$this,'easy_table_short_code'));
	add_shortcode($this->option('attrtag'),  array(&$this,'easy_table_short_code_attr'));
	if($this->option('tablewidget')){
		add_filter('widget_text', 		'do_shortcode');
	}
}

private function easy_table_base($return){
	$easy_table_base = Array(
				'name' 			=> 'Easy Table',
				'version' 		=> '1.4',
				'plugin-domain'	=> 'easy-table'
	);
	return $easy_table_base[$return];
}

function easy_table_short_code($atts, $content="") {
	$shortcode_atts = shortcode_atts(array(
		'class'         => $this->option('class'),
		'caption'       => $this->option('caption'),
		'width'         => $this->option('width'),
		'th'            => $this->option('th'),
		'tf'            => $this->option('tf'),
		'border'        => $this->option('border'),
		'id'            => $this->option('id'),
		'theme'         => $this->option('theme'),
		'tablesorter'   => $this->option('tablesorter'),
		'delimiter'     => $this->option('delimiter'),
		'enclosure'     => $this->option('enclosure'),
		'escape'        => $this->option('escape'),
		'file'          => $this->option('file'),
		'trim'          => $this->option('trim'), 
		'sort'          => '',
		'nl'            => $this->option('nl'),
		'ai'            => false,
		'terminator'    => $this->option('terminator'),
		'limit'         => $this->option('limit'),
		'align'         => $this->option('align'),
		'style'         => '', /*table inline style, since 1.0*/
		'colalign'      => '', /*column align, ex: [table colalign="left|right|center"], @since 1.0*/
		'colwidth'      => '', /*column width, ex: [table colwidth="100|200|300"], @since 1.0*/
		'fixlinebreak'  => $this->option('fixlinebreak') /* fix linebreak on cell if terminator is not \n or \r @since 1.1.4 */
	 ), $atts);
	/**
	* because clean_pre is deprecated since WordPress 3.4, then replace it manually
	$content 		= clean_pre($content);*/
	
	$content = str_replace(array('<br />', '<br/>', '<br>'), array('', '', ''), $content);
	$content = str_replace('<p>', "\n", $content);
	$content = str_replace('</p>', '', $content);
	
	$content 		= str_replace('&nbsp;','',$content);
	$char_codes 	= array( '&#8216;', '&#8217;', '&#8220;', '&#8221;', '&#8242;', '&#8243;' );
	$replacements 	= array( "'", "'", '"', '"', "'", '"' );
	$content = str_replace( $char_codes, $replacements, $content );
		
	return $this->csv_to_table($content,$shortcode_atts);
}

/**
* Just return to strip attr shortcode for table cell, since we use custom regex for attr shortcode.
* @since 0.5
*/
function easy_table_short_code_attr($atts){
	return;
}

/**
* Convert CSV to table
* @param array|string $data could be CSV string or array
* @param array @args
* @return string
*/
private function csv_to_table($data,$args){
	extract($args);
	if( $this->option('csvfile') AND $file ){
		/*$data = @file_get_contents($file);*/
		/** use wp_remote_get
		* @since 0.8
		*/
		$data = '';
		$response = wp_remote_get($file);
		/**
			notify if error reading file.
			@since 0.9
		*/
		if( is_wp_error( $response ) ) {
		   return '<div style="color:red">Error reading file/URL.</div>';
		} else if( $response['response']['code'] == 200 ) {
			$data = $response['body'];
		}
	}

	if(!is_array($data)){
		/**
		normalize nl, since it may contains new line.
		@since 0.9
		*/
		$data = preg_replace('/'.preg_quote($nl).'([\s\r\n\t]+)?/i',$nl,$data);
	
		/*
		Fix encoding?
		@since: 1.0 beta
		*/
		require_once (dirname(__FILE__).'/inc/Encoding.php');
		//$data = ForceEncode::fixUTF8($data);
		$data = ForceEncode::toUTF8($data);
		
		/*
		convert csv to array.
		*/
		$data 	= $this->csv_to_array(trim($data), $delimiter, $enclosure, $escape, $terminator, $limit);
	}
	
	if(empty($data)) return false;
	
	$max_cols 	= count(max($data));

	$r=0;
	
	/**
	* initialize inline sort, 
	* extract header sort if any, and equalize with max column number
	* @since 0.8
	*/
	if( $tablesorter ) {
		$inline_sort = Array();
		$header_sort = explode(',',$sort);
		$header_sort = array_pad($header_sort,$max_cols,NULL);
	}
	
	/**
	* tfoot position
	* @since 0.4
	*/
	$tfpos = ($tf == 'last') ? count($data) : ($th?2:1);
	
	/**
	* add auto width
	* @since 1.1.3
	*/	

	if ( 'auto' !== $width ) {
 		$width = (stripos($width,'%') === false) ? (int)$width.'px' : (int)$width.'%';
 	}
	
	/*colalign & colwidth
	@since 1.0
	*/
	if($colalign) {
	    $c_align = explode('|',$colalign);
	}
	if($colwidth) {
	    $c_width = explode('|',$colwidth);
	}
	
	/* added back $align, with new way of implementation, 
	* @since 1.4
	*/
	$style = rtrim($style, ';');
	switch ($align) :
		case 'center':
			$alignstyle = '; margin-left:auto;margin-right:auto';
		break;
		case 'right':
			$alignstyle = '; margin-left:auto;margin-right:0';
		break;
		default:
			$alignstyle = '';
		break;
	endswitch; 
	
	$style = $style.$alignstyle;
	
	$output = '<table '.($id ? 'id="'.$id.'"':'');
	
	//$output .= ' width="'.$width.'" '; width attr not used, use style instead (see below) - since 1.1.3
	$output .= ' style="'.((stripos($style,'width') === false) ? ('width:'.$width.';') : '').' '.ltrim($style,';').'" ';
	$output .= ' class="easy-table easy-table-'.$theme.' '.($tablesorter ? 'tablesorter __sortlist__ ':'').$class.'" '.
	(($border !=='0') ? 'border="'.$border.'"' : '').
	'>'."\n";
	
	$output .= $caption ? '<caption>'.$caption.'</caption>'."\n" : '';
	$output .= $th ? '<thead>' : (($tf !== 'last') ? '' : '<tbody>');
	$output .= (!$th AND !$tf) ? '<tbody>':'';
	
	foreach($data as $k=>$cols){ $r++;
		//$cols = array_pad($cols,$max_cols,'');
		
		$output .= (($r==$tfpos) AND $tf) ? (($tf=='last')?'</tbody>':'').'<tfoot>': '';
		$output .= "\r\n".'<tr>';

		$thtd = ((($r==1) AND $th) OR (($r==$tfpos) AND $tf)) ? 'th' : 'td';
/**
ai is auto index
@since 0.9
add auto numbering in the begining of each row
ai="true" or ai="1" number will start from 1,
ai="n", n = any number, number will start from that.

Another possible value.
ai="n/head/width"
n     = index start
head  = head text, default is No.
width = column width, in pixel. Default is 20px
ai head, text to shown in the table head row, default is No.

*/		
		$index       = explode('/',$ai);
		$indexnum    = ((int)$index[0])+$r;
		$indexnum    = $th ? $indexnum-2 : $indexnum-1;
		$indexnum    = ($tf AND ($tf !== 'last')) ? $indexnum-1 : $indexnum;
		$indexhead   = isset($index[1]) ? $index[1] : 'No.';
		$indexwidth  = isset($index[2]) ? (int)$index[2] : 30;
		$output .= ($ai AND ($thtd == 'td'))  ? '<'.$thtd.' style="width:'.$indexwidth.'px">'.$indexnum."</$thtd>" : ($ai ? "<$thtd>".$indexhead."</$thtd>" : '');
		
		foreach($cols as $c=>$cell){
			/**
			* Add attribute for each cell
			* @since 0.5
			*/
			preg_match('/\['.$this->option('attrtag').' ([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)/',$cell,$matchattr);
			$attr = isset($matchattr[1]) ? $matchattr[1] : '';
				/**
				* extract $attr value
				* @since 0.8
				* this is for inline sorting option, 
				* eg [attr sort="desc"],[attr sort="asc"] or [attr sort="none"]
				* only affect if it's TH and $tablesorter enabled
				* extract sort value and insert appropriate class value.
				*/ 
				
				if( ('th' == $thtd) AND $tablesorter ) {
					$attrs = $attr ? shortcode_parse_atts($attr) : Array();
					$attrs['sort']  =  isset($attrs['sort']) ? $attrs['sort'] : $header_sort[$c];
					$attrs['class'] =  isset($attrs['class']) ? $attrs['class'] : '';
					
					$inline_sort[$c] = $attrs['sort'];

					$attr = '';
					$sorter = in_array(strtolower($attrs['sort']),array('desc','asc')) ? '' : (!empty($attrs['sort']) ? 'false' : '');
					foreach($attrs as $katr => $vatr){
						if($katr == 'sort') {
						}
						else if(($katr == 'class')){
							$attr .= "$katr='$vatr ";
							$attr .= $sorter ? "{sorter: $sorter}":'';
							$attr .= "' ";
						}
						else {
							$attr .= "$katr='$vatr' ";
						}
					}
				}
			/**
			nl, replace nl with new line
			@since 0.9
			*/
			$cell     = str_replace($nl,'<br />',$cell);
			 /*trim cell content?
			 @since 1.0
			 */
			$cell  = $trim ? trim(str_replace('&nbsp;','',$cell)) : $cell;
			
			/*nl2br? only if terminator is not \n or \r*/
			/* optionally, if $fixlinebreak is set. @since 1.1.4 */
			
			if ( $fixlinebreak ) {
				if(( '\n' !== $terminator )  OR ( '\r' !== $terminator )) {
					$cell = nl2br($cell);
				}
			}
			/*colalign
			 @since 1.0
			 */
			if (isset($c_align[$c]) AND (stripos($attr,'text-align') === false)) {
				if(stripos($attr,'style') === false) {
				   $attr = $attr. ' style="text-align:'.$c_align[$c].'" ';
				}
				else {
					$attr = preg_replace('/style(\s+)?=(\s+)?("|\')(\s+)?/i','style=${3}text-align:'.$c_align[$c].';',$attr);
				}
			}
			/*colwidth
			 @since 1.0
			 */
			if (isset($c_width[$c]) AND (stripos($attr,'width') === false) AND ($r == 1)) {
				$c_width[$c] = (stripos($c_width[$c],'%') === false) ? (int)$c_width[$c].'px' : (int)$c_width[$c].'%';
				
				if(stripos($attr,'style') === false) {
				   $attr = $attr. ' style="width:'.$c_width[$c].'" ';
				}
				else {
					$attr = preg_replace('/style(\s+)?=(\s+)?("|\')(\s+)?/i','style=${3}width:'.$c_width[$c].';',$attr);
				}
			}
			
			$output .= "<$thtd $attr>".do_shortcode($cell)."</$thtd>\n";
		}
	
		$output .= '</tr>'."\n";
		$output .= (($r==1) AND $th) ? '</thead>'."\n".'<tbody>' : '';
		$output .= (($r==$tfpos) AND $tf) ? '</tfoot>'.((($tf==1) AND !$th) ? '<tbody>':''): '';
		
	}
	$output .= (($tf!=='last')?'</tbody>':'').'</table>';
	
	/** 
	* Build sortlist metadata and append it to the table class
	* @since 0.8
	* This intended to $tablesorter,
	* so don't bother if $tablesorter is false/disabled
	*/

	
	if( $tablesorter ) {
		$sortlist = '';
		$all_sort = array_replace($header_sort,$inline_sort);
		
		if(implode('',$all_sort)) {
			$sortlist = '{sortlist: [';
			foreach($all_sort as $k=>$v){
				$v = (($v == 'desc') ? 1 : (($v == 'asc') ? 0 : '' ));
				if($v !=='') {
					$sortlist .= '['.$k.','.$v.'], ';
				}
			}
			$sortlist .= ']}';
		}
		$output = str_replace('__sortlist__',$sortlist,$output);
	}
	return $output;
}

/**
* Convert CSV to array
*/
private function csv_to_array($csv, $delimiter = ',', $enclosure = '"', $escape = '\\', $terminator = "\n", $limit = 0 ) {
$r = array();

$terminator = ($terminator == '\n') ? "\n" : $terminator;
$terminator = ($terminator == '\r') ? "\r" : $terminator;
$terminator = ($terminator == '\t') ? "\t" : $terminator;

$rows = easy_table_str_getcsv($csv, $terminator,$enclosure,$escape); 
$rows = array_diff($rows,Array(''));
/*
* limit how many rows will be included?
* default 0, means ulimited.
* @since 1.0
*/
if($limit > 0) {
	$rows = array_slice($rows, 0, $limit); 
}

foreach($rows as &$row) {
	$r[] = easy_table_str_getcsv($row,$delimiter);
}
return $r;
}

/**
* Retrieve options from database if any, or use default options instead.
*/
function option($key=''){
	$option = get_option('easy_table_plugin_option') ? get_option('easy_table_plugin_option') : Array();
	$option = array_merge($this->settings,$option);
	if($key){
		$return = $option[$key];
	}
	else{
		$return = $option;
	}
	return $return;

}

/**
* Retrieve themes directory
* @since: 0.8
*/

function themes(){
	/**
	* delete theme cache on setting updated.
	*/
	if( ( 'easy-table' == $_GET['page']) AND isset($_GET['settings-updated']) ) {
		delete_transient('easy_table_themes');
	}
	
	if(!function_exists('scandir')){
		return Array('default');
	}
	if ( false === ( $themes = get_transient( 'easy_table_themes' ) )) {
	
	$dir = plugin_dir_path(__FILE__).'themes/';
	$dirs = scandir($dir);
	foreach($dirs as $d){
		if( (substr($d,0,1) !=='.') AND (is_dir($dir.$d)) ) {
			$themes[] = $d;
		}
	}
	set_transient( 'easy_table_themes', $themes , 86400 );
	}
	return $themes;
}
function theme_content() {
	if(!isset($_GET['edit'])) {
		return false;
	}
		$theme = $_GET['edit'];
		$dir   = plugin_dir_path(__FILE__).'themes/';
		if(is_writable($dir.$theme.'/style.css')) {
			return file_get_contents($dir.$theme.'/style.css');
		}
}
/**
* Register plugin setting
*/
function easy_table_register_setting() {
	register_setting('easy_table_option_field', 'easy_table_plugin_option');
}

/**
* Render form
* @param array 
*/	
function render_form($fields){
	$output ='<table class="form-table">';
	foreach($fields as $field){
		$field['rowclass'] = isset($field['rowclass']) ? $field['rowclass'] : false;
		$field['label'] = isset($field['label']) ? $field['label'] : '';
		
		if($field['type']=='text'){
			$output .= '<tr '.($field['rowclass'] ? 'class="'.$field['rowclass'].'"': '').'><th><label for="'.$field['name'].'">'.$field['label'].'</label></th>';
			$output .= '<td><input type="text" id="'.$field['name'].'" name="'.$field['name'].'" value="'.$field['value'].'" />';
			$output .= ' <a href="#" class="help-btn ttt" data-title="'.$field['label'].'" data-content="'.$field['description'].'">?</a></td></tr>';
		}
		if($field['type']=='checkbox'){
			$output .= '<tr '.($field['rowclass'] ? 'class="'.$field['rowclass'].'"': '').'><th><label for="'.$field['name'].'">'.$field['label'].'</label></th>';
			$output .= '<td><input type="hidden" name="'.$field['name'].'" value="" /><input type="checkbox" id="'.$field['name'].'" name="'.$field['name'].'" value="'.$field['value'].'" '.$field['attr'].' />';
			$output .= ' <a href="#" class="help-btn ttt" data-title="'.$field['label'].'" data-content="'.$field['description'].'">?</a></td></tr>';
		}
		if($field['type']=='checkboxgroup'){
			$output .= '<tr '.($field['rowclass'] ? 'class="'.$field['rowclass'].'"': '').'><th><label>'.$field['grouplabel'].'</label></th>';
			$output .= '<td>';
			foreach($field['groupitem'] as $key=>$item){
				$output .= '<input type="hidden" name="'.$item['name'].'" value="" /><input type="checkbox" id="'.$item['name'].'" name="'.$item['name'].'" value="'.$item['value'].'" '.$item['attr'].' /> <label for="'.$item['name'].'">'.$item['label'].'</label><br />';
			}
			$output .= ' <a href="#" class="help-btn ttt" data-title="'.$field['label'].'" data-content="'.$field['description'].'">?</a></td></tr>';
		}
		if($field['type'] == 'select'){
			$output .= '<tr '.($field['rowclass'] ? 'class="'.$field['rowclass'].'"': '').'><th><label>'.$field['label'].'</label></th>';
			$output .= '<td>';
			$output .= '<select name="'.$field['name'].'">';
				foreach( (array)$field['values'] as $val=>$name ) {
					$output .= '<option '.(($val==$field['value']) ? 'selected="selected"' : '' ).' value="'.$val.'">'.$name.'</option>';
				}
			$output .= '</select>';
			$output .= ' <a href="#" class="help-btn ttt" data-title="'.$field['label'].'" data-content="'.$field['description'].'">?</a></td></tr>';
		}
	}
	$output .= '</table>';
	return $output;
}

/**
* Register javascript
*/	
function easy_table_script() {
	if(	is_single() AND in_array('is_single',$this->option('scriptloadin')) OR
		is_page() AND in_array('is_page',$this->option('scriptloadin')) OR 
		is_home() AND in_array('is_home',$this->option('scriptloadin')) OR 
		is_archive() AND in_array('is_archive',$this->option('scriptloadin')) OR 
		is_search() AND in_array('is_search',$this->option('scriptloadin'))
		)
	{
	if($this->option('tablesorter')) {
		wp_enqueue_script('easy_table_script',plugins_url( 'js/easy-table-script.js' , __FILE__ ),array('jquery'),$this->easy_table_base('version'),$this->option('scriptinfooter'));
	}
	}
}

/**
* Register stylesheet
*/	
function easy_table_style() {
	if(	is_single() AND in_array('is_single',$this->option('scriptloadin')) OR
		is_page() AND in_array('is_page',$this->option('scriptloadin')) OR 
		is_home() AND in_array('is_home',$this->option('scriptloadin')) OR 
	    is_archive() AND in_array('is_archive',$this->option('scriptloadin')) OR 
	    is_search() AND in_array('is_search',$this->option('scriptloadin'))
		)
	{
	if($this->option('loadcss')) {
		wp_enqueue_style('easy_table_style', plugins_url('themes/'.$this->option('theme').'/style.css', __FILE__),false,$this->easy_table_base('version'));
	}
	}
}

function easy_table_admin_script(){
$page = isset($_GET['page']) ? $_GET['page'] : '';
if($page == $this->easy_table_base('plugin-domain')) { 
if($this->option('tablesorter')) { ?>
<script src="<?php echo plugins_url( 'js/easy-table-script.js' , __FILE__);?>"></script>
<?php }
if($this->option('loadcss')) { ?>
<link rel="stylesheet" href="<?php echo plugins_url('themes/'.$this->option('theme').'/style.css?ver='.$this->easy_table_base('version'), __FILE__);?>" />
<?php } ?>

<link rel="stylesheet" href="<?php echo plugins_url( 'css/admin-style.css?ver='.$this->easy_table_base('version') , __FILE__);?>" />
<script src="<?php echo plugins_url( 'js/ttooltip/script/jquery-ttooltip.min.js' , __FILE__);?>"></script>
<link rel="stylesheet" href="<?php echo plugins_url( 'js/ttooltip/style/jquery-ttooltip.css?ver='.$this->easy_table_base('version') , __FILE__);?>" />

<script type="text/javascript">
//<![CDATA[
	jQuery(document).ready(function($){
		$('.ttt').ttooltip({
			maxwidth:300,
			timeout:500,
			template:'<div class="ttooltip-wrap"><div class="ttooltip-arrow ttooltip-arrow-border"></div><div class="ttooltip-arrow"></div><div class="ttooltip-inner"><h3 class="ttooltip-title"></h3><div class="ttooltip-content"><p></p></div><div class="ttooltip-footer"></div></div></div>'
		}); 
		$('.togglethis a').click(function(e){
			var target = $(this).attr('data-target');
			$(target).toggle();
			e.preventDefault();
		});
	});
//]]>
	</script>
<?php
}
} /* end easy_table_admin_script*/

/**
* Add action link to plugins page
* from plugins listing.
*/
function easy_table_settings_link($links) {
	  $settings_link = '<a href="options-general.php?page='.$this->easy_table_base('plugin-domain').'">'.__('Settings','easy-table').'</a>';
	  array_unshift($links, $settings_link);
	  return $links;
} 

/**
* Contextual help
*/	
function easy_table_help($help) {
	$page = isset($_GET['page']) ? $_GET['page'] : '';
	if($page == $this->easy_table_base('plugin-domain')) {
		$help = '<h2>'.$this->easy_table_base('name').' '.$this->easy_table_base('version').'</h2>';
		$help .= '<h5>'.__('Instruction','easy-table').':</h5>
		<ol><li>'.__('Once plugin installed, go to plugin options page to configure some options','easy-table').'</li>';
		$help .= '<li>'.__('You are ready to write a table in post or page.','easy-table').'</li>';
		$help .= '<li>'.__('To be able write table in widget you have to check <em>Enable render table in widget</em> option in the option page.','easy-table').'</li></ol>';
	return $help;
	}
}

/**
* Add plugin page
*/	
function easy_table_add_page() {
	add_options_page($this->easy_table_base('name'), $this->easy_table_base('name'), 'administrator', $this->easy_table_base('plugin-domain'), array(&$this,'easy_table_page'));
}

/**
* Plugin option page
*/	
function easy_table_page() { ?>
<div class="wrap easy-table-wrap">
<div class="icon32"><img src="<?php echo plugins_url('/images/icon-table.png', __FILE__);?>" /></div>
<h2 class="nav-tab-wrapper">
	<a href="options-general.php?page=<?php echo $this->easy_table_base('plugin-domain');?>" class="nav-tab <?php echo !isset($_GET['gettab']) ? 'nav-tab-active' : '';?>"><?php printf(__('%s Option','easy-table'), $this->easy_table_base('name'));?></a>
	<?php
	/** currently not available
	<a href="options-general.php?page=<?php echo $this->easy_table_base('plugin-domain');?>&gettab=themes" class="nav-tab <?php echo (isset($_GET['gettab']) AND ($_GET['gettab'] == 'themes')) ? 'nav-tab-active' : '';?>"><?php _e('Themes','easy-table');?></a>
	*/?>
	<a href="options-general.php?page=<?php echo $this->easy_table_base('plugin-domain');?>&gettab=support" class="nav-tab <?php echo (isset($_GET['gettab']) AND ($_GET['gettab'] == 'support')) ? 'nav-tab-active' : '';?>"><?php _e('Support','easy-table');?></a>
	<a href="options-general.php?page=<?php echo $this->easy_table_base('plugin-domain');?>&gettab=about" class="nav-tab <?php echo (isset($_GET['gettab']) AND ($_GET['gettab'] == 'about')) ? 'nav-tab-active' : '';?>"><?php _e('About','easy-table');?></a>
</h2>
<?php if(!isset($_GET['gettab'])) : ?>
<div class="left">
<form method="post" action="options.php">
<?php 
wp_nonce_field('update-options'); 
settings_fields('easy_table_option_field');

?>
	<span class="togglethis toggledesc"><em><a href="#" data-target=".help-btn"><?php _e('Show/hide help button');?></a></em></span>
	<h3><?php _e('General options','easy-table');?></h3>
	<?php
	$fields = Array(
		Array(
			'name'			=> 'easy_table_plugin_option[shortcodetag]',
			'label'			=> __('Short code tag','easy-table'),
			'type'			=> 'text',
			'description'	=> __('Shortcode tag, type \'table\' if you want to use [table] short tag.','easy-table'),
			'value'			=> $this->option('shortcodetag')
			)
		,
		Array(
			'name'			=> 'easy_table_plugin_option[attrtag]',
			'label'			=> __('Cell attribute tag','easy-table'),
			'type'			=> 'text',
			'description'	=> __('Cell attribute tag, default is attr.','easy-table'),
			'value'			=> $this->option('attrtag')
			)
		,Array(
			'name'			=> 'easy_table_plugin_option[tablewidget]',
			'label'			=> __('Also render table in widget?','easy-table'),
			'type'			=> 'checkbox',
			'description'	=> __('Check this if you want the table could be rendered in widget.','easy-table'),
			'value'			=> 1,
			'attr'			=> $this->option('tablewidget') ? 'checked="checked"' : '')
		,Array(
			'type'			=> 'checkboxgroup',
			'grouplabel'	=> __('Only load JS/CSS when in this condition','easy-table'),
			'description'	=> __('Please check in where JavaScript and CSS should be loaded','easy-table'),
			'groupitem'		=> Array(
								Array(
								'name' 	=> 'easy_table_plugin_option[scriptloadin][]',
								'label'	=> __('Single','easy-table'),
								'value'	=> 'is_single',
								'attr'	=> in_array('is_single',$this->option('scriptloadin')) ? 'checked="checked"' : ''
								),
								Array(
								'name' 	=> 'easy_table_plugin_option[scriptloadin][]',
								'label'	=> __('Page','easy-table'),
								'value'	=> 'is_page',
								'attr'	=> in_array('is_page',$this->option('scriptloadin')) ? 'checked="checked"' : ''
								),
								Array(
								'name' 	=> 'easy_table_plugin_option[scriptloadin][]',
								'label'	=> __('Front page','easy-table'),
								'value'	=> 'is_home',
								'attr'	=> in_array('is_home',$this->option('scriptloadin')) ? 'checked="checked"' : ''
								),
								Array(
								'name' 	=> 'easy_table_plugin_option[scriptloadin][]',
								'label'	=> __('Archive page','easy-table'),
								'value'	=> 'is_archive',
								'attr'	=> in_array('is_archive',$this->option('scriptloadin')) ? 'checked="checked"' : ''
								),
								Array(
								'name' 	=> 'easy_table_plugin_option[scriptloadin][]',
								'label'	=> __('Search page','easy-table'),
								'value'	=> 'is_search',
								'attr'	=> in_array('is_search',$this->option('scriptloadin')) ? 'checked="checked"' : ''
								)
								)
		)
		,Array(
			'name'			=> 'easy_table_plugin_option[scriptinfooter]',
			'label'			=> __('Load script on footer?','easy-table'),
			'type'			=> 'checkbox',
			'description'	=> __('Check this if you want the script to be rendered in footer. Try to check or uncheck this if you experienced conflict with another JavaScript library (not guaranteed though).','easy-table'),
			'value'			=> 1,
			'attr'			=> $this->option('scriptinfooter') ? 'checked="checked"' : ''
		)
		
	);
	echo $this->render_form($fields);

	$fields = Array(
		Array(	
			'name'			=> 'easy_table_plugin_option[tablesorter]',
			'label'			=> __('Use tablesorter?','easy-table'),
			'type'			=> 'checkbox',
			'value'			=> 1,
			'description'	=> __('Check this to use tablesorter jQuery plugin','easy-table'),
			'attr'			=> $this->option('tablesorter') ? 'checked="checked"':'')
		,Array(
			'name'			=> 'easy_table_plugin_option[th]',
			'label'			=> __('Use TH for the first row?','easy-table'),
			'type'			=> 'checkbox',
			'value'			=> 1,
			'description'	=> __('Check this if you want to use first row as table head (required by tablesorter)','easy-table'),
			'attr'			=> $this->option('th') ? 'checked="checked"' : '')
		,Array(
			'name'			=> 'easy_table_plugin_option[loadcss]',
			'label'			=> __('Load CSS?','easy-table'),
			'type'			=> 'checkbox',
			'value'			=> 1,
			'description'	=> __('Check this to use CSS included in this plugin to styling table, you may unceck if you want to write your own style.','easy-table'),
			'attr'			=> $this->option('loadcss') ? 'checked="checked"':'')	
		,Array(
			'name'			=> 'easy_table_plugin_option[class]',
			'label'			=> __('Table class','easy-table'),
			'type'			=> 'text',
			'description'	=> __('Additional table class attribute.','easy-table'),
			'value'			=> $this->option('class'))
		,Array(
			'name'			=> 'easy_table_plugin_option[width]',
			'label'			=> __('Table width','easy-table'),
			'type'			=> 'text',
			'description'	=> __('Table width, in pixel or percent (may be overriden by CSS)','easy-table'),
			'value'			=> $this->option('width'))
		,Array(
			'name'			=>'easy_table_plugin_option[border]',
			'label'			=> __('Table border','easy-table'),
			'type'			=> 'text',
			'description'	=> __('Table border (may be overriden by CSS)','easy-table'),
			'value'			=> $this->option('border'))
		,Array(
			'name'			=>'easy_table_plugin_option[align]',
			'label'			=> __('Table align','easy-table'),
			'type'			=> 'text',
			'description'	=> __('Table align (left, center, right)','easy-table'),
			'value'			=> $this->option('align'))
	);
	?>	

	<h3><?php _e('Table options','easy-table');?></h3>
	<?php
		echo $this->render_form($fields);
	?>
	<h3><?php _e('Theme selector','easy-table');?></h3>
	<?php
	$fields = Array(
		Array(	
			'name'			=> 'easy_table_plugin_option[theme]',
			'label'			=> __('Default theme','easy-table'),
			'type'			=> 'select',
			'value'			=> $this->option('theme'),
			'values'		=> array_combine($this->themes(),$this->themes()),
			'description'	=> __('Select default theme of the table','easy-table')
	)
	);
		echo $this->render_form($fields);
	?>
	
	<h3><?php _e('Data options','easy-table');?></h3>
	<?php
		$fields = Array(
		Array(	
			'name'			=> 'easy_table_plugin_option[limit]',
			'label'			=> __('Row limit','easy-table'),
			'type'			=> 'text',
			'value'			=> $this->option('limit'),
			'rowclass'		=> 'new',
			'description'	=>__('Max row to convert to table, default 0 (unlimited)','easy-table')
		),
		Array(	
			'name'			=> 'easy_table_plugin_option[trim]',
			'label'			=> __('Trim cell data?','easy-table'),
			'type'			=> 'checkbox',
			'value'			=> 1,
			'attr'			=> $this->option('trim') ? 'checked="checked"':'',
			'rowclass'		=> 'new',
			'description'	=>__('Trim empty character around cell data','easy-table')
		),
		);
		echo $this->render_form($fields);
	?>
	
	<h3><?php _e('Parser options','easy-table');?></h3>
	<p><em><?php _e('Do not change this unless you know what you\'re doing','easy-table');?></em>
	</p>
	<?php
	$fields = Array(
		Array(
			'name'			=> 'easy_table_plugin_option[nl]',
			'label'			=> __('New line replacement','easy-table'),
			'type'			=> 'text',
			'value'			=> $this->option('nl'),
			'description'	=> __('Since new line is used by parser, you need specify character as a replacement.','easy-table'))
		,Array(
			'name'			=> 'easy_table_plugin_option[terminator]',
			'label'			=> __('Row terminator','easy-table'),
			'type'			=> 'text',
			'value'			=> $this->option('terminator'),
			'rowclass'		=> 'new',
			'description'	=> __('This caharacter will converted into new row. Default value \n (this is invisible character when you press Enter). If your new line not converted as new row in the table, try use \r instead.','easy-table'))
		,Array(
			'name'			=> 'easy_table_plugin_option[delimiter]',
			'label'			=> __('Delimiter','easy-table'),
			'type'			=> 'text',
			'value'			=> $this->option('delimiter'),
			'description'	=> __('CSV delimiter (default is comma)','easy-table'))
		,Array(
			'name'			=> 'easy_table_plugin_option[enclosure]',
			'label'			=> __('Enclosure','easy-table'),
			'type'			=> 'text',
			'value'			=> htmlentities($this->option('enclosure')),
			'description'	=> __('CSV enclosure (default is double quote)','easy-table'))
		,Array(	
			'name'			=> 'easy_table_plugin_option[escape]',
			'label'			=> __('Escape','easy-table'),
			'type'			=> 'text',
			'value'			=> $this->option('escape'),
			'description'	=>__('CSV escape (default is backslash)','easy-table'))
		,Array(
			'name'			=> 'easy_table_plugin_option[fixlinebreak]',
			'label'			=> __('Fix linebreak','easy-table'),
			'type'			=> 'checkbox',
			'value'			=> 1,
			'description'	=> __('If terminator is not default (linebreak), you may encounter some issue with linebreak inside cell, try to check or uncheck this to resolve','easy-table'),
			'attr'			=> $this->option('fixlinebreak') ? 'checked="checked"' : '')
		,Array(
			'name'			=> 'easy_table_plugin_option[csvfile]',
			'label'			=> __('Allow read CSV from file?','easy-table'),
			'type'			=> 'checkbox',
			'value'			=> 1,
			'description'	=> __('Check this if you also want to convert CSV file to table','easy-table'),
			'attr'			=> $this->option('csvfile') ? 'checked="checked"' : '')
		);
		echo $this->render_form($fields);
	?>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="easy_table_option_field" value="easy_table_plugin_option" />
<p><input type="submit" class="button-primary" value="<?php _e('Save','easy-table');?>" /> </p>
</form>
</div>
<div class="right">
<?php

$defaulttableexample = '
[table caption="Just test table" width="500" colwidth="20|100|50" colalign="left|left|center|left|right"]
no,head1,head2,head3,head4
1,row1col1,row1col2,row1col3,100
2,row2col1,row2col2,row2col3,20000
3,row3col1,,row3col3,1405
4,row4col1,row4col2,row4col3,23023
[/table]	';
$tableexample = $defaulttableexample;
if(isset($_POST['test-easy-table'])){
	$tableexample = $_POST['easy-table-test-area'];
}

if(isset($_POST['test-easy-table-reset'])){
	$tableexample = $defaulttableexample;
}

?>
<h3><?php _e('Possible parameter','easy-table');?></h3>
<p><?php _e('These parameters commonly can override global options in the left side of this page. Example usage:','easy-table');?></p>
<p> <code>[table param1="param-value1" param2="param-value2"]table data[/table]</code></p>
<ol>
<li><strong>class</strong>, <?php _e('default value','easy-table');?> <em>'table-striped'</em>, <?php _e('another value','easy-table');?> <em>table-bordered, table-striped, table-condensed</em></li>
<li><strong>caption</strong>,<?php _e('default value','easy-table');?> <em>''</em></li>
<li><strong>width</strong>, <?php _e('default value','easy-table');?> <em>'100%'</em></li>
<li><strong>align</strong>, <?php _e('default value','easy-table');?> <em>'left'</em></li>
<li><strong>th</strong>, <?php _e('default value','easy-table');?> <em>'true'</em></li>
<li><strong>tf</strong>, <?php _e('default value','easy-table');?> <em>'false'</em></li>
<li><strong>border</strong>, <?php _e('default value','easy-table');?> <em>'0'</em></li>
<li><strong>id</strong>, <?php _e('default value','easy-table');?> <em>'false'</em></li>
<li><strong>tablesorter</strong>, <?php _e('default value','easy-table');?> <em>'false'</em></li>
<li><strong>file</strong>, <?php _e('default value','easy-table');?> <em>'false'</em></li>
<li><strong>sort</strong>, <?php _e('default value','easy-table');?> <em>''</em></li>
<li class="new"><strong>trim</strong>, <?php _e('default value','easy-table');?> <em>false</em></li>
<li class="new"><strong>style</strong>, <?php _e('default value','easy-table');?> <em>''</em></li>
<li class="new"><strong>limit</strong>, <?php _e('default value','easy-table');?> <em>0</em></li>
<li class="new"><strong>terminator</strong>, <?php _e('default value','easy-table');?> <em>\n</em></li>
<li class="new"><strong>colalign</strong>, <?php _e('default value','easy-table');?> <em>''</em>, see example on the test area</li>
<li class="new"><strong>colwidth</strong>, <?php _e('default value','easy-table');?> <em>''</em>, see example on the test area</li>
</ol>
<h3><?php printf('Example usage of %s parameter','sort','easy-table');?></h3>
<p><em>sort</em> <?php _e('parameter is for initial sorting order. Value for each column separated by comma. See example below:','easy-table');?></p>
<ol>
<li><?php _e('Set initial order of first column descending and second column ascending:','easy-table');?>
<pre><code>[table sort="desc,asc"]
col1,col2,col3
col4,col5,col6
[/table]</code></pre>
</li>
<li><?php _e('Set initial order of second column descending:','easy-table');?>
<pre><code>[table sort=",desc,asc"]
col1,col2,col3
col4,col5,col6
[/table]</code></pre>
</li>
<li><?php _e('Additionaly, sort option also can be set via sort attr in a cell. See example below','easy-table');?></li>
</ol>
<h3><?php _e('Cell attribute tag','easy-table');?></h3>
<ol>
<li><p><strong>attr</strong>, <?php _e('To set attribute for cell eg. class, colspan, rowspan, etc','easy-table');?></p>
	<p><?php _e('Example','easy-table');?>: </p>

<pre><code>[table]
col1,col2[attr style="width:200px" class="someclass"],col3
col4,col5,col6
[/table]
</code></pre>
</li>

<li><p><strong>attr sort</strong>, <?php _e('To set initial sort order, this is intended to TH (first row) only.','easy-table');?></p>
	<p><?php _e('Example: sort second column descending ','easy-table');?> </p>

<pre><code>[table]
col1,col2[attr sort="desc"],col3
col4,col5,col6
[/table]
</code></pre>
<p><?php printf('To disable sort, use "%s". In the example below first column is not sortable','false','easy-table');?> </p>

<pre><code>[table]
col1[attr sort="false"],col2,col3
col4,col5,col6
[/table]
</code></pre>
</li>
</ol>

<h3><?php _e('Test area:','easy-table');?></h3>
	<form action="" method="post">
	<textarea name="easy-table-test-area" style="width:500px;height:200px;border:1px solid #ccc"><?php echo trim(htmlentities(stripslashes($tableexample)));?>
	</textarea>
	<input type="hidden" name="test-easy-table" value="1" />
	<p><input class="button" type="submit" name="test-easy-table-reset" value="<?php _e('Reset','easy-table');?>" />
	<input class="button-primary" type="submit" value="<?php _e('Update preview','easy-table');?> &raquo;" /></p></form>
	<div>
	<h3><?php _e('Preview','easy-table');?></h3>
	<?php echo do_shortcode(stripslashes($tableexample));?>
	</div>

</div>
<div class="clear"></div>
<?php elseif($_GET['gettab'] == 'themes') : ?>
	<h3><?php _e('Easy Table theme editor');?></h3>

	<div class="row">
		<div class="columns nine">
			<textarea name="" id="easy-table-theme-editor"><?php echo esc_textarea($this->theme_content());?></textarea>
			<input type="submit" class="button primary" value="Save"/>
		</div>
		<div class="columns three">
			<ul>
				<?php
					foreach($this->themes() as $theme) {
						echo '
						<li><a href="#">'.$theme.'</a> 
						<a href="options-general.php?page=easy-table&gettab=themes&edit='.$theme.'">edit</a>
						<a href="&edit-theme=1&clone=1#">clone</a>
						<a href="#">delete</a>
						<a href="#">preview</a>
						</li>';
					}
				?>
			</ul>
			<form action="">
				New theme: <br/>
				<input type="text" value="" placeholder="Theme name" name="themename"/>
				<input type="submit" value="Create"/>
			</form>
		</div>
	</div>

<?php elseif($_GET['gettab'] == 'support') : ?>
<p><?php _e('I have tried to make this plugin can be used as easy as possible and documentation as complete as possible. However it is also possible that you are still confused. Therefore feel free to ask. I would be happy to answer.','easy-table');?></p>
<p><?php _e('You can use this discussion to get support, request feature or reporting bug.','easy-table');?></p>
<p><a target="_blank" href="http://takien.com/plugins/easy-table"><?php _e('Before you ask something, make sure you have read documentation here!','easy-table');?></a></p>

<div id="disqus_thread"></div>
<script type="text/javascript">
/* <![CDATA[ */
    var disqus_url = 'http://takien.com/1126/easy-table-is-the-easiest-way-to-create-table-in-wordpress.php';
    var disqus_identifier = '1126 http://takien.com/?p=1126';
    var disqus_container_id = 'disqus_thread';
    var disqus_domain = 'disqus.com';
    var disqus_shortname = 'takien';
    var disqus_title = "Easy Table is The Easiest Way to Create Table in WordPress";
        var disqus_config = function () {
        var config = this; 
        config.callbacks.preData.push(function() {
            // clear out the container (its filled for SEO/legacy purposes)
            document.getElementById(disqus_container_id).innerHTML = '';
        });
                config.callbacks.onReady.push(function() {
            // sync comments in the background so we don't block the page
            DISQUS.request.get('?cf_action=sync_comments&post_id=1126');
        });
                    };
    var facebookXdReceiverPath = 'http://takien.com/wp-content/plugins/disqus-comment-system/xd_receiver.htm';
/* ]]> */
</script>

<script type="text/javascript">
/* <![CDATA[ */
    var DsqLocal = {
        'trackbacks': [
        ],
        'trackback_url': "http:\/\/takien.com\/1126\/easy-table-is-the-easiest-way-to-create-table-in-wordpress.php\/trackback"    };
/* ]]> */
</script>

<script type="text/javascript">
/* <![CDATA[ */
(function() {
    var dsq = document.createElement('script'); dsq.type = 'text/javascript';
    dsq.async = true;
        dsq.src = 'http' + '://' + disqus_shortname + '.' + disqus_domain + '/embed.js?pname=wordpress&pver=2.72';
    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
})();
/* ]]> */
</script>
<?php elseif ($_GET['gettab'] == 'about') : ?>
<?php
require_once(ABSPATH.'wp-admin/includes/plugin-install.php');
$api = plugins_api('plugin_information', array('slug' => 'easy-table' ));
?>
 	<div>
	<h2 class="mainheader"><?php echo $this->easy_table_base('name') .' ' . $this->easy_table_base('version'); ?></h2>
		<?php if ( ! empty($api->download_link) && ( current_user_can('install_plugins') || current_user_can('update_plugins') ) ) : ?>
		<p class="action-button">
		<?php
		$status = install_plugin_install_status($api);
		switch ( $status['status'] ) {
			case 'install':
				if ( $status['url'] )
					echo '<a href="' . $status['url'] . '" target="_parent">' . __('Install Now') . '</a>';
				break;
			case 'update_available':
				if ( $status['url'] )
					echo '<a  class="red" href="' . $status['url'] . '" target="_parent">' . __('Install Update Now') .'</a>';
				break;
			case 'newer_installed':
				echo '<a class="green">' . sprintf(__('Newer Version (%s) Installed'), $status['version']) . '</a>';
				break;
			case 'latest_installed':
				echo '<a class="green">' . __('Latest Version Installed') . '</a>';
				break;
		}
		?>
		</p>
		<?php endif; ?>
		
		<ul>
<?php if ( ! empty($api->version) ) : ?>
			<li><strong><?php _e('Latest Version:','easy-table') ?></strong> <?php echo $api->version ?></li>
<?php endif; if ( ! empty($api->author) ) : ?>
			<li><strong><?php _e('Author:') ?></strong> <?php echo links_add_target($api->author, '_blank') ?></li>
<?php endif; if ( ! empty($api->last_updated) ) : ?>
			<li><strong><?php _e('Last Updated:') ?></strong> <span title="<?php echo $api->last_updated ?>"><?php
							printf( __('%s ago'), human_time_diff(strtotime($api->last_updated)) ) ?></span></li>
<?php endif; if ( ! empty($api->requires) ) : ?>
			<li><strong><?php _e('Requires WordPress Version:') ?></strong> <?php printf(__('%s or higher'), $api->requires) ?></li>
<?php endif; if ( ! empty($api->tested) ) : ?>
			<li><strong><?php _e('Compatible up to:') ?></strong> <?php echo $api->tested ?></li>
<?php endif; if ( ! empty($api->downloaded) ) : ?>
			<li><strong><?php _e('Downloaded:') ?></strong> <?php printf(_n('%s time', '%s times', $api->downloaded), number_format_i18n($api->downloaded)) ?></li>
<?php endif; if ( ! empty($api->slug) && empty($api->external) ) : ?>
			<li><a target="_blank" href="http://wordpress.org/extend/plugins/<?php echo $api->slug ?>/"><?php _e('WordPress.org Plugin Page &#187;') ?></a></li>
<?php endif; if ( ! empty($api->homepage) ) : ?>
			<li><a target="_blank" href="<?php echo $api->homepage ?>"><?php _e('Plugin Homepage  &#187;') ?></a></li>
<?php endif; ?>
		</ul>
		<?php if ( ! empty($api->rating) ) : ?>
		<h3><?php _e('Average Rating') ?></h3>
		<div class="star-holder" title="<?php printf(_n('(based on %s rating)', '(based on %s ratings)', $api->num_ratings), number_format_i18n($api->num_ratings)); ?>">
			<?php if ( version_compare( $GLOBALS['wp_version'], 3.4, '<') ) { ?>
			<div class="star star-rating" style="width: <?php echo esc_attr($api->rating) ?>px"></div>
			<div class="star star5"><img src="<?php echo admin_url('images/star.png?v=20110615'); ?>" alt="<?php esc_attr_e('5 stars') ?>" /></div>
			<div class="star star4"><img src="<?php echo admin_url('images/star.png?v=20110615'); ?>" alt="<?php esc_attr_e('4 stars') ?>" /></div>
			<div class="star star3"><img src="<?php echo admin_url('images/star.png?v=20110615'); ?>" alt="<?php esc_attr_e('3 stars') ?>" /></div>
			<div class="star star2"><img src="<?php echo admin_url('images/star.png?v=20110615'); ?>" alt="<?php esc_attr_e('2 stars') ?>" /></div>
			<div class="star star1"><img src="<?php echo admin_url('images/star.png?v=20110615'); ?>" alt="<?php esc_attr_e('1 star') ?>" /></div>
			<?php
			}
			else { ?>
			<div class="star star-rating" style="width: <?php echo esc_attr( str_replace( ',', '.', $api->rating ) ); ?>px"></div>
			<?php } ?>
		</div>
		<small><?php printf(_n('(based on %s rating)', '(based on %s ratings)', $api->num_ratings), number_format_i18n($api->num_ratings)); ?></small>
		
		
		<h3><?php _e('Support my work with donation','easy-table');?>:</h3>
		
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<p><?php _e('Don\'t have money? No problem, you can rate my plugin instead.','easy-table');?> 
<a target="_blank" href="http://wordpress.org/extend/plugins/easy-table/"><?php _e('Click here to rate','easy-table');?></a></p>

<h3><?php _e('Thanks to','easy-table');?>:</h3>
		
<ul>
<li><a target="_blank" href="<?php echo site_url();?>">You</a></li>
<li><a target="_blank" href="http://php.net">PHP</a></li>
<li><a target="_blank" href="http://wordpress.org">WordPress</a></li>
<li>Tablesorter <?php _e('by','easy-table');?> <a target="_blank" href="http://tablesorter.com">Christian Bach</a></li>
<li>CSS <?php _e('by','easy-table');?> <a target="_blank" href="http://twitter.github.com/bootstrap">Twitter Bootstrap</a></li>
<li>jQuery metadata <?php _e('by','easy-table');?> <a target="_blank" href="https://github.com/jquery/jquery-metadata/">John Resig</a></li>
<li>CuscoSky table styles <?php _e('by','easy-table');?> <a target="_blank" href="http://www.buayacorp.com">Braulio Soncco</a></li>
<li>Tablesorter updated version <?php _e('by','easy-table');?> <a target="_blank" href="https://github.com/Mottie/tablesorter">Rob Garrison</a></li>

</ul>
		<?php endif; ?>
	</div>
<?php endif; ?>
</div><!--wrap-->

<?php
	}
			
} /* end class */
}
add_action('init', 'easy_table_init');
function easy_table_init() {
	if (class_exists('EasyTable')) {
		new EasyTable();
	}
}

/**
* Create function str_getcsv if not exists in server
* @since version 0.2
* Use dedicated str_getcsv since 1.1
*/	
if (!function_exists('easy_table_str_getcsv')) {
	function easy_table_str_getcsv($input, $delimiter = ",", $enclosure = '"', $escape = '\\'){
		
		/** 
		* Bug fix, custom terminator wont work
		* @since version 1.1.1
		*/
		if( ("\r" === $delimiter) OR ("\n" === $delimiter) ) {
		}
		else {
			$input = str_replace("\n",'NLINEBREAK',$input);
			$input = str_replace("\r",'RLINEBREAK',$input);
		}
		$input = str_ireplace( $escape.$delimiter,'_ESCAPED_SEPATATOR_',$input );
		
		$fiveMBs = 5 * 1024 * 1024;
		if (($handle = fopen("php://temp/maxmemory:$fiveMBs", 'r+')) !== FALSE) {
		fputs($handle, $input);
		rewind($handle);
		$line = -1;
		$return = Array();
		/* add dynamic row limit, 
		* @since: 1.0
		*/
		
		$option = get_option('easy_table_plugin_option');
		$limit  = !empty($option['limit']) ? (int)$option['limit'] : 2000;
		while (($data = @fgetcsv( $handle, $limit, $delimiter, $enclosure )) !== FALSE) {
			$num = count($data);
			for ($c=0; $c < $num; $c++) {
				$line++;
				$data[$c] = str_replace('NLINEBREAK',"\n",$data[$c]);
				$data[$c] = str_replace('RLINEBREAK',"\r",$data[$c]);
				$data[$c] = str_replace('_ESCAPED_SEPATATOR_',$delimiter,$data[$c]);
				$return[$line] = $data[$c];
			}
		}
		fclose($handle);
		return $return;
		}
	}
}
if(!function_exists('array_replace')) {
	function array_replace(){
		$array=array();   
		$n=func_num_args();
		while ($n-- >0) {
			$array+=func_get_arg($n);
		}
		return $array;
	}
}