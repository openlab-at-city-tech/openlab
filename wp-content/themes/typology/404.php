<?php get_header(); ?>

<div id="typology-cover" class="typology-cover typology-cover-empty"></div>

<div class="typology-fake-bg">
	
	<div class="typology-section">
		<?php get_template_part('template-parts/ads/top'); ?>
		
		<div class="section-content">
			
			<article <?php post_class( 'typology-post' ); ?>>

		        <header class="entry-header">
		            <h1><?php echo esc_html( __typology( '404_title') ); ?></h1>
		            <div class="post-letter"><?php echo typology_get_letter( esc_html( __typology( '404_title') ) ); ?></div>
		        </header>
		        
		        <div class="entry-content">
		            <p><?php echo esc_html( __typology( '404_text') ); ?></p>
		            <?php get_search_form(); ?>
		        </div>

	    	</article>

		</div>
		
		<?php get_template_part('template-parts/ads/bottom'); ?>
	</div>

<?php get_footer(); ?>