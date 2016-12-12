<?php

namespace Visca\JsPackager\Compiler;

use Visca\JsPackager\ConfigurationDefinition;

/**
 * Class AbstractCompiler
 */
abstract class AbstractCompiler implements CompilerInterface
{
    /** @var boolean */
    protected $debug = false;

    /** @var int */
    protected $domainIterator;

    public function __construct()
    {
        $this->domainIterator = 0;
    }

    /**
     * @return boolean
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param boolean $debug
     *
     * @return AbstractCompiler
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;

        return $this;
    }


    /**
     * @param string $url
     *
     * @return string
     */
    protected function addScriptTag($url, ConfigurationDefinition $config)
    {
        $tag = '<script src="'.
            $this->filterUrl($url, $config).
            '"></script>';

        return $tag;
    }

    /**
     * @param                         $url
     * @param ConfigurationDefinition $config
     */
    protected function filterUrl($url, ConfigurationDefinition $config)
    {
        if ($config->getCurrentEnvironment() == $config->getDomainInjectionEnvironment()) {
            $domains = $config->getDomainsInjection();
            $domainsCount = count($domains);
            if ($domainsCount > 0) {
                $url = rtrim($domains[$this->domainIterator], '/').'/'.ltrim($url, '/');

                $this->domainIterator = $this->domainIterator < ($domainsCount - 1)
                    ? $this->domainIterator + 1
                    : 0;
            }
        }

        return $url;
    }
}
