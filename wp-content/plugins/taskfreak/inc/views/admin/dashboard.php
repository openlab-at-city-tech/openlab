<div class="wrap">
	<img src="<?php echo plugins_url('/img/logo.32.png', TFK_ROOT_FILE); ?>" class="icon32" style="background:none"/>
	<h2><?php _e('Dashboard', 'taskfreak'); ?></h2>
	
	<div id="welcome-panel" class="welcome-panel">
		<div class="welcome-panel-content">
			<h3>TaskFreak! Wordpress Free Plugin version <?php echo $this->options['version']; ?></h3>
			<p class="about-description">
				<?php printf(__( 'Learn more about this plugin on the official <a href="%1$s" target="_blank">TaskFreak! website</a>.', 'taskfreak' ), 'http://www.taskfreak.com'); ?>
			</p>
			<div class="welcome-panel-column-container">
				<div class="welcome-panel-column tfk-dash-col1">
					<?php
					if ($this->options['page_id']) {
					?>
					<a class="button button-primary button-hero" href="<?php echo $this->linktsk; ?>"><?php _e('Start working!', 'taskfreak'); ?></a>
					<ul>
						<li><a href="<?php echo $this->linkupd; ?>" class="welcome-icon welcome-view-site"><?php _e('Recent updates', 'taskfreak'); ?></a></li>
						<?php
						if (tfk_user::check_role('editor')) {
						?>
						<li><a href="<?php echo $this->linkprj; ?>" class="welcome-icon welcome-widgets-menus"><?php _e('Manage projects', 'taskfreak'); ?></a></li>
						<?php
						}
						?>
						<li><a href="http://www.taskfreak.com" class="welcome-icon welcome-learn-more" target="_blank"><?php _e('Learn more about TFWP', 'taskfreak'); ?></a></li>
					</ul>
					<?php
					} else {
					?>
					<p><?php printf(__('To start using TaskFreak! plugin for Wordpress, you first need to add the shortcode <code>[tfk_all]</code> in one of your <a href="%s">pages</a>.','taskfreak'), 'edit.php?post_type=page'); ?></p>
					<p><?php printf(__('You can also start by <a href="%s">creating projects</a>', 'taskfreak'), $this->linkprj); ?></p>
					<?php
					}
					?>
				</div>
				<div class="welcome-panel-column tfk-dash-col2">
					<table class="tfk_stats">
						<thead>
							<tr>
								<td></td>
								<th><?php _ex('In Progress', 'many projects', 'taskfreak'); ?></th>
								<th><?php _ex('Suspended', 'many projects', 'taskfreak'); ?></th>
								<th><?php _ex('Closed', 'many projects', 'taskfreak'); ?></th>
						</thead>
						<tbody>
							<tr>
								<th><?php _e('My Tasks', 'taskfreak'); ?></th>
								<td><?php echo $this->tskusr[20]; ?></td>
								<td><?php echo $this->tskusr[30]; ?></td>
								<td><?php echo $this->tskusr[60]; ?></td>
							</tr>
							<tr>
								<th><?php _e('All Tasks', 'taskfreak'); ?></th>
								<td><?php echo $this->tskall[20]; ?></td>
								<td><?php echo $this->tskall[30]; ?></td>
								<td><?php echo $this->tskall[60]; ?></td>
							</tr>
							<tr class="sep">
								<th><?php _e('My Projects', 'taskfreak'); ?></th>
								<td><?php echo $this->prjusr[20]; ?></td>
								<td><?php echo $this->prjusr[30]; ?></td>
								<td><?php echo $this->prjusr[60]; ?></td>
							</tr>
							<tr>
								<th><?php _e('All Projects', 'taskfreak'); ?></th>
								<td><?php echo $this->prjall[20]; ?></td>
								<td><?php echo $this->prjall[30]; ?></td>
								<td><?php echo $this->prjall[60]; ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<p class="about-description">
				<?php printf(__('Please consider <a href="%s" target="_blank">making a donation</a> if you enjoy using TFWP plugin.', 'taskfreak'), 'http://www.taskfreak.com'); ?>
			</p>
		</div>
	</div>
	
</div>