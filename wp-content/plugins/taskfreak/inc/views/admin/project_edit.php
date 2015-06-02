<div class="wrap">
	<img src="<?php echo plugins_url('/img/logo.32.png', TFK_ROOT_FILE); ?>" class="icon32" style="background:none"/>
	<h2>
		<?php echo ($this->pid)?__('Edit Project','taskfreak'):__('New Project','taskfreak'); ?>
	</h2>
	<?php
	if ($this->saveok) {
		?>
		<div id="message" class="updated"><p><?php _e('Project saved successfully','taskfreak'); ?></p></div>
		<?php
	} else if ($this->saverror) {
		?>
		<div id="message" class="error"><p><?php _e('Error saving project','taskfreak'); ?></p></div>
		<?php
	}
	?>
	<form name="my_form" action="<?php echo $this->baselink; ?>" method="post">
        <input type="hidden" name="action" value="save" />
        <?php
        wp_nonce_field('tfk_project_save');
		?>
        <div id="poststuff">
 
            <div id="post-body" class="metabox-holder columns-<?php 
            	$wp_screen = get_current_screen();
            	// get_columns requires WP 3.4+
            	echo (method_exists($wp_screen, 'get_columns') && $wp_screen->get_columns() == 1 ? '1' : '2'); 
            	?>">
                <div id="post-body-content">
                	<div id="titlediv">
	                	<div id="titlewrap">
							<label class="screen-reader-text" id="title-prompt-text" for="title"><?php echo __( 'Enter title here' , 'taskfreak'); ?></label>
							<input id="title" type="text" name="name" size="30" value="<?php echo $this->data->value('name'); ?>" autocomplete="off" />
						</div>
                	</div>
                	<div id="descriptiondiv">
	                    <?php
	                    	wp_editor($this->data->value('description'), 'description', array(
	                    		'media_buttons'=> false, // no media button
	                    		'wpautop'	=> false, // no <p>
	                    		'teeny'	=> true, // minimal editor
	                    		'dfw' => false,
	                    		// 'tabfocus_elements' => 'sample-permalink,post-preview',
	                    		'editor_height' => 360
	                    	));
	                    ?>
                	</div>
                </div>
 
                <div id="postbox-container-1" class="postbox-container">
                	<div id="submitdiv" class="postbox">
	                	<div class="handlediv" title="Click to toggle"><br /></div>
	                	<h3 class="hndle"><span><?php _e('Project Status','taskfreak'); ?></span></h3>
	                	<div class="inside">
		                	<div class="submitbox" id="submitpost">
		                		<div id="project_status" class="tzn_option_section">
		                			<?php
		                			$objstatus = $this->data->get_status();
			                		$statuskey = $objstatus->get('action_code');
			                		?>
		                			<label for="project_status_new"><?php _e('Status:', 'taskfreak') ?></label>
			                		<span class="tzn_option_display"><?php
			                			_ex($objstatus->get_status(), 'one project', 'taskfreak');
			                		?></span>
			                		<a href="javascript:{}" class="tzn_option_toggle hide-if-no-js"><?php _e('Edit', 'taskfreak') ?></a>
			                		<div id="project_status_select" class="tzn_option_panel hide-if-js">
				                		<input type="hidden" name="project_status_old" class="tzn_option_old" value="<?php echo $statuskey; ?>" />
				                		<?php
				                			echo tfk_project_status::list_select('project_status_new', $statuskey);
				                		?>
				                		<a href="javascript:{}" class="tzn_option_save hide-if-no-js button"><?php _e('OK', 'taskfreak'); ?></a>
				                		<a href="javascript:{}" class="tzn_option_cancel hide-if-no-js"><?php _e('Cancel', 'taskfreak'); ?></a>
				                	</div>
				                </div>
				                <div id="project_rights" class="tzn_option_section">
		                			<label for="project_rights_new"><?php _e('Access :','taskfreak') ?></label>
			                		<span class="tzn_option_display"><?php
			                			echo $this->data->get_visibility();
			                		?></span>
			                		<a href="javascript:{}" class="tzn_option_toggle hide-if-no-js"><?php _e('Edit', 'taskfreak') ?></a>
			                		<div id="project_status_select" class="tzn_option_panel hide-if-js">
				                		<ul>
				                			<li style="line-height:1.75em">
				                				<strong><?php _e('Who can access this project', 'taskfreak'); ?></strong><br />
				                				<select name="who_read" class="tzn_option_select">
				                					<?php echo wp_dropdown_roles($this->data->get('who_read')); ?>
				                					<option<?php selected($this->data->get('who_read'),''); ?> value=""><?php _e('Any visitor','taskfreak'); ?></option>
				                				</select>
				                			</li>
				                			<li style="line-height:1.75em">
				                				<strong><?php _e('Who can comment tasks', 'taskfreak'); ?></strong><br />
				                				<select name="who_comment"><?php echo wp_dropdown_roles($this->data->get('who_comment')); ?></select>
				                			</li>
				                			<li style="line-height:1.75em">
				                				<strong><?php _e('Who can create tasks', 'taskfreak'); ?></strong><br />
				                				<select name="who_post"><?php echo wp_dropdown_roles($this->data->get('who_post')); ?></select>
				                			</li>
				                			<li style="line-height:1.75em">
				                				<strong><?php _e('Who can moderate the project', 'taskfreak'); ?></strong><br />
				                				<select name="who_manage"><?php echo wp_dropdown_roles($this->data->get('who_manage')); ?></select>
				                			</li>
				                		</ul>
				                		<a href="javascript:{}" class="tzn_option_save hide-if-no-js button"><?php _e('OK', 'taskfreak'); ?></a>
				                		<a href="javascript:{}" class="tzn_option_cancel hide-if-no-js"><?php _e('Cancel', 'taskfreak'); ?></a>
				                	</div>
				                </div>
								<div class="clear"></div>
								<div id="major-publishing-actions">
									<div id="delete-action">
										<a class="submitdelete deletion" href="<?php 
											echo wp_nonce_url(add_query_arg(array('action'=>'trash','noheader'=>'true'), $this->baselink),'tfk_project_trash'); 
										?>"><?php _e('Move to Trash', 'taskfreak'); ?></a>
									</div>
									<div id="publishing-action">
										<span class="spinner" style="display: none; "></span>
										<input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php _e('Update', 'taskfreak'); ?>" />
									</div>
									<div class="clear"></div>
								</div>
		                	</div>
						</div>
					</div>
					<?php
					
					// --- STATUS HISTORY --------------------------------
					
					if ($this->pid) {
					?>
					
					<div id="statusdiv" class="postbox">
	                	<div class="handlediv" title="<?php _e('Click to toggle', 'taskfreak'); ?>"><br /></div>
	                	<h3 class="hndle"><span><?php _e('Project History', 'taskfreak'); ?></span></h3>
	                	<div class="inside">
	                		<?php
	                		if ($this->status->count()) {
		                		while ($this->status->next()) {
			                		echo '<p><strong>'._x($this->status->get_status(), 'one project', 'taskfreak').'</strong><br />'
			                			.$this->status->html('log_date')
			                			.' <em>'.__(' by ', 'taskfreak').'</em> '.$this->status->get('user')->get('display_name').'</p>';
		                		}
	                		} else {
	                		?>
	                		<p>
	                			<strong><?php _e('New project', 'taskfreak'); ?></strong>
	                		</p>
	                		<?php
	                		}
	                		?>
	                	</div>
                	</div>
                	<?php
                	
                	} // --- end status history ---
                	
                	?>
				</div>
				
            </div>
 
        </div>
    </form>
</div>