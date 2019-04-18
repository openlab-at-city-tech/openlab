<?php $footer_widgets = johannes_get( 'footer', 'widgets' ); ?>

<?php if ( !empty( $footer_widgets ) ): ?>
	<div class="row johannes-footer-widgets justify-content-center">
	    <?php foreach ( $footer_widgets as $i => $column ) :?>
	        <?php if ( is_active_sidebar( 'johannes_sidebar_footer_'.( $i+1 ) ) ): ?>
	            <div class="col-12 col-md-6 <?php echo esc_attr( 'col-lg-' . $column ); ?>">
		            <?php dynamic_sidebar( 'johannes_sidebar_footer_'.( $i+1 ) ); ?>
	            </div>
	        <?php endif; ?>
	    <?php endforeach; ?>
	</div>
<?php endif; ?>
