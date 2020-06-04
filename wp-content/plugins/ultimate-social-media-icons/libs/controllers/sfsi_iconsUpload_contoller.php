<?php
/* upload custom Skins {Monad}*/
add_action('wp_ajax_UploadSkins','sfsi_UploadSkins');
function sfsi_UploadSkins()
{
	// extract($_REQUEST);
	if ( !wp_verify_nonce( $_POST['nonce'], "UploadSkins")) {
      echo  json_encode(array("wrong_nonce")); exit;
    }
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }

	$custom_imgurl = (isset($_POST['custom_imgurl']))?sanitize_text_field($_POST['custom_imgurl']):'';
	
	$upload_dir = wp_upload_dir();
	
	$ThumbSquareSize 		= 100; //Thumbnail will be 57X57
	$Quality 				= 90; //jpeg quality
	$DestinationDirectory   = $upload_dir['path'].'/'; //specify upload directory ends with / (slash)
	$AcceessUrl             = $upload_dir['url'].'/';
	$ThumbPrefix			= "cmicon_";
	
	$data = $custom_imgurl;
	$params = array();
	parse_str($data, $params);
	// var_dump($params);die();
	$site_url = home_url();
	foreach($params as $key => $value)
	{

		$custom_imgurl = $value;
		if(!empty($custom_imgurl))
		{
			if(strpos($custom_imgurl, $site_url) === false){
				die(json_encode(array('res'=>'thumb_error')));
			}
			$sfsi_custom_files[] = $custom_imgurl;
			
			list($CurWidth, $CurHeight) = getimagesize($custom_imgurl);
		
			$info = explode("/", $custom_imgurl);
			$iconName = array_pop($info);
			$ImageExt = substr($iconName, strrpos($iconName, '.'));
			$ImageExt = str_replace('.','',$ImageExt);
			
			$iconName = str_replace(' ','-',strtolower($iconName)); // get image name
			$ImageType = 'image/'.$ImageExt;
			
			switch(strtolower($ImageType))
			{
				case 'image/png':
						// Create a new image from file 
						$CreatedImage =  imagecreatefrompng($custom_imgurl);
						break;
				case 'image/gif':
						$CreatedImage =  imagecreatefromgif($custom_imgurl);
						break;
				case 'image/jpg':
						$CreatedImage = imagecreatefromjpeg($custom_imgurl);
						break;					
				case 'image/jpeg':
				case 'image/pjpeg':
						$CreatedImage = imagecreatefromjpeg($custom_imgurl);
						break;
				default:
						 die(json_encode(array('res'=>'type_error'))); //output error and exit
			}
	
			$ImageName = preg_replace("/\\.[^.\\s]{3,4}$/", "", $iconName);
			
			$NewIconName = "/custom_icon".$key.'.'.$ImageExt;
			$iconPath 	= $DestinationDirectory.$NewIconName; //Thumbnail name with destination directory
			
			//Create a square Thumbnail right after, this time we are using cropImage() function
			if(cropImage($CurWidth,$CurHeight,$ThumbSquareSize,$iconPath,$CreatedImage,$Quality,$ImageType))
			{
				//update database information 
				$AccressImagePath=$AcceessUrl.$NewIconName;                                        
				update_option($key,$AccressImagePath);
				die(json_encode(array('res'=>'success')));
			}
			else
			{        
			   die(json_encode(array('res'=>'thumb_error')));
			}
		}	
	}
}

/* Delete custom Skins {Monad}*/
add_action('wp_ajax_DeleteSkin','sfsi_DeleteSkin');
function sfsi_DeleteSkin()
{
	if ( !wp_verify_nonce( $_POST['nonce'], "deleteCustomSkin")) {
		echo  json_encode(array('res'=>"error")); exit;
	} 
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }
	
	$upload_dir = wp_upload_dir();
	
	if(sanitize_text_field($_POST['action']) == 'DeleteSkin' && isset($_POST['iconname']) && !empty($_POST['iconname']) && current_user_can('manage_options'))
	{
		$iconsArray = array(
			"rss_skin","email_skin","facebook_skin","twitter_skin",
			"share_skin","youtube_skin","linkedin_skin","pintrest_skin","instagram_skin"
		);
		if(in_array(sanitize_text_field($_POST['iconname']), $iconsArray))
		{
			$imgurl = get_option( sanitize_text_field($_POST['iconname']) );
			$path = parse_url($imgurl, PHP_URL_PATH);
			
			if(is_file($_SERVER['DOCUMENT_ROOT'] . $path))
			{
				unlink($_SERVER['DOCUMENT_ROOT'] . $path);
			}
		   
			delete_option( sanitize_text_field($_POST['iconname']) );
			die(json_encode(array('res'=>'success')));
		}
		else
		{
			die(json_encode(array('res'=>'error')));
		}
	}
	else
	{
		die(json_encode(array('res'=>'error')));
	}	
}

/* add ajax action for custom skin done & save{Monad}*/
add_action('wp_ajax_Iamdone','sfsi_Iamdone');
function sfsi_Iamdone()
{
	 $return = '';
	if ( !wp_verify_nonce( $_POST['nonce'], "Iamdone")) {
		echo  json_encode(array('res'=>"error")); exit;
	}
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }

	 if(get_option("rss_skin"))
	 {
		$icon = get_option("rss_skin");
		$return .= '<span class="row_17_1 rss_section sfsi-bgimage" style="background: url('.$icon.') no-repeat;"></span>';
	 }else
	 {
		$return .= '<span class="row_17_1 rss_section" style="background-position:-1px 0;"></span>';
	 }
	 
	 if(get_option("email_skin"))
	 {
		$icon = get_option("email_skin");
		$return .= '<span class="row_17_2 email_section sfsi-bgimage" style="background: url('.$icon.') no-repeat;"></span>';
	 }else
	 {
		$return .= '<span class="row_17_2 email_section" style="background-position:-58px 0;"></span>';
	 }
	 
	 if(get_option("facebook_skin"))
	 {
		$icon = get_option("facebook_skin");
		$return .= '<span class="row_17_3 facebook_section sfsi-bgimage" style="background: url('.$icon.') no-repeat;"></span>';
	 }else
	 {
		$return .= '<span class="row_17_3 facebook_section" style="background-position:-118px 0;"></span>';
	 }
	 
	 if(get_option("twitter_skin"))
	 {
		$icon = get_option("twitter_skin");
		$return .= '<span class="row_17_5 twitter_section sfsi-bgimage" style="background: url('.$icon.') no-repeat;"></span>';
	 }else
	 {
		$return .= '<span class="row_17_5 twitter_section" style="background-position:-235px 0;"></span>';
	 }
	 
	 if(get_option("share_skin"))
	 {
		$icon = get_option("share_skin");
		$return .= '<span class="row_17_6 share_section sfsi-bgimage" style="background: url('.$icon.') no-repeat;"></span>';
	 }else
	 {
		$return .= '<span class="row_17_6 share_section" style="background-position:-293px 0;"></span>';
	 }
	 
	 if(get_option("youtube_skin"))
	 {
		$icon = get_option("youtube_skin");
		$return .= '<span class="row_17_7 youtube_section sfsi-bgimage" style="background: url('.$icon.') no-repeat;"></span>';
	 }else
	 {
		$return .= '<span class="row_17_7 youtube_section" style="background-position:-350px 0;"></span>';
	 }
	 
	 if(get_option("pintrest_skin"))
	 {
		$icon = get_option("pintrest_skin");
		$return .= '<span class="row_17_8 pinterest_section sfsi-bgimage" style="background: url('.$icon.') no-repeat;"></span>';
	 }else
	 {
		$return .= '<span class="row_17_8 pinterest_section" style="background-position:-409px 0;"></span>';
	 }
	 
	 if(get_option("linkedin_skin"))
	 {
		$icon = get_option("linkedin_skin");
		$return .= '<span class="row_17_9 linkedin_section sfsi-bgimage" style="background: url('.$icon.') no-repeat;"></span>';
	 }else
	 {
		$return .= '<span class="row_17_9 linkedin_section" style="background-position:-467px 0;"></span>';
	 }
	 
	 if(get_option("instagram_skin"))
	 {
		$icon = get_option("instagram_skin");
		$return .= '<span class="row_17_10 instagram_section sfsi-bgimage" style="background: url('.$icon.') no-repeat;"></span>';
	 }else
	 {
		$return .= '<span class="row_17_10 instagram_section" style="background-position:-526px 0;"></span>';
	 }
	 if(get_option("telegram_skin"))
	 {
		$icon = get_option("telegram_skin");
		$return .= '<span class="row_17_10 telegram_section sfsi-bgimage" style="background: url('.$icon.') no-repeat;"></span>';
	 }else
	 {
		$return .= '<span class="row_17_10 telegram_section" style="background-position:-773px 0;"></span>';
	 }
	 if(get_option("vk_skin"))
	 {
		$icon = get_option("vk_skin");
		$return .= '<span class="row_17_10 vk_section sfsi-bgimage" style="background: url('.$icon.') no-repeat;"></span>';
	 }else
	 {
		$return .= '<span class="row_17_10 vk_section" style="background-position:-838px 0;"></span>';
	 }
	 if(get_option("ok_skin"))
	 {
		$icon = get_option("ok_skin");
		$return .= '<span class="row_17_10 ok_section sfsi-bgimage" style="background: url('.$icon.') no-repeat;"></span>';
	 }else
	 {
		$return .= '<span class="row_17_10 ok_section" style="background-position:-909px 0;"></span>';
	 }
	 if(get_option("weibo_skin"))
	 {
		$icon = get_option("weibo_skin");
		$return .= '<span class="row_17_10 weibo_section sfsi-bgimage" style="background: url('.$icon.') no-repeat;"></span>';
	 }else
	 {
		$return .= '<span class="row_17_10 weibo_section" style="background-position:-977px 0;"></span>';
	 }
	 if(get_option("wechat_skin"))
	 {
		$icon = get_option("wechat_skin");
		$return .= '<span class="row_17_10 wechat_section sfsi-bgimage" style="background: url('.$icon.') no-repeat;"></span>';
	 }else
	 {
		$return .= '<span class="row_1_18 wechat_section"></span>';
	 }
	 die($return);
}

/* add ajax action for custom icons upload {Monad}*/
add_action('wp_ajax_UploadIcons','sfsi_UploadIcons');

/* uplaod custom icon {change by monad}*/
function sfsi_UploadIcons()
{
	if ( !wp_verify_nonce( $_POST['nonce'], "UploadIcons")) {
		echo  json_encode(array('res'=>"error")); exit;
	}
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }

	// extract($_POST);
	$custom_imgurl = isset($_POST) && isset($_POST['custom_imgurl']) ? esc_url($_POST['custom_imgurl']):'';
	
	if(strpos($custom_imgurl, home_url()) === false){
		die(json_encode(array('res'=>'thumb_error')));
	}

	$upload_dir = wp_upload_dir();
	
	$ThumbSquareSize 		= 100; //Thumbnail will be 57X57
	$Quality 				= 90; //jpeg quality
	$DestinationDirectory   = $upload_dir['path'].'/'; //specify upload directory ends with / (slash)
	$AcceessUrl             = $upload_dir['url'].'/';
	$ThumbPrefix			= "cmicon_";
	
   if(!empty($custom_imgurl))
	{
		$sfsi_custom_files[] = $custom_imgurl;	
			
		list($CurWidth, $CurHeight) = getimagesize($custom_imgurl);
	
		$info = explode("/", $custom_imgurl);
		$iconName = array_pop($info);
		$ImageExt = substr($iconName, strrpos($iconName, '.'));
		$ImageExt = str_replace('.','',$ImageExt);
		
		$iconName = str_replace(' ','-',strtolower($iconName)); // get image name
		$ImageType = 'image/'.$ImageExt;
		
		 switch(strtolower($ImageType))
		 {
			 	case 'image/png':
						// Create a new image from file 
						$CreatedImage =  imagecreatefrompng($custom_imgurl);
						break;
				case 'image/gif':
						$CreatedImage =  imagecreatefromgif($custom_imgurl);
						break;
				case 'image/jpg':
						$CreatedImage = imagecreatefromjpeg($custom_imgurl);
						break;					
				case 'image/jpeg':
				case 'image/pjpeg':
						$CreatedImage = imagecreatefromjpeg($custom_imgurl);
						break;
				default:
						 die(json_encode(array('res'=>'type_error'))); //output error and exit
		}

		
		$ImageName = preg_replace("/\\.[^.\\s]{3,4}$/", "", $iconName);
		//$cnt=$i+1;
		
		$sec_options= (get_option('sfsi_section1_options',false)) ? unserialize(get_option('sfsi_section1_options',false)) : '' ;        
		$icons = (is_array(unserialize($sec_options['sfsi_custom_files']))) ? unserialize($sec_options['sfsi_custom_files']) : array();
		if(empty($icons))
		{   
			end($icons);
			$new=0;
		}    
		else {
			end($icons);
			$cnt=key($icons);
			$new=$cnt+1;
		}
		$NewIconName = "custom_icon".$new.'.'.$ImageExt;
        $iconPath 	= $DestinationDirectory.$NewIconName; //Thumbnail name with destination directory
		
		//Create a square Thumbnail right after, this time we are using cropImage() function
		if(cropImage($CurWidth,$CurHeight,$ThumbSquareSize,$iconPath,$CreatedImage,$Quality,$ImageType))
		{
			 //update database information 
				$AccressImagePath=$AcceessUrl.$NewIconName;                                        
					$sec_options= (get_option('sfsi_section1_options',false)) ? unserialize(get_option('sfsi_section1_options',false)) : '' ;
					$icons = (is_array(unserialize($sec_options['sfsi_custom_files']))) ? unserialize($sec_options['sfsi_custom_files']) : array();
					$icons[] = $AccressImagePath;
					
					$sec_options['sfsi_custom_files'] = serialize($icons);
					$total_uploads = ( isset($icons) && is_array($icons) )?count($icons):0; end($icons); $key = key($icons);
					update_option('sfsi_section1_options',serialize($sec_options));
					die(json_encode(array('res'=>'success','img_path'=>$AccressImagePath,'element'=>$total_uploads,'key'=>$key)));
	   }
	   else
	   {        
		   die(json_encode(array('res'=>'thumb_error')));
	   }
		
	}
}
/* delete uploaded icons */
add_action('wp_ajax_deleteIcons','sfsi_deleteIcons'); 

function sfsi_deleteIcons()
{
	if ( !wp_verify_nonce( $_POST['nonce'], "deleteIcons")) {
		echo  json_encode(array('res'=>"error")); exit;
	}
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }
	
   if(isset($_POST['icon_name']) && !empty($_POST['icon_name']))
   {
       /* get icons details to delete it from plugin folder */ 
       $custom_icon_name= sanitize_text_field($_POST['icon_name']);
       preg_match_all('/\d+/', $custom_icon_name, $custom_icon_numbers);
       $custom_icon_number =    count($custom_icon_numbers)>0?((is_array($custom_icon_numbers[0])&&count($custom_icon_numbers[0])>0)?$custom_icon_numbers[0][0]:0):0;
       $sec_options1	= (get_option('sfsi_section1_options',false)) ? unserialize(get_option('sfsi_section1_options',false)) : array() ;
       $sec_options2	= (get_option('sfsi_section2_options',false)) ? unserialize(get_option('sfsi_section2_options',false)) : array() ;
       $up_icons		= (is_array(unserialize($sec_options1['sfsi_custom_files']))) ? unserialize($sec_options1['sfsi_custom_files']) : array();
       $icons_links		= (is_array(unserialize($sec_options2['sfsi_CustomIcon_links']))) ? unserialize($sec_options2['sfsi_CustomIcon_links']) : array();
       $icon_url=$up_icons[$custom_icon_number];  
       $url_info=  pathinfo($icon_url);      
	   // Changes By {Monad}
	   /*if(is_file(SFSI_DOCROOT.'/images/custom_icons/'.$path['basename']))
	   {
		  
        	unlink(SFSI_DOCROOT.'/images/custom_icons/'.$path['basename']);
       }*/
	    $imgpath = parse_url($icon_url, PHP_URL_PATH);

		if(is_file($_SERVER['DOCUMENT_ROOT'] . $imgpath))
		{
		   unlink($_SERVER['DOCUMENT_ROOT'] . $imgpath);
		}
	   
		if(isset($up_icons[$custom_icon_number]))
		{
			 unset($up_icons[$custom_icon_number]);
			 unset($icons_links[$custom_icon_number]);
		}
		else
		{
		  	unset($up_icons[0]);
			unset($icons_links[0]);
		}        
        
		/* update database after delete */
	 	$sec_options1['sfsi_custom_files']=serialize($up_icons);
        $sec_options2['sfsi_CustomIcon_links']=serialize($icons_links);
         
        end($up_icons);
        $key=(key($up_icons))? key($up_icons) :$custom_icon_number;
        $total_uploads=(isset($up_icons) && is_array($up_icons))?count($up_icons):0;
         
        update_option('sfsi_section1_options',serialize($sec_options1));
        update_option('sfsi_section2_options',serialize($sec_options2));
          
       	die(json_encode(array('res'=>'success','last_index'=>$key,'total_up'=>$total_uploads)));
   } 
}

/*  This function will proportionally resize image */
function resizeImage($CurWidth,$CurHeight,$MaxSize,$DestFolder,$SrcImage,$Quality,$ImageType)
{
	/* Check Image size is not 0 */
	if($CurWidth <= 0 || $CurHeight <= 0) 
	{
		return false;
	}
	/* Construct a proportional size of new image */
	$ImageScale      	= min($MaxSize/$CurWidth, $MaxSize/$CurHeight); 
	$NewWidth  			= ceil($ImageScale*$CurWidth);
	$NewHeight 			= ceil($ImageScale*$CurHeight);
	$NewCanves 			= imagecreatetruecolor($NewWidth, $NewHeight);
	
	/* Resize Image */
	if(imagecopyresampled($NewCanves, $SrcImage,0, 0, 0, 0, $NewWidth, $NewHeight, $CurWidth, $CurHeight))
	{
		return $ImageType;
		switch(strtolower($ImageType))
		{
			case 'image/png':
				imagepng($NewCanves,$DestFolder);
				break;
			case 'image/gif':
				imagegif($NewCanves,$DestFolder);
				break;			
			case 'image/jpg':
				imagejpeg($NewCanves,$DestFolder,$Quality);
				break;
			case 'image/jpeg':
			case 'image/pjpeg':
				imagejpeg($NewCanves,$DestFolder,$Quality);
				break;
			default:
				return false;
		}
		/* Destroy image, frees memory	*/
		if(is_resource($NewCanves)) {imagedestroy($NewCanves);} 
		return true;
	}
}

/* This function corps image to create exact square images, no matter what its original size! */
function cropImage($CurWidth,$CurHeight,$iSize,$DestFolder,$SrcImage,$Quality,$ImageType)
{	 
	//Check Image size is not 0
	if($CurWidth <= 0 || $CurHeight <= 0) 
	{
		return false;
	}
	
	if($CurWidth>$CurHeight)
	{
		$y_offset = 0;
		$x_offset = ($CurWidth - $CurHeight) / 2;
		$square_size 	= $CurWidth - ($x_offset * 2);
	}else{
		$x_offset = 0;
		$y_offset = ($CurHeight - $CurWidth) / 2;
		$square_size = $CurHeight - ($y_offset * 2);
	}
	
	$NewCanves 	= imagecreatetruecolor($iSize, $iSize);
	imagealphablending($NewCanves, false);
	imagesavealpha($NewCanves,true);
	$white = imagecolorallocate($NewCanves, 255, 255, 255);
	$alpha_channel = imagecolorallocatealpha($NewCanves, 255, 255, 255, 127); 
	imagecolortransparent($NewCanves, $alpha_channel); 
	$maketransparent = imagecolortransparent($NewCanves,$white);
	imagefill($NewCanves, 0, 0, $maketransparent);
	
	/*
	 * Change offset for increase image quality ($x_offset, $y_offset)
	 * imagecopyresampled($NewCanves, $SrcImage,0, 0, $x_offset, $y_offset, $iSize, $iSize, $square_size, $square_size)
	 */
	if(imagecopyresampled($NewCanves, $SrcImage,0, 0, 0, 0, $iSize, $iSize, $square_size, $square_size))
	{
		imagesavealpha($NewCanves,true); 
		switch(strtolower($ImageType))
		{
			case 'image/png':
				imagepng($NewCanves,$DestFolder);
				break;
			case 'image/gif':
				imagegif($NewCanves,$DestFolder);
				break;	
			case 'image/jpg':
				imagejpeg($NewCanves,$DestFolder,$Quality);
				break;			
			case 'image/jpeg':
			case 'image/pjpeg':
				imagejpeg($NewCanves,$DestFolder,$Quality);
				break;
			default:
				return false;
		}
		
		/* Destroy image, frees memory	*/
		if(is_resource($NewCanves)) {imagedestroy($NewCanves);} 
		return true;
	}
	else
	{
		return false;
	}
}
?>