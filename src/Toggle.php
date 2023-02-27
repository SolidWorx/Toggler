<?php

declare(strict_types=1);

/*
 * This file is part of the Toggler package.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SolidWorx\Toggler;

use function class_exists;
use function is_callable;
use function is_object;
use function method_exists;
use SolidWorx\Toggler\Storage\StorageInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class Toggle implements ToggleInterface
{
    /**
     * @var StorageInterface
     */
    private $config;

    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    public function __construct(StorageInterface $config, ExpressionLanguage $expressionLanguage = null)
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
     * @param string|Expression $value
     * @param array<string, mixed> $context
     *
     * @return mixed
     */
    private function evaluateExpression($value, array $context)
    {
        return $this->expressionLanguage->evaluate($value, $context);
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return mixed
     */
    private function evaluateCallback(callable $value, array $context)
    {
        return $value(...$context);
    }
}
