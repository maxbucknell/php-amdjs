<?php

namespace AmdJs;
use AmdJs\Adapter;

class ModulesTest extends \PHPUnit_Framework_TestCase
{
    private $adapter;

    public function setUp()
    {
        $this->adapter = new Adapter\TestAdapter();
        $path = $this->adapter->getBuiltBaseDir();
        if (!is_dir($path)) {
            mkdir(realpath(__DIR__ . '/..') . '/dist');
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

        $modules = new Modules($this->adapter, $ids);
        $urls = $modules->getUrls();

        foreach ($urls as $url) {
            $specimen = __DIR__ . '/../specimen/testNoConcatenation' . $url;
            $actual = __DIR__ . '/../' . $url;

            $this->assertFileEquals($specimen, $actual, "$url is incorrect.");
        }
    }

    public function testConcatenation()
    {
        $ids = array('foo/bar', 'mine/yours');

        $this->adapter->setConcatenationEnabled(true);
        $modules = new Modules($this->adapter, $ids);

        $urls = $modules->getUrls();

        foreach ($urls as $url) {
            $specimen = __DIR__ . '/../specimen/testConcatenation' . $url;
            $actual = __DIR__ . '/../' . $url;

            $this->assertFileEquals($specimen, $actual, "$url is incorrect.");
        }
    }

    public function testCaching()
    {
        $this->adapter->setCachingEnabled(true);

        $ids = array('foo/bar', 'mine/yours', 'foo/ding');

        $modules = new Modules($this->adapter, $ids);
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

        $modules = new Modules($this->adapter, $ids);
        $urls = $modules->getUrls();

        foreach ($urls as $url) {
            $specimen = __DIR__ . '/../specimen/testDefaultModules' . $url;
            $actual = __DIR__ . '/../' . $url;

            $this->assertFileEquals($specimen, $actual, "$url is incorrect.");
        }
    }

    public function testAliases()
    {
        $ids = array('dapper');

        $this->adapter->setAliases(array('dapper' => 'foo/ding'));

        $modules = new Modules($this->adapter, $ids);
        $urls = $modules->getUrls();

        foreach ($urls as $url) {
            $specimen = __DIR__ . '/../specimen/testAliases' . $url;
            $actual = __DIR__ . '/../' . $url;

            $this->assertFileEquals($specimen, $actual, "$url is incorrect.");
        }
    }
}
