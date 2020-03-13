<?php declare(strict_types=1);

// src/Controller/UserRegulationController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class UserRegulationController extends Controller
{
    /**
     * @Route("/regulamin")
     */
    public function userRegulationAction(): object
    {
        return $this->render(
            'user-regulation/user-regulation.html.twig',
            array('activeMenu' => 'user-regulation')
        );
    }
}
