<?php

namespace InstagramFeed\Vendor;

/**
 * Show messages
 *
 * This template can be overridden by copying it to yourtheme/smashballoon/Notification/templates/information.php.
 *
 * HOWEVER, on occasion Notices will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://smashballoon.com/doc/
 * @package Notification\templates
 * @version 1.0.0
 */
if (!\defined('ABSPATH')) {
    exit;
}
if (!$notice) {
    return;
}
/*
 * Fires before the notices are displayed.
 */
\do_action('sb_notices_before_information_notice');
echo $notice;
/*
 * Fires after the notices are displayed.
 */
\do_action('sb_notices_after_information_notice');
