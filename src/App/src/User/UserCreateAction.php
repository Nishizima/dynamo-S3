<?php
/**
 * Created by PhpStorm.
 * User: hiro_
 * Date: 18/05/2018
 * Time: 21:53
 */

namespace App\User;

use App\Filter\UserFilter;
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



            $validator = new UserFilter();
            $resp = $validator->filterUserCreate(['email' => $data['email'],
                                                  'password' => $data['password'],
                                                  'name' => $data['name'],
                                                  'role' => $data['role']]);

            if($resp !== true)
            {
                return new JsonResponse($resp,422);
            }

            if(!empty($data['password']))
            {
                $password = $data['password'];
                $data['password'] = md5($data['password']);
            }

            $key = $marshaler->marshalJson(json_encode($data));

            $data['password'] = $password;


            $params = [
                'TableName' => $tableName,
                'Item' => $key,
                'ReturnValues' => 'ALL_OLD'
            ];

            try {
                $result = $dynamodb->putItem($params);
                if($result['@metadata']['statusCode'] === 200)
                    return new JsonResponse($data,201);
                else
                    return new JsonResponse([],404);

            } catch (DynamoDbException $e) {
                return new JsonResponse([],404);
            }
        }

        return new JsonResponse([],404);


    }
}