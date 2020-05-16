<?php

if (!class_exists('C_NextGen_Settings'))
{
    class C_NextGen_Settings
    {
        /**
         * @return C_Photocrati_Settings_Manager
         */
        static function get_instance()
        {
            return C_Photocrati_Settings_Manager::get_instance();
        }
    }
}

if (!class_exists('C_NextGen_Global_Settings'))
{
    class C_NextGen_Global_Settings extends C_NextGen_Settings
    {
        static function get_instance()
        {
            if (is_multisite())
                return C_Photocrati_Global_Settings_Manager::get_instance();
            else
                return C_Photocrati_Settings_Manager::get_instance();
        }
    }
}