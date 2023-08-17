<?php
namespace Bookly\Backend\Modules\Diagnostics\Tools;

/**
 * Class Roles
 *
 * @package Bookly\Backend\Modules\Diagnostics\Tools
 */
class Roles extends Tool
{
    protected $slug = 'roles';
    protected $hidden = true;
    protected $title = 'User Roles';
    protected $has_error = false;
    protected $fixable = true;

    protected $roles = array(
        'bookly_supervisor' => array( 'title' => 'Bookly Supervisor', 'capabilities' => array( 'view_admin_dashboard', 'manage_bookly_appointments' ) ),
        'bookly_administrator' => array( 'title' => 'Bookly Administrator', 'capabilities' => array( 'view_admin_dashboard', 'manage_bookly' ) ),
    );

    public function __construct()
    {
        foreach ( $this->roles as $role_name => $data ) {
            if ( ! $role = get_role( $role_name ) ) {
                $this->has_error = true;
                break;
            }

            $role_capabilities = array_keys( $role->capabilities );
            foreach ( $data['capabilities'] as $capability ) {
                if ( ! in_array( $capability, $role_capabilities, true ) ) {
                    $this->has_error = true;
                    break;
                }
            }
        }

        if ( $this->has_error && ( ! current_user_can( 'create_users' ) || ! current_user_can( 'edit_users' ) || ! current_user_can( 'promote_users' ) ) ) {
            $this->fixable = false;
        }
    }

    public function render()
    {
        return self::renderTemplate( '_roles', array( 'roles' => $this->roles, 'has_error' => $this->has_error, 'fixable' => $this->fixable ), false );
    }

    public function hasError()
    {
        return $this->has_error;
    }

    /**
     * Fix roles
     *
     * @return void
     */
    public function fixRoles()
    {
        $base_capabilities = array();
        if ( $subscriber = get_role( 'subscriber' ) ) {
            $base_capabilities = $subscriber->capabilities;
        }
        foreach ( $this->roles as $role_name => $data ) {
            $role_capabilities = $base_capabilities;
            foreach ( $data['capabilities'] as $capability ) {
                $role_capabilities[ $capability ] = true;
            }
            $role = get_role( $role_name );
            if ( $role ) {
                foreach ( $role_capabilities as $capability => $value ) {
                    if ( ! in_array( $capability, $role->capabilities, true ) ) {
                        $role->add_cap( $capability );
                    }
                }
            } else {
                add_role( $role_name, $data['title'], $role_capabilities );
            }
        }

        wp_send_json_success();
    }
}