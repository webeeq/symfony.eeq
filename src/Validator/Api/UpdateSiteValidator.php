<?php declare(strict_types=1);

// src/Validator/Api/UpdateSiteValidator.php
namespace App\Validator\Api;

use App\Bundle\Message;
use Doctrine\ORM\EntityManagerInterface;

class UpdateSiteValidator extends Message
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    public function validate(
        string $user,
        string $password,
        object $data
    ): void {
        $apiUserPassword = $this->em->getRepository('App:User')
            ->getApiUserPassword($user);
        $passwordVerify = password_verify(
            $password,
            ($apiUserPassword) ? $apiUserPassword->getPassword() : ''
        );
        if (!$passwordVerify) {
            $this->addMessage('Błędna autoryzacja przesyłanych danych.');

            return;
        }
        $apiUserSite = $this->em->getRepository('App:Site')
            ->isApiUserSite($apiUserPassword->getId(), $data->id);
        if (!$apiUserSite) {
            $this->addMessage(
                'Baza nie zawiera podanej strony dla autoryzacji.'
            );
        }
        if (strlen($data->name) < 1) {
            $this->addMessage('Nazwa strony www musi zostać podana.');
        } elseif (strlen($data->name) > 180) {
            $this->addMessage(
                'Nazwa strony www może zawierać maksymalnie 180 znaków.'
            );
        }
    }
}
