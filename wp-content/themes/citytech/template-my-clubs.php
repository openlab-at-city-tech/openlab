<?php /* Template Name: My Clubs */

remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'cuny_my_clubs' );

function cuny_my_clubs() {
	echo cuny_profile_activty_block('club', 'My Clubs', ''); ?>
<?php }


function cuny_profile_activty_block($type,$title,$last) { 
	global $wpdb,$bp, $ribbonclass;

	$ids="9999999";
	  $sql="SELECT a.group_id,b.content FROM {$bp->groups->table_name_groupmeta} a, {$bp->activity->table_name} b where a.group_id=b.item_id and a.meta_key='wds_group_type' and a.meta_value='".$type."' and b.user_id=".$bp->loggedin_user->id." ORDER BY b.date_recorded desc LIMIT 3";
	  $rs = $wpdb->get_results($sql);
	  foreach ( (array)$rs as $r ){
		  $activity[]=$r->content;
		  $ids.= ",".$r->group_id;
	  }
	  
	  echo  '<h1 class="entry-title">My Clubs</h1>';

	  if ( bp_has_groups( 'include='.$ids.'&per_page=3&max=3' ) ) : ?>
<ul id="club-list" class="item-list">
		<?php 
		$count = 1;
		while ( bp_groups() ) : bp_the_group(); 
			$group_id=bp_get_group_id();?>
			<li class="club<?php echo cuny_o_e_class($count) ?>">
				<div class="item-avatar alignleft">
					<a href="<?php bp_group_permalink() ?>"><?php echo bp_get_group_avatar(array( 'type' => 'full', 'width' => 100, 'height' => 100 )) ?></a>
				</div>
				<div class="item">
					<h2 class="item-title"><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></h2>
					<?php 
					$wds_faculty=groups_get_groupmeta($group_id, 'wds_faculty' );
					$wds_club_code=groups_get_groupmeta($group_id, 'wds_club_code' );
					$wds_semester=groups_get_groupmeta($group_id, 'wds_semester' );
		  			$wds_year=groups_get_groupmeta($group_id, 'wds_year' );
		  			$wds_departments=groups_get_groupmeta($group_id, 'wds_departments' );
					?>
                    <div class="info-line"><?php echo $wds_faculty; ?> | <?php echo $wds_departments;?> | <?php echo $wds_club_code;?><br /> <?php echo $wds_semester;?> <?php echo $wds_year;?></div>
					<?php
					     $len = strlen(bp_get_group_description());
					     if ($len > 135) {
						$this_description = substr(bp_get_group_description(),0,135);
						$this_description = str_replace("</p>","",$this_description);
						echo $this_description.'&hellip; (<a href="'.bp_get_group_permalink().'">View More</a>)</p>';
					     } else {
						bp_group_description();
					     }
					?>
				</div>
				
			</li>
			<?php if ( $count % 2 == 0 ) { echo '<hr style="clear:both;" />'; } ?>
			<?php $count++ ?>
		<?php endwhile; ?>
	</ul>

<?php else: ?>

	<div class="widget-error">
		<?php _e('There are no groups to display.', 'buddypress') ?>
	</div>

<?php endif; ?>

		<div class="pagination-links" id="group-dir-pag-top">
			<?php bp_groups_pagination_links() ?>
		</div><?php

}

genesis();
