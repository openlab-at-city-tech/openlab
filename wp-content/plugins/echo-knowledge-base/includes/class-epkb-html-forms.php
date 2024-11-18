<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * HTML boxes and dialogs for admin pages
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_HTML_Forms {

	/********************************************************************************
	 *
	 *                                   NOTIFICATIONS
	 *
	 ********************************************************************************/

	/**
	 * HTML Notification box with Title and Body text.
	 *
	 * $values:
	 *  string $value['id']            ( Optional ) Container ID, used for targeting with other JS
	 *  string $value['type']          ( Required ) ( error, success, warning, info )
	 *  string $value['title']         ( Optional ) The big Bold Main text
	 *  HTML   $value['desc']          ( Required ) Any HTML P, List etc...
	 *
	 * @param array $args
	 * @param bool $return_html
	 *
	 * @return false|string
	 */
	public static function notification_box_popup( array $args = array(), $return_html=false ) {

		$icon = '';
		switch ( $args['type']) {
			case 'error':   $icon = 'epkbfa-exclamation-triangle';
				break;
			case 'error-no-icon':
				break;
			case 'success': $icon = 'epkbfa-check-circle';
				break;
			case 'warning': $icon = 'epkbfa-exclamation-circle';
				break;
			case 'info':    $icon = 'epkbfa-info-circle';
				break;
		}

		if ( $return_html ) {
			ob_start();
		}   ?>

		<div <?php echo isset( $args['id'] ) ? 'id="' . esc_attr( $args['id'] ) . '"' : ''; ?> class="epkb-notification-box-popup <?php echo 'epkb-notification-box-popup--' . esc_attr( $args['type'] ); ?>">

			<div class="epkb-notification-box-popup__icon">
				<div class="epkb-notification-box-popup__icon__inner epkbfa <?php echo esc_html( $icon ); ?>"></div>
			</div>

			<div class="epkb-notification-box-popup__body">     <?php

				if ( ! empty( $args['title'] ) ) { ?>
					<h6 class="epkb-notification-box-popup__body__title">
						<?php echo wp_kses( $args['title'], array( 'a' => array(
							'href'  => array(),
							'title' => array()
						),
							'br'        => array(),
							'em'        => array(),
							'strong'    => array(),
							'ul'        => array(),
							'li'        => array(),
						) ); ?>
					</h6>                <?php
				}

				if ( isset( $args['desc'] ) ) { ?>
					<div class="epkb-notification-box-popup__body__desc"><?php echo wp_kses( $args['desc'], array( 'a' => array(
							'href'  => array(),
							'title' => array()
						),
							'br'        => array(),
							'em'        => array(),
							'strong'    => array(),
							'ul'        => array(),
							'li'        => array(),
						) ); ?>
					</div> <?php
				}

				if ( ! empty( $args['id'] ) ) {  ?>
					<div class="epkb-notification-box-popup__buttons-wrap">
						<span class="epkb-notification-box-popup__button-confirm epkb-notice-dismiss"<?php echo empty( $args['close_target'] ) ? '' : ' data-target="' . esc_html( $args['close_target'] ) . '"'; ?>
						      data-notice-id="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['button_confirm'] ); ?></span>
					</div>     <?php
				}   ?>
			</div>

		</div>    <?php

		if ( $return_html ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * This is the Top Notification Box
	 * Must be placed above the Admin Content ( #ekb-admin-page-wrap ). Used usually with hooks.
	 *
	 * @param array $args Array of Settings.
	 * @param bool $return_html Optional. Returns html if true, otherwise echo's out function html.
	 *
	 * @return string
	 */
	public static function notification_box_top( array $args = array(), $return_html=false ) {

		$icon = '';
		switch ( $args['type']) {
			case 'error':   $icon = 'epkbfa-exclamation-triangle';
				break;
			case 'success': $icon = 'epkbfa-check-circle';
				break;
			case 'warning': $icon = 'epkbfa-exclamation-circle';
				break;
			case 'info':    $icon = 'epkbfa-info-circle';
				break;
		}

		if ( $return_html ) {
			ob_start();
		}        ?>

		<div <?php echo isset( $args['id'] ) ? 'id="' . esc_attr( $args['id'] ) . '"' : ''; ?> class="epkb-notification-box-top <?php echo 'epkb-notification-box-top--' . esc_attr( $args['type'] ); ?>">

			<div class="epkb-notification-box-top__icon">
				<div class="epkb-notification-box-top__icon__inner epkbfa <?php echo esc_html( $icon ); ?>"></div>
			</div>

			<div class="epkb-notification-box-top__body">                <?php
				if ( ! empty( $args['title'] ) ) { ?>
					<h6 class="epkb-notification-box-top__body__title">						<?php
						echo wp_kses( $args['title'], array( 'a' => array(
							'href'  => array(),
							'title' => array()
						),
							'br'        => array(),
							'em'        => array(),
							'strong'    => array(),
							'ul'        => array(),
							'li'        => array(),
						) ); ?>
					</h6>                <?php
				}

				if ( isset( $args['desc'] ) ) { ?>
					<div class="epkb-notification-box-top__body__desc"><?php
						echo wp_kses( $args['desc'], array( 'a' => array(
							'href'  => array(),
							'title' => array()
						),
							'br'        => array(),
							'em'        => array(),
							'strong'    => array(),
							'ul'        => array(),
							'li'        => array(),
						) ); ?>
					</div> <?php
				}

				if ( ! empty( $args['id'] ) ) {  ?>
					<div class="epkb-notification-box-top__buttons-wrap">
						<span class="epkb-notification-box-top__button-confirm epkb-notice-dismiss"<?php echo empty( $args['close_target'] ) ? '' : ' data-target="' . esc_html( $args['close_target'] ) . '"'; ?>
						      data-notice-id="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['button_confirm'] ); ?></span>
					</div>     <?php
				}   ?>
			</div>

		</div>    <?php

		if ( $return_html ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * This is the Middle Notification Box
	 * Must be placed within the Admin Content ( #ekb-admin-page-wrap ). Used inside boxes and within the Admin Content.
	 *
	 * @param array $args Array of Settings.
	 * @param bool $return_html Optional. Returns html if true, otherwise echo's out function html.
     *
     * Types - success, error, error-no-icon, warning, info
	 *
	 * @return string
	 */
	public static function notification_box_middle( array $args = array(), $return_html=false ) {

		$icon = '';
		$box_type = isset( $args['type'] ) ? $args['type'] : 'info';
		switch ( $box_type ) {
			case 'error':   $icon = 'epkbfa-exclamation-triangle';
				break;
			case 'success': $icon = 'epkbfa-check-circle';
				break;
			case 'warning': $icon = 'epkbfa-exclamation-circle';
				break;
			case 'info':    $icon = 'epkbfa-info-circle';
				break;
			case 'error-no-icon':
			case 'success-no-icon':
			default:
				break;
		}

		if ( $return_html ) {
			ob_start();
		}        ?>

		<div <?php echo isset( $args['id'] ) ? 'id="' . esc_attr( $args['id'] ) . '"' : ''; ?> class="epkb-notification-box-middle <?php echo 'epkb-notification-box-middle--' . esc_attr( $box_type ); ?>">

			<div class="epkb-notification-box-middle__icon">
				<div class="epkb-notification-box-middle__icon__inner epkbfa <?php echo esc_html( $icon ); ?>"></div>
			</div>

			<div class="epkb-notification-box-middle__body">                <?php
				if ( ! empty( $args['title'] ) ) { ?>
					<h6 class="epkb-notification-box-middle__body__title">						<?php
						echo wp_kses( $args['title'], array( 'a' => array(
							'href'  => array(),
							'title' => array()
						),
							'br'        => array(),
							'em'        => array(),
							'strong'    => array(),
							'ul'        => array(),
							'li'        => array(),
						) ); ?>
					</h6>                <?php
				}

				if ( isset( $args['desc'] ) ) { ?>
					<div class="epkb-notification-box-middle__body__desc"><?php
						echo wp_kses( $args['desc'], array(
							'a' => array(
								'href'   => array(),
								'title'  => array(),
								'target' => array(),
								'class'  => array(),
							),
							'span'      => array(
								'class' => array(),
							),
							'br'        => array(),
							'em'        => array(),
							'strong'    => array(),
							'ul'        => array(),
							'li'        => array(),
							'div'       => array()
						) ); ?>
					</div> <?php
				}

				if ( ! empty( $args['id'] ) && ! empty( $args['button_confirm'] ) ) {  ?>
					<div class="epkb-notification-box-middle__buttons-wrap">
						<span class="epkb-notification-box-middle__button-confirm epkb-notice-dismiss"<?php echo empty( $args['close_target'] ) ? '' : ' data-target="' . esc_html( $args['close_target'] ) . '"'; ?>>
							<?php echo esc_html( $args['button_confirm'] ); ?></span>
					</div>     <?php
				}

				if ( ! empty( $args['html'] ) ) {
                    echo wp_kses_post( $args['html'] );
				}   ?>
			</div>

		</div>    <?php

		if ( $return_html ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Show info or error message to the user
	 *
	 * @param $message
	 * @param string $title
	 * @param string $type
	 * @return string
	 */
	public static function notification_box_bottom( $message, $title='', $type='success' ) {

		/* array $EZSQL_ERROR */
		global $EZSQL_ERROR;

		if ( EPKB_Utilities::is_amag_on() && ! empty($EZSQL_ERROR) && is_array($EZSQL_ERROR) ) {
			foreach ( $EZSQL_ERROR as $error ) {
				$amgr_tables = array("amgr_access_kb_categories", "amgr_access_read_articles", "amgr_access_read_categories", "amgr_kb_group_users", "amgr_kb_groups", "amgr_kb_public_groups");
				foreach ( $amgr_tables as $table_name ) {
					if ( ! empty( $error['error_str'] ) && strpos( $error['error_str'], $table_name ) !== false ) {
						//LOG Only Access Manager Error
						EPKB_Logging::add_log( 'Database error', $EZSQL_ERROR );
						$message .= esc_html__( '. Database Error.', 'echo-knowledge-base' );
					}
				}
			}
		}

		$message = empty( $message ) ? '' : $message;

		return
			"<div class='eckb-bottom-notice-message'>
				<div class='contents'>
					<span class='" . esc_attr( $type ) . "'>" .
			( empty( $title ) ? '' : '<h4>' . esc_html( $title ) . '</h4>' ) . "
						<p> " . wp_kses_post( $message ) . "</p>
					</span>
				</div>
				<div class='epkb-close-notice epkbfa epkbfa-window-close'></div>
			</div>";
	}

	/**
	 * DIALOG BOX - User confirms action like delete records with OK or Cancel buttons.
	 *	$values ['id']                  CSS ID, used for JS targeting, no CSS styling.
	 *	$values ['title']               Top Title of Dialog Box.
	 *	$values ['body']                Text description.
	 *	$values ['form_inputs']         Form Inputs
	 *	$values ['accept_label']        Text for Accept button.
	 *	$values ['accept_type']         Text for Accept button. ( success, default, primary, error , warning )
	 *	$values ['show_cancel_btn']     ( yes, no )
	 *	$values ['show_close_btn']      ( yes, no )
	 *  $values ['hidden']              true/false hidden form or not, not required
	 *
	 * @param $values
	 */
	public static function dialog_confirm_action( $values ) { ?>

		<div id="<?php echo esc_attr( $values[ 'id' ] ); ?>" class="epkb-dialog-box-form" style="<?php echo empty( $values['hidden'] ) ? '' : 'display: none;'; ?>">

			<!---- Header ---->
			<div class="epkb-dbf__header">
				<h4><?php echo esc_html( $values['title'] ); ?></h4>
			</div>

			<!---- Body ---->
			<div class="epkb-dbf__body">				<?php
				echo empty( $values['body']) ? '' : wp_kses( $values['body'], EPKB_Utilities::get_admin_ui_extended_html_tags() ); ?>
			</div>

			<!---- Form ---->			<?php
			if ( !empty( $values[ 'form_method' ] ) ) { 		?>
				<form class="epkb-dbf__form"<?php echo empty( $values['form_method'] ) ? '' : ' method="' . esc_attr( $values['form_method'] ) . '"'; ?>>				<?php
					if ( isset($values['form_inputs']) ) {
						foreach ( $values['form_inputs'] as $input ) {
							echo '<div class="epkb-dbf__form__input">' . wp_kses( $input, EPKB_Utilities::get_admin_ui_extended_html_tags() ) . '</div>';
						}
					} ?>
				</form>			<?php
			} 		?>

			<!---- Footer ---->
			<div class="epkb-dbf__footer">

				<div class="epkb-dbf__footer__accept <?php echo isset($values['accept_type']) ? 'epkb-dbf__footer__accept--' . esc_attr( $values['accept_type'] ) : 'epkb-dbf__footer__accept--success'; ?>">
					<span class="epkb-accept-button epkb-dbf__footer__accept__btn">
						<?php echo $values['accept_label'] ? esc_html( $values['accept_label'] ) : esc_html__( 'Accept', 'echo-knowledge-base' ); ?>
					</span>
				</div>				<?php
				if ( ! empty( $values['show_cancel_btn' ] ) && $values['show_cancel_btn'] === 'yes' ) { 		?>
					<div class="epkb-dbf__footer__cancel">
						<span class="epkb-dbf__footer__cancel__btn"><?php esc_html_e( 'Cancel', 'echo-knowledge-base' ); ?></span>
					</div>				<?php
				} 		?>
			</div>  		           <?php

			if ( ! empty( $values['show_close_btn'] ) && $values['show_close_btn'] === 'yes' ) { 		?>
				<div class="epkb-dbf__close epkbfa epkbfa-times"></div>             <?php
			} 		?>

		</div>
		<div class="epkb-dialog-box-form-black-background"></div>		<?php
	}

	/**
	 * Pro Feature Advertisement Dialog
	 * @param: string $args ['id']             ( Required ) Dialog ID, used for JS targeting.
	 * @param: string $args['title']           ( Required ) The text title
	 * @param: string $args['footer_desc']     ( Optional ) Paragraph Text at the bottom.
	 * @param: array  $args['list']            ( Optional ) array() of list items.
	 * @param: string $args['btn_text']        ( Optional ) Button Text
	 * @param: string $args['btn_url']         ( Optional ) Button URL
	 * @param: string $args['show_close_btn']  ( Optional | Yes / No ) Default: No
	 *
	 */
	public static function dialog_pro_feature_ad( $args ) {

		$class = empty( $args['img_list'] ) ? 'epkb-dialog-pro-feature-ad' : 'epkb-dialog-pro-feature-ad2';		?>
		
		<div id="<?php echo esc_attr( $args[ 'id' ] ); ?>" class="<?php echo esc_attr( $class ); ?>">  <?php

			if ( empty( $args['img_list'] ) ) {
				self::pro_feature_ad_box( array(
					'title'             => $args['title'],
					'footer_desc'       => $args['footer_desc'] ?? '',
					'list'              => $args['list'] ?? [],
					'btn_text'          => $args['btn_text'] ?? '',
					'btn_url'           => $args['btn_url'] ?? '',
				) );
				if ( ! empty( $args['show_close_btn'] ) && $args['show_close_btn'] === 'yes' ) { 		?>
					<div class="epkb-dbf__close epkbfa epkbfa-times"></div>             <?php
				}
			} else {
				self::pro_feature_ad_box_with_images( array(
					'title'             => $args['title'],
					'footer_desc'       => $args['footer_desc'] ?? '',
					'img_list'          => $args['img_list'] ?? [],
					'btn_text'          => $args['btn_text'] ?? '',
					'btn_url'           => $args['btn_url'] ?? '',
				) );
			} 			?>

		</div>
		<div class="epkb-dialog-pro-feature-ad-black-background"></div>		<?php
	}



	/********************************************************************************
	 *
	 *                                   BOXES
	 *
	 ********************************************************************************/

	/**
	 * Show a box with Icon, Title, Description and Link
	 *
	 * @param $args array

	 * - ['icon_class']     Icon Beside title
	 * - ['icon_img_url']   Icon URL beside title
	 * - ['title']          Title above Video
	 * - ['video_src']      URL of Video source
	 * - ['desc']           Description text under video
	 * - ['keywords']       This will be used for on page search via JS. This will output hidden keywords for each video box.
	 */
	public static function video_info_box( $args ) { ?>

		<div class="epkb-video-container">

			<!-- Header -------------------->
			<div class="epkb-v__header-container">
				<h4 class="epkb__header__title"><?php echo esc_html( $args['title'] ); ?></h4>      <?php

				if ( isset( $args['icon_class'] ) ) { ?>
					<span class="epkb__header__icon epkbfa <?php echo esc_attr( $args['icon_class'] ); ?>"></span>				<?php
				} else if ( isset($args['icon_img_url'] ) ) { ?>
					<span class="epkb__header__img">
						 <img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . $args['icon_img_url'] ); ?>">
					 </span>				<?php
				}				 ?>

			</div>

			<!-- Body ---------------------->
			<div class="epkb-v__video-container">
				<iframe width="" height="" src="<?php echo esc_url( $args['video_src'] ); ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>			<?php

			if ( isset( $args['desc'] ) ) { ?>
				<div class="epkb-v__desc-container">
					<p><?php echo esc_html( $args['desc'] );?></p>
				</div>			<?php
			}	?>

			<div class="epkb-v__keywords-container">				<?php
				foreach( $args['keywords'] as $keyword ){
					echo esc_html( $keyword ) . ' ';
				}				?>
			</div>

		</div>	<?php
	}

	/**
	 * Display box for Call to Action step on Need Help page
	 *
	 * @param $step
	 */
	public static function display_step_cta_box( $step ) {    ?>
		<div class="epkb-admin__step-cta-box"<?php echo ( isset( $step['id'] ) ? ' id="' . esc_attr( $step['id'] ) . '"' : '' ); ?>>			<?php

			if (  isset( $step['icon_img_url'] ) ) {    ?>
				<div class="epkb-admin__step-cta-box__img-container">
					<img src=" <?php echo esc_url( Echo_Knowledge_Base::$plugin_url . $step['icon_img_url'] ); ?>">
				</div>			<?php
			}       ?>

			<div class="epkb-admin__step-cta-box__icon-wrap">
				<span class="epkb-admin__step-cta-box__icon <?php echo empty( $step['icon_class'] ) ? '' : esc_attr( $step['icon_class'] ); ?>"></span>
			</div>
			<div class="epkb-admin__step-cta-box__content">
				<h4 class="epkb-admin__step-cta-box__header"><?php echo esc_html( $step['title'] ); ?></h4>     <?php

				if ( isset( $step['content_icon_class'] ) ) {   ?>
					<span class="epkb-admin__step-cta-box__content__icon <?php echo esc_attr( $step['content_icon_class'] ); ?>"></span>    <?php
				}   ?>

				<p class="epkb-admin__step-cta-box__desc"><?php echo esc_html( $step['desc'] ); ?></p>
				<div class="epkb-admin__step-cta-box__body"><?php echo wp_kses_post( $step['html'] ); ?></div>
			</div>
		</div>      <?php
	}

	/**
	 * HTML Advertisement Box
	 * This box will have a title, image, either a description or list a button and more info link.
	 * $values:
	 *
	 * CSS ---------------------------------------------------------------
	 * @param: string $args['id']              ( Optional ) Container ID, used for targeting with other JS
	 * @param: string $args['class']           ( Optional ) Container CSS, used for targeting with CSS
	 * @param: string $args['icon']            ( Optional ) Icon to display ( from this list: https://fontawesome.com/v4.7.0/icons/ )
	 * @param: string $args['title']           ( Required ) The text title
	 * @param: string $args['img_url']         ( Required ) URL of image.
	 * @param: string $args['desc']            ( Optional ) Paragraph Text
	 * @param: array  $args['list']            ( Optional ) array() of list items.
	 * @param: string $args['btn_text']        ( Optional ) Button Text
	 * @param: string $args['btn_url']         ( Optional ) Button URL
	 * @param: string $args['btn_color']       ( Required ) blue,yellow,orange,red,green
	 * @param: string $args['more_info_text']  ( Optional ) More Info Text
	 * @param: string $args['more_info_url']   ( Optional ) More Info URL
	 * @param: string $args['more_info_color'] ( Required ) blue,yellow,orange,red,green
	 *
	 * @return false|string
	 */
	public static function advertisement_ad_box( $args ) {

		if ( $args['return_html'] ) {
			ob_start();
		}

		$args = EPKB_HTML_Elements::add_defaults( $args );		?>

		<div<?php echo empty( $args['id'] ) ? '' : ' id="' . esc_attr( $args['id'] ) . '"'; ?> class="epkb-admin-ad-container <?php echo esc_attr( $args['class'] ); ?>">

			<div class="epkb-admin-ad-ribbon">PRO</div>

			<!----- Header ----->
			<div class="epkb-aa__header-container">
				<div class="epkb-header__title"><?php echo esc_html( $args['title'] ); ?></div>
			</div>

			<!----- Body ------->
			<div class="epkb-aa__body-container">
				<div class="featured_img">
					<img class="epkb-body__img" src="<?php echo esc_url( $args['img_url'] ); ?>" alt="<?php echo esc_attr( $args['title'] ); ?>">
				</div>
				<p class="epkb-body__desc"><?php echo esc_html( $args['desc'] ); ?></p>

				<ul class="epkb-body__check-mark-list-container">					<?php
					if ( $args['list'] ) {
						foreach ($args['list'] as $item) {
							echo '<li class="epkb-check-mark-list__item">';
							echo '<span class="epkb-check-mark-list__item__icon epkbfa epkbfa-check"></span>';
							echo '<span class="epkb-check-mark-list__item__text">' . esc_html( $item ) . '</span>';
							echo '</li>';
						}
					}					?>
				</ul>

			</div>

			<!----- Footer ----->
			<div class="epkb-aa__footer-container">				<?php
				if ( $args['btn_text'] ) { ?>
					<a href="<?php echo esc_url( $args['btn_url'] ); ?>" target="_blank" class="epkb-body__btn epkb-body__btn--<?php echo esc_attr( $args['btn_color'] ); ?>"><?php echo esc_html( $args['btn_text'] ); ?></a>				<?php
				}

				if ( $args['more_info_text'] ) { ?>
					<a href="<?php echo esc_url( $args['more_info_url'] ); ?>" target="_blank" class="epkb-body__link epkb-body__link--<?php echo esc_attr( $args['more_info_color'] ); ?>">
						<span class="epkb-body__link__icon epkbfa epkbfa-info-circle"></span>
						<span class="epkb-body__link__text"><?php echo esc_html( $args['more_info_text'] ); ?></span>
						<span class="epkb-body__link__icon-after epkbfa epkbfa-angle-double-right"></span>
					</a>				<?php
				} ?>
			</div>
		</div>	<?php

		if ( $args['return_html'] ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Pro Feature Advertisement Box
	 * This box will have a title, image, either a description or list a button and more info link.
	 * $values:
	 *
	 * CSS ---------------------------------------------------------------
	 * @param: string $args['id']              ( Optional ) Container ID, used for targeting with other JS
	 * @param: string $args['class']           ( Optional ) Container CSS, used for targeting with CSS
	 *
	 *
	 * CONTENT ------------------------------------------------------------
	 * @param: string $args['title']           ( Required ) The text title
	 * @param: string $args['desc']            ( Optional ) Paragraph Text at the top.
	 * @param: string $args['footer_desc']     ( Optional ) Paragraph Text at the bottom.
	 * @param: array  $args['list']            ( Optional ) array() of list items.
	 * @param: string $args['btn_text']        ( Optional ) Button Text
	 * @param: string $args['btn_url']         ( Optional ) Button URL
	 *
	 *
	 * @return false|string
	 */
	public static function pro_feature_ad_box( $args ) {

		if ( ! empty( $args['return_html'] ) ) {
			ob_start();
		}
		$allowed_tags = array(
			'strong'  => array(),
			'i'       => array(),
			'br'      => array(),
		);

		$args = EPKB_HTML_Elements::add_defaults( $args );		?>

		<div<?php echo empty( $args['id'] ) ? '' : ' id="' . esc_attr( $args['id'] ) . '"'; ?> class="epkb-admin-pro-feature-ad-container <?php echo esc_attr( $args['class'] ); ?>">

			<div class="epkb-admin-ad-icon">
				<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/ad/' . 'kb-pro-icon.png' ); ?>">
			</div>

			<!----- Header ----->			<?php
			if ( !empty( $args['title'] ) ) { ?>
				<div class="epkb-aa__header-container">
					<div class="epkb-header__title"><?php echo wp_kses( $args['title'] , $allowed_tags ); ?></div>
				</div>			<?php
			} ?>

			<!----- Body ------->
			<div class="epkb-aa__body-container">				<?php

				if ( !empty( $args['desc'] ) ) { ?>
					<p class="epkb-body__desc"><?php echo esc_html( $args['desc'] ); ?></p>				<?php
				} ?>

				<ul class="epkb-body__check-mark-list-container">					<?php
					if ( $args['list'] ) {
						foreach ( $args['list'] as $item ) {
							echo '<li class="epkb-check-mark-list__item">';
							echo '<span class="epkb-check-mark-list__item__icon epkbfa epkbfa-check"></span>';
							echo '<span class="epkb-check-mark-list__item__text">' . esc_html( $item ) . '</span>';
							echo '</li>';
						}
					}					?>
				</ul>

			</div>

			<!----- Footer ----->
			<div class="epkb-aa__footer-container">				<?php
				if ( !empty( $args['footer_desc'] ) ) { ?>
					<p class="epkb-body__footer_desc"><?php echo esc_html( $args['footer_desc'] ); ?></p>				<?php
				}

				if ( $args['btn_text'] ) { ?>
					<a href="<?php echo esc_url( $args['btn_url'] ); ?>" class="epkb-btn" target="_blank" ><?php echo esc_html( $args['btn_text'] ); ?></a>				<?php
				} ?>

			</div>

			<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/ad/' . 'kb-pro-background.jpg' ); ?>" class="epkb-admin-pro-feature-ad-background-img" alt="">
		</div>	<?php

		if ( ! empty( $args['return_html'] ) ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Pro Feature Advertisement Box with 6 featured images.
	 * This box will have a title, image, either a description or list a button and more info link.
	 * $values:
	 *
	 * CSS ---------------------------------------------------------------
	 * @param: string $args['id']              ( Optional ) Container ID, used for targeting with other JS
	 * @param: string $args['class']           ( Optional ) Container CSS, used for targeting with CSS
	 *
	 *
	 * CONTENT ------------------------------------------------------------
	 * @param: string $args['title']           ( Required ) The text title
	 * @param: string $args['desc']            ( Optional ) Paragraph Text at the top.
	 * @param: string $args['footer_desc']     ( Optional ) Paragraph Text at the bottom.
	 * @param: array  $args['list']            ( Optional ) array() of list items.
	 * @param: string $args['btn_text']        ( Optional ) Button Text
	 * @param: string $args['btn_url']         ( Optional ) Button URL
	 *
	 *
	 * @return false|string
	 */
	public static function pro_feature_ad_box_with_images( $args ) {

		if ( ! empty( $args['return_html'] ) ) {
			ob_start();
		}
		$allowed_tags = array(
			'strong'  => array(),
			'i'       => array(),
			'br'      => array(),
		);

		$args = EPKB_HTML_Elements::add_defaults( $args );		?>

		<div<?php echo empty( $args['id'] ) ? '' : ' id="' . esc_attr( $args['id'] ) . '"'; ?> class="epkb-admin-pro-feature-ad2-container <?php echo esc_attr( $args['class'] ); ?>">

			<!----- Header ----->
			<div class="epkb-aa__header-container">				<?php

				if ( ! empty( $args['title'] ) ) { ?>
					<div class="epkb-feature-header__title">
						<h4><?php echo wp_kses( $args['title'] , $allowed_tags ); ?></h4>
					</div>				<?php
				} ?>

			</div>

			<!----- Body ------->
			<div class="epkb-aa__body-container">

				<div class="epkb-feature-list-container">					<?php

					if ( ! empty( $args['img_list'] ) ) {
						foreach ( $args['img_list'] as $index => $item ) { ?>

							<div class="epkb-feature-container <?php echo $index === 0 ? 'epkb-feature--active' : ''; ?>">

								<div class="epkb-feature-previous epkbfa ep_font_icon_arrow_carrot_left"></div>
								<div class="epkb-feature-next ep_font_icon_arrow_carrot_right"></div>

								<!-- Image -->
								<div class="epkb-feature-img">    <?php
									echo '<img class="epkb-featured-img-url" src="' . esc_html( $item[3] ) . '" >'; ?>
								</div>

								<div class="epkb-feature-footer">

									<!-- Description -->
									<div class="epkb-feature-footer__desc">
										<p><?php echo esc_html( $item[1] ); ?></p>
									</div>
									<!-- Button -->
									<div class="epkb-feature-footer__btn-container">										<?php
										echo '<a href="'.esc_url( $item[2] ).'" class="epkb-view-demo-btn" target="_blank">' . esc_html( $item[0] ) . '</a>';

										if ( $args['btn_text'] ) { ?>
											<a href="<?php echo esc_url( $args['btn_url'] ); ?>" class="epkb-buy-btn" target="_blank" ><?php echo esc_html( $args['btn_text'] ); ?></a>				<?php
										} ?>
									</div>
								</div>

							</div>							<?php
						}
					} ?>

				</div>
			</div>

		</div>	<?php

		if ( ! empty( $args['return_html'] ) ) {
			return ob_get_clean();
		}
		
		return '';
	}

	/**
	 * Box with Title, image or icon, description and button.
	 * Used for Configuration -> KB Design and Need Help? -> Contact Us boxes.
	 *
	 * @param $args array
	 * - ['container_class']    Main class for custom CSS for specific CTA
	 * - ['style']              The style of the Call to Action
	 *                          style-1: Center Aligned Icon top
	 *                          style-2: Left Aligned Icon top
	 * - ['icon_class']         Top Icon to display ( Choose between these available ones: https://fontawesome.com/v4.7.0/icons/ )
	 * - ['title']              H3 title of the box.
	 * - ['content']            Body content of the box.
	 * - ['btn_text']           Show button and the text of the button at the bottom of the box, if no text is defined no button will show up.
	 * - ['btn_url']            Button URL.
	 * - ['btn_target']         __blank
	 */
	public static function call_to_action_box( $args ) {

		$args = EPKB_HTML_Elements::add_defaults( $args ); ?>

		<div class="epkb-call-to-action-container <?php echo esc_attr( $args['container_class'] ); ?> <?php echo 'epkb-call-to-action--' . esc_attr( $args['style'] ); ?>">

			<!-- Header -------------------->
			<div class="epkb-cta__header">
				<h3 class="epkb-cta__header__title"><?php echo esc_html( $args['title'] ); ?></h3>				<?php
				if ( isset( $args['icon_class'] ) ) { ?>
					<span class="epkb-cta__header__icon epkbfa <?php echo esc_attr( $args['icon_class'] ); ?>"></span>				<?php
				} elseif ( isset($args['icon_img_url'] ) ) { ?>
					<span class="epkb-cta__header__img">
						 <img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . $args['icon_img_url'] ); ?>">
					 </span>				<?php
				}				 ?>
			</div>

			<!-- Body ---------------------->			<?php
			if ( isset( $args['content'] ) ) { ?>
				<div class="epkb-cta__body">
					<?php echo empty( $args['content'] ) ? '' : wp_kses_post( $args['content'] ); ?>
				</div>			<?php
			}

			if ( ! empty($args['btn_target']) ) {    ?>
				<!-- Footer ---------------------->
				<div class="epkb-cta__footer">
					<a class="epkb-cta__footer__button" href="<?php echo esc_url( $args['btn_url'] ); ?>" target="<?php echo isset( $args['btn_target'] ) ? esc_attr( $args['btn_target'] ) : ''; ?>"><?php echo esc_html( $args['btn_text'] ); ?></a>
				</div>  <?php
			} ?>

		</div>	<?php
	}

	/**
	 * Show a single Settings Box for one configuration for configuration pages
	 *
	 * @param $box_options
	 *
	 * @return false|string
	 */
	public static function admin_settings_box( $box_options ) {

		// Skip box if its content HTML is empty (due to user access level or add-ons disabled)
		if ( empty( $box_options['html'] ) ) {
			return '';
		}

		if ( $box_options['return_html'] ) {
			ob_start();
		}   ?>

		<!-- Admin Box -->
		<div class="epkb-admin__boxes-list__box <?php echo esc_attr( $box_options['class'] ); ?>">  <?php

			// Display header
			if ( ! empty( $box_options['title'] ) ){    ?>
				<h4 class="epkb-admin__boxes-list__box__header<?php
					echo empty( $box_options['icon_class'] ) ? '' : ' epkb-kbc__boxes-list__box__header--icon ' . esc_attr( $box_options['icon_class'] );
				?>">
                    <span><?php echo esc_html( $box_options['title'] ); ?></span>   <?php
	                // Add box header tooltip
					if ( ! empty( $box_options['tooltip_title'] ) && ! empty( $box_options['tooltip_desc'] ) ) {
						$box_options['tooltip_args'] = isset( $box_options['tooltip_args'] ) ? $box_options['tooltip_args'] : [];
						EPKB_HTML_Elements::display_tooltip( $box_options['tooltip_title'], $box_options['tooltip_desc'], $box_options['tooltip_args'] );
					} ?>
                </h4>   <?php
			}

			// Display body         ?>
			<div class="epkb-admin__boxes-list__box__body">   <?php

				// Display description
				if ( ! empty( $box_options['description'] ) ) {    ?>
					<p class="epkb-admin__boxes-list__box__desc"><?php echo wp_kses_post( $box_options['description'] ); ?></p>   <?php
				}

				// Display HTML Content
				$box_options['extra_tags'] = isset( $box_options['extra_tags'] ) ? $box_options['extra_tags'] : array();
				$admin_ui_escaped = EPKB_Utilities::admin_ui_wp_kses( $box_options['html'], $box_options['extra_tags'] ); ?>
				<div class="epkb-admin__boxes-list__box__content"><?php echo $admin_ui_escaped;//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>

			</div>

		</div> <?php

		if ( $box_options['return_html'] ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * A Feature box - title, description, action buttons and links to docs and sales page
	 * Used in Need Help -> Features pages and Import/Export config screens.
	 *
	 * @param $feature
	 * @return string
	 */
	public static function get_feature_box_html( $feature ) {

		// apply defaults
		$feature = array_merge( [
			'icon'           => '',
			'title'          => '',
			'title_class'    => '', // additional title class
			'desc'           => '',
			'desc_escaped'   => '',
			'custom_links'   => '', // html format for custom links
			'config'         => '', // url
			'docs'           => '', // url
			'demo'           => '', // url
			'video'          => '', // url
			'learn_more'     => '', // url
			'active_status'  => false,
			'install_link'   => '', // url
			'upgrade_link'   => '', // url
			'button_id'      => '', // button id. Will work only with button_title
			'button_title'   => '', // button title. Will work only with button_id
			'experimental'   => '', // Experimental label. Value is tooltip body
		], $feature );


		ob_start(); ?>

		<div class="epkb-kbnh__feature-container__col epkb-kbnh__feature__icon-col"><span
				class="<?php echo esc_attr( $feature['icon'] ); ?>"></span></div>

		<div class="epkb-kbnh__feature-container__col epkb-kbnh__feature__content-col">
			<h3 class="epkb-kbnh__feature-name <?php echo esc_attr( $feature['title_class'] ); ?>"><?php echo esc_html( $feature['title'] ); ?> <?php
				// Optional experimental label
				if ( ! empty( $feature['experimental'] ) ) {   ?>
					<div class="epkb-kbnh__feature-experimental"><?php
					esc_html_e( 'Experimental', 'echo-knowledge-base' );
					EPKB_HTML_Elements::display_tooltip( '', $feature['experimental'], ['link_text' => '', ]); ?>
					</div><?php
				} ?>
			</h3> <?php

			// Optional description
			if ( ! empty( $feature['desc'] ) ) { ?>
				<div class="epkb-kbnh__feature-desc"><?php echo esc_html( $feature['desc'] ) . $feature['desc_escaped']; ?></div><?php
			}

			// Links    ?>
			<div class="epkb-kbnh__feature-links">  <?php

				// Action Button
				if ( ! empty( $feature['button_id'] ) && ! empty( $feature['button_title'] ) ) { ?>
					<button class="epkb-primary-btn" id="<?php echo esc_attr( $feature['button_id'] ); ?>" type="button"><?php echo esc_html( $feature['button_title'] ); ?></button> <?php
				}

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $feature['custom_links'];

				// Link to Configure ( only if dedicated plugin is active and initial KB installation is completed )
				if ( ! empty( $feature['config'] ) ) { ?>
					<a class="epkb-kbnh__feature-link" href="<?php echo esc_url( $feature['config'] ); ?>" target="_blank"><span><?php esc_html_e( 'Configure', 'echo-knowledge-base' ); ?></span></a>    <?php
				}

				if ( ! empty( $feature['docs'] ) ) { ?>
					<a class="epkb-kbnh__feature-link" href="<?php echo esc_url( $feature['docs'] ); ?>"
					   target="_blank"><span><?php esc_html_e( 'Documentation', 'echo-knowledge-base' ); ?></span></a>    <?php
				}

				// Link to demo
				if ( ! empty( $feature['demo'] ) ) { ?>
					<a class="epkb-kbnh__feature-link" href="<?php echo esc_url( $feature['demo'] ); ?>"
					   target="_blank"><span><?php esc_html_e( 'Demo', 'echo-knowledge-base' ); ?></span></a>    <?php
				}

				// Link to Video Tutorial
				if ( ! empty( $feature['video'] ) ) { ?>
					<a class="epkb-kbnh__feature-link" href="<?php echo esc_url( $feature['video'] ); ?>"
					   target="_blank"><span><?php esc_html_e( 'Video Tutorial', 'echo-knowledge-base' ); ?></span></a>    <?php
				}

				// Learn More
				if ( ! empty( $feature['learn_more'] ) ) { ?>
					<a class="epkb-kbnh__feature-link" href="<?php echo esc_url( $feature['learn_more'] ); ?>"
					   target="_blank"><span><?php esc_html_e( 'Learn More', 'echo-knowledge-base' ); ?></span></a>    <?php
				} ?>

			</div>
		</div>

		<div class="epkb-kbnh__feature-container__col epkb-kbnh__feature__status-col">    <?php

			// Plugin is enabled
			if ( ! empty( $feature['active_status'] ) ) {   ?>
				<span class="epkb-kbnh__feature-status epkb-kbnh__feature--installed">
                    <span class="epkbfa epkbfa-check"></span>
                </span>    <?php
			// Plugin is not enabled
			} else if ( $feature['plugin'] == 'ep'.'hd' && empty( $feature['hide_install_btn'] ) ) {
				echo '<a class="epkb-kbnh__feature-status epkb-kbnh__feature--disabled epkb-success-btn" href="https://wordpress.org/plugins/help-dialog/" target="_blank"><span>' . esc_html__( 'Install Now', 'echo-knowledge-base' ) . '</span></a>';
			} else if ( empty( $feature['hide_install_btn'] ) ) {
				echo '<a class="epkb-kbnh__feature-status epkb-kbnh__feature--disabled epkb-success-btn" href="' . esc_url( EPKB_Core_Utilities::get_plugin_sales_page( $feature['plugin'] ) ) . '" target="_blank"><span>' . esc_html__( 'Install Now', 'echo-knowledge-base' ) . '</span></a>';
			} ?>

		</div><?php

		if ( ! empty( $feature['corner_label'] ) ) { ?>
			<div class="epkb-kbnh__feature-corner__label"><?php echo esc_html( $feature['corner_label'] ); ?></div><?php
		}

		return ob_get_clean();
	}


	/********************************************************************************
	 *
	 *                                   TABLE
	 *
	 ********************************************************************************/

	/**
	 * Get an HTML Table for a list of items
	 *
	 * @param $list_of_items
	 * @param $total_items_number
	 * @param $item_primary_key
	 * @param $item_column_fields - item's fields which need to display as columns
	 * @param $item_row_fields - item's fields which need to display as rows
	 * @param $item_optional_row_fields - item's fields which need to display as rows only if they are not empty
	 * @param $load_more_action
	 *
	 * @return false|string
	 * @noinspection PhpUnused
	 */
	public static function get_html_table( $list_of_items, $total_items_number, $item_primary_key, $item_column_fields, $item_row_fields, $item_optional_row_fields, $load_more_action ) {

		$columns_count = count( $item_column_fields ) + 1;  // +1 is set for actions row

		ob_start();     ?>

		<!--Items List -->
		<table class="epkb-admin__items-list">

			<!-- Items List Header -->
			<thead class="epkb-admin__items-list__header">
				<tr>    <?php

					foreach( $item_column_fields as $field_key => $field_title ) {    ?>
						<th class="epkb-admin__items-list__field"><?php echo esc_html( $field_title ); ?></th>    <?php
					}    ?>

				</tr>
			</thead>    <?php

			// Items list body
			$table_rows_escaped = self::get_html_table_rows( $list_of_items, $item_primary_key, $item_column_fields, $item_row_fields, $item_optional_row_fields, $columns_count );
			echo $table_rows_escaped; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped    ?>

			<!-- Items List No Results -->
			<tbody class="epkb-admin__items-list__no-results">
				<tr>
					<td colspan="<?php echo esc_attr( $columns_count ); ?>"><?php echo esc_html__( 'No entries found.', 'echo-knowledge-base' ); ?></td>
				</tr>
			</tbody>
		</table>    <?php

		// Show message that more items exist
		$items_left = $total_items_number - count( $list_of_items );
		if ( $items_left > 0 ) {    ?>

			<!-- More Items -->
			<div class="epkb-admin__items-list__more-items-message">
				<form>
					<input type="hidden" name="page_number" value="1">   <?php
					EPKB_HTML_Elements::submit_button_v2( esc_html__( 'See More', 'echo-knowledge-base' ), $load_more_action, 'epkb-admin__items-list__more-items-message__button', '', false );   ?>
				</form>
			</div>  <?php
		}

		return ob_get_clean();
	}

	/**
	 * Get items as rows of HTML table
	 *
	 * @param $list_of_items
	 * @param $item_primary_key
	 * @param $item_column_fields
	 * @param $item_row_fields
	 * @param $item_optional_row_fields
	 * @param $columns_count
	 *
	 * @return false|string
	 */
	public static function get_html_table_rows( $list_of_items, $item_primary_key, $item_column_fields, $item_row_fields, $item_optional_row_fields, $columns_count ) {

		ob_start(); ?>

			<tbody class="epkb-admin__items-list__item">   <?php
			foreach ( $list_of_items as $item ) {

				// Column fields
				self::display_item_column_fields( $item, $item_column_fields, $item_primary_key );

				// Row fields
				self::display_item_row_fields( $item, $item_row_fields, $columns_count );

				// Optional row fields
				self::display_item_optional_row_fields( $item, $item_optional_row_fields, $columns_count );		
			} ?>
			</tbody>    <?php

		return ob_get_clean();
	}

	/**
	 * Display single item's fields as columns
	 *
	 * @param $item
	 * @param $item_column_fields
	 * @param $item_primary_key
	 */
	private static function display_item_column_fields( $item, $item_column_fields, $item_primary_key ) {   ?>

		<tr class="epkb-admin__items-list__column-fields">     <?php

			// Display item's fields
			foreach ( $item_column_fields as $field_key => $field_title ) {   ?>
				<td class="epkb-admin__items-list__column-field"><?php echo wp_kses_post( $item->$field_key ); ?></td>  <?php
			}   ?>

		</tr> <?php
	}

	/**
	 * Display single item's fields as rows
	 *
	 * @param $item
	 * @param $item_row_fields
	 * @param $columns_count
	 */
	private static function display_item_row_fields( $item, $item_row_fields, $columns_count ) {

		// Display item's fields
		foreach ( $item_row_fields as $field_key => $field_title ) {
			self::display_item_row_field( $field_title, $item->$field_key, $columns_count );
		}
	}

	/**
	 * Display single item's optional fields as rows only if they are not empty
	 *
	 * @param $item
	 * @param $item_optional_row_fields
	 * @param $columns_count
	 */
	private static function display_item_optional_row_fields( $item, $item_optional_row_fields, $columns_count ) {

		// Display item's fields
		foreach ( $item_optional_row_fields as $field_key => $field_title ) {

			if ( empty( $item->$field_key ) ) {
				continue;
			}

			self::display_item_row_field( $field_title, $item->$field_key, $columns_count );
		}
	}

	/**
	 * Display single item's field as row
	 *
	 * @param $field_title
	 * @param $field_value
	 * @param $columns_count
	 */
	private static function display_item_row_field( $field_title, $field_value, $columns_count ) {  ?>
		<tr class="epkb-admin__items-list__row-field">
			<td colspan="<?php echo esc_attr( $columns_count ); ?>">
				<p class="epkb-admin__items-list__row-field__title"><?php echo esc_html( $field_title ); ?>:</p>
				<div class="epkb-admin__items-list__row-field__content"><?php echo wp_kses_post( wpautop( $field_value ) ); ?></div></td>
		</tr>   <?php
	}
}