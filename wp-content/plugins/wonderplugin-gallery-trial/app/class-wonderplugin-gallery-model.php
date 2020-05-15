<?php 

if ( ! defined( 'ABSPATH' ) )
	exit;
	
require_once 'wonderplugin-gallery-functions.php';

class WonderPlugin_Gallery_Model {

	private $controller;
	
	function __construct($controller) {
		
		$this->controller = $controller;

		$this->multilingual = false;

		if ( get_option( 'wonderplugin_gallery_supportmultilingual', 1 ) == 1 )
		{
			$defaultlang = apply_filters( 'wpml_default_language', NULL);
			if ( !empty($defaultlang) )
			{
				$this->multilingual = true;
				$this->multilingualsys = "wpml";
				$this->defaultlang = $defaultlang;
				$this->currentlang = apply_filters('wpml_current_language', NULL );
			}
		}
	}
	
	function get_upload_path() {
		
		$uploads = wp_upload_dir();
		return $uploads['basedir'] . '/wonderplugin-gallery/';
	}
	
	function get_upload_url() {
	
		$uploads = wp_upload_dir();
		return $uploads['baseurl'] . '/wonderplugin-gallery/';
	}
	
	function xml_cdata( $str ) {

		if ( ! seems_utf8( $str ) ) {
			$str = utf8_encode( $str );
		}

		$str = '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $str ) . ']]>';

		return $str;
	}

	function replace_data($replace_list, $data)
	{
		foreach($replace_list as $replace)
		{
			$data = str_replace($replace['search'], $replace['replace'], $data);
		}

		return $data;
	}

	function search_replace_items($post)
	{
		$allitems = sanitize_text_field($_POST['allitems']);
		$itemid = sanitize_text_field($_POST['itemid']);

		$replace_list = array();
		for ($i = 0; ; $i++)
		{
			if (empty($post['standalonesearch' . $i]) || empty($post['standalonereplace' . $i]))
				break;

			$replace_list[] = array(
					'search' => str_replace('/', '\\/', sanitize_text_field($post['standalonesearch' . $i])),
					'replace' => str_replace('/', '\\/', sanitize_text_field($post['standalonereplace' . $i]))
			);
		}

		global $wpdb;

		if (!$this->is_db_table_exists())
			$this->create_db_table();

		$table_name = $wpdb->prefix . "wonderplugin_gallery";

		$total = 0;

		foreach($replace_list as $replace)
		{
			$search = $replace['search'];
			$replace = $replace['replace'];

			if ($allitems)
			{
				$ret = $wpdb->query( $wpdb->prepare(
						"UPDATE $table_name SET data = REPLACE(data, %s, %s) WHERE INSTR(data, %s) > 0",
						$search,
						$replace,
						$search
				));
			}
			else
			{
				$ret = $wpdb->query( $wpdb->prepare(
						"UPDATE $table_name SET data = REPLACE(data, %s, %s) WHERE INSTR(data, %s) > 0 AND id = %d",
						$search,
						$replace,
						$search,
						$itemid
				));
			}

			if ($ret > $total)
				$total = $ret;
		}

		if (!$total)
		{
			return array(
					'success' => false,
					'message' => 'No gallery modified' .  (isset($wpdb->lasterror) ? $wpdb->lasterror : '')
			);
		}

		return array(
				'success' => true,
				'message' => sprintf( _n( '%s gallery', '%s galleries', $total), $total) . ' modified'
		);
	}

	function import_gallery($post, $files)
	{
		if (!isset($files['importxml']))
		{
			return array(
					'success' => false,
					'message' => 'No file or invalid file sent.'
			);
		}

		if (!empty($files['importxml']['error']))
		{
			$message = 'XML file error.';

			switch ($files['importxml']['error']) {
				case UPLOAD_ERR_NO_FILE:
					$message = 'No file sent.';
					break;
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					$message = 'Exceeded filesize limit.';
					break;
			}

			return array(
					'success' => false,
					'message' => $message
			);
		}

		if ($files['importxml']['type'] != 'text/xml')
		{
			return array(
					'success' => false,
					'message' => 'Not an xml file'
			);
		}

		add_filter( 'wp_check_filetype_and_ext', 'wonderplugin_gallery_wp_check_filetype_and_ext', 10, 4);

		$xmlfile = wp_handle_upload($files['importxml'], array(
				'test_form' => false,
				'mimes' => array('xml' => 'text/xml')
		));

		remove_filter( 'wp_check_filetype_and_ext', 'wonderplugin_gallery_wp_check_filetype_and_ext');

		if ( empty($xmlfile) || !empty( $xmlfile['error'] ) ) {
			return array(
					'success' => false,
					'message' => (!empty($xmlfile) && !empty( $xmlfile['error'] )) ? $xmlfile['error']: 'Invalid xml file'
			);
		}

		$content = file_get_contents($xmlfile['file']);

		$xmlparser = xml_parser_create();
		xml_parse_into_struct($xmlparser, $content, $values, $index);
		xml_parser_free($xmlparser);

		if (empty($index) || empty($index['WONDERPLUGINGALLERY']) || empty($index['ID']))
		{
			return array(
					'success' => false,
					'message' => 'Not an exported xml file'
			);
		}

		$keepid = (!empty($post['keepid'])) ? true : false;
		$authorid = sanitize_text_field($post['authorid']);

		$replace_list = array();
		for ($i = 0; ; $i++)
		{
			if (empty($post['olddomain' . $i]) || empty($post['newdomain' . $i]))
				break;

			$replace_list[] = array(
					'search' => str_replace('/', '\\/', sanitize_text_field($post['olddomain' . $i])),
					'replace' => str_replace('/', '\\/', sanitize_text_field($post['newdomain' . $i]))
			);
		}

		$import_items = Array();
		foreach($index['ID'] as $key => $val)
		{
			$import_items[] = Array(
					'id' => ($keepid ? $values[$index['ID'][$key]]['value'] : 0),
					'name' => $values[$index['NAME'][$key]]['value'],
					'data' => $this->replace_data($replace_list, $values[$index['DATA'][$key]]['value']),
					'time' => $values[$index['TIME'][$key]]['value'],
					'authorid' => $authorid
			);
		}

		if (empty($import_items))
		{
			return array(
					'success' => false,
					'message' => 'No gallery found'
			);
		}

		global $wpdb;

		if (!$this->is_db_table_exists())
			$this->create_db_table();

		$table_name = $wpdb->prefix . "wonderplugin_gallery";

		$total = 0;
		foreach($import_items as $import_item)
		{
			$ret = $wpdb->query($wpdb->prepare(
					"
					INSERT INTO $table_name (id, name, data, time, authorid)
					VALUES (%d, %s, %s, %s, %s) ON DUPLICATE KEY UPDATE
					name=%s, data=%s, time=%s, authorid=%s
					",
					$import_item['id'], $import_item['name'], $import_item['data'], $import_item['time'], $import_item['authorid'],
					$import_item['name'], $import_item['data'], $import_item['time'], $import_item['authorid']
			));

			if ($ret)
				$total++;
		}

		if (!$total)
		{
			return array(
					'success' => false,
					'message' => 'No gallery imported' .  (isset($wpdb->lasterror) ? $wpdb->lasterror : '')
			);
		}

		return array(
				'success' => true,
				'message' => sprintf( _n( '%s gallery', '%s galleries', $total), $total) . ' imported'
		);

	}
	
	function export_gallery()
	{
		if ( !check_admin_referer('wonderplugin-gallery', 'wonderplugin-gallery-export') || !isset($_POST['allgallery']) || !isset($_POST['galleryid']) || !is_numeric($_POST['galleryid']) )
			exit;

		$allgallery = sanitize_text_field($_POST['allgallery']);
		$galleryid = sanitize_text_field($_POST['galleryid']);

		if ($allgallery)
			$data = $this->get_list_data(true);
		else
			$data = array($this->get_list_item_data($galleryid));

		header('Content-Description: File Transfer');
		header("Content-Disposition: attachment; filename=wonderplugin_gallery_export.xml");
		header('Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true);
		header("Cache-Control: no-cache, no-store, must-revalidate");
		header("Pragma: no-cache");
		header("Expires: 0");
		$output = fopen("php://output", "w");

		echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";
		echo "<WONDERPLUGINGALLERY>\r\n";
		foreach($data as $row)
		{
			if (empty($row))
				continue;

			echo "<ID>" . intval($row["id"]) . "</ID>\r\n";
			echo "<NAME>" . $this->xml_cdata($row["name"]) . "</NAME>\r\n";
			echo "<DATA>" . $this->xml_cdata($row["data"]) . "</DATA>\r\n";
			echo "<TIME>" . $this->xml_cdata($row["time"]) . "</TIME>\r\n";
			echo "<AUTHORID>" . $this->xml_cdata($row["authorid"]) . "</AUTHORID>\r\n";
		}
		echo '</WONDERPLUGINGALLERY>';

		fclose($output);
		exit;
	}

	function get_list_item_data($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";

		return $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) , ARRAY_A);
	}

	function eacape_html_quotes($str) {
	
		$result = str_replace("\'", "&#39;", $str);
		$result = str_replace('\"', '&quot;', $result);
		$result = str_replace("'", "&#39;", $result);
		$result = str_replace('"', '&quot;', $result);
		return $result;
	}
	
	function generate_schema_code($id, $slides, $itemTime) {
		
		$ret = "\r\n\r\n" . '<div id="wonderplugin-gallery-schema-markup-' . $id . '" class="wonderplugin-gallery-schema-markup" style="display:none;">';
		
		foreach ($slides as $slide)
		{
			$ret .= "\r\n" . '<div itemprop="video" itemscope itemtype="http://schema.org/VideoObject">';
			$ret .= "\r\n" . '<span itemprop="name">' . $slide->title . '</span>';
			$ret .= "\r\n" . '<span itemprop="description">' . $slide->description . '</span>';
			$ret .= "\r\n" . '<meta itemprop="thumbnailUrl" content="' . $slide->image . '" />';
			$ret .= "\r\n" . '<meta itemprop="uploadDate" content="' . $itemTime . '" />';
			
			if ($slide->type == 1)
			{
				$ret .= "\r\n" . '<meta itemprop="contentURL" content="' . $slide->mp4 . '" />';
			}
			else if ($slide->type == 2 || $slide->type == 3 || $slide->type == 4 || $slide->type == 5)
			{
				$ret .= "\r\n" . '<meta itemprop="embedURL" content="' . $slide->video . '" />';
			}
			$ret .= "\r\n" . '</div>';	
		}
		
		$ret .= "\r\n" . "</div>" . "\r\n";
		
		return $ret;
	}
	
	function get_multilingual_slide_text($slide, $attr, $currentlang) {

		$result = !empty($slide->{$attr}) ? $slide->{$attr} : '';

		if ($this->multilingual && !empty($slide->langs) )		
		{
			$langs = json_decode($slide->langs, true);
			if ( !empty($langs) && array_key_exists($currentlang, $langs) && array_key_exists($attr, $langs[$currentlang]))
			{
				$result = $langs[$currentlang][$attr];
			}
		}

		return $result;
	}

	function generate_body_code($id, $contents, $data_attributes, $has_wrapper) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
		
		if ( !$this->is_db_table_exists() )
		{
			return '<p>The specified gallery does not exist.</p>';
		}
		
		$ret = "";
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$data = json_decode(trim($item_row->data));
			
			if ( isset($data->publish_status) && ($data->publish_status === 0) )
			{
				return '<p>The specified gallery is trashed.</p>';
			}
			
			$itemTime = $item_row->time;
			
			if (!empty($data_attributes))
			{
				foreach($data_attributes as $key => $value)
				{
					$data->{$key} = $value;
				}
			}
			
			foreach($data as $datakey => &$value)
			{
				if ($datakey == 'customjs')
					continue;
				
				if ( is_string($value) )
					$value = wp_kses_post($value);
			}
			
			if (isset($data->customcss) && strlen($data->customcss) > 0)
			{
				$customcss = str_replace("\r", " ", $data->customcss);
				$customcss = str_replace("\n", " ", $customcss);
				$customcss = str_replace("GALLERYID", $id, $customcss);
				$ret .= '<style type="text/css">' . $customcss . '</style>';
			}
			
			$ret .= '<div class="wonderplugingallery-container" id="wonderplugingallery-container-' . $id . '" style="';
			
			if ( (isset($data->fullwidth) && strtolower($data->fullwidth) === 'true') || (isset($data->responsive) && strtolower($data->responsive) === 'true' && !isset($data->fullwidth)) )
				$ret .= 'max-width:100%;';
			else
				$ret .= 'max-width:' . $data->width . 'px;';
			
			
			if ($has_wrapper)
				$ret .= 'margin:0 auto 180px;';
			else
				$ret .= 'margin:0 auto;';
			
			$ret .= '">';
			
			// div data tag
			$ret .= '<div class="wonderplugingallery" id="wonderplugingallery-' . $id . '"';
			if ( isset($data->specifyid) && strtolower($data->specifyid) === 'true' )
				$ret .= ' data-galleryid="'. $id . '"';
			
			$ret .= ' data-width="' . $data->width . '" data-height="' . $data->height . '" data-skin="' . $data->skin . '"';
			
			if (isset($data->dataoptions) && strlen($data->dataoptions) > 0)
			{
				$ret .= ' ' . stripslashes($data->dataoptions);
			}
			
			$boolOptions = array('playsinline', 'mutevideo', 'random', 'autoslide', 'autoplayvideo', 'schemamarkup', 'stopallplaying', 'reloadonvideoend', 'donotuseposter', 'enabletabindex', 'loadnextonvideoend', 'hidetitlewhenvideoisplaying', 'disablehovereventontouch', 'autoslideandplayafterfirstplayed', 'html5player', 'responsive', 'fullwidth', 'showtitle', 'showdescription', 'showplaybutton', 'showfullscreenbutton', 'showtimer', 'showcarousel', 'galleryshadow', 'slideshadow', 'thumbshowtitle', 'thumbshadow', 'lightboxshowtitle', 'lightboxshowdescription', 'specifyid', 'donotinit', 'addinitscript', 'triggerresize', 'thumbcolumnsresponsive');
			foreach ( $boolOptions as $key )
			{
				if (isset($data->{$key}) )
					$ret .= ' data-' . $key . '="' . ((strtolower($data->{$key}) === 'true') ? 'true': 'false') .'"';
			}
			
			$boolOptions = array('showimgtitle', 'titlesmallscreen', 'initsocial', 'showsocial', 'showemail', 'showfacebook', 'showtwitter', 'showpinterest', 'socialrotateeffect', 'doshortcodeontext');
			foreach ( $boolOptions as $key )
				$ret .= ' data-' . $key . '="' . ((isset($data->{$key}) && (strtolower($data->{$key}) === 'true')) ? 'true': 'false') .'"';
			
			$valOptions = array('loop', 'src', 'duration', 'slideduration', 'slideshowinterval', 'googleanalyticsaccount', 'resizemode', 'imagetoolboxmode', 'effect', 'padding', 'bgcolor', 'bgimage', 'thumbwidth', 'thumbheight', 'thumbgap', 'thumbrowgap', 'lightboxtextheight', 'lightboxtitlecss', 'lightboxdescriptioncss', 'titlecss', 'descriptioncss', 'titleheight', 'titlesmallscreenwidth', 'titleheightsmallscreen',
					'socialmode', 'socialposition', 'socialpositionlightbox', 'socialdirection', 'socialbuttonsize', 'socialbuttonfontsize', 'defaultvideovolume',
					'thumbtitleheight', 'thumbmediumtitleheight', 'thumbsmalltitleheight',
					'triggerresizedelay', 'thumbmediumsize', 'thumbsmallsize', 'thumbmediumwidth', 'thumbmediumheight', 'thumbsmallwidth', 'thumbsmallheight', 'imgtitle');
			foreach ( $valOptions as $key )
			{
				if (isset($data->{$key}) )
					$ret .= ' data-' . $key . '="' . $data->{$key} . '"';
			}
				
			$ret .= ' data-jsfolder="' . WONDERPLUGIN_GALLERY_URL . 'engine/"'; 
			$ret .= ' style="display:none;" >';
			
			$hasVideo = false;
			
			$currentlang = $this->multilingual ? (!empty($data->lang) ? $data->lang : $this->currentlang) : null;

			// dynamic contents
			if ( !empty($contents['mediaids']) )
			{
				$mediaIds = array_map('trim', explode(",", $contents['mediaids']));

				if (!isset($data->slides))
				{
					$data->slides = array();
				}

				foreach($mediaIds as $id)
				{
					$mediaId = $id;
					
					if ($this->multilingual && $currentlang != $this->defaultlang)
					{
						$mediaId = apply_filters( 'wpml_object_id', $id, 'attachment', TRUE, $currentlang );
					}

					$data->slides[] = (object) array(
						'type' => 12,
						'mediaid' => $mediaId
					);
				}
			}

			if (isset($data->slides) && count($data->slides) > 0)
			{
				// process posts
				$items = array();
				foreach ($data->slides as $slide)
				{
					if ($slide->type == 6)
					{
						$items = array_merge($items, $this->get_post_items($slide));
					}
					else if ($slide->type == 11)
					{
						if (!empty($data->importfolder))
						{
							$slide->folder = $data->importfolder;
						}
						$items = array_merge($items, $this->get_items_from_folder($slide));
					}
					else if ($slide->type == 12)
					{
						$items = array_merge($items, $this->get_media_item($slide));
					}
					else
					{
						$items[] = $slide;
					}
				}
				
				foreach ($items as $slide)
				{			
					if ( isset($slide->schedule) && strtolower($slide->schedule) === 'true')
					{
						$starttime = strtotime( $slide->starttime );
						$endtime = strtotime( $slide->endtime );
						$currenttime = current_time( 'timestamp' );

						if ( $currenttime < $starttime || $currenttime > $endtime)
							continue;
					}
					
					foreach($slide as &$value)
					{
						if ( is_string($value) )
							$value = wp_kses_post($value);
					}
					
					if ($slide->type == 10)
					{
						$ret .= '<a href="#" data-mediatype=13 data-youtubeapikey="' . $slide->youtubeapikey . '" data-youtubeplaylistid="' . $slide->youtubeplaylistid . '"';
						if (isset($slide->youtubeplaylistmaxresults) && $slide->youtubeplaylistmaxresults)
							$ret .= ' data-youtubeplaylistmaxresults="' . $slide->youtubeplaylistmaxresults . '"';
						$ret .= '><img class="html5galleryimg html5gallery-tn-image"></a>';
					}
					else
					{
						if ($this->multilingual && $currentlang != $this->defaultlang)
						{
							$slide->title = $this->get_multilingual_slide_text($slide, 'title', $currentlang);
							$slide->description = $this->get_multilingual_slide_text($slide, 'description', $currentlang);
							$slide->alt = $this->get_multilingual_slide_text($slide, 'alt', $currentlang);
						}

						if ( isset($data->doshortcodeontext) && (strtolower($data->doshortcodeontext) === 'true') )
						{
							if ($slide->title && strlen($slide->title) > 0)
								$slide->title = do_shortcode($slide->title);
						
							if ($slide->description && strlen($slide->description) > 0)
								$slide->description = do_shortcode($slide->description);
							
							if ($slide->alt && strlen($slide->alt) > 0)
								$slide->alt = do_shortcode($slide->alt);
						}
						
						$ret .= '<a';
						if ($slide->type == 0)
						{
							$ret .=' href="' . $slide->image . '" data-mediatype=1';
						}
						else if ($slide->type == 1)
						{
							$hasVideo = true;
							
							$ret .=' data-mediatype=6 href="' . $slide->mp4 . '"';
							if (isset($slide->image) && $slide->image)
								$ret .=' data-poster="' . $slide->image . '"';
							if (isset($slide->hdmp4) && $slide->hdmp4)
								$ret .=' data-hd="' . $slide->hdmp4 . '"';
							if (isset($slide->webm) && $slide->webm)
								$ret .=' data-webm="' . $slide->webm . '"';
							if (isset($slide->hdwebm) && $slide->hdwebm)
								$ret .=' data-hdwebm="' . $slide->hdwebm . '"';
							if ( !empty($slide->vtt) )
								$ret .=' data-vtt="' . $slide->vtt . '"';
						}
						else if ($slide->type == 2 || $slide->type == 3 || $slide->type == 4 || $slide->type == 5)
						{
							$hasVideo = true;
							
							$ret .=' href="' . $slide->video . '"';
							if ($slide->type == 5)
								$ret .= " data-mediatype=11";
							if (isset($slide->image) && $slide->image)
								$ret .=' data-poster="' . $slide->image . '"';
						}
						
						if (isset($slide->weblink) && strlen($slide->weblink) > 0)
						{
							$ret .= ' data-link="' . $slide->weblink . '"';
							if (isset($slide->linktarget) && strlen($slide->linktarget) > 0)
								$ret .= ' data-linktarget="' . $slide->linktarget . '"';
						}
												
						$ret .= '><img class="html5galleryimg html5gallery-tn-image" src="' . ((isset($data->showcarousel) && strtolower($data->showcarousel) === 'true') ? $slide->thumbnail : '') . '"';
						
						if ( isset($slide->altusetitle) && (strtolower($slide->altusetitle) === 'false') && isset($slide->alt) )
							$ret .= ' alt="' . $this->eacape_html_quotes(strip_tags($slide->alt)) . '" data-title="' . $this->eacape_html_quotes($slide->title) . '"';
						else
							$ret .= ' alt="' . $this->eacape_html_quotes(strip_tags($slide->title)) . '"';
						
						if ( isset($data->showimgtitle) && (strtolower($data->showimgtitle) === 'true') && isset($data->imgtitle) )
						{
							if ($data->imgtitle == 'title' && isset($slide->title))
								$ret .= ' title="' . $this->eacape_html_quotes($slide->title) . '"';
							else if ($data->imgtitle == 'description' && isset($slide->description))
								$ret .= ' title="' . $this->eacape_html_quotes($slide->description) . '"';
							else if ($data->imgtitle == 'alt' && isset($slide->alt))
								$ret .= ' title="' . $this->eacape_html_quotes($slide->alt) . '"';
						}
						
						if (isset($slide->description) && strlen($slide->description) > 0)
							$ret .= ' data-description="' . $this->eacape_html_quotes($slide->description) . '"';
						$ret .= '></a>';
					}
				}
			}
			if ('F' == 'F')
				$ret .= '<div class="wonderplugin-engine"><a href="http://www.wonderplugin.com/wordpress-gallery/" title="'. get_option('wonderplugin-gallery-engine')  .'">' . get_option('wonderplugin-gallery-engine') . '</a></div>';
			$ret .= '</div>';
			
			$ret .= '</div>';
			
			if (isset($data->addinitscript) && strtolower($data->addinitscript) === 'true')
			{
				$ret .= '<script>jQuery(document).ready(function(){jQuery(".wonderplugin-engine").css({display:"none"});jQuery(".wonderplugingallery").wonderplugingallery({forceinit:true});});</script>';				
			}
			
			if (isset($data->triggerresize) && strtolower($data->triggerresize) === 'true')
			{
				$ret .= '<script>jQuery(document).ready(function(){';
				if ($data->triggerresizedelay > 0)
					$ret .= 'setTimeout(function(){jQuery(window).trigger("resize");},' . $data->triggerresizedelay . ');';
				else
					$ret .= 'jQuery(window).trigger("resize");';
				$ret .= '});</script>';
			}
			
			if (isset($data->slides) && (count($data->slides) > 0) && $hasVideo && isset($data->schemamarkup) && (strtolower($data->schemamarkup) === 'true'))
			{
				$ret .= $this->generate_schema_code($id, $data->slides, $itemTime);
			}
			
			if (isset($data->customjs) && strlen($data->customjs) > 0)
			{
				$customjs = str_replace("\r", " ", $data->customjs);
				$customjs = str_replace("\n", " ", $customjs);
				$customjs = str_replace('&lt;',  '<', $customjs);
				$customjs = str_replace('&gt;',  '>', $customjs);
				$customjs = str_replace("GALLERYID", $id, $customjs);
				$ret .= '<script language="JavaScript">' . $customjs . '</script>';
			}
		}
		else
		{
			$ret = '<p>The specified gallery id does not exist.</p>';
		}
		return $ret;
	}
	
	function get_items_from_folder($slide) {
		
		$dir = ABSPATH . $slide->folder;
		
		$dirurl = get_site_url(). '/' . str_replace(DIRECTORY_SEPARATOR, '/', $slide->folder) . '/';
		
		$items = array();
		
		if (!is_readable($dir) || !file_exists($dir))
		{
			$item = array(
					'type'			=> 0,
					'image'			=> '',
					'thumbnail'		=> '',
					'title'			=> 'No permissions to browse the folder or the folder does not exist',
					'description'	=> '',
					'weblink'		=> '',
					'linktarget'	=> ''
			);
			
			$items[] = (object) $item;
			
			return $items;
		}
		
		if (isset($slide->onlyusexml) && strtolower($slide->onlyusexml) === 'true')
		{
			
			$xmlfile = $dir . DIRECTORY_SEPARATOR . 'list.xml';
			if (file_exists($xmlfile) && function_exists("simplexml_load_string"))
			{
				$content = file_get_contents($xmlfile);
			
				$xml = simplexml_load_string($content);
			
				if ($xml && isset($xml->item))
				{
					foreach($xml->item as $xmlitem)
					{
						$new = array(
								'type'			=> 0,
								'image'			=> '',
								'thumbnail'		=> '',
								'title'			=> '',
								'description'	=> '',
								'weblink'		=> '',
								'linktarget'	=> ''
						);
						
						foreach ($xmlitem as $key => $value)
						{
							$new[$key] = $value;
						}
						
						$props = array('image', 'thumbnail', 'video');
						foreach($props as $prop)
						{
							if (!empty($new[$prop]) && (strpos(strtolower($new[$prop]), 'http://') !== 0) && (strpos(strtolower($new[$prop]), 'https://') !== 0) && (strpos(strtolower($new[$prop]), '/') !== 0))
							{
								$new[$prop] = $dirurl . $new[$prop];
							}
						}
						
						if (empty($new['thumbnail']))
							$new['thumbnail'] = $new['image'];
						
						$items[] = (object) $new;
					}
				}
			}
			
			return $items;
		}
		
		$exts = explode('|', $slide->imageext);
				
		if ($slide->sortorder == 'ASC')
			$cdir = scandir($dir);
		else
			$cdir = scandir($dir, 1);

		$usefilenameastitle = isset($slide->usefilenameastitle) && strtolower($slide->usefilenameastitle) === 'true';
		
		foreach ($cdir as $key => $value)
		{
			if (!is_dir($dir . DIRECTORY_SEPARATOR . $value))
			{
				if (preg_match('/(?<!' . $slide->thumbname . '|' . $slide->postername . ')\.(' . $slide->imageext . ')$/i', $value))
				{
					$info = pathinfo($value);
					$thumb = $info['filename'] . $slide->thumbname . '.' . $info['extension'];
					
					$imageurl = $dirurl . $value;
					$thumburl = (in_array($thumb, $cdir)) ? $dirurl . $thumb : $imageurl;
										
					$item = array(
							'type'			=> 0,
							'image'			=> $imageurl,
							'thumbnail'		=> $thumburl,
							'title'			=> $usefilenameastitle ? $info['filename'] : '',
							'description'	=> '',
							'weblink'		=> '',
							'linktarget'	=> ''
					);
				
					$items[] = (object) $item;
				}
				else if (preg_match('/\.(' . $slide->videoext . ')$/i', $value))
				{
					$info = pathinfo($value);
					
					$videourl = $dirurl . $value;
					
					$thumburl = '';
					foreach($exts as $ext)
					{
						$thumb = $info['filename'] . $slide->thumbname . '.' . $ext;
												
						if (in_array($thumb, $cdir))
						{
							$thumburl = $dirurl . $thumb;
							break;
						}
					}
					
					$posterurl = '';
					foreach($exts as $ext)
					{
						$poster = $info['filename'] . $slide->postername . '.' . $ext;
						if (in_array($poster, $cdir))
						{
							$posterurl = $dirurl . $poster;
							break;
						}
					}
										
					$item = array(
							'type'			=> 1,
							'mp4'			=> $videourl,
							'image'			=> $posterurl,
							'thumbnail'		=> $thumburl,
							'title'			=> $usefilenameastitle ? $info['filename'] : '',
							'description'	=> '',
							'weblink'		=> '',
							'linktarget'	=> ''
					);
										
					$items[] = (object) $item;
				}
			}
		}
		
		// read config.xml file
		$xmlfile = $dir . DIRECTORY_SEPARATOR . 'list.xml';
		if (file_exists($xmlfile) && function_exists("simplexml_load_string"))
		{
			$content = file_get_contents($xmlfile);
				
			$xml = simplexml_load_string($content);
		
			if ($xml && isset($xml->item))
			{
				foreach($xml->item as $xmlitem)
				{
					if (isset($xmlitem->image) && (strpos(strtolower($xmlitem->image), 'http://') !== 0) && (strpos(strtolower($xmlitem->image), 'https://') !== 0) && (strpos(strtolower($xmlitem->image), '/') !== 0))
					{
						$xmlitem->image = $dirurl . $xmlitem->image;
							
						foreach($items as &$item)
						{
							if (isset($item->image) && (strtolower($item->image) == strtolower($xmlitem->image)))
							{
								unset($xmlitem->image);
		
								foreach ($xmlitem as $key => $value)
								{									
									if (($key == 'thumbnail' || $key == 'video') && !empty($value) && (strpos(strtolower($value), 'http://') !== 0) && (strpos(strtolower($value), 'https://') !== 0) && (strpos(strtolower($value), '/') !== 0))
									{
										$value = $dirurl . $value;
									}
									$item->{$key} = $value;
								}
		
								break;
							}
						}
					}
					else
					{
						
						$new = array(
								'type'			=> 0,
								'image'			=> '',
								'thumbnail'		=> '',
								'title'			=> '',
								'description'	=> '',
								'weblink'		=> '',
								'linktarget'	=> ''
						);
		
						foreach ($xmlitem as $key => $value)
						{
							$new[$key] = $value;
						}
												
						if (empty($new['thumbnail']))
							$new['thumbnail'] = $new['image'];
						
						$items[] = (object) $new;
					}
				}
			}
		}
				
		return $items;
	}
	
	function get_media_item($slide) {

		$items = array();

		$mediaData = get_post($slide->mediaid);
		if ( empty($mediaData) )
		{
			return $items;
		}

		$mediaType = 0;
		if ( strtolower(substr($mediaData->post_mime_type, 0, 6)) == "video/" )
		{
			$mediaType = 1;
		}

		$mediumAlt = get_post_meta($slide->mediaid, '_wp_attachment_image_alt', true);
		$altusetitle = empty($mediumAlt) ? 'true' : 'false';

		$settings = $this->get_settings();
		$imagesize = $settings['imagesize'];
		
		if ($mediaType == 1)
		{
			$poster = '';
			$thumbnail = '';

			$featuredImageId = get_post_thumbnail_id($slide->mediaid);
			if ( !empty($featuredImageId) )
			{
				$postImages = wp_get_attachment_image_src($featuredImageId, 'full');
				$poster = empty($postImages) ? '' : $postImages[0]; 

				$thumbimages = wp_get_attachment_image_src($featuredImageId, $imagesize);
				$thumbnail = empty($thumbimages) ? '' : $thumbimages[0]; 
			}

			$new = array(
				'type'			=> 1,
				'mp4'			=> wp_get_attachment_url($slide->mediaid),
				'image'			=> $poster,
				'thumbnail'		=> $thumbnail
			);
		}
		else
		{
			$thumbimages = wp_get_attachment_image_src($slide->mediaid, $imagesize);
			$thumbnail = empty($thumbimages) ? '' : $thumbimages[0]; 

			$new = array(
				'type'			=> 0,
				'image'			=> wp_get_attachment_url($slide->mediaid),
				'thumbnail'		=> $thumbnail
			);
		}

		$new = array_merge($new, 
			array(
				'title'			=> $mediaData->post_title,
				'description'	=> $mediaData->post_content,
				'altusetitle'	=> $altusetitle,
				'alt'			=> $mediumAlt,
				'weblink'		=> '',
				'linktarget'	=> ''
			)
		);

		$items[] = (object) $new;

		return $items;
	}

	function get_post_items($options) {
	
		$posts = array();
	
		if ($options->postcategory == -1)
		{
			$posts = wp_get_recent_posts(array(
					'numberposts' 	=> $options->postnumber,
					'post_status' 	=> 'publish'
			));
		}
		else
		{
			$posts = get_posts(array(
					'numberposts' 	=> $options->postnumber,
					'post_status' 	=> 'publish',
					'category'		=> $options->postcategory
			));
		}
	
		$items = array();
	
		foreach($posts as $post)
		{
			if (is_object($post))
				$post = get_object_vars($post);
	
			$thumbnail = '';
			$image = '';
			if ( has_post_thumbnail($post['ID']) )
			{
				$featured_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post['ID']), $options->featuredimagesize);
				$thumbnail = $featured_thumb[0];
	
				$featured_image = wp_get_attachment_image_src(get_post_thumbnail_id($post['ID']), 'full');
				$image = $featured_image[0];
			}
	
			$excerpt = $post['post_excerpt'];
			if (empty($excerpt))
			{
				$excerpts = explode( '<!--more-->', $post['post_content'] );
				$excerpt = $excerpts[0];
				$excerpt = strip_tags( str_replace(']]>', ']]&gt;', strip_shortcodes($excerpt)) );
			}
			$excerpt = wonderplugin_gallery_wp_trim_words($excerpt, $options->excerptlength);
	
			$post_item = array(
					'type'			=> 0,
					'image'			=> $image,
					'thumbnail'		=> $thumbnail,
					'title'			=> $post['post_title'],
					'description'	=> $excerpt,
					'weblink'		=> get_permalink($post['ID']),
					'linktarget'	=> $options->postlinktarget
			);
			
			if (isset($options->posttitlelink) && strtolower($options->posttitlelink) === 'true')
			{
				$post_item['title'] = '<a class="html5gallery-posttitle-link" href="' . $post_item['weblink'] . '"';
				if (isset($post_item['linktarget']) && strlen($post_item['linktarget']) > 0)
					$post_item['title'] .= ' target="' . $post_item['linktarget'] . '"';
				$post_item['title'] .= '>' . $post['post_title'] . '</a>';
			}
				
			$items[] = (object) $post_item;
		}
	
		return $items;
	}
	
	function delete_item($id) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
		
		$ret = $wpdb->query( $wpdb->prepare(
				"
				DELETE FROM $table_name WHERE id=%s
				",
				$id
		) );
		
		return $ret;
	}
	
	function trash_item($id) {
	
		return $this->set_item_status($id, 0);
	}
	
	function restore_item($id) {
	
		return $this->set_item_status($id, 1);
	}
	
	function set_item_status($id, $status) {
	
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
	
		$ret = false;
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$data = json_decode($item_row->data, true);
			$data['publish_status'] = $status;
			$data = json_encode($data);
	
			$update_ret = $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET data=%s WHERE id=%d", $data, $id ) );
			if ( $update_ret )
				$ret = true;
		}
	
		return $ret;
	}
	
	function clone_item($id) {
	
		global $wpdb, $user_ID;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
		
		$cloned_id = -1;
		
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$time = current_time('mysql');
			$authorid = $user_ID;
			
			$ret = $wpdb->query( $wpdb->prepare(
					"
					INSERT INTO $table_name (name, data, time, authorid)
					VALUES (%s, %s, %s, %s)
					",
					$item_row->name . " Copy",
					$item_row->data,
					$time,
					$authorid
			) );
				
			if ($ret)
				$cloned_id = $wpdb->insert_id;
		}
	
		return $cloned_id;
	}
	
	function is_db_table_exists() {
	
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
	
		return ( strtolower($wpdb->get_var("SHOW TABLES LIKE '$table_name'")) == strtolower($table_name) );
	}
	
	function is_id_exist($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
		
		$slider_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		return ($slider_row != null);
	}
	
	function create_db_table() {
	
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
		
		$charset = '';
		if ( !empty($wpdb -> charset) )
			$charset = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( !empty($wpdb -> collate) )
			$charset .= " COLLATE $wpdb->collate";
	
		$sql = "CREATE TABLE $table_name (
		id INT(11) NOT NULL AUTO_INCREMENT,
		name tinytext DEFAULT '' NOT NULL,
		data MEDIUMTEXT DEFAULT '' NOT NULL,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		authorid tinytext NOT NULL,
		PRIMARY KEY  (id)
		) $charset;";
			
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	
	function save_item($item) {
		
		global $wpdb, $user_ID;
		
		if ( !$this->is_db_table_exists() )
		{
			$this->create_db_table();
		
			$create_error = "CREATE DB TABLE - ". $wpdb->last_error;
			if ( !$this->is_db_table_exists() )
			{
				return array(
						"success" => false,
						"id" => -1,
						"message" => $create_error
				);
			}
		}
		
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
		
		$id = $item["id"];
		$name = $item["name"];
		
		unset($item["id"]);
		$data = json_encode($item);
		
		if ( empty($data) )
		{
			$json_error = "json_encode error";
			if ( function_exists('json_last_error_msg') )
				$json_error .= ' - ' . json_last_error_msg();
			else if ( function_exists('json_last_error') )
				$json_error .= 'code - ' . json_last_error();
		
			return array(
					"success" => false,
					"id" => -1,
					"message" => $json_error
			);
		}
		
		$time = current_time('mysql');
		$authorid = $user_ID;
		
		if ( ($id > 0) && $this->is_id_exist($id) )
		{
			$ret = $wpdb->query( $wpdb->prepare(
					"
					UPDATE $table_name
					SET name=%s, data=%s, time=%s, authorid=%s
					WHERE id=%d
					",
					$name,
					$data,
					$time,
					$authorid,
					$id
			) );
			
			if (!$ret)
			{
				return array(
						"success" => false,
						"id" => $id, 
						"message" => "UPDATE - ". $wpdb->last_error
					);
			}
		}
		else
		{
			$ret = $wpdb->query( $wpdb->prepare(
					"
					INSERT INTO $table_name (name, data, time, authorid)
					VALUES (%s, %s, %s, %s)
					",
					$name,
					$data,
					$time,
					$authorid
			) );
			
			if (!$ret)
			{
				return array(
						"success" => false,
						"id" => -1,
						"message" => "INSERT - " . $wpdb->last_error
				);
			}
			
			$id = $wpdb->insert_id;
		}
		
		return array(
				"success" => true,
				"id" => intval($id),
				"message" => "Gallery published!"
		);
	}
	
	function get_list_data() {
		
		if ( !$this->is_db_table_exists() )
			$this->create_db_table();
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
		
		$rows = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A);
		
		$ret = array();
		
		if ( $rows )
		{
			foreach ( $rows as $row )
			{
				$ret[] = array(
							"id" => $row['id'],
							'name' => $row['name'],
							'data' => $row['data'],
							'time' => $row['time'],
							'authorid' => $row['authorid']
						);
			}
		}
	
		return $ret;
	}
	
	function get_item_data($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
	
		$ret = "";
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$ret = $item_row->data;
		}

		return $ret;
	}
	
	function get_settings() {
		
		$userrole = get_option( 'wonderplugin_gallery_userrole' );
		if ( $userrole == false )
		{
			update_option( 'wonderplugin_gallery_userrole', 'manage_options' );
			$userrole = 'manage_options';
		}
		
		$imagesize = get_option( 'wonderplugin_gallery_imagesize' );
		if ( $imagesize == false )
		{
			update_option( 'wonderplugin_gallery_imagesize', 'full' );
			$imagesize = 'full';
		}

		$thumbnailsize = get_option( 'wonderplugin_gallery_thumbnailsize' );
		if ( $thumbnailsize == false )
		{
			update_option( 'wonderplugin_gallery_thumbnailsize', 'thumbnail' );
			$thumbnailsize = 'thumbnail';
		}

		$keepdata = get_option( 'wonderplugin_gallery_keepdata', 1 );
		$disableupdate = get_option( 'wonderplugin_gallery_disableupdate', 0 );
		
		$supportwidget = get_option( 'wonderplugin_gallery_supportwidget', 1 );
		$addjstofooter = get_option( 'wonderplugin_gallery_addjstofooter', 0 );
		
		$jsonstripcslash = get_option( 'wonderplugin_gallery_jsonstripcslash', 1 );
		$jetpackdisablelazyload = get_option( 'wonderplugin_gallery_jetpackdisablelazyload', 1 );

		$supportmultilingual = get_option( 'wonderplugin_gallery_supportmultilingual', 1 );

		$settings = array(
			"userrole" => $userrole,
			"imagesize" => $imagesize,
			"thumbnailsize" => $thumbnailsize,
			"keepdata" => $keepdata,
			"disableupdate" => $disableupdate,
			"supportwidget" => $supportwidget,
			"addjstofooter" => $addjstofooter,
			"jsonstripcslash" => $jsonstripcslash,
			"jetpackdisablelazyload" => $jetpackdisablelazyload,
			"supportmultilingual" => $supportmultilingual
		);
		
		return $settings;
	}
	
	function save_settings($options) {
		
		if (!isset($options) || !isset($options['userrole']))
			$userrole = 'manage_options';
		else if ( $options['userrole'] == "Editor") 
			$userrole = 'moderate_comments';
		else if ( $options['userrole'] == "Author")
			$userrole = 'upload_files';
		else
			$userrole = 'manage_options';
		update_option( 'wonderplugin_gallery_userrole', $userrole );
		
		if (isset($options) && isset($options['imagesize']))
			$imagesize = $options['imagesize'];
		else
			$imagesize = 'full';
		update_option( 'wonderplugin_gallery_imagesize', $imagesize );

		if (isset($options) && isset($options['thumbnailsize']))
			$thumbnailsize = $options['thumbnailsize'];
		else
			$thumbnailsize = 'thumbnail';
		update_option( 'wonderplugin_gallery_thumbnailsize', $thumbnailsize );

		if (!isset($options) || !isset($options['keepdata']))
			$keepdata = 0;
		else
			$keepdata = 1;
		update_option( 'wonderplugin_gallery_keepdata', $keepdata );
		
		if (!isset($options) || !isset($options['disableupdate']))
			$disableupdate = 0;
		else
			$disableupdate = 1;
		update_option( 'wonderplugin_gallery_disableupdate', $disableupdate );
		
		if (!isset($options) || !isset($options['supportwidget']))
			$supportwidget = 0;
		else
			$supportwidget = 1;
		update_option( 'wonderplugin_gallery_supportwidget', $supportwidget );
		
		if (!isset($options) || !isset($options['addjstofooter']))
			$addjstofooter = 0;
		else
			$addjstofooter = 1;
		update_option( 'wonderplugin_gallery_addjstofooter', $addjstofooter );
		
		if (!isset($options) || !isset($options['jsonstripcslash']))
			$jsonstripcslash = 0;
		else
			$jsonstripcslash = 1;
		update_option( 'wonderplugin_gallery_jsonstripcslash', $jsonstripcslash );

		if (!isset($options) || !isset($options['jetpackdisablelazyload']))
			$jetpackdisablelazyload = 0;
		else
			$jetpackdisablelazyload = 1;
		update_option( 'wonderplugin_gallery_jetpackdisablelazyload', $jetpackdisablelazyload );

		if (!isset($options) || !isset($options['supportmultilingual']))
			$supportmultilingual = 0;
		else
			$supportmultilingual = 1;
		update_option( 'wonderplugin_gallery_supportmultilingual', $supportmultilingual );
	}
		
	function get_plugin_info() {
		
		$info = get_option('wonderplugin_gallery_information');
		if ($info === false)
			return false;
		
		return unserialize($info);
	}
	
	function save_plugin_info($info) {
		
		update_option( 'wonderplugin_gallery_information', serialize($info) );
	}
	
	function check_license($options) {
		
		$ret = array(
			"status" => "empty"
		);
				
		if ( !isset($options) || empty($options['wonderplugin-gallery-key']) )
		{
			return $ret;
		}
		
		$key = sanitize_text_field( $options['wonderplugin-gallery-key'] );
		if ( empty($key) )
			return $ret;
		
		$update_data = $this->controller->get_update_data('register', $key);
		if( $update_data === false )
		{
			$ret['status'] = 'timeout';
			return $ret;
		}
		
		if ( isset($update_data->key_status) )						
			$ret['status'] = $update_data->key_status;
		
		return $ret;
	}
	
	function deregister_license($options) {
		
		$ret = array(
			"status" => "empty"
		);
		
		if ( !isset($options) || empty($options['wonderplugin-gallery-key']) )
			return $ret;
		
		$key = sanitize_text_field( $options['wonderplugin-gallery-key'] );
		if ( empty($key) )
			return $ret;
		
		$info = $this->get_plugin_info();
		$info->key = '';
		$info->key_status = 'empty';
		$info->key_expire = 0;
		$this->save_plugin_info($info);
		
		$update_data = $this->controller->get_update_data('deregister', $key);
		if ($update_data === false)
		{
			$ret['status'] = 'timeout';
			return $ret;
		}
		
		$ret['status'] = 'success';	
		
		return $ret;
	}

}
