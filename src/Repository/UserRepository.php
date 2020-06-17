<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function isUserId(int $user, int $user2): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT u FROM App:User u
            WHERE u.enabled = 1 AND u.id = :user AND u.id = :user2'
        )
            ->setParameter('user', $user)
            ->setParameter('user2', $user2);

        return $query->getOneOrNullResult();
    }

    public function getIdPassword(int $user): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT u FROM App:User u WHERE u.id = :user'
        )->setParameter('user', $user);

        return $query->getOneOrNullResult();
    }

    public function getApiUserPassword(string $login): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT u FROM App:User u
            WHERE u.enabled = 1 AND u.usernameCanonical = :loginCanonical'
        )->setParameter('loginCanonical', strtolower($login));

        return $query->getOneOrNullResult();
    }

    public function isUserEmail(string $email): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT u FROM App:User u WHERE u.emailCanonical = :emailCanonical'
        )->setParameter('emailCanonical', strtolower($email));

        return $query->getOneOrNullResult();
    }

    public function setUserData(
        int $user,
        ?int $province,
        ?int $city,
        string $name,
        string $surname,
        string $password,
        string $key,
        string $email,
        string $url,
        string $phone,
        string $street,
        string $postcode,
        string $description,
        string $ip,
        object $date
    ): ?int {
        if ($password !== '') {
            $setPassword = 'u.password = :password, ';
        } else {
            $setPassword = '';
        }
        if ($email !== '') {
            $setActive = 'u.enabled = 0, ';
            $setKey = 'u.confirmationToken = :key, ';
            $setEmail = 'u.email = :email, '
                . 'u.emailCanonical = :emailCanonical, ';
        } else {
            $setActive = '';
            $setKey = '';
            $setEmail = '';
        }

        $query = $this->getEntityManager()->createQuery(
            'UPDATE App:User u
            SET u.province = :province, u.city = :city, ' . $setActive
                . 'u.name = :name, u.surname = :surname, '
                . $setPassword . $setKey . $setEmail . 'u.url = :url,
                u.phone = :phone, u.street = :street,
                u.postcode = :postcode, u.description = :description,
                u.ipUpdated = :ip, u.dateUpdated = :date
            WHERE u.id = :user
                AND :password = :password AND :key = :key AND :email = :email
                AND :emailCanonical = :emailCanonical'
        )
            ->setParameter('user', $user)
            ->setParameter('province', $province)
            ->setParameter('city', $city)
            ->setParameter('name', $name)
            ->setParameter('surname', $surname)
            ->setParameter('password', password_hash(
                $password,
                PASSWORD_BCRYPT
            ))
            ->setParameter('key', $key)
            ->setParameter('email', $email)
            ->setParameter('emailCanonical', strtolower($email))
            ->setParameter('url', $url)
            ->setParameter('phone', $phone)
            ->setParameter('street', $street)
            ->setParameter('postcode', $postcode)
            ->setParameter('description', $description)
            ->setParameter('ip', $ip)
            ->setParameter('date', $date);

        return $query->getOneOrNullResult();
    }

    public function getUserData(int $user): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT u FROM App:User u
            WHERE u.enabled = 1 AND u.id = :user'
        )->setParameter('user', $user);

        return $query->getOneOrNullResult();
    }

    public function isUserMaxShow(int $user, int $show = 0): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT u FROM App:User u
            WHERE u.show >= (
                SELECT COUNT(s.id) FROM App:Site s
                INNER JOIN App:User u2 WITH s.user = u2.id
                WHERE s.active = 1 AND s.visible = 1 AND u2.enabled = 1
                    AND u2.show >= :show AND u2.id != :id
            ) AND u.id = :user'
        )
            ->setParameter('user', $user)
            ->setParameter('show', $show);

        return $query->getOneOrNullResult();
    }

    public function setUserShow(int $user, object $user2, int $show = 1): bool
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();

        $query = $em->createQuery(
            'UPDATE App:User u
            SET u.show = u.show + 1
            WHERE u.id = :user'
        )->setParameter('user', $user);
        $result = $query->getOneOrNullResult();

        if ($result && ($user2->getShow() < 1 || $show < 1)) {
            $em->getConnection()->commit();

            return true;
        }

        $query2 = $em->createQuery(
            'UPDATE App:User u
            SET u.show = u.show - :show
            WHERE u.id = :user2'
        )
            ->setParameter('user2', $user2->getId())
            ->setParameter('show', $show);
        $result2 = $query2->getOneOrNullResult();

        if ($result && $result2) {
            $em->getConnection()->commit();

            return true;
        } else {
            $em->getConnection()->rollback();
        }

        return false;
    }
}
