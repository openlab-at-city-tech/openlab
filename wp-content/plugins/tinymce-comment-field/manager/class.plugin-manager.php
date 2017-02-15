<?php

class TMCECF_PluginManager {
    
    public static function isTitanEnabled() {
        global $tmcecf_titan_enabled;

        if (!isset($tmcecf_titan_enabled)) {
            $tmcecf_titan_enabled = false;

            if (class_exists('TitanFramework')) {
                $tmcecf_titan_enabled = true;
            }
        }

        return $tmcecf_titan_enabled;
    }

    public static function isTMCECFEnabled() {
        global $tmcecf_enabled;

        if (!isset($tmcecf_enabled) && self::isTitanEnabled()) {
            $titan = TitanFramework::getInstance('tinymce-comment-field');
            $tmcecf_enabled = $titan->getOption('enabled');
        } elseif (isset($tmcecf_enabled) && !self::isTitanEnabled()) {
            $tmcecf_enabled = false;
        }

        return $tmcecf_enabled;
    }

    public static function displayEditor() {
        global $tmcecf_display_editor;

        if (!isset($tmcecf_display_editor)):

            $tmcecf_display_editor = false;

            if (!self::isTitanEnabled()):
                $tmcecf_display_editor = false;
                return $tmcecf_display_editor;
            endif;

            if (!self::isTMCECFEnabled()):
                $tmcecf_display_editor = false;
                return $tmcecf_display_editor;
            endif;

            $titan = TitanFramework::getInstance('tinymce-comment-field');
            $mobile_browser_support = $titan->getOption('mobile-browser-support');

            if (!is_singular()):
                $tmcecf_display_editor = false;
                return $tmcecf_display_editor;
            endif;

            global $post;

            if (!comments_open($post->ID)):
                $tmcecf_display_editor = false;
                return $tmcecf_display_editor;
            endif;

            $enabled_on_object = get_post_meta($post->ID, 'tinymce-comment-field_enabled', true);

            if ($enabled_on_object === '0'):
                $tmcecf_display_editor = false;
                return $tmcecf_display_editor;
            endif;

            if (!$mobile_browser_support && wp_is_mobile()):
                $tmcecf_display_editor = false;
                return $tmcecf_display_editor;
            endif;

            $tmcecf_display_editor = user_can_richedit();

        endif;


        return $tmcecf_display_editor;
    }

    public static function imagesAllowed() {

        $titan = TitanFramework::getInstance('tinymce-comment-field');
        $images_allowed = $titan->getOption('allow_images_as_tag');
        $images_allowed_roles = $titan->getOption('allow_images_as_tag_roles');

        if (!$images_allowed) {
            return false;
        }

        if (!is_user_logged_in() && in_array('unregistered', $images_allowed_roles, true)) {
            return true;
        }

        if (is_user_logged_in()) {

            if(current_user_can('manage_options')) {
                return true;
            }

            $current_user = wp_get_current_user();

            foreach ($current_user->roles as $user_role) {
                if (in_array($user_role, $images_allowed_roles, true)) {
                    return true;
                }
            }
        }

        return false;
    }
}