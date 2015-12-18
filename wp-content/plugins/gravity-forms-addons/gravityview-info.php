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
	<?php echo isset($_GET['viewinstructions']) ? 'display:none;' : ''; ?>
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
		   class="aligncenter"><img src="<?php echo plugins_url( '/images/GravityView.png', __FILE__ ); ?>"
		                            alt="GravityView Logo"/></a>
		<h2><?php esc_html_e( 'Better, simpler, more powerful.', 'gravity-forms-addons' ); ?></h2>

		<h3><?php esc_html_e( 'Not just a premium version: a whole different plugin.', 'gravity-forms-addons' ); ?></h3>

		<img src="<?php echo plugins_url( '/images/money-back-guarantee.jpg', __FILE__ ); ?>"
		     alt="GravityView money-back Guarantee" class="alignright" style="margin: 10px 0 0 10px;"/>
		<?php

		echo wpautop( sprintf( esc_html__( '%sGravityView%s is the best way to display Gravity Forms entries on your website.', 'gravity-forms-addons' ), '<a href="http://katz.si/gravityview">', '</a>' ) );

		?>

		<ul class="ul-square">
			<li><?php esc_html_e( 'Drag & Drop interface', 'gravity-forms-addons' ); ?></li>
			<li><?php esc_html_e( 'Different layout types - display entries as a table or profiles', 'gravity-forms-addons' ); ?></li>
			<li><?php esc_html_e( 'Preset templates make it easy to get started', 'gravity-forms-addons' ); ?></li>
			<li><?php esc_html_e( 'Great support', 'gravity-forms-addons' ); ?></li>
		</ul>

		<?php

		echo wpautop( sprintf( esc_html__( 'We re-wrote the Directory plugin from the ground up to be more simple and more powerful. If you like the Directory plugin, you&rsquo;ll %slove%s GravityView.', 'gravity-forms-addons' ), '<em>', '</em>' ) ); ?>

		<p><a href="http://katz.si/gravityview"
		      class="button button-hero button-primary"><?php esc_html_e( 'Check out GravityView', 'gravity-forms-addons' ); ?></a>
		</p>
	</div>
</div>
<div class="clear"></div>