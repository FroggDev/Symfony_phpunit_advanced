<?php
namespace App\Exporter;

use App\Entity\Contact;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ExportContact
 * @package App\Exporter
 */
class ExportContact
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var ObjectManager */
    private $eManager;

    /**
     * ExportContact constructor.
     * @param SerializerInterface $serializer
     * @param ObjectManager $eManager
     */
    public function __construct(SerializerInterface $serializer, ObjectManager $eManager)
    {
        $this->serializer = $serializer;
        $this->eManager = $eManager;
    }

    /**
     * @param string $format
     * @return string
     */
    public function export(string $format = 'csv')
    {
        # get persons from database
        $contacts = $this->eManager->getRepository(Contact::class)->findAll();

        return $this->serializer->serialize($contacts, $format);
    }

    /**
     * @param string $format
     * @return Response
     */
    public function getResponse(string $format)
    {
        return new Response($this->export($format));
    }

    /**
     * Stream a file from memmorty datas
     * @param string $format
     * @return Response
     */
    public function getDownload(string $format)
    {
        $response = new Response($this->export($format));

        $response->headers->set(
            'Content-Disposition',
            'attachment;filename="contact.'. $format.'"'
        );

        return $response;
    }


    /**
     * Stream a file from existing
     * @param string $format
     * @return BinaryFileResponse
     */
    public function getDownloadFile(string $format)
    {
        # file name
        $filename = 'contact.' . $format;

        // in this example, the TextFile.txt needs to exist in the server
        $fullFilename = __DIR__ . "/../../var/export/contact." . $format;

        # save file
        # file_put_contents($fullFilename, $this->export($format));

        # This should return the file to the browser as response
        $response = new BinaryFileResponse($fullFilename);

        #set Content-type
        $response->headers->set('Content-Type', 'application/' . $format);

        // Set content disposition inline of the file
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }
}