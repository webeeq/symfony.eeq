<?php

declare(strict_types=1);

// src/Entity/User.php
namespace App\Entity;

use App\Bundle\Config;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(
 *     name="`user`",
 *     indexes={@ORM\Index(name="show", columns={"show"})}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\Length(
     *     max=50,
     *     maxMessage="Login może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $username;

    /**
     * @Assert\Length(
     *     max=72,
     *     maxMessage="Hasło może zawierać maksymalnie {{ limit }} znaki."
     * )
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="string", length=30)
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=30,
     *     maxMessage="Imię może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=50,
     *     maxMessage="Nazwisko może zawierać maksymalnie {{ limit }} znaków."
     * )
     */
    protected $surname;

    /**
     * @ORM\Column(type="string", length=180)
     */
    protected $url;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=60)
     */
    protected $street;

    /**
     * @ORM\Column(type="string", length=6)
     */
    protected $postcode;

    /**
     * @ORM\Column(type="text", length=65535)
     */
    protected $description;

    /**
     * @ORM\Column(name="`show`", type="integer")
     */
    protected $show;

    /**
     * @ORM\Column(type="string", length=15)
     */
    protected $ipAdded;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateAdded;

    /**
     * @ORM\Column(type="string", length=15)
     */
    protected $ipUpdated;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateUpdated;

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

    /**
     * @Assert\NotBlank()
     * @Assert\IsTrue(message="Musisz zaakceptować regulamin serwisu.")
     */
    protected $accept = true;

    public function __construct()
    {
        parent::__construct();
        $config = new Config(null);
        $this->sites = new ArrayCollection();

        $this->url = '';
        $this->phone = '';
        $this->street = '';
        $this->postcode = '';
        $this->description = '';
        $this->show = 0;
        $this->ipAdded = $config->getRemoteAddress();
        $this->dateAdded = $config->getDateTimeNow();
        $this->ipUpdated = '';
        $this->dateUpdated = new \DateTime('1970-01-01 00:00:00');
        $this->province = null;
        $this->city = null;
        $this->accept = false;

        $this->roles = array('ROLE_USER');
    }

    public function getId(): ?int
    {
        return (int) $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setPostcode(string $postcode): void
    {
        $this->postcode = $postcode;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setShow(int $show): void
    {
        $this->show = $show;
    }

    public function getShow(): ?int
    {
        return (int) $this->show;
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

    public function setProvince(?Province $province): void
    {
        $this->province = $province;
    }

    public function getProvince(): ?Province
    {
        return $this->province;
    }

    public function setCity(?City $city): void
    {
        $this->city = $city;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setAccept(bool $accept): void
    {
        $this->accept = $accept;
    }

    public function getAccept(): ?bool
    {
        return $this->accept;
    }
}
