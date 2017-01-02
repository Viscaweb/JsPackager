<?php

namespace Visca\JsPackager\Model;

/**
 * Class AliasResource
 */
class AliasResource implements Resource
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
