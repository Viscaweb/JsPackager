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
     * @return string
     */
    public function getName();

    /**
     * @param EntryPoint              $entryPoint
     * @param ConfigurationDefinition $config
     * @param string|null             $requiredPageName The page's javascript we want.
     *
     * @return string
     */
    public function compile(EntryPoint $entryPoint, ConfigurationDefinition $config);

    /**
     * @param EntryPoint[]            $entryPoints
     * @param ConfigurationDefinition $config
     *
     * @return array
     */
    public function compileCollection(ConfigurationDefinition $config);

    /**
     * @return PackageStats
     */
    public function getStats();
}