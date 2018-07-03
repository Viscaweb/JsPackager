<?php

namespace Visca\JsPackager\TemplateEngine;

interface TemplateEngine
{
    public function render(string $templateFile, $templateVars): string;
}
