<?php /*

Template Name: Archives

*/ ?>

<?php get_header(); ?>

<div class="post">

	<div class="post-info">

		<h1>Search</h1>

	</div>

	<div class="post-content">
		<?php get_search_form(); ?>
	</div>

	<div class="clearboth"><!-- --></div>

</div>

<div class="post">

	<div class="post-info">
		<h1>Archives by Month</h1>
	</div>

	<div class="post-content">
		<ul class="archives">
			<?php wp_get_archives('type=monthly'); ?>
		</ul>
	</div>

	<div class="clearboth"><!-- --></div>

</div>

<div class="post">

	<div class="post-info">
		<h1>Archives by Tag</h1>
	</div>

	<div class="post-content">
		<p><?php wp_tag_cloud(''); ?></p>
	</div>

	<div class="clearboth"><!-- --></div>

</div>

<div class="post">

	<div class="post-info">
		<h1>Archives by Category</h1>
	</div>

	<div class="post-content">
		<ul class="archives">
			 <?php wp_list_categories('title_li='); ?>
		</ul>
	</div>

	<div class="clearboth"><!-- --></div>

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
