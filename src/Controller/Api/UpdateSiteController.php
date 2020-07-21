<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Bundle\{Config, Response};
use App\Service\Api\UpdateSiteService;
use App\Validator\Api\UpdateSiteValidator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;

class UpdateSiteController extends Controller
{
    /**
     * @Route("/api/update-site")
     */
    public function updateSiteAction(Request $request): object
    {
        $config = new Config($this);
        $updateSiteValidator = new UpdateSiteValidator(
            $this->getDoctrine()->getManager()
        );

        $user = $request->headers->get('php-auth-user') ?? '';
        $password = $request->headers->get('php-auth-pw') ?? '';

        $data = json_decode(
            ($request->getContent()) ? $request->getContent() : '{}'
        );

        $updateSiteService = new UpdateSiteService(
            $this,
            $config,
            $updateSiteValidator
        );
        $message = $updateSiteService->updateSiteMessage(
            $user,
            $password,
            $data
        );

        $response = new Response();
        $response->success = $message->getOk();
        $response->message = $message->getStrMessage();

        return new JsonResponse($response);
    }
}
