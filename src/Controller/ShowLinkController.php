<?php declare(strict_types=1);

// src/Controller/ShowLinkController.php
namespace App\Controller;

use App\Bundle\{Config, CookieLogin};
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ShowLinkController extends Controller
{
    /**
     * @Route("/link")
     */
    public function showLinkAction(Request $request): object
    {
        $config = new Config();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $cookieLogin = new CookieLogin($em, $config);
        $cookieLogin->setCookieLogin($session);

        if ($session->get('user') == '') {
            return $this->redirectToRoute('loginpage');
        }

        return $this->render('show-site/show-site.html.twig', array(
            'sessionUser' => $session->get('user'),
            'sessionAdmin' => $session->get('admin'),
            'activeMenu' => 'show-link',
            'url' => $request->get('www')
        ));
    }
}
