<?php declare(strict_types=1);

// src/Bundle/CookieLogin.php
namespace App\Bundle;

use Doctrine\ORM\EntityManager;

class CookieLogin
{
    protected $em;
    protected $config;

    public function __construct(EntityManager $em, object $config)
    {
        $this->em = $em;
        $this->config = $config;
    }

    public function setCookieLogin(object &$session): void
    {
        if (!$session->get('user') && !empty($this->config->getCookieLogin())) {
            $this->setSessionLogin($session);
        }
    }

    private function setSessionLogin(object &$session): void
    {
        $login = explode(';', $this->config->getCookieLogin());

        if ($user = $this->isUserPassword($login[0], $login[1])) {
            if ($user->getActive()) {
                $user->setIpLoged($this->config->getRemoteAddress());
                $user->setDateLoged($this->config->getDateTimeNow());
                $this->em->flush();
                $session->set('id', $user->getId());
                $session->set('admin', $user->getAdmin());
                $session->set('user', $user->getLogin());
            }
        }
    }

    private function isUserPassword(string $login, string  $password): ?object
    {
        $repository = $this->em->getRepository('App:User');
        $query = $repository->createQueryBuilder('u')
            ->select('u')
            ->where('u.login = :login')
            ->andWhere('u.password = :password')
            ->setParameters(array('login' => $login, 'password' => $password))
            ->getQuery();

        return $query->getOneOrNullResult();
    }
}
