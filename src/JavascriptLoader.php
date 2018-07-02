<?php

namespace Visca\JsPackager;

use Visca\JsPackager\Configuration\EntryPoint;
use Visca\JsPackager\Configuration\ConfigurationDefinition;

interface JavascriptLoader
{
    public function getPageJavascript(EntryPoint $entryPoint, ConfigurationDefinition $configuration): string;
}
