<?php declare(strict_types=1);

// src/Controller/EditUserController.php
namespace App\Controller;

use App\Bundle\Config;
use App\Service\EditUserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EditUserController extends Controller
{
    /**
     * @Route("/uzytkownik,{user},edycja", requirements={"user": "\d+"})
     */
    public function editUserAction(Request $request, int $user): object
    {
        $config = new Config();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        if (!$session->get('user')) {
            return $this->redirectToRoute('loginpage');
        }

        $userId = $em->getRepository('App:User')
            ->isUserId($session->get('id'), $user);
        if (!$userId) {
            return $this->redirectToRoute('loginpage');
        }

        $editUserService = new EditUserService($this, $config);
        $array = $editUserService->formAction($request, $user);

        return $this->render($array[0], $array[1]);
    }
}
