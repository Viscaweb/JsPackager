<?php declare(strict_types=1);

namespace Visca\JsPackager\Report;

class EntryPoint
{
    /** @var string */
    private $id;

    /** @var string[] */
    private $urls;

    public function __construct(string $id, array $urls = [])
    {
        $this->id = $id;
        $this->urls = $urls;
    }

    public function getId(): string
    {
        return $this->id;
    }

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
