<?php

namespace Visca\JsPackager\Model;

/**
 * Class StringResource
 */
class StringResource implements Resource
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
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function getPath()
    {
        // TODO: Implement getPath() method.
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        // TODO: Implement getUrl() method.
    }
}
