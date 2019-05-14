<?php declare(strict_types=1);

// src/Controller/AdminAccountController.php
namespace App\Controller;

use App\Bundle\Config;
use App\Html\PageNavigatorHtml;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class AdminAccountController extends Controller
{
    /**
     * @Route("/admin")
     */
    public function adminAccountAction(): object
    {
        $config = new Config($this);
        $html = new PageNavigatorHtml();
        $em = $this->getDoctrine()->getManager();

        $userData = $em->getRepository('App:User')
            ->getUserData($id = $this->getUser()->getId());
        if (!$userData) {
            return $this->redirectToRoute('login_page');
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
        int $account,
        int $level
    ): object {
        $config = new Config($this);
        $html = new PageNavigatorHtml();
        $em = $this->getDoctrine()->getManager();

        if ($account != $this->getUser()->getId()) {
            return $this->redirectToRoute('login_page');
        }

        $userData = $em->getRepository('App:User')
            ->getUserData($account);
        if (!$userData) {
            return $this->redirectToRoute('login_page');
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
