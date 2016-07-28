<?php
class wp_lightbox_2_general_settings_page{
	private $menu_name;
	private $databese_settings;
	public  $initial_values;
	
	
	function __construct($params){
		// set plugin url
		if(isset($params['plugin_url']))
			$this->plugin_url=$params['plugin_url'];
		else
			$this->plugin_url=trailingslashit(dirname(plugins_url('',__FILE__)));
		// set plugin path
		if(isset($params['plugin_path']))
			$this->plugin_path=$params['plugin_path'];
		else
			$this->plugin_path=trailingslashit(dirname(plugin_dir_path('',__FILE__)));
			
		$this->databese_settings=$params['databese_settings'];
	
		/*ajax parametrs*/
		add_action( 'wp_ajax_save_in_databese_lightbox2', array($this,'save_parametrs') );
	
	}
	public function save_parametrs(){
		 $initial_values= $this->databese_settings;
	$kk=1;	
		if(isset($_POST['wp_lightbox_2_general_settings_page']) && wp_verify_nonce( $_POST['wp_lightbox_2_general_settings_page'],'wp_lightbox_2_general_settings_page')){
			
			foreach($initial_values as $key => $value){
				if(isset($_POST[$key])){
					update_option($key,stripslashes($_POST[$key]));
				}
				else{
					$kk=0;
					printf('error saving %s <br>',$key);
				}
			}	
		}
		else{
			die('Authorization Problem ');
		}
		if($kk==0){
			exit;
		}
		die('sax_normala');
	}
	/*#################### CONTROLERRR ########################*/
	/*#################### CONTROLERRR ########################*/
	/*#################### CONTROLERRR ########################*/
	public function controller_page(){
		
			$this->display_table_list_answers();
	}
	

	private function display_table_list_answers(){
		
    $initial_values= $this->databese_settings;
    foreach($initial_values as $key => $value){
			$$key=$value;
	}
	?>
		
        <style>
		.popup_settings{
			<?php echo $youtube_plus_show_popup?'':'display:none;'; ?>
		}
        </style>
        <h2>Lightbox General Settings</h2>	
        <div class="main_yutube_plus_params">	
        <table class="wp-list-table widefat fixed posts wp_lightbox2_settings_table" style="width: 900px; min-width:320px !important;table-layout: fixed;">
            <thead>
                <tr>
                    <th width="50%">
                   		<span> Lightbox General Settings </span>
                    </th>                  
                    <th width="50%">
                    	&nbsp;
                   	</th>         
                </tr>
            </thead>
            <tbody>
                <tr class="parametr_chechbox">
                    <td>     
                   		Use Lightbox for all image links: <span title="Enable or disable the lightbox." class="desription_class">?</span>
                    </td>
                    <td>     
                    	<input type="checkbox" name="jqlb_automate_checkbox" id="jqlb_automate_checkbox" <?php checked($jqlb_automate,'1'); ?> value="1">
                    	<input type="hidden" name="jqlb_automate" id="jqlb_automate" value="<?php echo $jqlb_automate; ?>">
                    </td>
                </tr>
                <tr class="parametr_chechbox">
                    <td>     
                   		Enable lightbox in comments: <span title="This feature will enable lightbox for your comments. " class="desription_class">?</span>
                    </td>
                    <td>     
                    	<input type="checkbox" name="jqlb_comments_checkbox" id="jqlb_comments_checkbox" <?php checked($jqlb_comments,'1'); ?> value="1">
                    	<input type="hidden" name="jqlb_comments" id="jqlb_comments" value="<?php echo $jqlb_comments; ?>">
                    </td>
                </tr>
                <tr class="parametr_chechbox">
                    <td>     
                    	Show download link: <span title="You can display download link." class="desription_class">?</span>
                    </td>
                    <td>     
                    	<input type="checkbox" name="jqlb_show_download_checkbox" id="jqlb_show_download_checkbox" <?php checked($jqlb_show_download,'1'); ?> value="1">
                    	<input type="hidden" name="jqlb_show_download" id="jqlb_show_download" value="<?php echo $jqlb_show_download; ?>">
                    </td>
                </tr>
                <tr>
                    <td>     
                    	Overlay opacity: <span title="Set overlay opacity for lightbox." class="desription_class">?</span>
                    </td>
                    <td>     
						<input type="number" min="0" max="100" step="1" name="jqlb_overlay_opacity" id="jqlb_overlay_opacity" value="<?php echo $jqlb_overlay_opacity; ?>"><span class="befor_input_small_desc">%</span>
                    </td>
                </tr>
                <tr class="parametr_chechbox">
                    <td>     
                  	 	Show image info on top: <span title="Choose image info position." class="desription_class">?</span>
                    </td>
                    <td>     
                    	<input type="checkbox" name="jqlb_navbarOnTop_checkbox" id="jqlb_navbarOnTop_checkbox" <?php checked($jqlb_navbarOnTop,'1'); ?> value="1">
                    	<input type="hidden" name="jqlb_navbarOnTop" id="jqlb_navbarOnTop" value="<?php echo $jqlb_navbarOnTop; ?>">
                    </td>
                </tr>
                <tr class="parametr_chechbox">
                    <td>     
                   		Reduce large images to fit smaller screens: <span title="We recommend to enable this option, it will reduce large images to fit smaller screens." class="desription_class">?</span>
                    </td>
                    <td>     
                    	<input type="checkbox" name="jqlb_resize_on_demand_checkbox" id="jqlb_resize_on_demand_checkbox" <?php checked($jqlb_resize_on_demand,'1'); ?> value="1">
                    	<input type="hidden" name="jqlb_resize_on_demand" id="jqlb_resize_on_demand" value="<?php echo $jqlb_resize_on_demand; ?>">
                    </td>
                </tr> 
                <tr>
                    <td>     
                    	Minimum margin from top:  <span title="You can change image position from top." class="desription_class">?</span>
                    </td>
                    <td>     
                    	<input type="number" min="0" max="999" step="1" name="jqlb_margin_size" id="jqlb_margin_size" value="<?php echo $jqlb_margin_size; ?>"><span class="befor_input_small_desc">(default: 0)</span>
                    </td>
                </tr> 
                <tr>
                    <td>     
                   		Lightbox Animation duration: <span title="Type here animation duration for lightbox." class="desription_class">?</span>
                    </td>
                    <td>     
                    	<input type="number" min="0" max="9999999" step="1" name="jqlb_resize_speed" id="jqlb_resize_speed" value="<?php echo $jqlb_resize_speed; ?>"><span class="befor_input_small_desc">(milliseconds)</span>
                    </td>
                </tr> 
                <tr>
                    <td>     
                    	Additional text below image info:  <span title="Type here text, and it will appear below images. " class="desription_class">?</span>
                    </td>
                    <td>     
                    	<input type="text" name="jqlb_help_text" id="jqlb_help_text" value="<?php echo $jqlb_help_text; ?>"><span class="befor_input_small_desc">(default: none)</span>
                    </td>
                </tr>   
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" width="100%"><button type="button" id="save_button_general" class="save_button button button-primary"><span class="save_button_span">Save Settings</span> <span class="saving_in_progress"> </span><span class="sucsses_save"> </span><span class="error_in_saving"> </span></button></th>
                </tr>
            </tfoot>
		</table>
		<ol> 
	<li>You can use WordPress image galleries and have them grouped and auto-lightboxed: <a href="http://codex.wordpress.org/Gallery_Shortcode"><code>[gallery link="file"]</code></a></li> 	
	<li>You can also add a <code>rel="lightbox"</code> attribute to any link tag to activate the lightbox. For example:
	<pre><code>	&lt;a href=&quot;images/image-1.jpg&quot; rel=&quot;lightbox&quot; title=&quot;my caption&quot;&gt;image #1&lt;/a&gt;</code></pre> 
	<em>Optional:</em> Use the <code>title</code> attribute if you want to show a caption.
	</li> 
	<li>If you have a set of related images that you would like to group, simply include a group name in the rel attribute. For example:
	<pre><code>	&lt;a href=&quot;images/image-1.jpg&quot; rel=&quot;lightbox[roadtrip]&quot;&gt;image #1&lt;/a&gt;
	&lt;a href=&quot;images/image-2.jpg&quot; rel=&quot;lightbox[roadtrip]&quot;&gt;image #2&lt;/a&gt;
	&lt;a href=&quot;images/image-3.jpg&quot; rel=&quot;lightbox[roadtrip]&quot;&gt;image #3&lt;/a&gt;</code></pre> 
	No limits to the number of image sets per page or how many images are allowed in each set. Go nuts!</li> 
	<li>To <strong>disable</strong> lightboxing of an image link, just set any other rel-attribute: <code>rel="nobox"</code></li>
	</ol>
	
         <?php wp_nonce_field('wp_lightbox_2_general_settings_page','wp_lightbox_2_general_settings_page'); ?>
	</div><br /><br /><span class="error_massage"></span>
   
		<script>
		
		
		
		jQuery(document).ready(function(e) {		

			 jQuery('#save_button_general').click(function(){
					
					jQuery('#save_button_general').addClass('padding_loading');
					jQuery("#save_button_general").prop('disabled', true);
					jQuery('.saving_in_progress').css('display','inline-block');
					generete_checkbox('parametr_chechbox');					
					
					jQuery.ajax({
						type:'POST',
						url: "<?php echo admin_url( 'admin-ajax.php?action=save_in_databese_lightbox2' ); ?>",
						data: {wp_lightbox_2_general_settings_page:jQuery('#wp_lightbox_2_general_settings_page').val()<?php foreach($initial_values as $key => $value){echo ','.$key.':jQuery("#'.$key.'").val()';} ?>},
					}).done(function(date) {
						if(date=='sax_normala'){
							console.log
						jQuery('.saving_in_progress').css('display','none');
						jQuery('.sucsses_save').css('display','inline-block');
						setTimeout(function(){jQuery('.sucsses_save').css('display','none');jQuery('#save_button_general').removeClass('padding_loading');jQuery("#save_button_general").prop('disabled', false);},2500);
						}else{
							jQuery('.saving_in_progress').css('display','none');
							jQuery('.error_in_saving').css('display','inline-block');
							jQuery('.error_massage').css('display','inline-block');
							jQuery('.error_massage').html(date);
							setTimeout(function(){jQuery('#save_button_general').removeClass('padding_loading');jQuery("#save_button_general").prop('disabled', false);},5000);
						}

					});
				});
				function generete_radio_input(radio_class){
					jQuery('.'+radio_class).each(function(index, element) {
                       jQuery(this).find('input[type=hidden]').val(jQuery(this).find('input[type=radio]:checked').val())
                    });
				}
				function generete_checkbox(checkbox_class){
					jQuery('.'+checkbox_class).each(function(index, element) {
						if(jQuery(this).find('input[type=checkbox]').prop('checked'))
                        	jQuery(this).find('input[type=hidden]').val(jQuery(this).find('input[type=checkbox]:checked').val());
						else
							jQuery(this).find('input[type=hidden]').val(0);
                    });
				}

		});
			
        </script>

		<?php
	}	
	
}


 ?>