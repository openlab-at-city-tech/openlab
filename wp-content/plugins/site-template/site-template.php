<?php
/* Plugin Name: Site Template
Description: Allows user to select a profile when creating a blog.
Author: Michael Porter
Version: 1.1
Site Wide Only: true
Network: true
*/ 


/**************************************************************
* @return the major version (2 or 3)
***************************************************************/
function get_major_version()
{
	global  $wp_version;
	return array_shift(explode('.',$wp_version));
}


/*************************************************************** 
* Moves a template to a new sequence
* @param $templates an array of templates
* @param $tid is the id of the template to be moved
* @param $new_seq is the new position for the above template 
* @return the newly oredered template list
***************************************************************/
function st_move_template($templates, $tid, $new_seq)
{
	foreach ($templates as $key=>$template) {
		if ($template['seq'] != 0) {
			if ($template['seq'] < $templates[$tid]['seq'] && $template['seq'] >= $new_seq) {
				$templates[$key]['seq'] = $template['seq'] + 1;
			} elseif ($template['seq'] > $templates[$tid]['seq'] && $template['seq'] <= $new_seq) {
				$templates[$key]['seq'] = $template['seq'] - 1;
			}
		}
	}
	$templates[$tid]['seq'] = $new_seq;
	return st_set_templates($templates);
}

/*************************************************************** 
* Used when sorting the templates
* @param $a is a template array
* @param $b is a template array
* @return 0 if $a=$b, -1 if $a<$b, 1 if $a>$b
***************************************************************/ 
function st_compare_templates($a, $b)
{
	if ($a['seq'] == $b['seq']) return 0;
	return ($a['seq'] < $b['seq'])? -1: 1;
}

/*************************************************************** 
* Sets the template variables
* @param $templates an array of template arrays
* @return $templates array
***************************************************************/ 
function st_set_templates($templates)
{
	if (!update_site_option('site-templates',$templates)) 
		add_site_option('site-templates',$templates);
	return $templates;
}

/*************************************************************** 
* Gets the template variables
* @return $templates array
***************************************************************/ 
function st_get_templates()
{
	$templates = get_site_option('site-templates');
	if ($templates==''){
		$templates = array();
		$templates[] = array(
					'option-txt' => 'Other',
					'template' => 0,
					'children' => array(),
					'active' => 1,
					'seq' => 999,
				);
		st_set_templates($templates);
	}
	return $templates;
}

/*************************************************************** 
* Gets the maximum sequence 
* @param $templates an array of template arrays
* @return max seq
***************************************************************/ 
function st_get_max_seq($templates)
{
	$max = 0;
	foreach ($templates as $template) if ($template['seq'] > $max && $template['seq'] != 999) $max = $template['seq'];
	return $max;
}

/*************************************************************** 
* Deactivates a template
* @param $tid the id of the template to be deactivated
* @param $templates an array of template arrays
* @return $templates array
***************************************************************/ 
function st_deactivate_template($tid, $templates)
{
	foreach ($templates as $key=>$template) {
		if (($template['seq'] > $templates[$tid]['seq']) && ($template['seq']!=999)) $templates[$key]['seq'] = $templates[$key]['seq'] - 1;
	}
	$templates[$tid]['active']=0;
	$templates[$tid]['seq']=-1;
	return st_set_templates($templates);
}

/*************************************************************** 
* Gets the url for the site template admin page
* @param $action is the action to perform
* @param $tid is the id of the template on which the above action will be executred
* @return the formatted url
***************************************************************/
function st_get_url($action, $tid)
{
	$actions = array(
		'page' => $_GET['page'],
		'taction' => $action,
		'tid' => $tid,
	);
	return add_query_arg($actions,((get_major_version() == 3)?"./sites.php":"./wpmu-admin.php"));
}

/*************************************************************** 
* Called when deleting blog.  
***************************************************************/ 
add_action('delete_blog','st_delete_blog',10,2);
function st_delete_blog($blog_id,$drop)
{
	$templates = st_get_templates();
	if (array_key_exists($blog_id,$templates)) {
		$seq = $templates[$blog_id]['seq'];
		foreach ($templates as $key=>$template)
		{
			if ($key == $blog_id)
			{
				unset($templates[$key]);
			} 
			else {
				$cur_seq = $templates[$key]['seq'];
				if (($cur_seq > $seq) && ($cur_seq < 999))  {
					$templates[$key]['seq'] = $cur_seq - 1;
				}
			}
		}
		st_set_templates($templates);
	}
}

/*************************************************************** 
* Deactivate or Archive Site (on site admin panel)
***************************************************************/ 
add_action('deactivate_blog','st_deactivate_blog');
add_action('archive_blog','st_deactivate_blog');
function st_deactivate_blog($blog_id)
{
	$templates = st_get_templates();
	if (array_key_exists($blog_id, $templates)) {
		$templates[$blog_id]['active'] = 0;
	}
	st_set_templates($templates);
}

/*************************************************************** 
* Activate or unarchive Site (on site admin panel)
***************************************************************/ 
add_action('activate_blog','st_reactivate_blog');
add_action('unarchive_blog','st_reactivate_blog');
function st_reactivate_blog($blog_id)
{
	$templates = st_get_templates();
	if (array_key_exists($blog_id, $templates)) {
		$templates[$blog_id]['active'] = 1;
	}
	st_set_templates($templates);
}


/*************************************************************** 
* Deactivate plugin
***************************************************************/ 
register_deactivation_hook(__FILE__,'st_deactivate_plugin'); 
function st_deactivate_plugin()
{
	st_set_templates(NULL);
}
	
/*************************************************************** 
* Adds the "make template", "remove template" column to the Sites Admin panel
* @param $blog_id for the current row
***************************************************************/ 
add_action('wpmublogsaction','st_wpmublogsaction');
function st_wpmublogsaction($blog_id)
{
	global $blog;

	$templates = st_get_templates();
	$tid = $_GET['tid'];
	$details = get_blog_details($blog_id);
	if ($tid == $blog_id) {
		$action = $_GET['taction'];
		switch ($action) {
			case 'activate':
				$templates[$blog_id] = array(
					'option-txt' => $details->blogname,
					'template' => $blog_id,
					'children' => array(),
					'active' => 1,
					'seq' => st_get_max_seq($templates) + 1,
				);
				$templates = st_set_templates($templates);
				break;
			case 'deactivate':
				$templates = st_deactivate_template($blog_id, $templates);
				break;
		}
	}
	
	if (($details->deleted == 0) && ($details->archived ==0)) {
		if (array_key_exists($blog_id, $templates)) {
			$template = $templates[$blog_id];
			if ($template['active'] === 1) {
				$action = 'deactivate';
				$str = 'Deactivate Template';
			} else {
				$action = 'activate';
				$str = 'Activate Template';
			}
		} else {
			$action = 'activate';
			$str = 'Activate Template';
		}
		
		$args = array_merge ($_GET, array(
			'taction' => $action,
			'tid' => $blog_id,
			'action' => 'template',
			'updated' => 'true',
		));
		$link = add_query_arg($args);
		echo '<a href="'.$link.'">'.$str.'</a>';
	}
}

/*************************************************************** 
* Adds the "Site Template" menu to the Network Admin Sites menu panel
* @param $blog_id for the current row
***************************************************************/
add_action('network_admin_menu', 'st_admin_menu');
function st_admin_menu() 
{
	add_submenu_page(((get_major_version() == 3)?'sites.php':'wpmu-admin.php'),'Site Template Options', 'Site Template', 8, __FILE__, 'st_options');
}

/*************************************************************** 
* Copies the values from one blog table to another blog table
* @param $table_name is the name of the table without the prefix
* @param $blog_id is the id of the new blog
* @param $template is the blog id of the template
***************************************************************/ 
function st_copy_table($table_name, $blog_id, $template)
{
	global $wpdb;
	global $table_prefix;
	
	$new_table = $table_prefix . $blog_id . "_" . $table_name;
	$wpdb->query ("Delete from " . $new_table);
	$wpdb->query ("Insert into " . $new_table . 
		" select * from " . $table_prefix . $template . "_" . $table_name);
}

/*************************************************************** 
* Deletes all post revisions
* @param $blog_id is the id of the new blog
***************************************************************/ 
function st_delete_post_revisions($blog_id)
{
	global $wpdb;
	global $table_prefix;
	
	$wpdb->query ("Delete from " . $table_prefix . $blog_id . "_posts where post_type = 'revision'");
}

/*************************************************************** 
* Copies the option table.  Must be handled seperately because of arrays.  Also have to avoid adding or 
* overwriting certain options.
***************************************************************/ 
function st_copy_options($blog_id, $template)
{
	global $wpdb;
	global $table_prefix;

	switch_to_blog($template);
	// get all old options
	$all_options = (array) get_alloptions(); // 2.9.2 returns a class, 3.0 returns an array
	$options = array();
	foreach (array_keys($all_options) as $key) {
		$options[$key] = get_option($key);  // have to do this to deal with arrays
	}
	// theme mods -- don't show up in all_options.  Won't add mods for inactive theme.
	$theme = get_option('current_theme');
	$mods = get_option('mods_'.$theme);
	
	$preserve_option = array(
		"siteurl",
		"blogname",
		"admin_email",
		"new_admin_email",
		"home",
		"upload_path",
		"db_version",
		$table_prefix . $template . "_user_roles",
		"fileupload_url");
	
	// now write them all back 
	switch_to_blog($blog_id); 
	foreach ($options as $key => $value) {
		if (!in_array($key, $preserve_option)) update_option($key, $value);}
	// add the theme mods
	update_option('mods_'.$theme,$mods);
}

/*************************************************************** 
* Called when creating a site immediately after registering a user.
* Put the template id in the site's meta data
***************************************************************/ 
add_filter('add_signup_meta','st_add_signup_meta');
function st_add_signup_meta($meta)
{
		$meta['st-template'] = $_POST['st-template'];
		return $meta;
}

/*************************************************************** 
* Main function for copying template pages, posts, categories, themes, plugins, and widgets to new blog
* first gets info from template than copies over to the new blog
***************************************************************/ 
add_action('wpmu_new_blog','st_wpmu_new_blog',10,6);
function st_wpmu_new_blog ($blog_id, $user_id, $domain, $path, $site_id, $meta )
{
	$template = array_key_exists('st-template',$_POST) ?
		$_POST['st-template'] :
		$meta['st-template'];
	if ($template == 0 || empty($template)) return;
	
	st_copy_table ('commentmeta', $blog_id, $template);
	st_copy_table ('comments', $blog_id, $template);
	st_copy_table ('links', $blog_id, $template);
	st_copy_table ('postmeta', $blog_id, $template);
	st_copy_table ('posts', $blog_id, $template);
	st_copy_table ('term_relationships', $blog_id, $template);
	st_copy_table ('term_taxonomy', $blog_id, $template);
	st_copy_table ('terms', $blog_id, $template);
	st_copy_options($blog_id, $template); 
	st_delete_post_revisions($blog_id);
}

/*************************************************************** 
* Run when the signup blog form appears.  Add radio buttons.
***************************************************************/ 
add_action('signup_blogform','st_signup_blogform');
function st_signup_blogform($errors)
{
	$templates = st_get_templates();
	if (sizeof($templates) == 1) return; 
	usort($templates,'st_compare_templates'); ?>
	<label for="sp-template">What type of site are you creating?</label>
	<?php foreach ($templates as $template) {
		if ($template['active'] === 1) {
			$id = $template['template']; ?>
			<input type="radio" name="st-template" value="<?php echo $id; ?>"<?php if($id===0) echo 'checked'; ?>><?php echo $template['option-txt']; ?></input><br>
		<?php } ?>
	<?php } 
}

/***************************************************************
* Updates templates.  Called in the Site Template option page
* @param $templates a collection of templates
* @param $errs an array to store any errors in
* @return Updated array of templates if no errors, the same as the arg if errors
***************************************************************/
function st_update_templates($templates, &$errs)
{
	$tids = $_POST['tid'];
	$tnames = $_POST['template-opt-txt'];
	foreach ($tids as $key=>$tid) {
		$templates[$tid]['option-txt'] = $tnames[$key];
		if (empty($tnames[$key])) $errs[$tid] = "Option description is required.";
	}
	if (!empty($errs)) return $templates;
	return st_set_templates($templates);
}

/*************************************************************** 
* The main function for the options screen
***************************************************************/
function st_options()
{
	$templates = st_get_templates();
	$action = $_GET['taction'];
	$tid = $_GET['tid'];
	$max_seq = st_get_max_seq($templates);
	$errs = array();
	switch ($action) {
		case 'movefirst':
			$templates =st_move_template($templates,$tid,1);
			break;
		case 'moveup':
			$templates =st_move_template($templates,$tid,$templates[$tid]['seq']-1);
			break;
		case 'movedown':
			$templates =st_move_template($templates,$tid,$templates[$tid]['seq']+1);
			break;
		case 'movelast':
			$templates = st_move_template($templates,$tid,$max_seq);
			break;
		case 'deactivate':
			$templates = st_deactivate_template($tid,$templates);
			break;
		case 'update':
			$templates = st_update_templates($templates, $errs);
			break;
	} ?>
	<div class="wrap">
	<?php if ($action != 'children') { ?>
		<h2>Site Template</h2>
		<?php if ($action=='update') { 
			if (empty($errs)) {?>
				<div id="message" class="updated"><p>Templates Updated.</p></div>
			<?php } else { ?> 
				<div id="message" class="error"><p>Update Failed.</p></div>
		<?php }} ?> 
		<?php if (!$templates) { ?>
			There are no active templates.  To activate a template go to the <a href="<?php echo (get_major_version()==2)?'./wpmu-blogs.php':'./ms-sites.php'; ?>"><?php echo (get_major_version()==2)?'Blogs':'Sites'; ?> page</a> and click on the <em>Activate Template</em> link.
			<?php return;
		} 
		$actions = array(
			'page' => $_GET['page'],
			'taction' => 'update',
		); ?>
		<form name="update-template-form" action="<?php echo add_query_arg($actions,((get_major_version() == 3)?"./sites.php":"./wpmu-admin.php")); ?>" method="post">
		<table class="widefat"> 
			<thead> 
				<tr> 
					<th scope="col">Option Description</th>
					<th scope="col">Site Name</th>
					<th scope="col">Path</th>
					<th scope="col">Children</th>
				</tr> 
			</thead>
		<tbody id="the-template-list" class="list:site">	
		<?php 
		usort($templates,'st_compare_templates');
		$row = 0;
		foreach ($templates as $template) { 
			$row = $row + 1;
			if ($template['active']===1) {
				$id = $template['template']; 
				$blog = get_blog_details($id); ?>
				<tr class='<?php echo (($row%2)?"alternate":""); ?>'>
					<input type="hidden" name="tid[]" value="<?php echo $id; ?>" />
					<td class="column-title"><input name="template-opt-txt[]" type="text" size="60" value="<?php echo $template['option-txt']; ?>" />
					<?php if (!empty($errs[$id])) echo '<br><font color="red">'.$errs[$id].'</font>'; ?>
					<?php if (empty($errs) && $id !== 0) { ?>
						<div class="row-actions">
							<?php if($template['seq']>1) { ?>
								<span class="movefirst"><a href="<?php echo st_get_url('movefirst',$id); ?>">Move First</a></span> |
								<span class="moveup"><a href="<?php echo st_get_url('moveup',$id); ?>">Move Up</a></span> | 
							<?php } ?>
							<?php if($template['seq']<$max_seq) { ?>
								<span class="movedown"><a href="<?php echo st_get_url('movedown',$id); ?>">Move Down</a></span> |
								<span class="movelast"><a href="<?php echo st_get_url('movelast',$id); ?>">Move Last</a></span> | 
							<?php } ?>
							<?php if ($id != 0) { ?>
								<span class="view"><a href="<?php echo $blog->siteurl; ?>">View</a></span> | 
								<span class="backend"><a href="../../..<?php echo $blog->path; ?>wp-admin">Backend</a></span> | 
								<span class="delete"><a href="<?php echo st_get_url('deactivate',$id); ?>">Deactivate</a></span> 
							<?php } ?>
						</div>
					<?php } ?>
					</td>
					<td><?php echo (($id==0)?'&lt;Default&gt;':$blog->blogname); ?></td>
					<td><?php echo $blog->path; ?></td>
					<td><a href="<?php echo  add_query_arg('tstart',0,st_get_url('children',$id)); ?>"><?php echo sizeof($template['children']); ?></a></td>
					
				</tr>
			<?php } ?>
		<?php } ?>
		</tbody></table>	
		<p><input class="button" type="submit" value="Update" /></p>
		</form>
	<?php } else { ?>
		<h2>Children of <?php echo get_blog_details($tid)->blogname; ?></h2>
		<table class="widefat"> 
			<thead> 
				<tr> 
					<th scope="col">Name</th>
					<th scope="col">URL</th>
					<th scope="col">Posts</th>
				</tr> 
			</thead>
		<tbody id="the-template-list" class="list:site">
			<?php 
			$children=$templates[$tid]['children'];
			$start = $_GET['tstart'];
			for ($key=0; $key<15; $key++) {
				if (($start + $key) > sizeof($children)-1) break;
				$child = $children[$start + $key];
				$blogdets = get_blog_details($child);
				$blogname = $blogdets->blogname; ?>
				<tr class='<?php echo (($key%2)?"alternate":""); ?>'>
					
					<td class="column-title"><?php echo $blogname; ?>
					<div class="row-actions">
						<span class="view"><a href="<?php echo $blogdets->siteurl; ?>">View</a></span>
					</div>
					</td>
					<td><?php echo $blogdets->path; ?></td>
					<td><?php echo $blogdets->post_count; ?></td>
				</tr>
			<?php } ?>
		</tbody>
		</table>
		<?php if ($start > 0) {?>
			<a href="<?php echo add_query_arg('tstart',$start - 15) ?>">&lt;&lt; Previous</a>&nbsp;
		<?php } 
		if (($start + 15) < sizeof($children)) { ?>
			<a href="<?php echo add_query_arg('tstart',$start + 15) ?>">Next &gt;&gt;</a>&nbsp;
		<?php } ?>		
		
	<?php } ?>
	</div>
<?php } 
