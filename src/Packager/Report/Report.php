<?php

namespace Visca\JsPackager\Packager\Report;

interface Report
{
    public function getVersion();

    public function getTime();

    public function getErrors();

    public function getAssets(?string $key = null);

    public function getCommonAssets();
}