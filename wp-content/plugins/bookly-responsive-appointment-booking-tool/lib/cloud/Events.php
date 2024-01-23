<?php
namespace Bookly\Lib\Cloud;

abstract class Events
{
    const ACCOUNT_LOGGED_OUT         = 'account::logged-out';
    const ACCOUNT_LOW_BALANCE        = 'account::low-balance';
    const ACCOUNT_PROFILE_LOADED     = 'account::profile-loaded';
    const ACCOUNT_PROFILE_NOT_LOADED = 'account::profile-not-loaded';

    const GENERAL_INFO_LOADED        = 'general::info-loaded';
    const GENERAL_INFO_NOT_LOADED    = 'general::info-not-loaded';
}