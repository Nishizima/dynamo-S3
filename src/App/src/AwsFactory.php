<?php
/**
 * Created by PhpStorm.
 * User: hiro_
 * Date: 17/05/2018
 * Time: 21:24
 */

namespace App;


use Interop\Container\ContainerInterface;
use Aws\Sdk;
use Aws\Credentials\CredentialProvider;

class AwsFactory
{
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        $sdk = new Sdk([
            //'endpoint'   => 'http://localhost:8000',
            'region'   => 'sa-east-1',
            'version'  => 'latest',
            'credentials' => CredentialProvider::env()
        ]);

        return new $requestedName($sdk);
    }

}