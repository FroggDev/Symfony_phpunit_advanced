<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ExportControllerTest
 * @package App\Tests\Controller
 */
class ExportControllerTest extends WebTestCase
{

    static private $client;

    /**
     * @param $client
     */
    public function isValidResponse(Client $client)
    {
        $this->assertEquals(
            200 ,
            $client->getResponse()->getStatusCode(),
            "Page returned status code : " . $client->getResponse()->getStatusCode()
        );
    }

    /**
     * Will be excecuted once
     */
    static public function setUpBeforeClass()
    {
        self::$client = self::createClient();
    }


    public function testContactExport()
    {
        $client = self::createClient();

        //var_dump($client->getKernel()->getEnvironment());

        $crawler = $client->request('GET' , '/contact/export.xml');

        $this->isValidResponse($client);

        $this->assertEquals(1,$crawler->filter('h1')->count(),"No balise H1 found !");

        $this->assertContains("Home Page",$crawler->filter('h1')->text(),"H1 Content is not Home Page!");
    }

}
