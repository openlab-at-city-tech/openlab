<?php
/**
 * The template to display GravityView info on the admin settings page.
 *
 * @link       https://gravityview.co
 * @since      4.2
 *
 * @package    gravity-forms-addons
 * @subpackage gravity-forms-addons/includes/views
 */
?>

<style type="text/css">
	#kws_gravityview_info {
		float: left;
		width: 95%;
		border: 1px solid #ccc;
		padding: 10px 2.5%;
		color: #333;
		margin: 0;
		margin-bottom: 10px;
		background: #fff;
		text-align: center;
	<?php echo isset( $_GET['viewinstructions'] ) ? 'display:none;' : ''; ?>
	}

	#kws_gravityview_info div.aligncenter {
		max-width: 700px;
		margin: 0 auto;
		float: none;
	}

	#kws_gravityview_info * {
		text-align: left;
	}

	#kws_gravityview_info h3 {
		margin: 0;
		margin-top: 10px;
	}

	#kws_gravityview_info p, #kws_gravityview_info li {
		font-size: 1.1em;
	}

	#kws_gravityview_info .email {
		padding: 5px;
		font-size: 15px;
		line-height: 20px;
		margin-bottom: 10px;
	}

	#kws_gravityview_info .button-primary {
		display: block;
		float: left;
		margin: 5px 0;
		text-align: center;
	}

	.wp-core-ui .button-primary {
		background: #007cba;
		border-color: #007cba;
		color: #fff;
		text-decoration: none;
		text-shadow: none;
	}

	#kws_gravityview_info .button-primary:hover,
	#kws_gravityview_info .button-primary:focus {
		background: #0071a1;
		border-color: #0071a1;
		color: #fff;
	}

	#kws_gravityview_info .button-primary:focus {
		box-shadow:
				0 0 0 1px #fff,
				0 0 0 3px #007cba;
	}

	#kws_gravityview_info .button-primary:active {
		background: #00669b;
		border-color: #00669b;
		box-shadow: none;
		color: #fff;
	}

	#kws_gravityview_info img {
		max-width: 100%;
		margin: 0 auto 10px;
		display: block;
		text-align: center;
	}
</style>
<div id="kws_gravityview_info">
	<div class="aligncenter">
		<a href="http://katz.si/gravityview"
		   title="<?php esc_attr_e( 'Go to the GravityView Website', 'gravity-forms-addons' ); ?>"
		   class="aligncenter"><img src="<?php echo GF_DIRECTORY_URL . '/images/GravityView.png'; ?>"
									alt="GravityView Logo" width="306" height="93"/></a>
		<h2><?php esc_html_e( 'Better, simpler, more powerful.', 'gravity-forms-addons' ); ?></h2>

		<h3><?php esc_html_e( 'Not just a premium version: a whole different plugin.', 'gravity-forms-addons' ); ?></h3>

		<img src="<?php echo GF_DIRECTORY_URL . '/images/money-back-guarantee.jpg'; ?>"
			 alt="GravityView money-back Guarantee" class="alignright" style="margin: 10px 0 0 10px;"/>
		<?php

		echo wpautop( sprintf( esc_html__( '%1$sGravityView%2$s is the best way to display Gravity Forms entries on your website.', 'gravity-forms-addons' ), '<a href="https://katz.si/gravityview">', '</a>' ) );

		?>

		<ul class="ul-square">
			<li><?php esc_html_e( 'Drag & Drop interface', 'gravity-forms-addons' ); ?></li>
			<li><?php esc_html_e( 'Different layout types - display entries as a table or profiles', 'gravity-forms-addons' ); ?></li>
			<li><?php esc_html_e( 'Preset templates make it easy to get started', 'gravity-forms-addons' ); ?></li>
			<li><?php esc_html_e( 'Great support', 'gravity-forms-addons' ); ?></li>
		</ul>

		<?php

		echo wpautop( sprintf( esc_html__( 'We re-wrote the Directory plugin from the ground up to be more simple and more powerful. If you like the Directory plugin, you&rsquo;ll %1$slove%2$s GravityView.', 'gravity-forms-addons' ), '<em>', '</em>' ) );
		?>

		<p><a href="http://katz.si/gravityview" class="button button-hero button-primary"><?php esc_html_e( 'Check out GravityView', 'gravity-forms-addons' ); ?></a>
		</p>
	</div>
</div>
<div class="clear"></div>
