<?php declare(strict_types=1);

// src/Controller/UserHelpController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class UserHelpController extends Controller
{
    /**
     * @Route("/pomoc")
     */
    public function userHelpAction(): object
    {
        return $this->render('user-help/user-help.html.twig', array(
            'activeMenu' => 'user-help'
        ));
    }
}
