<?php
// put this file into your zf2 clone dir.
// require 'sudo' for execute 'chown'
// $sudo php zf2-jenekins-builds.php 

$do_chown = (isset($argv[1]) && !$argv[1]) ? false : true;

$builds_dir = __DIR__.'/builds';
$tests_path = __DIR__.'/tests/Zend';
$user = 'jenkins';

setUp();
run();

function setUp() {
    global $builds_dir, $tests_path;
    if (!is_dir($tests_path)) {
        throw new Exception ();
    }

    // mkdir builds ?
    if (is_dir($builds_dir)) {
        $buildsDir = new DirectoryIterator($builds_dir);
        //if ($buildsDir->count() > 0) {
        //    throw new RuntimeException("$builds_dir is not empty");
        //}
    } else {
        mkdir($builds_dir);
    }
}

function run() {
    global $do_chown, $builds_dir, $tests_path, $user;

    foreach (new DirectoryIterator($tests_path) as $d) {
        $name = null;
        $component = null;
        $tests = null;

        $replace_phpunitxml = false;

        if ($d->isDot() || ($d->isDir() && in_array($d, array('AllTests', '_files')))) {
            continue;
        }

        if ($d->isFile() && in_array($d->getFileName(), array('DebugTest.php', 'RegistryTest.php', 'VersionTest.php'))) {
            $component = substr($d->getFilename(), 0, -8);
            $tests = "../../tests/Zend/".$d->getFileName();
            make($component, $tests, $user, $do_chown, true);
        } else if ($d->getFilename() == 'Service') {
            foreach (new DirectoryIterator($d->getRealpath()) as $s) {
                if ($s->isDot()) continue;

                $component = "Service".$s->getFilename();
                $tests = "../../tests/Zend/Service/".$s->getFilename();
                make($component, $tests, $user, $do_chown);
            }

        } else {
            $component = $d->getFilename();
            $tests = "../../tests/Zend/".$d->getFileName();
            make($component, $tests, $user, $do_chown);
        }

    }
}

        //echo $component, PHP_EOL;
        //$mkdir builds/Amf
        //$ppw --name ZF2Amf --tests ../../tests/Zend/Amf --bootstrap ../../tests/Bootstrap.php builds/Amf
        //$chown builds/Amf/build/logs
function make($component, $tests, $user, $do_chown, $replace_phpunitxml = false) {
        echo "mkdir builds/$component", " .... ";
        mkdir("builds/$component");

        echo "ppw & chown", PHP_EOL;
        $ppw = "ppw --name ZF2$component --tests $tests --bootstrap ../../tests/Bootstrap.php builds/$component";
        exec($ppw);
        if ($replace_phpunitxml) {
            replace_phpunitxml("builds/$component/phpunit.xml.dist", $component);        
        }

        mkdir("builds/$component/build");
        if ($do_chown) {
            $chown = "chown -R $user:nogroup builds/$component/build";
            exec($chown);
        }

        echo "build.xml is at " . __DIR__ . "/builds/$component/build.xml", PHP_EOL;
}


//<directory suffix="Test.php">../../tests/Zend/DebugTest.php</directory>
// ->
//<file>../../tests/Zend/DebugTest.php</file>
function replace_phpunitxml($phpunitxml, $component) {
    $target = sprintf('<directory suffix="Test.php">../../tests/Zend/%sTest.php</directory>', $component);
    $replace = sprintf("<file>../../tests/Zend/%sTest.php</file>", $component);
    $replaced = str_replace($target, $replace, file_get_contents($phpunitxml));
    file_put_contents($phpunitxml, $replaced);
}


