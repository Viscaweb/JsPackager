<?php

namespace Visca\JsPackager;

use Visca\JsPackager\Configuration\ConfigurationDefinition;

interface JavascriptPackager
{
    public function getName(): string;

    public function package(ConfigurationDefinition $configuration);
}
