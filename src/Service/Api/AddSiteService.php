<?php

declare(strict_types=1);

// src/Service/Api/AddSiteService.php
namespace App\Service\Api;

use App\Bundle\Config;
use App\Controller\Api\AddSiteController;
use App\Entity\Site;
use App\Validator\Api\AddSiteValidator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AddSiteService extends Controller
{
    protected AddSiteController $controller;
    protected Config $config;
    protected AddSiteValidator $validator;

    public function __construct(
        AddSiteController $controller,
        Config $config,
        AddSiteValidator $validator
    ) {
        $this->controller = $controller;
        $this->config = $config;
        $this->validator = $validator;
    }

    public function addSiteMessage(
        string $user,
        string $password,
        object $data
    ): object {
        $em = $this->controller->getDoctrine()->getManager();

        $this->validator->validate($user, $password, $data, $apiUser);
        if ($this->validator->isValid()) {
            $site = new Site();
            $site->setUser($apiUser);
            $site->setActive(false);
            $site->setVisible(true);
            $site->setName($data->name);
            $site->setUrl($data->url);
            $site->setIpAdded($this->config->getRemoteAddress());
            $site->setDateAdded($this->config->getDateTimeNow());
            $site->setIpUpdated('');
            $site->setDateUpdated(new \DateTime('1970-01-01 00:00:00'));
            $em->persist($site);
            try {
                $em->flush();
                $this->validator->addMessage(
                    'Strona www została dodana i oczekuje na akceptację.'
                );
                $this->validator->setOk(true);
            } catch (\Exception $e) {
                $this->validator->addMessage(
                    'Dodanie strony www nie powiodło się.'
                );
            }
        }

        return $this->validator;
    }
}
