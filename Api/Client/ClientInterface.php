<?php
namespace Htmlpet\CloudStorage\Api\Client;

interface ClientInterface {
    public function initialize($options);

    public function upload($what);
}