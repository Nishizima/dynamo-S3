<?php
/**
 * Created by PhpStorm.
 * User: hiro_
 * Date: 03/06/2018
 * Time: 15:14
 */
namespace App\Filter;

use Zend\InputFilter;

class UserFilter
{
    const FILTER_NAME = [
        'name' => 'name',
        'required' => true,
        'validators' => [
            [
                'name' => 'StringLength',
                'options' => [
                    'min' => 5,
                    'max' => 255,
                ],
            ],
            [
                'name' => 'Regex',
                'options' => [
                    'pattern' => '/^[a-zA-z]+(\s[a-zA-Z]+)*$/',
                    'message' => "It is only allowed 'letters' and ' '",
                ],
            ],
        ],
    ];

    const FILTER_EMAIL = [
        'name' => 'email',
        'required' => true,
        'validators' => [
            [
                'name' => 'Regex',
                'options' => [
                    'pattern' => "/^[a-zA-Z0-9_\-\.\+\^!#\$%&*+\/\=\?\`\|\{\}~\']+@((?:(?:[a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.?)+|(\[([0-9]{1,3}(\.[0-9]{1,3}){3}|[0-9a-fA-F]{1,4}(\:[0-9a-fA-F]{1,4}){7})\]))$/",
                    'message' => "It is only allowed 'letters', 'numbers', '_', '.', '-', '@'",
                ],
            ],
        ],
    ];

    const FILTER_USERNAME = [
        'name' => 'username',
        'required' => true,
        'validators' => [
            [
                'name' => 'Regex',
                'options' => [
                    'pattern' => "/^[a-zA-Z0-9_\-\.\+\^!#\$%&*+\/\=\?\`\|\{\}~\']+@((?:(?:[a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.?)+|(\[([0-9]{1,3}(\.[0-9]{1,3}){3}|[0-9a-fA-F]{1,4}(\:[0-9a-fA-F]{1,4}){7})\]))$/",
                    'message' => "It is only allowed 'letters', 'numbers', '_', '.', '-', '@'",
                ],
            ],
        ],
    ];

    const FILTER_PASSWORD = [
        'name' => 'password',
        'required' => true,
        'validators' => [
            [
                'name' => 'StringLength',
                'options' => [
                    'min' => 5,
                    'max' => 25,
                ],
            ],
            [
                'name' => 'Regex',
                'options' => [
                    'pattern' => "/^[a-zA-z0-9.!@#$%^&*;:_-]+$/",
                    'message' => "It is only allowed 'letters', 'numbers', '.', '!', '@', '#', '$', '%', '^', '&', '*', ';', ':', '_', '-'",
                ],
            ],
        ],
    ];

    const FILTER_ROLE = [
        'name' => 'role',
        'required' => true,
        'validators' => [
            [
                'name' => 'Regex',
                'options' => [
                    'pattern' => "/^[a-zA-z0-9.&_-]+$/",
                    'message' => "It is only allowed 'letters', 'numbers', '.', '&', '_', '-'",
                ],
            ],
        ],
    ];

    public $filter;

    public function __construct()
    {
        $this->filter = new InputFilter\InputFilter();
    }

    public function filterUserEmail($data)
    {
        $this->filter->add(self::FILTER_EMAIL);
        $this->filter->setData($data);

        if($this->filter->isValid())
        {
            return true;
        }

        return $this->filter->getMessages();
    }

    public function filterUserLogin($data)
    {
        $this->filter->add(self::FILTER_USERNAME);
        $this->filter->add(self::FILTER_PASSWORD);
        $this->filter->setData($data);

        if($this->filter->isValid())
        {
            return true;
        }

        return $this->filter->getMessages();
    }

    public function filterUserCreate($data)
    {
        $this->filter->add(self::FILTER_NAME);
        $this->filter->add(self::FILTER_EMAIL);
        $this->filter->add(self::FILTER_ROLE);
        $this->filter->add(self::FILTER_PASSWORD);
        $this->filter->setData($data);

        if($this->filter->isValid())
        {
            return true;
        }

        return $this->filter->getMessages();
    }
    public function filterUserUpdate($data)
    {
        if(!empty($data['name']))
            $this->filter->add(self::FILTER_NAME);
        if(!empty($data['role']))
            $this->filter->add(self::FILTER_ROLE);
        if(!empty($data['password']))
            $this->filter->add(self::FILTER_PASSWORD);

        $this->filter->setData($data);

        if($this->filter->isValid())
        {
            return true;
        }

        return $this->filter->getMessages();
    }
}
