<?php $component = \ColibriWP\Theme\View::getData( 'component' ); ?>
<a class="<?php echo esc_attr( \ColibriWP\Theme\View::getData( 'wrapper_class' ) ); ?>"
   data-kubio="kubio/logo" href="<?php echo $component->getHomeUrl(); ?>">
	<?php if ( $component->getLayoutType() === 'image' ) : ?>
		<img class="<?php echo esc_attr( \ColibriWP\Theme\View::getData( 'logo_image_class' ) ); ?>"
			 src="<?php echo $component->customLogoUrl(); ?>"/>
		<img class="<?php echo esc_attr( \ColibriWP\Theme\View::getData( 'alt_logo_image_class' ) ); ?>"
			 src="<?php echo $component->alternateLogoUrl(); ?>"/>
	<?php else : ?>
	<span class="<?php echo esc_attr( \ColibriWP\Theme\View::getData( 'logo_text_class' ) ); ?>">
		<?php $component->printTextLogo(); ?>
	</span>
	<?php endif; ?>
</a>
