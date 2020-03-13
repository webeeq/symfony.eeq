<?php declare(strict_types=1);

// src/Controller/Example/UpdateSiteController.php
namespace App\Controller\Example;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Webeeq\Sieciq\{AddSite, Auth, Config, DeleteSite, Order, UpdateSite};

class UpdateSiteController extends Controller
{
    /**
     * @Route("/aktualizacja")
     */
    public function updateSiteAction(): object
    {
        $auth = new Auth();
        $auth->user = 'user';
        $auth->password = '!@#$%^&*()';

        $updateSite = new UpdateSite();
        $updateSite->id = 8;
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
}
