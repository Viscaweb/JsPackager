<?php
namespace Visca\JsPackager\Compiler;

use Visca\JsPackager\ConfigurationDefinition;

/**
 * Interface CompilerInterface
 * @package Visca\JS\Manager
 */
interface CompilerInterface
{
    /**
     * @param string $pageName
     * @param ConfigurationDefinition $config
     *
     * @return string
     */
    public function compile($pageName, ConfigurationDefinition $config);
}