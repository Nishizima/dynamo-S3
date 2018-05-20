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

class UserUpdateAction implements RequestHandlerInterface
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

            if(!empty($data['password']))
            {
                $dataeav[':p'] = md5($data['password']);
                $expression .= empty($expression) ? "set password = :p" : ", password = :p";
            }
            if(!empty($data['name']))
            {
                $dataeav[':n'] = ($data['name']);
                $dataname['#N'] = "name";
                $expression .= empty($expression) ? "set #N = :n" : ", #N = :n";
            }
            if(!empty($data['role']))
            {
                $dataeav[':r'] = ($data['role']);
                $dataname['#R'] = "role";
                $expression .= empty($expression) ? "set #R = :r" : ", #R = :r";
            }

            $key = $marshaler->marshalJson(json_encode(['email' => $id]));

            $eav = $marshaler->marshalJson(json_encode($dataeav));




            $params = [
                'TableName' => $tableName,
                'Key' => $key,
                'UpdateExpression' => $expression,
                'ExpressionAttributeNames'=> $dataname,
                'ExpressionAttributeValues'=> $eav,
                'ReturnValues' => 'UPDATED_NEW'
            ];

            try {
                $result = $dynamodb->updateItem($params);
                return new JsonResponse($result['Attributes'],200);

            } catch (DynamoDbException $e) {
                return new JsonResponse([],500);
            }
        }

    }
}