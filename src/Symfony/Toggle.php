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

namespace SolidWorx\Toggler\Symfony;

use SolidWorx\Toggler\Toggle as BaseToggle;
use SolidWorx\Toggler\ToggleInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Toggle implements ToggleInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    private static $variables;

    private $toggle;

    public function __construct(BaseToggle $toggle)
    {
        $this->toggle = $toggle;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive(string $feature, array $context = []): bool
    {
        if (!self::$variables) {
            $session = $this->container->get('session');

            $token = $this->container->get('security.token_storage')->getToken();

            $roles = [];

            if (null !== $token) {
                $roleHierarchy = $this->container->get('security.role_hierarchy');
                $roles = $roleHierarchy->getReachableRoles($token->getRoles());
            }

            $request = $this->container->get('request_stack')->getCurrentRequest();

            self::$variables = [
                'token' => $token,
                'request' => $request,
                'roles' => array_map(function (RoleInterface $role) {
                    return $role->getRole();
                }, $roles),
                'trust_resolver' => $this->container->get('security.authentication.trust_resolver'),
                'auth_checker' => $this->container->get('security.authorization_checker'),
                'container' => $this->container,
                'user' => null !== $token ? $token->getUser() : null,
            ];
        }

        return $this->toggle->isActive($feature, array_merge(self::$variables, $context));
    }
}
