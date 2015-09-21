<?php

/*
 * This file is part of the toggler project.
 *
 * @author    pierre
 * @copyright Copyright (c) 2015
 */

namespace Toggler\Storage;

interface StorageInterface
{
    /**
     * Reads a key from the storage
     *
     * @param string $key
     *
     * @return bool
     */
    public function get($key);
}