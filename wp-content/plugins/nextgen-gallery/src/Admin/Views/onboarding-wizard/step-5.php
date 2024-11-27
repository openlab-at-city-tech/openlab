<?php
/**
 * Outputs the first step of the Onboarding Wizard.
 *
 * @since   3.59.4
 *
 * @package NextGEN Gallery
 */

?>
<div class="nextgen-gallery-onboarding-form-step nextgen-gallery-wizard-success nextgen-gallery-onboarding-step-5" id="resources">
	<div class="nextgen-gallery-onboarding-wizard-body">
		<div class="steps"><?php esc_html_e( 'Step - 5 of 5', 'nextgen-gallery' ); ?></div>
		<div class="nextgen-gallery-onboarding-settings-row no-border ">
			<div class="settings-name">
				<h2><?php esc_html_e( 'Congratulations!', 'nextgen-gallery' ); ?><br/>
					<?php esc_html_e( 'Your system is all set to help you create your first gallery.', 'nextgen-gallery' ); ?>
				</h2>
				<div class="name small-margin">
				</div>
				<div class="nextgen-gallery-onboarding-description"><?php esc_html_e( 'Need Help? Here’s what to do next', 'nextgen-gallery' ); ?></div>
			</div>
			<div class="nextgen-gallery-onboarding-input-container">
				<div class="nextgen-gallery-onboarding-input">
				</div>
			</div>
		</div>
		<div class="actions">
			<div class="">
				<div class="icon">
					<svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="ngg-book"><path d="M14.5834 3.75C12.9584 3.75 11.2084 4.08333 10 5C8.79171 4.08333 7.04171 3.75 5.41671 3.75C4.20837 3.75 2.92504 3.93333 1.85004 4.40833C1.24171 4.68333 0.833374 5.275 0.833374 5.95V15.35C0.833374 16.4333 1.85004 17.2333 2.90004 16.9667C3.71671 16.7583 4.58337 16.6667 5.41671 16.6667C6.71671 16.6667 8.10004 16.8833 9.21671 17.4333C9.71671 17.6833 10.2834 17.6833 10.775 17.4333C11.8917 16.875 13.275 16.6667 14.575 16.6667C15.4084 16.6667 16.275 16.7583 17.0917 16.9667C18.1417 17.2417 19.1584 16.4417 19.1584 15.35V5.95C19.1584 5.275 18.75 4.68333 18.1417 4.40833C17.075 3.93333 15.7917 3.75 14.5834 3.75ZM17.5 14.3583C17.5 14.8833 17.0167 15.2667 16.5 15.175C15.875 15.0583 15.225 15.0083 14.5834 15.0083C13.1667 15.0083 11.125 15.55 10 16.2583V6.66667C11.125 5.95833 13.1667 5.41667 14.5834 5.41667C15.35 5.41667 16.1084 5.49167 16.8334 5.65C17.2167 5.73333 17.5 6.075 17.5 6.46667V14.3583Z" fill="currentColor"></path><path d="M11.65 9.17504C11.3833 9.17504 11.1416 9.00837 11.0583 8.74171C10.95 8.41671 11.1333 8.05838 11.4583 7.95838C12.7416 7.54171 14.4 7.40838 15.925 7.58338C16.2666 7.62504 16.5166 7.93338 16.475 8.27504C16.4333 8.61671 16.125 8.86671 15.7833 8.82504C14.4333 8.66671 12.9583 8.79171 11.8416 9.15004C11.775 9.15837 11.7083 9.17504 11.65 9.17504ZM11.65 11.3917C11.3833 11.3917 11.1416 11.225 11.0583 10.9584C10.95 10.6334 11.1333 10.275 11.4583 10.175C12.7333 9.75837 14.4 9.62504 15.925 9.80004C16.2666 9.84171 16.5166 10.15 16.475 10.4917C16.4333 10.8334 16.125 11.0834 15.7833 11.0417C14.4333 10.8834 12.9583 11.0084 11.8416 11.3667C11.779 11.3827 11.7146 11.3911 11.65 11.3917ZM11.65 13.6084C11.3833 13.6084 11.1416 13.4417 11.0583 13.175C10.95 12.85 11.1333 12.4917 11.4583 12.3917C12.7333 11.975 14.4 11.8417 15.925 12.0167C16.2666 12.0584 16.5166 12.3667 16.475 12.7084C16.4333 13.05 16.125 13.2917 15.7833 13.2584C14.4333 13.1 12.9583 13.225 11.8416 13.5834C11.779 13.5993 11.7146 13.6077 11.65 13.6084Z" fill="currentColor"></path></svg>
				</div>
				<div class="content">
					<div><a target="_blank" href="<?php echo esc_url( 'https://www.imagely.com/docs/nextgen-gallery/?utm_source=WordPress&utm_campaign=nextgen-galleryliteplugin&utm_medium=nextgen-gallery-wizard-success' ); ?>" title="<?php esc_attr_e( 'Read our Step by Step Guide to Create and Share your Gallery' ); ?>" ><?php esc_html_e( 'Read our Step by Step Guide to Create and Share your Gallery', 'nextgen-gallery' ); ?></a></div>
				</div>
			</div>
			<div class="">
				<div class="icon">
					<svg viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg"><path d="m12.539257 8.77788c1.505652 1.72253.111482 4.22212-2.460402 4.22212-1.0607676 0-2.1113566-.47376-2.6823746-1.34036-.184473.01-.608089.01-.792589 0-.57225.86842-1.623857 1.34036-2.682402 1.34036-2.557768 0-3.973607-2.49096-2.460402-4.22212-2.119794-3.53173.837456-7.77788 5.539072-7.77788 4.7044276 0 7.6572316 4.24891 5.5390976 7.77788zm-9.0976876-2.22247h1.112384v-1.55558h-1.112384zm5.817322 2.3055v-.63854c-1.620697.56022-3.545223.24637-5.024706-.90994l.0066.66688c1.368776 1.24198 3.52891 1.55023 5.018116.8816zm-4.038027-2.3055h4.447982v-1.55558h-4.447982z"/></svg>
				</div>
				<div class="content">
					<div><a target="_blank" href="<?php echo esc_url( 'https://www.wpbeginner.com/?utm_source=WordPress&utm_campaign=nextgen-galleryliteplugin&utm_medium=nextgen-gallery-wizard-success' ); ?>" title="<?php esc_attr_e( 'Learn WordPress Tutorials' ); ?>" ><?php esc_html_e( 'Learn WordPress Tutorials', 'nextgen-gallery' ); ?></a></div>
				</div>
			</div>
		</div>
	</div>
	<div class="nextgen-gallery-onboarding-wizard-footer">
		<div class="go-back"><a href="#summary" data-prev="3" class="nextgen-gallery-onboarding-wizard-back-btn nextgen-gallery-onboarding-btn-prev" id="" >←&nbsp;<?php esc_html_e( 'Go back', 'nextgen-gallery' ); ?></a></div>
		<div class="spacer"></div>
		<a class="btn btn-transparent" href="<?php echo esc_url( admin_url( '/admin.php?page=ngg_other_options' ) ); ?>"><?php esc_html_e( 'Go to settings', 'nextgen-gallery' ); ?></a>
		<a class="btn nextgen-gallery-onboarding-wizard-primary-btn" href="<?php echo esc_url( admin_url( '/admin.php?page=ngg_addgallery' ) ); ?>"><?php esc_html_e( 'Create your first gallery', 'nextgen-gallery' ); ?>&nbsp; →</a>
	</div>
</div>
