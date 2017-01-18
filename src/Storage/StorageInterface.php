<?php

declare(strict_types=1);

/*
 * This file is part of the toggler project.
 *
 * @author    pierre
 * @copyright Copyright (c) 2015
 */

namespace SolidWorx\Toggler\Storage;

interface StorageInterface
{
    /**
     * Reads a key from the storage
     *
     * @param string $key
     *
     * @return bool
     */
    public function get(string $key): bool;
}