<?php declare(strict_types=1);

// src/Controller/Api/UpdateSiteController.php
namespace App\Controller\Api;

use App\Bundle\{Config, Message, Response};
use App\Service\Api\UpdateSiteService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;

class UpdateSiteController extends Controller
{
    /**
     * @Route("/rest/update-site")
     */
    public function updateSiteAction(Request $request): object
    {
        $config = new Config($this);
        $message = new Message();

        $user = $request->headers->get('php-auth-user') ?? '';
        $password = $request->headers->get('php-auth-pw') ?? '';

        $data = json_decode($request->getContent());

        $updateSiteService = new UpdateSiteService($this, $config);
        $message = $updateSiteService->updateSiteMessage(
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
