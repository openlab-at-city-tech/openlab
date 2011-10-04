<div>

<div class="center-column-sidebar">

<ul id="sidebar">
<?php if ( !function_exists('dynamic_sidebar')
		|| !dynamic_sidebar() ) : ?>
	<li id="links" class="widget">
		<h2 class="widgettitle">Links</h2>
		<ul>
			<li><a href="http://wordpress.com">Wordpress</a></li>
			<li><a href="http://wordpress.org/development/">Development Blog</a></li>
			<li><a href="http://wordpress.org/extend/plugins/">Plugins</a></li>
			<li><a href="http://wordpress.org/extend/themes/">Themes</a></li>
			<li><a href="http://wordpress.org/extend/ideas/">Suggest Ideas</a></li>
			<li><a href="http://wordpress.org/support/">Support Forum</a></li>
		</ul>
	</li>
	<li id="tag_cloud" class="widget">
	<h2 class="widgettitle">Tag Cloud</h2>
	<?php wp_tag_cloud(); ?>
	</li>
	<li id="categories" class="widget widget_categories">
		<h2 class="widgettitle">Categories</h2>
		<ul>
			<?php wp_list_categories('title_li='); ?>
		</ul>
	</li>
<?php endif; ?>
</ul>