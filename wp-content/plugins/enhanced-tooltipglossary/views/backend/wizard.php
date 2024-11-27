<div class="cm-wizard-step step-0">
    <h1>Welcome to the Glossary Setup Wizard</h1>
    <p>Thank you for installing the CM Tooltip Glossary plugin!</p>
    <p>This plugin enhances your website by allowing you to create a glossary of terms related to your content and add interactive tooltips for those terms.</p>
    <p>This makes your content more informative and engaging for your visitors.</p>
    <img class="img" src="<?php echo CMTT_PLUGIN_URL . 'assets/img/wizard_logo.png';?>">
    <p>To help you get started, we’ve created a quick setup wizard that will guide you through the following steps:</p>
    <ul>
        <li>• Configuring important settings</li>
        <li>• Customizing the appearance of tooltips and glossary pages</li>
        <li>• Adding your first glossary term</li>
    </ul>
    <button class="next-step" data-step="0">Start</button>
    <p><a href="<?php echo admin_url( 'admin.php?page=cmtt_settings' ); ?>" >Skip the setup wizard</a></p>
</div>
<?php echo CMTT_SetupWizard::renderSteps(); ?>