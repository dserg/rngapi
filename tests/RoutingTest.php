<?php
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;


final class RoutingTest extends TestCase 
{
    public function testAuthIsAccessible(): void
    {
        $client = new Client([
            'base_uri' => 'http://rngapi.local/api/'
        ]);
        $response = $client->request('GET', 'auth', ['http_errors' => false]);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGenerateIsForbidden(): void
    {
        $client = new Client([
            'base_uri' => 'http://rngapi.local/api/'
        ]);
        $response = $client->request('GET', 'generate', ['http_errors' => false]);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testRetrieveIsForbidden(): void
    {
        $client = new Client([
            'base_uri' => 'http://rngapi.local/api/'
        ]);
        $response = $client->request('GET', 'retrieve', ['http_errors' => false]);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testLongPathIsForbidden(): void
    {
        $client = new Client([
            'base_uri' => 'http://rngapi.local/api/very/bad/path/'
        ]);
        $response = $client->request('GET', 'auth', ['http_errors' => false]);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testBadPathIsForbidden(): void
    {
        $client = new Client([
            'base_uri' => 'http://rngapi.local/not-api/'
        ]);
        $response = $client->request('GET', 'auth', ['http_errors' => false]);
        $this->assertEquals(404, $response->getStatusCode());
    }
}
