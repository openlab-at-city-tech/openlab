<?php
	$this->view_inc('front/nav.php');
?>
<ul id="tfk_subfilters">
<?php echo tfk_log::list_links($_SERVER['REQUEST_URI'], 'filter_recent', $this->filters['filter_recent']); ?>
</ul>
<div id="tfk_updates">
<?php $current_date = '' ?>
<?php if ($this->log->count()): ?>
	<?php while($this->log->next()): ?>
		<?php if ($current_date != substr($this->log->get('log_date'),0,10)): ?>
			<?php if ($current_date): ?>
				</ul>
			<?php endif; ?>
			<?php $current_date = substr($this->log->get('log_date'),0,10) ?>
			<h4><?php echo $current_date ?></h4>
			<ul>
		<?php endif; ?>
		<li class="tfk_<?php 
			if ($this->log->get('comment_id')) {
				echo "com";
			} elseif ($this->log->get('info') == 'creation') {
				echo "cre";
			} elseif ($this->log->get('action_code') != '') {
				echo "set";
			} else {
				echo "mod";
			}
			?>"><span class="tfk_user">
			<?php echo ($this->log->get('user')->get('display_name') ? $this->log->get('user')->get('display_name') : __('[deleted user]', 'taskfreak')).' ' ?>
			</span>
			<?php
			if ($this->log->get('type') == "task") {
				$task_link = esc_url(remove_query_arg('mode', add_query_arg('view', $this->log->get('item_id'), $this->baselink)));
				if ($this->log->get('comment_id'))
					echo __('commented task', 'taskfreak').' <a href="'.$task_link.'#tfk_comment_'.$this->log->get('comment_id').'">'.$this->log->get('title_or_name').'</a>';
				elseif ($this->log->get('info') == 'creation')
					echo __('created task', 'taskfreak').' <a href="'.$task_link.'">'.$this->log->get('title_or_name').'</a>';
				elseif ($this->log->get('action_code') != '')
					echo __('set task', 'taskfreak').' <a href="'.$task_link.'">'.$this->log->get('title_or_name').'</a> '.__('to', 'taskfreak').' '._x($this->log->get_status(), 'one task', 'taskfreak');
				else
					echo __('modified task', 'taskfreak').' <a href="'.$task_link.'">'.$this->log->get('title_or_name').'</a> <small>('.$this->log->get_info().')</small>';
			} else {
				$project_link = esc_url(remove_query_arg('mode', add_query_arg('proj', $this->log->get('project_id'), $this->baselink)));
				if ($this->log->get('info') == 'creation')
					echo __('created project', 'taskfreak').' <a href="'.$project_link.'">'.$this->log->get('title_or_name').'</a>';
				elseif ($this->log->get('action_code') != '')
					echo __('set project', 'taskfreak').' <a href="'.$project_link.'">'.$this->log->get('title_or_name').'</a> '.__('to', 'taskfreak').' '._x($this->log->get_status(), 'one project', 'taskfreak');
				else
					echo __('modified project', 'taskfreak').' <a href="'.$project_link.'">'.$this->log->get('title_or_name').'</a>';
			}
			?>
		</li>
	<?php endwhile; ?>
<?php else: ?>
	<?php _e('Nothing, for the moment', 'taskfreak') ?>
<?php endif; ?>
</div><!-- .tfk_updates -->
