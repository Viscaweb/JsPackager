<?php
namespace Visca\JsPackager\Compiler\Webpack\Loaders;

interface WebpackLoaderInterface
{
    /**
     * Loader name.
     *
     * @return string
     */
    public function getName();

    /**
     * Return the regex expression that enables this loader.
     *
     * @return return
     */
    public function getTest();
}