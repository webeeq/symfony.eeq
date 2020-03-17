<?php

declare(strict_types=1);

// src/Controller/AcceptSiteController.php
namespace App\Controller;

use App\Bundle\Config;
use App\Service\AcceptSiteService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AcceptSiteController extends Controller
{
    /**
     * @Route("/admin/strona,{site},akceptacja", requirements={"site": "\d+"})
     */
    public function acceptSiteAction(Request $request, int $site): object
    {
        $config = new Config($this);
        $em = $this->getDoctrine()->getManager();

        $acceptSiteId = $em->getRepository('App:Site')->isAcceptSiteId($site);
        if (!$acceptSiteId) {
            return $this->redirectToRoute('login_page');
        }

        $acceptSiteService = new AcceptSiteService($this, $config);
        $array = $acceptSiteService->formAction($request, $site);

        return $this->render($array[0], $array[1]);
    }
}
