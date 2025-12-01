<?php

// Functions and constants
namespace StellarWP\Shepherd {
    if(!function_exists('\\StellarWP\\Shepherd\\shepherd')){
        function shepherd(...$args) {
            return \TEC\Common\StellarWP\Shepherd\shepherd(...func_get_args());
        }
    }
}
namespace StellarWP\Uplink {
    if(!function_exists('\\StellarWP\\Uplink\\get_container')){
        function get_container(...$args) {
            return \TEC\Common\StellarWP\Uplink\get_container(...func_get_args());
        }
    }
    if(!function_exists('\\StellarWP\\Uplink\\render_authorize_button')){
        function render_authorize_button(...$args) {
            return \TEC\Common\StellarWP\Uplink\render_authorize_button(...func_get_args());
        }
    }
    if(!function_exists('\\StellarWP\\Uplink\\get_authorization_token')){
        function get_authorization_token(...$args) {
            return \TEC\Common\StellarWP\Uplink\get_authorization_token(...func_get_args());
        }
    }
    if(!function_exists('\\StellarWP\\Uplink\\is_authorized')){
        function is_authorized(...$args) {
            return \TEC\Common\StellarWP\Uplink\is_authorized(...func_get_args());
        }
    }
    if(!function_exists('\\StellarWP\\Uplink\\is_user_authorized')){
        function is_user_authorized(...$args) {
            return \TEC\Common\StellarWP\Uplink\is_user_authorized(...func_get_args());
        }
    }
    if(!function_exists('\\StellarWP\\Uplink\\build_auth_url')){
        function build_auth_url(...$args) {
            return \TEC\Common\StellarWP\Uplink\build_auth_url(...func_get_args());
        }
    }
    if(!function_exists('\\StellarWP\\Uplink\\get_resource')){
        function get_resource(...$args) {
            return \TEC\Common\StellarWP\Uplink\get_resource(...func_get_args());
        }
    }
    if(!function_exists('\\StellarWP\\Uplink\\get_license_key')){
        function get_license_key(...$args) {
            return \TEC\Common\StellarWP\Uplink\get_license_key(...func_get_args());
        }
    }
    if(!function_exists('\\StellarWP\\Uplink\\set_license_key')){
        function set_license_key(...$args) {
            return \TEC\Common\StellarWP\Uplink\set_license_key(...func_get_args());
        }
    }
    if(!function_exists('\\StellarWP\\Uplink\\get_disconnect_url')){
        function get_disconnect_url(...$args) {
            return \TEC\Common\StellarWP\Uplink\get_disconnect_url(...func_get_args());
        }
    }
    if(!function_exists('\\StellarWP\\Uplink\\get_auth_url')){
        function get_auth_url(...$args) {
            return \TEC\Common\StellarWP\Uplink\get_auth_url(...func_get_args());
        }
    }
    if(!function_exists('\\StellarWP\\Uplink\\get_license_domain')){
        function get_license_domain(...$args) {
            return \TEC\Common\StellarWP\Uplink\get_license_domain(...func_get_args());
        }
    }
    if(!function_exists('\\StellarWP\\Uplink\\get_field')){
        function get_field(...$args) {
            return \TEC\Common\StellarWP\Uplink\get_field(...func_get_args());
        }
    }
    if(!function_exists('\\StellarWP\\Uplink\\get_form')){
        function get_form(...$args) {
            return \TEC\Common\StellarWP\Uplink\get_form(...func_get_args());
        }
    }
    if(!function_exists('\\StellarWP\\Uplink\\get_plugins')){
        function get_plugins(...$args) {
            return \TEC\Common\StellarWP\Uplink\get_plugins(...func_get_args());
        }
    }
}


namespace TEC\Common {

    class AliasAutoloader
    {
        private string $includeFilePath;

        private array $autoloadAliases = array (
  'Firebase\\JWT\\BeforeValidException' => 
  array (
    'type' => 'class',
    'classname' => 'BeforeValidException',
    'isabstract' => false,
    'namespace' => 'Firebase\\JWT',
    'extends' => 'TEC\\Common\\Firebase\\JWT\\BeforeValidException',
    'implements' => 
    array (
    ),
  ),
  'Firebase\\JWT\\CachedKeySet' => 
  array (
    'type' => 'class',
    'classname' => 'CachedKeySet',
    'isabstract' => false,
    'namespace' => 'Firebase\\JWT',
    'extends' => 'TEC\\Common\\Firebase\\JWT\\CachedKeySet',
    'implements' => 
    array (
      0 => 'ArrayAccess',
    ),
  ),
  'Firebase\\JWT\\ExpiredException' => 
  array (
    'type' => 'class',
    'classname' => 'ExpiredException',
    'isabstract' => false,
    'namespace' => 'Firebase\\JWT',
    'extends' => 'TEC\\Common\\Firebase\\JWT\\ExpiredException',
    'implements' => 
    array (
    ),
  ),
  'Firebase\\JWT\\JWK' => 
  array (
    'type' => 'class',
    'classname' => 'JWK',
    'isabstract' => false,
    'namespace' => 'Firebase\\JWT',
    'extends' => 'TEC\\Common\\Firebase\\JWT\\JWK',
    'implements' => 
    array (
    ),
  ),
  'Firebase\\JWT\\JWT' => 
  array (
    'type' => 'class',
    'classname' => 'JWT',
    'isabstract' => false,
    'namespace' => 'Firebase\\JWT',
    'extends' => 'TEC\\Common\\Firebase\\JWT\\JWT',
    'implements' => 
    array (
    ),
  ),
  'Firebase\\JWT\\Key' => 
  array (
    'type' => 'class',
    'classname' => 'Key',
    'isabstract' => false,
    'namespace' => 'Firebase\\JWT',
    'extends' => 'TEC\\Common\\Firebase\\JWT\\Key',
    'implements' => 
    array (
    ),
  ),
  'Firebase\\JWT\\SignatureInvalidException' => 
  array (
    'type' => 'class',
    'classname' => 'SignatureInvalidException',
    'isabstract' => false,
    'namespace' => 'Firebase\\JWT',
    'extends' => 'TEC\\Common\\Firebase\\JWT\\SignatureInvalidException',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\App' => 
  array (
    'type' => 'class',
    'classname' => 'App',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'TEC\\Common\\lucatume\\DI52\\App',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\Builders\\CallableBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'CallableBuilder',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'TEC\\Common\\lucatume\\DI52\\Builders\\CallableBuilder',
    'implements' => 
    array (
      0 => 'lucatume\\DI52\\Builders\\BuilderInterface',
      1 => 'lucatume\\DI52\\Builders\\ReinitializableBuilderInterface',
    ),
  ),
  'lucatume\\DI52\\Builders\\ClassBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'ClassBuilder',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'TEC\\Common\\lucatume\\DI52\\Builders\\ClassBuilder',
    'implements' => 
    array (
      0 => 'lucatume\\DI52\\Builders\\BuilderInterface',
      1 => 'lucatume\\DI52\\Builders\\ReinitializableBuilderInterface',
    ),
  ),
  'lucatume\\DI52\\Builders\\ClosureBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'ClosureBuilder',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'TEC\\Common\\lucatume\\DI52\\Builders\\ClosureBuilder',
    'implements' => 
    array (
      0 => 'lucatume\\DI52\\Builders\\BuilderInterface',
    ),
  ),
  'lucatume\\DI52\\Builders\\Factory' => 
  array (
    'type' => 'class',
    'classname' => 'Factory',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'TEC\\Common\\lucatume\\DI52\\Builders\\Factory',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\Builders\\Parameter' => 
  array (
    'type' => 'class',
    'classname' => 'Parameter',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'TEC\\Common\\lucatume\\DI52\\Builders\\Parameter',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\Builders\\Resolver' => 
  array (
    'type' => 'class',
    'classname' => 'Resolver',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'TEC\\Common\\lucatume\\DI52\\Builders\\Resolver',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\Builders\\ValueBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'ValueBuilder',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'TEC\\Common\\lucatume\\DI52\\Builders\\ValueBuilder',
    'implements' => 
    array (
      0 => 'lucatume\\DI52\\Builders\\BuilderInterface',
    ),
  ),
  'lucatume\\DI52\\Container' => 
  array (
    'type' => 'class',
    'classname' => 'Container',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'TEC\\Common\\lucatume\\DI52\\Container',
    'implements' => 
    array (
      0 => 'ArrayAccess',
      1 => 'Psr\\Container\\ContainerInterface',
    ),
  ),
  'lucatume\\DI52\\ContainerException' => 
  array (
    'type' => 'class',
    'classname' => 'ContainerException',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'TEC\\Common\\lucatume\\DI52\\ContainerException',
    'implements' => 
    array (
      0 => 'Psr\\Container\\ContainerExceptionInterface',
    ),
  ),
  'lucatume\\DI52\\NestedParseError' => 
  array (
    'type' => 'class',
    'classname' => 'NestedParseError',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'TEC\\Common\\lucatume\\DI52\\NestedParseError',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\NotFoundException' => 
  array (
    'type' => 'class',
    'classname' => 'NotFoundException',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'TEC\\Common\\lucatume\\DI52\\NotFoundException',
    'implements' => 
    array (
      0 => 'Psr\\Container\\NotFoundExceptionInterface',
    ),
  ),
  'lucatume\\DI52\\ServiceProvider' => 
  array (
    'type' => 'class',
    'classname' => 'ServiceProvider',
    'isabstract' => true,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'TEC\\Common\\lucatume\\DI52\\ServiceProvider',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Attribute\\AsMonologProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'AsMonologProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Attribute',
    'extends' => 'TEC\\Common\\Monolog\\Attribute\\AsMonologProcessor',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\DateTimeImmutable' => 
  array (
    'type' => 'class',
    'classname' => 'DateTimeImmutable',
    'isabstract' => false,
    'namespace' => 'Monolog',
    'extends' => 'TEC\\Common\\Monolog\\DateTimeImmutable',
    'implements' => 
    array (
      0 => 'JsonSerializable',
    ),
  ),
  'Monolog\\ErrorHandler' => 
  array (
    'type' => 'class',
    'classname' => 'ErrorHandler',
    'isabstract' => false,
    'namespace' => 'Monolog',
    'extends' => 'TEC\\Common\\Monolog\\ErrorHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\ChromePHPFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'ChromePHPFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'TEC\\Common\\Monolog\\Formatter\\ChromePHPFormatter',
    'implements' => 
    array (
      0 => 'Monolog\\Formatter\\FormatterInterface',
    ),
  ),
  'Monolog\\Formatter\\ElasticaFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'ElasticaFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'TEC\\Common\\Monolog\\Formatter\\ElasticaFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\ElasticsearchFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'ElasticsearchFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'TEC\\Common\\Monolog\\Formatter\\ElasticsearchFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\FlowdockFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'FlowdockFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'TEC\\Common\\Monolog\\Formatter\\FlowdockFormatter',
    'implements' => 
    array (
      0 => 'Monolog\\Formatter\\FormatterInterface',
    ),
  ),
  'Monolog\\Formatter\\FluentdFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'FluentdFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'TEC\\Common\\Monolog\\Formatter\\FluentdFormatter',
    'implements' => 
    array (
      0 => 'Monolog\\Formatter\\FormatterInterface',
    ),
  ),
  'Monolog\\Formatter\\GelfMessageFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'GelfMessageFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'TEC\\Common\\Monolog\\Formatter\\GelfMessageFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\GoogleCloudLoggingFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'GoogleCloudLoggingFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'TEC\\Common\\Monolog\\Formatter\\GoogleCloudLoggingFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\HtmlFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'HtmlFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'TEC\\Common\\Monolog\\Formatter\\HtmlFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\JsonFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'JsonFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'TEC\\Common\\Monolog\\Formatter\\JsonFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\LineFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'LineFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'TEC\\Common\\Monolog\\Formatter\\LineFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\LogglyFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'LogglyFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'TEC\\Common\\Monolog\\Formatter\\LogglyFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\LogmaticFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'LogmaticFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'TEC\\Common\\Monolog\\Formatter\\LogmaticFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\LogstashFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'LogstashFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'TEC\\Common\\Monolog\\Formatter\\LogstashFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\MongoDBFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'MongoDBFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'TEC\\Common\\Monolog\\Formatter\\MongoDBFormatter',
    'implements' => 
    array (
      0 => 'Monolog\\Formatter\\FormatterInterface',
    ),
  ),
  'Monolog\\Formatter\\NormalizerFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'NormalizerFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'TEC\\Common\\Monolog\\Formatter\\NormalizerFormatter',
    'implements' => 
    array (
      0 => 'Monolog\\Formatter\\FormatterInterface',
    ),
  ),
  'Monolog\\Formatter\\ScalarFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'ScalarFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'TEC\\Common\\Monolog\\Formatter\\ScalarFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\WildfireFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'WildfireFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'TEC\\Common\\Monolog\\Formatter\\WildfireFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\AbstractHandler' => 
  array (
    'type' => 'class',
    'classname' => 'AbstractHandler',
    'isabstract' => true,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\AbstractHandler',
    'implements' => 
    array (
      0 => 'Monolog\\ResettableInterface',
    ),
  ),
  'Monolog\\Handler\\AbstractProcessingHandler' => 
  array (
    'type' => 'class',
    'classname' => 'AbstractProcessingHandler',
    'isabstract' => true,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\AbstractProcessingHandler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\ProcessableHandlerInterface',
      1 => 'Monolog\\Handler\\FormattableHandlerInterface',
    ),
  ),
  'Monolog\\Handler\\AbstractSyslogHandler' => 
  array (
    'type' => 'class',
    'classname' => 'AbstractSyslogHandler',
    'isabstract' => true,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\AbstractSyslogHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\AmqpHandler' => 
  array (
    'type' => 'class',
    'classname' => 'AmqpHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\AmqpHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\BrowserConsoleHandler' => 
  array (
    'type' => 'class',
    'classname' => 'BrowserConsoleHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\BrowserConsoleHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\BufferHandler' => 
  array (
    'type' => 'class',
    'classname' => 'BufferHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\BufferHandler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\ProcessableHandlerInterface',
      1 => 'Monolog\\Handler\\FormattableHandlerInterface',
    ),
  ),
  'Monolog\\Handler\\ChromePHPHandler' => 
  array (
    'type' => 'class',
    'classname' => 'ChromePHPHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\ChromePHPHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\CouchDBHandler' => 
  array (
    'type' => 'class',
    'classname' => 'CouchDBHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\CouchDBHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\CubeHandler' => 
  array (
    'type' => 'class',
    'classname' => 'CubeHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\CubeHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\Curl\\Util' => 
  array (
    'type' => 'class',
    'classname' => 'Util',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler\\Curl',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\Curl\\Util',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\DeduplicationHandler' => 
  array (
    'type' => 'class',
    'classname' => 'DeduplicationHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\DeduplicationHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\DoctrineCouchDBHandler' => 
  array (
    'type' => 'class',
    'classname' => 'DoctrineCouchDBHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\DoctrineCouchDBHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\DynamoDbHandler' => 
  array (
    'type' => 'class',
    'classname' => 'DynamoDbHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\DynamoDbHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\ElasticaHandler' => 
  array (
    'type' => 'class',
    'classname' => 'ElasticaHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\ElasticaHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\ElasticsearchHandler' => 
  array (
    'type' => 'class',
    'classname' => 'ElasticsearchHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\ElasticsearchHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\ErrorLogHandler' => 
  array (
    'type' => 'class',
    'classname' => 'ErrorLogHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\ErrorLogHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\FallbackGroupHandler' => 
  array (
    'type' => 'class',
    'classname' => 'FallbackGroupHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\FallbackGroupHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\FilterHandler' => 
  array (
    'type' => 'class',
    'classname' => 'FilterHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\FilterHandler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\ProcessableHandlerInterface',
      1 => 'Monolog\\ResettableInterface',
      2 => 'Monolog\\Handler\\FormattableHandlerInterface',
    ),
  ),
  'Monolog\\Handler\\FingersCrossed\\ChannelLevelActivationStrategy' => 
  array (
    'type' => 'class',
    'classname' => 'ChannelLevelActivationStrategy',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler\\FingersCrossed',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\FingersCrossed\\ChannelLevelActivationStrategy',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\FingersCrossed\\ActivationStrategyInterface',
    ),
  ),
  'Monolog\\Handler\\FingersCrossed\\ErrorLevelActivationStrategy' => 
  array (
    'type' => 'class',
    'classname' => 'ErrorLevelActivationStrategy',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler\\FingersCrossed',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\FingersCrossed\\ErrorLevelActivationStrategy',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\FingersCrossed\\ActivationStrategyInterface',
    ),
  ),
  'Monolog\\Handler\\FingersCrossedHandler' => 
  array (
    'type' => 'class',
    'classname' => 'FingersCrossedHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\FingersCrossedHandler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\ProcessableHandlerInterface',
      1 => 'Monolog\\ResettableInterface',
      2 => 'Monolog\\Handler\\FormattableHandlerInterface',
    ),
  ),
  'Monolog\\Handler\\FirePHPHandler' => 
  array (
    'type' => 'class',
    'classname' => 'FirePHPHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\FirePHPHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\FleepHookHandler' => 
  array (
    'type' => 'class',
    'classname' => 'FleepHookHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\FleepHookHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\FlowdockHandler' => 
  array (
    'type' => 'class',
    'classname' => 'FlowdockHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\FlowdockHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\GelfHandler' => 
  array (
    'type' => 'class',
    'classname' => 'GelfHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\GelfHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\GroupHandler' => 
  array (
    'type' => 'class',
    'classname' => 'GroupHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\GroupHandler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\ProcessableHandlerInterface',
      1 => 'Monolog\\ResettableInterface',
    ),
  ),
  'Monolog\\Handler\\Handler' => 
  array (
    'type' => 'class',
    'classname' => 'Handler',
    'isabstract' => true,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\Handler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\HandlerInterface',
    ),
  ),
  'Monolog\\Handler\\HandlerWrapper' => 
  array (
    'type' => 'class',
    'classname' => 'HandlerWrapper',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\HandlerWrapper',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\HandlerInterface',
      1 => 'Monolog\\Handler\\ProcessableHandlerInterface',
      2 => 'Monolog\\Handler\\FormattableHandlerInterface',
      3 => 'Monolog\\ResettableInterface',
    ),
  ),
  'Monolog\\Handler\\IFTTTHandler' => 
  array (
    'type' => 'class',
    'classname' => 'IFTTTHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\IFTTTHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\InsightOpsHandler' => 
  array (
    'type' => 'class',
    'classname' => 'InsightOpsHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\InsightOpsHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\LogEntriesHandler' => 
  array (
    'type' => 'class',
    'classname' => 'LogEntriesHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\LogEntriesHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\LogglyHandler' => 
  array (
    'type' => 'class',
    'classname' => 'LogglyHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\LogglyHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\LogmaticHandler' => 
  array (
    'type' => 'class',
    'classname' => 'LogmaticHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\LogmaticHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\MailHandler' => 
  array (
    'type' => 'class',
    'classname' => 'MailHandler',
    'isabstract' => true,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\MailHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\MandrillHandler' => 
  array (
    'type' => 'class',
    'classname' => 'MandrillHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\MandrillHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\MissingExtensionException' => 
  array (
    'type' => 'class',
    'classname' => 'MissingExtensionException',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\MissingExtensionException',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\MongoDBHandler' => 
  array (
    'type' => 'class',
    'classname' => 'MongoDBHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\MongoDBHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\NativeMailerHandler' => 
  array (
    'type' => 'class',
    'classname' => 'NativeMailerHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\NativeMailerHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\NewRelicHandler' => 
  array (
    'type' => 'class',
    'classname' => 'NewRelicHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\NewRelicHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\NoopHandler' => 
  array (
    'type' => 'class',
    'classname' => 'NoopHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\NoopHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\NullHandler' => 
  array (
    'type' => 'class',
    'classname' => 'NullHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\NullHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\OverflowHandler' => 
  array (
    'type' => 'class',
    'classname' => 'OverflowHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\OverflowHandler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\FormattableHandlerInterface',
    ),
  ),
  'Monolog\\Handler\\PHPConsoleHandler' => 
  array (
    'type' => 'class',
    'classname' => 'PHPConsoleHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\PHPConsoleHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\ProcessHandler' => 
  array (
    'type' => 'class',
    'classname' => 'ProcessHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\ProcessHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\PsrHandler' => 
  array (
    'type' => 'class',
    'classname' => 'PsrHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\PsrHandler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\FormattableHandlerInterface',
    ),
  ),
  'Monolog\\Handler\\PushoverHandler' => 
  array (
    'type' => 'class',
    'classname' => 'PushoverHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\PushoverHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\RedisHandler' => 
  array (
    'type' => 'class',
    'classname' => 'RedisHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\RedisHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\RedisPubSubHandler' => 
  array (
    'type' => 'class',
    'classname' => 'RedisPubSubHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\RedisPubSubHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\RollbarHandler' => 
  array (
    'type' => 'class',
    'classname' => 'RollbarHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\RollbarHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\RotatingFileHandler' => 
  array (
    'type' => 'class',
    'classname' => 'RotatingFileHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\RotatingFileHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SamplingHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SamplingHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\SamplingHandler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\ProcessableHandlerInterface',
      1 => 'Monolog\\Handler\\FormattableHandlerInterface',
    ),
  ),
  'Monolog\\Handler\\SendGridHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SendGridHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\SendGridHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\Slack\\SlackRecord' => 
  array (
    'type' => 'class',
    'classname' => 'SlackRecord',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler\\Slack',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\Slack\\SlackRecord',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SlackHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SlackHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\SlackHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SlackWebhookHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SlackWebhookHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\SlackWebhookHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SocketHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SocketHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\SocketHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SqsHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SqsHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\SqsHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\StreamHandler' => 
  array (
    'type' => 'class',
    'classname' => 'StreamHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\StreamHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SwiftMailerHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SwiftMailerHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\SwiftMailerHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SymfonyMailerHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SymfonyMailerHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\SymfonyMailerHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SyslogHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SyslogHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\SyslogHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SyslogUdp\\UdpSocket' => 
  array (
    'type' => 'class',
    'classname' => 'UdpSocket',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler\\SyslogUdp',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\SyslogUdp\\UdpSocket',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SyslogUdpHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SyslogUdpHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\SyslogUdpHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\TelegramBotHandler' => 
  array (
    'type' => 'class',
    'classname' => 'TelegramBotHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\TelegramBotHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\TestHandler' => 
  array (
    'type' => 'class',
    'classname' => 'TestHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\TestHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\WhatFailureGroupHandler' => 
  array (
    'type' => 'class',
    'classname' => 'WhatFailureGroupHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\WhatFailureGroupHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\ZendMonitorHandler' => 
  array (
    'type' => 'class',
    'classname' => 'ZendMonitorHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'TEC\\Common\\Monolog\\Handler\\ZendMonitorHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Logger' => 
  array (
    'type' => 'class',
    'classname' => 'Logger',
    'isabstract' => false,
    'namespace' => 'Monolog',
    'extends' => 'TEC\\Common\\Monolog\\Logger',
    'implements' => 
    array (
      0 => 'Psr\\Log\\LoggerInterface',
      1 => 'Monolog\\ResettableInterface',
    ),
  ),
  'Monolog\\Processor\\GitProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'GitProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'TEC\\Common\\Monolog\\Processor\\GitProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Processor\\HostnameProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'HostnameProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'TEC\\Common\\Monolog\\Processor\\HostnameProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Processor\\IntrospectionProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'IntrospectionProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'TEC\\Common\\Monolog\\Processor\\IntrospectionProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Processor\\MemoryPeakUsageProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'MemoryPeakUsageProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'TEC\\Common\\Monolog\\Processor\\MemoryPeakUsageProcessor',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Processor\\MemoryProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'MemoryProcessor',
    'isabstract' => true,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'TEC\\Common\\Monolog\\Processor\\MemoryProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Processor\\MemoryUsageProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'MemoryUsageProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'TEC\\Common\\Monolog\\Processor\\MemoryUsageProcessor',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Processor\\MercurialProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'MercurialProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'TEC\\Common\\Monolog\\Processor\\MercurialProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Processor\\ProcessIdProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'ProcessIdProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'TEC\\Common\\Monolog\\Processor\\ProcessIdProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Processor\\PsrLogMessageProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'PsrLogMessageProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'TEC\\Common\\Monolog\\Processor\\PsrLogMessageProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Processor\\TagProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'TagProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'TEC\\Common\\Monolog\\Processor\\TagProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Processor\\UidProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'UidProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'TEC\\Common\\Monolog\\Processor\\UidProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
      1 => 'Monolog\\ResettableInterface',
    ),
  ),
  'Monolog\\Processor\\WebProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'WebProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'TEC\\Common\\Monolog\\Processor\\WebProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Registry' => 
  array (
    'type' => 'class',
    'classname' => 'Registry',
    'isabstract' => false,
    'namespace' => 'Monolog',
    'extends' => 'TEC\\Common\\Monolog\\Registry',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\SignalHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SignalHandler',
    'isabstract' => false,
    'namespace' => 'Monolog',
    'extends' => 'TEC\\Common\\Monolog\\SignalHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Test\\TestCase' => 
  array (
    'type' => 'class',
    'classname' => 'TestCase',
    'isabstract' => false,
    'namespace' => 'Monolog\\Test',
    'extends' => 'TEC\\Common\\Monolog\\Test\\TestCase',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Utils' => 
  array (
    'type' => 'class',
    'classname' => 'Utils',
    'isabstract' => false,
    'namespace' => 'Monolog',
    'extends' => 'TEC\\Common\\Monolog\\Utils',
    'implements' => 
    array (
    ),
  ),
  'Psr\\Log\\AbstractLogger' => 
  array (
    'type' => 'class',
    'classname' => 'AbstractLogger',
    'isabstract' => true,
    'namespace' => 'Psr\\Log',
    'extends' => 'TEC\\Common\\Psr\\Log\\AbstractLogger',
    'implements' => 
    array (
      0 => 'Psr\\Log\\LoggerInterface',
    ),
  ),
  'Psr\\Log\\InvalidArgumentException' => 
  array (
    'type' => 'class',
    'classname' => 'InvalidArgumentException',
    'isabstract' => false,
    'namespace' => 'Psr\\Log',
    'extends' => 'TEC\\Common\\Psr\\Log\\InvalidArgumentException',
    'implements' => 
    array (
    ),
  ),
  'Psr\\Log\\LogLevel' => 
  array (
    'type' => 'class',
    'classname' => 'LogLevel',
    'isabstract' => false,
    'namespace' => 'Psr\\Log',
    'extends' => 'TEC\\Common\\Psr\\Log\\LogLevel',
    'implements' => 
    array (
    ),
  ),
  'Psr\\Log\\NullLogger' => 
  array (
    'type' => 'class',
    'classname' => 'NullLogger',
    'isabstract' => false,
    'namespace' => 'Psr\\Log',
    'extends' => 'TEC\\Common\\Psr\\Log\\NullLogger',
    'implements' => 
    array (
    ),
  ),
  'Psr\\Log\\Test\\DummyTest' => 
  array (
    'type' => 'class',
    'classname' => 'DummyTest',
    'isabstract' => false,
    'namespace' => 'Psr\\Log\\Test',
    'extends' => 'TEC\\Common\\Psr\\Log\\Test\\DummyTest',
    'implements' => 
    array (
    ),
  ),
  'Psr\\Log\\Test\\LoggerInterfaceTest' => 
  array (
    'type' => 'class',
    'classname' => 'LoggerInterfaceTest',
    'isabstract' => true,
    'namespace' => 'Psr\\Log\\Test',
    'extends' => 'TEC\\Common\\Psr\\Log\\Test\\LoggerInterfaceTest',
    'implements' => 
    array (
    ),
  ),
  'Psr\\Log\\Test\\TestLogger' => 
  array (
    'type' => 'class',
    'classname' => 'TestLogger',
    'isabstract' => false,
    'namespace' => 'Psr\\Log\\Test',
    'extends' => 'TEC\\Common\\Psr\\Log\\Test\\TestLogger',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\Actions\\DisplayNoticesInAdmin' => 
  array (
    'type' => 'class',
    'classname' => 'DisplayNoticesInAdmin',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\Actions',
    'extends' => 'TEC\\Common\\StellarWP\\AdminNotices\\Actions\\DisplayNoticesInAdmin',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\Actions\\RenderAdminNotice' => 
  array (
    'type' => 'class',
    'classname' => 'RenderAdminNotice',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\Actions',
    'extends' => 'TEC\\Common\\StellarWP\\AdminNotices\\Actions\\RenderAdminNotice',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\AdminNotice' => 
  array (
    'type' => 'class',
    'classname' => 'AdminNotice',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices',
    'extends' => 'TEC\\Common\\StellarWP\\AdminNotices\\AdminNotice',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\AdminNotices' => 
  array (
    'type' => 'class',
    'classname' => 'AdminNotices',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices',
    'extends' => 'TEC\\Common\\StellarWP\\AdminNotices\\AdminNotices',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\Exceptions\\NotificationCollisionException' => 
  array (
    'type' => 'class',
    'classname' => 'NotificationCollisionException',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\Exceptions',
    'extends' => 'TEC\\Common\\StellarWP\\AdminNotices\\Exceptions\\NotificationCollisionException',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\NotificationsRegistrar' => 
  array (
    'type' => 'class',
    'classname' => 'NotificationsRegistrar',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices',
    'extends' => 'TEC\\Common\\StellarWP\\AdminNotices\\NotificationsRegistrar',
    'implements' => 
    array (
      0 => 'StellarWP\\AdminNotices\\Contracts\\NotificationsRegistrarInterface',
    ),
  ),
  'StellarWP\\AdminNotices\\ValueObjects\\NoticeUrgency' => 
  array (
    'type' => 'class',
    'classname' => 'NoticeUrgency',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\ValueObjects',
    'extends' => 'TEC\\Common\\StellarWP\\AdminNotices\\ValueObjects\\NoticeUrgency',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\ValueObjects\\ScreenCondition' => 
  array (
    'type' => 'class',
    'classname' => 'ScreenCondition',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\ValueObjects',
    'extends' => 'TEC\\Common\\StellarWP\\AdminNotices\\ValueObjects\\ScreenCondition',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\AdminNotices\\ValueObjects\\UserCapability' => 
  array (
    'type' => 'class',
    'classname' => 'UserCapability',
    'isabstract' => false,
    'namespace' => 'StellarWP\\AdminNotices\\ValueObjects',
    'extends' => 'TEC\\Common\\StellarWP\\AdminNotices\\ValueObjects\\UserCapability',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Arrays\\Arr' => 
  array (
    'type' => 'class',
    'classname' => 'Arr',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Arrays',
    'extends' => 'TEC\\Common\\StellarWP\\Arrays\\Arr',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Assets\\Asset' => 
  array (
    'type' => 'class',
    'classname' => 'Asset',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Assets',
    'extends' => 'TEC\\Common\\StellarWP\\Assets\\Asset',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Assets\\Assets' => 
  array (
    'type' => 'class',
    'classname' => 'Assets',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Assets',
    'extends' => 'TEC\\Common\\StellarWP\\Assets\\Assets',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Assets\\Config' => 
  array (
    'type' => 'class',
    'classname' => 'Config',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Assets',
    'extends' => 'TEC\\Common\\StellarWP\\Assets\\Config',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Assets\\Controller' => 
  array (
    'type' => 'class',
    'classname' => 'Controller',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Assets',
    'extends' => 'TEC\\Common\\StellarWP\\Assets\\Controller',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Assets\\Data' => 
  array (
    'type' => 'class',
    'classname' => 'Data',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Assets',
    'extends' => 'TEC\\Common\\StellarWP\\Assets\\Data',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Assets\\Utils' => 
  array (
    'type' => 'class',
    'classname' => 'Utils',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Assets',
    'extends' => 'TEC\\Common\\StellarWP\\Assets\\Utils',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\Config' => 
  array (
    'type' => 'class',
    'classname' => 'Config',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\Config',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\DB' => 
  array (
    'type' => 'class',
    'classname' => 'DB',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\DB',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\Database\\Actions\\EnableBigSqlSelects' => 
  array (
    'type' => 'class',
    'classname' => 'EnableBigSqlSelects',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\Database\\Actions',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\Database\\Actions\\EnableBigSqlSelects',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\Database\\Exceptions\\DatabaseQueryException' => 
  array (
    'type' => 'class',
    'classname' => 'DatabaseQueryException',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\Database\\Exceptions',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\Database\\Exceptions\\DatabaseQueryException',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\Database\\Provider' => 
  array (
    'type' => 'class',
    'classname' => 'Provider',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\Database',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\Database\\Provider',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\From' => 
  array (
    'type' => 'class',
    'classname' => 'From',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Clauses\\From',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\Having' => 
  array (
    'type' => 'class',
    'classname' => 'Having',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Clauses\\Having',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\Join' => 
  array (
    'type' => 'class',
    'classname' => 'Join',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Clauses\\Join',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\JoinCondition' => 
  array (
    'type' => 'class',
    'classname' => 'JoinCondition',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Clauses\\JoinCondition',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\MetaTable' => 
  array (
    'type' => 'class',
    'classname' => 'MetaTable',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Clauses\\MetaTable',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\OrderBy' => 
  array (
    'type' => 'class',
    'classname' => 'OrderBy',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Clauses\\OrderBy',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\RawSQL' => 
  array (
    'type' => 'class',
    'classname' => 'RawSQL',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Clauses\\RawSQL',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\Select' => 
  array (
    'type' => 'class',
    'classname' => 'Select',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Clauses\\Select',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\Union' => 
  array (
    'type' => 'class',
    'classname' => 'Union',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Clauses\\Union',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Clauses\\Where' => 
  array (
    'type' => 'class',
    'classname' => 'Where',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Clauses',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Clauses\\Where',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\JoinQueryBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'JoinQueryBuilder',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\JoinQueryBuilder',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\QueryBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'QueryBuilder',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\QueryBuilder',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Types\\JoinType' => 
  array (
    'type' => 'class',
    'classname' => 'JoinType',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Types',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Types\\JoinType',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Types\\Math' => 
  array (
    'type' => 'class',
    'classname' => 'Math',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Types',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Types\\Math',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Types\\Operator' => 
  array (
    'type' => 'class',
    'classname' => 'Operator',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Types',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Types\\Operator',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Types\\Type' => 
  array (
    'type' => 'class',
    'classname' => 'Type',
    'isabstract' => true,
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Types',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Types\\Type',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\WhereQueryBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'WhereQueryBuilder',
    'isabstract' => false,
    'namespace' => 'StellarWP\\DB\\QueryBuilder',
    'extends' => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\WhereQueryBuilder',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Installer\\Assets' => 
  array (
    'type' => 'class',
    'classname' => 'Assets',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Installer',
    'extends' => 'TEC\\Common\\StellarWP\\Installer\\Assets',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Installer\\Button' => 
  array (
    'type' => 'class',
    'classname' => 'Button',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Installer',
    'extends' => 'TEC\\Common\\StellarWP\\Installer\\Button',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Installer\\Config' => 
  array (
    'type' => 'class',
    'classname' => 'Config',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Installer',
    'extends' => 'TEC\\Common\\StellarWP\\Installer\\Config',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Installer\\Handler\\Plugin' => 
  array (
    'type' => 'class',
    'classname' => 'Plugin',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Installer\\Handler',
    'extends' => 'TEC\\Common\\StellarWP\\Installer\\Handler\\Plugin',
    'implements' => 
    array (
      0 => 'StellarWP\\Installer\\Contracts\\Handler',
    ),
  ),
  'StellarWP\\Installer\\Installer' => 
  array (
    'type' => 'class',
    'classname' => 'Installer',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Installer',
    'extends' => 'TEC\\Common\\StellarWP\\Installer\\Installer',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Installer\\Utils\\Array_Utils' => 
  array (
    'type' => 'class',
    'classname' => 'Array_Utils',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Installer\\Utils',
    'extends' => 'TEC\\Common\\StellarWP\\Installer\\Utils\\Array_Utils',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Models\\Config' => 
  array (
    'type' => 'class',
    'classname' => 'Config',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Models',
    'extends' => 'TEC\\Common\\StellarWP\\Models\\Config',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Models\\DataTransferObject' => 
  array (
    'type' => 'class',
    'classname' => 'DataTransferObject',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Models',
    'extends' => 'TEC\\Common\\StellarWP\\Models\\DataTransferObject',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Models\\Exceptions\\ReadOnlyPropertyException' => 
  array (
    'type' => 'class',
    'classname' => 'ReadOnlyPropertyException',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Models\\Exceptions',
    'extends' => 'TEC\\Common\\StellarWP\\Models\\Exceptions\\ReadOnlyPropertyException',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Models\\Model' => 
  array (
    'type' => 'class',
    'classname' => 'Model',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Models',
    'extends' => 'TEC\\Common\\StellarWP\\Models\\Model',
    'implements' => 
    array (
      0 => 'StellarWP\\Models\\Contracts\\Model',
      1 => 'StellarWP\\Models\\Contracts\\Arrayable',
      2 => 'JsonSerializable',
    ),
  ),
  'StellarWP\\Models\\ModelProperty' => 
  array (
    'type' => 'class',
    'classname' => 'ModelProperty',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Models',
    'extends' => 'TEC\\Common\\StellarWP\\Models\\ModelProperty',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Models\\ModelPropertyCollection' => 
  array (
    'type' => 'class',
    'classname' => 'ModelPropertyCollection',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Models',
    'extends' => 'TEC\\Common\\StellarWP\\Models\\ModelPropertyCollection',
    'implements' => 
    array (
      0 => 'Countable',
      1 => 'IteratorAggregate',
    ),
  ),
  'StellarWP\\Models\\ModelPropertyDefinition' => 
  array (
    'type' => 'class',
    'classname' => 'ModelPropertyDefinition',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Models',
    'extends' => 'TEC\\Common\\StellarWP\\Models\\ModelPropertyDefinition',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Models\\ModelQueryBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'ModelQueryBuilder',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Models',
    'extends' => 'TEC\\Common\\StellarWP\\Models\\ModelQueryBuilder',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Models\\ModelRelationship' => 
  array (
    'type' => 'class',
    'classname' => 'ModelRelationship',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Models',
    'extends' => 'TEC\\Common\\StellarWP\\Models\\ModelRelationship',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Models\\ModelRelationshipCollection' => 
  array (
    'type' => 'class',
    'classname' => 'ModelRelationshipCollection',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Models',
    'extends' => 'TEC\\Common\\StellarWP\\Models\\ModelRelationshipCollection',
    'implements' => 
    array (
      0 => 'Countable',
      1 => 'IteratorAggregate',
    ),
  ),
  'StellarWP\\Models\\ModelRelationshipDefinition' => 
  array (
    'type' => 'class',
    'classname' => 'ModelRelationshipDefinition',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Models',
    'extends' => 'TEC\\Common\\StellarWP\\Models\\ModelRelationshipDefinition',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Models\\Repositories\\Repository' => 
  array (
    'type' => 'class',
    'classname' => 'Repository',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Models\\Repositories',
    'extends' => 'TEC\\Common\\StellarWP\\Models\\Repositories\\Repository',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Models\\ValueObjects\\Relationship' => 
  array (
    'type' => 'class',
    'classname' => 'Relationship',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Models\\ValueObjects',
    'extends' => 'TEC\\Common\\StellarWP\\Models\\ValueObjects\\Relationship',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\SchemaModels\\Exceptions\\BadMethodCallSchemaModelException' => 
  array (
    'type' => 'class',
    'classname' => 'BadMethodCallSchemaModelException',
    'isabstract' => false,
    'namespace' => 'StellarWP\\SchemaModels\\Exceptions',
    'extends' => 'TEC\\Common\\StellarWP\\SchemaModels\\Exceptions\\BadMethodCallSchemaModelException',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\SchemaModels\\Exceptions\\SchemaModelException' => 
  array (
    'type' => 'class',
    'classname' => 'SchemaModelException',
    'isabstract' => false,
    'namespace' => 'StellarWP\\SchemaModels\\Exceptions',
    'extends' => 'TEC\\Common\\StellarWP\\SchemaModels\\Exceptions\\SchemaModelException',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\SchemaModels\\Relationships\\ManyToManyWithPosts' => 
  array (
    'type' => 'class',
    'classname' => 'ManyToManyWithPosts',
    'isabstract' => false,
    'namespace' => 'StellarWP\\SchemaModels\\Relationships',
    'extends' => 'TEC\\Common\\StellarWP\\SchemaModels\\Relationships\\ManyToManyWithPosts',
    'implements' => 
    array (
      0 => 'StellarWP\\SchemaModels\\Contracts\\Relationships\\ManyToManyWithPosts',
    ),
  ),
  'StellarWP\\SchemaModels\\SchemaModel' => 
  array (
    'type' => 'class',
    'classname' => 'SchemaModel',
    'isabstract' => true,
    'namespace' => 'StellarWP\\SchemaModels',
    'extends' => 'TEC\\Common\\StellarWP\\SchemaModels\\SchemaModel',
    'implements' => 
    array (
      0 => 'StellarWP\\SchemaModels\\Contracts\\SchemaModel',
    ),
  ),
  'StellarWP\\Schema\\Activation' => 
  array (
    'type' => 'class',
    'classname' => 'Activation',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Activation',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Builder' => 
  array (
    'type' => 'class',
    'classname' => 'Builder',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Builder',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Collections\\Collection' => 
  array (
    'type' => 'class',
    'classname' => 'Collection',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Schema\\Collections',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Collections\\Collection',
    'implements' => 
    array (
      0 => 'ArrayAccess',
      1 => 'Iterator',
      2 => 'Countable',
      3 => 'JsonSerializable',
    ),
  ),
  'StellarWP\\Schema\\Collections\\Column_Collection' => 
  array (
    'type' => 'class',
    'classname' => 'Column_Collection',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Collections',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Collections\\Column_Collection',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Collections\\Index_Collection' => 
  array (
    'type' => 'class',
    'classname' => 'Index_Collection',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Collections',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Collections\\Index_Collection',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Columns\\Binary_Column' => 
  array (
    'type' => 'class',
    'classname' => 'Binary_Column',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Columns',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Binary_Column',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Columns\\Blob_Column' => 
  array (
    'type' => 'class',
    'classname' => 'Blob_Column',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Columns',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Blob_Column',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Columns\\Boolean_Column' => 
  array (
    'type' => 'class',
    'classname' => 'Boolean_Column',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Columns',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Boolean_Column',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Columns\\Column_Types' => 
  array (
    'type' => 'class',
    'classname' => 'Column_Types',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Columns',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Column_Types',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Columns\\Contracts\\Column' => 
  array (
    'type' => 'class',
    'classname' => 'Column',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Schema\\Columns\\Contracts',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Contracts\\Column',
    'implements' => 
    array (
      0 => 'StellarWP\\Schema\\Columns\\Contracts\\Column_Interface',
      1 => 'StellarWP\\Schema\\Columns\\Contracts\\Indexable',
    ),
  ),
  'StellarWP\\Schema\\Columns\\Created_At' => 
  array (
    'type' => 'class',
    'classname' => 'Created_At',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Columns',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Created_At',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Columns\\Datetime_Column' => 
  array (
    'type' => 'class',
    'classname' => 'Datetime_Column',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Columns',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Datetime_Column',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Columns\\Float_Column' => 
  array (
    'type' => 'class',
    'classname' => 'Float_Column',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Columns',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Float_Column',
    'implements' => 
    array (
      0 => 'StellarWP\\Schema\\Columns\\Contracts\\Lengthable',
      1 => 'StellarWP\\Schema\\Columns\\Contracts\\Signable',
      2 => 'StellarWP\\Schema\\Columns\\Contracts\\Precisionable',
      3 => 'StellarWP\\Schema\\Columns\\Contracts\\Uniquable',
    ),
  ),
  'StellarWP\\Schema\\Columns\\ID' => 
  array (
    'type' => 'class',
    'classname' => 'ID',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Columns',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Columns\\ID',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Columns\\Integer_Column' => 
  array (
    'type' => 'class',
    'classname' => 'Integer_Column',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Columns',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Integer_Column',
    'implements' => 
    array (
      0 => 'StellarWP\\Schema\\Columns\\Contracts\\Lengthable',
      1 => 'StellarWP\\Schema\\Columns\\Contracts\\Signable',
      2 => 'StellarWP\\Schema\\Columns\\Contracts\\Auto_Incrementable',
      3 => 'StellarWP\\Schema\\Columns\\Contracts\\Uniquable',
    ),
  ),
  'StellarWP\\Schema\\Columns\\Last_Changed' => 
  array (
    'type' => 'class',
    'classname' => 'Last_Changed',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Columns',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Last_Changed',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Columns\\PHP_Types' => 
  array (
    'type' => 'class',
    'classname' => 'PHP_Types',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Columns',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Columns\\PHP_Types',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Columns\\Referenced_ID' => 
  array (
    'type' => 'class',
    'classname' => 'Referenced_ID',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Columns',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Referenced_ID',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Columns\\String_Column' => 
  array (
    'type' => 'class',
    'classname' => 'String_Column',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Columns',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Columns\\String_Column',
    'implements' => 
    array (
      0 => 'StellarWP\\Schema\\Columns\\Contracts\\Lengthable',
      1 => 'StellarWP\\Schema\\Columns\\Contracts\\Uniquable',
      2 => 'StellarWP\\Schema\\Columns\\Contracts\\Primarable',
    ),
  ),
  'StellarWP\\Schema\\Columns\\Text_Column' => 
  array (
    'type' => 'class',
    'classname' => 'Text_Column',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Columns',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Text_Column',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Columns\\Updated_At' => 
  array (
    'type' => 'class',
    'classname' => 'Updated_At',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Columns',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Updated_At',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Config' => 
  array (
    'type' => 'class',
    'classname' => 'Config',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Config',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Full_Activation_Provider' => 
  array (
    'type' => 'class',
    'classname' => 'Full_Activation_Provider',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Full_Activation_Provider',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Indexes\\Classic_Index' => 
  array (
    'type' => 'class',
    'classname' => 'Classic_Index',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Indexes',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Indexes\\Classic_Index',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Indexes\\Contracts\\Abstract_Index' => 
  array (
    'type' => 'class',
    'classname' => 'Abstract_Index',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Schema\\Indexes\\Contracts',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Indexes\\Contracts\\Abstract_Index',
    'implements' => 
    array (
      0 => 'StellarWP\\Schema\\Indexes\\Contracts\\Index',
    ),
  ),
  'StellarWP\\Schema\\Indexes\\Fulltext_Index' => 
  array (
    'type' => 'class',
    'classname' => 'Fulltext_Index',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Indexes',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Indexes\\Fulltext_Index',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Indexes\\Primary_Key' => 
  array (
    'type' => 'class',
    'classname' => 'Primary_Key',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Indexes',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Indexes\\Primary_Key',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Indexes\\Unique_Key' => 
  array (
    'type' => 'class',
    'classname' => 'Unique_Key',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Indexes',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Indexes\\Unique_Key',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Register' => 
  array (
    'type' => 'class',
    'classname' => 'Register',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Register',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Schema' => 
  array (
    'type' => 'class',
    'classname' => 'Schema',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Schema',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Schema\\Tables\\Collection' => 
  array (
    'type' => 'class',
    'classname' => 'Collection',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Tables',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Tables\\Collection',
    'implements' => 
    array (
      0 => 'ArrayAccess',
      1 => 'Countable',
      2 => 'Iterator',
    ),
  ),
  'StellarWP\\Schema\\Tables\\Contracts\\Table' => 
  array (
    'type' => 'class',
    'classname' => 'Table',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Schema\\Tables\\Contracts',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Tables\\Contracts\\Table',
    'implements' => 
    array (
      0 => 'StellarWP\\Schema\\Tables\\Contracts\\Table_Interface',
    ),
  ),
  'StellarWP\\Schema\\Tables\\Filters\\Group_FilterIterator' => 
  array (
    'type' => 'class',
    'classname' => 'Group_FilterIterator',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Tables\\Filters',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Tables\\Filters\\Group_FilterIterator',
    'implements' => 
    array (
      0 => 'Countable',
    ),
  ),
  'StellarWP\\Schema\\Tables\\Filters\\Needs_Update_FilterIterator' => 
  array (
    'type' => 'class',
    'classname' => 'Needs_Update_FilterIterator',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Tables\\Filters',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Tables\\Filters\\Needs_Update_FilterIterator',
    'implements' => 
    array (
      0 => 'Countable',
    ),
  ),
  'StellarWP\\Schema\\Tables\\Table_Schema' => 
  array (
    'type' => 'class',
    'classname' => 'Table_Schema',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Schema\\Tables',
    'extends' => 'TEC\\Common\\StellarWP\\Schema\\Tables\\Table_Schema',
    'implements' => 
    array (
      0 => 'StellarWP\\Schema\\Tables\\Contracts\\Table_Schema_Interface',
    ),
  ),
  'StellarWP\\Shepherd\\Abstracts\\Model_Abstract' => 
  array (
    'type' => 'class',
    'classname' => 'Model_Abstract',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Shepherd\\Abstracts',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Abstracts\\Model_Abstract',
    'implements' => 
    array (
      0 => 'StellarWP\\Shepherd\\Contracts\\Model',
    ),
  ),
  'StellarWP\\Shepherd\\Abstracts\\Provider_Abstract' => 
  array (
    'type' => 'class',
    'classname' => 'Provider_Abstract',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Shepherd\\Abstracts',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Abstracts\\Provider_Abstract',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Shepherd\\Abstracts\\Table_Abstract' => 
  array (
    'type' => 'class',
    'classname' => 'Table_Abstract',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Shepherd\\Abstracts',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Abstracts\\Table_Abstract',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Shepherd\\Abstracts\\Task_Abstract' => 
  array (
    'type' => 'class',
    'classname' => 'Task_Abstract',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Shepherd\\Abstracts',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Abstracts\\Task_Abstract',
    'implements' => 
    array (
      0 => 'StellarWP\\Shepherd\\Contracts\\Task',
    ),
  ),
  'StellarWP\\Shepherd\\Abstracts\\Task_Model_Abstract' => 
  array (
    'type' => 'class',
    'classname' => 'Task_Model_Abstract',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Shepherd\\Abstracts',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Abstracts\\Task_Model_Abstract',
    'implements' => 
    array (
      0 => 'StellarWP\\Shepherd\\Contracts\\Task_Model',
    ),
  ),
  'StellarWP\\Shepherd\\Action_Scheduler_Methods' => 
  array (
    'type' => 'class',
    'classname' => 'Action_Scheduler_Methods',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Action_Scheduler_Methods',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Shepherd\\Config' => 
  array (
    'type' => 'class',
    'classname' => 'Config',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Config',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Shepherd\\Exceptions\\ShepherdTaskAlreadyExistsException' => 
  array (
    'type' => 'class',
    'classname' => 'ShepherdTaskAlreadyExistsException',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd\\Exceptions',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Exceptions\\ShepherdTaskAlreadyExistsException',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Shepherd\\Exceptions\\ShepherdTaskException' => 
  array (
    'type' => 'class',
    'classname' => 'ShepherdTaskException',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd\\Exceptions',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Exceptions\\ShepherdTaskException',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Shepherd\\Exceptions\\ShepherdTaskFailWithoutRetryException' => 
  array (
    'type' => 'class',
    'classname' => 'ShepherdTaskFailWithoutRetryException',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd\\Exceptions',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Exceptions\\ShepherdTaskFailWithoutRetryException',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Shepherd\\Log' => 
  array (
    'type' => 'class',
    'classname' => 'Log',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Log',
    'implements' => 
    array (
      0 => 'StellarWP\\Shepherd\\Contracts\\Log_Model',
    ),
  ),
  'StellarWP\\Shepherd\\Loggers\\ActionScheduler_DB_Logger' => 
  array (
    'type' => 'class',
    'classname' => 'ActionScheduler_DB_Logger',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd\\Loggers',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Loggers\\ActionScheduler_DB_Logger',
    'implements' => 
    array (
      0 => 'StellarWP\\Shepherd\\Contracts\\Logger',
    ),
  ),
  'StellarWP\\Shepherd\\Loggers\\DB_Logger' => 
  array (
    'type' => 'class',
    'classname' => 'DB_Logger',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd\\Loggers',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Loggers\\DB_Logger',
    'implements' => 
    array (
      0 => 'StellarWP\\Shepherd\\Contracts\\Logger',
    ),
  ),
  'StellarWP\\Shepherd\\Loggers\\Null_Logger' => 
  array (
    'type' => 'class',
    'classname' => 'Null_Logger',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd\\Loggers',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Loggers\\Null_Logger',
    'implements' => 
    array (
      0 => 'StellarWP\\Shepherd\\Contracts\\Logger',
    ),
  ),
  'StellarWP\\Shepherd\\Provider' => 
  array (
    'type' => 'class',
    'classname' => 'Provider',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Provider',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Shepherd\\Regulator' => 
  array (
    'type' => 'class',
    'classname' => 'Regulator',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Regulator',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Shepherd\\Tables\\AS_Logs' => 
  array (
    'type' => 'class',
    'classname' => 'AS_Logs',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd\\Tables',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Tables\\AS_Logs',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Shepherd\\Tables\\Provider' => 
  array (
    'type' => 'class',
    'classname' => 'Provider',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd\\Tables',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Tables\\Provider',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Shepherd\\Tables\\Task_Logs' => 
  array (
    'type' => 'class',
    'classname' => 'Task_Logs',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd\\Tables',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Tables\\Task_Logs',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Shepherd\\Tables\\Tasks' => 
  array (
    'type' => 'class',
    'classname' => 'Tasks',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd\\Tables',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Tables\\Tasks',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Shepherd\\Tables\\Utility\\Safe_Dynamic_Prefix' => 
  array (
    'type' => 'class',
    'classname' => 'Safe_Dynamic_Prefix',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd\\Tables\\Utility',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Tables\\Utility\\Safe_Dynamic_Prefix',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Shepherd\\Tasks\\Email' => 
  array (
    'type' => 'class',
    'classname' => 'Email',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd\\Tasks',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Tasks\\Email',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Shepherd\\Tasks\\HTTP_Request' => 
  array (
    'type' => 'class',
    'classname' => 'HTTP_Request',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd\\Tasks',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Tasks\\HTTP_Request',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Shepherd\\Tasks\\Herding' => 
  array (
    'type' => 'class',
    'classname' => 'Herding',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Shepherd\\Tasks',
    'extends' => 'TEC\\Common\\StellarWP\\Shepherd\\Tasks\\Herding',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Telemetry\\Admin\\Admin_Subscriber' => 
  array (
    'type' => 'class',
    'classname' => 'Admin_Subscriber',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry\\Admin',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Admin\\Admin_Subscriber',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Telemetry\\Admin\\Resources' => 
  array (
    'type' => 'class',
    'classname' => 'Resources',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry\\Admin',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Admin\\Resources',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Telemetry\\Config' => 
  array (
    'type' => 'class',
    'classname' => 'Config',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Config',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Telemetry\\Contracts\\Abstract_Subscriber' => 
  array (
    'type' => 'class',
    'classname' => 'Abstract_Subscriber',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Telemetry\\Contracts',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Contracts\\Abstract_Subscriber',
    'implements' => 
    array (
      0 => 'StellarWP\\Telemetry\\Contracts\\Subscriber_Interface',
    ),
  ),
  'StellarWP\\Telemetry\\Core' => 
  array (
    'type' => 'class',
    'classname' => 'Core',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Core',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Telemetry\\Data_Providers\\Debug_Data' => 
  array (
    'type' => 'class',
    'classname' => 'Debug_Data',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry\\Data_Providers',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Data_Providers\\Debug_Data',
    'implements' => 
    array (
      0 => 'StellarWP\\Telemetry\\Contracts\\Data_Provider',
    ),
  ),
  'StellarWP\\Telemetry\\Data_Providers\\Null_Data_Provider' => 
  array (
    'type' => 'class',
    'classname' => 'Null_Data_Provider',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry\\Data_Providers',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Data_Providers\\Null_Data_Provider',
    'implements' => 
    array (
      0 => 'StellarWP\\Telemetry\\Contracts\\Data_Provider',
    ),
  ),
  'StellarWP\\Telemetry\\Events\\Event' => 
  array (
    'type' => 'class',
    'classname' => 'Event',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry\\Events',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Events\\Event',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Telemetry\\Events\\Event_Subscriber' => 
  array (
    'type' => 'class',
    'classname' => 'Event_Subscriber',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry\\Events',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Events\\Event_Subscriber',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Telemetry\\Exit_Interview\\Exit_Interview_Subscriber' => 
  array (
    'type' => 'class',
    'classname' => 'Exit_Interview_Subscriber',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry\\Exit_Interview',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Exit_Interview\\Exit_Interview_Subscriber',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Telemetry\\Exit_Interview\\Template' => 
  array (
    'type' => 'class',
    'classname' => 'Template',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry\\Exit_Interview',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Exit_Interview\\Template',
    'implements' => 
    array (
      0 => 'StellarWP\\Telemetry\\Contracts\\Template_Interface',
    ),
  ),
  'StellarWP\\Telemetry\\Last_Send\\Last_Send' => 
  array (
    'type' => 'class',
    'classname' => 'Last_Send',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry\\Last_Send',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Last_Send\\Last_Send',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Telemetry\\Last_Send\\Last_Send_Subscriber' => 
  array (
    'type' => 'class',
    'classname' => 'Last_Send_Subscriber',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry\\Last_Send',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Last_Send\\Last_Send_Subscriber',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Telemetry\\Opt_In\\Opt_In_Subscriber' => 
  array (
    'type' => 'class',
    'classname' => 'Opt_In_Subscriber',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry\\Opt_In',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Opt_In\\Opt_In_Subscriber',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Telemetry\\Opt_In\\Opt_In_Template' => 
  array (
    'type' => 'class',
    'classname' => 'Opt_In_Template',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry\\Opt_In',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Opt_In\\Opt_In_Template',
    'implements' => 
    array (
      0 => 'StellarWP\\Telemetry\\Contracts\\Template_Interface',
    ),
  ),
  'StellarWP\\Telemetry\\Opt_In\\Status' => 
  array (
    'type' => 'class',
    'classname' => 'Status',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry\\Opt_In',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Opt_In\\Status',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Telemetry\\Telemetry\\Telemetry' => 
  array (
    'type' => 'class',
    'classname' => 'Telemetry',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry\\Telemetry',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Telemetry\\Telemetry',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Telemetry\\Telemetry\\Telemetry_Subscriber' => 
  array (
    'type' => 'class',
    'classname' => 'Telemetry_Subscriber',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry\\Telemetry',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Telemetry\\Telemetry_Subscriber',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Telemetry\\Uninstall' => 
  array (
    'type' => 'class',
    'classname' => 'Uninstall',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Telemetry',
    'extends' => 'TEC\\Common\\StellarWP\\Telemetry\\Uninstall',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\API\\Client' => 
  array (
    'type' => 'class',
    'classname' => 'Client',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\API',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\API\\Client',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\API\\V3\\Auth\\Auth_Url' => 
  array (
    'type' => 'class',
    'classname' => 'Auth_Url',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\API\\V3\\Auth',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\API\\V3\\Auth\\Auth_Url',
    'implements' => 
    array (
      0 => 'StellarWP\\Uplink\\API\\V3\\Auth\\Contracts\\Auth_Url',
    ),
  ),
  'StellarWP\\Uplink\\API\\V3\\Auth\\Auth_Url_Cache_Decorator' => 
  array (
    'type' => 'class',
    'classname' => 'Auth_Url_Cache_Decorator',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\API\\V3\\Auth',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\API\\V3\\Auth\\Auth_Url_Cache_Decorator',
    'implements' => 
    array (
      0 => 'StellarWP\\Uplink\\API\\V3\\Auth\\Contracts\\Auth_Url',
    ),
  ),
  'StellarWP\\Uplink\\API\\V3\\Auth\\Token_Authorizer' => 
  array (
    'type' => 'class',
    'classname' => 'Token_Authorizer',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\API\\V3\\Auth',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\API\\V3\\Auth\\Token_Authorizer',
    'implements' => 
    array (
      0 => 'StellarWP\\Uplink\\API\\V3\\Auth\\Contracts\\Token_Authorizer',
    ),
  ),
  'StellarWP\\Uplink\\API\\V3\\Auth\\Token_Authorizer_Cache_Decorator' => 
  array (
    'type' => 'class',
    'classname' => 'Token_Authorizer_Cache_Decorator',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\API\\V3\\Auth',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\API\\V3\\Auth\\Token_Authorizer_Cache_Decorator',
    'implements' => 
    array (
      0 => 'StellarWP\\Uplink\\API\\V3\\Auth\\Contracts\\Token_Authorizer',
    ),
  ),
  'StellarWP\\Uplink\\API\\V3\\Client' => 
  array (
    'type' => 'class',
    'classname' => 'Client',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\API\\V3',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\API\\V3\\Client',
    'implements' => 
    array (
      0 => 'StellarWP\\Uplink\\API\\V3\\Contracts\\Client_V3',
    ),
  ),
  'StellarWP\\Uplink\\API\\V3\\Provider' => 
  array (
    'type' => 'class',
    'classname' => 'Provider',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\API\\V3',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\API\\V3\\Provider',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\API\\Validation_Response' => 
  array (
    'type' => 'class',
    'classname' => 'Validation_Response',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\API',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\API\\Validation_Response',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Admin\\Ajax' => 
  array (
    'type' => 'class',
    'classname' => 'Ajax',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Admin',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Admin\\Ajax',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Admin\\Asset_Manager' => 
  array (
    'type' => 'class',
    'classname' => 'Asset_Manager',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Admin',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Admin\\Asset_Manager',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Admin\\Field' => 
  array (
    'type' => 'class',
    'classname' => 'Field',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Uplink\\Admin',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Admin\\Field',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Admin\\Fields\\Field' => 
  array (
    'type' => 'class',
    'classname' => 'Field',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Admin\\Fields',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Admin\\Fields\\Field',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Admin\\Fields\\Form' => 
  array (
    'type' => 'class',
    'classname' => 'Form',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Admin\\Fields',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Admin\\Fields\\Form',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Admin\\Group' => 
  array (
    'type' => 'class',
    'classname' => 'Group',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Admin',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Admin\\Group',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Admin\\License_Field' => 
  array (
    'type' => 'class',
    'classname' => 'License_Field',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Admin',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Admin\\License_Field',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Admin\\Notice' => 
  array (
    'type' => 'class',
    'classname' => 'Notice',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Admin',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Admin\\Notice',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Admin\\Package_Handler' => 
  array (
    'type' => 'class',
    'classname' => 'Package_Handler',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Admin',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Admin\\Package_Handler',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Admin\\Plugins_Page' => 
  array (
    'type' => 'class',
    'classname' => 'Plugins_Page',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Admin',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Admin\\Plugins_Page',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Admin\\Provider' => 
  array (
    'type' => 'class',
    'classname' => 'Provider',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Admin',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Admin\\Provider',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Admin\\Update_Prevention' => 
  array (
    'type' => 'class',
    'classname' => 'Update_Prevention',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Admin',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Admin\\Update_Prevention',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Auth\\Action_Manager' => 
  array (
    'type' => 'class',
    'classname' => 'Action_Manager',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Auth',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Auth\\Action_Manager',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Auth\\Admin\\Connect_Controller' => 
  array (
    'type' => 'class',
    'classname' => 'Connect_Controller',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Auth\\Admin',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Auth\\Admin\\Connect_Controller',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Auth\\Admin\\Disconnect_Controller' => 
  array (
    'type' => 'class',
    'classname' => 'Disconnect_Controller',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Auth\\Admin',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Auth\\Admin\\Disconnect_Controller',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Auth\\Auth_Url_Builder' => 
  array (
    'type' => 'class',
    'classname' => 'Auth_Url_Builder',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Auth',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Auth\\Auth_Url_Builder',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Auth\\Authorizer' => 
  array (
    'type' => 'class',
    'classname' => 'Authorizer',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Auth',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Auth\\Authorizer',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Auth\\Nonce' => 
  array (
    'type' => 'class',
    'classname' => 'Nonce',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Auth',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Auth\\Nonce',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Auth\\Provider' => 
  array (
    'type' => 'class',
    'classname' => 'Provider',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Auth',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Auth\\Provider',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Auth\\Token\\Connector' => 
  array (
    'type' => 'class',
    'classname' => 'Connector',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Auth\\Token',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Auth\\Token\\Connector',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Auth\\Token\\Disconnector' => 
  array (
    'type' => 'class',
    'classname' => 'Disconnector',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Auth\\Token',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Auth\\Token\\Disconnector',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Auth\\Token\\Exceptions\\InvalidTokenException' => 
  array (
    'type' => 'class',
    'classname' => 'InvalidTokenException',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Auth\\Token\\Exceptions',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Auth\\Token\\Exceptions\\InvalidTokenException',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Auth\\Token\\Token_Manager' => 
  array (
    'type' => 'class',
    'classname' => 'Token_Manager',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Auth\\Token',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Auth\\Token\\Token_Manager',
    'implements' => 
    array (
      0 => 'StellarWP\\Uplink\\Auth\\Token\\Contracts\\Token_Manager',
    ),
  ),
  'StellarWP\\Uplink\\Components\\Admin\\Authorize_Button_Controller' => 
  array (
    'type' => 'class',
    'classname' => 'Authorize_Button_Controller',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Components\\Admin',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Components\\Admin\\Authorize_Button_Controller',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Components\\Controller' => 
  array (
    'type' => 'class',
    'classname' => 'Controller',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Uplink\\Components',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Components\\Controller',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Config' => 
  array (
    'type' => 'class',
    'classname' => 'Config',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Config',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Contracts\\Abstract_Provider' => 
  array (
    'type' => 'class',
    'classname' => 'Abstract_Provider',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Uplink\\Contracts',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Contracts\\Abstract_Provider',
    'implements' => 
    array (
      0 => 'StellarWP\\Uplink\\Contracts\\Provider_Interface',
    ),
  ),
  'StellarWP\\Uplink\\Exceptions\\ResourceAlreadyRegisteredException' => 
  array (
    'type' => 'class',
    'classname' => 'ResourceAlreadyRegisteredException',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Exceptions',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Exceptions\\ResourceAlreadyRegisteredException',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Messages\\API' => 
  array (
    'type' => 'class',
    'classname' => 'API',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Messages',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Messages\\API',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Messages\\Expired_Key' => 
  array (
    'type' => 'class',
    'classname' => 'Expired_Key',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Messages',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Messages\\Expired_Key',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Messages\\Message_Abstract' => 
  array (
    'type' => 'class',
    'classname' => 'Message_Abstract',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Uplink\\Messages',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Messages\\Message_Abstract',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Messages\\Network_Licensed' => 
  array (
    'type' => 'class',
    'classname' => 'Network_Licensed',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Messages',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Messages\\Network_Licensed',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Messages\\Network_Unlicensed' => 
  array (
    'type' => 'class',
    'classname' => 'Network_Unlicensed',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Messages',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Messages\\Network_Unlicensed',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Messages\\Network_Expired' => 
  array (
    'type' => 'class',
    'classname' => 'Network_Expired',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Messages',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Messages\\Network_Expired',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Messages\\Unlicensed' => 
  array (
    'type' => 'class',
    'classname' => 'Unlicensed',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Messages',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Messages\\Unlicensed',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Messages\\Unreachable' => 
  array (
    'type' => 'class',
    'classname' => 'Unreachable',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Messages',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Messages\\Unreachable',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Messages\\Update_Available' => 
  array (
    'type' => 'class',
    'classname' => 'Update_Available',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Messages',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Messages\\Update_Available',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Messages\\Update_Now' => 
  array (
    'type' => 'class',
    'classname' => 'Update_Now',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Messages',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Messages\\Update_Now',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Messages\\Valid_Key' => 
  array (
    'type' => 'class',
    'classname' => 'Valid_Key',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Messages',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Messages\\Valid_Key',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Notice\\Notice' => 
  array (
    'type' => 'class',
    'classname' => 'Notice',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Notice',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Notice\\Notice',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Notice\\Notice_Controller' => 
  array (
    'type' => 'class',
    'classname' => 'Notice_Controller',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Notice',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Notice\\Notice_Controller',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Notice\\Notice_Handler' => 
  array (
    'type' => 'class',
    'classname' => 'Notice_Handler',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Notice',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Notice\\Notice_Handler',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Notice\\Provider' => 
  array (
    'type' => 'class',
    'classname' => 'Provider',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Notice',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Notice\\Provider',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Pipeline\\Pipeline' => 
  array (
    'type' => 'class',
    'classname' => 'Pipeline',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Pipeline',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Pipeline\\Pipeline',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Register' => 
  array (
    'type' => 'class',
    'classname' => 'Register',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Register',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Resources\\Collection' => 
  array (
    'type' => 'class',
    'classname' => 'Collection',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Resources',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Resources\\Collection',
    'implements' => 
    array (
      0 => 'ArrayAccess',
      1 => 'Iterator',
      2 => 'Countable',
    ),
  ),
  'StellarWP\\Uplink\\Resources\\Filters\\Path_FilterIterator' => 
  array (
    'type' => 'class',
    'classname' => 'Path_FilterIterator',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Resources\\Filters',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Resources\\Filters\\Path_FilterIterator',
    'implements' => 
    array (
      0 => 'Countable',
    ),
  ),
  'StellarWP\\Uplink\\Resources\\Filters\\Plugin_FilterIterator' => 
  array (
    'type' => 'class',
    'classname' => 'Plugin_FilterIterator',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Resources\\Filters',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Resources\\Filters\\Plugin_FilterIterator',
    'implements' => 
    array (
      0 => 'Countable',
    ),
  ),
  'StellarWP\\Uplink\\Resources\\Filters\\Service_FilterIterator' => 
  array (
    'type' => 'class',
    'classname' => 'Service_FilterIterator',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Resources\\Filters',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Resources\\Filters\\Service_FilterIterator',
    'implements' => 
    array (
      0 => 'Countable',
    ),
  ),
  'StellarWP\\Uplink\\Resources\\License' => 
  array (
    'type' => 'class',
    'classname' => 'License',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Resources',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Resources\\License',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Resources\\Plugin' => 
  array (
    'type' => 'class',
    'classname' => 'Plugin',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Resources',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Resources\\Plugin',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Resources\\Resource' => 
  array (
    'type' => 'class',
    'classname' => 'Resource',
    'isabstract' => true,
    'namespace' => 'StellarWP\\Uplink\\Resources',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Resources\\Resource',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Resources\\Service' => 
  array (
    'type' => 'class',
    'classname' => 'Service',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Resources',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Resources\\Service',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Site\\Data' => 
  array (
    'type' => 'class',
    'classname' => 'Data',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Site',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Site\\Data',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Storage\\Drivers\\Option_Storage' => 
  array (
    'type' => 'class',
    'classname' => 'Option_Storage',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Storage\\Drivers',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Storage\\Drivers\\Option_Storage',
    'implements' => 
    array (
      0 => 'StellarWP\\Uplink\\Storage\\Contracts\\Storage',
    ),
  ),
  'StellarWP\\Uplink\\Storage\\Drivers\\Transient_Storage' => 
  array (
    'type' => 'class',
    'classname' => 'Transient_Storage',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Storage\\Drivers',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Storage\\Drivers\\Transient_Storage',
    'implements' => 
    array (
      0 => 'StellarWP\\Uplink\\Storage\\Contracts\\Storage',
    ),
  ),
  'StellarWP\\Uplink\\Storage\\Exceptions\\Invalid_Key_Exception' => 
  array (
    'type' => 'class',
    'classname' => 'Invalid_Key_Exception',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Storage\\Exceptions',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Storage\\Exceptions\\Invalid_Key_Exception',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Storage\\Provider' => 
  array (
    'type' => 'class',
    'classname' => 'Provider',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Storage',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Storage\\Provider',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Uplink' => 
  array (
    'type' => 'class',
    'classname' => 'Uplink',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Uplink',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Utils\\Checks' => 
  array (
    'type' => 'class',
    'classname' => 'Checks',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Utils',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Utils\\Checks',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\Utils\\Sanitize' => 
  array (
    'type' => 'class',
    'classname' => 'Sanitize',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\Utils',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\Utils\\Sanitize',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\View\\Exceptions\\FileNotFoundException' => 
  array (
    'type' => 'class',
    'classname' => 'FileNotFoundException',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\View\\Exceptions',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\View\\Exceptions\\FileNotFoundException',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\View\\Provider' => 
  array (
    'type' => 'class',
    'classname' => 'Provider',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\View',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\View\\Provider',
    'implements' => 
    array (
    ),
  ),
  'StellarWP\\Uplink\\View\\WordPress_View' => 
  array (
    'type' => 'class',
    'classname' => 'WordPress_View',
    'isabstract' => false,
    'namespace' => 'StellarWP\\Uplink\\View',
    'extends' => 'TEC\\Common\\StellarWP\\Uplink\\View\\WordPress_View',
    'implements' => 
    array (
      0 => 'StellarWP\\Uplink\\View\\Contracts\\View',
    ),
  ),
  'TrustedLogin\\Admin' => 
  array (
    'type' => 'class',
    'classname' => 'Admin',
    'isabstract' => false,
    'namespace' => 'TrustedLogin',
    'extends' => 'TEC\\Common\\TrustedLogin\\Admin',
    'implements' => 
    array (
    ),
  ),
  'TrustedLogin\\Ajax' => 
  array (
    'type' => 'class',
    'classname' => 'Ajax',
    'isabstract' => false,
    'namespace' => 'TrustedLogin',
    'extends' => 'TEC\\Common\\TrustedLogin\\Ajax',
    'implements' => 
    array (
    ),
  ),
  'TrustedLogin\\Client' => 
  array (
    'type' => 'class',
    'classname' => 'Client',
    'isabstract' => false,
    'namespace' => 'TrustedLogin',
    'extends' => 'TEC\\Common\\TrustedLogin\\Client',
    'implements' => 
    array (
    ),
  ),
  'TrustedLogin\\Config' => 
  array (
    'type' => 'class',
    'classname' => 'Config',
    'isabstract' => false,
    'namespace' => 'TrustedLogin',
    'extends' => 'TEC\\Common\\TrustedLogin\\Config',
    'implements' => 
    array (
    ),
  ),
  'TrustedLogin\\Cron' => 
  array (
    'type' => 'class',
    'classname' => 'Cron',
    'isabstract' => false,
    'namespace' => 'TrustedLogin',
    'extends' => 'TEC\\Common\\TrustedLogin\\Cron',
    'implements' => 
    array (
    ),
  ),
  'TrustedLogin\\Encryption' => 
  array (
    'type' => 'class',
    'classname' => 'Encryption',
    'isabstract' => false,
    'namespace' => 'TrustedLogin',
    'extends' => 'TEC\\Common\\TrustedLogin\\Encryption',
    'implements' => 
    array (
    ),
  ),
  'TrustedLogin\\Endpoint' => 
  array (
    'type' => 'class',
    'classname' => 'Endpoint',
    'isabstract' => false,
    'namespace' => 'TrustedLogin',
    'extends' => 'TEC\\Common\\TrustedLogin\\Endpoint',
    'implements' => 
    array (
    ),
  ),
  'TrustedLogin\\Envelope' => 
  array (
    'type' => 'class',
    'classname' => 'Envelope',
    'isabstract' => false,
    'namespace' => 'TrustedLogin',
    'extends' => 'TEC\\Common\\TrustedLogin\\Envelope',
    'implements' => 
    array (
    ),
  ),
  'TrustedLogin\\Form' => 
  array (
    'type' => 'class',
    'classname' => 'Form',
    'isabstract' => false,
    'namespace' => 'TrustedLogin',
    'extends' => 'TEC\\Common\\TrustedLogin\\Form',
    'implements' => 
    array (
    ),
  ),
  'TrustedLogin\\Logger' => 
  array (
    'type' => 'class',
    'classname' => 'Logger',
    'isabstract' => false,
    'namespace' => 'TrustedLogin',
    'extends' => 'TEC\\Common\\TrustedLogin\\Logger',
    'implements' => 
    array (
    ),
  ),
  'TrustedLogin\\Logging' => 
  array (
    'type' => 'class',
    'classname' => 'Logging',
    'isabstract' => false,
    'namespace' => 'TrustedLogin',
    'extends' => 'TEC\\Common\\TrustedLogin\\Logging',
    'implements' => 
    array (
    ),
  ),
  'TrustedLogin\\Remote' => 
  array (
    'type' => 'class',
    'classname' => 'Remote',
    'isabstract' => false,
    'namespace' => 'TrustedLogin',
    'extends' => 'TEC\\Common\\TrustedLogin\\Remote',
    'implements' => 
    array (
    ),
  ),
  'TrustedLogin\\SecurityChecks' => 
  array (
    'type' => 'class',
    'classname' => 'SecurityChecks',
    'isabstract' => false,
    'namespace' => 'TrustedLogin',
    'extends' => 'TEC\\Common\\TrustedLogin\\SecurityChecks',
    'implements' => 
    array (
    ),
  ),
  'TrustedLogin\\SiteAccess' => 
  array (
    'type' => 'class',
    'classname' => 'SiteAccess',
    'isabstract' => false,
    'namespace' => 'TrustedLogin',
    'extends' => 'TEC\\Common\\TrustedLogin\\SiteAccess',
    'implements' => 
    array (
    ),
  ),
  'TrustedLogin\\SupportRole' => 
  array (
    'type' => 'class',
    'classname' => 'SupportRole',
    'isabstract' => false,
    'namespace' => 'TrustedLogin',
    'extends' => 'TEC\\Common\\TrustedLogin\\SupportRole',
    'implements' => 
    array (
    ),
  ),
  'TrustedLogin\\SupportUser' => 
  array (
    'type' => 'class',
    'classname' => 'SupportUser',
    'isabstract' => false,
    'namespace' => 'TrustedLogin',
    'extends' => 'TEC\\Common\\TrustedLogin\\SupportUser',
    'implements' => 
    array (
    ),
  ),
  'TrustedLogin\\Utils' => 
  array (
    'type' => 'class',
    'classname' => 'Utils',
    'isabstract' => false,
    'namespace' => 'TrustedLogin',
    'extends' => 'TEC\\Common\\TrustedLogin\\Utils',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\FormattableHandlerTrait' => 
  array (
    'type' => 'trait',
    'traitname' => 'FormattableHandlerTrait',
    'namespace' => 'Monolog\\Handler',
    'use' => 
    array (
      0 => 'TEC\\Common\\Monolog\\Handler\\FormattableHandlerTrait',
    ),
  ),
  'Monolog\\Handler\\ProcessableHandlerTrait' => 
  array (
    'type' => 'trait',
    'traitname' => 'ProcessableHandlerTrait',
    'namespace' => 'Monolog\\Handler',
    'use' => 
    array (
      0 => 'TEC\\Common\\Monolog\\Handler\\ProcessableHandlerTrait',
    ),
  ),
  'Monolog\\Handler\\WebRequestRecognizerTrait' => 
  array (
    'type' => 'trait',
    'traitname' => 'WebRequestRecognizerTrait',
    'namespace' => 'Monolog\\Handler',
    'use' => 
    array (
      0 => 'TEC\\Common\\Monolog\\Handler\\WebRequestRecognizerTrait',
    ),
  ),
  'Psr\\Log\\LoggerAwareTrait' => 
  array (
    'type' => 'trait',
    'traitname' => 'LoggerAwareTrait',
    'namespace' => 'Psr\\Log',
    'use' => 
    array (
      0 => 'TEC\\Common\\Psr\\Log\\LoggerAwareTrait',
    ),
  ),
  'Psr\\Log\\LoggerTrait' => 
  array (
    'type' => 'trait',
    'traitname' => 'LoggerTrait',
    'namespace' => 'Psr\\Log',
    'use' => 
    array (
      0 => 'TEC\\Common\\Psr\\Log\\LoggerTrait',
    ),
  ),
  'StellarWP\\AdminNotices\\Traits\\HasNamespace' => 
  array (
    'type' => 'trait',
    'traitname' => 'HasNamespace',
    'namespace' => 'StellarWP\\AdminNotices\\Traits',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\AdminNotices\\Traits\\HasNamespace',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\Aggregate' => 
  array (
    'type' => 'trait',
    'traitname' => 'Aggregate',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Concerns\\Aggregate',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\CRUD' => 
  array (
    'type' => 'trait',
    'traitname' => 'CRUD',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Concerns\\CRUD',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\FromClause' => 
  array (
    'type' => 'trait',
    'traitname' => 'FromClause',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Concerns\\FromClause',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\GroupByStatement' => 
  array (
    'type' => 'trait',
    'traitname' => 'GroupByStatement',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Concerns\\GroupByStatement',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\HavingClause' => 
  array (
    'type' => 'trait',
    'traitname' => 'HavingClause',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Concerns\\HavingClause',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\JoinClause' => 
  array (
    'type' => 'trait',
    'traitname' => 'JoinClause',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Concerns\\JoinClause',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\LimitStatement' => 
  array (
    'type' => 'trait',
    'traitname' => 'LimitStatement',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Concerns\\LimitStatement',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\MetaQuery' => 
  array (
    'type' => 'trait',
    'traitname' => 'MetaQuery',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Concerns\\MetaQuery',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\OffsetStatement' => 
  array (
    'type' => 'trait',
    'traitname' => 'OffsetStatement',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Concerns\\OffsetStatement',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\OrderByStatement' => 
  array (
    'type' => 'trait',
    'traitname' => 'OrderByStatement',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Concerns\\OrderByStatement',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\SelectStatement' => 
  array (
    'type' => 'trait',
    'traitname' => 'SelectStatement',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Concerns\\SelectStatement',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\TablePrefix' => 
  array (
    'type' => 'trait',
    'traitname' => 'TablePrefix',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Concerns\\TablePrefix',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\UnionOperator' => 
  array (
    'type' => 'trait',
    'traitname' => 'UnionOperator',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Concerns\\UnionOperator',
    ),
  ),
  'StellarWP\\DB\\QueryBuilder\\Concerns\\WhereClause' => 
  array (
    'type' => 'trait',
    'traitname' => 'WhereClause',
    'namespace' => 'StellarWP\\DB\\QueryBuilder\\Concerns',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\DB\\QueryBuilder\\Concerns\\WhereClause',
    ),
  ),
  'StellarWP\\Schema\\Traits\\Custom_Table_Query_Methods' => 
  array (
    'type' => 'trait',
    'traitname' => 'Custom_Table_Query_Methods',
    'namespace' => 'StellarWP\\Schema\\Traits',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Schema\\Traits\\Custom_Table_Query_Methods',
    ),
  ),
  'StellarWP\\Schema\\Traits\\Indexable' => 
  array (
    'type' => 'trait',
    'traitname' => 'Indexable',
    'namespace' => 'StellarWP\\Schema\\Traits',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Schema\\Traits\\Indexable',
    ),
  ),
  'StellarWP\\Shepherd\\Traits\\Loggable' => 
  array (
    'type' => 'trait',
    'traitname' => 'Loggable',
    'namespace' => 'StellarWP\\Shepherd\\Traits',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Shepherd\\Traits\\Loggable',
    ),
  ),
  'StellarWP\\Uplink\\Storage\\Traits\\With_Key_Formatter' => 
  array (
    'type' => 'trait',
    'traitname' => 'With_Key_Formatter',
    'namespace' => 'StellarWP\\Uplink\\Storage\\Traits',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Uplink\\Storage\\Traits\\With_Key_Formatter',
    ),
  ),
  'StellarWP\\Uplink\\Traits\\With_Debugging' => 
  array (
    'type' => 'trait',
    'traitname' => 'With_Debugging',
    'namespace' => 'StellarWP\\Uplink\\Traits',
    'use' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Uplink\\Traits\\With_Debugging',
    ),
  ),
  'lucatume\\DI52\\Builders\\BuilderInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'BuilderInterface',
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 
    array (
      0 => 'TEC\\Common\\lucatume\\DI52\\Builders\\BuilderInterface',
    ),
  ),
  'lucatume\\DI52\\Builders\\ReinitializableBuilderInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ReinitializableBuilderInterface',
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 
    array (
      0 => 'TEC\\Common\\lucatume\\DI52\\Builders\\ReinitializableBuilderInterface',
    ),
  ),
  'Monolog\\Formatter\\FormatterInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'FormatterInterface',
    'namespace' => 'Monolog\\Formatter',
    'extends' => 
    array (
      0 => 'TEC\\Common\\Monolog\\Formatter\\FormatterInterface',
    ),
  ),
  'Monolog\\Handler\\FingersCrossed\\ActivationStrategyInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ActivationStrategyInterface',
    'namespace' => 'Monolog\\Handler\\FingersCrossed',
    'extends' => 
    array (
      0 => 'TEC\\Common\\Monolog\\Handler\\FingersCrossed\\ActivationStrategyInterface',
    ),
  ),
  'Monolog\\Handler\\FormattableHandlerInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'FormattableHandlerInterface',
    'namespace' => 'Monolog\\Handler',
    'extends' => 
    array (
      0 => 'TEC\\Common\\Monolog\\Handler\\FormattableHandlerInterface',
    ),
  ),
  'Monolog\\Handler\\HandlerInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'HandlerInterface',
    'namespace' => 'Monolog\\Handler',
    'extends' => 
    array (
      0 => 'TEC\\Common\\Monolog\\Handler\\HandlerInterface',
    ),
  ),
  'Monolog\\Handler\\ProcessableHandlerInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ProcessableHandlerInterface',
    'namespace' => 'Monolog\\Handler',
    'extends' => 
    array (
      0 => 'TEC\\Common\\Monolog\\Handler\\ProcessableHandlerInterface',
    ),
  ),
  'Monolog\\LogRecord' => 
  array (
    'type' => 'interface',
    'interfacename' => 'LogRecord',
    'namespace' => 'Monolog',
    'extends' => 
    array (
      0 => 'TEC\\Common\\Monolog\\LogRecord',
    ),
  ),
  'Monolog\\Processor\\ProcessorInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ProcessorInterface',
    'namespace' => 'Monolog\\Processor',
    'extends' => 
    array (
      0 => 'TEC\\Common\\Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\ResettableInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ResettableInterface',
    'namespace' => 'Monolog',
    'extends' => 
    array (
      0 => 'TEC\\Common\\Monolog\\ResettableInterface',
    ),
  ),
  'Psr\\Container\\ContainerExceptionInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ContainerExceptionInterface',
    'namespace' => 'Psr\\Container',
    'extends' => 
    array (
      0 => 'TEC\\Common\\Psr\\Container\\ContainerExceptionInterface',
    ),
  ),
  'Psr\\Container\\ContainerInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ContainerInterface',
    'namespace' => 'Psr\\Container',
    'extends' => 
    array (
      0 => 'TEC\\Common\\Psr\\Container\\ContainerInterface',
    ),
  ),
  'Psr\\Container\\NotFoundExceptionInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'NotFoundExceptionInterface',
    'namespace' => 'Psr\\Container',
    'extends' => 
    array (
      0 => 'TEC\\Common\\Psr\\Container\\NotFoundExceptionInterface',
    ),
  ),
  'Psr\\Log\\LoggerAwareInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'LoggerAwareInterface',
    'namespace' => 'Psr\\Log',
    'extends' => 
    array (
      0 => 'TEC\\Common\\Psr\\Log\\LoggerAwareInterface',
    ),
  ),
  'Psr\\Log\\LoggerInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'LoggerInterface',
    'namespace' => 'Psr\\Log',
    'extends' => 
    array (
      0 => 'TEC\\Common\\Psr\\Log\\LoggerInterface',
    ),
  ),
  'StellarWP\\AdminNotices\\Contracts\\NotificationsRegistrarInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'NotificationsRegistrarInterface',
    'namespace' => 'StellarWP\\AdminNotices\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\AdminNotices\\Contracts\\NotificationsRegistrarInterface',
    ),
  ),
  'StellarWP\\ContainerContract\\ContainerInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ContainerInterface',
    'namespace' => 'StellarWP\\ContainerContract',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\ContainerContract\\ContainerInterface',
    ),
  ),
  'StellarWP\\Installer\\Contracts\\Handler' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Handler',
    'namespace' => 'StellarWP\\Installer\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Installer\\Contracts\\Handler',
    ),
  ),
  'StellarWP\\Models\\Contracts\\Arrayable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Arrayable',
    'namespace' => 'StellarWP\\Models\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Models\\Contracts\\Arrayable',
    ),
  ),
  'StellarWP\\Models\\Contracts\\Model' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Model',
    'namespace' => 'StellarWP\\Models\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Models\\Contracts\\Model',
    ),
  ),
  'StellarWP\\Models\\Contracts\\ModelPersistable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ModelPersistable',
    'namespace' => 'StellarWP\\Models\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Models\\Contracts\\ModelPersistable',
    ),
  ),
  'StellarWP\\Models\\Repositories\\Contracts\\Deletable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Deletable',
    'namespace' => 'StellarWP\\Models\\Repositories\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Models\\Repositories\\Contracts\\Deletable',
    ),
  ),
  'StellarWP\\Models\\Repositories\\Contracts\\Insertable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Insertable',
    'namespace' => 'StellarWP\\Models\\Repositories\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Models\\Repositories\\Contracts\\Insertable',
    ),
  ),
  'StellarWP\\Models\\Repositories\\Contracts\\Updatable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Updatable',
    'namespace' => 'StellarWP\\Models\\Repositories\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Models\\Repositories\\Contracts\\Updatable',
    ),
  ),
  'StellarWP\\SchemaModels\\Contracts\\Relationships\\ManyToManyWithPosts' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ManyToManyWithPosts',
    'namespace' => 'StellarWP\\SchemaModels\\Contracts\\Relationships',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\SchemaModels\\Contracts\\Relationships\\ManyToManyWithPosts',
    ),
  ),
  'StellarWP\\SchemaModels\\Contracts\\Relationships\\RelationshipCRUD' => 
  array (
    'type' => 'interface',
    'interfacename' => 'RelationshipCRUD',
    'namespace' => 'StellarWP\\SchemaModels\\Contracts\\Relationships',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\SchemaModels\\Contracts\\Relationships\\RelationshipCRUD',
    ),
  ),
  'StellarWP\\SchemaModels\\Contracts\\SchemaModel' => 
  array (
    'type' => 'interface',
    'interfacename' => 'SchemaModel',
    'namespace' => 'StellarWP\\SchemaModels\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\SchemaModels\\Contracts\\SchemaModel',
    ),
  ),
  'StellarWP\\Schema\\Columns\\Contracts\\Auto_Incrementable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Auto_Incrementable',
    'namespace' => 'StellarWP\\Schema\\Columns\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Contracts\\Auto_Incrementable',
    ),
  ),
  'StellarWP\\Schema\\Columns\\Contracts\\Column_Interface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Column_Interface',
    'namespace' => 'StellarWP\\Schema\\Columns\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Contracts\\Column_Interface',
    ),
  ),
  'StellarWP\\Schema\\Columns\\Contracts\\Indexable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Indexable',
    'namespace' => 'StellarWP\\Schema\\Columns\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Contracts\\Indexable',
    ),
  ),
  'StellarWP\\Schema\\Columns\\Contracts\\Lengthable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Lengthable',
    'namespace' => 'StellarWP\\Schema\\Columns\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Contracts\\Lengthable',
    ),
  ),
  'StellarWP\\Schema\\Columns\\Contracts\\Precisionable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Precisionable',
    'namespace' => 'StellarWP\\Schema\\Columns\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Contracts\\Precisionable',
    ),
  ),
  'StellarWP\\Schema\\Columns\\Contracts\\Primarable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Primarable',
    'namespace' => 'StellarWP\\Schema\\Columns\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Contracts\\Primarable',
    ),
  ),
  'StellarWP\\Schema\\Columns\\Contracts\\Signable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Signable',
    'namespace' => 'StellarWP\\Schema\\Columns\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Contracts\\Signable',
    ),
  ),
  'StellarWP\\Schema\\Columns\\Contracts\\Uniquable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Uniquable',
    'namespace' => 'StellarWP\\Schema\\Columns\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Schema\\Columns\\Contracts\\Uniquable',
    ),
  ),
  'StellarWP\\Schema\\Indexes\\Contracts\\Index' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Index',
    'namespace' => 'StellarWP\\Schema\\Indexes\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Schema\\Indexes\\Contracts\\Index',
    ),
  ),
  'StellarWP\\Schema\\Tables\\Contracts\\Table_Interface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Table_Interface',
    'namespace' => 'StellarWP\\Schema\\Tables\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Schema\\Tables\\Contracts\\Table_Interface',
    ),
  ),
  'StellarWP\\Schema\\Tables\\Contracts\\Table_Schema_Interface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Table_Schema_Interface',
    'namespace' => 'StellarWP\\Schema\\Tables\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Schema\\Tables\\Contracts\\Table_Schema_Interface',
    ),
  ),
  'StellarWP\\Shepherd\\Contracts\\Log_Model' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Log_Model',
    'namespace' => 'StellarWP\\Shepherd\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Shepherd\\Contracts\\Log_Model',
    ),
  ),
  'StellarWP\\Shepherd\\Contracts\\Logger' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Logger',
    'namespace' => 'StellarWP\\Shepherd\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Shepherd\\Contracts\\Logger',
    ),
  ),
  'StellarWP\\Shepherd\\Contracts\\Model' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Model',
    'namespace' => 'StellarWP\\Shepherd\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Shepherd\\Contracts\\Model',
    ),
  ),
  'StellarWP\\Shepherd\\Contracts\\Task' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Task',
    'namespace' => 'StellarWP\\Shepherd\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Shepherd\\Contracts\\Task',
    ),
  ),
  'StellarWP\\Shepherd\\Contracts\\Task_Model' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Task_Model',
    'namespace' => 'StellarWP\\Shepherd\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Shepherd\\Contracts\\Task_Model',
    ),
  ),
  'StellarWP\\Telemetry\\Contracts\\Data_Provider' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Data_Provider',
    'namespace' => 'StellarWP\\Telemetry\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Telemetry\\Contracts\\Data_Provider',
    ),
  ),
  'StellarWP\\Telemetry\\Contracts\\Runnable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Runnable',
    'namespace' => 'StellarWP\\Telemetry\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Telemetry\\Contracts\\Runnable',
    ),
  ),
  'StellarWP\\Telemetry\\Contracts\\Subscriber_Interface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Subscriber_Interface',
    'namespace' => 'StellarWP\\Telemetry\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Telemetry\\Contracts\\Subscriber_Interface',
    ),
  ),
  'StellarWP\\Telemetry\\Contracts\\Template_Interface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Template_Interface',
    'namespace' => 'StellarWP\\Telemetry\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Telemetry\\Contracts\\Template_Interface',
    ),
  ),
  'StellarWP\\Uplink\\API\\V3\\Auth\\Contracts\\Auth_Url' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Auth_Url',
    'namespace' => 'StellarWP\\Uplink\\API\\V3\\Auth\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Uplink\\API\\V3\\Auth\\Contracts\\Auth_Url',
    ),
  ),
  'StellarWP\\Uplink\\API\\V3\\Auth\\Contracts\\Token_Authorizer' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Token_Authorizer',
    'namespace' => 'StellarWP\\Uplink\\API\\V3\\Auth\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Uplink\\API\\V3\\Auth\\Contracts\\Token_Authorizer',
    ),
  ),
  'StellarWP\\Uplink\\API\\V3\\Contracts\\Client_V3' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Client_V3',
    'namespace' => 'StellarWP\\Uplink\\API\\V3\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Uplink\\API\\V3\\Contracts\\Client_V3',
    ),
  ),
  'StellarWP\\Uplink\\Auth\\Token\\Contracts\\Token_Manager' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Token_Manager',
    'namespace' => 'StellarWP\\Uplink\\Auth\\Token\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Uplink\\Auth\\Token\\Contracts\\Token_Manager',
    ),
  ),
  'StellarWP\\Uplink\\Contracts\\Provider_Interface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Provider_Interface',
    'namespace' => 'StellarWP\\Uplink\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Uplink\\Contracts\\Provider_Interface',
    ),
  ),
  'StellarWP\\Uplink\\Storage\\Contracts\\Storage' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Storage',
    'namespace' => 'StellarWP\\Uplink\\Storage\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Uplink\\Storage\\Contracts\\Storage',
    ),
  ),
  'StellarWP\\Uplink\\View\\Contracts\\View' => 
  array (
    'type' => 'interface',
    'interfacename' => 'View',
    'namespace' => 'StellarWP\\Uplink\\View\\Contracts',
    'extends' => 
    array (
      0 => 'TEC\\Common\\StellarWP\\Uplink\\View\\Contracts\\View',
    ),
  ),
);

        public function __construct()
        {
            $this->includeFilePath = __DIR__ . '/autoload_alias.php';
        }

        public function autoload($class)
        {
            if (!isset($this->autoloadAliases[$class])) {
                return;
            }
            switch ($this->autoloadAliases[$class]['type']) {
                case 'class':
                        $this->load(
                            $this->classTemplate(
                                $this->autoloadAliases[$class]
                            )
                        );
                    break;
                case 'interface':
                    $this->load(
                        $this->interfaceTemplate(
                            $this->autoloadAliases[$class]
                        )
                    );
                    break;
                case 'trait':
                    $this->load(
                        $this->traitTemplate(
                            $this->autoloadAliases[$class]
                        )
                    );
                    break;
                default:
                    // Never.
                    break;
            }
        }

        private function load(string $includeFile)
        {
            file_put_contents($this->includeFilePath, $includeFile);
            include $this->includeFilePath;
            file_exists($this->includeFilePath) && unlink($this->includeFilePath);
        }

        private function classTemplate(array $class): string
        {
            $abstract = $class['isabstract'] ? 'abstract ' : '';
            $classname = $class['classname'];
            if (isset($class['namespace'])) {
                $namespace = "namespace {$class['namespace']};";
                $extends = '\\' . $class['extends'];
                $implements = empty($class['implements']) ? ''
                : ' implements \\' . implode(', \\', $class['implements']);
            } else {
                $namespace = '';
                $extends = $class['extends'];
                $implements = !empty($class['implements']) ? ''
                : ' implements ' . implode(', ', $class['implements']);
            }
            return <<<EOD
                <?php
                $namespace
                $abstract class $classname extends $extends $implements {}
                EOD;
        }

        private function interfaceTemplate(array $interface): string
        {
            $interfacename = $interface['interfacename'];
            $namespace = isset($interface['namespace'])
            ? "namespace {$interface['namespace']};" : '';
            $extends = isset($interface['namespace'])
            ? '\\' . implode('\\ ,', $interface['extends'])
            : implode(', ', $interface['extends']);
            return <<<EOD
                <?php
                $namespace
                interface $interfacename extends $extends {}
                EOD;
        }
        private function traitTemplate(array $trait): string
        {
            $traitname = $trait['traitname'];
            $namespace = isset($trait['namespace'])
            ? "namespace {$trait['namespace']};" : '';
            $uses = isset($trait['namespace'])
            ? '\\' . implode(';' . PHP_EOL . '    use \\', $trait['use'])
            : implode(';' . PHP_EOL . '    use ', $trait['use']);
            return <<<EOD
                <?php
                $namespace
                trait $traitname { 
                    use $uses; 
                }
                EOD;
        }
    }

    spl_autoload_register([ new AliasAutoloader(), 'autoload' ]);
}
