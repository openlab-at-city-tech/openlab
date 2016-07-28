<?php 



class wp_lightbox2_database_params{
	
	public $installed_options; // all standart_options
	private $plugin_url;
	public  $get_general_settings;
	public  $get_design_settings;
	function __construct(){
		
		if(isset($params['plugin_url']))
			$this->plugin_url=$params['plugin_url'];
		else
			$this->plugin_url=trailingslashit(dirname(plugins_url('',__FILE__)));	
		$general_settings=array(
			'jqlb_overlay_opacity'=>'80',
			'jqlb_help_text'=>'',
			'jqlb_margin_size'=>0,			
			'jqlb_automate'=>1,
			'jqlb_comments'=>1,
			'jqlb_resize_on_demand'=>0,
			'jqlb_show_download'=>0,
			'jqlb_navbarOnTop'=>0,
			'jqlb_resize_speed'=>400,
		);
		foreach($general_settings as $key => $value){
			if(!(get_option($key,12365498798465132148947984651)==12365498798465132148947984651)){
				$general_settings[$key]=get_option($key);				
			}
			else{
				$general_settings[$key]=$value;
			}			
		}	
		 $this->get_general_settings=$general_settings;
		 $locale = jqlb_get_locale();
		 $folder='';
		 switch($locale){
			case 'cs_CZ':
				$folder='cs_CZ/';
			break;
			case 'ru_RU':
				$folder='ru_RU/';
			break;
			case 'pl_PL':
				$folder='pl_PL/';
			break;
			case 'he_IL':
				$folder='he_IL/';
			break; 
		}

		// Note: Deprecated 3.0.5+
		$design_settings=array(
			'jqlb_overlay_opacity'=>'80',
			'jqlb_overlay_color'=>'#000000',
			'jqlb_overlay_close'=>'1',			
			'jqlb_border_width'=>'10',
			'jqlb_border_color'=>'#ffffff',
			'jqlb_border_radius'=>'0',			
			'jqlb_image_info_background_transparency'=>'100',
			'jqlb_image_info_bg_color'=>'#ffffff',
			'jqlb_image_info_text_color'=>'#000000',
			'jqlb_image_info_text_fontsize'=>'10',
			'jqlb_show_text_for_image'=>'1',
			'jqlb_next_image_title'=> __('next image', 'jqlb'),
			'jqlb_previous_image_title'=>__('previous image', 'jqlb'),
			'jqlb_next_button_image'=>$this->plugin_url.'styles/images/'.$folder.'next.gif',
			'jqlb_previous_button_image'=>$this->plugin_url.'styles/images/'.$folder.'prev.gif',
			'jqlb_maximum_width'=>'',
			'jqlb_maximum_height'=>'',
			'jqlb_show_close_button'=>'1',
			'jqlb_close_image_title'=>__('close image gallery', 'jqlb'),
			'jqlb_close_image_max_heght'=>'22',			
			'jqlb_image_for_close_lightbox'=>$this->plugin_url.'styles/images/'.$folder.'closelabel.gif',	
			'jqlb_keyboard_navigation'=>'1',
			'jqlb_popup_size_fix'=>'0',			
		);	
		foreach($design_settings as $key => $value){
			if(!(get_option($key,12365498798465132148947984651)==12365498798465132148947984651)){
				$design_settings[$key]=get_option($key);				
			}
			else{
				$design_settings[$key]=$value;
			}			
		}	
		  $this->get_design_settings=$design_settings;
				
	}
	

}