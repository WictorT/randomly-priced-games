<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testRegister()
    {
        $client = static::createClient();

        $client->request('POST', '/register');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
