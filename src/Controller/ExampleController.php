<?php declare(strict_types=1);

// src/Controller/ExampleController.php
namespace App\Controller;

use Library\Sieciq\{AddSite, Auth, Config, DeleteSite, Order, UpdateSite};
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
        $auth->password = '!@#$%^&*()';

        $addSite = new AddSite();
        $addSite->name = 'Nasz Fach';
        $addSite->url = 'http://www.naszfach.pl';

        $config = new Config();
        $order = new Order($config);
        $response = $order->addSite($auth, $addSite);

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
        $auth->password = '!@#$%^&*()';

        $updateSite = new UpdateSite();
        $updateSite->id = 7;
        $updateSite->name = 'Fachowcy';
        $updateSite->visible = true;

        $config = new Config();
        $order = new Order($config);
        $response = $order->updateSite($auth, $updateSite);

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
        $auth->password = '!@#$%^&*()';

        $deleteSite = new DeleteSite();
        $deleteSite->id = 7;

        $config = new Config();
        $order = new Order($config);
        $response = $order->deleteSite($auth, $deleteSite);

        return $this->render('example/example.html.twig', array(
            'title' => 'Delete Site',
            'data' => $deleteSite,
            'response' => $response
        ));
    }
}
