<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit90f43fa9dd54e820f67ac7497e2e9576
{
    public static $files = array (
        'a2bb5fd4cb9bceefb44ebec5edc18194' => __DIR__ . '/../..' . '/src/helpers.php',
    );

    public static $prefixLengthsPsr4 = array (
        'O' => 
        array (
            'OpenLab\\Portfolio\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'OpenLab\\Portfolio\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'OpenLab\\Portfolio\\Contracts\\Registerable' => __DIR__ . '/../..' . '/src/Contracts/Registerable.php',
        'OpenLab\\Portfolio\\Export\\Exporter' => __DIR__ . '/../..' . '/src/Export/Exporter.php',
        'OpenLab\\Portfolio\\Export\\Service' => __DIR__ . '/../..' . '/src/Export/Service.php',
        'OpenLab\\Portfolio\\Export\\WXP' => __DIR__ . '/../..' . '/src/Export/WXP.php',
        'OpenLab\\Portfolio\\Import\\ArchiveUpload' => __DIR__ . '/../..' . '/src/Import/ArchiveUpload.php',
        'OpenLab\\Portfolio\\Import\\Decompressor' => __DIR__ . '/../..' . '/src/Import/Decompressor.php',
        'OpenLab\\Portfolio\\Import\\Importer' => __DIR__ . '/../..' . '/src/Import/Importer.php',
        'OpenLab\\Portfolio\\Import\\Service' => __DIR__ . '/../..' . '/src/Import/Service.php',
        'OpenLab\\Portfolio\\Iterator\\UploadsIterator' => __DIR__ . '/../..' . '/src/Iterator/UploadsIterator.php',
        'OpenLab\\Portfolio\\Logger\\ErrorLogLogger' => __DIR__ . '/../..' . '/src/Logger/ErrorLogLogger.php',
        'OpenLab\\Portfolio\\Logger\\Logger' => __DIR__ . '/../..' . '/src/Logger/Logger.php',
        'OpenLab\\Portfolio\\Logger\\ServerSentEventsLogger' => __DIR__ . '/../..' . '/src/Logger/ServerSentEventsLogger.php',
        'OpenLab\\Portfolio\\Portfolio' => __DIR__ . '/../..' . '/src/Portfolio.php',
        'OpenLab\\Portfolio\\Share\\RestController' => __DIR__ . '/../..' . '/src/Share/RestController.php',
        'OpenLab\\Portfolio\\Share\\Service' => __DIR__ . '/../..' . '/src/Share/Service.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit90f43fa9dd54e820f67ac7497e2e9576::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit90f43fa9dd54e820f67ac7497e2e9576::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit90f43fa9dd54e820f67ac7497e2e9576::$classMap;

        }, null, ClassLoader::class);
    }
}
