<?php
namespace Htmlpet\CloudStorage\Api\Client;

interface ClientInterface {
    public function initialize($projectId, $bucketId);

    public function upload($what);
}