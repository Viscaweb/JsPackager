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
    protected $debug = false;

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
    protected function addScriptTag($url)
    {
        $tag = '<script src="'.$url.'"></script>';

        return $tag;
    }
}
