<?php declare(strict_types=1);

// src/Service/Api/DeleteSiteService.php
namespace App\Service\Api;

use App\Controller\Api\DeleteSiteController;
use App\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DeleteSiteService extends Controller
{
    protected $controller;

    public function __construct(DeleteSiteController $controller)
    {
        $this->controller = $controller;
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
