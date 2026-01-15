<?php
namespace Bookly\Backend\Components\Dialogs\MobileStaffCabinet\AccessEdit;

use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Dialogs;
use Bookly\Lib;

class Dialog extends Lib\Base\Component
{
    /**
     * Render create/edit access dialog.
     */
    public static function render()
    {
        // Required
        Dialogs\Queue\Dialog::render();

        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ) ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/grant-auth-dialog.js' => array( 'bookly-backend-globals', 'bookly-queue-dialog.js' ), ),
        ) );

        $staff_members = Lib\Entities\Staff::query( 's')
            ->select( 's.id, s.full_name' )
            ->leftJoin( 'Auth', 'a', 'a.staff_id = s.id' )
            ->sortBy( 's.full_name' )
            ->whereNot( 's.visibility', 'archive' )
            ->where( 'a.token', null )
            ->fetchArray();

        $expressions = array(
            'fields' => array( 'ID', 'display_name' ),
            'orderby' => 'display_name',
        );
        if ( is_multisite() ) {
            $expressions[] = array(
                'blog_id' => get_current_blog_id(),
            );
        }

        $users = get_users( $expressions );

        $wp_roles = wp_roles();
        $role_names = $wp_roles->get_names();

        $users_list = array(
            'with_access' => array(),
            'without_access' => array(),
        );
        foreach ( $users as $user ) {
            $item = array(
                'id' => $user->ID,
                'full_name' => $user->display_name,
                'is_bookly_admin' => user_can( $user->ID, 'manage_bookly' ) || user_can( $user->ID, 'manage_options' ),
                'is_bookly_supervisor' => user_can( $user->ID, 'manage_bookly_appointments' ),
                'info' => '',
            );

            $user_data = get_userdata( $user->ID );
            $role = '';
            if ( $user_data && ! empty( $user_data->roles ) ) {
                $names = array();
                foreach ( $user_data->roles as $role_slug ) {
                    $names[] = isset( $role_names[ $role_slug ] ) ? $role_names[ $role_slug ] : $role_slug;
                }
                $role = implode( ', ', $names );
            }
            $item['info'] = $role;
            if ( $item['is_bookly_admin'] || $item['is_bookly_supervisor'] ) {
                $users_list['with_access'][] = $item;
            } else {
                $users_list['without_access'][] = $item;
            }
        }

        wp_localize_script( 'bookly-grant-auth-dialog.js', 'BooklyL10nGrantAuthDialog', array(
            'staff_members' => $staff_members,
            'users_list' => $users_list,
            'l10n' => array(
                'associate_token_with' => __( 'Associate token with', 'bookly' ),
                'associate_token_with_info' => __( 'By associating a token with a WordPress user, you can provide administrative access to Bookly from the mobile app. The level of access is determined by the user\'s role', 'bookly' ),
                'close' => __( 'Close', 'bookly' ),
                'edit_item' => __( 'Edit access token', 'bookly' ),
                'new_item' => __( 'Create new access token', 'bookly' ),
                'save' => __( 'Save', 'bookly' ),
                'select_wp_user' => __( 'Select WordPress user', 'bookly' ),
                'send_notifications' => __( 'Send notification with access data', 'bookly' ),
                'settings_saved' => __( 'Settings saved.', 'bookly' ),
                'staff' => __( 'Staff', 'bookly' ),
                'with_access' => __( 'With access to Bookly', 'bookly' ),
                'without_access' => __( 'Without access to Bookly', 'bookly' ),
                'wp_user' => __( 'WordPress user', 'bookly' ),
            )
        ) );
    }

    /**
     * Render button
     */
    public static function renderNewToken()
    {
        print '<div class="col-auto">';
        Buttons::renderAdd( 'bookly-js-new-key', 'btn-success', __( 'New access token', 'bookly' ) );
        print '</div>';
    }
}