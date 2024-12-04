<?php

namespace SuperbThemesThemeInformationContent;

use SuperbThemesThemeInformationContent\AdminNotices\AdminNoticeController;
use SuperbThemesThemeInformationContent\Templates\TemplateInformationController;
use SuperbThemesThemeInformationContent\ThemePage\ThemePageController;

defined('ABSPATH') || exit();

class ThemeEntryPoint
{
    const Version = '1.0';

    private static function InitializeThemePage($options)
    {
        ThemePageController::init($options);
    }

    private static function InitializeAdminNotices($options)
    {
        AdminNoticeController::init($options);
    }

    public static function InitializeTemplateFilter($options)
    {
        TemplateInformationController::init($options);
    }

    public static function init($options)
    {
        self::InitializeThemePage($options);
        self::InitializeAdminNotices($options);
        self::InitializeTemplateFilter($options);
        add_action('switch_theme', array(__CLASS__, 'ThemeCleanup'));
    }

    public static function ThemeCleanup()
    {
        AdminNoticeController::Cleanup();
        ThemePageController::Cleanup();
    }
}
