<!-- onboard_steps nav begins -->
<?php

use ElementsKit_Lite\Libs\Framework\Classes\Onboard_Status;

// redirect to plugin home page if onboard already has completed
Onboard_Status::instance()->exit_from_onboard();

echo '<ul class="ekit-onboard-nav"><div class="ekit-onboard-progressbar"></div>';
$count = 1;
foreach ( $onboard_steps as $step_key => $step ) :
	$icon  = ! empty( $step['icon'] ) ? $step['icon'] : '';
	$title = ! empty( $step['title'] ) ? $step['title'] : '';
	?>
	<li data-step_key="<?php echo esc_attr( $step_key ); ?>"
		class="ekit-onboard-nav-item 
		<?php 
		echo $count === 1 ? 'active' : '';
		echo $count === count( $onboard_steps ) ? 'last' : ''; 
		?>
		">
		<?php if ( ! empty( $icon ) ) : ?>
			<i class="ekit-onboard-nav-icon <?php echo esc_attr( $icon ); ?>"></i>
		<?php endif; ?>
		<?php if ( ! empty( $title ) ) : ?>
			<span class="ekit-onboard-nav-text"><?php echo esc_html( $title ); ?></span>
		<?php endif; ?>
	</li>
	<?php 
	$count ++;
endforeach;
echo '</ul>';
?>
<!-- onboard_steps nav ends -->

<!-- onboard_steps content begins -->
<?php foreach ( $onboard_steps as $step_key => $step ) : ?>

	<!-- includes view file for this step -->
	<?php
	$path = isset( $step['view_path'] )
		? $step['view_path']
		: self::get_dir() . 'views/onboard-steps/' . $step_key . '.php';

	if ( file_exists( $path ) ) {
		echo '<div class="ekit-onboard-step-wrapper ekit-onboard-' . esc_attr( $step_key ) . '">';
		include $path;
		echo '</div>';
	} 
	?>

<?php endforeach; ?>
<!-- onboard_steps content ends -->


