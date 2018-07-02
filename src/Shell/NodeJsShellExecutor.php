<?php

namespace Visca\JsPackager\Shell;

final class NodeJsShellExecutor
{
    /** @var string */
    private $nodeBinary;

    /** @var string */
    private $nodePath;

    /**
     * NodeJs constructor.
     *
     * @param string $nodeBinary Path to node binary
     * @param string $nodePath   value of NODE_PATH so node finds requested modules
     */
    public function __construct($nodeBinary, $nodePath)
    {
        $this->nodeBinary = $nodeBinary;
        $this->nodePath = $nodePath;
    }

    /**
     * @param string $arguments
     * @param        $workingPath
     *
     * @return string
     */
    public function run($arguments, $workingPath)
    {
        $command = [];
        $command[] = 'cd '.escapeshellarg(realpath($workingPath));
        $command[] = 'export NODE_PATH=' . escapeshellarg($this->nodePath);
        $command[] = $this->nodeBinary . ' ' . $arguments;

        $inlineCommand = implode(' && ', $command);

        return shell_exec($inlineCommand);
    }

}
