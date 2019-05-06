<?php declare(strict_types=1);

// src/Service/RestService.php
namespace App\Service;

use App\Bundle\Config;
use App\Controller\RestController;
use App\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RestService extends Controller
{
    protected $controller;
    protected $config;

    public function __construct(
        RestController $controller,
        Config $config
    ) {
        $this->controller = $controller;
        $this->config = $config;
    }

    public function addSiteMessage(
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
            if (strlen($data->name) < 1) {
                $message->addMessage('Nazwa strony www musi zostać podana.');
            } elseif (strlen($data->name) > 180) {
                $message->addMessage(
                    'Nazwa strony www może zawierać maksymalnie 180 znaków.'
                );
            }
            $http = substr($data->url, 0, 7) != 'http://';
            $https = substr($data->url, 0, 8) != 'https://';
            if ($http && $https) {
                $message->addMessage(
                    'Url musi rozpoczynać się od znaków: http://'
                );
            }
            if (strlen($data->url) > 180) {
                $message->addMessage(
                    'Url może zawierać maksymalnie 180 znaków.'
                );
            }
            if (!$message->isMessage()) {
                $site = new Site();
                $site->setUser($restUserPassword);
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
                    $message->addMessage(
                        'Strona www została dodana i oczekuje na akceptację.'
                    );
                    $message->setOk(true);
                } catch (\Exception $e) {
                    $message->addMessage(
                        'Dodanie strony www nie powiodło się.'
                    );
                }
            }
        } else {
            $message->addMessage('Błędna autoryzacja przesyłanych danych.');
        }

        return $message;
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

    public function deleteSiteMessage(
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
            if (!$message->isMessage()) {
                $siteData = $em->getRepository('App:Site')
                    ->deleteSiteData($data->id);
                if ($siteData) {
                    $message->addMessage('Dane strony www zostały usunięte.');
                    $message->setOk(true);
                } else {
                    $message->addMessage(
                        'Usunięcie danych strony www nie powiodło się.'
                    );
                }
            }
        } else {
            $message->addMessage('Błędna autoryzacja przesyłanych danych.');
        }

        return $message;
    }
}
