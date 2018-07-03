<?php

namespace Visca\JsPackager\TemplateEngine;

class MustacheEngine implements TemplateEngine
{
    /** @var \Mustache_Engine */
    private $mustache;

    public function __construct(\Mustache_Engine $mustache)
    {
        $this->mustache = $mustache;
    }

    public function render(string $templateFile, $templateVars): string
    {
        if (!file_exists($templateFile)) {
            throw new \InvalidArgumentException('Can\'t find template file '.$templateFile);
        }

        $fileContents = file_get_contents($templateFile);

        return $this->mustache->render($fileContents, $templateVars);
    }
}
