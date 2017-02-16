<?php

namespace Visca\JsPackager\Compiler;

use Visca\JsPackager\Compiler\Url\UrlProcessor;
use Visca\JsPackager\ConfigurationDefinition;

/**
 * Class AbstractCompiler
 */
abstract class AbstractCompiler implements CompilerInterface
{
    /** @var boolean */
    protected $debug;

    /** @var UrlProcessor */
    protected $urlProcessor;

    /**
     * AbstractCompiler constructor.
     *
     * @param UrlProcessor $urlProcessor
     * @param bool         $debug
     */
    public function __construct(UrlProcessor $urlProcessor = null, $debug = false)
    {
        $this->urlProcessor = $urlProcessor;
        $this->debug = $debug;
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
     * @param string                       $url
     * @param ConfigurationDefinition|null $config
     *
     * @return string
     */
    protected function addScriptTag($url, ConfigurationDefinition $config = null)
    {
        if(!is_null($config)){
            $url = $this->urlProcessor->processUrl($url, $config);
        }

        $tag = '<script src="'.$url.'"></script>';

        return $tag;
    }
}
