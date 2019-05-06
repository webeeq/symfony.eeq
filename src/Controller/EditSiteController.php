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
     * @Route("/konto/strona,{site},edycja", requirements={"site": "\d+"})
     */
    public function editSiteAction(Request $request, int $site): object
    {
        $config = new Config($this);
        $em = $this->getDoctrine()->getManager();

        $userSiteId = $em->getRepository('App:Site')
            ->isUserSiteId($this->getUser()->getId(), $site);
        if (!$userSiteId) {
            return $this->redirectToRoute('login_page');
        }

        $editSiteService = new EditSiteService($this, $config);
        $array = $editSiteService->formAction($request, $site);

        return $this->render($array[0], $array[1]);
    }
}
