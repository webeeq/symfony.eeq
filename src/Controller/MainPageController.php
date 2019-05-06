<?php declare(strict_types=1);

// src/Controller/MainPageController.php
namespace App\Controller;

use App\Bundle\{Config, CookieLogin};
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends Controller
{
    /**
     * @Route("/", name="mainpage")
     */
    public function mainPageAction(Request $request): object
    {
        $config = new Config();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $cookieLogin = new CookieLogin($em, $config);
        $cookieLogin->setCookieLogin($session);

        return $this->render('main-page/main-page.html.twig', array(
            'sessionUser' => $session->get('user'),
            'sessionAdmin' => $session->get('admin'),
            'activeMenu' => 'main-page'
        ));
    }
}
