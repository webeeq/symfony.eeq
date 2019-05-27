<?php declare(strict_types=1);

// src/Service/Api/DeleteSiteService.php
namespace App\Service\Api;

use App\Controller\Api\DeleteSiteController;
use App\Validator\Api\DeleteSiteValidator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DeleteSiteService extends Controller
{
    protected $controller;
    protected $validator;

    public function __construct(
        DeleteSiteController $controller,
        DeleteSiteValidator $validator
    ) {
        $this->controller = $controller;
        $this->validator = $validator;
    }

    public function deleteSiteMessage(
        string $user,
        string $password,
        object $data
    ): object {
        $em = $this->controller->getDoctrine()->getManager();

        $this->validator->validate($user, $password, $data);
        if ($this->validator->isValid()) {
            $siteData = $em->getRepository('App:Site')
                ->deleteSiteData($data->id);
            if ($siteData) {
                $this->validator->addMessage(
                    'Dane strony www zostały usunięte.'
                );
                $this->validator->setOk(true);
            } else {
                $this->validator->addMessage(
                    'Usunięcie danych strony www nie powiodło się.'
                );
            }
        }

        return $this->validator;
    }
}
