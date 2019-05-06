<?php declare(strict_types=1);

// src/Controller/ExampleController.php
namespace App\Controller;

use Library\Sieciq\{AddSite, Auth, DeleteSite, Order, UpdateSite};
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ExampleController extends Controller
{
    /**
     * @Route("/dodawanie")
     */
    public function addSiteAction(): object
    {
        $auth = new Auth();
        $auth->user = 'user';
        $auth->password = md5('11111111');

        $addSite = new AddSite();
        $addSite->name = 'Nasz Fach';
        $addSite->url = 'http://www.naszfach.pl';

        $response = Order::addSite($auth, $addSite);

        return $this->render('example/example.html.twig', array(
            'title' => 'Add Site',
            'data' => $addSite,
            'response' => $response
        ));
    }

    /**
     * @Route("/aktualizacja")
     */
    public function updateSiteAction(): object
    {
        $auth = new Auth();
        $auth->user = 'user';
        $auth->password = md5('11111111');

        $updateSite = new UpdateSite();
        $updateSite->id = 77;
        $updateSite->name = 'Fachowcy';
        $updateSite->visible = true;

        $response = Order::updateSite($auth, $updateSite);

        return $this->render('example/example.html.twig', array(
            'title' => 'Update Site',
            'data' => $updateSite,
            'response' => $response
        ));
    }

    /**
     * @Route("/usuwanie")
     */
    public function deleteSiteAction(): object
    {
        $auth = new Auth();
        $auth->user = 'user';
        $auth->password = md5('11111111');

        $deleteSite = new DeleteSite();
        $deleteSite->id = 77;

        $response = Order::deleteSite($auth, $deleteSite);

        return $this->render('example/example.html.twig', array(
            'title' => 'Delete Site',
            'data' => $deleteSite,
            'response' => $response
        ));
    }
}
