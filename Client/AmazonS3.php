<?php
namespace Htmlpet\CloudStorage\Client;

use Htmlpet\CloudStorage\Api\Client\ClientInterface;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class AmazonS3 implements ClientInterface
{
    private $s3;

    public function initialize($projectId, $bucketId) {
        $key = 'AKIA3A2O4ETLUY4EOGEG';
        $secret = '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';

        $credentials = new \Aws\Credentials\Credentials($key, $secret);

        $this->s3 = new S3Client([
            'version' => 'latest',
            'region' => 'eu-west-1',
            'credentials' => $credentials
        ]);
    }

    public function upload($what) {

        $this->s3->putObject([
            'Bucket' => 'mdenchevimages',
            'Key' => basename($what),
            'SourceFile' => $what
        ]);
    }
}