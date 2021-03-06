<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Bundle\Response;
use App\Service\Api\DeleteSiteService;
use App\Validator\Api\DeleteSiteValidator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;

class DeleteSiteController extends Controller
{
    /**
     * @Route("/api/delete-site")
     */
    public function deleteSiteAction(Request $request): object
    {
        $deleteSiteValidator = new DeleteSiteValidator(
            $this->getDoctrine()->getManager()
        );

        $user = $request->headers->get('php-auth-user') ?? '';
        $password = $request->headers->get('php-auth-pw') ?? '';

        $data = json_decode(
            ($request->getContent()) ? $request->getContent() : '{}'
        );

        $deleteSiteService = new DeleteSiteService(
            $this,
            $deleteSiteValidator
        );
        $message = $deleteSiteService->deleteSiteMessage(
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
