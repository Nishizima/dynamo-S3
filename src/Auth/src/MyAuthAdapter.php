<?php

namespace Auth;

use Aws\DynamoDb\Marshaler;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use GuzzleHttp;

class MyAuthAdapter implements AdapterInterface
{
    private $password;
    private $username;

    private $sdk;

    public function __construct($sdk)
    {
        $this->sdk = $sdk;
    }

    public function setPassword(string $password) : void
    {
        $this->password = $password;
    }

    public function setUsername(string $username) : void
    {
        $this->username = $username;
    }

    /**
     * Performs an authentication attempt
     *
     * @return Result
     */
    public function authenticate()
    {

        $dynamodb = $this->sdk->createDynamoDb();
        $marshaler = new Marshaler();

        $tableName = 'user';

        $email = $this->username;

        $key = $marshaler->marshalJson(json_encode(array('email'=>$email)));

        $params = [
            'TableName' => $tableName,
            'Key' => $key
        ];

        try {
            $result = $dynamodb->getItem($params);

            if(!empty($result['Item']))
            {
                $row = $result['Item'];

                if(md5($this->password) == $row['password']['S'])
                {
                    return new Result(Result::SUCCESS, $row);
                }
                else
                {
                    return new Result(Result::FAILURE_CREDENTIAL_INVALID, $this->username);
                }
            }
            else
            {
                return new Result(Result::FAILURE_CREDENTIAL_INVALID, $this->username);
            }



        } catch (DynamoDbException $e) {
            return new JsonResponse([],404);
        }

    }
}