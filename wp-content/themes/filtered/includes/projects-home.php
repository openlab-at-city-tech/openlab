<?php $ttrust_featured_on_home = of_get_option('ttrust_featured_on_home'); ?>	
<?php if($ttrust_featured_on_home) : //show only featured projects ?>
	
	<?php $skills_nav = array(); ?>
	<?php query_posts( 'post_type=projects&posts_per_page=200&meta_key=_ttrust_home_featured_value&meta_value=true' ); ?>
	
	<?php  while (have_posts()) : the_post(); ?>	   			    
		<?php 				
		$s = "";
		$skills = get_the_terms( $post->ID, 'skill');
		if ($skills) {
		   foreach ($skills as $skill) {
			  if (isset($skills_nav[$skill->term_id])) {
			  	continue;
			  }
			  $skills_nav[$skill->term_id] = $skill;		      		  		
		   }		   
		}		

		?>
	<?php endwhile; ?>
	
	<ul id="filterNav" class="clearfix">				
		<li class="allBtn"><a href="#" data-filter="*" class="selected">All</a></li>
		<?php
		$j=1;		  
		  foreach ($skills_nav as $skill) {
		  	$a = '<li class="'.$skill->slug.'Btn"><a href="#" data-filter=".'.$skill->slug.'">';
			$a .= $skill->name;					
			$a .= '</a></li>';
			echo $a;
			echo "\n";
			$j++;
		  }
		 ?>								
	</ul>
	
	<?php  include( TEMPLATEPATH . '/includes/project-grid.php'); ?>		
		
<?php else: //show all projects ?>	
			
	<ul id="filterNav" class="clearfix">
		<li class="allBtn"><a href="#" data-filter="*" class="selected">All</a></li>	
		<?php
		$categories =  get_categories('taxonomy=skill'); 										
		foreach ($categories as $category) {					
			$a = '<li class="'.$category->slug.'Btn"><a href="#" data-filter=".'.$category->slug.'">';
			$a .= $category->name;					
			$a .= '</a></li>';
			echo $a;
			echo "\n";								
		 }
		 ?>					
	</ul>
	
	<?php query_posts( 'post_type=projects&posts_per_page=200' ); ?>
	
	<?php  include( TEMPLATEPATH . '/includes/project-grid.php'); ?>
		
<?php endif; ?>
	
