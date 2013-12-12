<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZfrRbac\Permissions\Rbac;

use RecursiveIteratorIterator;

/**
 * Rbac object. It is used to check a permission against a role
 */
class Rbac
{
    /**
     * Determines if access is granted by checking the role and child roles for permission.
     *
     * @param  RoleInterface                    $role
     * @param  PermissionInterface|string       $permission
     * @param  AssertionInterface|Callable|null $assert
     * @throws Exception\InvalidArgumentException
     * @return bool
     */
    public function isGranted(RoleInterface $role, $permission, $assert = null)
    {
        if ($assert) {
            if ($assert instanceof AssertionInterface) {
                if (!$assert->assert($this)) {
                    return false;
                }
            } elseif (is_callable($assert)) {
                if (!$assert($this)) {
                    return false;
                }
            } else {
                throw new Exception\InvalidArgumentException(
                    'Assertions must be a callable or an instance of Zend\Permissions\Rbac\AssertionInterface'
                );
            }
        }

        $permission = (string) $permission;

        // First check directly the role
        if ($role->hasPermission($permission)) {
            return true;
        }

        // Otherwise, we recursively check each children
        $iteratorIterator = new RecursiveIteratorIterator($role, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iteratorIterator as $child) {
            /** @var RoleInterface $child */
            if ($child->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }
}
