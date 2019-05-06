<?php declare(strict_types=1);

// src/Controller/AddUserController.php
namespace App\Controller;

use App\Bundle\Config;
use App\Service\AddUserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddUserController extends Controller
{
    /**
     * @Route("/rejestracja")
     */
    public function addUserAction(Request $request): object
    {
        $config = new Config();

        $addUserService = new AddUserService($this, $config);
        $array = $addUserService->formAction($request);

        return $this->render($array[0], $array[1]);
    }

    /**
     * @Route("/rejestracja,{user},{code}")
     */
    public function userCodeAction(string $user, string $code): object
    {
        $config = new Config();

        $addUserService = new AddUserService($this, $config);
        $array = $addUserService->codeAction($user, $code);

        return $this->render($array[0], $array[1]);
    }
}
