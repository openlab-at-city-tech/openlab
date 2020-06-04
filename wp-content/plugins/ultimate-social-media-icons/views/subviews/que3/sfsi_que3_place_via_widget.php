	<?php 

		$sfsi_show_via_widget = "no";

		if(isset($option9['sfsi_show_via_widget']) && !empty($option9['sfsi_show_via_widget'])){
			$sfsi_show_via_widget = $option9['sfsi_show_via_widget'];
		}
 
		$label_style 	 = 'style="display:none;font-size: 16px;"';
		$checked 	 	 = '';

		if($sfsi_show_via_widget =='yes'){			
			$label_style = 'style="display:block;font-size: 16px;"';
			$checked     = 'checked="true"';
		}

	?>
    	
		<li class="sfsi_show_via_widget_li">
			
			<div class="radio_section tb_4_ck" onclick="checkforinfoslction(this);">
				
				<input name="sfsi_show_via_widget" <?php echo $checked ;?>  id="sfsi_show_via_widget_li" type="checkbox" value="<?php echo $sfsi_show_via_widget; ?>" class="styled"  />

			</div>
			
			<div class="sfsi_right_info">
				<p>
					<span class="sfsi_toglepstpgspn">Show them via a widget</span><br>
                    
					<label  <?php echo $label_style; ?> class="sfsiplus_sub-subtitle ckckslctn">Go to the <a href="<?php echo admin_url('widgets.php');?>">widget area</a> and drag & drop it where you want to show them!
                    	
                    </label>
				</p>

			</div>

		</li>