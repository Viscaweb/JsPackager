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
        return $this->map[$entryPoint->getName()] ?? '';
    }
}
