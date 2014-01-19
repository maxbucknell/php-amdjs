<?php

namespace AmdJs\Adapter;

interface Adapter
{
    public function getSourceBaseDir();

    public function getBuiltBaseDir();

    public function getBuiltBaseUrl();



    public function getAliases();



    public function isConcatenationEnabled();



    public function getOutputTransformers();



    public function isCachingEnabled();



    public function splitDefaultModules();

    public function getDefaultModules();



    public function loadFromCache($hash);

    public function cacheModules($hash, $urls);

    public function clearCache();
}
