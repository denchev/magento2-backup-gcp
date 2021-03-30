<?php
namespace Htmlpet\CloudStorage\Client;

use Google\Cloud\Storage\StorageClient;

class GoogleCloudPlatform implements ClientInterface
{
    private $bucket;

    private $directoryList;

    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList
    ) {
        $this->directoryList = $directoryList;
    }

    public function initialize($projectId, $bucketId) {
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