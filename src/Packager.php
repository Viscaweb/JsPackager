<?php

namespace Visca\JsPackager;

use Visca\JsPackager\Compiler\CompilerInterface;

/**
 * Class Packager
 */
class Packager
{
    /** @var CompilerInterface */
    private $compiler;

    /**
     * Packager constructor.
     *
     * @param CompilerInterface $driver
     */
    public function __construct(CompilerInterface $driver)
    {
        $this->compiler = $driver;
    }

    /**
     * @param string                  $pageName
     * @param ConfigurationDefinition $config
     *
     * @return string
     */
    public function packageJavascript($pageName, ConfigurationDefinition $config)
    {
        return $this->compiler->compile($pageName, $config);
    }
}
