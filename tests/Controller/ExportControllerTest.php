<?php
namespace App\Tests\Controller;

use App\Entity\Contact;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Component\Console\Input\StringInput;

/**
 * Class ExportControllerTest
 * @package App\Tests\Controller
 */
class ExportControllerTest extends WebTestCase
{

    static private $client;

    /**
     * Will be excecuted once
     */
    static public function setUpBeforeClass()
    {
        self::$client = self::createClient();

        $client = self::createClient();

        $application = new Application($client->getKernel());

        $application->setAutoExit(false);

        $application->run(new StringInput('doctrine:database:drop --force --env=test'));

        $application->run(new StringInput('doctrine:database:create --env=test'));

        $application->run(new StringInput('doctrine:migrations:migrate --env=test'));



        echo "\n[ TESTING ".__CLASS__." ]\n";
    }

    public function setUp()
    {



    }

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

    public function testAllRoutes()
    {

        echo "\ntestAllRoutes\n";

        $client = static::createclient();

        # enable symfony profiler (before the request !)
        $client->enableProfiler();

        $client->request('GET', '/');

        $router = $client->getContainer()->get('router');

        $routesCollection = $router->getRouteCollection();

        foreach ($routesCollection as $route) {

            $tokens = $route->compile()->getTokens();

            foreach ($tokens as $token) {
                if ('text' === $token[0]) {
                    $path = $token[1];
                }
            }

            # enable symfony profiler (before the request !)
            $client->enableProfiler();

            $client->request('GET', $path);

            # check if response is 200 OK
            $this->isValidResponse($client,"Error on page ".$path ."with code " . $client->getResponse()->getStatusCode());


            # @ TODO test if reponse < 500ms (return 0)
            # check if display page in less than 500 ms

            echo "1==>=============";
            $time = $client->getProfile()->getCollector('time')->getDuration();
            echo $time;
            echo "1==>===========";
            #echo $time;

            $this->assertLessThan(500,$time, "Page is too long to load = ".$time);

        }
    }




    public function testContactExport()
    {
        echo "\ntestContactExport\n";


        $client = self::createClient();


        # enable symfony profiler (before the request !)
        $client->enableProfiler();

        //var_dump($client->getKernel()->getEnvironment());

        $crawler = $client->request('GET' , '/contact/export.xml');

        $time = $client->getProfile()->getCollector('time')->getDuration();

        $this->isValidResponse($client);

        $this->assertLessThan(500,$time, "Page is too long to load = ".$time);


        echo "2==>=============";
        dump($time);
        echo "2==>===========";
        # $this->assertEquals(1,$crawler->filter('h1')->count(),"No balise H1 found !");
        # $this->assertContains("Home Page",$crawler->filter('h1')->text(),"H1 Content is not Home Page!");
    }

    public function testContactAdd()
    {

        echo "\n\nDO NOT FORGET TO CREATE TEST DATABASE\n";
        echo "php bin/console doctrine:database:create --env=test\n\n";

        $client = self::createClient();

        $crawler = $client->request('GET' , '/contact/create');

        $this->isValidResponse($client);


        $this->assertEquals(1,$crawler->filter('form')->count(),"No balise H1 found !");
        $this->assertContains("Ajout de contact",$crawler->filter('h1')->text(),"H1 Content is not Home Page!");
    }


    public function testFailContactAdd()
    {
        $client = self::createClient();

        $crawler = $client->request('GET' , '/contact/create');


        #$form = $crawler->selectButton('submit')->form();
        # same as
        $form = $crawler->filter('form')->form();

        # selector by name !
        $form['contact[firstname]']='';
        $form['contact[phone]']='00000000';
        $form['contact[email]']='test@frogg.fr';
        $form['contact[lastname]']='lastname';

        $crawler = $client->submit($form);

        # echo "====";
        # dump($crawler);
        # dump($crawler->filter('li'));
        # echo "====";

        $this->assertEquals(1,$crawler->filter('li')->count(),"No balise <li> found !");
        $this->assertContains("This value should not be blank.",$crawler->filter('li')->text());

    }

    public function testSuccessContactAdd()
    {
        $client = self::createClient();

        $crawler = $client->request('GET' , '/contact/create');


        #$form = $crawler->selectButton('submit')->form();
        # same as
        $form = $crawler->filter('form')->form();

        # selector by name !
        $form['contact[firstname]']='sdfsdfdsf';
        $form['contact[phone]']='00000000';
        $form['contact[email]']='test@frogg.fr';
        $form['contact[lastname]']='lastname';

        $client->submit($form);

        $crawler = $client->followRedirect();

        # echo "====";
        # dump($crawler);
        # dump($crawler->filter('li'));
        # echo "====";

        $this->assertEquals(1,$crawler->filter('.success')->count(),"No balise success div found !");
        $this->assertContains("Saved !",$crawler->filter('.success')->text());


        /** @var $er EntityRepository */
        $er = $client->getContainer()->get('doctrine')->getRepository(Contact::class);
        $er->count([]);

    }

    static public function tearDownAfterClass()
    {
        echo "\n\nTHIS METHOD IS STARTED AT THE END OF TESTS\n\n";
    }

}
