<?php defined( 'ABSPATH' ) || exit; ?>
<div class="dialog-widget dialog-lightbox-widget dialog-type-buttons dialog-type-lightbox elementor-templates-modal elementskit-dynamic-content-modal" id="elementor-template-widgetarea-modal-container" style="display: none;">
	<div class="dialog-widget-content dialog-lightbox-widget-content">
		<div class="dialog-header dialog-lightbox-header">
			<div class="elementor-templates-modal__header">
				<div class="elementor-templates-modal__header__logo-area">
					<div class="elementor-templates-modal__header__logo">
						<span class="elementor-templates-modal__header__logo__icon-wrapper">
						<i class="eicon-elementor"></i>
						</span>
						<span class="elementor-templates-modal__header__logo__title"><?php esc_html_e( 'Widget Area', 'elementskit-lite' ); ?></span>
					</div>
				</div>

				<div class="elementor-templates-modal__header__items-area">
					<div class="elementor-templates-modal__header__close elementor-templates-modal__header__close--normal elementor-templates-modal__header__item">
						<i class="eicon-close" aria-hidden="true" title="<?php echo esc_attr__( 'Close', 'elementskit-lite' ); ?>"></i>
						<span class="elementor-screen-only"><?php esc_html_e( 'Close', 'elementskit-lite' ); ?></span>
					</div>
				</div>
			</div>
		</div>
		<div class="dialog-message dialog-lightbox-message">
			<div class="dialog-content dialog-lightbox-content" style="display: block;">
				<div id="elementor-template-library-templates" data-template-source="remote">

					<div id="elementor-template-library-templates-container">
						<iframe id="widgetarea-control-iframe"></iframe>
					</div>
				</div>
			</div>
			<div class="dialog-loading dialog-lightbox-loading" style="display: block;">
				<div id="elementor-template-library-loading">
					<div class="elementor-loader-wrapper">
						<div class="elementor-loader">
							<div class="elementor-loader-boxes">
								<div class="elementor-loader-box"></div>
								<div class="elementor-loader-box"></div>
								<div class="elementor-loader-box"></div>
								<div class="elementor-loader-box"></div>
							</div>
						</div>
						<div class="elementor-loading-title"><?php esc_html_e( 'Loading', 'elementskit-lite' ); ?></div>
					</div>
				</div>
			</div>
		</div>
		<div class="dialog-buttons-wrapper dialog-lightbox-buttons-wrapper"></div>
	</div>
</div>
