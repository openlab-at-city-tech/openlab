<?php

defined('ABSPATH') || exit;

/**
 * Class MC4WP_Registration_Form_Integration
 *
 * @ignore
 */
class MC4WP_Registration_Form_Integration extends MC4WP_User_Integration
{
    /**
     * @var string
     */
    public $name = 'Registration Form';

    /**
     * @var string
     */
    public $description = 'Subscribes people from your WordPress registration form.';

    /**
     * @var bool
     */
    public $shown = false;

    /**
     * Add hooks
     */
    public function add_hooks()
    {
        if (! $this->options['implicit']) {
            add_action('login_head', [ $this, 'print_css_reset' ]);
            add_action('um_after_register_fields', [ $this, 'maybe_output_checkbox' ], 20);
            add_action('register_form', [ $this, 'maybe_output_checkbox' ], 20);
            add_action('woocommerce_register_form', [ $this, 'maybe_output_checkbox' ], 20);
            add_action('user_new_form', [ $this, 'maybe_output_user_new_checkbox' ], 20);
        }

        add_action('um_user_register', [ $this, 'subscribe_from_registration' ], 90, 1);
        add_action('user_register', [ $this, 'subscribe_from_registration' ], 90, 1);

        if (defined('um_plugin') && class_exists('UM')) {
            $this->name        = 'UltimateMember';
            $this->description = 'Subscribes people from your UltimateMember registration form.';
        }
    }

    /**
     * Output checkbox in the admin "Add New User" form.
     */
    public function maybe_output_user_new_checkbox($form_type)
    {
        if ('add-new-user' !== $form_type || $this->shown) {
            return;
        }

        $this->shown = true;
        $this->output_user_new_checkbox();
    }

    /**
     * Output checkbox as a form-table row.
     */
    public function output_user_new_checkbox()
    {
        $checkbox_id = esc_attr($this->checkbox_name);
        echo '<table class="form-table" role="presentation">';
        echo '<tr>';
        echo '<th scope="row">', esc_html__('Mailchimp subscribe', 'mailchimp-for-wp'), '</th>';
        echo '<td>';
        $this->output_checkbox();
        echo '</td>';
        echo '</tr>';
        echo '</table>';
    }

    /**
     * Output checkbox, once.
     */
    public function maybe_output_checkbox()
    {
        if (! $this->shown) {
            $this->output_checkbox();
            $this->shown = true;
        }
    }

    /**
     * Subscribes from WP Registration Form
     *
     * @param int $user_id
     *
     * @return bool|string
     */
    public function subscribe_from_registration($user_id)
    {
        // was sign-up checkbox checked?
        if (! $this->triggered()) {
            return false;
        }

        // gather emailadress from user who WordPress registered
        $user = get_userdata($user_id);

        // was a user found with the given ID?
        if (! $user instanceof WP_User) {
            return false;
        }

        $data = $this->user_merge_vars($user);

        return $this->subscribe($data, $user_id);
    }
    /* End registration form functions */


    /**
     * @return bool
     */
    public function is_installed()
    {
        return true;
    }
}
