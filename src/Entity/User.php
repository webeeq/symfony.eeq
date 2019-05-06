<?php declare(strict_types=1);

// src/Entity/User.php
namespace App\Entity;

use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="`user`",
 *     indexes={
 *         @ORM\Index(name="active", columns={"active"}),
 *         @ORM\Index(name="login", columns={"login"}),
 *         @ORM\Index(name="password", columns={"password"}),
 *         @ORM\Index(name="show", columns={"show"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Column(type="bigint", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    protected $admin;

    /**
     * @ORM\Column(type="boolean", options={"default":1})
     */
    protected $active;

    /**
     * @ORM\Column(type="string", length=50, options={"default":""})
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=100, options={"default":""})
     */
    protected $surname;

    /**
     * @ORM\Column(
     *     type="string",
     *     length=20,
     *     unique=true,
     *     options={"default":""}
     * )
     */
    protected $login;

    /**
     * @ORM\Column(
     *     name="`password`",
     *     type="string",
     *     length=41,
     *     options={"default":""}
     * )
     */
    protected $password;

    /**
     * @ORM\Column(
     *     name="`key`",
     *     type="string",
     *     length=32,
     *     options={"default":""}
     * )
     */
    protected $key;

    /**
     * @ORM\Column(type="string", length=100, options={"default":""})
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=100, options={"default":""})
     */
    protected $url;

    /**
     * @ORM\Column(type="string", length=12, options={"default":""})
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=30, options={"default":""})
     */
    protected $street;

    /**
     * @ORM\Column(type="string", length=6, options={"default":""})
     */
    protected $postcode;

    /**
     * @ORM\Column(type="text", length=65535)
     */
    protected $description;

    /**
     * @ORM\Column(
     *     name="`show`",
     *     type="bigint",
     *     options={"unsigned":true, "default":0}
     * )
     */
    protected $show;

    /**
     * @ORM\Column(type="string", length=15, options={"default":""})
     */
    protected $ipAdded;

    /**
     * @ORM\Column(type="datetime", options={"default":"0000-00-00 00:00:00"})
     */
    protected $dateAdded;

    /**
     * @ORM\Column(type="string", length=15, options={"default":""})
     */
    protected $ipUpdated;

    /**
     * @ORM\Column(type="datetime", options={"default":"0000-00-00 00:00:00"})
     */
    protected $dateUpdated;

    /**
     * @ORM\Column(type="string", length=15, options={"default":""})
     */
    protected $ipLoged;

    /**
     * @ORM\Column(type="datetime", options={"default":"0000-00-00 00:00:00"})
     */
    protected $dateLoged;

    /**
     * @ORM\ManyToOne(targetEntity="Province", inversedBy="users")
     * @ORM\JoinColumn(name="province_id", referencedColumnName="id")
     */
    protected $province;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="users")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\OneToMany(targetEntity="Site", mappedBy="user")
     */
    protected $sites;

    public function __construct()
    {
        $this->sites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return (int) $this->id;
    }

    public function getAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getShow(): ?int
    {
        return (int) $this->show;
    }

    public function setShow(int $show): self
    {
        $this->show = $show;

        return $this;
    }

    public function getIpAdded(): ?string
    {
        return $this->ipAdded;
    }

    public function setIpAdded(string $ipAdded): self
    {
        $this->ipAdded = $ipAdded;

        return $this;
    }

    public function getDateAdded(): ?\DateTimeInterface
    {
        return $this->dateAdded;
    }

    public function setDateAdded(\DateTimeInterface $dateAdded): self
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    public function getIpUpdated(): ?string
    {
        return $this->ipUpdated;
    }

    public function setIpUpdated(string $ipUpdated): self
    {
        $this->ipUpdated = $ipUpdated;

        return $this;
    }

    public function getDateUpdated(): ?\DateTimeInterface
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(\DateTimeInterface $dateUpdated): self
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    public function getIpLoged(): ?string
    {
        return $this->ipLoged;
    }

    public function setIpLoged(string $ipLoged): self
    {
        $this->ipLoged = $ipLoged;

        return $this;
    }

    public function getDateLoged(): ?\DateTimeInterface
    {
        return $this->dateLoged;
    }

    public function setDateLoged(\DateTimeInterface $dateLoged): self
    {
        $this->dateLoged = $dateLoged;

        return $this;
    }

    public function getProvince(): ?Province
    {
        return $this->province;
    }

    public function setProvince(?Province $province): self
    {
        $this->province = $province;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection|Site[]
     */
    public function getSites(): Collection
    {
        return $this->sites;
    }

    public function addSite(Site $site): self
    {
        if (!$this->sites->contains($site)) {
            $this->sites[] = $site;
            $site->setUser($this);
        }

        return $this;
    }

    public function removeSite(Site $site): self
    {
        if ($this->sites->contains($site)) {
            $this->sites->removeElement($site);
            // set the owning side to null (unless already changed)
            if ($site->getUser() === $this) {
                $site->setUser(null);
            }
        }

        return $this;
    }
}
