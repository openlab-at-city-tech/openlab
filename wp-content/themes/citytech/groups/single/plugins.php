<?php 
remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'cuny_group_single' );

function cuny_group_single() { ?>
	
	<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

			<?php do_action( 'bp_before_group_plugin_template' ) ?>
			
			<?php locate_template( array( 'groups/single/group-header.php' ), true ); ?>
			
			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php do_action( 'bp_group_plugin_options_nav' ) ?>
					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body">

				<?php do_action( 'bp_before_group_body' ) ?>

				<?php do_action( 'bp_template_content' ) ?>

				<?php do_action( 'bp_after_group_body' ) ?>
			</div><!-- #item-body -->

			<?php endwhile; endif; ?>

			<?php do_action( 'bp_after_group_plugin_template' ) ?>

<? }
		
add_action('genesis_before_sidebar_widget_area', 'cuny_buddypress_group_actions');
function cuny_buddypress_group_actions() { ?>
<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>
		<div id="item-buttons">
			<h2 class="sidebar-header"><?php echo ucwords(groups_get_groupmeta( bp_get_group_id(), 'wds_group_type' )) ?></h2>
			<?php do_action( 'bp_group_header_actions' ); ?>
			<ul>
				<?php $menu=cuny_get_options_nav();
				//echo $menu;
				//echo "<xmp>".$menu."</xmp>";?>
			</ul>
			<?php do_action( 'bp_group_options_nav' ) ?>

		</div><!-- #item-buttons -->

<?php endwhile; endif; ?>
<?php }

genesis();