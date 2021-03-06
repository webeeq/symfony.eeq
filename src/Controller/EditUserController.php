<?php

declare(strict_types=1);

namespace App\Controller;

use App\Bundle\{Config, Key};
use App\Service\EditUserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EditUserController extends Controller
{
    /**
     * @Route("/konto/uzytkownik,{user},edycja", requirements={"user": "\d+"})
     */
    public function editUserAction(Request $request, int $user): object
    {
        $config = new Config($this);
        $key = new Key();
        $em = $this->getDoctrine()->getManager();

        $userId = $em->getRepository('App:User')
            ->isUserId($this->getUser()->getId(), $user);
        if (!$userId) {
            return $this->redirectToRoute('login_page');
        }

        $editUserService = new EditUserService($this, $config, $key);
        $array = $editUserService->formAction($request, $user);

        return $this->render($array[0], $array[1]);
    }
}
