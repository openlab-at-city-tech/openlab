<?php
/**
* front page
*
* Note to themers: home-right appears before home-left in this template file,
* to make responsive styling easier
*/

get_header(); ?>

<div id="content" class="hfeed">
	<div id="home-right">
		<?php dynamic_sidebar('pgw-gallery'); ?>

		<div id="home-group-list-wrapper">
			<?php cuny_home_square( 'course' ); ?>
			<?php cuny_home_square( 'project' ); ?>
			<?php cuny_home_square( 'club' ); ?>
			<?php cuny_home_square( 'portfolio' ); ?>
			<div class="clearfloat"></div>
			<script type='text/javascript'>(function($){ $('.activity-list').css('visibility','hidden'); })(jQuery);</script>";
		</div>
	</div>

	<div id="home-left">
		<div id="cuny_openlab_jump_start">
			<?php cuny_home_login(); ?>
		</div>

		<?php dynamic_sidebar( 'cac-featured' ) ?>

		<div class="box-1" id="whos-online">
			<h3 class="title">Who's Online?</h3>
			<?php cuny_whos_online(); ?>
		</div>

		<?php cuny_home_new_members(); ?>
	</div>

</div><!--content-->

<?php get_footer();

