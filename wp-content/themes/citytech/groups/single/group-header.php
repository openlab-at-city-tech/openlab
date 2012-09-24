<?php
$wds_group_meta = groups_get_groupmeta( bp_get_current_group_id(), 'wds_group_type' );

switch ( $wds_group_meta ) {
	case 'course' :
		locate_template( array( 'groups/courses/single/header.php' ), true );
		break;

	case 'project' :
		locate_template( array( 'groups/projects/single/header.php' ), true );
		break;

	case 'club' :
		locate_template( array( 'groups/clubs/single/header.php' ), true );
		break;

	default :
		echo 'test';
}
