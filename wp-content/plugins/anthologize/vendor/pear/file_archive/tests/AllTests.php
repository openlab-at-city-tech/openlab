<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'File_Archive_AllTests::main');
}

if ($fp = @fopen('PHPUnit/Autoload.php', 'r', true)) {
    require_once 'PHPUnit/Autoload.php';
} elseif ($fp = @fopen('PHPUnit/Framework.php', 'r', true)) {
    require_once 'PHPUnit/Framework.php';
} else {
    die("skip could not find PHPUnit");
}
fclose($fp);

require_once dirname(__FILE__) . '/FileArchiveTest.php';

class File_Archive_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('File_Archive package');
        $suite->addTestSuite('FileArchiveTest');


        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'File_Archive_AllTests::main') {
    File_Archive_AllTests::main();
}

