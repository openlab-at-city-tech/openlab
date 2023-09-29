<?php

namespace SimpleCalendar\plugin_deps;

if (\class_exists('SimpleCalendar\\plugin_deps\\Google_Client', \false)) {
    // Prevent error with preloading in PHP 7.4
    // @see https://github.com/googleapis/google-api-php-client/issues/1976
    return;
}
$classMap = ['SimpleCalendar\\plugin_deps\\Google\\Client' => 'Google_Client', 'SimpleCalendar\\plugin_deps\\Google\\Service' => 'Google_Service', 'SimpleCalendar\\plugin_deps\\Google\\AccessToken\\Revoke' => 'Google_AccessToken_Revoke', 'SimpleCalendar\\plugin_deps\\Google\\AccessToken\\Verify' => 'Google_AccessToken_Verify', 'SimpleCalendar\\plugin_deps\\Google\\Model' => 'Google_Model', 'SimpleCalendar\\plugin_deps\\Google\\Utils\\UriTemplate' => 'Google_Utils_UriTemplate', 'SimpleCalendar\\plugin_deps\\Google\\AuthHandler\\Guzzle6AuthHandler' => 'Google_AuthHandler_Guzzle6AuthHandler', 'SimpleCalendar\\plugin_deps\\Google\\AuthHandler\\Guzzle7AuthHandler' => 'Google_AuthHandler_Guzzle7AuthHandler', 'SimpleCalendar\\plugin_deps\\Google\\AuthHandler\\Guzzle5AuthHandler' => 'Google_AuthHandler_Guzzle5AuthHandler', 'SimpleCalendar\\plugin_deps\\Google\\AuthHandler\\AuthHandlerFactory' => 'Google_AuthHandler_AuthHandlerFactory', 'SimpleCalendar\\plugin_deps\\Google\\Http\\Batch' => 'Google_Http_Batch', 'SimpleCalendar\\plugin_deps\\Google\\Http\\MediaFileUpload' => 'Google_Http_MediaFileUpload', 'SimpleCalendar\\plugin_deps\\Google\\Http\\REST' => 'Google_Http_REST', 'SimpleCalendar\\plugin_deps\\Google\\Task\\Retryable' => 'Google_Task_Retryable', 'SimpleCalendar\\plugin_deps\\Google\\Task\\Exception' => 'Google_Task_Exception', 'SimpleCalendar\\plugin_deps\\Google\\Task\\Runner' => 'Google_Task_Runner', 'SimpleCalendar\\plugin_deps\\Google\\Collection' => 'Google_Collection', 'SimpleCalendar\\plugin_deps\\Google\\Service\\Exception' => 'Google_Service_Exception', 'SimpleCalendar\\plugin_deps\\Google\\Service\\Resource' => 'Google_Service_Resource', 'SimpleCalendar\\plugin_deps\\Google\\Exception' => 'Google_Exception'];
foreach ($classMap as $class => $alias) {
    \class_alias($class, $alias);
}
/**
 * This class needs to be defined explicitly as scripts must be recognized by
 * the autoloader.
 */
class Google_Task_Composer extends \SimpleCalendar\plugin_deps\Google\Task\Composer
{
}
/**
 * This class needs to be defined explicitly as scripts must be recognized by
 * the autoloader.
 */
\class_alias('SimpleCalendar\\plugin_deps\\Google_Task_Composer', 'Google_Task_Composer', \false);
/** @phpstan-ignore-next-line */
if (\false) {
    class Google_AccessToken_Revoke extends \SimpleCalendar\plugin_deps\Google\AccessToken\Revoke
    {
    }
    class Google_AccessToken_Verify extends \SimpleCalendar\plugin_deps\Google\AccessToken\Verify
    {
    }
    class Google_AuthHandler_AuthHandlerFactory extends \SimpleCalendar\plugin_deps\Google\AuthHandler\AuthHandlerFactory
    {
    }
    class Google_AuthHandler_Guzzle5AuthHandler extends \SimpleCalendar\plugin_deps\Google\AuthHandler\Guzzle5AuthHandler
    {
    }
    class Google_AuthHandler_Guzzle6AuthHandler extends \SimpleCalendar\plugin_deps\Google\AuthHandler\Guzzle6AuthHandler
    {
    }
    class Google_AuthHandler_Guzzle7AuthHandler extends \SimpleCalendar\plugin_deps\Google\AuthHandler\Guzzle7AuthHandler
    {
    }
    class Google_Client extends \SimpleCalendar\plugin_deps\Google\Client
    {
    }
    class Google_Collection extends \SimpleCalendar\plugin_deps\Google\Collection
    {
    }
    class Google_Exception extends \SimpleCalendar\plugin_deps\Google\Exception
    {
    }
    class Google_Http_Batch extends \SimpleCalendar\plugin_deps\Google\Http\Batch
    {
    }
    class Google_Http_MediaFileUpload extends \SimpleCalendar\plugin_deps\Google\Http\MediaFileUpload
    {
    }
    class Google_Http_REST extends \SimpleCalendar\plugin_deps\Google\Http\REST
    {
    }
    class Google_Model extends \SimpleCalendar\plugin_deps\Google\Model
    {
    }
    class Google_Service extends \SimpleCalendar\plugin_deps\Google\Service
    {
    }
    class Google_Service_Exception extends \SimpleCalendar\plugin_deps\Google\Service\Exception
    {
    }
    class Google_Service_Resource extends \SimpleCalendar\plugin_deps\Google\Service\Resource
    {
    }
    class Google_Task_Exception extends \SimpleCalendar\plugin_deps\Google\Task\Exception
    {
    }
    interface Google_Task_Retryable extends \SimpleCalendar\plugin_deps\Google\Task\Retryable
    {
    }
    class Google_Task_Runner extends \SimpleCalendar\plugin_deps\Google\Task\Runner
    {
    }
    class Google_Utils_UriTemplate extends \SimpleCalendar\plugin_deps\Google\Utils\UriTemplate
    {
    }
}
