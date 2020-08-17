<?php


namespace App\Test\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GetUserTest extends WebTestCase
{
    private const HTTP_OK = 200;
    private const HTTP_NOT_FOUND = 404;

    public function testReturnsOkStatusOnValidRequest()
    {
        $client = static::createClient();
        $client->request('GET', 'users/symfony');
        $this->assertEquals(self::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testReturnsNotFoundStatusOnInvalidUsername()
    {
        $client = static::createClient();
        $client->request('GET', 'users/'.md5(uniqid()));
        $this->assertEquals(self::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }
}
