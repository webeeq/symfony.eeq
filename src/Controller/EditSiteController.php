<?php declare(strict_types=1);

// src/Controller/EditSiteController.php
namespace App\Controller;

use App\Bundle\Config;
use App\Service\EditSiteService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EditSiteController extends Controller
{
    /**
     * @Route("/strona,{site},edycja", requirements={"site": "\d+"})
     */
    public function editSiteAction(Request $request, int $site): object
    {
        $config = new Config();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        if (!$session->get('user')) {
            return $this->redirectToRoute('loginpage');
        }

        $userSiteId = $em->getRepository('App:Site')
            ->isUserSiteId($session->get('id'), $site);
        if (!$userSiteId) {
            return $this->redirectToRoute('loginpage');
        }

        $editSiteService = new EditSiteService($this, $config);
        $array = $editSiteService->formAction($request, $site);

        return $this->render($array[0], $array[1]);
    }
}
