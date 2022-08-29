<?php
/**
 * Circle - Dot Move
 */

$classlist['li'] .= 'before:met_pos--absolute before:met_w--16 before:met_h--16 before:met_transition--300ms before:met_radius--circle before:met_top--0 before:met_left--0 editor:before:met_bgc';

$classlist['link'] .= 'met_d--block met_pos--relative';

$classlist['span'] = 'met_d--block met_w--16 met_h--16 editor:met_bgc met_radius--circle met_transition--300ms met_opacity--30 a:hover:met_opacity--100';

$classlist['tooltip'] .= 'nav_tooltip met_d--none met_pos--absolute met_px--8 met_py--5 met_color--white editor:met_bgc met_radius--4 met_fs--14 met_lh--16 met_text--nowrap met_transition--300ms met_opacity--0 a:hover:met_opacity--100 active:_:met_opacity--100 ';

$classlist['arrow'] .= 'met_d--none met_pos--absolute editor:met_color met_bd--6 met_bdc--none met_transition--300ms met_opacity--0 a:hover:met_opacity--100 active:_:met_opacity--100 ';

if ( $nav_pos === 'top' || $nav_pos === 'bottom' ) :
	$classlist['tooltip'] .= 'met_left--50p met_translateLeft--m50p met_my--12 ';
	$classlist['arrow']   .= 'met_left--50p met_translateLeft--m50p ';
else :
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
