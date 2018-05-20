<?php
/**
 * Created by PhpStorm.
 * User: hiro_
 * Date: 18/05/2018
 * Time: 22:26
 */
namespace App\User;

use Aws\DynamoDb\Marshaler;
use Aws\Sdk;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

class UserDeleteAction implements RequestHandlerInterface
{
    private $sdk;

    public function __construct(Sdk $sdk)
    {
        $this->sdk = $sdk;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userData = $request->getAttribute('userData');

        if($userData->role == 'Admin')
        {
            $dynamodb = $this->sdk->createDynamoDb();
            $marshaler = new Marshaler();

            $tableName = 'user';

            $id = $request->getAttribute('key');

            $body = [];
            mb_parse_str((string)$request->getBody(), $data);

            $dataeav = array();
            $dataname = array();
            $expression = "";

            if(count($data) > 1)
                throw new \Exception("Too many conditional fields");

            if(!empty($data['password']))
            {
                $dataeav[':p'] = md5($data['password']);
                $expression .= empty($expression) ? "password = :p" : ", password = :p";
            }
            if(!empty($data['name']))
            {
                $dataeav[':n'] = ($data['name']);
                $dataname['#N'] = "name";
                $expression .= empty($expression) ? "#N = :n" : ", #N = :n";
            }
            if(!empty($data['role']))
            {
                $dataeav[':r'] = ($data['role']);
                $dataname['#R'] = "role";
                $expression .= empty($expression) ? "#R = :r" : ", #R = :r";
            }

            $key = $marshaler->marshalJson(json_encode(['email' => $id]));
            if(!empty($dataeav))
                $eav = $marshaler->marshalJson(json_encode($dataeav));

            if(!empty($dataeav))
                $params = [
                    'TableName' => $tableName,
                    'Key' => $key,
                    'ConditionExpression' => $expression,
                    'ExpressionAttributeNames'=> $dataname,
                    'ExpressionAttributeValues'=> $eav
                ];
            else
                $params = [
                    'TableName' => $tableName,
                    'Key' => $key
                ];


            try {
                $result = $dynamodb->deleteItem($params);
                return new JsonResponse($result,200);

            } catch (DynamoDbException $e) {
                return new JsonResponse([],500);
            }
        }

    }
}