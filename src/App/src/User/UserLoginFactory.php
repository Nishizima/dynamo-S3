<?php
/**
 * Created by PhpStorm.
 * User: hiro_
 * Date: 17/05/2018
 * Time: 22:34
 */

namespace App\User;

use Auth\MyAuthAdapter;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Expressive\Router\RouterInterface;

class UserLoginFactory
{
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        return new $requestedName($container->get(AuthenticationService::class),
            $container->get(MyAuthAdapter::class),
            $container->get(RouterInterface::class)
        );
    }
}