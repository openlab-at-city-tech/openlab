<?php

namespace Kubio\Theme;

use ColibriWP\Theme\Core\Utils;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Theme as ThemeBase;
use ColibriWP\Theme\View;
use ColibriWP\Theme\Translations;

class Theme extends ThemeBase {


	private $state = array();

	public function afterSetup() {
		parent::afterSetup();
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueThemeInfoPageScripts' ), 20 );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'addKubioOnboarding' ), 20 );

		add_action(
			'wp_ajax_kubio_onboarding_disable_notice',
			function () {
				check_ajax_referer( 'kubio_onboarding_disable_notice_nonce' );
				update_option( 'kubio-onboarding-notice-disabled', true );
			}
		);
	}

	public function addThemeInfoPage() {
		return;
	}

	public function enqueueThemeInfoPageScripts() {
		global $plugin_page;
		$slug = get_template() . '-page-info';

		if ( $plugin_page === $slug || $this->shouldDisplayAdminNotice() ) {
			wp_enqueue_style( $slug );
			wp_enqueue_script( $slug );
			wp_enqueue_script( 'wp-util' );
		}

		if ( $this->shouldDisplayAdminNotice() ) {
			ob_start();

			?>
            <script>
				jQuery(function ($) {
					$(".mistify-admin-big-notice").show();
				});
            </script>
			<?php
			$script = strip_tags( ob_get_clean() );
			wp_add_inline_script( 'jquery', $script );
		}
	}

	public function shouldDisplayAdminNotice() {
		global $pagenow;
		if ( Flags::get( 'kubio_activation_time', false ) ) {
			return false;
		}

		$slug = get_template() . '-page-info';
		$is_fresh_site = !$this->themeWasCustomized();

		if ( !$is_fresh_site ) {
			return false;
		}

		if ( get_option( "{$slug}-theme-notice-dismissed", false ) !== false ) {
			return false;
		}

		if ( apply_filters( 'kubio_is_enabled', false ) ) {
			return false;
		}

		if ( $pagenow === 'update.php' ) {
			return false;
		}

		return true;
	}

	public function addThemeNotice() {
		if ( $this->shouldDisplayAdminNotice() ) :
			?>
            <div class="notice notice-success is-dismissible mistify-admin-big-notice notice-large">
				<?php View::make( 'admin/admin-notice-frontpage' ); ?>
            </div>
            <script>

            </script>
		<?php
		endif;
	}

	public function addKubioOnboarding() {
		$notice_disabled = ! ! get_option( 'kubio-onboarding-notice-disabled', false );
		$is_fresh_site   = ! ! get_option( 'fresh_site' );
		$kubio_enabled   = apply_filters( 'kubio_is_enabled', false );

		if ( $notice_disabled || ! $is_fresh_site || $kubio_enabled ) {
			return;
		}

		?>
        <div class="kubio-onboarding kubio-step-1--active">
            <div>
                <!-- STEP 1 -->
                <div class="kubio-onboarding__wrapper kubio-step-1">
                    <div class="kubio-onboarding__wrapper__header">
                        <div class="kubio-onboarding__dismiss">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x"
                                 viewBox="0 0 16 16">
                                <path
                                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                            </svg>
                        </div>
                        <h2> <?php echo Translations::get( 'themebase_step1_title' ); ?> </h2>
                    </div>

                    <div class="kubio-onboarding__wrapper__content">
                        <label>
                            <input type="radio" id="kubio-install-new-homepage" name="kubio-onboarding-action"
                                   value="kubio-install-new-homepage" checked ai="true"></input>
                            <div class="radio-card">
                                <div class="radio-card__radio-checked">
									<?php echo file_get_contents( mistify_theme()->getThemeResource( 'images/theme-base/check.svg' ) ); ?>
                                    <div class="radio-card__radio-checked__background"></div>
                                </div>
                                <div class="radio-card__image-container">
                                    <img src="<?php echo mistify_theme()->getThemeResource( 'images/theme-base/web-layout.png' ); ?>"
                                         alt="web-layout" draggable="false">
                                </div>
                                <span>
									<p><?php echo Translations::get( 'themebase_step1_option1_title' ); ?></p>
									<p><?php echo Translations::get( 'themebase_step1_option1_subtitle' ); ?></p>
								</span>

                            </div>
                        </label>

                        <label>
                            <input type="radio" id="kubio-keep-current-layout" name="kubio-onboarding-action"
                                   value="kubio-keep-current-layout"></input>
                            <div class="radio-card">
                                <div class="radio-card__radio-checked">
									<?php echo file_get_contents( mistify_theme()->getThemeResource( 'images/theme-base/check.svg' ) ); ?>
                                    <div class="radio-card__radio-checked__background"></div>
                                </div>
                                <div class="radio-card__image-container">
                                    <img src="<?php echo mistify_theme()->getThemeResource( 'images/theme-base/blog-layout.png' ); ?>"
                                         alt="blog-layout" draggable="false">
                                </div>
                                <span>
									<p><?php echo Translations::get( 'themebase_step2_option1_title' ); ?></p>
									<p><?php echo Translations::get( 'themebase_step2_option1_subtitle' ); ?></p>
								</span>
                            </div>
                        </label>
                    </div>

                    <div class="kubio-onboarding__wrapper__footer">
                        <div>
                            <button class="button button-primary button-hero" data-action="continue">
								<?php echo Translations::get( 'continue' ); ?>
                            </button>
                        </div>
                    </div>
                </div>


                <!-- STEP 2 -->
                <div class="kubio-onboarding__wrapper kubio-step-2">
                    <div class="kubio-onboarding__wrapper__header">
                        <div class="kubio-onboarding__dismiss">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x"
                                 viewBox="0 0 16 16">
                                <path
                                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                            </svg>
                        </div>
                        <h2><?php Translations::escHtmlE( 'themebase_step2_title' ); ?></h2>
                    </div>

                    <div class="kubio-onboarding__wrapper__content">
                        <p><?php Translations::escHtmlE( 'theme_description', mistify_theme()->getName() ); ?></p>

                        <ul>
                            <li>
								<?php echo file_get_contents( mistify_theme()->getThemeResource( 'images/theme-base/round-check.svg' ) ); ?>
								<?php Translations::escHtmlE( 'benefit_1' ); ?>
                            </li>
                            <li>
								<?php echo file_get_contents( mistify_theme()->getThemeResource( 'images/theme-base/round-check.svg' ) ); ?>
								<?php Translations::escHtmlE( 'benefit_2' ); ?>
                            </li>
                            <li>
								<?php echo file_get_contents( mistify_theme()->getThemeResource( 'images/theme-base/round-check.svg' ) ); ?>
								<?php Translations::escHtmlE( 'benefit_3' ); ?>
                            </li>
                            <li>
								<?php echo file_get_contents( mistify_theme()->getThemeResource( 'images/theme-base/round-check.svg' ) ); ?>
								<?php Translations::escHtmlE( 'benefit_4' ); ?>
                            </li>
                        </ul>

                        <div class="kubio-onboarding__wrapper__content__buttons">
                            <button class="button button-primary button-hero" data-action="generate">
								<?php Translations::escHtmlE( 'themebase_step2_button1' ); ?>
                            </button>
                            <button class="button-link button-hero" data-action="default">
								<?php Translations::escHtmlE( 'themebase_step2_button2' ); ?>
                            </button>
                        </div>

                        <p><?php Translations::escHtmlE( 'themebase_step2_footer_info' ); ?></p>

                    </div>

                    <div class="kubio-onboarding__wrapper__footer"></div>
                </div>
            </div>
        </div>
		<?php
	}

	public function themeWasCustomized() {

		if ( Flags::get( 'theme_customized' ) ) {
			return true;
		}

		$mods              = get_theme_mods();
		$mods_keys         = array_keys( is_array( $mods ) ? $mods : array() );
		$default_keys      = array_keys( Defaults::getDefaults() );
		$default_blog_keys = array(
			'blog_post_thumb_placeholder_color',
			'blog_show_post_thumb_placeholder',
			'blog_posts_per_row',
			'blog_enable_masonry'
		);

		foreach ( $default_keys as $default_key ) {
			foreach ( $mods_keys as $mod_key ) {
				if ( in_array( $mod_key, $default_blog_keys) || strpos( $mod_key, "{$default_key}." ) === 0 ) {
					Flags::set( 'theme_customized', true );

					return true;
				}
			}
		}

		return false;
	}


	public function getState( $path, $fallback = null ) {
		return Utils::pathGet( $this->state, $path, $fallback );
	}

	public function setState( $path, $value ) {
		Utils::pathSet( $this->state, $path, $value );
	}

	public function deleteState( $path ) {
		Utils::pathDelete( $this->state, $path );
	}

	public function getName() {
		$slug = $this->getThemeSlug();
		$theme = $this->getTheme( $slug );

		return $theme->get( 'Name' );
	}

	public function getScreenshot() {
		$slug  = $this->getThemeSlug();
		$theme = $this->getTheme( $slug );

		return $theme->get_screenshot();
	}

	public function getFrontPagePreview() {
		return Theme::rootDirectoryUri() . '/resources/images/admin/admin-notice.png';
	}
	public function getListMarkerUrl() {
		return Theme::rootDirectoryUri() . '/resources/images/admin/checkmark.svg';
	}


	public function getThemeResource( $path ) {
		return Theme::rootDirectoryUri() . '/resources/' . $path;
	}

}
