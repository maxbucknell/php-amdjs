<?php

// require 'MaxBucknell/AMDJS/Test/Adapter.php';

namespace MaxBucknell\AMDJS\Test;

require_once 'vendor/autoload.php';
require_once 'MaxBucknell/AMDJS/Test/Adapter.php';
require_once 'MaxBucknell/AMDJS/Modules.php';

use MaxBucknell\AMDJS;

class ModulesTest extends \PHPUnit_Framework_TestCase
{
    private $adapter;

    public function setUp()
    {
        $this->adapter = new Adapter();
        $path = $this->adapter->getBuiltBaseDir();
        if (!is_dir($path)) {
            mkdir(realpath('MaxBucknell/AMDJS/Test') . '/dist');
        }
    }

    public function tearDown()
    {
        $this->deleteDirectory($this->adapter->getBuiltBaseDir());
    }

    private function deleteDirectory($path)
    {
        $files = array_slice(scandir($path), 2);

        foreach ($files as $file) {
            if (is_dir($path . '/' . $file)) {
                $this->deleteDirectory($path . '/' . $file);
            } else {
                unlink($path . '/' . $file);
            }
        }

        rmdir($path);
    }

    private function hash($ids)
    {
        ksort($ids);
        return md5(implode($ids));
    }

    public function testNoConcatenation()
    {
        $ids = array('foo/bar', 'mine/yours', 'foo/ding');

        $modules = new AMDJS\Modules($this->adapter, $ids);
        $urls = $modules->getUrls();

        foreach ($urls as $url) {
            $specimen = 'MaxBucknell/AMDJS/Test/specimen/testNoConcatenation' . $url;
            $actual = 'MaxBucknell/AMDJS/Test' . $url;

            $this->assertFileEquals($specimen, $actual, "$url is incorrect.");
        }
    }

    public function testConcatenation()
    {
        $ids = array('foo/bar', 'mine/yours');

        $this->adapter->setConcatenationEnabled(true);
        $modules = new AMDJS\Modules($this->adapter, $ids);

        $urls = $modules->getUrls();

        foreach ($urls as $url) {
            $specimen = 'MaxBucknell/AMDJS/Test/specimen/testConcatenation' . $url;
            $actual = 'MaxBucknell/AMDJS/Test' . $url;

            $this->assertFileEquals($specimen, $actual, "$url is incorrect.");
        }
    }

    public function testCaching()
    {
        $this->adapter->setCachingEnabled(true);

        $ids = array('foo/bar', 'mine/yours', 'foo/ding');

        $modules = new AMDJS\Modules($this->adapter, $ids);
        $urls = $modules->getUrls();

        $hash = $this->hash($ids);

        $cachedUrls = $this->adapter->loadFromCache($hash);

        if ($cachedUrls === false) {
            $cachedUrls = array('broken');
        }

        sort($urls);
        sort($cachedUrls);

        $this->assertEquals($urls, $cachedUrls);
    }

    public function testDefaultModules()
    {
        $this->adapter->setSplitDefaultModules(true);
        $this->adapter->setDefaultModules(array('foo/bar'));

        $ids = array('foo/ding', 'mine/yours', 'foo/baz');

        $modules = new AMDJS\Modules($this->adapter, $ids);
        $urls = $modules->getUrls();

        foreach ($urls as $url) {
            $specimen = 'MaxBucknell/AMDJS/Test/specimen/testDefaultModules' . $url;
            $actual = 'MaxBucknell/AMDJS/Test' . $url;

            $this->assertFileEquals($specimen, $actual, "$url is incorrect.");
        }
    }

    public function testAliases()
    {
        $ids = array('dapper');

        $this->adapter->setAliases(array('dapper' => 'foo/ding'));

        $modules = new AMDJS\Modules($this->adapter, $ids);
        $urls = $modules->getUrls();

        foreach ($urls as $url) {
            $specimen = 'MaxBucknell/AMDJS/Test/specimen/testAliases' . $url;
            $actual = 'MaxBucknell/AMDJS/Test' . $url;

            $this->assertFileEquals($specimen, $actual, "$url is incorrect.");
        }
    }
}
