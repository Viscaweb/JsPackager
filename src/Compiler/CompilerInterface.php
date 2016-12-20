<?php
namespace Visca\JsPackager\Compiler;

use Visca\JsPackager\ConfigurationDefinition;
use Visca\JsPackager\Model\EntryPoint;

/**
 * Interface CompilerInterface
 * @package Visca\JS\Manager
 */
interface CompilerInterface
{
    /**
     * @param EntryPoint|EntryPoint[] $entryPoints
     * @param ConfigurationDefinition $config
     *
     * @return string
     */
    public function compile($entryPoints, ConfigurationDefinition $config);
}