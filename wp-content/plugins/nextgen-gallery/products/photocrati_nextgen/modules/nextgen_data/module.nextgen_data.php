<?php

/***
{
Module: photocrati-nextgen-data,
Depends: { photocrati-datamapper }
}
 ***/

class M_NextGen_Data extends C_Base_Module
{
	function define($id = 'pope-module',
		$name = 'Pope Module',
		$description = '',
		$version = '',
		$uri = '',
		$author = '',
		$author_uri = '',
		$context = FALSE)
	{
		parent::define(
			'photocrati-nextgen-data',
			'NextGEN Data Tier',
			"Provides a data tier for NextGEN gallery based on the DataMapper module",
			'3.3.14',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);

		C_Photocrati_Installer::add_handler($this->module_id, 'C_NextGen_Data_Installer');
	}

	function _register_adapters()
	{
		$this->get_registry()->add_adapter('I_Component_Factory', 'A_NextGen_Data_Factory');
		#$this->get_registry()->add_adapter('I_CustomPost_DataMapper', 'A_Attachment_DataMapper', 'attachment');
		$this->get_registry()->add_adapter('I_Installer', 'A_NextGen_Data_Installer');
	}


	function _register_utilities()
	{
		$this->get_registry()->add_utility('I_Gallery_Mapper', 'C_Gallery_Mapper');
		$this->get_registry()->add_utility('I_Image_Mapper', 'C_Image_Mapper');
		$this->get_registry()->add_utility('I_Album_Mapper', 'C_Album_Mapper');
		$this->get_registry()->add_utility('I_Gallery_Storage', 'C_Gallery_Storage');
	}

    function initialize()
    {
    }

    public static function check_gd_requirement()
    {
        return function_exists("gd_info");
    }

    public static function check_pel_min_php_requirement()
    {
        return version_compare(phpversion(), '5.3.0', '>');
    }

    public function check_domdocument_requirement()
    {
        return class_exists('DOMDocument');
    }

    function _register_hooks()
	{
	    add_action('admin_init', array($this, 'register_requirements'));
		add_action('init', array(&$this, 'register_custom_post_types'));
		add_filter('posts_orderby', array($this, 'wp_query_order_by'), 10, 2);
	}

	public function register_requirements()
    {
        C_Admin_Requirements_Manager::get_instance()->add(
            'nextgen_data_sanitation',
            'phpext',
            array($this, 'check_domdocument_requirement'),
            array('message' => __('XML is strongly encouraged for safely editing image data', 'nggallery'))
        );

        C_Admin_Requirements_Manager::get_instance()->add(
            'nextgen_data_pel_min_php_version',
            'phpver',
            array($this, 'check_pel_min_php_requirement'),
            array('message' => __('PHP 5.3 is required to write EXIF data to thumbnails and resized images', 'nggallery'))
        );

        C_Admin_Requirements_Manager::get_instance()->add(
            'nextgen_data_gd_requirement',
            'phpext',
            array($this, 'check_gd_requirement'),
            array('message'     => __('GD is required for generating image thumbnails, resizing images, and generating watermarks', 'nggallery'),
                'dismissable' => FALSE)
        );
    }

    function register_custom_post_types()
	{
		$types = array(
			'ngg_album'		=>	'NextGEN Gallery - Album',
			'ngg_gallery'	=>	'NextGEN Gallery - Gallery',
			'ngg_pictures'	=>	'NextGEN Gallery - Image',
		);

		foreach ($types as $type => $label) {
			register_post_type($type, array(
				'label'					=>	$label,
				'publicly_queryable'	=>	FALSE,
				'exclude_from_search'	=>	TRUE,
			));
		}
	}

    function wp_query_order_by($order_by, $wp_query)
	{
		if ($wp_query->get('datamapper_attachment'))
		{
			$order_parts = explode(' ', $order_by);
			$order_name = array_shift($order_parts);

			$order_by = 'ABS(' . $order_name . ') ' . implode(' ', $order_parts) . ', ' . $order_by;
		}

		return $order_by;
	}

    static function strip_html($data, $just_scripts=FALSE)
	{
		// NGG 3.3.11 fix. Some of the data persisted with 3.3.11 didn't strip out all HTML
		if (strpos($data, 'ngg_data_strip_html_placeholder') !== FALSE) {
			if (class_exists('DomDocument')) {
				$dom = new DOMDocument('1.0', 'UTF-8');
				$dom->loadHTML($data);
				$el = $dom->getElementById('ngg_data_strip_html_placeholder');
				$parts = array_map(
					function($el) use ($dom) {
						$part = $dom->saveHTML($el);
						return $part instanceof DOMText ? $part->data : (string) $part;
					},
					$el->childNodes ? iterator_to_array($el->childNodes) : []
				);
				return self::strip_html(implode(" ", $parts), $just_scripts);
			}
			else return strip_tags($data);
		}

		// Remove all HTML elements
		if (!$just_scripts) return strip_tags($data);

		// Remove unsafe HTML
		else if (class_exists('DOMDocument')) {
			// This can generate a *lot* of warnings when given improper texts
			libxml_use_internal_errors(true);
			libxml_clear_errors();

			if (!class_exists("HTMLPurifier_Config")) {
				require_once(NGG_PLUGIN_DIR."vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php");
			}
			$config = HTMLPurifier_Config::createDefault();
			$config->set('Cache', 'DefinitionImpl', NULL);
			$purifier = new HTMLPurifier($config);
			return $purifier->purify($data);
		}
		else  {
			// wp_strip_all_tags() is misleading in a way - it only removes <script> and <style>
			// tags, nothing
			return wp_strip_all_tags($data, TRUE);
		}

		return $data;
	}

    function get_type_list()
    {
        return array(
            'A_Attachment_Datamapper'           => 'adapter.attachment_datamapper.php',
            'A_Customtable_Sorting_Datamapper'  => 'adapter.customtable_sorting_datamapper.php',
            'A_Nextgen_Data_Factory'            => 'adapter.nextgen_data_factory.php',
            'A_Parse_Image_Metadata'            => 'adapter.parse_image_metadata.php',
            'C_Album'                           => 'class.album.php',
            'C_Album_Mapper'                    => 'class.album_mapper.php',
            'C_Exif_Writer_Wrapper'             => 'class.exif_writer_wrapper.php',
            'C_Gallery'                         => 'class.gallery.php',
            'C_Gallery_Mapper'                  => 'class.gallery_mapper.php',
            'C_Gallery_Storage'                 => 'class.gallery_storage.php',
            'C_Image'                           => 'class.image.php',
            'C_Image_Mapper'                    => 'class.image_mapper.php',
            'C_Image_Wrapper'                   => 'class.image_wrapper.php',
            'C_Image_Wrapper_Collection'        => 'class.image_wrapper_collection.php',
            'C_NextGen_Data_Installer'          => 'class.nextgen_data_installer.php',
            'C_Nextgen_Metadata'                => 'class.nextgen_metadata.php',
			'C_Ngglegacy_Thumbnail'             => 'class.ngglegacy_thumbnail.php',
			'C_Dynamic_Thumbnails_Manager' 			=> 'class.dynamic_thumbnails_manager.php',			
            'Mixin_NextGen_Table_Extras'        => 'mixin.nextgen_table_extras.php',
            'Mixin_GalleryStorage_Base'              => 'mixin.gallerystorage_base.php',
            'Mixin_GalleryStorage_Base_Dynamic'      => 'mixin.gallerystorage_base_dynamic.php',
            'Mixin_GalleryStorage_Base_Getters'      => 'mixin.gallerystorage_base_getters.php',
            'Mixin_GalleryStorage_Base_Management'   => 'mixin.gallerystorage_base_management.php',
            'Mixin_GalleryStorage_Base_MediaLibrary' => 'mixin.gallerystorage_base_medialibrary.php',
            'Mixin_GalleryStorage_Base_Upload'       => 'mixin.gallerystorage_base_upload.php'

        );
    }
}
new M_NextGen_Data();