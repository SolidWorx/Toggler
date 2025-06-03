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

namespace SolidWorx\Toggler\Symfony;

use SolidWorx\Toggler\Toggle as BaseToggle;
use SolidWorx\Toggler\ToggleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Contracts\Service\ResetInterface;
use function array_merge;

final class Toggle implements ToggleInterface, ResetInterface
{
    /**
     * @var array<mixed>
     */
    private static array $variables = [];

    private BaseToggle $toggle;

    private TokenStorageInterface $tokenStorage;

    private RoleHierarchyInterface $roleHierarchy;

    private RequestStack $requestStack;

    private AuthenticationTrustResolverInterface $trustResolver;

    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(
        BaseToggle $toggle,
        TokenStorageInterface $tokenStorage,
        RoleHierarchyInterface $roleHierarchy,
        RequestStack $requestStack,
        AuthenticationTrustResolverInterface $trustResolver,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->toggle = $toggle;
        $this->tokenStorage = $tokenStorage;
        $this->roleHierarchy = $roleHierarchy;
        $this->requestStack = $requestStack;
        $this->trustResolver = $trustResolver;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function isActive(string $feature, array $context = []): bool
    {
        if (self::$variables === []) {
            $token = $this->tokenStorage->getToken();

            $roles = [];

            if ($token instanceof TokenInterface) {
                $rolesArray = method_exists($token, 'getRoles') ? $token->getRoles() : $token->getRoleNames();
                $roles = method_exists($this->roleHierarchy, 'getReachableRoles')
                    ? $this->roleHierarchy->getReachableRoles($rolesArray)
                    : $this->roleHierarchy->getReachableRoleNames($rolesArray);
            }

            $request = $this->requestStack->getCurrentRequest();

            self::$variables = [
                'token' => $token,
                'request' => $request,
                'roles' => $roles,
                'session' => $request instanceof Request ? $request->getSession() : $request,
                'trust_resolver' => $this->trustResolver,
                'auth_checker' => $this->authorizationChecker,
                'user' => $token !== null ? $token->getUser() : null,
            ];
        }

        return $this->toggle->isActive($feature, array_merge(self::$variables, $context));
    }

    public function reset(): void
    {
        self::$variables = [];
    }
}
