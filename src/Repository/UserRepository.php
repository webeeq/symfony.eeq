<?php declare(strict_types=1);

// src/Repository/UserRepository.php
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function generatePassword(): string
    {
        $password = '';

        for ($i = 0; $i < 30; $i++) {
            if (rand(0, 2) != 0) {
                $j = rand(0, 51);
                $password .= substr(
                    'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
                    $j,
                    1
                );
            } else {
                $j = rand(0, 19);
                $password .= substr(
                    '1234567890!@#$%^&*()',
                    $j,
                    1
                );
            }
        }

        return $password;
    }

    public function generateKey(): string
    {
        $key = '';

        for ($i = 0; $i < 100; $i++) {
            if (rand(0, 2) != 0) {
                $j = rand(0, 51);
                $key .= substr(
                    'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
                    $j,
                    1
                );
            } else {
                $j = rand(0, 9);
                $key .= substr(
                    '1234567890',
                    $j,
                    1
                );
            }
        }

        return $key;
    }

    public function isUserId(int $id, int $user): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT u FROM App:User u
            WHERE u.active = 1 AND u.id = :id AND u.id = :user'
        )
            ->setParameter('id', $id)
            ->setParameter('user', $user);

        return $query->getOneOrNullResult();
    }

    public function isUserLogin(string $login): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT u FROM App:User u WHERE u.login = :login'
        )->setParameter('login', $login);

        return $query->getOneOrNullResult();
    }

    public function setUserPassword(
        int $id,
        string $password,
        string $key,
        string $ip,
        object $date
    ): ?int {
        $query = $this->getEntityManager()->createQuery(
            'UPDATE App:User u
            SET u.password = :password, u.key = :key,
                u.ipUpdated = :ip, u.dateUpdated = :date
            WHERE u.id = :id'
        )
            ->setParameter('id', $id)
            ->setParameter('password', password_hash(
                $password,
                PASSWORD_ARGON2I
            ))
            ->setParameter('key', $key)
            ->setParameter('ip', $ip)
            ->setParameter('date', $date);

        return $query->getOneOrNullResult();
    }

    public function getIdPassword(int $id): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT u FROM App:User u WHERE u.id = :id'
        )->setParameter('id', $id);

        return $query->getOneOrNullResult();
    }

    public function getUserPassword(string $login): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT u FROM App:User u WHERE u.login = :login'
        )->setParameter('login', $login);

        return $query->getOneOrNullResult();
    }

    public function getRestUserPassword(string $login): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT u FROM App:User u
            WHERE u.active = 1 AND u.login = :login'
        )->setParameter('login', $login);

        return $query->getOneOrNullResult();
    }

    public function setUserActive(int $id): ?int
    {
        $query = $this->getEntityManager()->createQuery(
            'UPDATE App:User u SET u.active = 1 WHERE u.id = :id'
        )->setParameter('id', $id);

        return $query->getOneOrNullResult();
    }

    public function setUserLoged(int $id, string $ip, object $date): ?int
    {
        $query = $this->getEntityManager()->createQuery(
            'UPDATE App:User u
            SET u.ipLoged = :ip, u.dateLoged = :date
            WHERE u.id = :id'
        )
            ->setParameter('id', $id)
            ->setParameter('ip', $ip)
            ->setParameter('date', $date);

        return $query->getOneOrNullResult();
    }

    public function setUserData(
        int $id,
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
        if ($password != '') {
            $setPassword = 'u.password = :password, ';
        } else {
            $setPassword = '';
        }
        if ($email != '') {
            $setActive = 'u.active = 0, ';
            $setKey = 'u.key = :key, ';
            $setEmail = 'u.email = :email, ';
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
            WHERE u.id = :id
                AND :password = :password AND :key = :key AND :email = :email'
        )
            ->setParameter('id', $id)
            ->setParameter('province', $province)
            ->setParameter('city', $city)
            ->setParameter('name', $name)
            ->setParameter('surname', $surname)
            ->setParameter('password', password_hash(
                $password,
                PASSWORD_ARGON2I
            ))
            ->setParameter('key', $key)
            ->setParameter('email', $email)
            ->setParameter('url', $url)
            ->setParameter('phone', $phone)
            ->setParameter('street', $street)
            ->setParameter('postcode', $postcode)
            ->setParameter('description', $description)
            ->setParameter('ip', $ip)
            ->setParameter('date', $date);

        return $query->getOneOrNullResult();
    }

    public function getUserData(int $id): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT u FROM App:User u
            WHERE u.active = 1 AND u.id = :id'
        )->setParameter('id', $id);

        return $query->getOneOrNullResult();
    }

    public function isUserMaxShow(int $id, int $show = 0): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT u FROM App:User u
            WHERE u.show >= (
                SELECT COUNT(s.id) FROM App:Site s
                INNER JOIN App:User u2 WITH s.user = u2.id
                WHERE s.active = 1 AND s.visible = 1 AND u2.active = 1
                    AND u2.show >= :show AND u2.id != :id
            ) AND u.id = :id'
        )
            ->setParameter('id', $id)
            ->setParameter('show', $show);

        return $query->getOneOrNullResult();
    }

    public function setUserShow(int $id, object $user, int $show = 1): bool
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();

        $query1 = $em->createQuery(
            'UPDATE App:User u
            SET u.show = u.show + 1
            WHERE u.id = :id'
        )->setParameter('id', $id);
        $result1 = $query1->getOneOrNullResult();

        if ($result1 && ($user->getShow() < 1 || $show < 1)) {
            $em->getConnection()->commit();

            return true;
        }

        $query2 = $em->createQuery(
            'UPDATE App:User u
            SET u.show = u.show - :show
            WHERE u.id = :id'
        )
            ->setParameter('id', $user->getId())
            ->setParameter('show', $show);
        $result2 = $query2->getOneOrNullResult();

        if ($result1 && $result2) {
            $em->getConnection()->commit();

            return true;
        } else {
            $em->getConnection()->rollback();
        }

        return false;
    }
}
