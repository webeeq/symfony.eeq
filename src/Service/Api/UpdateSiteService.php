<?php declare(strict_types=1);

// src/Service/Api/UpdateSiteService.php
namespace App\Service\Api;

use App\Bundle\Config;
use App\Controller\Api\UpdateSiteController;
use App\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UpdateSiteService extends Controller
{
    protected $controller;
    protected $config;

    public function __construct(
        UpdateSiteController $controller,
        Config $config
    ) {
        $this->controller = $controller;
        $this->config = $config;
    }

    public function updateSiteMessage(
        string $user,
        string $password,
        ?object $data,
        object $message
    ): object {
        $em = $this->controller->getDoctrine()->getManager();

        $restUserPassword = $em->getRepository('App:User')
            ->getRestUserPassword($user);
        $passwordVerify = password_verify(
            $password,
            ($restUserPassword) ? $restUserPassword->getPassword() : ''
        );
        if ($passwordVerify) {
            $restUserSite = $em->getRepository('App:Site')
                ->isRestUserSite($restUserPassword->getId(), $data->id);
            if (!$restUserSite) {
                $message->addMessage(
                    'Baza nie zawiera podanej strony dla autoryzacji.'
                );
            }
            if (strlen($data->name) < 1) {
                $message->addMessage('Nazwa strony www musi zostać podana.');
            } elseif (strlen($data->name) > 180) {
                $message->addMessage(
                    'Nazwa strony www może zawierać maksymalnie 180 znaków.'
                );
            }
            if (!$message->isMessage()) {
                $siteData = $em->getRepository('App:Site')->setSiteData(
                    $data->id,
                    $data->visible,
                    $data->name,
                    $this->config->getRemoteAddress(),
                    $this->config->getDateTimeNow()
                );
                if ($siteData) {
                    $message->addMessage('Dane strony www zostały zapisane.');
                    $message->setOk(true);
                } else {
                    $message->addMessage(
                        'Zapisanie danych strony www nie powiodło się.'
                    );
                }
            }
        } else {
            $message->addMessage('Błędna autoryzacja przesyłanych danych.');
        }

        return $message;
    }
}
