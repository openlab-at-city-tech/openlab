<?php /*
Template Name: Portfolio - paginated
*/ ?>
<?php get_header(); ?>	
			<?php if(!is_front_page()):?>
			<div id="pageHead" class="withBorder">
				<h1><?php the_title(); ?></h1>
				<?php $page_description = get_post_meta($post->ID, "_ttrust_page_description_value", true); ?>
				<?php if ($page_description) : ?>
					<p><?php echo $page_description; ?></p>
				<?php endif; ?>				
			</div>
			<?php endif; ?>			

			<div id="content" class="fullProjects clearfix full grid">									
				<?php while (have_posts()) : the_post(); ?>											
					<?php the_content(); ?>														
				<?php endwhile; ?>				
				
				<div id="projects" class="clearfix">		

					<?php $page_skills = get_post_meta($post->ID, "_ttrust_page_skills_value", true); ?>

					<?php if ($page_skills) : // if there are a limited number of skills set ?>
						<?php $skill_slugs = ""; $skills = explode(",", $page_skills); ?>

						<?php if (sizeof($skills) > 1) : // if there is more than one skill ?>								
							<?php $skill_slugs = substr($skill_slugs, 0, strlen($skill_slugs)-1); ?>
						<?php else: ?>
							<?php $skill = $skills[0]; ?>
							<?php $s = get_term_by( 'name', trim(htmlentities($skill)), 'skill'); ?>
							<?php if($s) { $skill_slugs = $s->slug; } ?>
						<?php endif; 	

						query_posts( 'skill='.$skill_slugs.'&post_type=project&posts_per_page=9&paged='.$paged );

					else : // if not, use all the skills ?>
						
						<?php query_posts( 'post_type=project&paged='.$paged );

					endif; ?>

					<div class="thumbs">			
					<?php  while (have_posts()) : the_post(); ?>

						<?php
						global $p;				
						$p = "";
						$skills = get_the_terms( $post->ID, 'skill');
						if ($skills) {
						   foreach ($skills as $skill) {				
						      $p .= $skill->slug . " ";						
						   }
						}
						?>  	
						<?php get_template_part( 'part-project-thumb'); ?>		

					<?php endwhile; ?>
					</div>
				</div>

				<?php get_template_part( 'part-pagination'); ?>			
			</div>
	
<?php get_footer(); ?>