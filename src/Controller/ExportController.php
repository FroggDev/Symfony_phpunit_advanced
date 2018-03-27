<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Exporter\ExportContact;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ExportController
 * @package App\Controller
 */
class ExportController extends AbstractController
{
    /**
     * @Route("/contact/export.{_format}", name="contact_export",defaults={"_format"="csv"})
     */
    public function contactExport(Request $request, ExportContact $exportContact) //, ExportContact $exportContact //SerializerInterface $serializer,
    {
        # get request format
        $format = $request->getRequestFormat();

        return $exportContact->getDownload($format);
    }


    /**
     * @Route ("/",name="index")
     * @Route("/contact/create.{_format}", name="contact_create",defaults={"_format"="html"},requirements={"_format"="html"}))
     */
    public function contactCreate(Request $request)
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);

        # Form posted data management
        $form->handleRequest($request);

        # Check the form (order is important)
        if ($form->isSubmitted() && $form->isValid()) {

            # insert Into database
            $eManager = $this->getDoctrine()->getManager();
            $eManager->persist($form->getData());
            $eManager->flush();

            $this->addFlash("success" , "Saved !" );

            # redirect on the created article
            return $this->redirectToRoute(
                'contact_create'
            );
        }

        return $this->render('form/contact.html.twig', array(
            'form' => $form->createView(),
        ));
    }


}
