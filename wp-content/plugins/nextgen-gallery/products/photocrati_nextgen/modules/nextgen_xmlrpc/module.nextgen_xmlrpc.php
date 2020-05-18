<?php

/***
{
	Module: photocrati-nextgen_xmlrpc
}
***/

class M_NextGen_XmlRpc extends C_Base_Module
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
			'photocrati-nextgen_xmlrpc',
			'NextGEN Gallery XML-RPC',
			'Provides an XML-RPC API for NextGEN Gallery',
			'3.2.19',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);
	}

  function get_type_list()
  {
      return array(
          'C_NextGen_API' => 'class.nextgen_api.php',
          'C_NextGen_API_XMLRPC' => 'class.nextgen_api_xmlrpc.php',
          'A_NextGen_API_Ajax' => 'adapter.nextgen_api_ajax.php',
      );
  }

  function _register_utilities()
  {
		$this->nextgen_api = C_NextGen_API::get_instance();
		$this->nextgen_api_xmlrpc = C_NextGen_API_XMLRPC::get_instance();
  }

	function _register_hooks()
	{
		add_filter('xmlrpc_methods', array($this, 'add_methods') );
	}
	
	function _register_adapters()
	{
		// Provides AJAX actions for the JSON API interface
		$this->get_registry()->add_adapter(
			'I_Ajax_Controller',   'A_NextGen_API_Ajax'
		);
	}

	function add_methods($methods)
	{
		$methods['ngg.installed'] = array($this->nextgen_api_xmlrpc, 'get_version');
		$methods['ngg.setPostThumbnail'] = array($this->nextgen_api_xmlrpc, 'set_post_thumbnail');

		// Image methods
		$methods['ngg.getImage'] = array($this->nextgen_api_xmlrpc, 'get_image');
		$methods['ngg.getImages'] = array($this->nextgen_api_xmlrpc, 'get_images');
		$methods['ngg.uploadImage'] = array($this->nextgen_api_xmlrpc, 'upload_image');
		$methods['ngg.editImage'] = array($this->nextgen_api_xmlrpc, 'edit_image');
		$methods['ngg.deleteImage'] = array($this->nextgen_api_xmlrpc, 'delete_image');

		// Gallery methods
		$methods['ngg.getGallery'] = array($this->nextgen_api_xmlrpc, 'get_gallery');
		$methods['ngg.getGalleries'] = array($this->nextgen_api_xmlrpc, 'get_galleries');
		$methods['ngg.newGallery'] = array($this->nextgen_api_xmlrpc, 'create_gallery');
		$methods['ngg.editGallery'] = array($this->nextgen_api_xmlrpc, 'edit_gallery');
		$methods['ngg.deleteGallery'] = array($this->nextgen_api_xmlrpc, 'delete_gallery');
		
		// Album methods
		$methods['ngg.getAlbum'] = array($this->nextgen_api_xmlrpc, 'get_album');
		$methods['ngg.getAlbums'] = array($this->nextgen_api_xmlrpc, 'get_albums');
		$methods['ngg.newAlbum'] = array($this->nextgen_api_xmlrpc, 'create_album');
		$methods['ngg.editAlbum'] = array($this->nextgen_api_xmlrpc, 'edit_album');
		$methods['ngg.deleteAlbum'] = array($this->nextgen_api_xmlrpc, 'delete_album');

		return $methods;
	}
}

new M_NextGen_XmlRpc;
