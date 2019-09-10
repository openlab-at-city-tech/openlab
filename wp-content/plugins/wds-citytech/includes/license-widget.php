<?php

add_action(
	'widgets_init',
	function() {
		register_widget( 'OpenLab_License_Widget' );
	}
);

class OpenLab_License_Widget extends WP_Widget {
	protected $licenses = [
		'by' => [
			'label' => 'Attribution (CC BY)',
			'url'   => 'https://creativecommons.org/licenses/by/4.0',
		],
		'by-sa' => [
			'label' => 'Attribution-ShareAlike (CC BY-SA)',
			'url'   => 'https://creativecommons.org/licenses/by-sa/4.0',
		],
		'by-nd' => [
			'label' => 'Attribution-NoDerivs (CC BY-ND)',
			'url'   => 'http://creativecommons.org/licenses/by-nd/4.0',
		],
		'by-nc' => [
			'label' => 'Attribution-NonCommercial (CC BY-NC)',
			'url'   => 'http://creativecommons.org/licenses/by-nc/4.0',
		],
		'by-nc-sa' => [
			'label' => 'Attribution-NonCommercial-ShareAlike (CC BY-NC-SA)',
			'url'   => 'http://creativecommons.org/licenses/by-nc-sa/4.0',
		],
		'by-nc-nd' => [
			'label' => 'Attribution-NonCommercial-NoDerivs (CC BY-NC-ND)',
			'url'   => 'http://creativecommons.org/licenses/by-nc-nd/4.0',
		],
		'cc-zero' => [
			'label' => 'Public Domain, CC0',
			'url'   => 'http://creativecommons.org/publicdomain/zero/1.0/',
		],
		'publicdomain' => [
			'label' => 'Public Domain',
			'url'   => 'https://wiki.creativecommons.org/Public_domain',
		],
	];

	public function __construct() {
		parent::__construct(
			'openlab_license',
			'Creative Commons License',
			array(
				'description' => '',
			)
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		$license_slug = $instance['license'];
		$license_data = $this->licenses[ $license_slug ];

		printf(
			'<a class="cc-widget-icon-link" href="%s"><img src="%s" alt="%s" /></a><p class="cc-widget-text">Except where otherwise noted, content on this site is made available under the terms of the <strong>%s</strong> license. <a href="%s">Learn more.</a></p>',
			esc_attr( $license_data['url'] ),
			esc_attr( content_url( 'plugins/wds-citytech/assets/img/cc/' . $license_slug . '.png' ) ),
			esc_attr( $license_data['label'] ),
			esc_html( $license_data['label'] ),
			esc_attr( $license_data['url'] )
		);

		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$r = array_merge(
			[
				'license' => 'by',
			],
			$instance
		);

		?>
		<p>
			<label class="screen-reader-text" for="<?php echo esc_attr( $this->get_field_id( 'license' ) ); ?>">Select your Creative Commons license.</p>
			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'license' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'license' ) ); ?>">
				<?php foreach ( $this->licenses as $slug => $data ) : ?>
					<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $slug, $r['license'] ); ?>><?php echo esc_html( $data['label'] ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$new_license = isset( $new_instance['license'] ) ? wp_unslash( $new_instance['license'] ) : 'by';

		$instance = [
			'license' => isset( $this->licenses[ $new_license ] ) ? $new_license : 'by',
		];

		return $instance;
	}

}
