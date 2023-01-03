<?php
/**
 * Widgets Initialize
 *
 * @package badgeos
 */

add_action( 'widgets_init', 'badgeos_register_widgets' );

/**
 * Widgets Initialize
 *
 * @package badgeos
 */
function badgeos_register_widgets() {

	register_widget( 'Earned_User_Achievements_Widget' );
	register_widget( 'Earned_User_Ranks_Widget' );
	register_widget( 'Earned_User_Points_Widget' );
}

require_once badgeos_get_directory_path() . 'includes/widgets/earned-user-achievements-widget.php';
require_once badgeos_get_directory_path() . 'includes/widgets/earned-user-ranks-widget.php';
require_once badgeos_get_directory_path() . 'includes/widgets/earned-user-points-widget.php';
