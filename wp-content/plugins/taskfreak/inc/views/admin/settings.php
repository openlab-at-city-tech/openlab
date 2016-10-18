<div class="wrap">
	<img src="<?php echo plugins_url('/img/logo.32.png', TFK_ROOT_FILE); ?>" class="icon32" style="background:none"/>
	<h2><?php _e('Settings & Options', 'taskfreak') ?></h2>
	<?php
	if ($this->message) {
		?>
		<div id="message" class="updated"><p><?php echo $this->message; ?></p></div>
		<?php
	}
	?>
	<form name="my_form" action="<?php echo $this->baselink; ?>" method="post">
		<?php
        wp_nonce_field('tfk_settings');
		?>
		<h3><?php _e('Setup', 'taskfreak') ?></h3>
		<table class="form-table">
		<tr>
			<th><label><?php _e('Default [tfk_all] view', 'taskfreak') ?></label></th>
			<td>
				<select name="tfk_all">
					<option value="projects" <?php selected( $this->options['tfk_all'], 'projects'); ?>><?php _e('List of projects','taskfreak'); ?></option>
					<option value="tasks" <?php selected( $this->options['tfk_all'], 'tasks'); ?>><?php _e('Tasks (to do)','taskfreak'); ?></option>
					<option value="recent" <?php selected( $this->options['tfk_all'], 'recent'); ?>><?php _e('Recent updates','taskfreak'); ?></option>
				</select><br />
				<small><?php _e('What shortcut [tfk_all] shows by default', 'taskfreak') ?></small>
			</td>
		</tr>
		</table>
		<h3><?php _e('Display options', 'taskfreak') ?></h3>
		<table class="form-table">
		<tr>
			<th><label><?php _e('Number of tasks per page', 'taskfreak') ?></label></th>
			<td>
				<select name="tasks_per_page">
					<option value="5" <?php selected( $this->options['tasks_per_page'], '5'); ?>>5</option>
					<option value="10" <?php selected( $this->options['tasks_per_page'], '10'); ?>>10</option>
					<option value="20" <?php selected( $this->options['tasks_per_page'], '20'); ?>>20</option>
					<option value="50" <?php selected( $this->options['tasks_per_page'], '50'); ?>>50</option>
					<option value="100" <?php selected( $this->options['tasks_per_page'], '100'); ?>>100</option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label><?php _e('Date format', 'taskfreak') ?></label></th>
			<td>
				<select name="format_date">
					<option value="m/d/Y" <?php selected( $this->options['format_date'], 'm/d/Y'); ?>><?php _e('American (mm/dd/yyyy)', 'taskfreak') ?></option>
					<option value="d/m/Y" <?php selected( $this->options['format_date'], 'd/m/Y'); ?>><?php _e('European (dd/mm/yyyy)', 'taskfreak') ?></option>
					<option value="Y-m-d" <?php selected( $this->options['format_date'], 'Y-m-d'); ?>><?php _e('Electronic (yyyy-mm-dd)', 'taskfreak') ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label><?php _e('Time format', 'taskfreak') ?></label></th>
			<td>
				<select name="format_time">
					<option value="H:i" <?php selected( $this->options['format_date'], 'H:i'); ?>><?php _e('24 hours', 'taskfreak') ?> (23:45)</option>
					<option value="h.i a" <?php selected( $this->options['format_date'], 'h.i a'); ?>><?php _e('12 hours', 'taskfreak') ?> (11.45 pm)</option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label><?php _e('Proximity bar', 'taskfreak') ?></label></th>
			<td>
				<input type="radio" name="proximity" id="proximity1" value="1" <?php checked( $this->options['proximity'], 1); ?> />
				<label for="proximity1"><?php _e('Visible', 'taskfreak'); ?></label>
				&nbsp;
				<input type="radio" name="proximity" id="proximity0" value="0" <?php checked( $this->options['proximity'], 0); ?> />
				<label for="proximity0"><?php _e('Hidden', 'taskfreak'); ?></label>
			</td>
		</tr>
		<tr>
			<th><label><?php _e('Size task in list according to priority', 'taskfreak') ?></label></th>
			<td>
				<input type="radio" name="prio_size" id="prio_size1" value="1" <?php checked( $this->options['prio_size'], 1); ?> />
				<label for="prio_size1"><?php _e('Yes', 'taskfreak'); ?></label>
				&nbsp;
				<input type="radio" name="prio_size" id="prio_size0" value="0" <?php checked( $this->options['prio_size'], 0); ?> />
				<label for="prio_size0"><?php _e('No', 'taskfreak'); ?></label>
			</td>
		</tr>
		<tr>
			<th><label><?php _e('Updates page shows', 'taskfreak') ?></label></th>
			<td>
				<select name="number_updates">
					<?php for ($i = 1; $i <= 10 ; $i++): ?>
					<option value="<?php echo $i ?>" <?php selected( $this->options['number_updates'], $i); ?>><?php echo $i ?></option>
					<?php endfor; ?>
					<option value="15" <?php selected( $this->options['number_updates'], 15); ?>>15</option>
					<option value="20" <?php selected( $this->options['number_updates'], 20); ?>>20</option>
					<option value="30" <?php selected( $this->options['number_updates'], 30); ?>>30</option>
					<option value="60" <?php selected( $this->options['number_updates'], 60); ?>>60</option>
					<option value="90" <?php selected( $this->options['number_updates'], 90); ?>>90</option>
					<option value="180" <?php selected( $this->options['number_updates'], 180); ?>>180</option>
					<option value="999999999" <?php selected( $this->options['number_updates'], 999999999); ?>><?php _e('No limit', 'taskfreak') ?></option>
				</select>
				<?php _e('day(s)', 'taskfreak') ?>
			</td>
		</tr>
		<tr>
			<th><label><?php _e('Avatar size', 'taskfreak') ?></label></th>
			<td>
				<select name="avatar_size">
					<option value="32" <?php selected( $this->options['avatar_size'], 32); ?>><?php _e('Small', 'taskfreak') ?></option>
					<option value="48" <?php selected( $this->options['avatar_size'], 48); ?>><?php _e('Medium', 'taskfreak') ?></option>
					<option value="64" <?php selected( $this->options['avatar_size'], 64); ?>><?php _e('Large', 'taskfreak') ?></option>
				</select>
			</td>
		</tr>
		</table>
		<h3><?php _e('Default project access', 'taskfreak') ?></h3>
		<table class="form-table">
		<tr>
			<th><label><?php _e('Who can access the project', 'taskfreak') ?></label></th>
			<td><select name="access_read" class="tzn_option_select">
				<?php echo wp_dropdown_roles($this->options['access_read']); ?>
				<option<?php selected($this->options['access_read'],''); ?> value=""><?php _e('Any visitor','taskfreak'); ?></option>
			</select></td>
		</tr>
		<tr>
			<th><label><?php _e('Who can comment tasks', 'taskfreak') ?></label></th>
			<td><select name="access_comment" class="tzn_option_select">
				<?php echo wp_dropdown_roles($this->options['access_comment']); ?>
			</select></td>
		</tr>
		<tr>
			<th><label><?php _e('Who can create tasks', 'taskfreak') ?></label></th>
			<td><select name="access_post" class="tzn_option_select">
				<?php echo wp_dropdown_roles($this->options['access_post']); ?>
			</select></td>
		</tr>
		<tr>
			<th><label><?php _e('Who can moderate the project', 'taskfreak') ?></label></th>
			<td><select name="access_manage" class="tzn_option_select">
				<?php echo wp_dropdown_roles($this->options['access_manage']); ?>
			</select></td>
		</tr>
		</table>
		<h3><?php _e('Security options', 'taskfreak') ?></h3>
		<table class="form-table">
		<tr>
			<th><label><?php _e('Allow uploads in task comments', 'taskfreak') ?></label></th>
			<td>
				<input type="radio" name="comment_upload" id="comment_upload1" value="1" <?php checked( $this->options['comment_upload'], 1); ?> />
				<label for="comment_upload1"><?php _e('Yes', 'taskfreak'); ?></label>
				&nbsp;
				<input type="radio" name="comment_upload" id="comment_upload0" value="0" <?php checked( $this->options['comment_upload'], 0); ?> />
				<label for="comment_upload0"><?php _e('No', 'taskfreak'); ?></label>
			</td>
		</tr>
		</table>
		
		<p class="submit"><input type="submit" value="<?php _e('Save Changes', 'taskfreak') ?>" name="opt_save" class="button button-primary" /></p>
	</form>	
</div>
