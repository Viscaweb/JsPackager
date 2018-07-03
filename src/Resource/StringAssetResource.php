<?php

namespace Visca\JsPackager\Resource;

class StringAssetResource implements AssetResource
{
    /** @var string */
    protected $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getPath(): string
    {
        return '';
    }

    public function getUrl(): string
    {
        return '';
    }
}
