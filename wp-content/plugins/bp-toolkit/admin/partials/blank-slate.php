<?php

/**
 * Displays onboarding message in the event of an empty report list table.
 *
 * @since 2.0
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

?>

<div class="bptk-blank-slate">
    <img class="bptk-blank-slate-image" src="<?php 
echo  plugin_dir_url( __FILE__ ) . '../assets/images/main.png' ;
?>"
         alt="<?php 
_e( 'Block, Suspend, Report Logo', 'bp-toolkit' );
?>">
    <h2 class="bptk-blank-slate-heading"><?php 
_e( 'Fantastic! No one has submitted a report yet.', 'bp-toolkit' );
?></h2>
	<?php 
echo  '<p class="bptk-blank-slate-message">' . __( 'In the Pro Edition, site administrators can also submit their own reports.', 'bp-toolkit' ) . '</p>' ;
echo  '<a class="bptk-blank-slate-cta button button-primary" href="' . bptk_fs()->get_upgrade_url() . '">' . __( 'Upgrade Now', 'bp-toolkit' ) . '</a>' ;
?>
    <p class="bptk-blank-slate-help"><?php 
_e( 'Need help? Get started at our ', 'bp-toolkit' );
?><a
                href="<?php 
echo  BP_TOOLKIT_SUPPORT ;
?>"><?php 
_e( 'support page', 'bp-toolkit' );
?></a>.</p>
</div>
