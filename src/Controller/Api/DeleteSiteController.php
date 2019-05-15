<?php declare(strict_types=1);

// src/Controller/Api/DeleteSiteController.php
namespace App\Controller\Api;

use App\Bundle\{Message, Response};
use App\Service\Api\DeleteSiteService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;

class DeleteSiteController extends Controller
{
    /**
     * @Route("/rest/delete-site")
     */
    public function deleteSiteAction(Request $request): object
    {
        $message = new Message();

        $user = $request->headers->get('php-auth-user') ?? '';
        $password = $request->headers->get('php-auth-pw') ?? '';

        $data = json_decode($request->getContent());

        $deleteSiteService = new DeleteSiteService($this);
        $message = $deleteSiteService->deleteSiteMessage(
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
