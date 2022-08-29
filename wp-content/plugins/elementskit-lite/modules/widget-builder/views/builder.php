<?php

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We are taking the post id from the URL. The page only can access admin. So nonce verification is not required.
$id = ( isset( $_GET['post'] ) ? intval( wp_unslash( $_GET['post'] ) ) : 0 );
?>
<script>
	var ekitWidgetBuilder = {
		api: '<?php echo esc_url( get_rest_url() . 'elementskit/v1/widget-builder/' ); ?>',
		pull_id: <?php echo intval( $id ); ?>,
		nonce: '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>',
		live_url: '<?php echo esc_url( str_replace( array( '&amp;', 'action=edit' ), array( '&', 'action=elementor' ), get_edit_post_link( $id ) ) ); ?>',
		pro: <?php echo \ElementsKit_Lite::package_type() == 'pro' ? 'true' : 'false'; ?>,
		assets: {
			'wysiwyg': '<?php echo esc_url( $this->url . 'assets/img/wysiwyg.png' ); ?>',
			'noImagePreview': '<?php echo esc_url( $this->url . 'assets/img/no-image.png' ); ?>',
			'imagePreviewTrans': '<?php echo esc_url( $this->url . 'assets/img/transparent_bg.png' ); ?>'
		}
	};
</script>


<div id="ekitWidgetBuilderApp"></div>
