<?php

namespace Visca\JsPackager\Compiler\Url;

use Visca\JsPackager\ConfigurationDefinition;
use Doctrine\Common\Cache;
use AppBundle\Cache\MemcachedCache;

/**
 * Class UrlProcessor
 */
class UrlProcessor
{
    /** @var int */
    protected $domainIterator;

    /** @var MemcachedCache */
    protected $cacheStorage;

    /** @var string */
    protected $publicPath;

    /**
     * UrlProcessor constructor.
     *
     * @param MemcachedCache $cacheStorage
     * @param string         $rootPath
     */
    public function __construct(MemcachedCache $cacheStorage, $rootPath)
    {
        $this->cacheStorage = $cacheStorage;
        $this->publicPath = dirname($rootPath).'/web';
        $this->domainIterator = 0;
    }

    /**
     * Modifies a url with a serie of filters.
     *
     * @param string                  $url
     * @param ConfigurationDefinition $config
     *
     * @return string
     */
    public function processUrl($url, ConfigurationDefinition $config)
    {
        $url = $this->injectCacheBusting($url, $config);
        $url = $this->injectDomain($url, $config);

        return $url;
    }

    /**
     * This class will adds (only when required, for example when having many CDNs subdomains to preload assets)
     * domains before the real assets URL.
     *
     * @param string                  $url
     * @param ConfigurationDefinition $config
     *
     * @return string
     */
    protected function injectDomain($url, ConfigurationDefinition $config)
    {
        if ($config->getCurrentEnvironment() == $config->getDomainInjectionEnvironment()) {
            $domains = $config->getDomainsInjection();
            $domainsCount = count($domains);
            if ($domainsCount > 0) {
                $url = rtrim($domains[$this->domainIterator], '/').'/'.ltrim($url, '/');

                $this->domainIterator = $this->domainIterator < ($domainsCount - 1) ? $this->domainIterator + 1 : 0;
            }
        }

        return $url;
    }

    /**
     * @param                         $url
     * @param ConfigurationDefinition $config
     *
     * @return string
     * @throws \RuntimeException
     */
    protected function injectCacheBusting($url, ConfigurationDefinition $config)
    {
        $fileNameHash = md5($url);
        $cacheKey = 'js.modified.time.' . $fileNameHash;
        $modifiedTime = $this->cacheStorage->fetch($cacheKey);

        if (!$modifiedTime) {
            $path = $this->publicPath . '/' . ltrim($url, '/');
            if (file_exists($path . '.js')) {
                $modifiedTime = filemtime($path . '.js');
                $this->cacheStorage->save($cacheKey, $modifiedTime);
            } elseif (is_dir($path)) {
                throw new \RuntimeException("Cannot add cache busting to alias pointing to folders ($url).");
            } else {
                // Nothing found...
            }
        }

        if ($modifiedTime !== null) {
            $queryGlue = (strpos($url, '?') === false) ? '?' : '&';

            $extension = strpos($url, '.js');
            $extension = ($extension == (strlen($url) - 3)) ? '' : '.js';
            $url = $url . $extension . $queryGlue . 'v=' . md5($modifiedTime);
        }

        return $url;
    }
}
