<?php

declare(strict_types=1);

/*
 * This file is part of SolidWorx Toggler project.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SolidWorx\Toggler;

interface ToggleInterface
{
    /**
     * @param array<mixed> $context
     */
    public function isActive(string $feature, array $context = []): bool;
}
