<?php

namespace  Visca\JsPackager\Compiler\Storage;

use Visca\JsPackager\Model\EntryPoint;

interface CompiledFileStorage
{
    /**
     * Fetches an entry from the cache.
     *
     * @param string EntryPoint $entryPoint
     *
     * @return mixed The cached data or FALSE, if no cache entry exists for the given id.
     */
    public function fetch(EntryPoint $entryPoint);

    /**
     * Tests if an entry exists in the cache.
     *
     * @param string EntryPoint $entryPoint
     *
     * @return boolean TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    public function contains(EntryPoint $entryPoint);

    /**
     * Puts data into the cache.
     *
     * @param EntryPoint $entryPoint
     * @param string     $javascriptContent
     *
     * @return boolean TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    public function save(EntryPoint $entryPoint, $javascriptContent);

    /**
     * Deletes all cache entries.
     *
     * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    public function clear();


}
