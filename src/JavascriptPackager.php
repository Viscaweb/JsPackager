<?php

namespace Visca\JsPackager;

use Visca\JsPackager\Configuration\ConfigurationDefinition;

interface JavascriptPackager
{
    public function package(ConfigurationDefinition $configuration);
}
