<?php declare(strict_types=1);

namespace Visca\JsPackager\Packager\Report;

/**
 * Class EntryPoint
 */
class EntryPoint
{
    /** @var string */
    private $id;

    /** @var string[] */
    private $urls;

    /**
     * EntryPoint constructor.
     *
     * @param string   $id
     * @param string[] $urls
     */
    public function __construct($id, array $urls = [])
    {
        $this->id = $id;
        $this->urls = $urls;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string[]
     */
    public function getUrls(): array
    {
        return $this->urls;
    }

    public function addUrl(string $url)
    {
        $this->urls[] = $url;

        return $this;
    }
}
