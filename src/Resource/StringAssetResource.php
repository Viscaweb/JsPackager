<?php

namespace Visca\JsPackager\Resource;

class StringAssetResource implements AssetResource
{
    /**
     * @var string
     */
    protected $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return 'string://memory';
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        return 'string://memory';
    }
}
