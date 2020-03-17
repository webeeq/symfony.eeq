<?php

declare(strict_types=1);

// src/Controller/UserPrivacyController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class UserPrivacyController extends Controller
{
    /**
     * @Route("/prywatnosc")
     */
    public function userPrivacyAction(): object
    {
        return $this->render('user-privacy/user-privacy.html.twig', array(
            'activeMenu' => 'user-privacy'
        ));
    }
}
