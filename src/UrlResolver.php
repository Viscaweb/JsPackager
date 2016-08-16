<?php

namespace Visca\JsPackager;

/**
 * Class UrlResolver
 */
class UrlResolver
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * UrlResolver constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function resolveUrl($url)
    {
        $pattern = '/{{ '.
            'asset\((.*)\)'.
            '|'.
            'path\((.*)\)'.
            ' }}/';
        if (preg_match_all($pattern, $url)) {
            $url = str_replace(' }}', '|raw }}', $url);
            $url = $this->twig->createTemplate($url)->render([]);
        }

        return $url;
    }
}
