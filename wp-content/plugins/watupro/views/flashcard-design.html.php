<div class="wrap">
	<h1><?php printf(__('Configure design for flashcard %s', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL);?></h1>
	
	<form method="post" class="watupro">
		<div class="postarea watupro">
			<p><label><?php _e('Card width (px):', 'watupro');?></label> <input type="text" name="width" value="<?php echo @$design_settings['width']?>" size="6"></p>
			<p><label><?php _e('Card height (px):', 'watupro');?></label> <input type="text" name="height" value="<?php echo @$design_settings['height']?>" size="6"></p>
			<p><label><?php _e('Border radius (%):', 'watupro');?></label> <input type="text" name="border_radius" value="<?php echo @$design_settings['border_radius']?>" size="6"></p>
			<p><label><?php _e('Front color:', 'watupro');?></label> <input type="color" name="color" value="<?php echo $design_settings['color']?>" size="6"></p>
			<p><label><?php _e('Front text color:', 'watupro');?></label> <input type="color" name="text_color" value="<?php echo @$design_settings['text_color']?>" size="6"></p>
			<p><label><?php _e('Text size (px):', 'watupro');?></label> <input type="text" name="text_size" value="<?php echo @$design_settings['text_size']?>" size="6"></p>
			
			<p><label><?php _e('Back color:', 'watupro');?></label> <input type="color" name="back_color" value="<?php echo @$design_settings['back_color']?>" size="6"></p>
			<p><label><?php _e('Back text color:', 'watupro');?></label> <input type="color" name="back_text_color" value="<?php echo @$design_settings['back_text_color']?>" size="6"></p>
			<p><label><?php _e('Back text size (px):', 'watupro');?></label> <input type="text" name="back_text_size" value="<?php echo @$design_settings['back_text_size']?>" size="6"></p>
			<p><?php _e('Separator between both sides of the flashcard (if you will have images or other HTML choose == or |||)', 'watupro');?> <select name="flashcard_separator">
				<option value="=">=</option>
				<option value="=="<?php if(!empty($design_settings['flashcard_separator']) and $design_settings['flashcard_separator'] == '==') echo 'selected';?>>==</option>
				<option value="|||"<?php if(!empty($design_settings['flashcard_separator']) and $design_settings['flashcard_separator'] == '|||') echo 'selected';?>>|||</option>
			</select></p>
			
			<p><input type="submit" value="<?php _e('Save Settings', 'watupro');?>" class="button button-primary"></p>
			<input type="hidden" name="ok" value="1">
			<?php wp_nonce_field('watupro_flashcards');?>
		</div>	
	</form>
</div>