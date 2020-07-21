<?php

declare(strict_types=1);

namespace App\Service\Api;

use App\Bundle\Config;
use App\Controller\Api\AddSiteController;
use App\Entity\Site;
use App\Validator\Api\AddSiteValidator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AddSiteService extends Controller
{
    protected AddSiteController $addSiteController;
    protected Config $config;
    protected AddSiteValidator $addSiteValidator;

    public function __construct(
        AddSiteController $addSiteController,
        Config $config,
        AddSiteValidator $addSiteValidator
    ) {
        $this->addSiteController = $addSiteController;
        $this->config = $config;
        $this->addSiteValidator = $addSiteValidator;
    }

    public function addSiteMessage(
        string $user,
        string $password,
        object $data
    ): object {
        $em = $this->addSiteController->getDoctrine()->getManager();

        $this->addSiteValidator->validate($user, $password, $data, $apiUser);
        if ($this->addSiteValidator->isValid()) {
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
                $this->addSiteValidator->addMessage(
                    'Strona www została dodana i oczekuje na akceptację.'
                );
                $this->addSiteValidator->setOk(true);
            } catch (\Exception $e) {
                $this->addSiteValidator->addMessage(
                    'Dodanie strony www nie powiodło się.'
                );
            }
        }

        return $this->addSiteValidator;
    }
}
