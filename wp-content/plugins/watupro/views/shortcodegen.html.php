<div class="wrap watupro-wrap">
	<h1><?php _e('Watu PRO Shortcode Generator', 'watupro')?></h1>
	<h2 class="nav-tab-wrapper">
		<a class='nav-tab <?php if($tab == 'quiz'):?>nav-tab-active<?php endif;?>' href='admin.php?page=watupro_shortcode_generator&tab=quiz'><?php printf(__('Single %s', 'watupro'), WATUPRO_QUIZ_WORD);?></a>
		<a class='nav-tab <?php if($tab == 'list'):?>nav-tab-active<?php endif;?>' href='admin.php?page=watupro_shortcode_generator&tab=list'><?php printf(__('List %s', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL);?></a>
		<a class='nav-tab <?php if($tab == 'myexams'):?>nav-tab-active<?php endif;?>' href='admin.php?page=watupro_shortcode_generator&tab=myexams'><?php printf(__('My %s page', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL);?></a>
		<a class='nav-tab <?php if($tab == 'leaderboard'):?>nav-tab-active<?php endif;?>' href='admin.php?page=watupro_shortcode_generator&tab=leaderboard'><?php _e('Basic leaderboard', 'watupro')?></a>
		<a class='nav-tab <?php if($tab == 'result'):?>nav-tab-active<?php endif;?>' href='admin.php?page=watupro_shortcode_generator&tab=result'><?php printf(__('%s result', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD));?></a>
		<a class='nav-tab <?php if($tab == 'chart'):?>nav-tab-active<?php endif;?>' href='admin.php?page=watupro_shortcode_generator&tab=chart'><?php _e('Basic chart', 'watupro');?></a>
		<a class='nav-tab <?php if($tab == 'attempts'):?>nav-tab-active<?php endif;?>' href='admin.php?page=watupro_shortcode_generator&tab=attempts'><?php _e('No. attempts', 'watupro');?></a>
		<a class='nav-tab <?php if($tab == 'users_completed'):?>nav-tab-active<?php endif;?>' href='admin.php?page=watupro_shortcode_generator&tab=users_completed'><?php _e('Users completed', 'watupro');?></a>
		<a class='nav-tab <?php if($tab == 'retake'):?>nav-tab-active<?php endif;?>' href='admin.php?page=watupro_shortcode_generator&tab=retake'><?php _e('Retake button', 'watupro');?></a>
		<a class='nav-tab <?php if($tab == 'segment'):?>nav-tab-active<?php endif;?>' href='admin.php?page=watupro_shortcode_generator&tab=segment'><?php _e('Segment stats', 'watupro');?></a>
		<a class='nav-tab <?php if($tab == 'paginator'):?>nav-tab-active<?php endif;?>' href='admin.php?page=watupro_shortcode_generator&tab=paginator'><?php _e('Paginator', 'watupro');?></a>
		<?php if(watupro_intel()):?>
			<a class='nav-tab <?php if($tab == 'expand_personality'):?>nav-tab-active<?php endif;?>' href='admin.php?page=watupro_shortcode_generator&tab=expand_personality'><?php _e('Expand personality result', 'watupro');?></a>
		<?php endif;?>
	</h2>
	
	<?php switch($tab):
		case 'quiz':
			default: ?>
			<div class="wrap" id="quizTab">
				<h2><?php printf(__('Single %s Shortcode', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD));?></h2>
				<form method="post">
					<p><?php printf(__('Select %s', 'watupro'), WATUPRO_QUIZ_WORD);?> <select name="quiz_id" onchange="loadQuizCats(this.value);">
						<option value=""><?php _e('- Please select -', 'watupro');?></option>
						<?php foreach($quizzes as $quiz):?>
							<option value="<?php echo $quiz->ID?>"<?php if(!empty($_POST['quiz_id']) and $_POST['quiz_id'] == $quiz->ID) echo ' selected'?>><?php echo stripslashes($quiz->name);?></option>
						<?php endforeach;?>
					</select></p>
					<p><?php _e('Limit questions by question category:', 'watupro');?>
					<select name="category_id" id="qcatIDs">
						<option value=""><?php _e('All categories', 'watupro');?></option>
						<?php if(!empty($qcats) and count($qcats)):
							foreach($qcats as $qcat):?>
							<option value="<?php echo $qcat->ID?>"<?php if(!empty($_POST['category_id']) and $_POST['category_id'] == $qcat->ID) echo ' selected'?>><?php echo stripslashes($qcat->name);?></option>
						<?php endforeach; 
						endif;?>
					</select></p>
					
					<?php if(count($diff_levels_arr)):?>
					<p><?php _e('Limit questions by difficulty level:', 'watupro');?> <select name="difficulty_level">
						<option value=""><?php _e('All difficulty levels', 'watupro');?></option>
						<?php foreach($diff_levels_arr as $diff_level):?>
							<option value="<?php echo trim($diff_level)?>"<?php if(!empty($_POST['difficulty_level']) and $_POST['difficulty_level'] == trim($diff_level)) echo ' selected'?>><?php echo stripslashes($diff_level);?></option>
						<?php endforeach;?>
					</select></p>
					<?php endif;?>
					
					<p><?php _e('Limit questions by tags:', 'watupro');?> <input type="text" name="tags" value="<?php echo empty($_POST['tags']) ? '' : esc_attr($_POST['tags']);?>">
					<i><?php _e('Separate multiple tags by comma. The search will return questions containing any of the tags.', 'watupro');?></i> </p>
					
					<p><input type="submit" name="generate_shortcode" value="<?php _e('Generate shortcode', 'watupro');?>" class="button button-primary"></p>
					
					<?php if(!empty($_POST['generate_shortcode']) and !empty($_POST['quiz_id'])):?>
						<p><?php _e('Shortcode to use:', 'watupro');?> <input type="text" size="50" value="<?php echo htmlspecialchars($shortcode)?>" onclick="this.select()" readonly="readonly"></p>
					<?php endif;?>
					<input type="hidden" name="shortcode" value="quiz">
				</form>
			</div>
		<?php break;
		case 'list':?>
		<div class="wrap" id="listTab">
				<h2><?php printf(__('List Publisted %s Shortcode', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD_PLURAL));?></h2>
				<form method="post">
					<p><?php _e('Note that all these shortcodes will display only the published tests. A quiz which is not published inside a post or page, or is deactivated will not be shown.', 'watupro');?></p>
					<p><?php _e('Limit by category:','watupro');?> <select name="cat_id">
						<option value="ALL"><?php _e('All categories', 'watupro');?></option>
						<?php foreach($cats as $cat):?>
							<option value="<?php echo $cat->ID?>"<?php if(!empty($_POST['cat_id']) and $_POST['cat_id'] == $cat->ID) echo ' selected';?>><?php echo stripslashes($cat->name);?></option>
							<?php foreach($cat->subs as $sub):?>
									<option value="<?php echo $sub->ID?>" <?php if(!empty($_POST['cat_id']) and $_POST['cat_id']==$sub->ID) echo "selected"?>>&nbsp; - <?php echo stripslashes($sub->name);?></option>
							<?php endforeach; 
						 endforeach;?>
					</select>
					<?php _e('Order by:', 'watupro');?>
					<select name="orderby">
						<option value="created"><?php _e('Date created, oldest on top', 'watupro');?></option>
						<option value="latest"<?php if(!empty($_POST['orderby']) and $_POST['orderby'] == 'latest') echo ' selected';?>><?php _e('Date created, latest on top', 'watupro');?></option>
						<option value="title"<?php if(!empty($_POST['orderby']) and $_POST['orderby'] == 'title') echo ' selected';?>><?php _e('Title', 'watupro');?></option>
					</select>					
					</p>
					<p><?php printf(__('By default this shortcode creates a simple list of %1$s with links to them. You can however use your own design as explained <a href="%2$s" target="_blank">here</a>.', 'watupro'), WATUPRO_QUIZ_WORD, 'https://blog.calendarscripts.info/list-quizzes-in-watupro/');?></p>
					<p><input type="submit" name="generate_shortcode" value="<?php _e('Generate shortcode', 'watupro');?>" class="button button-primary"></p>
					
					<?php if(!empty($_POST['generate_shortcode'])):?>
						<p><?php _e('Shortcode to use:', 'watupro');?> <input type="text" size="40" value="<?php echo htmlspecialchars($shortcode)?>" onclick="this.select()" readonly="readonly"></p>
					<?php endif;?>
					<input type="hidden" name="shortcode" value="list">
				</form>
		</div>		
		<?php break;
		case 'myexams':?>
		<div class="wrap" id="myexamsTab">
			<h2><?php printf(__('My %s Page Shortcode', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD_PLURAL));?></h2>
			<form method="post">
				<p><?php _e('Limit by categories:', 'watupro');?> <select size="4" multiple="multiple" name="cats[]">
					<option value=""><?php _e('All Categories', 'watupro');?></option>
					<?php foreach($cats as $cat):?>
							<option value="<?php echo $cat->ID?>"<?php if(!empty($_POST['cats']) and is_array($_POST['cats']) and in_array($cat->ID, $_POST['cats'])) echo ' selected';?>><?php echo stripslashes($cat->name);?></option>
								<?php foreach($cat->subs as $sub):?>
									<option value="<?php echo $sub->ID?>"<?php if(!empty($_POST['cats']) and is_array($_POST['cats']) and in_array($sub->ID, $_POST['cats'])) echo ' selected';?>>&nbsp; - <?php echo stripslashes($sub->name);?></option>
							<?php endforeach; 
					 endforeach;?>
				</select>
				<?php _e('Order by:', 'watupro');?>
					<select name="orderby">
						<option value=""><?php _e('Default order, date created', 'watupro');?></option>
						<option value="latest"<?php if(!empty($_POST['orderby']) and $_POST['orderby'] == 'latest') echo ' selected';?>><?php _e('Date created, latest on top', 'watupro');?></option>
						<option value="title"<?php if(!empty($_POST['orderby']) and $_POST['orderby'] == 'title') echo ' selected';?>><?php _e('Title', 'watupro');?></option>
					</select></p>
					<p><input type="checkbox" name="reorder_by_latest_taking" value="1" <?php if(!empty($_POST['reorder_by_latest_taking'])) echo 'checked';?>> <?php printf(__('Reorder by latest taken %s on top', 'watupro'), WATUPRO_QUIZ_WORD);?></p>
					<p><?php _e('Status:', 'watupro');?> <select name="status">
						<option value=""><?php _e('Any status', 'watupro');?></option>
						<option value="todo" <?php if(!empty($_POST['status']) and $_POST['status'] == 'todo') echo 'selected'?>><?php _e('To-do', 'watupro');?></option>
						<option value="completed" <?php if(!empty($_POST['status']) and $_POST['status'] == 'completed') echo 'selected'?>><?php _e('Completed', 'watupro');?></option>
					</select></p>
					
					<p><input type="checkbox" name="details_no_popup" value="1" <?php if(!empty($_POST['details_no_popup'])) echo 'checked';?>> <?php _e('Do not open the "View details" link in a pop-up', 'watupro');?></p>
					
					<p><input type="submit" name="generate_shortcode" value="<?php _e('Generate shortcode', 'watupro');?>" class="button button-primary"></p>
					
					<?php if(!empty($_POST['generate_shortcode'])):?>
						<p><?php _e('Shortcode to use:', 'watupro');?> <input type="text" size="60" value="<?php echo htmlspecialchars($shortcode)?>" onclick="this.select()" readonly="readonly"></p>
					<?php endif;?>
					<input type="hidden" name="shortcode" value="myexams">
			</form>
		</div>
		<?php break;
		case 'leaderboard':?>
		<div class="wrap" id="leaderboardTab">
			<h2><?php _e('Basic Leaderboard Shortcode','watupro');?></h2>
			<form method="post">
					<p><?php printf(__('This shortcode generates a basic leaderboard of users ordered by total points collected. More advanced leaderboards are available in the <a href="%s" target="_blank">WatuPRO Play Plugin</a> which comes with its own shortcodes generator.', 'watupro'), 'https://calendarscripts.info/watupro/modules.html');?></p>
					
					<p><?php _e('Number of participants to show in the leaderboard:', 'watupro');?> <input type="text" size="4" name="number" value="<?php echo (!empty($_POST['number']) and intval($_POST['number']) > 0) ? intval($_POST['number']) : 10;?>"></p>
					
					<p><input type="submit" name="generate_shortcode" value="<?php _e('Generate shortcode', 'watupro');?>" class="button button-primary"></p>
					
					<?php if(!empty($_POST['generate_shortcode'])):?>
						<p><?php _e('Shortcode to use:', 'watupro');?> <input type="text" size="60" value="<?php echo htmlspecialchars($shortcode)?>" onclick="this.select()" readonly="readonly"></p>
					<?php endif;?>
					<input type="hidden" name="shortcode" value="leaderboard">
			</form>
		</div>
		<?php break;		
		case 'result':?>
		<div class="wrap" id="resultTab">
			<h2><?php printf(__('%s Result Shortcode','watupro'), ucfirst(WATUPRO_QUIZ_WORD));?></h2>
			<form method="post">
					<p><?php printf(__('This shortcode shows the latest result on a given %s of a given user. If the user has completed the quiz multiple times, their latest result will be shown.'), WATUPRO_QUIZ_WORD);?></p>
					
					<p><?php _e('Display:', 'watupro');?> <select name="what">
						<option value="points" <?php if(!empty($_POST['what']) and $_POST['what'] == 'points') echo 'selected';?>><?php _e('Points', 'watupro');?></option>
						<option value="percent" <?php if(!empty($_POST['what']) and $_POST['what'] == 'percent') echo 'selected';?>><?php _e('Percent correct answers', 'watupro');?></option>
						<option value="percent_points" <?php if(!empty($_POST['what']) and $_POST['what'] == 'percent_points') echo 'selected';?>><?php _e('Percent of maximum points', 'watupro');?></option>
						<option value="grade" <?php if(!empty($_POST['what']) and $_POST['what'] == 'grade') echo 'selected';?>><?php _e('Grade', 'watupro');?></option>
						<option value="details" <?php if(!empty($_POST['what']) and $_POST['what'] == 'details') echo 'selected';?>><?php _e('Full "final screen"', 'watupro');?></option>
					</select>
					
					<?php printf(__('For %s:', 'watupro'), WATUPRO_QUIZ_WORD);?> <select name="quiz_id">
						<option value="0"><?php printf(__('Latest attempt of the user, any %s', 'watupro'), WATUPRO_QUIZ_WORD);?></option>
						<?php foreach($quizzes as $quiz):?>
							<option value="<?php echo $quiz->ID?>"<?php if(!empty($_POST['quiz_id']) and $_POST['quiz_id'] == $quiz->ID) echo ' selected'?>><?php echo stripslashes($quiz->name);?></option>
						<?php endforeach;?>
					</select>
					</p>
					
					<p>
						<?php _e('For user:', 'watupro');?> <select name="for_user" onchange="this.value == 'logged' ? jQuery('#specifyUserId').hide() : jQuery('#specifyUserId').show();">
							<option value="logged"><?php _e('The logged in user sees their own result', 'watupro');?></option>
							<option value="specify"<?php if(!empty($_POST['for_user']) and $_POST['for_user'] == 'specify') echo ' selected';?>><?php _e('Specific user ID', 'watupro');?></option>
						</select>
						
						<span id="specifyUserId" style='display:<?php echo (!empty($_POST['for_user']) and $_POST['for_user'] == 'specify') ? 'inline' : 'none';?>'>
							<?php _e('enter user ID:', 'watupro');?> <input type="number" name="user_id" size="4" value="<?php echo empty($_POST['user_id']) ? '' : intval($_POST['user_id']);?>">
						</span>
					</p>
					
					<p><?php _e('Question category result:', 'watupro');?> <select name="qcat_id">
					<option value=""><?php _e("- No question category result -", 'watupro');?></option>
					<?php foreach($qcats as $cat):?>
						<option value="<?php echo $cat->ID?>" <?php if(!empty($_POST['qcat_id']) and $_POST['qcat_id'] == $cat->ID) echo "selected"?>><?php echo stripslashes(apply_filters('watupro_qtranslate', $cat->name));?></option>
						<?php foreach($cat->subs as $sub):?>
							<option value="<?php echo $sub->ID?>" <?php if(!empty($_POST['qcat_id']) and $_POST['qcat_id'] == $sub->ID) echo "selected"?>> - <?php echo stripslashes(apply_filters('watupro_qtranslate', $sub->name));?></option>
						<?php endforeach;
					 endforeach;?>
					</select><br>
					<i><?php printf(__('By selecting a category you can have the results shown for a specific question category instead of the global %s result. Note that for this to work you should either have used the %%CATGRADES%% variable in the final screen or enabled the "Always calculate category grades" option.', 'watupro'), WATUPRO_QUIZ_WORD);?></i></p>
					
					<p><?php _e('Optional placeholder text. It will be shown in case there is no result.', 'watupro');?> <input type="text" name="placeholder" size="50" value="<?php if(!empty($_POST['placeholder'])) echo htmlspecialchars($_POST['placeholder']);?>"></p>
					
					<p><input type="submit" name="generate_shortcode" value="<?php _e('Generate shortcode', 'watupro');?>" class="button button-primary"></p>
					
					<?php if(!empty($_POST['generate_shortcode'])):?>
						<p><?php _e('Shortcode to use:', 'watupro');?> <input type="text" size="60" value="<?php echo htmlspecialchars($shortcode)?>" onclick="this.select()" readonly="readonly"></p>
					<?php endif;?>
					<input type="hidden" name="shortcode" value="result">
			</form>
		</div>
		<?php break;
		case 'chart':?>
		<div class="wrap" id="chartTab">
			<h2><?php _e('Basic Chart Shortcode','watupro');?></h2>
			
			<p><?php printf(__('This shortcode should be used only on the "Final screen" or "Email contents" of the %1$s. See examples and learn more <a href="%2$s" target="_blank">here</a>.', 'watupro'), WATUPRO_QUIZ_WORD, 'https://blog.calendarscripts.info/watupro-basic-bar-chart/');?></p>
			
			<form method="post" onsubmit="return validateChartForm(this);">
					<p><?php _e('Generate chart(s) from:', 'watupro');?> <select name="show"  onchange="if(this.value == 'max_points') { this.form.average.value = 'show'; this.form.overview.value=1;}">
						<option value="both"><?php _e('Points and % correct answers', 'watupro');?></option>
						<option value="points"<?php if(!empty($_POST['show']) and $_POST['show'] == 'points') echo ' selected';?>><?php _e('Points', 'watupro');?></option>
						<option value="percent"<?php if(!empty($_POST['show']) and $_POST['show'] == 'percent') echo ' selected';?>><?php _e('% correct answers', 'watupro');?></option>
						<option value="max_points"<?php if(!empty($_POST['show']) and $_POST['show'] == 'max_points') echo ' selected';?>><?php _e('User points vs. maximum possible points', 'watupro');?></option>
					</select></p>	
					
					<p><?php _e('Bar color for the user bar (default is blue):', 'watupro')?> <input type="text" name="your_color" value="<?php echo empty($_POST['your_color']) ? '' : esc_attr($_POST['your_color']);?>" size="6"> <br>
					<?php _e('Bar color for the average results bar (default is gray):', 'watupro')?> <input type="text" name="avg_color" value="<?php echo empty($_POST['avg_color']) ? '' : esc_attr($_POST['avg_color']);?>" size="6"> <br>
					</p>			
					
					<p><?php _e('Bars width in pixels (defaults to 100):', 'watupro');?> <input type="text" name="bar_width" value="<?php echo empty($_POST['bar_width']) ? '' : intval($_POST['bar_width']);?>" size="6"></p>	
					
					<p><?php _e('Show the average values from everyone:', 'watupro');?> <select name="average">
						<option value="show"><?php _e('Show','watupro');?></option>
						<option value="hide"<?php if(!empty($_POST['average']) and $_POST['average'] == 'hide') echo ' selected';?>><?php _e('Hide','watupro');?></option>
					</select></p>
					
					<p><input type="checkbox" name="round_points" value="1" <?php if(!empty($_POST['round_points'])) echo 'checked';?>> <?php _e('Round points to the whole number','watupro');?></p>
					
					<p><?php printf(__('Show overview of %s attempts.', 'watupro'), '<input type="number" min="1" max="10" name="overview" value="'.(empty($_POST['overview']) ? 1 : intval($_POST['overview'])).'" size="4">');?><br>
					<i><?php _e('This will work only for logged in users. Otherwise the parameter reverts to 1. If the parameter show is set to max_points, this will always be switched off to 1.', 'watupro');?></i></p>
					
					<p><b><?php _e('The labels below are the texts that will be shown under the bars. They have default values so you need to enter them only if you want to change the defaults.', 'watupro');?></b></p>
					
					<p><?php _e('"Your points" label:', 'watupro');?> <input type="text" name="your_points_text" size="30" value="<?php echo empty($_POST['your_points_text']) ? '': esc_attr($_POST['your_points_text']);?>"></p>
					
					<p><?php _e('Label that will show for the previous attempts:', 'watupro');?> <input type="text" name="your_overview_points_text" size="30" value="<?php echo empty($_POST['your_overview_points_text']) ? '' : esc_attr($_POST['your_overview_points_text']);?>"></p>
					
					<p><?php _e('"Avg. points" label:', 'watupro');?> <input type="text" name="avg_points_text" size="30" value="<?php echo empty($_POST['avg_points_text']) ? '': esc_attr($_POST['avg_points_text']);?>"></p>
					
					<p><?php _e('"Your % correct" label:', 'watupro');?> <input type="text" name="your_percent_text" size="30" value="<?php echo empty($_POST['your_percent_text']) ? '': esc_attr($_POST['your_percent_text']);?>"></p>
					
					<p><?php _e('"% correct" label when overview of past results is shown:', 'watupro');?> <input type="text" name="your_overview_percent_text" size="30" value="<?php echo empty($_POST['your_overview_percent_text']) ? '': esc_attr($_POST['your_overview_percent_text']);?>"></p>
					
					<p><?php _e('"Avg. % correct" label:', 'watupro');?> <input type="text" name="avg_percent_text" size="30" value="<?php echo empty($_POST['avg_percent_text']) ? '': esc_attr($_POST['avg_percent_text']);?>"></p>
					
					<p><input type="submit" name="generate_shortcode" value="<?php _e('Generate shortcode', 'watupro');?>" class="button button-primary"></p>
					
					<?php if(!empty($_POST['generate_shortcode'])):?>
						<p><?php _e('Shortcode to use:', 'watupro');?> <input type="text" size="100" value="<?php echo htmlspecialchars($shortcode)?>" onclick="this.select()" readonly="readonly"></p>
					<?php endif;?>
					<input type="hidden" name="shortcode" value="chart">
			</form>
		</div>
		<?php break;		
		case 'attempts':?>
		<div class="wrap" id="attemptsTab">
			<h2><?php _e('No. Attempts Shortcode','watupro');?></h2>
			
			<p><?php printf(__('This shortcode shows the number of attempts left to the user for %s that limit the number of attempts per account or IP address. It can show the attempts in total or how many are left for that user or IP address (whichever is less)', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL);?></p>
			
			<form method="post" onsubmit="return validateChartForm(this);">
				<?php printf(__('Select %s:', 'watupro'), WATUPRO_QUIZ_WORD);?> <select name="quiz_id">
					<?php foreach($quizzes as $quiz):?>
						<option value="<?php echo $quiz->ID?>"<?php if(!empty($_POST['quiz_id']) and $_POST['quiz_id'] == $quiz->ID) echo ' selected'?>><?php echo stripslashes($quiz->name);?></option>
					<?php endforeach;?>
				</select>	
				
				<p><?php _e('Show:', 'watupro');?> <select name="show">
					<option value="total"><?php _e('Total attempts allowed to user or IP address', 'watupro');?></option>
					<option value="left" <?php if(!empty($_POST['show']) and $_POST['show'] == 'left') echo 'selected';?>><?php _e('Attempts left for this user or IP address', 'watupro');?></option>
				</select></p>			
				
				<p><input type="submit" name="generate_shortcode" value="<?php _e('Generate shortcode', 'watupro');?>" class="button button-primary"></p>
				
				<?php if(!empty($_POST['generate_shortcode'])):?>
					<p><?php _e('Shortcode to use:', 'watupro');?> <input type="text" size="50" value="<?php echo htmlspecialchars($shortcode)?>" onclick="this.select()" readonly="readonly"></p>
				<?php endif;?>
				<input type="hidden" name="shortcode" value="attempts">
			</form>
		</div>
		<?php break;		
		case 'users_completed':?>
		<div class="wrap" id="attemptsTab">
			<h2><?php printf(__('Users Completed %s Shortcode','watupro'), ucfirst(WATUPRO_QUIZ_WORD));?></h2>
			
			<p><?php printf(__('This shortcode shows the number or percentage of users who completed a %s with selected grade and/or given threshold of points or percent correct answers. For example it can display what %% of users completed a %s with more than 10 points and more than 75%% correct answers.', 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD);?></p>
			
			<form method="post">
				<?php printf(__('Select %s:', 'watupro'), WATUPRO_QUIZ_WORD);?> <select name="quiz_id" onchange="loadQuizGrades(this.value);">
					<option value=""><?php _e('- Please select -', 'watupro');?></option>
					<?php foreach($quizzes as $quiz):?>
						<option value="<?php echo $quiz->ID?>"<?php if(!empty($_POST['quiz_id']) and $_POST['quiz_id'] == $quiz->ID) echo ' selected'?>><?php echo stripslashes($quiz->name);?></option>
					<?php endforeach;?>
				</select>	
				
				<p><?php _e('Show:', 'watupro');?> <select name="return">
					<option value="number"><?php _e('Number of users', 'watupro');?></option>
					<option value="percent" <?php if(!empty($_POST['return']) and $_POST['return'] == 'percent') echo 'selected';?>><?php _e('Percent of users', 'watupro');?></option>
				</select></p>	
				
				<p><?php _e('Grade:', 'watupro');?> <select name="grade_id" id="qgradeIDs">		
					<option value="">------</option>		
					<?php if(!empty($grades) and is_array($grades)):
						foreach($grades as $grade):?>
							<option value="<?php echo $grade->ID?>"<?php if(!empty($_POST['grade_id']) and $_POST['grade_id'] == $grade->ID) echo ' selected';?>><?php echo stripslashes($grade->gtitle);?></option>
					<?php endforeach; 
					endif;?>
				</select></p>		
				
				<p><input type="checkbox" name="use_points" value="1" <?php if(!empty($_POST['use_points'])) echo 'checked';?>> <?php _e('Points collected:', 'watupro');?>
					<select name="points_op">
						<option value="=">=</option>
						<option value=">"<?php if(!empty($_POST['points_op']) and $_POST['points_op'] == '>') echo ' selected';?>>&gt;</option>
						<option value=">="<?php if(!empty($_POST['points_op']) and $_POST['points_op'] == '>=') echo ' selected';?>>&gt;=</option>
						<option value="<"<?php if(!empty($_POST['points_op']) and $_POST['points_op'] == '<') echo ' selected';?>>&lt;</option>
						<option value="<="<?php if(!empty($_POST['points_op']) and $_POST['points_op'] == '<=') echo ' selected';?>>&gt;=</option>
					</select>
					<input type="text" name="points" value="<?php echo empty($_POST['points']) ? '' : $_POST['points'];?>" size="4"></p>
				
				
				<p><input type="checkbox" name="use_percent" value="1" <?php if(!empty($_POST['use_percent'])) echo 'checked';?>> <?php _e('Percent correct answers:', 'watupro');?>
					<select name="percent_op">
						<option value="=">=</option>
						<option value=">"<?php if(!empty($_POST['percent_op']) and $_POST['percent_op'] == '>') echo ' selected';?>>&gt;</option>
						<option value=">="<?php if(!empty($_POST['percent_op']) and $_POST['percent_op'] == '>=') echo ' selected';?>>&gt;=</option>
						<option value="<"<?php if(!empty($_POST['percent_op']) and $_POST['percent_op'] == '<') echo ' selected';?>>&lt;</option>
						<option value="<="<?php if(!empty($_POST['percent_op']) and $_POST['percent_op'] == '<=') echo ' selected';?>>&gt;=</option>
					</select>
					<input type="text" name="percent" value="<?php echo empty($_POST['percent']) ? '' : $_POST['percent'];?>" size="4"></p>
					
				<p><input type="submit" name="generate_shortcode" value="<?php _e('Generate shortcode', 'watupro');?>" class="button button-primary"></p>
				
				<?php if(!empty($_POST['generate_shortcode'])):?>
					<p><?php _e('Shortcode to use:', 'watupro');?> <input type="text" size="50" value="<?php echo htmlspecialchars($shortcode)?>" onclick="this.select()" readonly="readonly"></p>
				<?php endif;?>
				<input type="hidden" name="shortcode" value="users_completed">
			</form>
		</div>
		<?php break;	
		case 'segment':?>
		<div class="wrap" id="segmentTab">
			<h2><?php _e('Segment Stats','watupro');?></h2>
			
			<p><?php printf(__('This powerful shortcode lets you output stats how a segment of users have performed on a %1$s. <a href="%2$s" target="_blank">Learn more here</a>.', 'watupro'), WATUPRO_QUIZ_WORD, 'https://blog.calendarscripts.info/segment-performance-shortcode-in-watupro/');?></p>
			
			<form method="post" onsubmit="return validateSegmentForm(this);">
				<p><?php _e('Question ID (required):', 'watupro');?> <input type="text" size="4" name="question_id" value="<?php echo empty($_POST['question_id']) ? '' : intval($_POST['question_id']);?>"></p>
				
				<p><?php _e('Criteria:', 'watupro');?> <select name="criteria" onchange="wtpChangeCriteria(this.value)">
					<option value="percent_correct"<?php if(!empty($_POST['criteria']) and $_POST['criteria'] == 'percent_correct') echo ' selected';?>><?php _e('Percent correct answers', 'watupro');?></option>
					<option value="points"<?php if(!empty($_POST['criteria']) and $_POST['criteria'] == 'points') echo ' selected';?>><?php _e('Points', 'watupro');?></option>
					<option value="grade"<?php if(!empty($_POST['criteria']) and $_POST['criteria'] == 'grade') echo ' selected';?>><?php _e('Grade', 'watupro');?></option>
					<option value="category_grade"<?php if(!empty($_POST['criteria']) and $_POST['criteria'] == 'category_grade') echo ' selected';?>><?php _e('Category grade', 'watupro');?></option>
				</select></p>	
				
				<p id="segmentGradeID" style='display: <?php echo (!empty($_POST['criteria']) and $_POST['criteria'] == 'grade') ? 'block' : 'none';?>'><?php _e('Grade ID (optional):', 'watupro');?> <input type="text" size="4" name="grade_id" value="<?php echo empty($_POST['grade_id']) ? '' : intval($_POST['grade_id']);?>"> <i><?php _e('If empty the calculation will be based on the currently achieved grade from the user', 'watupro');?></i></p>			
				
				<p id="segmentCategyrGrade" style='display: <?php echo (!empty($_POST['criteria']) and $_POST['criteria'] == 'category_grade') ? 'block' : 'none';?>'><?php _e('Category grade ID:', 'watupro');?> <input type="text" size="4" name="catgrade_id" value="<?php echo empty($_POST['catgrade_id']) ? '' : intval($_POST['catgrade_id']);?>"> <?php _e('or', 'watupro');?>
				<?php _e('Question category ID:', 'watupro');?> <input type="text" size="4" name="category_id" value="<?php echo empty($_POST['category_id']) ? '' : esc_attr($_POST['category_id']);?>">				
				 <i><?php _e('Filling one of these is required.', 'watupro');?></i></p>	
				 
				 <p id="segmentCompare" style='display: <?php echo (!empty($_POST['criteria']) and ($_POST['criteria'] == 'grade' or $_POST['criteria'] == 'category_grade')) ? 'block' : 'none';?>'>
				 	<?php _e('Compare mode:', 'watupro');?> <select name="compare">
				 		<option value="same"<?php if(!empty($_POST['compare']) and $_POST['compare'] == 'same') echo ' selected';?>><?php _e('Same', 'watupro');?></option>
				 		<option value="better"<?php if(!empty($_POST['compare']) and $_POST['compare'] == 'better') echo ' selected';?>><?php _e('Better', 'watupro');?></option>
				 		<option value="worse"<?php if(!empty($_POST['compare']) and $_POST['compare'] == 'worse') echo ' selected';?>><?php _e('Worse', 'watupro');?></option>
				 	</select>
				 </p>
				 
				 <p><?php _e('Segment:', 'watupro');?> <input type="text" name="segment" value="<?php echo empty($_POST['segment']) ? '' : esc_attr($_POST['segment']);?>"></p>
				 
				 <p><input type="submit" name="generate_shortcode" value="<?php _e('Generate shortcode', 'watupro');?>" class="button button-primary"></p>
				
				<?php if(!empty($_POST['generate_shortcode'])):?>
					<p><?php _e('Shortcode to use:', 'watupro');?> <input type="text" size="100" value="<?php echo htmlspecialchars($shortcode)?>" onclick="this.select()" readonly="readonly"></p>
				<?php endif;?>
				<input type="hidden" name="shortcode" value="segment">
			</form>
		</div>
		<?php break;	
		case 'paginator':?>
		<div class="wrap" id="paginatorTab">
			<h2><?php _e('Paginator Stats','watupro');?></h2>
			
			<p><?php printf(__('Create a paginator for placing on the sidebar or elsewhere. It automatically knows if there is a single %s on the page or you are on archives or search results and appears only when appropriate. Do not use it if there is also paginator enabled in the %s settings.', 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD);?></p>
			
			<form method="post" onsubmit="return validateSegmentForm(this);">
				<p><?php _e('Based on:', 'watupro');?> <select name="paginator">
					<option value="questions"<?php if(!empty($_POST['paginator']) and $_POST['paginator'] == 'questions') echo ' selected';?>><?php _e('Questions', 'watupro');?></option>
					<option value="categories"<?php if(!empty($_POST['paginator']) and $_POST['paginator'] == 'categories') echo ' selected';?>><?php _e('Categories', 'watupro');?></option>
				</select></p>	
				
				<p><input type="checkbox" name="vertical" value="1" <?php if(!empty($_POST['vertical'])) echo 'checked';?>> <?php _e('Vertical', 'watupro');?></p>
				 
				 <p><input type="submit" name="generate_shortcode" value="<?php _e('Generate shortcode', 'watupro');?>" class="button button-primary"></p>
				
				<?php if(!empty($_POST['generate_shortcode'])):?>
					<p><?php _e('Shortcode to use:', 'watupro');?> <input type="text" size="100" value="<?php echo htmlspecialchars($shortcode)?>" onclick="this.select()" readonly="readonly"></p>
				<?php endif;?>
				<input type="hidden" name="shortcode" value="paginator">
			</form>
		</div>
		<?php break;	
		case 'expand_personality':?>
		<div class="wrap" id="expandPersonalityTab">
			<h2><?php _e('Expand Personality Result','watupro');?></h2>
			
			<p><?php printf(__('The following shortcode can be used only in the "Final Screen" and email box to improve the content shown to the user on personality %s. Many personality %s work better when displaying not just the assigned personality type but also information how many answers the user gave for the other types. Here is how to use it with an example:.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL, WATUPRO_QUIZ_WORD_PLURAL);?></p>
			
			<form method="post">
				<p><?php _e('Sort by:', 'watupro');?> <select name="sort">
					<option value=""><?php _e('Default / none', 'watupro');?></option>
					<option value="best"<?php if(!empty($_POST['sort']) and $_POST['sort'] == 'best') echo ' selected'?>><?php _e('Best matches first', 'watupro');?></option>
					<option value="worst"<?php if(!empty($_POST['sort']) and $_POST['sort'] == 'worst') echo ' selected'?>><?php _e('Worst matches first', 'watupro');?></option>
					<option value="alpha"<?php if(!empty($_POST['sort']) and $_POST['sort'] == 'alpha') echo ' selected'?>><?php _e('Alphabetical by personality name', 'watupro');?></option>
				</select></p>
				
				<p><?php _e('Limit to this number of personalities:', 'watupro');?> <input type="text" name="limit" value="<?php echo empty($_POST['limit']) ? '' : intval($_POST['limit']);?>" size="4"></p>
				
				<p><input type="checkbox" name="empty" value="1" <?php if(!empty($_POST['empty'])) echo 'checked';?>> <?php _e('Exclude personalities that did not get any points', 'watupro');?></p>
				
				<div id="limitToPersonality" style='display:<?php echo empty($_POST['chart']) ? 'block' : 'none';?>'>
					<p><?php _e('Show only the personality ranked at place:', 'watupro');?> <input type="text" name="rank" value="<?php echo empty($_POST['rank']) ? '' : intval($_POST['rank']);?>" size="4"></p>
					<p><?php _e('Show only this personality (enter personality name):', 'watupro');?> <input type="text" name="personality" value="<?php echo empty($_POST['personality']) ? '' : esc_attr($_POST['personality']);?>" size="30"></p>
				</div>
				
				<p><input type="checkbox" name="chart" value="1" <?php if(!empty($_POST['chart'])) echo 'checked';?> onclick="this.checked ? jQuery('#limitToPersonality').hide() : jQuery('#limitToPersonality').show();"> <?php _e('Display as simple chart', 'watupro');?></p>
				
				 <p><input type="submit" name="generate_shortcode" value="<?php _e('Generate shortcode', 'watupro');?>" class="button button-primary"></p>
				
				<?php if(!empty($_POST['generate_shortcode'])):?>
					<p><?php _e('Shortcode to use:', 'watupro');?> <input type="text" size="100" value="<?php echo htmlspecialchars($shortcode)?>" onclick="this.select()" readonly="readonly"></p>
				<?php endif;?>
				<input type="hidden" name="shortcode" value="expand_personality">
			</form>
		</div>
		<?php break;	
	endswitch;?>
</div>

<script type="text/javascript" >
function loadQuizGrades(id) {
	var data = {'action': 'watupro_ajax', 'do': 'select_grades', 'exam_id' : id};
	
	jQuery.post("<?php echo admin_url('admin-ajax.php')?>", data, function(msg) {
		jQuery('#qgradeIDs').html(msg);
	});
}

function loadQuizCats(id) {
	var data = {'action': 'watupro_ajax', 'do': 'select_quiz_qcats', 'quiz_id' : id};
	
	jQuery.post("<?php echo admin_url('admin-ajax.php')?>", data, function(msg) {
		msg = '<option value=""><?php _e('All categories', 'watupro');?></option>' + msg;
		jQuery('#qcatIDs').html(msg);
	});
}

function validateChartForm(frm) {
	// overview must be between 1 and 10
	if(frm.overview.value < 1 || frm.overview.value > 10) {
		alert("<?php _e('The allowed values for overview are between 1 and 10.', 'watupro');?>");
		frm.overview.focus();
		return false;
	}	
	
	var yourPointsStr = frm.your_points_text.value;
	if(yourPointsStr !='' && yourPointsStr.indexOf('%s') == -1) {
		alert("<?php _e("This field should contain the variable '%s' because it will be replaced with the number of points", 'watupro');?>");
		frm.your_points_text.focus();
		return false;
	}
	
	var yourOverviewPointsStr = frm.your_overview_points_text.value;
	var parts = yourOverviewPointsStr.split('%s');
	if(yourOverviewPointsStr != '' && parts.length != 3) {
		alert("<?php _e("This field should contain the variable '%s' exactly twice because they will be replaced with date and points.", 'watupro');?>");
		frm.your_overview_points_text.focus();
		return false;
	}
	
	var avgPointsStr = frm.avg_points_text.value;
	if(avgPointsStr != '' && avgPointsStr.indexOf('%s') == -1) {
		alert("<?php _e("This field should contain the variable '%s' because it will be replaced with the average number of points", 'watupro');?>");
		frm.avg_points_text.focus();
		return false;
	}
	
	var yourPercentStr = frm.your_percent_text.value;
	if(yourPercentStr != '' && (yourPercentStr.indexOf('%d') == -1 || yourPercentStr.indexOf('%%') == -1)) {
		alert("<?php _e("This field should contain the variable '%d' and '%%' because they will be the percentage and the % sign.", 'watupro');?>");
		frm.your_percent_text.focus();
		return false;
	}
	
	var yourOverviewPercentStr = frm.your_overview_percent_text.value;	
	if(yourOverviewPercentStr != '' && (yourOverviewPercentStr.indexOf('%s') == -1 || yourOverviewPercentStr.indexOf('%%') == -1 || yourOverviewPercentStr.indexOf('%%') == -1)) {
		alert("<?php _e("This field should contain the variable '%s', '%d' and '%%' because they will be replaced with data.", 'watupro');?>");
		frm.your_overview_percent_text.focus();
		return false;
	}
	
	var avgPercentStr = frm.avg_percent_text.value;
	if(avgPercentStr != '' && (avgPercentStr.indexOf('%d') == -1 || avgPercentStr.indexOf('%%') == -1)) {
		alert("<?php _e("This field should contain the variable '%d' and '%%' because they will be the percentage and the % sign.", 'watupro');?>");
		frm.avg_percent_text.focus();
		return false;
	}
	
	return true;
}

// change criteria for the segment stats shortcode
function wtpChangeCriteria(val) {
	jQuery('#segmentGradeID').hide();
	jQuery('#segmentCategyrGrade').hide();
	jQuery('#segmentCompare').hide();
	
	if(val == 'grade') {
		jQuery('#segmentGradeID').show();
		jQuery('#segmentCompare').show();
	}
	
	if(val == 'category_grade') {
		jQuery('#segmentCategyrGrade').show();
		jQuery('#segmentCompare').show();
	}
}

// validate segment stats shortcode
function validateSegmentForm(frm) {
	if(frm.question_id.value == '' || isNaN(frm.question_id.value) || frm.question_id.value < 1) {
		alert("<?php _e('Please enter valid question ID.','watupro');?>");
		frm.question_id.focus();
		return false;
	}
	
	if(frm.criteria.value == 'category_grade' && frm.catgrade_id.value == '' && frm.category_id.value == '') {
		alert("<?php _e('You need to fill either category ID or category grade ID. Please read the online guide for more information and examples.', 'watupro');?>");
		frm.catgrade_id.value.focus();
		return false;
	}
	
	return true;
}
</script>