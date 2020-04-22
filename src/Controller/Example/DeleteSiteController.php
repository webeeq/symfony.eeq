<?php

declare(strict_types=1);

namespace App\Controller\Example;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Webeeq\Sieciq\{AddSite, Auth, Config, DeleteSite, Order, UpdateSite};

class DeleteSiteController extends Controller
{
    /**
     * @Route("/usuwanie")
     */
    public function deleteSiteAction(): object
    {
        $auth = new Auth();
        $auth->user = 'user';
        $auth->password = '!@#$%^&*()';

        $deleteSite = new DeleteSite();
        $deleteSite->id = 8;

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
