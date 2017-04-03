<?php
namespace Visca\JsPackager\Compiler;

use Visca\JsPackager\Compiler\Storage\Exceptions\UnableToProvideScriptException;
use Visca\JsPackager\ConfigurationDefinition;
use Visca\JsPackager\Model\EntryPoint;

interface CompilerInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param EntryPoint              $entryPoint
     * @param ConfigurationDefinition $config
     *
     * @return string
     * @throws UnableToProvideScriptException
     */
    public function compile(EntryPoint $entryPoint, ConfigurationDefinition $config);

}