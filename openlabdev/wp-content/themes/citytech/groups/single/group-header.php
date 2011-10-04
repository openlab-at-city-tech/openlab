<?php
global $bp;
$wds_group_meta = groups_get_groupmeta( $bp->groups->current_group->id, 'wds_group_type' );
		if( $wds_group_meta == 'course') {
			locate_template( array( 'groups/courses/single/header.php' ), true );
		} elseif( $wds_group_meta == 'project') {
			locate_template( array( 'groups/projects/single/header.php' ), true );
		} elseif( $wds_group_meta == 'club') {
			locate_template( array( 'groups/clubs/single/header.php' ), true );
		} else {
			echo 'test';
		}
?>