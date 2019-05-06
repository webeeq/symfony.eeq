<?php declare(strict_types=1);

// src/Controller/RestController.php
namespace App\Controller;

use App\Bundle\{Config, Message, Response};
use App\Service\RestService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;

class RestController extends Controller
{
    /**
     * @Route("/rest/add-site")
     */
    public function addSiteAction(Request $request): object
    {
        $config = new Config();
        $message = new Message();

        $user = $request->headers->get('php-auth-user') ?? '';
        $password = $request->headers->get('php-auth-pw') ?? '';

        $data = json_decode($request->getContent());

        $restService = new RestService($this, $config);
        $message = $restService->addSiteMessage(
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

    /**
     * @Route("/rest/update-site")
     */
    public function updateSiteAction(Request $request): object
    {
        $config = new Config();
        $message = new Message();

        $user = $request->headers->get('php-auth-user') ?? '';
        $password = $request->headers->get('php-auth-pw') ?? '';

        $data = json_decode($request->getContent());

        $restService = new RestService($this, $config);
        $message = $restService->updateSiteMessage(
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

    /**
     * @Route("/rest/delete-site")
     */
    public function deleteSiteAction(Request $request): object
    {
        $config = new Config();
        $message = new Message();

        $user = $request->headers->get('php-auth-user') ?? '';
        $password = $request->headers->get('php-auth-pw') ?? '';

        $data = json_decode($request->getContent());

        $restService = new RestService($this, $config);
        $message = $restService->deleteSiteMessage(
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
