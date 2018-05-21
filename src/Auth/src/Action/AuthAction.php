<?php

namespace Auth\Action;


use PHPUnit\Util\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Firebase\JWT\JWT;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Stdlib\Response;

class AuthAction implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeader("token")[0];
        try {
            //JWT::$leeway = 5;
            if(empty($token))
                throw new \Exception('An access token is required');

            $decoded = JWT::decode($token, getenv("JWT_SECRET_KEY"), array('HS256'));

            return $handler->handle($request->withAttribute('userData',$decoded));

        }catch(\Exception $e)
        {
            return new JsonResponse($e->getMessage(),500);
        }
    }

}