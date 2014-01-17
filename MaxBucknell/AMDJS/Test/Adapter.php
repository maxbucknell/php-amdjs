<?php
namespace MaxBucknell\AMDJS\Test;

require_once 'MaxBucknell/AMDJS/Adapter.php';

class Adapter implements \MaxBucknell\AMDJS\Adapter
{
    public function getSourceBaseDir()
    {
        return realpath('MaxBucknell/AMDJS/Test/source');
    }

    public function getBuiltBaseDir()
    {
        return realpath('MaxBucknell/AMDJS/Test/dist');
    }

    public function getBuiltBaseUrl()
    {
        return '/dist';
    }



    private $aliases = array();

    public function getAliases() {
        return $this->aliases;
    }

    public function setAliases($aliases)
    {
        $this->aliases = $aliases;
    }



    private $concatenation = false;

    public function isConcatenationEnabled()
    {
        return $this->concatenation;
    }

    public function setConcatenationEnabled($concatenation)
    {
        $this->concatenation = $concatenation;
    }



    private $transformers = array();

    public function getOutputTransformers()
    {
        return $this->transformers;
    }

    public function setOutputTransformers($transformers)
    {
        $this->transformers = $transformers;
    }



    private $caching = false;

    public function isCachingEnabled()
    {
        return $this->caching;
    }

    public function setCachingEnabled($caching)
    {
        $this->caching = $caching;
    }



    private $splitting = false;
    private $defaultModules = array();

    public function splitDefaultModules()
    {
        return $this->splitting;
    }

    public function setSplitDefaultModules($splitting)
    {
        $this->splitting = $splitting;
    }

    public function getDefaultModules()
    {
        return $this->defaultModules;
    }

    public function setDefaultModules($ids)
    {
        $this->defaultModules = $ids;
    }



    private $cache = array();

    public function loadFromCache($hash)
    {
        return isset($this->cache[$hash]) ? $this->cache[$hash] : false;
    }

    public function cacheModules($hash, $urls)
    {
        $this->cache[$hash] = $urls;
    }

    public function clearCache()
    {
        $this->cache = array();
    }
}
