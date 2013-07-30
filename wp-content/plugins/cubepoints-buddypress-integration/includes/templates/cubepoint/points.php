<?php get_header('buddypress') ?>

	<div id="content">
		<div class="padder">

			<div id="item-header">
				<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div>

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav() ?>
					</ul>
				</div>
			</div>

			<div id="item-body">

				<div class="item-list-tabs no-ajax" id="subnav">
					<ul>
						<?php bp_get_options_nav() ?>
					</ul>
				</div>
				
				<?php global $bp; cp_show_logs($bp->displayed_user->id, get_option('bp_points_logs_per_page_cp_bp'), false);

				if(get_option('bp_tallyuserpoints_cp_bp')) {
					define('CBALLPTS', $wpdb->prefix . 'usermeta');
					$allpoints = $wpdb->get_var( $wpdb->prepare( 'select sum(`meta_value`) from '.CBALLPTS.' where `meta_key`=\'cpoints\'' ) );
					$yourpoints = cp_getPoints($bp->displayed_user->id);
					$yourworth1 = $yourpoints / $allpoints;
					$yourworth2 = $yourworth1 * 100;
					echo '
					<p class="cbbpimgawards" style="padding:20px;">'. __('Points earned by all members is', 'cp_buddypress').' '.number_format($allpoints).'</p>
					<p class="cbbpimgawards" style="padding:20px;">'. __('Your percentage of all points earned by members is', 'cp_buddypress').' '.number_format($yourworth2, 2, '.', '').' %</p>
					';
				}
				?>
				
				
			</div><!-- #item-body -->
		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_sidebar('buddypress') ?>

<?php get_footer('buddypress') ?>