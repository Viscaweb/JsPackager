<?php

namespace Visca\JsPackager;

use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\Report\BundleReport;

interface JavascriptBundler
{
    public function getName(): string;

    public function bundle(ConfigurationDefinition $configuration): BundleReport;
}
