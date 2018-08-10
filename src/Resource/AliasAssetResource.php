<?php

namespace Visca\JsPackager\Resource;

class AliasAssetResource implements AssetResource
{
    /** @var string[] */
    protected $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function getUrl(): string
    {
        return '';
    }

    public function getPath(): string
    {
        return $this->alias;
    }

    public function getContent(): string
    {
        return '';
    }

    public function prependContent(string $content)
    {
        // Ignore on purpose
    }

    public function getAliases(): array
    {
        return $this->alias;
    }
}
