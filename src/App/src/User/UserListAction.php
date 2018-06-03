<?php
/**
 * Created by PhpStorm.
 * User: hiro_
 * Date: 17/05/2018
 * Time: 22:08
 */
namespace App\User;

use Aws\DynamoDb\Marshaler;
use Aws\Sdk;
use PHPUnit\Util\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use GuzzleHttp;
use Zend\Diactoros\Response\JsonResponse;

class UserListAction implements RequestHandlerInterface
{
    private $sdk;
    public function __construct(Sdk $sdk)
    {
        $this->sdk = $sdk;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        $dynamodb = $this->sdk->createDynamoDb();
        $marshaler = new Marshaler();

        $tableName = 'user';

            $params = [
                'TableName' => $tableName,
                'ProjectionExpression' => '#N, email, #R',
                'ExpressionAttributeNames'=> [ '#N' => 'name', '#R' =>'role' ],
            ];

            try {
                $result = $dynamodb->scan($params);

                $resp = array();
                foreach ($result['Items'] as $user) {

                    $resp[] = array('name' => $marshaler->unmarshalValue((!empty($user['name']) ? $user['name'] : array('S' => ''))),
                                    'email' => $marshaler->unmarshalValue((!empty($user['email']) ? $user['email'] : array('S' => ''))),
                                    'role' => $marshaler->unmarshalValue((!empty($user['role']) ? $user['role'] : array('S' => ''))));
                }

                return new JsonResponse($resp,200);

            } catch (DynamoDbException $e) {
                return new JsonResponse([],404);
            }
    }

}
