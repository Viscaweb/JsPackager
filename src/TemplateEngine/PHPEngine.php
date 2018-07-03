<?php

namespace Visca\JsPackager\TemplateEngine;

class PHPEngine implements TemplateEngine
{
    public function render(string $templateFile, $templateVars): string
    {
        extract($templateVars, EXTR_SKIP);
        ob_start();
        try {
            require $templateFile;
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }

        return ob_get_clean();
    }
}
