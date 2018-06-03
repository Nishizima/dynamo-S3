<?php
/**
 * Created by PhpStorm.
 * User: hiro_
 * Date: 03/06/2018
 * Time: 17:13
 */
namespace App\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator\File;


class S3Filter
{
    const FILTER_FILE = [
        'name' => 'file',
        'required' => true,
        'validators' => [
            [
                'name' => 'Zend\Validator\File\Size',
                'options' => [
                    'min' => 1,
                    'max' => 200000,
                ],
            ],
            [
                'name' => 'Zend\Validator\File\Extension',
                'options' => [
                    'extension' => 'png,txt,jpg,jpeg,doc,docx,gif',
                ],
            ],
        ],
    ];

    const FILTER_NAME = [
        'name' => 'filename',
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
                    'pattern' => '/^[a-zA-z0-9_\-@#$%!]+\.[a-zA-Z]{3}$/',
                    'message' => "It is only allowed 'letters', 'numbers', '_', '-', '@', '#', '$', '%', '!'",
                ],
            ],
        ],
    ];

    public $filter;

    public function __construct()
    {
        $this->filter = new InputFilter();
    }


    public function filterFilename($data)
    {
        $this->filter->add(self::FILTER_NAME);
        $this->filter->setData($data);

        if($this->filter->isValid())
        {
            return true;
        }

        return $this->filter->getMessages();
    }


    public function filterUploadFile($data)
    {
        $file = array();
        if(is_array($data['file']['name']))
        {
            foreach ($data['file'] as $key => $each)
            {
                foreach ($each as $keye => $eache)
                {
                    $file['file'][$keye][$key] = $eache;
                    $file['file'][$keye][$key] = $eache;
                    $file['file'][$keye][$key] = $eache;
                    $file['file'][$keye][$key] = $eache;
                    $file['file'][$keye][$key] = $eache;
                }

            }
        }
        else
        {
            $file = ['file' => [$data['file']]];
        }

        $this->filter->add(self::FILTER_FILE);

        foreach ($file['file'] as $key => $each)
        {
            $this->filter->setData(['file' => $each]);
            if(!$this->filter->isValid())
            {
                return $this->filter->getMessages();
            }
        }
        return true;





    }



}