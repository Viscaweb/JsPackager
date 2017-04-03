<?php

namespace Visca\JsPackager\Compiler;

use Visca\JsPackager\Compiler\Storage\CompiledFileStorage;
use Visca\JsPackager\Compiler\Storage\Exceptions\UnableToProvideScriptException;
use Visca\JsPackager\ConfigurationDefinition;
use Visca\JsPackager\Model\EntryPoint;

final class Webpack implements CompilerInterface
{
    /** @var CompiledFileStorage $storage */
    protected $storage;

    /**
     * Webpack constructor.
     *
     * @param CompiledFileStorage $storage
     */
    public function __construct(CompiledFileStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'webpack';
    }

    /**
     * @param EntryPoint              $entryPoint
     * @param ConfigurationDefinition $config
     *
     * @return string
     * @throws UnableToProvideScriptException
     */
    public function compile(EntryPoint $entryPoint, ConfigurationDefinition $config)
    {
        if ($this->storage->contains($entryPoint)){
            return $this->storage->fetch($entryPoint);
        }

        throw new UnableToProvideScriptException('WebPack compiler only relies on pre-calculated cache entries.');
    }

}
