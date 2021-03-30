<?php
namespace Htmlpet\CloudStorage\Client;

use Google\Cloud\Storage\StorageClient;
use Htmlpet\CloudStorage\Api\Client\ClientInterface;

class GoogleCloudPlatform implements ClientInterface
{
    private $bucket;

    private $directoryList;

    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList
    ) {
        $this->directoryList = $directoryList;
    }

    public function initialize($options) {
        $projectId = $options['projectId'];
        $bucketId = $options['bucketId'];

        $keyFilePath = $this->directoryList->getPath('var') . '/google-cloud-keys.json';
        $storage = new StorageClient([
            'keyFilePath' => $keyFilePath,
            'projectId' => $projectId
        ]);
        $bucket = $storage->bucket($bucketId);

        $this->bucket = $bucket;
    }

    public function upload($what) {
        $this->bucket->upload(
            fopen($what, 'r')
        );
    }
}
