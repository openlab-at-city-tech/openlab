<?php
/**
 * Circle - Stroke Simple
 */

$classlist['li'] .= 'met_pos--relative before:met_content after:met_content before:met_pos--absolute after:met_pos--absolute editor:met_color before:met_opacity--30 after:met_opacity--30 before:last:met_d--none after:first:met_d--none after:active:met_d--none ';

$classlist['link'] .= 'met_d--block met_pos--relative';

$classlist['span'] .= 'met_d--block met_w--12 met_h--12 met_bd--2 editor:met_bdc active:_:met_bd--1 met_opacity--30 a:hover:met_opacity--60 active:_:met_opacity--100 met_radius--circle met_transition--300ms active:_:met_scale--150';

$classlist['tooltip'] .= 'nav_tooltip met_d--none met_pos--absolute met_px--8 met_py--5 met_color--white editor:met_bgc met_radius--4 met_fs--14 met_lh--16 met_text--nowrap met_transition--300ms met_opacity--0 a:hover:met_opacity--100 active:_:met_opacity--100 ';

$classlist['arrow'] .= 'met_d--none met_pos--absolute editor:met_color met_bd--6 met_bdc--none met_transition--300ms met_opacity--0 a:hover:met_opacity--100 active:_:met_opacity--100 ';

if ( $nav_pos === 'top' || $nav_pos === 'bottom' ) :
	$classlist['li'] .= 'before:met_w--19 active:before:met_w--18 before:met_top--5 before:met_left--12 before:active:met_left--13 before:met_bdt--2 after:met_bdt--2 after:met_w--1 after:met_left--m1 after:met_top--5';

	$classlist['tooltip'] .= 'met_left--50p met_translateLeft--m50p met_my--12 ';
	$classlist['arrow']   .= 'met_left--50p met_translateLeft--m50p ';
else :
	$classlist['li'] .= 'before:met_h--19 active:before:met_h--18 before:met_left--5 before:met_top--12 before:active:met_top--13 before:met_bdl--2 after:met_bdl--2 after:met_h--1 after:met_top--m1 after:met_left--5';
	
	$classlist['tooltip'] .= 'met_top--50p met_translateTop--m50p met_mx--12 ';
	$classlist['arrow']   .= 'met_top--50p met_translateTop--m50p ';
endif;

?>
<div class="onepage_scroll_nav <?php echo esc_attr( $classlist['wrapper'] ); ?>">
	<ul class="<?php echo esc_attr( $classlist['ul'] ); ?>">
		<li data-menuanchor="section_1" class="<?php echo esc_attr( $classlist['li'] ); ?>">
			<a href="#section_1" class="<?php echo esc_attr( $classlist['link'] ); ?>">
				<span class="<?php echo esc_attr( $classlist['span'] ); ?>"></span>

				<span class="<?php echo esc_attr( $classlist['tooltip'] ); ?>"></span>
				<span class="<?php echo esc_attr( $classlist['arrow'] ); ?>"></span>
			</a>
		</li>
	</ul>
</div>
