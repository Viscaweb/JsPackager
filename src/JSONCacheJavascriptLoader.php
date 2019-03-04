<?php

namespace Visca\JsPackager;

use Visca\JsPackager\Configuration\ConfigurationDefinition;
use Visca\JsPackager\Configuration\EntryPoint;

/**
 * Class MapJavascriptLoader
 * @package Visca\JsPackager
 *
 * This Javascript loader relies on the existence of app/config/page_scripts.json
 * file that is read and used as the sole argument this class requires as
 * constructor input.
 *
 * That page_scripts.json file is generated with `app/console visca:jspackager-compress`
 * console command.
 */
class JSONCacheJavascriptLoader implements JavascriptLoader
{
    /** @var array */
    private $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function getPageJavascript(EntryPoint $entryPoint, ConfigurationDefinition $configuration): string
    {
        $urls = $this->map[$configuration->getName()][$entryPoint->getName()] ?? '';

        $html = '';
        if (\is_array($urls) === false) {
            return $html;
        }

        foreach ($urls as $url) {
            $html.= '<script src="'.$url.'"></script>';
        }

        return $html;
    }
}
