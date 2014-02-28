<?php get_header(); ?>
	<div id="main" class="page clearfix">			 
	<div id="content" class="clearfix">
		<?php while (have_posts()) : the_post(); ?>
			    
		<div id="post-<?php the_ID(); ?>" <?php post_class('project'); ?>>																				
			<div class="right">
				<div class="inside">
				<h1><?php the_title(); ?></h1>
				<ul class="skillList clearfix"><?php ttrust_get_terms_list(); ?></ul> 
								
				<?php $project_notes = get_post_meta($post->ID, "_ttrust_notes_value", true); ?>
				<?php echo wpautop($project_notes); ?>
				
				<?php $project_url = get_post_meta($post->ID, "_ttrust_url_value", true); ?>
				<?php $project_url_label = get_post_meta($post->ID, "_ttrust_url_label_value", true); ?>
				<?php $project_url_label = ($project_url_label!="") ? $project_url_label : __('Visit Site', 'themetrust'); ?>
				<?php if ($project_url) : ?>
					<p><a class="action" href="<?php echo $project_url; ?>"><?php echo $project_url_label; ?> &rarr; </a></p>
				<?php endif; ?>
				</div>
				<?php include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); ?>
				<?php if(is_plugin_active( 'previous-and-next-post-in-same-taxonomy/previous-and-next-post-in-same-taxonomy.php' )) : ?>
				<div class="projectNav clearfix">
					<div class="inactive next">
						<?php be_next_post_link('%link', 'next', true, '', 'skill'); ?>
					</div>
					<div class="inactive previous">
						<?php be_previous_post_link('%link', 'previous', true, '', 'skill'); ?>
					</div>
				</div> <!-- end navigation -->
				<?php else : ?>
				<div class="projectNav clearfix">
					<div class="next <?php if(!get_next_post()){ echo 'inactive'; }?>">						
						<?php next_post_link('%link', 'next'); ?>				
					</div>
					<div class="previous <?php if(!get_previous_post()){ echo 'inactive'; }?>">
						<?php previous_post_link('%link', 'previous'); ?>
					</div>
				</div> <!-- end navigation -->
				<?php endif; ?>
					
			</div>																							
			<div class="left">
				<?php the_content(); ?>
				<?php comments_template('', true); ?>
			</div>
		</div>			
		<?php endwhile; ?>					    	
	</div>					
	</div>
<?php get_footer(); ?>