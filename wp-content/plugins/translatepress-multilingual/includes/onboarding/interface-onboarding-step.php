<?php
if ( ! defined( 'ABSPATH' ) ) exit;

interface TRP_Onboarding_Step_Interface {
    /**
     * Handle form submission logic for the step.
     *
     * @param array $data
     * @return void
     */
    public function handle( $data );

    /**
     * Render the step's HTML output.
     *
     * @return void
     */
    public function render();
}
