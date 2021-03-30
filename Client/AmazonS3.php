<?php
namespace Htmlpet\CloudStorage\Client;

use Htmlpet\CloudStorage\Api\Client\ClientInterface;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class AmazonS3 implements ClientInterface
{
    private $s3;

    private $bucketId;

    public function initialize($options) 
    {    
        $key = $options['key'];
        $secret = $options['secret'];
        $region = $options['region'];
        $bucketId = $options['bucketId'];

        $this->bucketId = $bucketId;

        $credentials = new \Aws\Credentials\Credentials($key, $secret);

        $this->s3 = new S3Client([
            'version' => 'latest',
            'region' => $region,
            'credentials' => $credentials
        ]);
    }

    public function upload($what)
    {
        $this->s3->putObject([
            'Bucket' => $this->bucketId,
            'Key' => basename($what),
            'SourceFile' => $what
        ]);
    }
}
