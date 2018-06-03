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
use App\Filter\UserFilter;

class UserGetAction implements RequestHandlerInterface
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

        $email = $request->getAttribute('key');

        $validator = new UserFilter();
        $resp = $validator->filterUserEmail(['email' => $email]);

        if($resp !== true)
        {
            return new JsonResponse($resp,422);
        }

            $key = $marshaler->marshalJson(json_encode(array('email'=>$email)));

            $params = [
                'TableName' => $tableName,
                'Key' => $key
            ];

            try {
                $result = $dynamodb->getItem($params);
                //print_r($result['Item']['password']['S']); exit;

                if(!empty($result["Item"]))
                    return new JsonResponse($result["Item"],200);
                else
                    return new JsonResponse([],404);

            } catch (DynamoDbException $e) {
                return new JsonResponse([],404);
            }
    }

}
