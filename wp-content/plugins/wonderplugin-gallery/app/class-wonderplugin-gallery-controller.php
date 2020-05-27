<?php 

if ( ! defined( 'ABSPATH' ) )
	exit;
	
require_once 'class-wonderplugin-gallery-model.php';
require_once 'class-wonderplugin-gallery-view.php';
require_once 'class-wonderplugin-gallery-update.php';

class WonderPlugin_Gallery_Controller {

	private $view, $model, $update;

	function __construct() {

		$this->model = new WonderPlugin_Gallery_Model($this);	
		$this->view = new WonderPlugin_Gallery_View($this);
		$this->update = new WonderPlugin_Gallery_Update($this);
		
		$this->init();
	}
	
	function add_metaboxes()
	{
		$this->view->add_metaboxes();
	}
	
	function show_overview() {
		
		$this->view->print_overview();
	}
	
	function show_items() {
	
		$this->view->print_items();
	}
	
	function add_new() {
		
		$this->view->print_add_new();
	}
	
	function show_item()
	{
		$this->view->print_item();
	}
	
	function edit_item()
	{
		$this->view->print_edit_item();
	}
	
	function edit_settings()
	{
		$this->view->print_edit_settings();
	}
	
	function save_settings($options)
	{
		$this->model->save_settings($options);
	}
	
	function get_settings() 
	{
		return $this->model->get_settings();
	}
	
	function register()
	{
		$this->view->print_register();
	}
	
	function check_license($options)
	{
		return $this->model->check_license($options);
	}
	
	function deregister_license($options)
	{
		return $this->model->deregister_license($options);
	}
	
	function save_plugin_info($info)
	{
		return $this->model->save_plugin_info($info);
	}
	
	function get_plugin_info()
	{
		return $this->model->get_plugin_info();
	}
	
	function get_update_data($action, $key)
	{
		return $this->update->get_update_data($action, $key);
	}
	
	function generate_body_code($id, $contents, $attributes, $has_wrapper) {
		
		return $this->model->generate_body_code($id, $contents, $attributes, $has_wrapper);
	}
	
	function delete_item($id)
	{
		return $this->model->delete_item($id);
	}
	
	function trash_item($id)
	{
		return $this->model->trash_item($id);
	}
	
	function restore_item($id)
	{
		return $this->model->restore_item($id);
	}
	
	function clone_item($id)
	{
		return $this->model->clone_item($id);
	}
	
	function save_item($item)
	{
		return $this->model->save_item($item);	
	}
	
	function get_list_data() {
	
		return $this->model->get_list_data();
	}
	
	function get_item_data($id) {
		
		return $this->model->get_item_data($id);
	}
	
	function search_replace_items($post)
	{
		return $this->model->search_replace_items($post);
	}
	
	function import_export()
	{
		$this->view->import_export();
	}
	
	function import_gallery($post, $files)
	{
		return $this->model->import_gallery($post, $files);
	}
	
	function export_gallery() {
	
		return $this->model->export_gallery();
	}
	
	function init() {
	
		$engine = array("WordPress Gallery", "WordPress Gallery Plugin", "WordPress Photo Gallery Plugin", "WordPress Image Gallery Plugin", "WordPress Video Gallery Plugin", "WordPress YouTube Gallery Plugin", "Responsive WordPress Gallery Plugin", "Responsive WordPress Photo Gallery Plugin", "Responsive WordPress Image Gallery Plugin", "Responsive WordPress Video Gallery Plugin", "Responsive WordPress YouTube Gallery Plugin");
		$option_name = 'wonderplugin-gallery-engine';
		if ( get_option( $option_name ) == false )
			update_option( $option_name, $engine[array_rand($engine)] );
	}
}