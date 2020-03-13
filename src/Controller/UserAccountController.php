<?php declare(strict_types=1);

// src/Controller/UserAccountController.php
namespace App\Controller;

use App\Bundle\Config;
use App\Html\PageNavigatorHtml;
use App\Service\UserAccountService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserAccountController extends Controller
{
    /**
     * @Route("/konto", name="user_account")
     */
    public function userAccountAction(Request $request): object
    {
        $config = new Config($this);
        $html = new PageNavigatorHtml();
        $em = $this->getDoctrine()->getManager();

        $userData = $em->getRepository('App:User')
            ->getUserData($id = $this->getUser()->getId());
        if (!$userData) {
            return $this->redirectToRoute('login_page');
        }

        $userAccountService = new UserAccountService($this, $config, $html);
        $array = $userAccountService->formAction($request, $userData, $id, 1);

        return $this->render($array[0], $array[1]);
    }

    /**
     * @Route(
     *     "/konto,{account},strona,{level}",
     *     requirements={"account": "\d+", "level": "\d+"}
     * )
     */
    public function userAccountLevelAction(
        Request $request,
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

        $userAccountService = new UserAccountService($this, $config, $html);
        $array = $userAccountService->formAction(
            $request,
            $userData,
            $account,
            $level
        );

        return $this->render($array[0], $array[1]);
    }
}
