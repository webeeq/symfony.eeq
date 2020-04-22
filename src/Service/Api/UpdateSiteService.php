<?php

declare(strict_types=1);

namespace App\Service\Api;

use App\Bundle\Config;
use App\Controller\Api\UpdateSiteController;
use App\Validator\Api\UpdateSiteValidator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UpdateSiteService extends Controller
{
    protected UpdateSiteController $controller;
    protected Config $config;
    protected UpdateSiteValidator $validator;

    public function __construct(
        UpdateSiteController $controller,
        Config $config,
        UpdateSiteValidator $validator
    ) {
        $this->controller = $controller;
        $this->config = $config;
        $this->validator = $validator;
    }

    public function updateSiteMessage(
        string $user,
        string $password,
        object $data
    ): object {
        $em = $this->controller->getDoctrine()->getManager();

        $this->validator->validate($user, $password, $data);
        if ($this->validator->isValid()) {
            $siteData = $em->getRepository('App:Site')->setSiteData(
                $data->id,
                $data->visible,
                $data->name,
                $this->config->getRemoteAddress(),
                $this->config->getDateTimeNow()
            );
            if ($siteData) {
                $this->validator->addMessage(
                    'Dane strony www zostały zapisane.'
                );
                $this->validator->setOk(true);
            } else {
                $this->validator->addMessage(
                    'Zapisanie danych strony www nie powiodło się.'
                );
            }
        }

        return $this->validator;
    }
}
