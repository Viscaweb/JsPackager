<?php

namespace Visca\JsPackager;

use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\Report\PackageReport;

interface JavascriptPackager
{
    public function getName(): string;

    public function package(ConfigurationDefinition $configuration): PackageReport;
}
