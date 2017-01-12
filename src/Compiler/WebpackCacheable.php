<?php

namespace Visca\JsPackager\Compiler;

use Visca\JsPackager\ConfigurationDefinition;
use Visca\JsPackager\Model\EntryPoint;
use Doctrine\Common\Cache\Cache;

/**
 * Class WebpackCacheable
 */
class WebpackCacheable implements CompilerInterface
{
    /** @var Webpack */
    private $webpack;

    /** @var Cache */
    private $cache;

    /**
     * WebpackCacheable constructor.
     *
     * @param Webpack $webpack
     */
    public function __construct(Webpack $webpack, Cache $cache)
    {
        $this->webpack = $webpack;
        $this->cache = $cache;
    }


    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->webpack->getName();
    }

    /**
     * @inheritDoc
     */
    public function compile(EntryPoint $entryPoint, ConfigurationDefinition $config)
    {
        $cacheKey = $this->getCacheKey([$entryPoint]);
        if ($this->cache->contains($cacheKey)) {
            return $this->cache->fetch($cacheKey);
        }

        throw new \RuntimeException('WebpackCacheable couldn\'t find cache entry for "'.$cacheKey.'"');
    }

    public function compileCollection(ConfigurationDefinition $config)
    {
        $output = $this->webpack->compileCollection($config);

        // Build cache
        $entryPoints = $config->getEntryPoints();
        foreach ($entryPoints as $entryPoint) {
            $key = $entryPoint->getName();
            $cacheKey = $this->getCacheKey([$entryPoint]);

            $html = isset($output[$key]) ? $output[$key] : '<!-- NOT FOUND -->';

            $result = $this->cache->save($cacheKey, $html);
        }

        return $output;
    }


    /**
     * @param EntryPoint[] $entryPoints
     *
     * @return string
     */
    private function getCacheKey($entryPoints)
    {
        $names = array_map(
            function (EntryPoint $entryPoint) {
                return $entryPoint->getName();
            },
            $entryPoints
        );

        return $this->webpack->getName().'.tags.'.implode('-', $names);
    }

    /**
     * @inheritDoc
     */
    public function getStats()
    {
        return $this->webpack->getStats();
    }
}
