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

use function array_merge;
use SolidWorx\Toggler\Toggle as BaseToggle;
use SolidWorx\Toggler\ToggleInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

final class Toggle implements ToggleInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var array<mixed>
     */
    private static $variables = [];

    /**
     * @var BaseToggle
     */
    private $toggle;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var RoleHierarchyInterface
     */
    private $roleHierarchy;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var AuthenticationTrustResolverInterface
     */
    private $trustResolver;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(
        BaseToggle $toggle,
        SessionInterface $session,
        TokenStorageInterface $tokenStorage,
        RoleHierarchyInterface $roleHierarchy,
        RequestStack $requestStack,
        AuthenticationTrustResolverInterface $trustResolver,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->toggle = $toggle;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
        $this->roleHierarchy = $roleHierarchy;
        $this->requestStack = $requestStack;
        $this->trustResolver = $trustResolver;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function isActive(string $feature, array $context = []): bool
    {
        if ([] === self::$variables) {
            $token = $this->tokenStorage->getToken();

            $roles = [];

            if ($token instanceof TokenInterface) {
                $rolesArray = method_exists($token, 'getRoles') ? $token->getRoles() : $token->getRoleNames();
                $roles = method_exists($this->roleHierarchy, 'getReachableRoles')
                    ? $this->roleHierarchy->getReachableRoles($rolesArray)
                    : $this->roleHierarchy->getReachableRoleNames($rolesArray);
            }

            self::$variables = [
                'token' => $token,
                'request' => $this->requestStack->getCurrentRequest(),
                'roles' => $roles,
                'session' => $this->session,
                'trust_resolver' => $this->trustResolver,
                'auth_checker' => $this->authorizationChecker,
                'user' => null !== $token ? $token->getUser() : null,
            ];
        }

        return $this->toggle->isActive($feature, array_merge(self::$variables, $context));
    }
}
