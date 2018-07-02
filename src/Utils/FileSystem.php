<?php

namespace Visca\JsPackager\Utils;

final class FileSystem
{
    /**
     * @throws \RuntimeException
     */
    public static function ensureDirExists(string $dir): void
    {
        if (is_dir($dir)) {
            return;
        }

        if (@mkdir($dir, 0777, true)) {
            return;
        }

        throw new \RuntimeException("Unable to ensure the given directory exists ($dir given).");
    }

    /**
     * @throws \RuntimeException
     */
    public static function saveContent(string $file, string $content): void
    {
        if (file_put_contents($file, $content) !== false) {
            return;
        }

        throw new \RuntimeException("Unable to write this file ($file given).");
    }

    public static function cleanDir(string $dir): void
    {
        $files = glob($dir.'/*');
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
