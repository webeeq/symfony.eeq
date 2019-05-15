<?php declare(strict_types=1);

// src/Controller/Example/AddSiteController.php
namespace App\Controller\Example;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Webeeq\Sieciq\{AddSite, Auth, Config, DeleteSite, Order, UpdateSite};

class AddSiteController extends Controller
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
}
