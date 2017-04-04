<?php

namespace Visca\JsPackager\Utils;

final class FileSystem
{
    /**
     * @param $dir
     *
     * @return bool
     */
    public static function ensureDirExists($dir){
        if (is_dir($dir)){
            return true;
        }

        return mkdir($dir, 0777, true);
    }

}
