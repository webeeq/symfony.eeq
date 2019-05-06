<?php declare(strict_types=1);

// src/Repository/ProvinceRepository.php
namespace App\Repository;

use App\Entity\Province;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ProvinceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Province::class);
    }

    public function getProvinceList(): array
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT p FROM App:Province p
            WHERE p.active = 1
            ORDER BY p.name ASC'
        );

        return $query->getResult();
    }
}
