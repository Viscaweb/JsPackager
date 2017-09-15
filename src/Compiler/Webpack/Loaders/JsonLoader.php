<?php

namespace Visca\JsPackager\Compiler\Webpack\Loaders;

/**
 * Class JsonLoader
 */
class JsonLoader implements WebpackLoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'json-loader';
    }

    /**
     * {@inheritdoc}
     */
    public function getTest()
    {
        return '/\.json$/';
    }
}
