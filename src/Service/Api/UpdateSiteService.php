<?php

declare(strict_types=1);

namespace App\Service\Api;

use App\Bundle\Config;
use App\Controller\Api\UpdateSiteController;
use App\Validator\Api\UpdateSiteValidator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UpdateSiteService extends Controller
{
    protected UpdateSiteController $updateSiteController;
    protected Config $config;
    protected UpdateSiteValidator $updateSiteValidator;

    public function __construct(
        UpdateSiteController $updateSiteController,
        Config $config,
        UpdateSiteValidator $updateSiteValidator
    ) {
        $this->updateSiteController = $updateSiteController;
        $this->config = $config;
        $this->updateSiteValidator = $updateSiteValidator;
    }

    public function updateSiteMessage(
        string $user,
        string $password,
        object $data
    ): object {
        $em = $this->updateSiteController->getDoctrine()->getManager();

        $this->updateSiteValidator->validate($user, $password, $data);
        if ($this->updateSiteValidator->isValid()) {
            $siteData = $em->getRepository('App:Site')->setSiteData(
                $data->id,
                $data->visible,
                $data->name,
                $this->config->getRemoteAddress(),
                $this->config->getDateTimeNow()
            );
            if ($siteData) {
                $this->updateSiteValidator->addMessage(
                    'Dane strony www zostały zapisane.'
                );
                $this->updateSiteValidator->setOk(true);
            } else {
                $this->updateSiteValidator->addMessage(
                    'Zapisanie danych strony www nie powiodło się.'
                );
            }
        }

        return $this->updateSiteValidator;
    }
}
