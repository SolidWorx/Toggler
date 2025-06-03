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

use SolidWorx\Toggler\Storage\StorageInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use function class_exists;
use function is_callable;
use function is_object;
use function method_exists;

final class Toggle implements ToggleInterface
{
    private StorageInterface $config;

    private ?ExpressionLanguage $expressionLanguage = null;

    public function __construct(StorageInterface $config, ?ExpressionLanguage $expressionLanguage = null)
    {
        $this->config = $config;

        if (class_exists(ExpressionLanguage::class)) {
            $this->expressionLanguage = $expressionLanguage ?? new ExpressionLanguage();
        }
    }

    public function isActive(string $feature, array $context = []): bool
    {
        $value = $this->config->get($feature);

        switch (true) {
            case $value instanceof Expression:
                $value = $this->evaluateExpression($value, $context);
                break;
            case is_callable($value):
                $value = $this->evaluateCallback($value, $context);
                break;
            case is_object($value) && method_exists($value, '__toString'):
                $value = (string) $value;
                break;
        }

        return Util::isTruthy($value);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function evaluateExpression(Expression | string $value, array $context): mixed
    {
        return $this->expressionLanguage?->evaluate($value, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function evaluateCallback(callable $value, array $context): mixed
    {
        return $value(...$context);
    }
}
