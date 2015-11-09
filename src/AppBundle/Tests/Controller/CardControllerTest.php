<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CardControllerTest extends WebTestCase
{
    public function testGetCards()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/cards');
        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue(is_array($response));
        $this->assertEquals($response['status'], 200);
        $this->assertEquals($response['message'], 'Success');

        $this->assertTrue(is_array($response['data']['items']));
        $this->assertTrue(is_int($response['data']['total']));
        $this->assertTrue(is_int($response['data']['pagination']['totalPages']));
        $this->assertTrue(is_int($response['data']['pagination']['currentPage']));
        $this->assertTrue(is_int($response['data']['pagination']['pageRange']));
    }
}
