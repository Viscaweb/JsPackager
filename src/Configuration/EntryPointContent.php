<?php

namespace Visca\JsPackager\Configuration;

/**
 * Class EntryPointContent
 */
class EntryPointContent implements EntryPointInterface
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $content;

    /**
     * EntryPointString constructor.
     *
     * @param string $name
     * @param string $content
     */
    public function __construct($name, $content)
    {
        $this->name = $name;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}
