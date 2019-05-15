<?php declare(strict_types=1);

// src/Controller/Api/AddSiteController.php
namespace App\Controller\Api;

use App\Bundle\{Config, Message, Response};
use App\Service\Api\AddSiteService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;

class AddSiteController extends Controller
{
    /**
     * @Route("/rest/add-site")
     */
    public function addSiteAction(Request $request): object
    {
        $config = new Config($this);
        $message = new Message();

        $user = $request->headers->get('php-auth-user') ?? '';
        $password = $request->headers->get('php-auth-pw') ?? '';

        $data = json_decode($request->getContent());

        $addSiteService = new AddSiteService($this, $config);
        $message = $addSiteService->addSiteMessage(
            $user,
            $password,
            $data,
            $message
        );

        $response = new Response();
        $response->message = $message->getStrMessage();
        $response->success = $message->getOk();

        return new JsonResponse($response);
    }
}
