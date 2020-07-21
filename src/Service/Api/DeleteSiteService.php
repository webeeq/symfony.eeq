<?php

declare(strict_types=1);

namespace App\Service\Api;

use App\Controller\Api\DeleteSiteController;
use App\Validator\Api\DeleteSiteValidator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DeleteSiteService extends Controller
{
    protected DeleteSiteController $deleteSiteController;
    protected DeleteSiteValidator $deleteSiteValidator;

    public function __construct(
        DeleteSiteController $deleteSiteController,
        DeleteSiteValidator $deleteSiteValidator
    ) {
        $this->deleteSiteController = $deleteSiteController;
        $this->deleteSiteValidator = $deleteSiteValidator;
    }

    public function deleteSiteMessage(
        string $user,
        string $password,
        object $data
    ): object {
        $em = $this->deleteSiteController->getDoctrine()->getManager();

        $this->deleteSiteValidator->validate($user, $password, $data);
        if ($this->deleteSiteValidator->isValid()) {
            $siteData = $em->getRepository('App:Site')
                ->deleteSiteData($data->id);
            if ($siteData) {
                $this->deleteSiteValidator->addMessage(
                    'Dane strony www zostały usunięte.'
                );
                $this->deleteSiteValidator->setOk(true);
            } else {
                $this->deleteSiteValidator->addMessage(
                    'Usunięcie danych strony www nie powiodło się.'
                );
            }
        }

        return $this->deleteSiteValidator;
    }
}
