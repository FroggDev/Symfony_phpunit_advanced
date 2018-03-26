<?php

namespace App\Tests\Exporter;

use App\Exporter\ExportContact;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ExportContactTest
 * @package App\Tests\Exporter
 */
class ExportContactTest extends TestCase
{

    /** @var String */
    static private $contacts;

    /** @var SerializerInterface */
    private $serializer;

    /** @var ObjectManager */
    private $eManager;

    /** @var ObjectRepository */
    private $contactRepository;

    /** @var ExportContact */
    private $exportContact;

    /**
     * Will be excecuted once
     */
    static public function setUpBeforeClass()
    {
        self::$contacts = include __DIR__ . '/../Data/contacts.php';
    }

    /**
     * Will be executed before methods
     */
    public function setUp()
    {
        $this->setSerializer();
        $this->setObjectManager();
        $this->setExportContact();

        # check if getRepository is called only once
        $this->eManager->expects($this->once())->method('getRepository')->willReturn($this->contactRepository);
    }


    public function testExportArrayToFormat()
    {
        $this->assertEquals(
            $this->exportContact->export(),
            self::$contacts
        );
    }

    public function testGetResponse()
    {
        $this->assertEquals(
            $this->exportContact->getResponse('csv')->getContent(),
            self::$contacts
        );
    }

    public function testGetADownloadableResponse()
    {
        $response = $this->exportContact->getDownload('csv');

        $headerContentDispo = $response->headers->get('Content-Disposition');

        preg_match ( '/filename="(.*)"/', $headerContentDispo , $file);

        # check headers
        $this->assertNotNull($headerContentDispo);

        # check headers
        $this->assertContains(
            'attachment;filename=',
            $headerContentDispo
        );

        # check if file has been found
        $this->assertCount(2,$file,'pattern filename="(.*)" not found in attachment');

        # check if file name is correct
        $this->assertEquals($file[1],'contact.csv');

        # response content
        $this->assertEquals(
            $response->getContent(),
            self::$contacts
        );
    }


    private function setSerializer()
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->serializer
            ->method('serialize')
            ->with(null, 'csv')
            ->willReturn(self::$contacts);
    }

    private function setObjectManager()
    {
        $this->contactRepository = $this->createMock(ObjectRepository::class);

        $this->eManager = $this->createMock(ObjectManager::class);
        $this->eManager->method('getRepository')->willReturn($this->contactRepository);
    }

    private function setExportContact()
    {
        $this->exportContact = new ExportContact(
            $this->serializer,
            $this->eManager
        );
    }

}

