<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Site::class);
    }

    public function isAcceptSiteId(int $site): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT s FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE u.enabled = 1 AND s.active = 0
                AND s.id = :site'
        )->setParameter('site', $site);

        return $query->getOneOrNullResult();
    }

    public function isUserSiteId(int $user, int $site): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT s FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE u.enabled = 1 AND s.active = 1
                AND s.user = :user AND s.id = :site'
        )
            ->setParameter('user', $user)
            ->setParameter('site', $site);

        return $query->getOneOrNullResult();
    }

    public function isApiUserSite(int $user, int $site): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT s FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE u.enabled = 1 AND s.user = :user
                AND s.id = :site'
        )
            ->setParameter('user', $user)
            ->setParameter('site', $site);

        return $query->getOneOrNullResult();
    }

    public function deleteSiteData(int $site): ?int
    {
        $query = $this->getEntityManager()->createQuery(
            'DELETE FROM App:Site s WHERE s.id = :site'
        )->setParameter('site', $site);

        return $query->getOneOrNullResult();
    }

    public function setSiteData(
        int $site,
        bool $visible,
        string $name,
        string $ip,
        object $date
    ): ?int {
        $query = $this->getEntityManager()->createQuery(
            'UPDATE App:Site s
            SET s.visible = :visible, s.name = :name,
                s.ipUpdated = :ip, s.dateUpdated = :date
            WHERE s.id = :site'
        )
            ->setParameter('site', $site)
            ->setParameter('visible', $visible)
            ->setParameter('name', $name)
            ->setParameter('ip', $ip)
            ->setParameter('date', $date);

        return $query->getOneOrNullResult();
    }

    public function setAcceptSiteData(
        int $site,
        bool $active,
        bool $visible,
        string $name,
        string $url,
        string $ip,
        object $date
    ): ?int {
        $query = $this->getEntityManager()->createQuery(
            'UPDATE App:Site s
            SET s.active = :active, s.visible = :visible,
                s.name = :name, s.url = :url,
                s.ipUpdated = :ip, s.dateUpdated = :date
            WHERE s.id = :site'
        )
            ->setParameter('site', $site)
            ->setParameter('active', $active)
            ->setParameter('visible', $visible)
            ->setParameter('name', $name)
            ->setParameter('url', $url)
            ->setParameter('ip', $ip)
            ->setParameter('date', $date);

        return $query->getOneOrNullResult();
    }

    public function getSiteData(int $site): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT s FROM App:Site s WHERE s.id = :site'
        )->setParameter('site', $site);

        return $query->getOneOrNullResult();
    }

    public function getAcceptSiteData(int $site): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT s FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE s.id = :site'
        )->setParameter('site', $site);

        return $query->getOneOrNullResult();
    }

    public function getAcceptUserData(int $site): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT u FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE s.id = :site'
        )->setParameter('site', $site);

        return $query->getOneOrNullResult();
    }

    public function getSiteList(int $user, int $level, int $listLimit): array
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT s FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE s.active = 1 AND u.enabled = 1 AND s.user = :user
            ORDER BY s.dateAdded DESC'
        )
            ->setParameter('user', $user)
            ->setFirstResult(($level - 1) * $listLimit)
            ->setMaxResults($listLimit);

        return $query->getResult();
    }

    public function getAdminSiteList(int $level, int $listLimit): array
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT s FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE s.active = 0 AND u.enabled = 1
            ORDER BY s.dateAdded DESC'
        )
            ->setFirstResult(($level - 1) * $listLimit)
            ->setMaxResults($listLimit);

        return $query->getResult();
    }

    public function getSiteCount(int $user): int
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT COUNT(s.id) AS total FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE s.active = 1 AND u.enabled = 1 AND s.user = :user'
        )->setParameter('user', $user);
        try {
            $count = (int) $query->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $count = 0;
        }

        return $count;
    }

    public function getAdminSiteCount(): int
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT COUNT(s.id) AS total FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE s.active = 0 AND u.enabled = 1'
        );
        try {
            $count = (int) $query->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $count = 0;
        }

        return $count;
    }

    public function getSiteRandomUrl(int $user, int $show = 1): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT s FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE s.active = 1 AND s.visible = 1 AND u.enabled = 1
                AND u.show >= :show AND u.id != :user
            ORDER BY RAND()'
        )
            ->setParameter('user', $user)
            ->setParameter('show', $show)
            ->setMaxResults(1);

        return $query->getOneOrNullResult();
    }
}
