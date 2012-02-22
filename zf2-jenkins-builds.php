<?php
// put this file into your zf2 clone dir.
// require 'sudo' for execute 'chown'
// $sudo php zf2-jenekins-builds.php 

$do_chown = (isset($argv[1]) && !$argv[1]) ? false : true;

$builds_dir = __DIR__.'/builds';
$tests_path = __DIR__.'/tests/Zend';
$user = 'jenkins';
$jobs_dir = '/var/lib/jenkins/jobs';
$jobs_tmp_dir = __DIR__.'/jobs';

setUp();
run();
echo PHP_EOL;
exit(0);

function setUp() {
    global $builds_dir, $tests_path, $jobs_tmp_dir;
    if (!is_dir($tests_path)) {
        throw new Exception ();
    }

    // mkdir builds ?
    if (!is_dir($builds_dir)) {
        mkdir($builds_dir);
    }
    if (!is_dir($jobs_tmp_dir)) {
        mkdir($jobs_tmp_dir);
    }
}

/**
 * make (All component)
 */
function run() {
    global $do_chown, $builds_dir, $tests_path, $user, $jobs_dir, $jobs_tmp_dir;

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
            make($component, $tests, $user, $do_chown, true, $component.'.php');
            make_job($component, $builds_dir, $jobs_tmp_dir, $user, $do_chown);
        } else if ($d->getFilename() == 'Service') {
            foreach (new DirectoryIterator($d->getRealpath()) as $s) {
                if ($s->isDot()) continue;

                $component = "Service".$s->getFilename();
                $tests = "../../tests/Zend/Service/".$s->getFilename();
                make($component, $tests, $user, $do_chown, false, "Service/".$s->getFilename());
                make_job($component, $builds_dir, $jobs_tmp_dir, $user, $do_chown);
            }

        } else {
            $component = $d->getFilename();
            $tests = "../../tests/Zend/".$d->getFileName();
            make($component, $tests, $user, $do_chown, false, $d->getFilename());
            make_job($component, $builds_dir, $jobs_tmp_dir, $user, $do_chown);
        }

    }
}

        //echo $component, PHP_EOL;
        //$mkdir builds/Amf
        //$ppw --name ZF2Amf --tests ../../tests/Zend/Amf --bootstrap ../../tests/Bootstrap.php builds/Amf
        //$chown builds/Amf/build/logs
/**
 * make build files by PHP Project Wizard
 */
function make($component, $tests, $user, $do_chown, $replace_phpunitxml = false, $component_source) {
        echo "mkdir builds/$component", " .... ";
        mkdir("builds/$component");

        echo "ppw & chown", PHP_EOL;
        $ppw = "ppw --source ../../library/Zend/$component_source --name ZF2$component --tests $tests --bootstrap ../../tests/Bootstrap.php builds/$component";
        exec($ppw);
        if ($replace_phpunitxml) {
            replace_phpunitxml("builds/$component/phpunit.xml.dist", $component); 
        }

        mkdir("builds/$component/build");
        if ($do_chown) {
            $chown = "chown -R $user:nogroup builds/$component";
            exec($chown);
        }

        echo "build.xml is at " . __DIR__ . "/builds/$component/build.xml", PHP_EOL;
}

/**
 * /var/lib/jenkins/jobs
 */
function make_job($component, $builds_dir, $jobs_tmp_dir, $user, $do_chown, $prefix = 'ZF2') {
        echo "mkdir $jobs_tmp_dir/$prefix$component", " .... ";
        mkdir("$jobs_tmp_dir/$prefix$component");

        $build_dir = $builds_dir.'/'.$component;

$configxml = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<project>
  <actions/>
  <description>Zend Framework 2 $component Component</description>
  <keepDependencies>false</keepDependencies>
  <properties/>
  <scm class="hudson.scm.NullSCM"/>
  <canRoam>true</canRoam>
  <disabled>false</disabled>
  <blockBuildWhenDownstreamBuilding>false</blockBuildWhenDownstreamBuilding>
  <blockBuildWhenUpstreamBuilding>false</blockBuildWhenUpstreamBuilding>
  <triggers class="vector"/>
  <concurrentBuild>false</concurrentBuild>
  <builders>
    <hudson.tasks.Ant>
      <targets>phpunit</targets>
      <buildFile>$build_dir/build.xml</buildFile>
    </hudson.tasks.Ant>
  </builders>
  <publishers/>
  <buildWrappers/>
</project>
XML;

    file_put_contents($jobs_tmp_dir."/$prefix$component/config.xml", $configxml);

    if ($do_chown) {
        //$chown = "chown -R $user:nogroup $component_jobs_dir";
        $chown = "chown -R $user:nogroup $jobs_tmp_dir/$prefix$component";
        exec($chown);
    }
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


