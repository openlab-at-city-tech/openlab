<?php
    $sfsi_show_via_afterposts = "no";
    if(isset($option9['sfsi_show_via_afterposts']) && !empty($option9['sfsi_show_via_afterposts'])){
        $sfsi_show_via_afterposts = $option9['sfsi_show_via_afterposts'];
    }
    $label_style = 'style="display:none;"';
    $checked     = "";
    if($sfsi_show_via_afterposts =='yes'){          
        $label_style = 'style="display:block;"';
        $checked     = 'checked="true"';
    }    
?>
		<li class="sfsi_show_via_afterposts">
			<div class="radio_section tb_4_ck" onclick="checkforinfoslction_checkbox(this);"><input name="sfsi_show_via_afterposts" <?php echo $checked; ?>  type="checkbox" value="<?php echo $sfsi_show_via_afterposts; ?>" class="styled"  /></div>
			<div class="sfsi_right_info">
                <p>
					<span class="sfsi_toglepstpgspn">Show icons after posts</span>
                </p>
                <div class="kckslctn" <?php echo $label_style; ?>>
                    <?php include(SFSI_DOCROOT.'/views/sfsi_option_view6.php'); ?>
                </div>
			</div>
		</li>