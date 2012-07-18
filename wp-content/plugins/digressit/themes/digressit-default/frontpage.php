<?php get_header(); ?>

<div id="container">
<?php get_dynamic_widgets(); ?>

<?php get_stylized_title(); ?>

<div id="content">
	<div id="frontpage">
		<div class="entry">
		
		<?php
		if(is_active_sidebar('frontpage-content')){
			dynamic_sidebar('Frontpage Content');
		}
		elseif(has_action('custom_digressit_frontpage')){
			do_action('custom_digressit_frontpage');
		}		
		else{
			?>
			
			<h2>Congratulations on installing the Multi-site edition of Digress.it</h2>
			
			<p>This is the frontpage to your Multi-site community.</p><br>
			<p>To edit this page you can take one of the following actions:<p>
			<ul style="width: 75%">
				<li>Login as an the admin and edit your "<a href="<?php bloginfo('url') ?>/wp-admin/options-reading.php">Reading Settings</a>." Choose "A static page" option and select the page you want to display here</li>
				<li>You can also edit (as admin) your "<a href="<?php bloginfo('url') ?>/wp-admin/widgets.php">Widgets</a>." Just drag your widgets to the "Frontpage Content" slot.</li>
				<li>And for the programmatically inclined, you can use the "custom_digressit_frontpage" <a href="http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters">hook</a></li>
			</ul>
			<?php
		}
		?>
		<div class="clear"></div>		
		</div>
	</div>
</div>

</div>
<?php get_footer(); ?>