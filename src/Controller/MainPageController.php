<?php declare(strict_types=1);

// src/Controller/MainPageController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends Controller
{
    /**
     * @Route("/", name="main_page")
     */
    public function mainPageAction(): object
    {
        return $this->render('main-page/main-page.html.twig', array(
            'activeMenu' => 'main-page'
        ));
    }
}
