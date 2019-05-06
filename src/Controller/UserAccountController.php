<?php declare(strict_types=1);

// src/Controller/UserAccountController.php
namespace App\Controller;

use App\Bundle\{Config, Html};
use App\Service\UserAccountService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserAccountController extends Controller
{
    /**
     * @Route("/konto")
     */
    public function userAccountAction(Request $request): object
    {
        $config = new Config();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $html = new Html();

        if (!$session->get('user')) {
            return $this->redirectToRoute('loginpage');
        }

        $userData = $em->getRepository('App:User')
            ->getUserData($id = $session->get('id'));
        if (!$userData) {
            return $this->redirectToRoute('loginpage');
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
        $config = new Config();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $html = new Html();

        if (!$session->get('user')) {
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
