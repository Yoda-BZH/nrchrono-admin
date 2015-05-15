<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmulationControllerTest extends WebTestCase
{
    public function testCompare()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/compare');
    }

}
