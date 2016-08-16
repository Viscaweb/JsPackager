<?php

namespace Visca\JsPackager\Compiler;

/**
 * Class AbstractCompiler
 */
abstract class AbstractCompiler implements CompilerInterface
{
    /** @var boolean */
    protected $debug;

    /**
     * AbstractCompiler constructor.
     *
     * @param bool $debug
     */
    public function __construct($debug = false)
    {
        $this->debug = $debug;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    protected function addScriptTag($url)
    {
        return '<script src="'.$url.'"></script>';
    }
}
