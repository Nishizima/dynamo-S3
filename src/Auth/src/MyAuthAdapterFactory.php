<?php
namespace Auth;

use Aws\Credentials\CredentialProvider;
use Aws\Sdk;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;

class MyAuthAdapterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $sdk = new Sdk([
            //'endpoint'   => 'http://localhost:8000',
            'region'   => 'sa-east-1',
            'version'  => 'latest',
            'credentials' => CredentialProvider::env()
        ]);

        // Retrieve any dependencies from the container when creating the instance
        return new MyAuthAdapter($sdk);
    }
}