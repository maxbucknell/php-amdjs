<?php

namespace MaxBucknell\AMDJS;

require_once 'Lib/amd-packager-php/Packager.php';
require_once 'MaxBucknell/AMDJS/Adapter.php';

class Modules
{
    private $id;

    public function __construct(Adapter $adapter, $ids)
    {
        $this->adapter = $adapter;
        $this->ids = $ids;
        $this->hash = $this->hash($this->ids);

        $this->packager = new \Packager();

        foreach ($adapter->getAliases() as $from => $to) {
            $this->packager->addAlias($from, $to);
        }

        $this->packager->setBaseUrl($adapter->getSourceBaseDir());
    }

    public function getUrls()
    {
        // If caching enabled, check the cache.
        if ($this->adapter->isCachingEnabled() && ($urls = $this->adapter->loadFromCache($this->hash))) {
            return $urls;
        }

        $urls = array();

        if ($this->adapter->splitDefaultModules()) {
            $urls[] = $this->getDefaultModulesUrl();
            $urls[] = $this->getCustomModulesUrl();
        } else if ($this->adapter->isConcatenationEnabled()) {
            $urls[] = $this->getAllModulesUrl();
        } else {
            foreach ($this->ids as $id) {
                $urls = array_merge($urls, $this->getModuleUrls($id));
            }
            $urls = array_unique($urls);
            $urls[] = $this->getConfigUrl();
        }

        if ($this->adapter->isCachingEnabled()) {
            $this->adapter->cacheModules($this->hash, $urls);
        }

        return $urls;
    }

    private function hash($ids)
    {
        ksort($ids);
        return md5(implode($ids));
    }

    private function getDefaultModulesUrl()
    {
        $defaultModules = $this->adapter->getDefaultModules();

        if ($cache = $this->adapter->loadFromCache('amdjs_default')) {
            return $cache;
        }

        $output = $this->buildModules($defaultModules);

        $path = $this->adapter->getBuiltBaseDir() . '/cache/amdjs_default.js';

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, $output);

        return $this->adapter->getBuiltBaseUrl() . '/cache/amdjs_default.js';
    }

    private function getCustomModulesUrl()
    {
        $customModules = array_diff($this->ids, $this->adapter->getDefaultModules());
        $output = $this->buildModules($customModules);

        $path = $this->adapter->getBuiltBaseDir() . '/cache/' . $this->hash . '.js';

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, $output . $this->getConfig());
        return $this->adapter->getBuiltBaseUrl() . '/cache/' . $this->hash . '.js';
    }

    private function getAllModulesUrl()
    {
        $output = $this->buildModules($this->ids);

        $path = $this->adapter->getBuiltBaseDir() . '/cache/' . $this->hash . '.js';

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, $output . $this->getConfig());
        return $this->adapter->getBuiltBaseUrl() . '/cache/' . $this->hash . '.js';
    }

    private function getModuleUrls($id)
    {
        $modules = $this->packager->req(array($id))->loaded();
        $urls = array();
        foreach ($modules as $module) {
            $path = $this->adapter->getBuiltBaseDir() . '/modules/' . $module['id'] . '.js';
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            $content = file_get_contents($this->adapter->getSourceBaseDir() . '/' . $module['id'] . '.js');
            $content = preg_replace('/define\((\[|\{|function)/', "define('" . $module['id'] . "', $1", $content);
            file_put_contents($path, $content);

            $urls[] = $this->adapter->getBuiltBaseUrl() . '/modules/' . $module['id'] . '.js';
        }


        return $urls;
    }

    private function buildModules($ids)
    {
        $output = $this->packager->req($ids)->output();

        foreach ($this->adapter->getOutputTransformers() as $transformer)
        {
            $output = $transform($output);
        }

        return $output;
    }

    private function getConfig()
    {
        return "\n\n" . 'require.config({ "deps": ' . json_encode($this->ids) . ' });' . "\n";
    }

    private function getConfigUrl()
    {
        $path = $this->adapter->getBuiltBaseDir() . '/cache/' . $this->hash . '.js';

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, $this->getConfig());
        return $this->adapter->getBuiltBaseUrl() . '/cache/' . $this->hash . '.js';
    }
}
