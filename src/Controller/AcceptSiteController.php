<?php declare(strict_types=1);

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
     * @Route("/strona,{site},akceptacja", requirements={"site": "\d+"})
     */
    public function acceptSiteAction(Request $request, int $site): object
    {
        $config = new Config();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        if (!$session->get('admin')) {
            return $this->redirectToRoute('loginpage');
        }

        $acceptSiteId = $em->getRepository('App:Site')->isAcceptSiteId($site);
        if (!$acceptSiteId) {
            return $this->redirectToRoute('loginpage');
        }

        $acceptSiteService = new AcceptSiteService($this, $config);
        $array = $acceptSiteService->formAction($request, $site);

        return $this->render($array[0], $array[1]);
    }
}
