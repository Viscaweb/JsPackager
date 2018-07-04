<?php

namespace Visca\JsPackager;

use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\Configuration\EntryPoint;

class MapJavascriptLoader implements JavascriptLoader
{
    /** @var array */
    private $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function getPageJavascript(EntryPoint $entryPoint, ConfigurationDefinition $configuration): string
    {
        $urls = $this->map[$configuration->getName()][$entryPoint->getName()] ?? '';

        $html = '';
        foreach ($urls as $url) {
            $html.= '<script src="'.$url.'"></script>';
        }

        return $html;
    }
}
