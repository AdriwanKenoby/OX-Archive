<?php

namespace Archivage\Services;

use GuzzleHttp\Client;

class Guzzle {

  private $client;

  public function __construct($server_fhir_uri) {
    $this->client = new Client([
      // Base URI is used with relative requests
      'base_uri' => $server_fhir_uri,
    ]);
  }

  public function getClient() {
    return $this->client;
  }
}
