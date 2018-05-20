<?php
/**
 * Created by PhpStorm.
 * User: hiro_
 * Date: 18/05/2018
 * Time: 21:53
 */

namespace App\User;

use Aws\DynamoDb\Marshaler;
use Aws\Sdk;
use PHPUnit\Util\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

class UserCreateAction implements RequestHandlerInterface
{
    private $sdk;

    public function __construct(Sdk $sdk)
    {
        $this->sdk = $sdk;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userData = $request->getAttribute('userData');

        if($userData->role == "Admin")
        {
            $dynamodb = $this->sdk->createDynamoDb();
            $marshaler = new Marshaler();

            $tableName = 'user';
            $data = $request->getParsedBody();

            if(empty($data['email']))
                throw new \Exception("email is required");
            if(empty($data['password']))
                throw new \Exception("password is required");
            else
                $data['password'] = md5($data['password']);
            if(empty($data['name']))
                throw new \Exception("name is required");
            if(empty($data['role']))
                throw new \Exception("role is required");

            $key = $marshaler->marshalJson(json_encode($data));

            $params = [
                'TableName' => $tableName,
                'Item' => $key
            ];

            try {
                $result = $dynamodb->putItem($params);

                return new JsonResponse($result,200); exit;

            } catch (DynamoDbException $e) {
                return new JsonResponse([],500);
            }
        }

        return new JsonResponse([],200);


    }
}