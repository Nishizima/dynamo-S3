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

use App\Filter\UserFilter;

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

        $validator = new UserFilter();
        $resp = $validator->filterUserLogin(['username' => $params['username'], 'password' => $params['password']]);

        if($resp !== true)
        {
            return new JsonResponse($resp,422);
        }

        $this->authAdapter->setUsername($params['username']);
        $this->authAdapter->setPassword($params['password']);

        $result = $this->auth->authenticate();
        if (!$result->isValid()) {
            return new JsonResponse([],404);
        }

        $data = $result->getIdentity();

        $token = array(
            'username' => $params['username'],
            'role' => $data['role']['S'],
            'name' => $data['name']['S']
        );

        $jwt = JWT::encode($token, getenv("JWT_SECRET_KEY"));


        return new JsonResponse(['token' =>$jwt],201);
    }



}