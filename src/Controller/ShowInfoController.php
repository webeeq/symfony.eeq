<?php declare(strict_types=1);

// src/Controller/ShowInfoController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ShowInfoController extends Controller
{
    /**
     * @Route("/info")
     */
    public function showInfoAction(): object
    {
        return $this->render('show-info/show-info.html.twig');
    }
}
