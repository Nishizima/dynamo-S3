<?php

namespace Auth\Action;


use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Expressive\Router\RouterInterface;
use Exception;

class AuthActionFactory
{
    public function __invoke(ContainerInterface $container,$requestedName)
    {
        return new $requestedName($container->get(AuthenticationService::class), $container->get(RouterInterface::class));
    }
}