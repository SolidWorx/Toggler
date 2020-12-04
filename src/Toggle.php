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

    /**
     * @param ExpressionLanguage $expressionLanguage
     */
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
                $value = $this->evaluateExpression($feature, $value, $context);
                break;
            case is_callable($value):
                $value = $this->evaluateCallback($feature, $value, $context);
                break;
        }

        return $this->isTruthy($value);
    }

    /**
     * Checked if a variable has a truthy value.
     *
     * @param mixed $value
     */
    private function isTruthy($value): bool
    {
        if (is_bool($value)) {
            return true === $value;
        }

        if (is_int($value)) {
            return 1 === $value;
        }

        if (is_string($value)) {
            if ((int) $value > 0) {
                return 1 === (int) $value;
            }

            return in_array(strtolower($value), ['on', 'true'], true);
        }

        return false;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    private function evaluateExpression(string $feature, $value, array $context)
    {
        return $this->expressionLanguage->evaluate($value, $context);
    }

    /**
     * @return mixed
     */
    private function evaluateCallback(string $feature, callable $value, array $context)
    {
        return call_user_func_array($value, $context);
    }
}
