<?php declare(strict_types=1);

// src/Controller/ShowSiteController.php
namespace App\Controller;

use App\Bundle\{Config, CookieLogin};
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
        $em = $this->getDoctrine()->getManager();
        $cookieLogin = new CookieLogin($em, $config);
        $cookieLogin->setCookieLogin($session);

        if ($session->get('user') == '') {
            return $this->redirectToRoute('loginpage');
        }

        $url = '';

        $userMaxShow = $em->getRepository('App:User')
            ->isUserMaxShow($id = $session->get('id'));
        if (!$userMaxShow) {
            $siteRandomUrl = $em->getRepository('App:Site')
                ->getSiteRandomUrl($id);
            if ($siteRandomUrl) {
                $user = $siteRandomUrl->getUser();
                $userShow = $em->getRepository('App:User')
                    ->setUserShow($id, $user);
                if ($userShow) {
                    $url = $siteRandomUrl->getUrl();
                }
            } else {
                $siteRandomUrl = $em->getRepository('App:Site')
                    ->getSiteRandomUrl($id, 0);
                if ($siteRandomUrl) {
                    $user = $siteRandomUrl->getUser();
                    $userShow = $em->getRepository('App:User')
                        ->setUserShow($id, $user, 0);
                    if ($userShow) {
                        $url = $siteRandomUrl->getUrl();
                    }
                }
            }
        } else {
            $url = $config->getUrl() . '/info';
        }

        return $this->render('show-site/show-site.html.twig', array(
            'sessionUser' => $session->get('user'),
            'sessionAdmin' => $session->get('admin'),
            'activeMenu' => 'show-site',
            'url' => $url
        ));
    }
}
