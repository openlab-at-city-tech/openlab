<?php
/**
 * Line - Line Grow
 */

$classlist['link'] .= 'met_d--block met_pos--relative';

$classlist['span'] = 'met_d--block editor:met_color met_transition--300ms met_opacity--30 a:hover:met_opacity--100 active:_:met_opacity--100 met_scale--' . $nav_pos;

$classlist['tooltip'] .= 'nav_tooltip met_d--none met_pos--absolute met_px--8 met_py--5 met_color--white editor:met_bgc met_radius--4 met_fs--14 met_lh--16 met_text--nowrap met_transition--300ms met_opacity--0 active:_:met_opacity--100 ';

$classlist['arrow'] .= 'met_d--none met_pos--absolute editor:met_color met_bd--6 met_bdc--none met_transition--300ms met_opacity--0 active:_:met_opacity--100 ';

if ( $nav_pos === 'top' || $nav_pos === 'bottom' ) :
	$classlist['span'] .= ' met_h--48 met_bdl--6 active:_:met_scaleY--20';

	$classlist['tooltip'] .= 'met_left--50p met_translateLeft--m50p met_my--m28 ';
	$classlist['arrow']   .= 'met_left--50p met_translateLeft--m50p met_my--m40 ';
else :
	$classlist['span'] .= ' met_w--48 met_bdt--6 active:_:met_scaleX--20';

	$classlist['tooltip'] .= 'met_top--50p met_translateTop--m50p met_mx--m28 ';
	$classlist['arrow']   .= 'met_top--50p met_translateTop--m50p met_mx--m40 ';
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
