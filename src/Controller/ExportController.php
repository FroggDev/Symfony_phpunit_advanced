<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends Controller
{
    /**
     * @Route("/export/contact.{_format}", name="export",defaults={"_format"="html"})
     */
    public function contactAction(Request $request)
    {

        var_dump($request->getRequestFormat());

        return new Response("TEST");

        /*
        return $this->render('export/index.html.twig', [
            'controller_name' => 'ExportController',
        ]);
        */

    }

}
