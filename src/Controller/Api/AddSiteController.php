<?php

declare(strict_types=1);

// src/Controller/Api/AddSiteController.php
namespace App\Controller\Api;

use App\Bundle\{Config, Response};
use App\Service\Api\AddSiteService;
use App\Validator\Api\AddSiteValidator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;

class AddSiteController extends Controller
{
    /**
     * @Route("/api/add-site")
     */
    public function addSiteAction(Request $request): object
    {
        $config = new Config($this);
        $validator = new AddSiteValidator(
            $this->getDoctrine()->getManager()
        );

        $user = $request->headers->get('php-auth-user') ?? '';
        $password = $request->headers->get('php-auth-pw') ?? '';

        $data = json_decode(
            ($request->getContent()) ? $request->getContent() : '{}'
        );

        $addSiteService = new AddSiteService($this, $config, $validator);
        $message = $addSiteService->addSiteMessage(
            $user,
            $password,
            $data
        );

        $response = new Response();
        $response->message = $message->getStrMessage();
        $response->success = $message->getOk();

        return new JsonResponse($response);
    }
}
