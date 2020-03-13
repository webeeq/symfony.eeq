<?php declare(strict_types=1);

// src/Controller/Api/DeleteSiteController.php
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
        $validator = new DeleteSiteValidator(
            $this->getDoctrine()->getManager()
        );

        $user = $request->headers->get('php-auth-user') ?? '';
        $password = $request->headers->get('php-auth-pw') ?? '';

        $data = json_decode(
            ($request->getContent()) ? $request->getContent() : '{}'
        );

        $deleteSiteService = new DeleteSiteService($this, $validator);
        $message = $deleteSiteService->deleteSiteMessage(
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
