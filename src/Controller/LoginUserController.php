<?php declare(strict_types=1);

// src/Controller/LoginUserController.php
namespace App\Controller;

use App\Bundle\Config;
use App\Service\LoginUserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LoginUserController extends Controller
{
    /**
     * @Route("/logowanie", name="loginpage")
     */
    public function loginUserAction(Request $request): object
    {
        $config = new Config();

        $loginUserService = new LoginUserService($this, $config);
        $array = $loginUserService->formAction($request);

        return $this->render($array[0], $array[1]);
    }

    /**
     * @Route("/logowanie,{user},{code}")
     */
    public function userCodeAction(string $user, string $code): object
    {
        $config = new Config();

        $loginUserService = new LoginUserService($this, $config);
        $array = $loginUserService->codeAction($user, $code);

        return $this->render($array[0], $array[1]);
    }
}
