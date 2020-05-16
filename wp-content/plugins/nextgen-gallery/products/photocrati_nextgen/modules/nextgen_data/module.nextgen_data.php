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
			'3.3.2',
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
		$retval = $data;

		if (!$just_scripts)
		{
			// Remove *ALL* HTML and tag contents
			$retval = wp_strip_all_tags($retval, TRUE);
		}
		else if (class_exists('DOMDocument')) {

			// Allows HTML to remain but we strip nearly all attributes, strip all
			// <script> tags, and sanitize hrefs to prevent javascript.
			//
			// This can generate a *lot* of warnings when given improper texts
			libxml_use_internal_errors(true);
			libxml_clear_errors();

			$allowed_attributes = array(
			    '*' => array('id', 'class', 'href', 'name', 'title', 'rel', 'style'),
                'a' => array('target', 'rel'),
                'img' => array('src', 'alt', 'title')
            );

			if (is_object($data))
			{
				// First... recurse to the deepest elements and work back & upwards
				if ($data->hasChildNodes())
				{
					foreach (range($data->childNodes->length - 1, 0) as $i) {
						self::strip_html($data->childNodes->item($i), TRUE);
					}
				}

				// Remove disallowed elements and content
				if ($data instanceof DOMElement) {
					foreach ($data->getElementsByTagName('script') as $deleteme) {
						/**
						 * @var DOMNode $deleteme
						 */
						$data->removeChild($deleteme);
					}
				}

				// Strip (nearly) all attributes
				if (!empty($data->attributes))
				{
					// DOMDocument reindexes as soon as any changes are made so we
					// must loop through attributes backwards
					for ($i = $data->attributes->length - 1; $i >= 0; --$i) {
						$item = $data->attributes->item($i);
						$name = $item->nodeName;

						$allowed = FALSE;
						foreach ($allowed_attributes as $element_type => $attributes) {
                            if (($data->tagName == $element_type || $element_type == '*')
                            &&  in_array($name, $attributes)) {
                                    $allowed = TRUE;
                            }
                        }

                        if (!$allowed)
							$data->removeAttribute($name);

						// DO NOT EVER allow href="javascript:...."
						if (strpos($item->nodeValue, 'javascript:') === 0)
							$item->nodeValue = '#';
					}
				}
			}
			else {
				$dom = new DOMDocument('1.0', 'UTF-8');

				if (!empty($data))
				{
					// Because DOMDocument wraps saveHTML() with HTML headers & tags we use
					// this placeholder to retrieve *just* the original given text
					$id = 'ngg_data_strip_html_placeholder';
					$start = "<div id=\"{$id}\">";
					$end = '</div>';
					$start_length = strlen($start);
					$end_length = strlen($end);

					// Prevent attempted work-arounds using &lt; and &gt; or other html entities
					$data = html_entity_decode($data);

					// This forces DOMDocument to treat the HTML as UTF-8
					$meta = '<meta http-equiv="Content-Type" content="charset=utf-8"/>';
					$data = $meta . $start . $data . $end;

					$dom->loadHTML($data);

					// Invoke the actual work
					self::strip_html($dom->documentElement, TRUE);

					// Export back to text
					//
					// TODO: When PHP 5.2 support is dropped we can use the target parameter
					// of the following saveHTML and rid ourselves of some of the nonsense
					// workarounds to the fact that DOMDocument used to force the output to
					// include full HTML/XML doctype and root elements.
					$retval = $dom->saveXML();

					// saveXML includes the full doctype and <html><body></body></html> wrappers
					// so we first drop everything generated up to our wrapper and chop off the
					// added end wrappers
					$position = strpos($retval, $start);
					$retval  = substr($retval, $position, -15);

					// Lastly remove our wrapper
					$retval = substr($retval, $start_length, -$end_length);
				}
				else {
					$retval = '';
				}
			}
		}

		return $retval;
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