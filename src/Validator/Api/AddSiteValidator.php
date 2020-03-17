<?php

declare(strict_types=1);

// src/Validator/Api/AddSiteValidator.php
namespace App\Validator\Api;

use App\Bundle\Message;
use Doctrine\ORM\EntityManagerInterface;

class AddSiteValidator extends Message
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
        object $data,
        ?object &$apiUserPassword
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
        if (strlen($data->name) < 1) {
            $this->addMessage('Nazwa strony www musi zostać podana.');
        } elseif (strlen($data->name) > 180) {
            $this->addMessage(
                'Nazwa strony www może zawierać maksymalnie 180 znaków.'
            );
        }
        $http = substr($data->url, 0, 7) == 'http://';
        $https = substr($data->url, 0, 8) == 'https://';
        if (!$http && !$https) {
            $this->addMessage(
                'Url musi rozpoczynać się od znaków: http://'
            );
        }
        if (strlen($data->url) > 180) {
            $this->addMessage(
                'Url może zawierać maksymalnie 180 znaków.'
            );
        }
    }
}
