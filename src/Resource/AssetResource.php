<?php

namespace Visca\JsPackager\Resource;

interface AssetResource
{
    public function getContent(): string;

    public function getPath(): string;

    public function getUrl(): string;
}
