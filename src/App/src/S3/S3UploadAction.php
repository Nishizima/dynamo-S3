<?php
/**
 * Created by PhpStorm.
 * User: hiro_
 * Date: 20/05/2018
 * Time: 00:05
 */
namespace App\S3;

use Aws\Exception\MultipartUploadException;
use Aws\Sdk;
use Aws\S3\MultipartUploader;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

class S3UploadAction implements RequestHandlerInterface
{
    private $sdk;

    public function __construct(Sdk $sdk)
    {
        $this->sdk = $sdk;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        $s3Client = $this->sdk->createS3();

        $bucket = getenv("AWS_BUCKET_NAME");

        $data = $request->getUploadedFiles();


        $uploadkeys = array();

        if(!is_array($data[array_keys($data)[0]]))
        {
            $data[array_keys($data)[0]] = array($data[array_keys($data)[0]]);
        }

        foreach ($data[array_keys($data)[0]] as $key => $each)
        {
            $uploader = new MultipartUploader($s3Client, $each->getStream(), [
                'bucket' => $bucket,
                'key'    => $each->getClientFilename(),
            ]);

            try {
                $result = $uploader->upload();

                //print_r($result);
                $uploadkeys[] = array('key' => $result['Key']);
                //echo "\n\nUpload complete: {$result['ObjectURL']}\n";
            } catch (MultipartUploadException $e) {
                $params = $e->getState()->getId();
                $result = $s3Client->abortMultipartUpload($params);

                return new JsonResponse($e->getMessage(),500);
            }
        }

        return new JsonResponse($uploadkeys,200);

    }
}