<?php
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;


final class SystemTest extends TestCase 
{
    public function testHandleAuthWorksWithGoodData() {
        $client = new Client([
            'base_uri' => 'http://rngapi.local/api/'
        ]);
        $response = $client->request('POST', 'auth', [
            'http_errors' => false,
            'form_params' => [
                'login' => 'test1',
                'password' => 'pass1'
            ]
        ]);
        $expectedResult = json_encode([
            "token" => "47a220a96bbe1ae9c314b287b57c0837"
        ]);
        $this->assertEquals($expectedResult, $response->getBody());
    }

    public function testHandleAuthFailsWithBadData() {
        $client = new Client([
            'base_uri' => 'http://rngapi.local/api/'
        ]);
        $response = $client->request('POST', 'auth', [
            'http_errors' => false,
            'form_params' => [
                'login' => 'test1bad',
                'password' => 'pass1bad'
            ]
        ]);
        $expectedResult = json_encode([
            "token" => "47a220a96bbe1ae9c314b287b57c0837"
        ]);
        $this->assertNotEquals($expectedResult, $response->getBody());
    }

    public function testHandleAuthFailsWithIncompleteData() {
        $client = new Client([
            'base_uri' => 'http://rngapi.local/api/'
        ]);
        $response = $client->request('POST', 'auth', [
            //'http_errors' => false,
            'form_params' => [
                'login' => 'test1'
            ]
        ]);
        $expectedResult = json_encode([
            "error" => "Not enough data to process this request."
        ]);
        $this->assertEquals($expectedResult, $response->getBody());
    }

    public function testHandleGenerateWorksWithGoodAuth() {
        $client = new Client([
            'base_uri' => 'http://rngapi.local/api/'
        ]);
        $client->request('POST', 'auth', [
            'http_errors' => false,
            'form_params' => [
                'login' => 'test1',
                'password' => 'pass1'
            ]
        ]);
        $response = $client->request('GET', 'generate', [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer 47a220a96bbe1ae9c314b287b57c0837',
            ]
        ]);
        $responseData = (array) json_decode($response->getBody()->getContents());
        $this->assertArrayHasKey('gid', $responseData);
        $this->assertArrayHasKey('number', $responseData);
        $this->assertNotEmpty($responseData['gid']);
        $this->assertNotEmpty($responseData['number']);
    }

    public function testHandleGenerateFailsWithBadAuth(): void
    {
        $client = new Client([
            'base_uri' => 'http://rngapi.local/api/'
        ]);
        $client->request('POST', 'auth', [
            'http_errors' => false,
            'form_params' => [
                'login' => 'test1',
                'password' => 'pass1'
            ]
        ]);
        $response = $client->request('GET', 'generate', [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer bad-bearer-code',
            ]
        ]);
        $this->assertEquals(401, $response->getStatusCode());
        $response = $client->request('GET', 'generate', [
            'http_errors' => false,
            'headers' => []
        ]);
    }

    public function testHandleRetrieveWorksWithGoodAuth() {
        $client = new Client([
            'base_uri' => 'http://rngapi.local/api/'
        ]);
        $client->request('POST', 'auth', [
            'http_errors' => false,
            'form_params' => [
                'login' => 'test1',
                'password' => 'pass1'
            ]
        ]);
        $response = $client->request('GET', 'generate', [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer 47a220a96bbe1ae9c314b287b57c0837',
            ]
        ]);
        $responseData = (array) json_decode($response->getBody()->getContents());
        $gid = $responseData['gid'];
        $value = $responseData['number'];
        $response = $client->request('GET', 'retrieve', [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer 47a220a96bbe1ae9c314b287b57c0837',
            ],
            'query' => [
                'gid' => $gid
            ]
        ]);
        $responseData = (array) json_decode($response->getBody()->getContents());
        $this->assertArrayHasKey('value', $responseData);
        $this->assertEquals($value, $responseData['value']);
    }

    public function testHandleRetrieveFailsWithBadAuth() {
        $client = new Client([
            'base_uri' => 'http://rngapi.local/api/'
        ]);
        $client->request('POST', 'auth', [
            'http_errors' => false,
            'form_params' => [
                'login' => 'test1',
                'password' => 'pass1'
            ]
        ]);
        $response = $client->request('GET', 'generate', [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer 47a220a96bbe1ae9c314b287b57c0837',
            ]
        ]);
        $responseData = (array) json_decode($response->getBody()->getContents());
        $gid = $responseData['gid'];
        $value = $responseData['number'];
        $response = $client->request('GET', 'retrieve', [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer not-a-valid-token',
            ],
            'query' => [
                'gid' => $gid
            ]
        ]);
        $this->assertEquals(401, $response->getStatusCode());
        $response = $client->request('GET', 'retrieve', [
            'http_errors' => false,
            'headers' => [],
            'query' => [
                'gid' => $gid
            ]
        ]);
        $this->assertEquals(401, $response->getStatusCode());
    }

}
