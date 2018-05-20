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
use PHPUnit\Util\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

class S3UrlAction implements RequestHandlerInterface
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

        $key = urldecode($request->getAttribute('key'));

        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key'    => $key
        ]);

        $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');

// Get the actual presigned-url
        $presignedUrl = (string) $request->getUri();
        return new JsonResponse($presignedUrl,200);


    }
}