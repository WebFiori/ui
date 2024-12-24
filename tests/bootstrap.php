<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
$DS = DIRECTORY_SEPARATOR;
$testsDirName = 'tests';
$rootDir = substr(__DIR__, 0, strlen(__DIR__) - strlen($testsDirName));
ini_set('display_errors', 1);
error_reporting(-1);
$rootDirTrimmed = trim($rootDir,'/\\');
//echo 'Include Path: \''. get_include_path().'\''."\n";
if (explode($DS, $rootDirTrimmed)[0] == 'home') {
    //linux.
    $rootDir = $DS.$rootDirTrimmed.$DS;
} else {
    $rootDir = $rootDirTrimmed.$DS;
}
define('ROOT', $rootDir);

require_once __DIR__.$DS.'..'.$DS.'vendor'.$DS.'autoload.php';