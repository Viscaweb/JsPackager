<?php

namespace Visca\JsPackager\Webpack\Loaders;

interface WebpackLoaderInterface
{
    /**
     * Loader name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Return the regex expression that enables this loader.
     *
     * @return string
     */
    public function getTest(): string;
}