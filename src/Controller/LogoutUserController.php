<?php declare(strict_types=1);

// src/Controller/LogoutUserController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LogoutUserController extends Controller
{
    /**
     * @Route("/wylogowanie")
     */
    public function logoutUserAction(Request $request): object
    {
        $session = $request->getSession();
        $session->invalidate();
        setcookie('login', '', 0, '/');

        return $this->redirectToRoute('loginpage');
    }
}
