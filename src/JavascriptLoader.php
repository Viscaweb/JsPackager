<?php

namespace Visca\JsPackager;

use Visca\JsPackager\Configuration\EntryPoint;

interface JavascriptLoader
{
    public function getPageJavascript(EntryPoint $entryPoint, ConfigurationDefinition $configuration): string;
}
