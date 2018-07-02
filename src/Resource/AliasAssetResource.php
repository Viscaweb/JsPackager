<?php

namespace Visca\JsPackager\Resource;

/**
 * Class AliasAssetResource
 */
class AliasAssetResource implements AssetResource
{
    /** @var string[] */
    protected $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        // TODO: Implement getUrl() method.
    }

    public function getPath()
    {
        return json_encode($this->alias);
    }

    public function getContent()
    {
        // TODO: Implement getContent() method.
    }
}
