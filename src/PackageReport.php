<?php

namespace Visca\JsPackager;

interface PackageReport
{
    public function getVersion();

    public function getTime();

    public function getErrors();

    public function getAssets(?string $key = null);

    public function getCommonAssets();
}
