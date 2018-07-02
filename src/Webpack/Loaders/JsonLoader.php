<?php

namespace Visca\JsPackager\Webpack\Loaders;

class JsonLoader implements WebpackLoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'json-loader';
    }

    /**
     * {@inheritdoc}
     */
    public function getTest(): string
    {
        return '/\.json$/';
    }
}
