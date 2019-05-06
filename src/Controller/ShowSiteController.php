<?php declare(strict_types=1);

// src/Controller/ShowSiteController.php
namespace App\Controller;

use App\Bundle\Config;
use App\Service\ShowSiteService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ShowSiteController extends Controller
{
    /**
     * @Route("/pokaz")
     */
    public function showSiteAction(Request $request): object
    {
        $config = new Config();
        $session = $request->getSession();

        if (!$session->get('user')) {
            return $this->redirectToRoute('loginpage');
        }

        $showSiteService = new ShowSiteService($this, $config);
        $array = $showSiteService->urlAction($request);

        return $this->render($array[0], $array[1]);
    }
}
