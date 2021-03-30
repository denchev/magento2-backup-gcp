<?php
namespace Htmlpet\CloudStorage\Client;

interface ClientInterface {
    public function initialize($projectId, $bucketId);

    public function upload($what);
}