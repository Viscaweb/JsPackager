<?php

namespace Visca\JsPackager\Model;

/**
 * Class Resource
 */
interface Resource
{
    /**
     * @return string
     */
    public function getContent();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return mixed
     */
    public function getUrl();
}
