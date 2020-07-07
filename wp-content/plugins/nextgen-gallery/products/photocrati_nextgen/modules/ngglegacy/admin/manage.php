<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

class nggManageGallery {

	var $mode = 'main';
	var $gid = false;
	var $gallery = NULL;
	var $pid = false;
	var $base_page = 'admin.php?page=nggallery-manage-gallery';
	var $search_result = false;

	// initiate the manage page
	function __construct()
	{
		// GET variables
		if( isset($_GET['gid']) ) {
			$this->gid  = (int) $_GET['gid'];
			$this->gallery = C_Gallery_Mapper::get_instance()->find($this->gid, TRUE);
		}
		if( isset($_GET['pid']) )
			$this->pid  = (int) $_GET['pid'];
		if( isset($_GET['mode']) )
			$this->mode = trim ($_GET['mode']);
        // Check for pagination request, avoid post process of other submit button, exclude search results
        if ( isset($_POST['post_paged']) && !isset($_GET['s'] ) ) {
            if ( $_GET['paged'] != $_POST['post_paged'] ) {
                $_GET['paged'] = absint( $_POST['post_paged'] );
                return;
            }
        }
        // Should be only called via manage galleries overview
		if ( isset($_POST['nggpage']) && $_POST['nggpage'] == 'manage-galleries' )
			$this->post_processor_galleries();
		// Should be only called via a edit single gallery page
		if ( isset($_POST['nggpage']) && $_POST['nggpage'] == 'manage-images' )
			$this->post_processor_images();

		//Look for other POST process
		if ( !empty($_POST) || !empty($_GET) )
			$this->processor();

        M_NextGen_Admin::emit_do_notices_action();
	}

	function controller() {

		switch($this->mode) {
			case 'sort':
				include_once (dirname (__FILE__) . '/manage-sort.php');
				nggallery_sortorder($this->gid);
			break;
			case 'edit':
				$this->setup_gallery_fields();
				$this->setup_image_rows();
				include_once (dirname (__FILE__) . '/manage-images.php');
				nggallery_picturelist($this);
			break;
			case 'main':
			default:
				include_once (dirname (__FILE__) . '/manage-galleries.php');
				nggallery_manage_gallery_main();
			break;
		}
	}

	function processor() {

		global $wpdb, $ngg, $nggdb;

		// Delete a picture
		if ($this->mode == 'delpic') {

			//TODO:Remove also Tag reference
			check_admin_referer('ngg_delpicture');
			$image = $nggdb->find_image( $this->pid );
			if ($image) {
				do_action('ngg_delete_picture', $this->pid, $image);
				if ($ngg->options['deleteImg']) {
                    $storage = $storage  = C_Gallery_Storage::get_instance();
                    $storage->delete_image($this->pid);
				}
				$mapper = C_Image_Mapper::get_instance();
				$result = $mapper->destroy($this->pid);

                if ($result)
                    nggGallery::show_message( __('Picture','nggallery').' \''.$this->pid.'\' '.__('deleted successfully','nggallery') );
            }

		 	$this->mode = 'edit'; // show pictures

		}

		// Recover picture from backup
		if ($this->mode == 'recoverpic') {

			check_admin_referer('ngg_recoverpicture');

            // bring back the old image
			nggAdmin::recover_image($this->pid);

            nggGallery::show_message(__('Operation successful. Please clear your browser cache.',"nggallery"));

		 	$this->mode = 'edit'; // show pictures

		}

		// will be called after a ajax operation
		if (isset ($_POST['ajax_callback']))  {
				if ($_POST['ajax_callback'] == 1)
					nggGallery::show_message(__('Operation successful. Please clear your browser cache.',"nggallery"));
		}

		// show sort order
		if ( isset ($_POST['sortGallery']) )
			$this->mode = 'sort';

		if ( isset ($_GET['s']) )
			$this->search_images();

	}

	function setup_image_rows()
	{
		add_filter('ngg_manage_images_row', array(&$this, 'render_image_row'), 10, 2);
		add_filter('ngg_manage_images_column_1_header', array(&$this, 'render_image_column_1_header'));
		add_filter('ngg_manage_images_column_1_content', array(&$this, 'render_image_column_1'), 10, 2);

		add_filter('ngg_manage_images_column_2_header', array(&$this, 'render_image_column_2_header'));
		add_filter('ngg_manage_images_column_2_content', array(&$this, 'render_image_column_2'), 10, 2);

		add_filter('ngg_manage_images_column_3_header', array(&$this, 'render_image_column_3_header'));
		add_filter('ngg_manage_images_column_3_content', array(&$this, 'render_image_column_3'), 10, 2);

		add_filter('ngg_manage_images_column_4_header', array(&$this, 'render_image_column_4_header'));
		add_filter('ngg_manage_images_column_4_content', array(&$this, 'render_image_column_4'), 10, 2);

		add_filter('ngg_manage_images_column_5_header', array(&$this, 'render_image_column_5_header'));
		add_filter('ngg_manage_images_column_5_content', array(&$this, 'render_image_column_5'), 10, 2);

		add_filter('ngg_manage_images_column_6_header', array(&$this, 'render_image_column_6_header'));
		add_filter('ngg_manage_images_column_6_content', array(&$this, 'render_image_column_6'), 10, 2);
	}

	function render_image_column_1_header()
	{
		return '<input type="checkbox" id="cb-select-all-1" onclick="checkAll(document.getElementById(\'updategallery\'));">';
	}

	function render_image_column_2_header()
	{
		return __('ID', 'nggallery');
	}

	function render_image_column_3_header()
	{
		return __('Thumbnail', 'nggallery');
	}

	function render_image_column_4_header()
	{
		return __('Filename', 'nggallery');
	}

	function render_image_column_5_header()
	{
		return __('Alt & Title Text / Description', 'nggallery');
	}

	function render_image_column_6_header()
	{
		return __('Tags', 'nggallery');
	}

	function render_image_column_1($output='', $picture=array())
	{
		return "<input type='checkbox' name='doaction[]' value='{$picture->pid}'/>";
	}

	function render_image_column_2($output='', $picture=array())
	{
		return $picture->pid;
	}

	function render_image_column_3($output='', $picture=array())
	{
		$image_url = add_query_arg('i', mt_rand(), $picture->imageURL);
		$thumb_url = add_query_arg('i', mt_rand(), $picture->thumbURL);
		$filename  = esc_attr($picture->filename);

		$output = array();

		$output[] = "<a href='{$image_url}' class='thickbox' title='{$filename}'>";
		$output[] = "<img class='thumb' src='{$thumb_url}' id='thumb{$picture->pid}'/>";
		$output[] = "</a>";

		return ($output = implode("\n", $output));
	}

	function render_image_column_4($output='', $picture=array())
	{
		$image_url		= nextgen_esc_url($picture->imageURL);
		$filename		= esc_attr($picture->filename);
		$caption		= esc_html((empty($picture->alttext) ? $picture->filename: $picture->alttext));
		$date 			= mysql2date(get_option('date_format'), $picture->imagedate);
		$width			= $picture->meta_data['width'];
		$height			= $picture->meta_data['height'];
		$pixels			= "{$width} x {$height} pixels";
		$excluded		= checked($picture->exclude, 1, false);
		$exclude_label	= __("Exclude ?", 'nggallery');

		$output = array();

		$output[] =  "<div><strong><a href='{$image_url}' class='thickbox' title='{$caption}'>{$filename}</a></strong></div>";
		$output[] =  '<div class="meta">'. esc_html($date) . '</div>';
		$output[] =  "<div class='meta'>{$pixels}</div>";
		$output[] =  "<label for='exclude_{$picture->pid}'>";
		$output[] =  "<input type='checkbox' id='exclude_{$picture->pid}' value='1' name='images[{$picture->pid}][exclude]' {$excluded}/> {$exclude_label}";
		$output[] =  "</label>";

		return ($output = implode("\n", $output));
	}

	function render_image_column_5($output='', $picture=array())
	{
		$alttext	= esc_attr(stripslashes($picture->alttext));
		$desc		= esc_html(stripslashes($picture->description));

		$output = array();

		$output[] = "<input title='Alt/Title Text' type='text' name='images[{$picture->pid}][alttext]' value='{$alttext}'/>";
		$output[] = "<textarea title='Description' rows='3' name='images[$picture->pid][description]'>{$desc}</textarea>";

		return ($output = implode("\n", $output));
	}

	function render_image_column_6($output='', $picture=array())
	{
        global $wp_version;
        $fields = version_compare($wp_version, '4.6', '<=') ? 'fields=names' : array('fields' => 'names');
        $tags = wp_get_object_terms($picture->pid, 'ngg_tag', $fields);
		if (is_array($tags)) $tags = implode(', ', $tags);
		$tags = esc_html($tags);

		return "<textarea rows='4' name='images[{$picture->pid}][tags]'>{$tags}</textarea>";
	}

	function render_image_row($picture, $counter)
	{
		// Get number of columns
		$class	= !($counter % 2 == 0) ? '' : 'alternate';
		$columns 	= apply_filters('ngg_manage_images_number_of_columns', 6);

		// Get the valid row actions
		$actions = array();
		$row_actions = apply_filters('ngg_manage_images_row_actions', array(
			'view'			=>	array(&$this, 'render_view_action_link'),
			'meta'			=>	array(&$this, 'render_meta_action_link'),
			'custom_thumb'	=>	array(&$this, 'render_custom_thumb_action_link'),
			'rotate'		=>	array(&$this, 'render_rotate_action_link'),
			'recover'		=>	array(&$this, 'render_recover_action_link'),
			'delete'		=>	array(&$this, 'render_delete_action_link')
		));
		foreach ($row_actions as $id => $callback) {
			if (is_callable($callback)) {
				$result = call_user_func($callback, $id, $picture);
				if ($result) $actions[] = $result;
			}
		}

		// Output row columns
		echo "<tr class='{$class} iedit' valign='top'>";
		for ($i=1; $i <= $columns; $i++) {
			$rowspan = $i > 4 ? "rowspan='2'" : '';
			echo "<td class='column column-{$i}' {$rowspan}>";
			echo apply_filters("ngg_manage_images_column_{$i}_content", '', $picture);
			echo '</td>';
		}
		echo '</tr>';

		// Actions row
		echo "<tr class='{$class} row_actions'>";
		echo '<td colspan="2"></td>';
		echo "<td colspan='".($columns-2)."'>";
		echo "<div class='row-actions'>";
		echo implode(" | ", $actions);
		echo "</div>";
		echo "</td>";
		echo '</tr>';
	}


	function render_view_action_link($id, $picture)
	{
		$image_url	= nextgen_esc_url($picture->imageURL);
		$label		= esc_html__('View', 'nggallery');
		$alt_text	= empty($picture->alttext) ? $picture->filename: $picture->alttext;
		$title		= esc_attr(__('View', 'nggallery'). " \"{$alt_text}\"");

		return "<a href='{$image_url}' class='thickbox' title='{$title}'>{$label}</a>";
	}

	function render_meta_action_link($id, $picture)
	{
		$url		= nextgen_esc_url(NGGALLERY_URLPATH.'admin/showmeta.php?id='.$picture->pid);
		$title		= esc_attr__('Show meta data', 'nggallery');
		$label		= esc_html__('Meta', 'nggallery');

		return "<a href='{$url}' class='ngg-dialog' title='{$title}'>{$label}</a>";
	}

	function render_custom_thumb_action_link($id, $picture)
	{
		$url		= nextgen_esc_url(NGGALLERY_URLPATH.'admin/edit-thumbnail.php?id='.$picture->pid);
		$title		= esc_attr__('Customize thumbnail', 'nggallery');
		$label		= esc_html__("Edit thumb", 'nggallery');

		return "<a href='{$url}' class='ngg-dialog' title='{$title}'>{$label}</a>";
	}

	function render_rotate_action_link($id, $picture)
	{
		$url		= nextgen_esc_url(NGGALLERY_URLPATH.'admin/rotate.php?id='.$picture->pid);
		$title		= esc_attr__('Rotate', 'nggallery');
		$label		= esc_html__('Rotate', 'nggallery');

		return "<a href='{$url}' class='ngg-dialog' title='{$title}'>{$label}</a>";
	}

	function render_recover_action_link($id, $picture)
	{
		if (!file_exists($picture->imagePath . '_backup'))
            return FALSE;

		$url      = wp_nonce_url("admin.php?page=nggallery-manage-gallery&amp;mode=recoverpic&amp;gid={$picture->galleryid}&amp;pid={$picture->pid}", 'ngg_recoverpicture');
		$title    = esc_attr__('Recover image from backup', 'nggallery');
		$label    = esc_html__('Recover', 'nggallery');
        $question = __('Recover', 'nggallery');

		$alttext = empty($picture->alttext) ? $picture->filename : $picture->alttext;
		$alttext = M_NextGen_Data::strip_html(html_entity_decode($alttext), TRUE);
		$alttext = htmlentities($alttext, ENT_QUOTES|ENT_HTML401);

        // Event handler is found in nextgen_admin_page.js
		return "<a href='{$url}'
                   class='confirmrecover'
                   data-question='{$question}'
                   data-text='{$alttext}'
                   title='{$title}'>{$label}</a>";
	}

	function render_delete_action_link($id, $picture)
	{
		$url      = wp_nonce_url("admin.php?page=nggallery-manage-gallery&amp;mode=delpic&amp;gid={$picture->galleryid}&amp;pid={$picture->pid}", 'ngg_delpicture');
		$title    = esc_attr__('Delete image', 'nggallery');
		$label    = esc_html__('Delete', 'nggallery');
		$question = __('Delete', 'nggallery');

		$alttext = empty($picture->alttext) ? $picture->filename : $picture->alttext;
		$alttext = M_NextGen_Data::strip_html(html_entity_decode($alttext), TRUE);
		$alttext = htmlentities($alttext, ENT_QUOTES|ENT_HTML401);

		// Event handler is found in nextgen_admin_page.js
		return "<a href='{$url}'
                   class='submitdelete delete'
                   data-question='{$question}'
                   data-text='{$alttext}'
                   title='{$title}'>{$label}</a>";
	}

	function render_image_row_header()
	{
		$columns 	= apply_filters('ngg_manage_images_number_of_columns', 6);
		echo '<tr>';
		for($i=1; $i<=$columns; $i++) {
			echo "<th class='column column-{$i}'>";
			echo apply_filters('ngg_manage_images_column_'.$i.'_header', "Column #{$i}");
			echo '</th>';
		}
		echo '</tr>';
	}

	function setup_gallery_fields()
	{
		add_filter('ngg_manage_gallery_fields', array(&$this, 'default_gallery_fields'), 10, 2);
	}

	function default_gallery_fields($fields=array(), $gallery=NULL)
	{
		$fields['left'] = array(
			'title'			=>	array(
				'callback'	=>	array(&$this, 'render_gallery_title_field'),
				'label'		=>	__('Title:', 'nggallery'),
                'tooltip'   =>  NULL,
				'id'		=>	'gallery_title'
			),
			'description'	=>	array(
				'callback'	=>	array(&$this, 'render_gallery_desc_field'),
				'label'		=>	__('Description:', 'nggallery'),
                'tooltip'   =>  NULL,
				'id'		=>	'gallery_desc'
			),
			'path'			=>	array(
				'callback'	=>	array(&$this, 'render_gallery_path_field'),
				'label'		=>	__('Gallery path:', 'nggallery'),
                'tooltip'   =>  NULL,
				'id'		=>	'gallery_path'
			),
			'gallery_author'=>	array(
				'callback'	=>	array(&$this, 'render_gallery_author_field'),
				'label'		=>	__('Author', 'nggallery'),
                'tooltip'   =>  NULL,
				'id'		=>	'gallery_author'
			)
		);

		$fields['right'] = array(
			'page_link_to'	=>	array(
				'callback'	=>	array(&$this, 'render_gallery_link_to_page_field'),
				'label'		=>	__('Link to page:', 'nggallery'),
                'tooltip'   =>  __('Albums will link this gallery to the selected page', 'nggallery'),
				'id'		=>	'gallery_page_link_to'
			),
			'preview_image'	=>	array(
				'callback'	=>	array(&$this, 'render_gallery_preview_image_field'),
				'label'		=>	__('Preview image:', 'nggallery'),
                'tooltip'   =>  NULL,
				'id'		=>	'gallery_preview_image',
			),
			'create_page'	=>	array(
				'callback'	=>	array(&$this, 'render_gallery_create_page_field'),
				'label'		=>	__('Create new page:', 'nggallery'),
                'tooltip'   =>  NULL,
				'id'		=>	'gallery_create_new_page'
			)
		);

		return $fields;
	}

	function render_gallery_field_label_column($text, $for, $tooltip = NULL)
	{
		$for = esc_attr($for);

        if (!empty($tooltip))
            $tooltip = "title='{$tooltip}' class='tooltip'";

		echo "<td><label {$tooltip} for='{$for}'>{$text}</label></td>";
	}

	function render_gallery_fields()
	{
		// Get the gallery entity
		$gallery = C_Gallery_Mapper::get_instance()->find($this->gid);

		// Get fields
		$fields = apply_filters('ngg_manage_gallery_fields', array(), $gallery);
		$left	= isset($fields['left']) ? $fields['left'] : array();
		$right	= isset($fields['right'])? $fields['right']: array();

		// Output table
		echo '<table id="gallery_fields">';
		$number_of_fields = max(count($left), count($right));
		$left_keys = array_keys($left);
		$right_keys = array_keys($right);
		for($i=0; $i<$number_of_fields; $i++) {
			// Start row
			echo '<tr>';

			// Left column
			if (isset($left_keys[$i])) {
				extract($left[$left_keys[$i]]);

				// Label
				$this->render_gallery_field_label_column($label, $id, $tooltip);

				// Input field
				if (is_callable($callback)) {
					echo '<td>';
					call_user_func($callback, $gallery);
					echo '</td>';
				}
				elseif (WP_DEBUG) echo "<p>Could not render {$left_keys[$i]} field. No callback exists</p>";
			}
			else $output[] = '<td colspan="2"></td>';

			// Right column
			if (isset($right_keys[$i])) {
				extract($right[$right_keys[$i]]);
				// Label
				$this->render_gallery_field_label_column($label, $id, $tooltip);

				// Input field
				if (is_callable($callback)) {
					echo '<td>';
					call_user_func($callback, $gallery);
					echo '</td>';
				}
				elseif (WP_DEBUG) echo "<p>Could not render {$right_keys[$i]} field. No callback exists</p>";

			}
			else $output[] = '<td colspan="2"></td>';

			// End row
			echo '</tr>';
		}
		echo '</table>';
	}

	function render_gallery_title_field($gallery)
	{
		include('templates/manage_gallery/gallery_title_field.php');
	}

	function render_gallery_desc_field($gallery)
	{
		include('templates/manage_gallery/gallery_desc_field.php');
	}

	function render_gallery_path_field($gallery)
	{
		include('templates/manage_gallery/gallery_path_field.php');
	}

	function render_gallery_author_field($gallery)
	{
		$user = get_userdata($gallery->author);
		$author = isset($user->display_name) ? $user->display_name : $user->user_nicename;
		include('templates/manage_gallery/gallery_author_field.php');
	}

	function render_gallery_link_to_page_field($gallery)
	{
		$pages = get_pages();
		include('templates/manage_gallery/gallery_link_to_page_field.php');
	}

	function render_gallery_preview_image_field($gallery)
	{
		$images = array();
		foreach (C_Image_Mapper::get_instance()->find_all(array("galleryid = %s", $gallery->{$gallery->id_field})) as $image) {
			$images[$image->{$image->id_field}] = "[{$image->{$image->id_field}}] {$image->filename}";
		}
		include('templates/manage_gallery/gallery_preview_image_field.php');
	}

	function render_gallery_create_page_field($gallery)
	{
		$pages = get_pages();
		include('templates/manage_gallery/gallery_create_page_field.php');
	}

	function post_processor_galleries() {
		global $wpdb, $ngg, $nggdb;

		// bulk update in a single gallery
		if (isset ($_POST['bulkaction']) && isset ($_POST['doaction']))  {

			check_admin_referer('ngg_bulkgallery');

			switch ($_POST['bulkaction']) {
				case 'no_action';
				// No action
					break;
				case 'recover_images':
				// Recover images from backup
					// A prefix 'gallery_' will first fetch all ids from the selected galleries
					nggAdmin::do_ajax_operation( 'gallery_recover_image' , $_POST['doaction'], __('Recover from backup','nggallery') );
					break;
				case 'set_watermark':
				// Set watermark
					// A prefix 'gallery_' will first fetch all ids from the selected galleries
					nggAdmin::do_ajax_operation( 'gallery_set_watermark' , $_POST['doaction'], __('Set watermark','nggallery') );
					break;
				case 'import_meta':
				// Import Metadata
					// A prefix 'gallery_' will first fetch all ids from the selected galleries
					nggAdmin::do_ajax_operation( 'gallery_import_metadata' , $_POST['doaction'], __('Import metadata','nggallery') );
					break;
				case 'delete_gallery':
					// Delete gallery
					if (is_array($_POST['doaction']))
					{
                        $deleted = FALSE;
						$mapper = C_Gallery_Mapper::get_instance();
						foreach ($_POST['doaction'] as $id) {

							$gallery = $mapper->find($id);
							if ($gallery->path == '../' || FALSE !== strpos($gallery->path, '/../'))
							{
								nggGallery::show_message(sprintf(__('One or more "../" in Gallery paths could be unsafe and NextGen Gallery will not delete gallery %s automatically', 'nggallery'), $gallery->{$gallery->id_field}));
							}
							else {
								/**
								 * @var $mapper Mixin_Gallery_Mapper
								 */
								if ($mapper->destroy($id, TRUE))
									$deleted = TRUE;
							}
						}

						if ($deleted)
                            nggGallery::show_message(__('Gallery deleted successfully ', 'nggallery'));
					}
					break;
			}
		}
		if (isset ($_POST['addgallery']) && isset ($_POST['galleryname'])){

			check_admin_referer('ngg_addgallery');

			if ( !nggGallery::current_user_can( 'NextGEN Add new gallery' ))
				wp_die(__('Cheatin&#8217; uh?', 'nggallery'));

			// get the default path for a new gallery
			$newgallery = $_POST['galleryname'];
			if (!empty($newgallery))
			{
				$gallery_mapper = C_Gallery_Mapper::get_instance();
				$gallery = $gallery_mapper->create(array('title' => $newgallery));
				if ($gallery->save() && !isset($_REQUEST['attach_to_post']))
				{
					$url = admin_url() . 'admin.php?page=nggallery-manage-gallery&mode=edit&gid=' . $gallery->gid;
					$message = sprintf(__('Gallery successfully created. <a href="%s" target="_blank">Manage gallery</a>', 'nggallery'), $url);
					nggGallery::show_message($message, 'gallery_created_msg');
				}
			}

			do_action( 'ngg_update_addgallery_page' );
		}

		if (isset ($_POST['TB_bulkaction']) && isset ($_POST['TB_ResizeImages']))  {

			check_admin_referer('ngg_thickbox_form');

			//save the new values for the next operation
			$ngg->options['imgWidth']  = (int) $_POST['imgWidth'];
			$ngg->options['imgHeight'] = (int) $_POST['imgHeight'];
			// What is in the case the user has no if cap 'NextGEN Change options' ? Check feedback
			update_option('ngg_options', $ngg->options);

			$gallery_ids  = explode(',', $_POST['TB_imagelist']);
			// A prefix 'gallery_' will first fetch all ids from the selected galleries
			nggAdmin::do_ajax_operation( 'gallery_resize_image' , $gallery_ids, __('Resize images','nggallery') );
		}

		if (isset ($_POST['TB_bulkaction']) && isset ($_POST['TB_NewThumbnail']))  {

			check_admin_referer('ngg_thickbox_form');

			// save the new values for the next operation
            $settings = C_NextGen_Settings::get_instance();
            $settings->thumbwidth  = (int)$_POST['thumbwidth'];
            $settings->thumbheight = (int)$_POST['thumbheight'];
            $settings->thumbfix    = isset($_POST['thumbfix']) ? TRUE : FALSE;
            $settings->save();
			ngg_refreshSavedSettings();

			// What is in the case the user has no if cap 'NextGEN Change options' ? Check feedback
			$gallery_ids  = explode(',', $_POST['TB_imagelist']);

			// A prefix 'gallery_' will first fetch all ids from the selected galleries
			nggAdmin::do_ajax_operation( 'gallery_create_thumbnail' , $gallery_ids, __('Create new thumbnails','nggallery') );
		}

	}

	function post_processor_images() {
		global $wpdb, $ngg, $nggdb;

		// bulk update in a single gallery
		if (isset ($_POST['bulkaction']) && isset ($_POST['doaction']))  {

			check_admin_referer('ngg_updategallery');

			switch ($_POST['bulkaction']) {
				case 'no_action';
					break;
				case 'rotate_cw':
					nggAdmin::do_ajax_operation( 'rotate_cw' , $_POST['doaction'], __('Rotate images', 'nggallery') );
					break;
				case 'rotate_ccw':
					nggAdmin::do_ajax_operation( 'rotate_ccw' , $_POST['doaction'], __('Rotate images', 'nggallery') );
					break;
				case 'recover_images':
					nggAdmin::do_ajax_operation( 'recover_image' , $_POST['doaction'], __('Recover from backup', 'nggallery') );
					break;
				case 'set_watermark':
					nggAdmin::do_ajax_operation( 'set_watermark' , $_POST['doaction'], __('Set watermark', 'nggallery') );
					break;
				case 'delete_images':
					if ( is_array($_POST['doaction']) ) {
						foreach ( $_POST['doaction'] as $imageID ) {
							$image = $nggdb->find_image( $imageID );
							if ($image) {
								do_action('ngg_delete_picture', $image->pid, $image);
								if ($ngg->options['deleteImg']) {
                                    $storage = C_Gallery_Storage::get_instance();
                                    $storage->delete_image($image->pid);
								}
								$delete_pic = C_Image_Mapper::get_instance()->destroy($image->pid);
							}
						}
						if($delete_pic)
							nggGallery::show_message(__('Pictures deleted successfully ', 'nggallery'));
					}
					break;
				case 'import_meta':
					nggAdmin::do_ajax_operation( 'import_metadata' , $_POST['doaction'], __('Import metadata', 'nggallery') );
					break;
			}
		}

		if (isset ($_POST['TB_bulkaction']) && isset ($_POST['TB_ResizeImages']))  {

			check_admin_referer('ngg_thickbox_form');

			//save the new values for the next operation
			$ngg->options['imgWidth']  = (int) $_POST['imgWidth'];
			$ngg->options['imgHeight'] = (int) $_POST['imgHeight'];

			update_option('ngg_options', $ngg->options);

			$pic_ids  = explode(',', $_POST['TB_imagelist']);
			nggAdmin::do_ajax_operation( 'resize_image' , $pic_ids, __('Resize images', 'nggallery') );
		}

		if (isset ($_POST['TB_bulkaction']) && isset ($_POST['TB_NewThumbnail']))  {

			check_admin_referer('ngg_thickbox_form');

			// save the new values for the next operation
            $settings = C_NextGen_Settings::get_instance();
            $settings->thumbwidth  = (int)$_POST['thumbwidth'];
            $settings->thumbheight = (int)$_POST['thumbheight'];
            $settings->thumbfix    = isset($_POST['thumbfix']) ? TRUE : FALSE;
            $settings->save();
			ngg_refreshSavedSettings();

			$pic_ids  = explode(',', $_POST['TB_imagelist']);
			nggAdmin::do_ajax_operation( 'create_thumbnail' , $pic_ids, __('Create new thumbnails', 'nggallery') );
		}

		if (isset ($_POST['TB_bulkaction']) && isset ($_POST['TB_SelectGallery']))  {

			check_admin_referer('ngg_thickbox_form');

			$pic_ids  = explode(',', $_POST['TB_imagelist']);
			$dest_gid = (int) $_POST['dest_gid'];

			switch ($_POST['TB_bulkaction']) {
				case 'copy_to':
				    $destination = C_Gallery_Mapper::get_instance()->find($dest_gid);
                    $new_ids     = C_Gallery_Storage::get_instance()->copy_images($pic_ids, $dest_gid);

                    if (!empty($new_ids))
                    {
                        $admin_url = admin_url();
                        $title     = esc_html($destination->title);
                        $link      = "<a href='{$admin_url}admin.php?page=nggallery-manage-gallery&mode=edit&gid={$destination->gid}'>{$title}</a>";
                        nggGallery::show_message(
                            sprintf(__('Copied %1$s picture(s) to gallery: %2$s .','nggallery'), count($new_ids), $link)
                        );
                    }
                    else {
                        nggGallery::show_error(
                            __('Failed to copy images', 'nggallery')
                        );
                    }

					break;
				case 'move_to':
                    $destination = C_Gallery_Mapper::get_instance()->find($dest_gid);
                    $new_ids     = C_Gallery_Storage::get_instance()->move_images($pic_ids, $dest_gid);

                    if (!empty($new_ids))
                    {
                        $admin_url = admin_url();
                        $title     = esc_html($destination->title);
                        $link      = "<a href='{$admin_url}admin.php?page=nggallery-manage-gallery&mode=edit&gid={$destination->gid}'>{$title}</a>";
                        nggGallery::show_message(
                            sprintf(__('Moved %1$s picture(s) to gallery: %2$s .','nggallery'), count($new_ids), $link)
                        );
                    }
                    else {
                        nggGallery::show_error(
                            __('Failed to move images', 'nggallery')
                        );
                    }
					break;
			}
		}

		if (isset ($_POST['TB_bulkaction']) && isset ($_POST['TB_EditTags']))  {
			// do tags update

			check_admin_referer('ngg_thickbox_form');

			// get the images list
			$pic_ids = explode(',', $_POST['TB_imagelist']);
			$taglist = explode(',', $_POST['taglist']);
			$taglist = array_map('trim', $taglist);

			if (is_array($pic_ids)) {

				foreach($pic_ids as $pic_id) {

					// which action should be performed ?
					switch ($_POST['TB_bulkaction']) {
						case 'no_action';
						// No action
							break;
						case 'overwrite_tags':
						// Overwrite tags
							wp_set_object_terms($pic_id, $taglist, 'ngg_tag');
							break;
						case 'add_tags':
						// Add / append tags
							wp_set_object_terms($pic_id, $taglist, 'ngg_tag', TRUE);
							break;
						case 'delete_tags':
						// Delete tags
							$oldtags = wp_get_object_terms($pic_id, 'ngg_tag', 'fields=names');
							// get the slugs, to vaoid  case sensitive problems
							$slugarray = array_map('sanitize_title', $taglist);
							$oldtags = array_map('sanitize_title', $oldtags);
							// compare them and return the diff
							$newtags = array_diff($oldtags, $slugarray);
							wp_set_object_terms($pic_id, $newtags, 'ngg_tag');
							break;
					}
				}

				nggGallery::show_message( __('Tags changed', 'nggallery') );
			}
		}

		if (isset($_POST['updatepictures']))
		{
            // Update pictures
			$success = FALSE;

			check_admin_referer('ngg_updategallery');

			if (nggGallery::current_user_can('NextGEN Edit gallery options') && !isset($_GET['s']))
			{
                $tags = array('<a>', '<abbr>', '<acronym>', '<address>', '<b>', '<base>', '<basefont>', '<big>', '<blockquote>', '<br>', '<br/>', '<caption>', '<center>', '<cite>', '<code>', '<col>', '<colgroup>', '<dd>', '<del>', '<dfn>', '<dir>', '<div>', '<dl>', '<dt>', '<em>', '<fieldset>', '<font>', '<h1>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>', '<hr>', '<i>', '<img>', '<ins>', '<label>', '<legend>', '<li>', '<menu>', '<noframes>', '<noscript>', '<ol>', '<optgroup>', '<option>', '<p>', '<pre>', '<q>', '<s>', '<samp>', '<select>', '<small>', '<span>', '<strike>', '<strong>', '<sub>', '<sup>', '<table>', '<tbody>', '<td>', '<tfoot>', '<th>', '<thead>', '<tr>', '<tt>', '<u>', '<ul>');
				$fields = array('title', 'galdesc');
				
				// Sanitize fields
				foreach ($fields as $field) {
					$html = stripslashes($_POST[$field]);
					$html = preg_replace('/\\s+on\\w+=(["\']).*?\\1/i', '', $html);
					$html = preg_replace('/(<\/[^>]+?>)(<[^>\/][^>]*?>)/', '$1 $2', $html);
					$html = strip_tags($html, implode('', $tags));
					$_POST[$field] = $html;
				}

				$mapper = C_Gallery_Mapper::get_instance();
				
				// Update the gallery
				if (!$this->gallery)
				{
					$this->gallery = $mapper->find($this->gid, TRUE);
				}

                if ($this->gallery)
                {
                    foreach ($_POST as $key => $value) {
                        // Yet another IIS hack: gallery paths can be mangled into \\wp-content\\blah\\ which causes
                        // later errors when validating the gallery path: just automatically replace \\ with / here.
                        if ($key === 'path')
                            $value = str_replace('\\\\', '/', $value);

                        $this->gallery->$key = $value;
                    }

					$mapper->save($this->gallery);

					if ($this->gallery->is_invalid())
					{
						foreach ($this->gallery->get_errors() as $property => $errors) {
							foreach ($errors as $error) {
								nggGallery::show_error($error);
							}
						}
					}

					wp_cache_delete($this->gid, 'ngg_gallery');
					$success = $this->gallery->is_valid();
				}
			}

            $pictures_updated = $this->update_pictures();
			if ($success || $pictures_updated >= 1)
			{
				// Hook for other plugin to update the fields
				do_action('ngg_update_gallery', $this->gid, $_POST);
				nggGallery::show_message(__('Updated successfully', 'nggallery'));
			}
		}

        // Rescan folder
        if (isset ($_POST['scanfolder']))
        {
			check_admin_referer('ngg_updategallery');

			$gallerypath = $wpdb->get_var("SELECT `path` FROM {$wpdb->nggallery} WHERE `gid` = '{$this->gid}'");
			nggAdmin::import_gallery($gallerypath, $this->gid);
		}

        // Add a new page
        if (isset ($_POST['addnewpage']))
        {
            check_admin_referer('ngg_updategallery');

            $parent_id     = esc_attr($_POST['parent_id']);
            $gallery_title = esc_attr($_POST['title']);
            $mapper        = C_Gallery_Mapper::get_instance();
            $gallery       = $mapper->find($this->gid);
            $gallery_name  = $gallery->name;

            // Create a WP page
            global $user_ID;

            $page['post_type']    = 'page';
	        $page['post_content'] = apply_filters('ngg_add_page_shortcode', '[nggallery id="' . $this->gid . '"]' );
            $page['post_parent']  = $parent_id;
            $page['post_author']  = $user_ID;
            $page['post_status']  = 'publish';
            $page['post_title']   = $gallery_title == '' ? $gallery_name : $gallery_title;
            $page = apply_filters('ngg_add_new_page', $page, $this->gid);

            $gallery_pageid = wp_insert_post ($page);
            if ($gallery_pageid != 0)
            {
                $gallery->pageid = $gallery_pageid;
                $mapper->save($gallery);
                nggGallery::show_message(__('New gallery page ID', 'nggallery') . ' ' . $gallery_pageid . ' -> <strong>' . $gallery_title . '</strong> ' . __('created','nggallery'));
            }

            do_action('ngg_gallery_addnewpage', $this->gid);
        }
    }

	function can_user_manage_gallery()
	{
		$retval 	= FALSE;

		if ($this->gallery && wp_get_current_user()->ID == $this->gallery->author) {
			$retval = TRUE;
		}
		elseif(M_Security::is_allowed('nextgen_edit_gallery_unowned')) {
			$retval = TRUE;
		}

		return $retval;
	}

	function update_pictures()
	{
		$updated = 0;

		if (!$this->can_user_manage_gallery()) return $updated;

		if (isset($_POST['images']) && is_array($_POST['images'])) {
			$image_mapper = C_Image_Mapper::get_instance();

			foreach ($_POST['images'] as $pid => $data) {
                if (!isset($data['exclude'])) $data['exclude'] = 0;
				if (($image = $image_mapper->find($pid))) {
					// Strip slashes from title/description/alttext fields
					if (isset($data['description'])) {
						$data['description'] = stripslashes($data['description']);
					}
					if (isset($data['alttext'])) {
						$data['alttext'] = stripslashes($data['alttext']);
					}
					if (isset($data['title'])) {
						$data['title'] = stripslashes($data['title']);
					}

					// Generate new slug if the alttext has changed
					if (isset($data['alttext']) && $image->alttext != $data['alttext']) {
						$data['slug'] = NULL; // will cause a new slug to be generated
					}

					// Update all fields
					foreach ($data as $key => $value) {
						$image->$key = $value;
					}
					if ($image_mapper->save($image)) {
						$updated += 1;

						// Update the tags for the image
						if (isset($data['tags'])) {
							$tags = $data['tags'];
							if (!is_array($tags)) $tags = explode(',', $tags);
							foreach ($tags as &$tag) $tag = trim($tag);
							wp_set_object_terms($image->{$image->id_field},$tags, 'ngg_tag');
						}

						// remove from cache
						wp_cache_delete($image->pid, 'ngg_image');

						// hook for other plugins after image is updated
						do_action('ngg_image_updated', $image);
					}
				}
			}

            // Determine if any WP terms have been orphaned and clean them up
            global $wpdb;
            $results = $wpdb->get_col("SELECT t.`term_id` FROM `{$wpdb->term_taxonomy}` tt
                                       LEFT JOIN `{$wpdb->terms}` t ON tt.`term_id` = t.`term_id`
                                       WHERE tt.`taxonomy` = 'ngg_tag' AND tt.`count` <= 0");
            if (!empty($results))
            {
                foreach ($results as $term_id) {
                    $term_id = apply_filters('ngg_pre_delete_unused_term_id', $term_id);
                    if (!empty($term_id))
                        wp_delete_term($term_id, 'ngg_tag');
                }
            }
		}

		return $updated;
	}

	// Check if user can select a author
	function get_editable_user_ids( $user_id, $exclude_zeros = true ) {
		global $wpdb;

		$user = new WP_User( $user_id );

		if ( ! $user->has_cap('NextGEN Manage others gallery') ) {
			if ( $user->has_cap('NextGEN Manage gallery') || $exclude_zeros == false )
				return array($user->id);
			else
				return false;
		}

		$level_key = $wpdb->prefix . 'user_level';
		$query = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '$level_key'";
		if ( $exclude_zeros )
			$query .= " AND meta_value != '0'";

		return $wpdb->get_col( $query );
	}

	function search_images() {
		global $nggdb;

		if ( empty($_GET['s']) )
			return;
		//on what ever reason I need to set again the query var
		set_query_var('s', $_GET['s']);
		$request = get_search_query();

        // look now for the images
        $search_for_images = (array) $nggdb->search_for_images( $request );
        $search_for_tags   = (array) nggTags::find_images_for_tags( $request , 'ASC' );

        // finally merge the two results together
        $this->search_result = array_merge( $search_for_images , $search_for_tags );

        // TODO: Currently we didn't support a proper pagination
        $nggdb->paged['total_objects'] = $nggdb->paged['objects_per_page'] = count ($this->search_result) ;
        $nggdb->paged['max_objects_per_page'] = 1;

		// show pictures page
		$this->mode = 'edit';
	}

	/**
	 * Display the pagination.
	 *
	 * @since 1.8.0
     * @author taken from WP core (see includes/class-wp-list-table.php)
	 * @return string echo the html pagination bar
	 */
	function pagination( $which, $current, $total_items, $per_page ) {

        $total_pages = ($per_page > 0) ? ceil( $total_items / $per_page ) : 1;

		$output = '<span class="displaying-num">' . sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );

		$page_links = array();

		$disable_first = $disable_last = '';
		if ( $current == 1 )
			$disable_first = ' disabled';
		if ( $current == $total_pages )
			$disable_last = ' disabled';

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'first-page' . $disable_first,
			esc_attr__( 'Go to the first page' ),
			nextgen_esc_url( remove_query_arg( 'paged', $current_url ) ),
			'&laquo;'
		);

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'prev-page' . $disable_first,
			esc_attr__( 'Go to the previous page' ),
			nextgen_esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
			'&lsaquo;'
		);

		if ( 'bottom' == $which )
			$html_current_page = $current;
		else
			$html_current_page = sprintf( "<input class='current-page' title='%s' type='text' name='%s' value='%s' size='%d' />",
				esc_attr__( 'Current page' ),
				esc_attr( 'post_paged' ),
				$current,
				strlen( $total_pages )
			);

		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		$page_links[] = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . '</span>';

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'next-page' . $disable_last,
			esc_attr__( 'Go to the next page' ),
			nextgen_esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
			'&rsaquo;'
		);

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'last-page' . $disable_last,
			esc_attr__( 'Go to the last page' ),
			nextgen_esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
			'&raquo;'
		);

		$output .= "\n<span class='pagination-links'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages )
			$page_class = $total_pages < 2 ? ' one-page' : '';
		else
			$page_class = ' no-pages';

		$pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

		echo $pagination;
		return $pagination;
	}

}