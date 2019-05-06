<?php declare(strict_types=1);

// src/Controller/AdminAccountController.php
namespace App\Controller;

use App\Bundle\{Config, Html};
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminAccountController extends Controller
{
    /**
     * @Route("/admin")
     */
    public function adminAccountAction(Request $request): object
    {
        $config = new Config();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $html = new Html();

        if (!$session->get('admin')) {
            return $this->redirectToRoute('loginpage');
        }

        $userData = $em->getRepository('App:User')
            ->getUserData($id = $session->get('id'));
        if (!$userData) {
            return $this->redirectToRoute('loginpage');
        }

        $adminSiteList = $em->getRepository('App:Site')
            ->getAdminSiteList(1, $listLimit = 10);
        $adminPageNavigator = $em->getRepository('App:Site')
            ->adminPageNavigator($config, $html, $id, 1, $listLimit);

        return $this->render('admin-account/admin-account.html.twig', array(
            'activeMenu' => 'admin-account',
            'adminSiteList' => $adminSiteList,
            'adminPageNavigator' => $adminPageNavigator
        ));
    }

    /**
     * @Route(
     *     "/admin,{account},strona,{level}",
     *     requirements={"account": "\d+", "level": "\d+"}
     * )
     */
    public function adminAccountLevelAction(
        Request $request,
        int $account,
        int $level
    ): object {
        $config = new Config();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $html = new Html();

        if (!$session->get('admin')) {
            return $this->redirectToRoute('loginpage');
        }

        if ($account != $session->get('id')) {
            return $this->redirectToRoute('loginpage');
        }

        $userData = $em->getRepository('App:User')
            ->getUserData($account);
        if (!$userData) {
            return $this->redirectToRoute('loginpage');
        }

        $adminSiteList = $em->getRepository('App:Site')
            ->getAdminSiteList($level, $listLimit = 10);
        $adminPageNavigator = $em->getRepository('App:Site')
            ->adminPageNavigator(
                $config,
                $html,
                $account,
                $level,
                $listLimit
            );

        return $this->render('admin-account/admin-account.html.twig', array(
            'activeMenu' => 'admin-account',
            'adminSiteList' => $adminSiteList,
            'adminPageNavigator' => $adminPageNavigator
        ));
    }
}
