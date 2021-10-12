<?php

/**
 * Flush user cache after BP manually updates password.
 */
add_action( 'bp_core_activated_user', function( $user_id ) {
        wp_cache_delete( $user_id, 'users' );
} );

function cac_set_return_path_header( $phpmailer ) {
       $phpmailer->Sender = 'wordpress@openlab.citytech.cuny.edu';
       return $phpmailer;
}
add_action( 'phpmailer_init', 'cac_set_return_path_header' );

/**
 * Fix for PDF embedding in Hypothesis.
 *
 * https://github.com/hypothesis/wp-hypothesis/pull/27/
 * http://redmine.citytech.cuny.edu/issues/2115
 */
function openlab_hypothesis_hotfix() {
	if ( ! function_exists( 'add_hypothesis' ) ) {
		return;
	}

	wp_enqueue_script( 'openlab-hypothesis', home_url( 'wp-content/mu-plugins/js/hypothesis.js' ), array(), '', true );
	$uploads = wp_upload_dir();
	wp_localize_script( 'openlab-hypothesis', 'HypothesisPDF', array(
		'uploadsBase' => trailingslashit( $uploads['baseurl'] ),
	) );
}
add_action( 'wp', 'openlab_hypothesis_hotfix', 20 );

/**
 * Load scripts for Fixed TOC fixes.
 */
add_action(
	'wp_enqueue_scripts',
	function() {
		if ( ! class_exists( 'Fixedtoc_Frontend_Control' ) ) {
			return;
		}
		wp_enqueue_script( 'openlab-fixed-toc', home_url( 'wp-content/mu-plugins/js/fixed-toc.js' ), array('jquery'), OL_VERSION, true );
	}
);

/**
 * Init TablePress caps.
 *
 * See #2498.
 */
add_action(
	'admin_init',
	function() {
		// Plugin not active.
		if ( ! class_exists( 'TablePress' ) ) {
			return;
		}

		// User doesn't have the caps, so don't bother checking.
		if ( ! current_user_can( 'publish_posts' ) ) {
			return;
		}

		// User already has the TP caps, so nothing to do.
		if ( current_user_can( 'tablepress_edit_tables' ) ) {
			return;
		}

		TablePress::$model_options->add_access_capabilities();
	}
);

/**
 * Hide update notices on plugins.php from non-super-admins.
 */
add_action(
	'admin_print_scripts',
	function() {
		if ( ! class_exists( 'GFCommon' ) ) {
			return;
		}

		if ( is_super_admin() ) {
			return;
		}

		?>
<style type="text/css">
.plugins tr[data-plugin="gravityforms/gravityforms.php"] td.plugin-update,
.plugins tr[data-plugin="gravityformsdropbox/dropbox.php"] td.plugin-update,
.plugins tr[data-plugin="gf-image-choices/gf-image-choices.php"] td.plugin-update,
.plugins tr[data-plugin="gravityformsquiz/quiz.php"] td.plugin-update,
.plugins tr[data-plugin="gravityformssurvey/survey.php"] td.plugin-update,
.plugins tr[data-plugin="gravityformszapier/zapier.php"] td.plugin-update {
	padding-bottom: 1px;
}

tr[data-plugin="gravityforms/gravityforms.php"] td.plugin-update .update-message,
tr[data-plugin="gravityformsdropbox/dropbox.php"] td.plugin-update .update-message,
tr[data-plugin="gf-image-choices/gf-image-choices.php"] td.plugin-update .update-message,
tr[data-plugin="gravityformsquiz/quiz.php"] td.plugin-update .update-message,
tr[data-plugin="gravityformssurvey/survey.php"] td.plugin-update .update-message,
tr[data-plugin="gravityformszapier/zapier.php"] td.plugin-update .update-message {
	display: none;
}

tr.plugin-update-tr .gf_image_choices-plugin-update {
	display: none;
}
</style>
		<?php
	}
);
