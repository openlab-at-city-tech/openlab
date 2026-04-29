<?php
/**
 * Outputs the welcome step of the Onboarding Wizard.
 *
 * @since   3.59.4
 *
 * @package NextGEN Gallery
 */

?>
<!-- logo -->
<img width="339" src="<?php echo esc_url( trailingslashit( NGG_PLUGIN_URI ) . 'assets/images/logo.png' ); ?>" alt="Imagely" class="nextgen-gallery-onboarding-wizard-logo">
<div class="nextgen-gallery-onboarding-wizard-step nextgen-gallery-onboarding-wizard-step-welcome">
	<div class="nextgen-gallery-onboarding-welcome-content">
		<h1><?php esc_html_e( 'Welcome to Imagely Setup Wizard!', 'nggallery' ); ?></h1>
		<p><?php esc_html_e( 'Crafting exquisite, responsive photo galleries for your website takes just minutes. You are only 5 minutes away for creating your first gallery!', 'nggallery' ); ?></p>
		<div class="nextgen-gallery-onboarding-wizard-cta">
			<a href="#" class="button nextgen-gallery-onboarding-wizard-primary-btn btn-large nextgen-gallery-button-dark nextgen-gallery-button-primary" id="nextgen-gallery-get-started-btn"><?php esc_html_e( 'Let\'s Get Started → ', 'nggallery' ); ?></a>
		</div>
	</div>
</div>
<a href="<?php echo esc_url( admin_url( '/admin.php?page=imagely&tab=general' ) ); ?>" class="nextgen-gallery-onboarding-wizard-back-btn">←&nbsp;<?php esc_html_e( 'Go back to the dashboard', 'nggallery' ); ?></a>

