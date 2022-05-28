<?php

namespace App\tests\Controller;

use Goutte\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Annotation\Route;

class GoutteClientControllerTest extends WebTestCase
{
    /**
     * @Route("/goutte/client", name="goutte_client")
     */
    public function index(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET',"https://odpf.org/la-xxvie-2019.html");
        $selector='/html/body/div[2]/div[2]/div[2]/div[2]/div[2]/ul/li[1]/a[1]';
        $this->assertEquals(1, $crawler->filterXPath($selector)->count());
        return;
    }

}

