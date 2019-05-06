<?php declare(strict_types=1);

// src/Controller/UserRegulationController.php
namespace App\Controller;

use App\Bundle\{Config, CookieLogin};
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserRegulationController extends Controller
{
    /**
     * @Route("/regulamin")
     */
    public function userRegulationAction(Request $request): object
    {
        $config = new Config();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $cookieLogin = new CookieLogin($em, $config);
        $cookieLogin->setCookieLogin($session);

        return $this->render(
            'user-regulation/user-regulation.html.twig',
            array(
                'sessionUser' => $session->get('user'),
                'sessionAdmin' => $session->get('admin'),
                'activeMenu' => 'user-regulation'
            )
        );
    }
}
