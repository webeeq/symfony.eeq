<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="site",
 *     indexes={
 *         @ORM\Index(name="active", columns={"active"}),
 *         @ORM\Index(name="visible", columns={"visible"}),
 *         @ORM\Index(name="date_added", columns={"date_added"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\SiteRepository")
 */
class Site
{
    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id;

    /**
     * @ORM\Column(type="boolean")
     */
    protected ?bool $active;

    /**
     * @ORM\Column(type="boolean")
     */
    protected ?bool $visible;

    /**
     * @ORM\Column(type="string", length=180)
     */
    protected ?string $name;

    /**
     * @ORM\Column(type="string", length=180)
     */
    protected ?string $url;

    /**
     * @ORM\Column(type="string", length=15)
     */
    protected ?string $ipAdded;

    /**
     * @ORM\Column(type="datetime")
     */
    protected ?\DateTimeInterface $dateAdded;

    /**
     * @ORM\Column(type="string", length=15)
     */
    protected ?string $ipUpdated;

    /**
     * @ORM\Column(type="datetime")
     */
    protected ?\DateTimeInterface $dateUpdated;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sites")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected ?User $user;

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

    public function setVisible(bool $visible): void
    {
        $this->visible = $visible;
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setIpAdded(string $ipAdded): void
    {
        $this->ipAdded = $ipAdded;
    }

    public function getIpAdded(): ?string
    {
        return $this->ipAdded;
    }

    public function setDateAdded(\DateTimeInterface $dateAdded): void
    {
        $this->dateAdded = $dateAdded;
    }

    public function getDateAdded(): ?\DateTimeInterface
    {
        return $this->dateAdded;
    }

    public function setIpUpdated(string $ipUpdated): void
    {
        $this->ipUpdated = $ipUpdated;
    }

    public function getIpUpdated(): ?string
    {
        return $this->ipUpdated;
    }

    public function setDateUpdated(\DateTimeInterface $dateUpdated): void
    {
        $this->dateUpdated = $dateUpdated;
    }

    public function getDateUpdated(): ?\DateTimeInterface
    {
        return $this->dateUpdated;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}
