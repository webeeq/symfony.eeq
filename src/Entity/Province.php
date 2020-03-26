<?php

declare(strict_types=1);

// src/Entity/Province.php
namespace App\Entity;

use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="province",
 *     indexes={@ORM\Index(name="name", columns={"name"})}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ProvinceRepository")
 */
class Province
{
    /**
     * @ORM\Column(type="smallint", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id;

    /**
     * @ORM\Column(type="boolean")
     */
    protected ?bool $active;

    /**
     * @ORM\Column(type="string", length=30)
     */
    protected ?string $name;

    /**
     * @ORM\OneToMany(targetEntity="City", mappedBy="province")
     */
    protected ?City $cities;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="province")
     */
    protected ?User $users;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return (int) $this->id;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
