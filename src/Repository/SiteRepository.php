<?php declare(strict_types=1);

// src/Repository/SiteRepository.php
namespace App\Repository;

use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class SiteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Site::class);
    }

    public function isAcceptSiteId(int $id): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT s FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE u.active = 1 AND s.active = 0
                AND s.id = :id'
        )->setParameter('id', $id);

        return $query->getOneOrNullResult();
    }

    public function isUserSiteId(int $id, int $site): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT s FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE u.active = 1 AND s.active = 1
                AND s.user = :id AND s.id = :site'
        )
            ->setParameter('id', $id)
            ->setParameter('site', $site);

        return $query->getOneOrNullResult();
    }

    public function isRestUserSite(int $id, int $site): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT s FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE u.active = 1 AND s.user = :id
                AND s.id = :site'
        )
            ->setParameter('id', $id)
            ->setParameter('site', $site);

        return $query->getOneOrNullResult();
    }

    public function deleteSiteData(int $id): ?int
    {
        $query = $this->getEntityManager()->createQuery(
            'DELETE FROM App:Site s WHERE s.id = :id'
        )->setParameter('id', $id);

        return $query->getOneOrNullResult();
    }

    public function setSiteData(
        int $id,
        bool $visible,
        string $name,
        string $ip,
        object $date
    ): ?int {
        $query = $this->getEntityManager()->createQuery(
            'UPDATE App:Site s
            SET s.visible = :visible, s.name = :name,
                s.ipUpdated = :ip, s.dateUpdated = :date
            WHERE s.id = :id'
        )
            ->setParameter('id', $id)
            ->setParameter('visible', $visible)
            ->setParameter('name', $name)
            ->setParameter('ip', $ip)
            ->setParameter('date', $date);

        return $query->getOneOrNullResult();
    }

    public function setAcceptSiteData(
        int $id,
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
            WHERE s.id = :id'
        )
            ->setParameter('id', $id)
            ->setParameter('active', $active)
            ->setParameter('visible', $visible)
            ->setParameter('name', $name)
            ->setParameter('url', $url)
            ->setParameter('ip', $ip)
            ->setParameter('date', $date);

        return $query->getOneOrNullResult();
    }

    public function getSiteData(int $id): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT s FROM App:Site s WHERE s.id = :id'
        )->setParameter('id', $id);

        return $query->getOneOrNullResult();
    }

    public function getAcceptSiteData(int $id): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT s FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE s.id = :id'
        )->setParameter('id', $id);

        return $query->getOneOrNullResult();
    }

    public function getAcceptUserData(int $id): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT u FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE s.id = :id'
        )->setParameter('id', $id);

        return $query->getOneOrNullResult();
    }

    public function getSiteList(int $id, int $level, int $listLimit): array
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT s FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE s.active = 1 AND u.active = 1 AND s.user = :id
            ORDER BY s.dateAdded DESC'
        )
            ->setParameter('id', $id)
            ->setFirstResult(($level - 1) * $listLimit)
            ->setMaxResults($listLimit);

        return $query->getResult();
    }

    public function getAdminSiteList(int $level, int $listLimit): array
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT s FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE s.active = 0 AND u.active = 1
            ORDER BY s.dateAdded DESC'
        )
            ->setFirstResult(($level - 1) * $listLimit)
            ->setMaxResults($listLimit);

        return $query->getResult();
    }

    public function pageNavigator(
        object $config,
        object $html,
        int $id,
        int $level,
        int $listLimit
    ): string {
        $query = $this->getEntityManager()->createQuery(
            'SELECT COUNT(s.id) AS total FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE s.active = 1 AND u.active = 1 AND s.user = :id'
        )->setParameter('id', $id);
        try {
            $count = (int) $query->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $count = 0;
        }

        return $html->preparePageNavigator(
            $config->getUrl() . '/konto,' . $id . ',strona,',
            $level,
            $listLimit,
            $count,
            3
        );
    }

    public function adminPageNavigator(
        object $config,
        object $html,
        int $id,
        int $level,
        int $listLimit
    ): string {
        $query = $this->getEntityManager()->createQuery(
            'SELECT COUNT(s.id) AS total FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE s.active = 0 AND u.active = 1'
        );
        try {
            $count = (int) $query->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $count = 0;
        }

        return $html->preparePageNavigator(
            $config->getUrl() . '/admin,' . $id . ',strona,',
            $level,
            $listLimit,
            $count,
            3
        );
    }

    public function getSiteRandomUrl(int $id, int $show = 1): ?object
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT s FROM App:Site s
            INNER JOIN App:User u WITH s.user = u.id
            WHERE s.active = 1 AND s.visible = 1 AND u.active = 1
                AND u.show >= :show AND u.id != :id
            ORDER BY RAND()'
        )
            ->setParameter('id', $id)
            ->setParameter('show', $show)
            ->setMaxResults(1);

        return $query->getOneOrNullResult();
    }
}
