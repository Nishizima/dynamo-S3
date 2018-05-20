<?php
/**
 * Created by PhpStorm.
 * User: hiro_
 * Date: 17/05/2018
 * Time: 22:33
 */

namespace App\User;

use Auth\MyAuthAdapter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router\RouterInterface;
use Firebase\JWT\JWT;

class UserLoginAction implements RequestHandlerInterface
{
    private $auth;
    private $authAdapter;
    private $router;

    public function __construct(AuthenticationService $auth, MyAuthAdapter $authAdapter,RouterInterface $router)
    {
        $this->auth = $auth;
        $this->authAdapter = $authAdapter;
        $this->router = $router;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->authenticate($request);
    }


    public function authenticate(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();

        if (empty($params['username'])) {
            return new JsonResponse([
                'error' => 'The username cannot be empty'
            ],202);
        }

        if (empty($params['password'])) {
            return new JsonResponse([
                'username' => $params['username'],
                'error'    => 'The password cannot be empty',
            ],202);
        }

        $this->authAdapter->setUsername($params['username']);
        $this->authAdapter->setPassword($params['password']);

        $result = $this->auth->authenticate();
        if (!$result->isValid()) {
            return new JsonResponse([
                'username' => $params['username'],
                'error'    => 'The credentials provided are not valid',
            ],202);
        }

        $data = $result->getIdentity();

        $token = array(
            'username' => $params['username'],
            'role' => $data['role']['S'],
            'name' => $data['name']['S']
        );

        $jwt = JWT::encode($token, getenv("JWT_SECRET_KEY"));


        return new JsonResponse(['token' =>$jwt],200);
    }



}