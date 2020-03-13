<?php declare(strict_types=1);

// src/Controller/ShowLinkController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ShowLinkController extends Controller
{
    /**
     * @Route("/link")
     */
    public function showLinkAction(Request $request): object
    {
        return $this->render('show-site/show-site.html.twig', array(
            'activeMenu' => 'show-link',
            'url' => $request->get('www')
        ));
    }
}
